<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetsReader
{
    protected Sheets $sheets;

    // colunas M, Q, R, S, T, U, V, W, X, Y, Z (0-indexado a partir de A=0)
    protected array $personCols = [12, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];

    public function __construct()
    {
        $client = new Client();
        $credentialsPath = config('services.google_drive.credentials');

        $fullPath = str_starts_with($credentialsPath, '/')
            ? $credentialsPath
            : base_path($credentialsPath);

        $client = new Client();
        $client->setAuthConfig($fullPath);
        // Escopo de leitura E escrita — precisamos de escrever de volta na folha.
        $client->addScope(Sheets::SPREADSHEETS);

        $this->sheets = new Sheets($client);
    }

    public function readAsRows(string $spreadsheetId, string $range = 'A1:Z1000'): array
    {
        $response = $this->sheets->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues() ?? [];

        if (empty($values)) {
            return [];
        }

        $header = array_map(fn ($h) => trim((string) $h), array_shift($values));
        $numCols = count($header);

        return array_values(array_filter(
            array_map(function ($row) use ($header, $numCols) {
                $row = array_pad($row, $numCols, '');
                return array_combine($header, array_slice($row, 0, $numCols));
            }, $values),
            fn ($row) => count(array_filter($row, fn ($v) => $v !== '')) > 0
        ));
    }

    /**
     * Leitor dedicado à folha "Noites de Campo". Devolve as pessoas na ordem
     * ORIGINAL das colunas (para a grelha bater certo com o Sheets) — a
     * ordenação por ranking é feita depois, na camada do controller/view.
     */
    public function readNoitesCampo(string $spreadsheetId): array
    {
        // $this->service já existe e está inicializado
        $response = $this->service->spreadsheets_values->get($spreadsheetId, 'A1:ZZ100');
        $rows = $response->getValues() ?? [];

        if (empty($rows)) {
            return ['activities' => [], 'people' => []];
        }

        $activities = [];
        $adjustmentRowIndex = null;

        // 1. Mapeia as atividades e localiza a linha de Ajustes
        foreach ($rows as $rowIndex => $row) {
            $nomeLinha = strtolower(trim($row[1] ?? ''));

            if ($rowIndex >= 3) {
                if (str_contains($nomeLinha, 'ajuste') || str_contains($nomeLinha, 'secções') || str_contains($nomeLinha, 'seccoes')) {
                    $adjustmentRowIndex = $rowIndex;
                    continue;
                }

                if (str_contains($nomeLinha, 'total')) {
                    break;
                }

                if (!empty($row[1])) {
                    $activities[] = [
                        'row' => $rowIndex + 1,
                        'data' => $row[0] ?? '',
                        'nome' => $row[1] ?? '',
                        'local' => $row[2] ?? '',
                        'noites' => (float) str_replace(',', '.', $row[4] ?? 0),
                        'acantonamento' => in_array(strtolower(trim($row[5] ?? '')), ['true', 'sim', '1', 'verdadeiro']),
                    ];
                }
            }
        }

        $people = [];

        // 2. Calcula os totais com o Ajuste / Secções Anteriores
        foreach ($this->personCols as $colIndex) {
            $personName = $rows[2][$colIndex] ?? '';
            if (empty($personName)) {
                continue;
            }

            $ajuste = 0;
            if ($adjustmentRowIndex !== null && isset($rows[$adjustmentRowIndex][$colIndex])) {
                $ajuste = (float) str_replace(',', '.', $rows[$adjustmentRowIndex][$colIndex]);
            }

            $totalNoitesAtividades = 0;
            $totalAtividadesCount = 0;
            $participantesCols = [];

            foreach ($activities as $act) {
                $rowIdx = $act['row'] - 1;
                $val = strtolower(trim($rows[$rowIdx][$colIndex] ?? ''));

                $participou = in_array($val, ['true', '1', 'x', 'sim', 'vade', 'verdadeiro']);

                if ($participou) {
                    $participantesCols[] = $colIndex;
                    $totalAtividadesCount++;

                    if (!$act['acantonamento']) {
                        $totalNoitesAtividades += $act['noites'];
                    }
                }
            }

            $people[] = [
                'col' => $colIndex,
                'name' => $personName,
                'past_nights' => $ajuste,
                'total_nights' => $totalNoitesAtividades + $ajuste, // Soma atividades + ajuste anterior
                'total_activities' => $totalAtividadesCount,
                'participantes_cols' => $participantesCols,
            ];
        }

        return [
            'activities' => $activities,
            'people' => $people,
        ];
    }

    public function updateCell(string $spreadsheetId, int $row, int $col, bool $value): void
    {
        // Converte o número da coluna para a letra correspondente do Sheets (ex: 12 -> L)
        $colLetter = $this->getColLetter($col);
        $range = "'Folha1'!{$colLetter}{$row}"; // Ajusta o nome da aba se necessário

        $body = new \Google\Service\Sheets\ValueRange([
            'values' => [[$value ? 'TRUE' : 'FALSE']],
        ]);

        $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, [
            'valueInputOption' => 'USER_ENTERED',
        ]);
    }

    private function getColLetter(int $colIndex): string
    {
        $letter = '';
        while ($colIndex > 0) {
            $module = ($colIndex - 1) % 26;
            $letter = chr(65 + $module) . $letter;
            $colIndex = (int)(($colIndex - $module) / 26);
        }
        return $letter;
    }


    public function readHorasMar(string $spreadsheetId): array
    {
        $formatted = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:S300')->getValues() ?? [];

        $raw = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:S300', [
            'valueRenderOption' => 'UNFORMATTED_VALUE',
        ])->getValues() ?? [];

        $colNome = 3; // coluna D

        $headerRow = $formatted[1] ?? []; // linha 2: nomes das atividades
        $horasRow = $formatted[2] ?? [];  // linha 3: horas de cada atividade

        // Deteta as colunas de atividades dinamicamente a partir da coluna E,
        // até encontrar uma célula vazia ou a coluna "Total:".
        $activities = [];
        for ($col = 4; $col < count($headerRow); $col++) {
            $label = trim($headerRow[$col] ?? '');
            if ($label === '' || mb_strtolower($label) === 'total:') {
                break;
            }
            $activities[$col] = [
                'col' => $col,
                'nome' => $label,
                'horas' => (float) str_replace(',', '.', trim($horasRow[$col] ?? '0')),
            ];
        }

        $people = [];
        foreach (array_slice($formatted, 3) as $i => $row) {
            $nome = trim($row[$colNome] ?? '');
            if ($nome === '') {
                continue; // fim da lista de pessoas
            }

            $rowNumber = $i + 4;
            $rawRow = $raw[$i + 3] ?? [];

            $totalHoras = 0.0;
            $totalAtividades = 0;
            $atividadesColsMarcadas = [];

            foreach ($activities as $col => $act) {
                $valorBruto = $rawRow[$col] ?? null;

                $marcado = match (true) {
                    is_bool($valorBruto) => $valorBruto,
                    is_string($valorBruto) => trim($valorBruto) !== '',
                    is_numeric($valorBruto) => (float) $valorBruto !== 0.0,
                    default => false,
                };

                if ($marcado) {
                    $totalHoras += $act['horas'];
                    $totalAtividades++;
                    $atividadesColsMarcadas[] = $col;
                }
            }

            $people[] = [
                'row' => $rowNumber,
                'name' => $nome,
                'total_hours' => $totalHoras,
                'total_activities' => $totalAtividades,
                'atividades_cols' => $atividadesColsMarcadas,
            ];
        }

        return [
            'people' => $people,
            'activities' => array_values($activities),
        ];
    }

    /**
     * Insere uma nova atividade (COLUNA, não linha) logo a seguir à última
     * atividade real. $afterCol é a coluna (0-indexada) da última atividade —
     * vem de readHorasMar().
     */
    public function appendAtividadeHorasMar(string $spreadsheetId, string $nome, float $horas, int $afterCol): void
    {
        $sheetId = $this->firstSheetId($spreadsheetId);

        $this->sheets->spreadsheets->batchUpdate($spreadsheetId, new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => [[
                'insertDimension' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'COLUMNS',
                        'startIndex' => $afterCol + 1,
                        'endIndex' => $afterCol + 2,
                    ],
                    'inheritFromBefore' => true,
                ],
            ]],
        ]));

        $newCol = $afterCol + 1;
        $letra = $this->columnLetter($newCol);

        $this->sheets->spreadsheets_values->update(
            $spreadsheetId,
            "{$letra}2:{$letra}3",
            new ValueRange(['values' => [[$nome], [$horas]]]),
            ['valueInputOption' => 'USER_ENTERED']
        );
    }

    /**
     * Insere uma nova atividade logo a seguir à última atividade real
     * (não no fim absoluto da folha, que pode ter linhas de resumo/notas
     * depois da tabela). $afterRow é o número da última linha de atividade
     * real (1-indexado) — vem de readNoitesCampo().
     *
     * Insere a linha em branco com inheritFromBefore=true, o que copia a
     * formatação da linha anterior — incluindo as checkboxes das pessoas,
     * que assim já aparecem corretamente no Sheets, sem precisares de as
     * estender à mão.
     */
    public function appendActivity(string $spreadsheetId, string $dia, string $nome, string $local, int $noites, int $afterRow): void
    {
        $sheetId = $this->firstSheetId($spreadsheetId);

        $this->sheets->spreadsheets->batchUpdate($spreadsheetId, new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => [[
                'insertDimension' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $afterRow,     // 0-indexado; equivale à linha $afterRow+1 (1-indexado)
                        'endIndex' => $afterRow + 1,
                    ],
                    'inheritFromBefore' => true,
                ],
            ]],
        ]));

        $newRow = $afterRow + 1;

        $this->sheets->spreadsheets_values->update(
            $spreadsheetId,
            "B{$newRow}:E{$newRow}",
            new ValueRange(['values' => [[$dia, $nome, $local, $noites]]]),
            ['valueInputOption' => 'USER_ENTERED']
        );
    }

    private function firstSheetId(string $spreadsheetId): int
    {
        $spreadsheet = $this->sheets->spreadsheets->get($spreadsheetId);
        return $spreadsheet->getSheets()[0]->getProperties()->getSheetId();
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $index = intdiv($index - $mod, 26);
        }
        return $letter;
    }
}
