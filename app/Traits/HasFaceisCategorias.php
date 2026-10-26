<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;

trait HasFaceisCategorias
{
    /**
     * Devolve as 6 dimensões educativas (F.A.C.E.I.S.).
     */
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

    /**
     * Mapeamento de cada dimensão nas suas 3 subcategorias / categorias (1, 2 e 3).
     */
    protected function subcategorias(): array
    {
        return [
            'F' => [
                1 => ['title' => 'Desempenho', 'refs' => ['F1']],
                2 => ['title' => 'Auto-conhecimento', 'refs' => ['F2', 'F3']],
                3 => ['title' => 'Bem-estar Físico', 'refs' => ['F4', 'F5', 'F6']],
            ],
            'A' => [
                1 => ['title' => 'Relacionamento e Sensibilidade', 'refs' => ['A1', 'A2', 'A3']],
                2 => ['title' => 'Equilíbrio emocional', 'refs' => ['A4']],
                3 => ['title' => 'Auto-estima', 'refs' => ['A5', 'A6']],
            ],
            'C' => [
                1 => ['title' => 'Autonomia', 'refs' => ['C1', 'C2', 'C3']],
                2 => ['title' => 'Responsabilidade', 'refs' => ['C4', 'C5', 'C6']],
                3 => ['title' => 'Coerência', 'refs' => ['C7', 'C8']],
            ],
            'E' => [
                1 => ['title' => 'Descoberta', 'refs' => ['E1', 'E2', 'E3']],
                2 => ['title' => 'Aprofundamento', 'refs' => ['E4', 'E5', 'E6']],
                3 => ['title' => 'Serviço', 'refs' => ['E7', 'E8']],
            ],
            'I' => [
                1 => ['title' => 'Procura do conhecimento', 'refs' => ['I1', 'I2', 'I3']],
                2 => ['title' => 'Resolução de problemas', 'refs' => ['I4', 'I5']],
                3 => ['title' => 'Criatividade e Expressão', 'refs' => ['I6', 'I7']],
            ],
            'S' => [
                1 => ['title' => 'Exercer ativamente Cidadania', 'refs' => ['S1', 'S2', 'S3']],
                2 => ['title' => 'Solidariedade e tolerância', 'refs' => ['S4', 'S5', 'S6']],
                3 => ['title' => 'Interação e cooperação', 'refs' => ['S7']],
            ],
        ];
    }

    /**
     * Mapeamento direto de cada ref para a sua Categoria (1, 2 ou 3).
     * Ex: ['F1' => 1, 'F2' => 2, 'F4' => 3, ...]
     */
    protected function refParaCategoria(): array
    {
        $map = [];
        foreach ($this->subcategorias() as $dim => $cats) {
            foreach ($cats as $catNum => $catData) {
                foreach ($catData['refs'] as $ref) {
                    $map[$ref] = $catNum;
                }
            }
        }
        return $map;
    }

    /**
     * Devolve a lista de refs associadas a uma categoria específica (1, 2 ou 3).
     */
    protected function refsPorCategoria(int $categoria): array
    {
        $refs = [];
        foreach ($this->subcategorias() as $dim => $cats) {
            if (isset($cats[$categoria])) {
                $refs = array_merge($refs, $cats[$categoria]['refs']);
            }
        }
        return $refs;
    }

    /**
     * Dado um reference (ex: "F3"), devolve a letra da dimensão a que pertence (ex: "F").
     */
    protected function categoriaDoRef(string $reference): string
    {
        foreach ($this->categorias() as $cat) {
            if (in_array($reference, $cat['refs'], true)) {
                return $cat['label'];
            }
        }

        return preg_replace('/[0-9]/', '', $reference) ?: $reference;
    }

    /**
     * Calcula o estado das etapas de progresso de um utilizador:
     * - Para concluir a Categoria 1 (1ª Etapa), tem de ter todos os refs da Cat. 1 em todas as 6 F.A.C.E.I.S.
     * - Para concluir a Categoria 2 (2ª Etapa), tem de ter todos os refs da Cat. 2 em todas as 6 F.A.C.E.I.S.
     * - Para concluir a Categoria 3 (3ª Etapa / Mérito), tem de ter todos os refs da Cat. 3 em todas as 6 F.A.C.E.I.S.
     */
    protected function calcularProgressoEtapas(array $userRefs): array
    {
        $sub = $this->subcategorias();

        $catCompleta = [1 => true, 2 => true, 3 => true];
        $totalPorCat = [1 => 0, 2 => 0, 3 => 0];
        $concluidosPorCat = [1 => 0, 2 => 0, 3 => 0];

        foreach ([1, 2, 3] as $catNum) {
            foreach ($sub as $dim => $cats) {
                $catRefs = $cats[$catNum]['refs'] ?? [];
                foreach ($catRefs as $r) {
                    $totalPorCat[$catNum]++;
                    if (in_array($r, $userRefs, true)) {
                        $concluidosPorCat[$catNum]++;
                    } else {
                        $catCompleta[$catNum] = false;
                    }
                }
            }
        }

        $etapasConcluidas = 0;
        if ($catCompleta[1]) $etapasConcluidas = 1;
        if ($catCompleta[1] && $catCompleta[2]) $etapasConcluidas = 2;
        if ($catCompleta[1] && $catCompleta[2] && $catCompleta[3]) $etapasConcluidas = 3;

        return [
            'cat1_completa'      => $catCompleta[1],
            'cat2_completa'      => $catCompleta[2],
            'cat3_completa'      => $catCompleta[3],
            'etapas_concluidas'  => $etapasConcluidas,
            'total_por_cat'      => $totalPorCat,
            'concluidos_por_cat' => $concluidosPorCat,
        ];
    }

    /**
     * Endpoint AJAX para atualizar a categoria ativa de um escuteiro.
     */
    public function setCategoria(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'categoria' => ['required', 'integer', 'in:1,2,3'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->categoria_ativa = $validated['categoria'];
        $user->save();

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'categoria' => (int) $user->categoria_ativa,
        ]);
    }
}
