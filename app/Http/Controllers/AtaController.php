<?php

namespace App\Http\Controllers;

use App\Models\Ata;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AtaController extends Controller
{
    /**
     * Guarda uma nova ata: move o ficheiro para public/atas e cria o registo.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'dia' => ['required', 'date'],
            'ficheiro' => ['required', 'file', 'mimes:pdf', 'max:10240'], // até 10MB
        ]);

        $file = $request->file('ficheiro');
        $filename = now()->format('Y-m-d') . '-' . str($validated['nome'])->slug() . '.' . $file->getClientOriginalExtension();

        // Guarda diretamente em public/atas (acessível via asset('atas/...'))
        $file->move(public_path('atas'), $filename);

        Ata::create([
            'nome' => $validated['nome'],
            'dia' => $validated['dia'],
            'ficheiro' => 'atas/' . $filename,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Ata adicionada com sucesso.');
    }

    /**
     * Devolve as atas no formato que o FullCalendar espera (GET /api/events).
     * Cada ata vira um evento no dia em que a reunião aconteceu.
     */
    public function events()
    {
        return Ata::orderBy('dia')->get()->map(fn (Ata $ata) => [
            'id' => $ata->id,
            'title' => $ata->nome,
            'start' => $ata->dia->toDateString(),
            'allDay' => true,
            'url' => asset($ata->ficheiro),
        ]);
    }
}
