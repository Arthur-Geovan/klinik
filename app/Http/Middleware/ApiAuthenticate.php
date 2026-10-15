<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda harus login terlebih dahulu.',
                'data' => null,
                'errors' => ['authorization' => ['Sesi login tidak ditemukan.']],
            ], 401);
        }

        return $next($request);
    }
}