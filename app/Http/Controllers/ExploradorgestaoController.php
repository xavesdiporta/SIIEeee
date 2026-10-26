<?php

namespace App\Http\Controllers;

use App\Models\ProgressNote;
use App\Models\User;
use App\Traits\HasFaceisCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExploradorGestaoController extends Controller
{
    use HasFaceisCategorias;

    public function index()
    {
        $categorias = $this->categorias();
        $totalRefsAll = collect($categorias)->sum(fn ($c) => count($c['refs'] ?? []));

        $exploradores = User::membros()->where('seccao', 'exploradores')->orderBy('name')->get() ?? collect();

        $matriz = ProgressNote::whereIn('user_id', $exploradores->pluck('id'))
            ->where('status', 'approved')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($notas) => $notas->pluck('reference')->all());

        $progressoEtapas = [];
        foreach ($exploradores as $explorador) {
            $userRefs = $matriz[$explorador->id] ?? [];
            $progressoEtapas[$explorador->id] = $this->calcularProgressoEtapas($userRefs);
        }

        return view('pages.dashboards.exploradores', [
            'categorias' => $categorias,
            'subcategorias' => $this->subcategorias(),
            'refCategorias' => $this->refParaCategoria(),
            'totalRefsAll' => $totalRefsAll,
            'exploradores' => $exploradores,
            'matriz' => $matriz,
            'progressoEtapas' => $progressoEtapas,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);

        $baseSlug = Str::slug($validated['nome'], '');
        $email = $baseSlug . '@gmail.com';

        $contador = 1;
        while (User::where('email', $email)->exists()) {
            $contador++;
            $email = $baseSlug . $contador . '@gmail.com';
        }

        User::create([
            'name' => $validated['nome'],
            'email' => $email,
            'seccao' => 'exploradores',
            'chefes' => false,
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
                [
                    'status' => 'approved',
                    'category' => $this->categoriaDoRef($validated['reference']),
                    'proposal' => '',
                    'note' => '',
                ]
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

        try {
            foreach ($validated['changes'] as $change) {
                if ($change['value']) {
                    ProgressNote::updateOrCreate(
                        ['user_id' => $change['user_id'], 'reference' => $change['reference']],
                        [
                            'status' => 'approved',
                            'category' => $this->categoriaDoRef($change['reference']),
                            'proposal' => '',
                            'note' => '',
                        ]
                    );
                } else {
                    ProgressNote::where('user_id', $change['user_id'])
                        ->where('reference', $change['reference'])
                        ->delete();
                }
            }
        } catch (\Throwable $e) {
            // TEMPORÁRIO — remover depois de encontrarmos o erro
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['ok' => true, 'count' => count($validated['changes'])]);
    }
}
