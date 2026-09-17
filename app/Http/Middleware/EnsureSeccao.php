<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeccao
{
    /**
     * Handle an incoming request.
     * Verifica se o utilizador autenticado pertence à secção exigida pela rota.
     * Admins (is_admin = true) têm acesso livre a todas as secções.
     */
    public function handle(Request $request, Closure $next, string $seccao): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins têm acesso a tudo
        if ($user->is_admin ?? false) {
            return $next($request);
        }

        // Verifica se a secção do utilizador corresponde à secção exigida
        if ($user->seccao !== $seccao) {
            // Redireciona para o dashboard correto da secção do utilizador
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
