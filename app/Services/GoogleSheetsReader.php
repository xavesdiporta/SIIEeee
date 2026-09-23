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
        $response = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:ZZ1000');
        $rows = $response->getValues() ?? [];

        if (empty($rows)) {
            return ['activities' => [], 'people' => []];
        }

        $activities = [];
        $adjustmentRowIndex = null;
        $noitesCampoRowIndex = null;

        // Helper para validar marcações das checkboxes das pessoas
        $isPersonChecked = function ($val): bool {
            if (is_bool($val)) return $val;
            if (is_numeric($val)) return (float)$val > 0;
            if (is_string($val)) {
                $v = strtolower(trim($val));
                return in_array($v, ['true', '1', 'x', 'sim', 'vade', 'verdadeiro', 'v', '●'], true);
            }
            return false;
        };

        // 1. Analisar as linhas da folha
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex < 3) {
                continue; // Pula cabeçalhos
            }

            $fullRowText = strtolower(implode(' ', $row));

            // Deteta a linha de Ajustes
            if (
                str_contains($fullRowText, 'ajuste') ||
                str_contains($fullRowText, 'anteriores') ||
                str_contains($fullRowText, 'transito') ||
                str_contains($fullRowText, 'trânsito')
            ) {
                $adjustmentRowIndex = $rowIndex;
                continue;
            }

            // Deteta a linha "Noites de campo:" (totais finais)
            if (str_contains($fullRowText, 'noites de campo')) {
                $noitesCampoRowIndex = $rowIndex;
                continue;
            }

            // Ignora linhas de subtotal
            if (str_contains($fullRowText, 'total c/') || str_contains($fullRowText, 'total lobitos') || str_contains($fullRowText, 'total exploradores')) {
                continue;
            }

            // Detetar posição exata das colunas para esta linha (evita desfasamento de colunas)
            $col0 = trim((string)($row[0] ?? ''));
            $col1 = trim((string)($row[1] ?? ''));
            $col2 = trim((string)($row[2] ?? ''));

            $col0IsDate = preg_match('/\d{1,4}[\/-]\d{1,2}[\/-]\d{1,4}/', $col0);
            $col1IsDate = preg_match('/\d{1,4}[\/-]\d{1,2}[\/-]\d{1,4}/', $col1);

            if ($col0IsDate) {
                // Data na Coluna A (idx 0)
                $dataVal = $col0;
                $nomeVal = $col1;
                $localVal = $col2;
                $noitesRaw = $row[3] ?? '0';
                $acantRaw = $row[4] ?? null;
            } elseif ($col1IsDate) {
                // Data na Coluna B (idx 1)
                $dataVal = $col1;
                $nomeVal = $col2;
                $localVal = $row[3] ?? '';
                $noitesRaw = $row[4] ?? '0';
                $acantRaw = $row[5] ?? null;
            } else {
                // Fallback inteligente
                if (!empty($col1)) {
                    $dataVal = $col0;
                    $nomeVal = $col1;
                    $localVal = $col2;
                    $noitesRaw = $row[3] ?? '0';
                    $acantRaw = $row[4] ?? null;
                } elseif (!empty($col2)) {
                    $dataVal = $col1;
                    $nomeVal = $col2;
                    $localVal = $row[3] ?? '';
                    $noitesRaw = $row[4] ?? '0';
                    $acantRaw = $row[5] ?? null;
                } else {
                    continue; // Linha vazia ou não identificada
                }
            }

            // Se não tem nome de atividade válido, ignora
            if (empty($nomeVal)) {
                continue;
            }

            // Converte noites para float
            $noitesVal = (float) str_replace(',', '.', $noitesRaw);

            // Acantonamento SÓ é true se tiver explicitamente sim/true/x/1/v
            $acantStr = strtolower(trim((string)$acantRaw));
            $acantVal = in_array($acantStr, ['true', '1', 'x', 'sim', 'vade', 'verdadeiro', 'v', '●'], true);

            // Identifica participantes marcados para esta atividade
            $participantesCols = [];
            foreach ($this->personCols as $colIndex) {
                if (isset($row[$colIndex]) && $isPersonChecked($row[$colIndex])) {
                    $participantesCols[] = $colIndex;
                }
            }

            $activities[] = [
                'row' => $rowIndex + 1,
                'data' => $dataVal,
                'nome' => $nomeVal,
                'local' => $localVal,
                'noites' => $noitesVal,
                'acantonamento' => $acantVal,
                'participantes_cols' => $participantesCols,
            ];
        }

        $people = [];

        // 2. Calcular totais de cada pessoa
        foreach ($this->personCols as $colIndex) {
            $personName = trim($rows[2][$colIndex] ?? '');
            if (empty($personName)) {
                continue;
            }

            $totalNoitesCheckboxes = 0;
            $totalAtividadesCount = 0;

            foreach ($activities as $act) {
                if (in_array($colIndex, $act['participantes_cols'], true)) {
                    $totalAtividadesCount++;

                    if (!$act['acantonamento']) {
                        $totalNoitesCheckboxes += $act['noites'];
                    }
                }
            }

            // Obter o valor do Ajuste das secções anteriores
            $ajuste = 0;
            if ($adjustmentRowIndex !== null && isset($rows[$adjustmentRowIndex][$colIndex])) {
                $rawVal = str_replace(',', '.', trim((string) $rows[$adjustmentRowIndex][$colIndex]));
                if (is_numeric($rawVal)) {
                    $ajuste = (float) $rawVal;
                }
            }

            $totalNights = $totalNoitesCheckboxes + $ajuste;

            // Salvaguarda: Se a linha Ajustes não deu valor, mas existe o total na linha "Noites de campo:"
            if ($ajuste === 0.0 && $noitesCampoRowIndex !== null && isset($rows[$noitesCampoRowIndex][$colIndex])) {
                $rawTotal = str_replace(',', '.', trim((string) $rows[$noitesCampoRowIndex][$colIndex]));
                if (is_numeric($rawTotal) && (float)$rawTotal > 0) {
                    $totalNights = (float) $rawTotal;
                    $ajuste = max(0, $totalNights - $totalNoitesCheckboxes);
                }
            }

            $people[] = [
                'col' => $colIndex,
                'name' => $personName,
                'past_nights' => $ajuste,
                'total_nights' => $totalNights,
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
