<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- 1. EVENT SAAT USER LOGIN ---
        Event::listen(Login::class, function ($event) {
            if ($event->user) {
                $ip = request()->ip();
                $agent = request()->userAgent();
                $now = now();

                // A. Update data Last Login di tabel Users (untuk Dashboard 'Last Seen')
                // Pastikan kolom 'last_login_at' & 'last_login_ip' sudah dibuat di DB
                try {
                    DB::table('users')->where('id', $event->user->id)->update([
                        'last_login_at' => $now,
                        'last_login_ip' => $ip
                    ]);
                } catch (\Exception $e) {
                    // Abaikan error jika kolom belum ada, agar login tetap jalan
                }

                // B. Simpan ke tabel Audit Logs (Wajib ada tabel audit_logs)
                try {
                    DB::table('audit_logs')->insert([
                        'user_id' => $event->user->id,
                        'action' => 'login',
                        'description' => 'User logged in',
                        'ip_address' => $ip,
                        'user_agent' => $agent,
                        'created_at' => $now
                    ]);
                } catch (\Exception $e) {
                    // Log error jika perlu: \Log::error($e->getMessage());
                }
            }
        });

        // --- 2. EVENT SAAT USER LOGOUT ---
        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                try {
                    DB::table('audit_logs')->insert([
                        'user_id' => $event->user->id,
                        'action' => 'logout',
                        'description' => 'User logged out',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'created_at' => now()
                    ]);
                } catch (\Exception $e) {
                    // Abaikan error
                }
            }
        });
    }
}
