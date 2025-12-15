<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth; // <--- JANGAN LUPA IMPORT INI
use Illuminate\Http\Request;         // <--- DAN INI

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // --- TAMBAHKAN BAGIAN INI UNTUK MENGATUR REDIRECT USER ---
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
        // ---------------------------------------------------------

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();