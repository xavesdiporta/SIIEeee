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
        $formatted = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:AA1000')->getValues() ?? [];

        $raw = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:AA1000', [
            'valueRenderOption' => 'UNFORMATTED_VALUE',
        ])->getValues() ?? [];

        $colData = 1;
        $colNome = 2;
        $colLocal = 3;
        $colNoites = 4;
        $colAcantonamento = 5; // coluna F: "Acantonamento (noites n/contam)"

        $headerRow = $formatted[2] ?? [];

        $people = [];
        foreach ($this->personCols as $col) {
            $name = trim($headerRow[$col] ?? '');
            if ($name !== '') {
                $people[$col] = ['col' => $col, 'name' => $name, 'total_activities' => 0, 'total_nights' => 0];
            }
        }

        $activities = [];
        foreach (array_slice($formatted, 3) as $i => $row) {
            $nome = trim($row[$colNome] ?? '');
            if ($nome === '') {
                continue;
            }

            $nomeLower = mb_strtolower($nome);

            // A linha "Ajustes" não é uma atividade — tem uma correção manual
            // de noites por pessoa (para casos não cobertos pelas atividades
            // registadas). Aplicamos esse valor ao total de cada um.
            if (str_starts_with($nomeLower, 'ajustes')) {
                foreach ($people as $col => $person) {
                    $ajuste = (int) ($row[$col] ?? 0);
                    $people[$col]['total_nights'] += $ajuste;
                }
                continue;
            }

            // A partir daqui a folha só tem linhas de resumo/notas (Total c/
            // acantonamento, Noites de campo:, Em falta...). Paramos aqui.
            $marcadoresDeFim = ['total c/', 'noites de campo', 'em falta'];
            $ehLinhaDeResumo = false;
            foreach ($marcadoresDeFim as $marcador) {
                if (str_starts_with($nomeLower, $marcador)) {
                    $ehLinhaDeResumo = true;
                    break;
                }
            }
            if ($ehLinhaDeResumo) {
                break;
            }

            $rowNumber = $i + 4; // número real da linha na folha (1-indexado)
            $rawRow = $raw[$i + 3] ?? [];
            $noites = (int) ($row[$colNoites] ?? 0);

            $acantonBruto = $rawRow[$colAcantonamento] ?? null;
            $acantonamento = match (true) {
                is_bool($acantonBruto) => $acantonBruto,
                is_string($acantonBruto) => trim($acantonBruto) !== '',
                is_numeric($acantonBruto) => (float) $acantonBruto !== 0.0,
                default => false,
            };

            $participantesCols = [];

            foreach ($people as $col => $person) {
                $valorBruto = $rawRow[$col] ?? null;

                $marcado = match (true) {
                    is_bool($valorBruto) => $valorBruto,
                    is_string($valorBruto) => trim($valorBruto) !== '',
                    is_numeric($valorBruto) => (float) $valorBruto !== 0.0,
                    default => false,
                };

                if ($marcado) {
                    $participantesCols[] = $col;
                    $people[$col]['total_activities']++;
                    // Acantonamento = noites que não contam para o total, por
                    // decisão da própria folha — só somamos se não for o caso.
                    if (! $acantonamento) {
                        $people[$col]['total_nights'] += $noites;
                    }
                }
            }

            $activities[] = [
                'row' => $rowNumber,
                'data' => trim($row[$colData] ?? ''),
                'nome' => $nome,
                'local' => trim($row[$colLocal] ?? ''),
                'noites' => $noites,
                'acantonamento' => $acantonamento,
                'participantes_cols' => $participantesCols, // colunas (int) marcadas nesta linha
            ];
        }

        return [
            'people' => array_values($people), // ordem original das colunas
            'activities' => $activities,
        ];
    }

    /**
     * Leitor dedicado à folha "Horas de Mar". Ao contrário de Noites de
     * Campo, aqui a orientação está invertida: cada PESSOA é uma linha
     * (a partir da linha 4, coluna D), e cada ATIVIDADE é uma coluna
     * (nome na linha 2, horas dessa atividade na linha 3, a partir da
     * coluna E). A participação é marcada nas células que cruzam pessoa
     * (linha) com atividade (coluna).
     */
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

    /**
     * Escreve true/false numa célula específica (linha real da folha, coluna
     * 0-indexada a partir de A). Usado para marcar/desmarcar participação.
     */
    public function updateCell(string $spreadsheetId, int $row, int $colIndex, bool $value): void
    {
        $range = $this->columnLetter($colIndex) . $row;

        $values = new ValueRange([
            'values' => [[$value]],
        ]);

        $this->sheets->spreadsheets_values->update(
            $spreadsheetId,
            $range,
            $values,
            ['valueInputOption' => 'RAW']
        );
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
