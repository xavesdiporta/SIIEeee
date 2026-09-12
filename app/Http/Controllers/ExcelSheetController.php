<?php

namespace App\Http\Controllers;

use App\Services\GoogleSheetsReader;
use Illuminate\Support\Facades\Cache;

class ExcelSheetController extends Controller
{
    public function noitesCampo(GoogleSheetsReader $reader)
    {
        $data = Cache::remember('sheet.noites_campo', now()->addMinutes(15), fn () =>
        $reader->readNoitesCampo(config('services.google_drive.files.noites_campo'))
        );

        return view('noites-campo', $data);
    }

    public function horasMar(GoogleSheetsReader $reader)
    {
        $rows = Cache::remember('sheet.horas_mar', now()->addMinutes(15), fn () =>
        $reader->readAsRows(config('services.google_drive.files.horas_mar'))
        );

        return view('horas-mar', ['rows' => $rows]);
    }
}
