<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsReader
{
    protected Sheets $sheets;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(base_path(config('services.google_drive.credentials')));
        $client->addScope(Sheets::SPREADSHEETS_READONLY);

        $this->sheets = new Sheets($client);
    }

    /**
     * Lê um intervalo de uma Google Sheet e devolve as linhas já associadas
     * ao cabeçalho (1ª linha do intervalo).
     *
     * $range por omissão lê a primeira folha inteira (ex: "Folha1").
     * Se a tua folha tiver outro nome, passa-o (ex: "Registo!A1:Z").
     */
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
                // completa a linha com strings vazias se tiver menos colunas que o cabeçalho
                $row = array_pad($row, $numCols, '');
                return array_combine($header, array_slice($row, 0, $numCols));
            }, $values),
            fn ($row) => count(array_filter($row, fn ($v) => $v !== '')) > 0
        ));
    }

    /**
     * Leitor dedicado à folha "Noites de Campo".
     *
     * Esta folha tem uma estrutura irregular: colunas B-F são Data/Nome/Local/
     * Noites/Acantonamento, depois há colunas vazias/ocultas, e as pessoas
     * aparecem em colunas não-contíguas (M, depois Q até Z) com o nome na
     * linha 3. A participação é marcada com um X (ou qualquer texto) na
     * célula da pessoa, na linha da atividade.
     */
    public function readNoitesCampo(string $spreadsheetId): array
    {
        $response = $this->sheets->spreadsheets_values->get($spreadsheetId, 'A1:AA1000');
        $values = $response->getValues() ?? [];

        $colData = 1;   // coluna B
        $colNome = 2;   // coluna C
        $colLocal = 3;  // coluna D
        $colNoites = 4; // coluna E

        // colunas M, Q, R, S, T, U, V, W, X, Y, Z (0-indexado a partir de A=0)
        $personCols = [12, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];

        $headerRow = $values[2] ?? []; // nomes das pessoas ficam na linha 3 da folha

        $people = [];
        foreach ($personCols as $col) {
            $name = trim($headerRow[$col] ?? '');
            if ($name !== '') {
                $people[$col] = ['name' => $name, 'total_activities' => 0, 'total_nights' => 0];
            }
        }

        $activities = [];
        foreach (array_slice($values, 3) as $row) {
            $nome = trim($row[$colNome] ?? '');
            if ($nome === '') {
                continue; // linha vazia ou de separação
            }

            $noites = (int) ($row[$colNoites] ?? 0);
            $participantes = [];

            foreach ($people as $col => $person) {
                if (trim($row[$col] ?? '') !== '') {
                    $participantes[] = $person['name'];
                    $people[$col]['total_activities']++;
                    $people[$col]['total_nights'] += $noites;
                }
            }

            $activities[] = [
                'data' => trim($row[$colData] ?? ''),
                'nome' => $nome,
                'local' => trim($row[$colLocal] ?? ''),
                'noites' => $noites,
                'participantes' => $participantes,
            ];
        }

        $peopleList = array_values($people);
        usort($peopleList, fn ($a, $b) => $b['total_nights'] <=> $a['total_nights']);

        return [
            'people' => $peopleList,
            'activities' => $activities,
        ];
    }
}
