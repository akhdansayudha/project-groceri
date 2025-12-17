<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MidtransCallbackController;

// URL ini otomatis akan berawalan /api/
// Jadi URL akhirnya: domain.com/api/midtrans/callback
Route::post('midtrans/callback', [MidtransCallbackController::class, 'handle']);
