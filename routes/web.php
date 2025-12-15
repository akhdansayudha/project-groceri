<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLLERS IMPORT ---
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

// Client Controllers
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\RequestController;
use App\Http\Controllers\Client\WorkspaceController as ClientWorkspaceController;
use App\Http\Controllers\Client\WalletController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\SettingController;
use App\Http\Controllers\Client\SupportController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\WorkspaceController as AdminWorkspaceController;

// Staff Controllers
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;


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
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


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

        // --- GROUP 2: PRODUCTION (PROJECTS) ---
        Route::resource('projects', AdminProjectController::class);
        // Custom Chat Routes untuk Admin
        Route::get('projects/{project}/chat', [AdminProjectController::class, 'chat'])->name('projects.chat');
        Route::post('projects/{project}/chat', [AdminProjectController::class, 'chatStore'])->name('projects.chat.store');

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

        // TOKEN MANAGER (NEW)
        Route::get('/tokens', [App\Http\Controllers\Admin\TokenController::class, 'index'])->name('tokens.index');
        Route::post('/tokens/adjust', [App\Http\Controllers\Admin\TokenController::class, 'storeAdjustment'])->name('tokens.adjust');

        Route::resource('notifications', App\Http\Controllers\Admin\NotificationController::class);
        Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');
    });
});


/*
|--------------------------------------------------------------------------
| STAFF PORTAL ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {
        Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
    });
});
