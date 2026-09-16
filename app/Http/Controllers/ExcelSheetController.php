<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExcelSheetController extends Controller
{
    // Mesma lista de colunas de pessoas do GoogleSheetsReader — usada aqui só
    // para validar que ninguém escreve numa coluna que não devia.
    protected array $personCols = [12, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25];

    public function noitesCampo(GoogleSheetsReader $reader)
    {
        $data = Cache::remember('sheet.noites_campo', now()->addMinutes(15), fn () =>
        $reader->readNoitesCampo(config('services.google_drive.files.noites_campo'))
        );

        $data['people_ranked'] = collect($data['people'])
            ->sortByDesc('total_nights')
            ->values()
            ->all();

        return view('pages.noitescamp', $data);
    }

    public function storeAtividadeNoitesCampo(Request $request, GoogleSheetsReader $reader)
    {
        $validated = $request->validate([
            'dia' => ['required', 'string', 'max:20'],
            'nome' => ['required', 'string', 'max:255'],
            'local' => ['nullable', 'string', 'max:255'],
            'noites' => ['required', 'integer', 'min:0'],
        ]);

        $spreadsheetId = config('services.google_drive.files.noites_campo');

        // Vai buscar dados frescos (não a cache) para ter a certeza de qual é
        // mesmo a última linha real de atividade neste preciso momento.
        $dadosAtuais = $reader->readNoitesCampo($spreadsheetId);
        $ultimaAtividade = end($dadosAtuais['activities']);
        $afterRow = $ultimaAtividade ? $ultimaAtividade['row'] : 3; // linha 3 = cabeçalho, cai logo na 4

        $reader->appendActivity(
            $spreadsheetId,
            $validated['dia'],
            $validated['nome'],
            $validated['local'] ?? '',
            $validated['noites'],
            $afterRow
        );

        Cache::forget('sheet.noites_campo');

        return back()->with('status', 'Atividade adicionada com sucesso.');
    }

    public function toggleParticipacaoNoitesCampo(Request $request, GoogleSheetsReader $reader)
    {
        $validated = $request->validate([
            'row' => ['required', 'integer', 'min:4'],
            'col' => ['required', 'integer'],
            'value' => ['required', 'boolean'],
        ]);

        if (! in_array($validated['col'], $this->personCols, true)) {
            abort(403, 'Coluna não permitida.');
        }

        $reader->updateCell(
            config('services.google_drive.files.noites_campo'),
            $validated['row'],
            $validated['col'],
            $validated['value']
        );

        Cache::forget('sheet.noites_campo');

        return response()->json(['ok' => true]);
    }

    public function horasMar(GoogleSheetsReader $reader)
    {
        $rows = Cache::remember('sheet.horas_mar', now()->addMinutes(15), fn () =>
        $reader->readAsRows(config('services.google_drive.files.horas_mar'))
        );

        return view('horasmar', ['rows' => $rows]);
    }
}
