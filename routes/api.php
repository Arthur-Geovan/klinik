<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\DokterApiController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Middleware\ApiAuthenticate;
use Illuminate\Support\Facades\Route;

Route::get('pasien', [ApiController::class, 'pasienApiKey']);

Route::get('dokter', [DokterApiController::class, 'index'])
    ->middleware('api.key')
    ->name('api.dokter.index');

Route::get('dokter5', [DokterApiController::class, 'index'])
    ->middleware('api.key')
    ->name('api.dokter5.index');

Route::middleware(ApiAuthenticate::class)->group(function (): void {
    Route::post('/reservasi', [ReservasiController::class, 'store'])
        ->name('api.reservasi.store');
});