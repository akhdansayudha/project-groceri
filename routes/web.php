<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLLERS IMPORT ---
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;

// Client Controllers
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\RequestController;
use App\Http\Controllers\Client\WorkspaceController as ClientWorkspaceController;
use App\Http\Controllers\Client\WalletController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\SettingController;
use App\Http\Controllers\Client\SupportController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\WorkspaceController as AdminWorkspaceController;
use App\Http\Controllers\Admin\StaffPerformanceController;

// Staff Controllers
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\NotificationController as StaffNotificationController;
use App\Http\Controllers\Staff\ProjectController as StaffProjectController;
use App\Http\Controllers\Staff\PerformanceController;
use App\Http\Controllers\Staff\WalletController as StaffWalletController;
use App\Http\Controllers\Staff\SettingController as StaffSettingController;

/*
|--------------------------------------------------------------------------
| PUBLIC & AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // FORGOT PASSWORD ROUTES
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::controller(GoogleAuthController::class)->group(function () {
    Route::get('auth/google', 'redirect')->name('google.redirect');
    Route::get('auth/google/callback', 'callback')->name('google.callback');
});


/*
|--------------------------------------------------------------------------
| CLIENT PORTAL ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // NOTIFICATIONS AJAX ROUTES
        Route::post('/notifications/{id}/read', [App\Http\Controllers\Client\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [App\Http\Controllers\Client\NotificationController::class, 'markAllRead'])->name('notifications.readAll');

        // Projects / Requests
        Route::resource('requests', RequestController::class);
        Route::get('requests/{request}/chat', [RequestController::class, 'chat'])->name('requests.chat');
        Route::post('requests/{request}/chat', [RequestController::class, 'chatStore'])->name('requests.chat.store');

        // Di dalam group prefix 'client' -> 'requests'
        Route::post('requests/{request}/complete', [RequestController::class, 'markCompleted'])->name('requests.complete');
        Route::post('requests/{request}/revision', [RequestController::class, 'requestRevision'])->name('requests.revision');

        // Workspaces
        Route::prefix('workspaces')->as('workspaces.')->group(function () {
            Route::get('/', [ClientWorkspaceController::class, 'index'])->name('index');
            Route::post('/', [ClientWorkspaceController::class, 'store'])->name('store');
            Route::get('/{workspace}', [ClientWorkspaceController::class, 'show'])->name('show')->whereUuid('workspace');
            Route::delete('/{workspace}', [ClientWorkspaceController::class, 'destroy'])->name('destroy')->whereUuid('workspace');
        });

        // Wallet & Finance
        Route::prefix('wallet')->as('wallet.')->group(function () {
            Route::get('/', [WalletController::class, 'index'])->name('index');
            Route::get('/topup', [WalletController::class, 'topup'])->name('topup');
            Route::post('/topup', [WalletController::class, 'processTopup'])->name('topup.process');
        });

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/simulate', [InvoiceController::class, 'simulatePayment'])->name('invoices.simulate');
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        Route::post('/invoices/{invoice}/simulate', [InvoiceController::class, 'simulatePayment'])->name('invoices.simulate');
        Route::put('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

        // Support & Settings
        Route::get('/support', [SupportController::class, 'index'])->name('support');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});


/*
|--------------------------------------------------------------------------
| ADMIN PORTAL ROUTES
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    // 1. Redirect Root Admin
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // 2. Admin Authentication (Guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // 3. Admin Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // 4. Protected Routes (Admin Only)
    Route::middleware(['auth'])->group(function () {

        // --- GROUP 1: AGENCY OVERVIEW ---
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [AdminAnalyticsController::class, 'exportPdf'])->name('analytics.export');

        // --- GROUP 2: PRODUCTION (PROJECTS) ---
        Route::resource('projects', AdminProjectController::class);
        // Custom Chat Routes untuk Admin
        Route::get('projects/{project}/chat', [AdminProjectController::class, 'chat'])->name('projects.chat');
        Route::post('projects/{project}/chat', [AdminProjectController::class, 'chatStore'])->name('projects.chat.store');
        Route::delete('projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('projects/{project}/submit', [AdminProjectController::class, 'submitWork'])->name('projects.submit');

        // --- GROUP 3: LIVE MONITORING (WORKSPACES) ---
        Route::get('/workspaces', [AdminWorkspaceController::class, 'index'])->name('workspaces.index');
        Route::get('/workspaces/{workspace}', [AdminWorkspaceController::class, 'show'])->name('workspaces.show');

        // --- GROUP 4: PEOPLE & SERVICES ---
        Route::resource('users', AdminUserController::class); // Client Database
        Route::resource('staff', App\Http\Controllers\Admin\StaffController::class); // <-- Tambahkan ini

        // SERVICES & PRICING
        Route::resource('services', App\Http\Controllers\Admin\ServiceController::class);

        // TOKEN PRICE ROUTES
        Route::post('token-prices', [App\Http\Controllers\Admin\ServiceController::class, 'storeTokenPrice'])->name('token-prices.store');
        Route::put('token-prices/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'updateTokenPrice'])->name('token-prices.update');
        Route::delete('token-prices/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'destroyTokenPrice'])->name('token-prices.destroy');

        // TIER ROUTES
        Route::put('tiers/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'updateTier'])->name('tiers.update');
        Route::delete('tiers/{id}', [App\Http\Controllers\Admin\ServiceController::class, 'destroyTier'])->name('tiers.destroy');

        // --- GROUP 5: FINANCE ---
        Route::resource('invoices', App\Http\Controllers\Admin\InvoiceController::class)->only(['index', 'show']);
        Route::put('invoices/{invoice}/paid', [App\Http\Controllers\Admin\InvoiceController::class, 'markAsPaid'])->name('invoices.paid');
        Route::put('invoices/{invoice}/cancel', [App\Http\Controllers\Admin\InvoiceController::class, 'cancel'])->name('invoices.cancel');
        
        // TOKEN MANAGER (NEW)
        Route::get('/tokens', [App\Http\Controllers\Admin\TokenController::class, 'index'])->name('tokens.index');
        Route::post('/tokens/adjust', [App\Http\Controllers\Admin\TokenController::class, 'storeAdjustment'])->name('tokens.adjust');

        Route::resource('notifications', App\Http\Controllers\Admin\NotificationController::class);
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');

        // STAFF PERFORMANCE & PAYROLL
        Route::get('/performance', [StaffPerformanceController::class, 'index'])->name('performance.index');
        Route::get('/performance/{id}', [StaffPerformanceController::class, 'show'])->name('performance.show');
        Route::post('/performance/{id}/approve', [StaffPerformanceController::class, 'approvePayout'])->name('performance.approve');
        Route::post('/performance/{id}/reject', [StaffPerformanceController::class, 'rejectPayout'])->name('performance.reject');
        Route::post('/performance/{id}/manual-payout', [StaffPerformanceController::class, 'storeManualPayout'])->name('performance.manual_payout');
        Route::post('/performance/rate/update', [StaffPerformanceController::class, 'updateRate'])->name('performance.update_rate');
    });
});


/*
|--------------------------------------------------------------------------
| STAFF PORTAL ROUTES
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {

    // 1. Redirect Root Staff
    Route::get('/', function () {
        return redirect()->route('staff.login');
    });

    // 2. Staff Authentication (Guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [StaffAuthController::class, 'index'])->name('login'); // <-- Rute Login
        Route::post('/login', [StaffAuthController::class, 'login'])->name('login.post');
    });

    // 3. Staff Logout
    Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout'); // <-- Rute Logout

    // 4. Protected Routes (Staff Only)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
        // Rute Task, Payout, dll. akan ditambahkan di sini.

        // NOTIFICATIONS AJAX ROUTES (STAFF)
        Route::post('/notifications/{id}/read', [StaffNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [StaffNotificationController::class, 'markAllRead'])->name('notifications.readAll');

        // TASKS ROUTES
        Route::get('/projects', [StaffProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{id}', [StaffProjectController::class, 'show'])->name('projects.show');
        Route::post('/projects/{id}/start', [StaffProjectController::class, 'startWork'])->name('projects.start');
        Route::post('/projects/{id}/submit', [StaffProjectController::class, 'submit'])->name('projects.submit');

        Route::get('/projects/{id}/chat', [StaffProjectController::class, 'chat'])->name('projects.chat');
        Route::post('/projects/{id}/chat', [StaffProjectController::class, 'chatStore'])->name('projects.chat.store');

        Route::get('/project-history', [StaffProjectController::class, 'history'])->name('projects.history');
        Route::get('/my-performance', [PerformanceController::class, 'index'])->name('performance.index');

        // Earnings & Payouts
        Route::get('/earnings', [StaffWalletController::class, 'index'])->name('finance.earnings');
        Route::post('/earnings/payout', [StaffWalletController::class, 'requestPayout'])->name('finance.payout');

        // TAMBAHKAN INI UNTUK DETAIL PAYOUT
        Route::get('/earnings/{id}', [StaffWalletController::class, 'show'])->name('finance.show');

        // SETTINGS
        Route::get('/settings', [StaffSettingController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [StaffSettingController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/bank', [StaffSettingController::class, 'updateBank'])->name('settings.bank');
        Route::put('/settings/password', [StaffSettingController::class, 'updatePassword'])->name('settings.password');
    });
});
