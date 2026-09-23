<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class GoogleSheetsReader
{
    protected Sheets $sheets;

    // Colunas M, Q, R, S, T, U, V, W, X, Y, Z (0-indexado a partir de A=0)
    protected array $personCols = [12, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];

    public function __construct()
    {
        $credentialsPath = config('services.google_drive.credentials');

        $fullPath = str_starts_with($credentialsPath, '/')
            ? $credentialsPath
            : base_path($credentialsPath);

        $client = new Client();
        $client->setAuthConfig($fullPath);
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
     * Leitor dedicado à folha "Noites de Campo".
     */
    public function readNoitesCampo(string $spreadsheetId): array
    {
        // Lê até à linha 1000 para garantir que apanha todas as atividades e ajustes
        $response = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:ZZ1000');
        $rows = $response->getValues() ?? [];

        if (empty($rows)) {
            return ['activities' => [], 'people' => []];
        }

        $activities = [];
        $adjustmentRowIndex = null;

        // 1. Mapeia todas as atividades da folha
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < 3) { // Salta as primeiras 3 linhas (cabeçalhos e nomes)
                continue;
            }

            $nomeLinha = strtolower(trim($row[1] ?? ''));

            // Deteta a linha de Ajustes / Secções Anteriores
            if (str_contains($nomeLinha, 'ajuste') || str_contains($nomeLinha, 'secções') || str_contains($nomeLinha, 'seccoes') || str_contains($nomeLinha, 'anteriores') || str_contains($nomeLinha, 'transito')) {
                $adjustmentRowIndex = $rowIndex;
                continue;
            }

            // Ignora linhas de subtotal sem parar o ciclo (continue em vez de break)
            if (str_contains($nomeLinha, 'total')) {
                continue;
            }

            // Se for uma atividade válida com nome na Coluna B ($row[1])
            if (!empty($row[1])) {
                $participantesCols = [];
                foreach ($this->personCols as $colIndex) {
                    $val = $row[$colIndex] ?? null;

                    $marcado = match (true) {
                        is_bool($val) => $val,
                        is_numeric($val) => (float)$val > 0,
                        is_string($val) => in_array(strtolower(trim($val)), ['true', '1', 'x', 'sim', 'vade', 'verdadeiro', 'v']),
                        default => false,
                    };

                    if ($marcado) {
                        $participantesCols[] = $colIndex;
                    }
                }

                // Coluna D (índice 3) = Noites | Coluna E (índice 4) = Acantonamento
                $noitesVal = (float) str_replace(',', '.', $row[3] ?? 0);
                $acantVal = in_array(strtolower(trim((string)($row[4] ?? ''))), ['true', 'sim', '1', 'verdadeiro', 'v', 'x']);

                $activities[] = [
                    'row' => $rowIndex + 1, // Linha real no Google Sheets (1-based)
                    'data' => $row[0] ?? '',
                    'nome' => $row[1] ?? '',
                    'local' => $row[2] ?? '',
                    'noites' => $noitesVal,
                    'acantonamento' => $acantVal,
                    'participantes_cols' => $participantesCols,
                ];
            }
        }

        $people = [];

        // 2. Calcula o total de cada pessoa (Atividades + Ajuste anterior)
        foreach ($this->personCols as $colIndex) {
            $personName = trim($rows[2][$colIndex] ?? '');
            if (empty($personName)) {
                continue;
            }

            // Lê o valor da linha de Ajuste para esta pessoa (se existir)
            $ajuste = 0;
            if ($adjustmentRowIndex !== null && isset($rows[$adjustmentRowIndex][$colIndex])) {
                $ajuste = (float) str_replace(',', '.', $rows[$adjustmentRowIndex][$colIndex]);
            }

            $totalNoitesAtividades = 0;
            $totalAtividadesCount = 0;

            foreach ($activities as $act) {
                if (in_array($colIndex, $act['participantes_cols'], true)) {
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
                'total_nights' => $totalNoitesAtividades + $ajuste,
                'total_activities' => $totalAtividadesCount,
            ];
        }

        return [
            'activities' => $activities,
            'people' => $people,
        ];
    }

    public function updateCell(string $spreadsheetId, int $row, int $col, bool $value): void
    {
        $colLetter = $this->columnLetter($col);
        $range = "{$colLetter}{$row}";

        $body = new ValueRange([
            'values' => [[$value ? 'TRUE' : 'FALSE']],
        ]);

        $this->sheets->spreadsheets_values->update($spreadsheetId, $range, $body, [
            'valueInputOption' => 'USER_ENTERED',
        ]);
    }

    public function readHorasMar(string $spreadsheetId): array
    {
        $formatted = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:S300')->getValues() ?? [];

        $raw = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:S300', [
            'valueRenderOption' => 'UNFORMATTED_VALUE',
        ])->getValues() ?? [];

        $colNome = 3;

        $headerRow = $formatted[1] ?? [];
        $horasRow = $formatted[2] ?? [];

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
                continue;
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

    public function appendActivity(string $spreadsheetId, string $dia, string $nome, string $local, int $noites, int $afterRow): void
    {
        $sheetId = $this->firstSheetId($spreadsheetId);

        $this->sheets->spreadsheets->batchUpdate($spreadsheetId, new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => [[
                'insertDimension' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $afterRow,
                        'endIndex' => $afterRow + 1,
                    ],
                    'inheritFromBefore' => true,
                ],
            ]],
        ]));

        $newRow = $afterRow + 1;

        $this->sheets->spreadsheets_values->update(
            $spreadsheetId,
            "A{$newRow}:D{$newRow}",
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
