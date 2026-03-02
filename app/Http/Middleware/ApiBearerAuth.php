<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;
use Spatie\Activitylog\Models\Activity;

class ApiBearerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            activity('api')
            ->withProperties([
                'status' => 'missing_token',
                'ip' => $request->ip(),
                'rota' => $request->path(),
                'metodo' => $request->method(),
                'browser' => $request->userAgent()
            ])
            ->log('API - Token não informado');
            return response()->json([
                'message' => 'Token Bearer não informado'
            ], 401);
        }

        $token = substr($authHeader, 7);
        $hashedToken = hash('sha256', $token);

        $tokenModel = ApiToken::where('token', $hashedToken)
            ->where('active', true)
            ->first();

        if (!$tokenModel) {
            activity('api')
            ->withProperties([
                'status' => 'invalid_token',
                'ip' => $request->ip(),
                'rota' => $request->path(),
                'metodo' => $request->method(),
                'token_hash' => $hashedToken
            ])
            ->log('API - Token inválido ou inativo');
            return response()->json([
                'message' => 'Token inválido ou inativo'
            ], 401);
        }

        activity('api')
        ->performedOn($tokenModel->empresa)
        ->withProperties([
            'status' => 'success',
            'empresa_id' => $tokenModel->empresa_id,
            'ip' => $request->ip(),
            'rota' => $request->path(),
            'metodo' => $request->method()
        ])
        ->log('API - Acesso autorizado');
        return $next($request);
    }
}
