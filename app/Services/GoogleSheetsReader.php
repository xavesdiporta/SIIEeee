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

            $rowNumber = $i + 4; // número real da linha na folha (1-indexado)
            $rawRow = $raw[$i + 3] ?? [];
            $noites = (int) ($row[$colNoites] ?? 0);
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
                    $people[$col]['total_nights'] += $noites;
                }
            }

            $activities[] = [
                'row' => $rowNumber,
                'data' => trim($row[$colData] ?? ''),
                'nome' => $nome,
                'local' => trim($row[$colLocal] ?? ''),
                'noites' => $noites,
                'participantes_cols' => $participantesCols, // colunas (int) marcadas nesta linha
            ];
        }

        return [
            'people' => array_values($people), // ordem original das colunas
            'activities' => $activities,
        ];
    }

    /**
     * Acrescenta uma nova atividade (linha) no fim da folha, nas colunas B-E
     * (Data, Nome, Local, Noites). As colunas das pessoas ficam por marcar.
     */
    public function appendActivity(string $spreadsheetId, string $dia, string $nome, string $local, int $noites): void
    {
        $values = new ValueRange([
            'values' => [[$dia, $nome, $local, $noites]],
        ]);

        $this->sheets->spreadsheets_values->append(
            $spreadsheetId,
            'B:E',
            $values,
            ['valueInputOption' => 'USER_ENTERED', 'insertDataOption' => 'INSERT_ROWS']
        );
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
