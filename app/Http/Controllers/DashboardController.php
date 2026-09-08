<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request, GoogleCalendarService $calendar)
    {
        // Navegação do calendário: espera um parâmetro ?mes=YYYY-MM (ex.: ?mes=2026-10).
        // Sem parâmetro (ou se vier inválido), mostra sempre o mês atual.
        $mesParam = $request->query('mes');

        try {
            $mesReferencia = $mesParam
                ? Carbon::createFromFormat('Y-m', $mesParam)->startOfMonth()
                : Carbon::now();
        } catch (\Exception $e) {
            $mesReferencia = Carbon::now();
        }

        return view('pages.dashboard', [
            'monthEvents'   => $calendar->getEventsForMonth($mesReferencia),
            'calendarUrl'   => $calendar->getPublicCalendarUrl(),
            'mesReferencia' => $mesReferencia,
        ]);
    }

    public function allcalendar()
    {
        return view('pages.faceistabel');
    }
}
