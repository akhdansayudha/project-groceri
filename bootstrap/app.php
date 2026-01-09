<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // Pastikan baris ini ada agar routes/api.php terbaca
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->trustProxies(at: '*');

        // 1. Disable CSRF untuk Webhook Midtrans
        $middleware->validateCsrfTokens(except: [
            'api/midtrans/callback',
        ]);

        // 2. Logic Redirect User (Kode Anda sebelumnya)
        $middleware->redirectUsersTo(function (Request $request) {
            // Cek user yang sedang login
            $user = Auth::user();
            $role = $user->role ?? 'client';

            // Arahkan ke dashboard sesuai role
            switch ($role) {
                case 'admin':
                    return route('admin.dashboard');
                case 'staff':
                    return route('staff.dashboard');
                default:
                    return route('client.dashboard');
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
