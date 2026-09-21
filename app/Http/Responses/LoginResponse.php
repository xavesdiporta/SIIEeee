<?php


namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $url = match (Auth::user()->seccao ?? 'cla') {
            'lobitos' => route('alcateia.dashboard'),
            'exploradores' => route('expedicao.dashboard'),
            'pioneiros' => route('comunidade.dashboard'),
            'cla' => route('cla.dashboard'),
            default => route('dashboard'),
        };

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended($url);
    }
}
