<?php

namespace App\Http\Controllers;

use App\Models\ProgressNote;
use App\Models\User;
use App\Services\GoogleCalendarService;
use App\Traits\HasFaceisCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    use HasFaceisCategorias;

    public function index(Request $request, GoogleCalendarService $calendar)
    {
        $seccao = Auth::user()?->seccao ?? 'cla';

        return match ($seccao) {
            'lobitos'      => redirect()->route('alcateia.dashboard'),
            'exploradores' => redirect()->route('expedicao.dashboard'),
            'pioneiros'    => redirect()->route('comunidade.dashboard'),
            default        => redirect()->route('cla.dashboard'),
        };
    }

    public function lobitos(Request $request, GoogleCalendarService $calendar)
    {
        return view('pages.dashboards.lobitos', $this->calendarData($request, $calendar));
    }

    public function exploradores(Request $request, GoogleCalendarService $calendar)
    {
        return view('pages.dashboards.exploradores', $this->calendarData($request, $calendar));
    }

    public function pioneiros(Request $request, GoogleCalendarService $calendar)
    {
        return view('pages.dashboards.pioneiros', $this->calendarData($request, $calendar));
    }

    public function cla(Request $request, GoogleCalendarService $calendar)
    {
        $user = Auth::user();
        $isChefe = (bool) ($user?->chefe || $user?->chefes);

        // Chefe do Clã vê a tabela de gestão diretamente na dashboard principal
        if ($isChefe) {
            $caminheiros = User::membros()->where('seccao', 'cla')->orderBy('name')->get() ?? collect();

            $matriz = ProgressNote::whereIn('user_id', $caminheiros->pluck('id'))
                ->where('status', 'approved')
                ->get()
                ->groupBy('user_id')
                ->map(fn ($notas) => $notas->pluck('reference')->all());

            $progressoEtapas = [];
            foreach ($caminheiros as $caminheiro) {
                $userRefs = $matriz[$caminheiro->id] ?? [];
                $progressoEtapas[$caminheiro->id] = $this->calcularProgressoEtapas($userRefs);
            }

            $categorias = $this->categorias();
            $totalRefsAll = collect($categorias)->sum(fn ($c) => count($c['refs'] ?? []));

            return view('pages.dashboards.cla-gestao', [
                'caminheiros'     => $caminheiros,
                'matriz'          => $matriz,
                'progressoEtapas' => $progressoEtapas,
                'categorias'      => $categorias,
                'subcategorias'   => $this->subcategorias(),
                'refCategorias'   => $this->refParaCategoria(),
                'totalRefsAll'    => $totalRefsAll,
            ]);
        }

        $completedRefs = $user
            ? ProgressNote::where('user_id', $user->id)
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray()
            : [];

        $progressoEtapas = $this->calcularProgressoEtapas($completedRefs);
        $subcategorias   = $this->subcategorias();
        $refCategorias   = $this->refParaCategoria();
        $categories      = $this->categorias();

        // Percentagens dos 3 pilares / etapas
        // Comunidade = Categoria 1
        $totalCat1 = $progressoEtapas['total_por_cat'][1] ?? 1;
        $doneCat1  = $progressoEtapas['concluidos_por_cat'][1] ?? 0;
        $comunidadePercent = $totalCat1 > 0 ? round(($doneCat1 / $totalCat1) * 100) : 0;

        // Partida = Categoria 2
        $totalCat2 = $progressoEtapas['total_por_cat'][2] ?? 1;
        $doneCat2  = $progressoEtapas['concluidos_por_cat'][2] ?? 0;
        $partidaPercent = $totalCat2 > 0 ? round(($doneCat2 / $totalCat2) * 100) : 0;

        // Serviço = Categoria 3
        $totalCat3 = $progressoEtapas['total_por_cat'][3] ?? 1;
        $doneCat3  = $progressoEtapas['concluidos_por_cat'][3] ?? 0;
        $servicoPercent = $totalCat3 > 0 ? round(($doneCat3 / $totalCat3) * 100) : 0;

        $data = array_merge($this->calendarData($request, $calendar), [
            'completedRefs'     => $completedRefs,
            'progressoEtapas'   => $progressoEtapas,
            'subcategorias'     => $subcategorias,
            'refCategorias'     => $refCategorias,
            'categories'        => $categories,
            'comunidadePercent' => $comunidadePercent,
            'partidaPercent'    => $partidaPercent,
            'servicoPercent'    => $servicoPercent,
            'categoriaAtiva'    => (int) ($user?->categoria_ativa ?? 1),
        ]);

        return view('pages.dashboard', $data);
    }

    protected function calendarData(Request $request, GoogleCalendarService $calendar): array
    {
        $mesParam = $request->query('mes');

        try {
            $mesReferencia = $mesParam
                ? Carbon::createFromFormat('Y-m', $mesParam)->startOfMonth()
                : Carbon::now();
        } catch (\Exception $e) {
            $mesReferencia = Carbon::now();
        }

        return [
            'monthEvents'   => $calendar->getEventsForMonth($mesReferencia),
            'calendarUrl'   => $calendar->getPublicCalendarUrl(),
            'mesReferencia' => $mesReferencia,
        ];
    }

    public function allcalendar(Request $request)
    {
        $user = Auth::user();
        $isChefe = (bool) ($user?->chefe || $user?->chefes);

        // Se for chefe do Clã e não solicitou explicitamente a vista grelha individual (?view=grelha)
        if ($isChefe && $user?->seccao === 'cla' && $request->query('view') !== 'grelha') {
            $caminheiros = User::membros()->where('seccao', 'cla')->orderBy('name')->get() ?? collect();

            $matriz = ProgressNote::whereIn('user_id', $caminheiros->pluck('id'))
                ->where('status', 'approved')
                ->get()
                ->groupBy('user_id')
                ->map(fn ($notas) => $notas->pluck('reference')->all());

            $progressoEtapas = [];
            foreach ($caminheiros as $caminheiro) {
                $userRefs = $matriz[$caminheiro->id] ?? [];
                $progressoEtapas[$caminheiro->id] = $this->calcularProgressoEtapas($userRefs);
            }

            $categorias = $this->categorias();
            $totalRefsAll = collect($categorias)->sum(fn ($c) => count($c['refs'] ?? []));

            return view('pages.dashboards.cla-gestao', [
                'caminheiros'     => $caminheiros,
                'matriz'          => $matriz,
                'progressoEtapas' => $progressoEtapas,
                'categorias'      => $categorias,
                'subcategorias'   => $this->subcategorias(),
                'refCategorias'   => $this->refParaCategoria(),
                'totalRefsAll'    => $totalRefsAll,
            ]);
        }

        // Vista de grelha individual
        $completedRefs = $user
            ? ProgressNote::where('user_id', $user->id)
                ->where('status', 'approved')
                ->pluck('reference')
                ->toArray()
            : [];

        $progressoEtapas = $this->calcularProgressoEtapas($completedRefs);
        $subcategorias   = $this->subcategorias();
        $refCategorias   = $this->refParaCategoria();
        $categories      = $this->categorias();

        return view('pages.faceistabel', [
            'completedRefs'   => $completedRefs,
            'progressoEtapas' => $progressoEtapas,
            'subcategorias'   => $subcategorias,
            'refCategorias'   => $refCategorias,
            'categories'      => $categories,
            'categoriaAtiva'  => (int) ($user?->categoria_ativa ?? 1),
            'isChefe'         => $isChefe,
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
            'seccao' => 'cla',
            'chefes' => false,
            'password' => Hash::make(Str::random(32)),
        ]);

        return back()->with('status', 'Caminheiro adicionado com sucesso.');
    }
    public function noites()
    {
        return view('pages.noitescamp');
    }
    public function horasmar()
    {
        return view('pages.horasmar');
    }
}
