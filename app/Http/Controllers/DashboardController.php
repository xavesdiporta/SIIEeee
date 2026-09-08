<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;

class DashboardController extends Controller
{
    public function index(GoogleCalendarService $calendar)
    {
        return view('pages.dashboard', [
            'monthEvents' => $calendar->getEventsForMonth(),
            'calendarUrl' => $calendar->getPublicCalendarUrl(),
        ]);
    }

    public function allcalendar()
    {
        return view('pages.faceistabel');
    }
}
