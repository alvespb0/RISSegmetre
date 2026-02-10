<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;

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
            return response()->json([
                'message' => 'Token inválido ou inativo'
            ], 401);
        }

        return $next($request);
    }
}
