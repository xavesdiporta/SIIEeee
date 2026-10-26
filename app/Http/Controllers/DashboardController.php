<?php

namespace App\Http\Controllers;

use App\Models\ProgressNote;
use App\Services\GoogleCalendarService;
use App\Traits\HasFaceisCategorias;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

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

    public function allcalendar()
    {
        $user = Auth::user();
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
        ]);
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
