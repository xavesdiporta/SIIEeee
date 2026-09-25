<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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
        return view('pages.dashboard', $this->calendarData($request, $calendar));
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
        return view('pages.faceistabel');
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
