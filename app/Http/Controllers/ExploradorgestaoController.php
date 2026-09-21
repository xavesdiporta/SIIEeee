<?php

namespace App\Http\Controllers;

use App\Models\ProgressNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExploradorgestaoController extends Controller
{
    // Mesma estrutura de dimensões/objetivos usada na página individual do Explorador.
    protected function categorias(): array
    {
        return [
            ['label' => 'F', 'name' => 'Físico', 'color' => '#16a34a', 'refs' => ['F1', 'F2', 'F3', 'F4', 'F5', 'F6']],
            ['label' => 'A', 'name' => 'Afectivo', 'color' => '#dc2626', 'refs' => ['A1', 'A2', 'A3', 'A4', 'A5', 'A6']],
            ['label' => 'C', 'name' => 'Carácter', 'color' => '#2563eb', 'refs' => ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8']],
            ['label' => 'E', 'name' => 'Espiritual', 'color' => '#9333ea', 'refs' => ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8']],
            ['label' => 'I', 'name' => 'Intelectual', 'color' => '#f97316', 'refs' => ['I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7']],
            ['label' => 'S', 'name' => 'Social', 'color' => '#eab308', 'refs' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7']],
        ];
    }

    public function index()
    {
        $categorias = $this->categorias();
        $totalRefsAll = collect($categorias)->sum(fn ($c) => count($c['refs'] ?? []));

        $exploradores = User::where('seccao', 'exploradores')->orderBy('name')->get() ?? collect();

        $matriz = ProgressNote::whereIn('user_id', $exploradores->pluck('id'))
            ->where('status', 'approved')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($notas) => $notas->pluck('reference')->all());

        return view('pages.dashboards.exploradores', [
            'categorias'   => $categorias,
            'totalRefsAll' => $totalRefsAll,
            'exploradores' => $exploradores,
            'matriz'       => $matriz,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        // Conta sem login real — só para existir um user_id a associar ao progresso.
        // O email é interno e nunca é mostrado nem usado para entrar na app.
        $emailInterno = Str::slug($validated['nome']) . '-' . Str::random(6) . '@exploradores.interno';

        User::create([
            'name' => $validated['nome'],
            'email' => $emailInterno,
            'seccao' => 'exploradores',
            'password' => Hash::make(Str::random(32)),
        ]);

        return back()->with('status', 'Explorador adicionado com sucesso.');
    }

    public function toggleObjetivo(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'reference' => ['required', 'string', 'max:10'],
            'value' => ['required', 'boolean'],
        ]);

        if ($validated['value']) {
            ProgressNote::updateOrCreate(
                ['user_id' => $validated['user_id'], 'reference' => $validated['reference']],
                ['status' => 'approved']
            );
        } else {
            ProgressNote::where('user_id', $validated['user_id'])
                ->where('reference', $validated['reference'])
                ->delete();
        }

        return response()->json(['ok' => true]);
    }

    public function toggleObjetivoBulk(Request $request)
    {
        $validated = $request->validate([
            'changes' => ['required', 'array', 'min:1'],
            'changes.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'changes.*.reference' => ['required', 'string', 'max:10'],
            'changes.*.value' => ['required', 'boolean'],
        ]);

        foreach ($validated['changes'] as $change) {
            if ($change['value']) {
                ProgressNote::updateOrCreate(
                    ['user_id' => $change['user_id'], 'reference' => $change['reference']],
                    ['status' => 'approved']
                );
            } else {
                ProgressNote::where('user_id', $change['user_id'])
                    ->where('reference', $change['reference'])
                    ->delete();
            }
        }

        return response()->json(['ok' => true, 'count' => count($validated['changes'])]);
    }
}
