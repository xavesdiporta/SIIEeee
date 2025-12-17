<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }

    public function allcalendar()
    {
        return view('pages.faceistabel');
    }
}
