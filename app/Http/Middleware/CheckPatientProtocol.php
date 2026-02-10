<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPatientProtocol
{
    /**
     * Handle an incoming request.
     * Verifica se a sessão contém o protocolo do paciente.
     * Se não existir, retorna 403 (Acesso não autorizado).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('patient_protocol') || empty(session('patient_protocol'))) {
            abort(403, 'Acesso não autorizado. É necessário fazer login com protocolo válido.');
        }

        return $next($request);
    }
}
