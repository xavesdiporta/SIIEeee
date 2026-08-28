<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProgressNoteController extends Controller
{
    /**
     * Mostra o formulário para submeter uma nota de progresso.
     * $reference é opcional (ex: "F3") — vem do botão "Submeter" da dashboard.
     */
    public function create(?string $reference = null): View
    {
        return view('progress-notes.create', [
            'reference' => $reference,
        ]);
    }

    /**
     * Recebe a submissão do formulário.
     *
     * IMPORTANTE: ainda NÃO grava nada em base de dados — isso fica para o próximo
     * passo (criar a migration + o Model ProgressNote e substituir isto por um
     * ProgressNote::create([...])). Por agora só valida os dados e confirma ao
     * utilizador, para o fluxo completo (dashboard -> submeter -> voltar) não dar erro.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:10'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        // TODO (próximo passo): substituir por persistência real, por exemplo:
        // \App\Models\ProgressNote::create([
        //     'user_id' => $request->user()->id,
        //     'reference' => $validated['reference'],
        //     'description' => $validated['description'],
        //     'status' => 'pending',
        // ]);

        return redirect()
            ->route('dashboard')
            ->with('status', "Nota para {$validated['reference']} recebida (ainda não gravada em BD — próximo passo).");
    }
}
