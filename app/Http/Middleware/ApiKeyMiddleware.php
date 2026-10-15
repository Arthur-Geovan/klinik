<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-API-KEY');
        $validKey = config('services.klinik.api_key');

        if (! is_string($providedKey) || ! is_string($validKey) || $validKey === '' || ! hash_equals($validKey, $providedKey)) {
            return response()->json([
                'status' => false,
                'message' => 'API key tidak valid atau tidak ditemukan.',
                'data' => null,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}