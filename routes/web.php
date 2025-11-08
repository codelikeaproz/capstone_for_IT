<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VictimController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemLogsController;
use App\Http\Controllers\HeatmapController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UserController;
use App\Models\Incident;

// ====================
// PUBLIC ROUTES (No Authentication Required)
// ====================

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password.post');
Route::get('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('reset-password.post');

// Two-Factor Authentication routes
Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
Route::post('/2fa/resend', [TwoFactorController::class, 'resendCode'])->name('2fa.resend');

// Email Verification routes
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verifyEmail'])->name('email.verify');
Route::get('/email/verification/resend', [EmailVerificationController::class, 'showResendForm'])->name('email.verification.resend.form');
Route::post('/email/verification/resend', [EmailVerificationController::class, 'resendVerification'])->name('email.verification.resend');

// Public request status checking
Route::get('/status', [RequestController::class, 'checkStatus'])->name('requests.status-check');
Route::get('/status/{requestNumber}', [RequestController::class, 'checkStatus'])->name('requests.status');

// ====================
// PROTECTED ROUTES (Authentication Required)
// ====================

Route::middleware('auth')->group(function () {

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard routes (Role-based)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/staff-dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
    Route::get('/responder-dashboard', [DashboardController::class, 'responderDashboard'])->name('responder.dashboard');
    Route::get('/admin-dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Dashboard API routes
    Route::prefix('api/dashboard')->group(function () {
        Route::get('/statistics', [DashboardController::class, 'getStatistics'])->name('api.dashboard.statistics');
        Route::get('/heatmap', [DashboardController::class, 'getHeatmapData'])->name('api.dashboard.heatmap');
    });

    // ====================
    // INCIDENT MANAGEMENT
    // ====================
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    // Allow accessing soft-deleted incidents for proper error handling
    Route::delete('/incidents/{incident}', [IncidentController::class, 'destroy'])
        ->name('incidents.destroy')
        ->withTrashed();

    // Location API routes
    Route::get('/api/municipalities', [IncidentController::class, 'getMunicipalities'])->name('api.municipalities');
    Route::get('/api/barangays', [IncidentController::class, 'getBarangays'])->name('api.barangays');

    // ====================
    // VEHICLE MANAGEMENT
    // ====================
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Vehicle assignment and management
    Route::post('/vehicles/{vehicle}/assign', [VehicleController::class, 'assignToIncident'])->name('vehicles.assign');
    Route::post('/vehicles/{vehicle}/release', [VehicleController::class, 'releaseFromIncident'])->name('vehicles.release');
    Route::post('/vehicles/{vehicle}/maintenance', [VehicleController::class, 'updateMaintenance'])->name('vehicles.maintenance');

    // ====================
    // VICTIM MANAGEMENT
    // ====================
    Route::get('/victims', [VictimController::class, 'index'])->name('victims.index');
    Route::get('/victims/create', [VictimController::class, 'create'])->name('victims.create');
    Route::post('/victims', [VictimController::class, 'store'])->name('victims.store');
    Route::get('/victims/{victim}', [VictimController::class, 'show'])->name('victims.show');
    Route::get('/victims/{victim}/edit', [VictimController::class, 'edit'])->name('victims.edit');
    Route::put('/victims/{victim}', [VictimController::class, 'update'])->name('victims.update');
    Route::delete('/victims/{victim}', [VictimController::class, 'destroy'])->name('victims.destroy');

    // ====================
    // REQUEST MANAGEMENT
    // ====================x
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
    Route::get('/requests/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/requests/{request}', [RequestController::class, 'update'])->name('requests.update');
    Route::delete('/requests/{request}', [RequestController::class, 'destroy'])->name('requests.destroy');

    // Request management
    Route::post('/requests/{request}/assign', [RequestController::class, 'assign'])->name('requests.assign');
    Route::post('/requests/bulk-approve', [RequestController::class, 'bulkApprove'])->name('requests.bulk-approve');
    Route::post('/requests/bulk-reject', [RequestController::class, 'bulkReject'])->name('requests.bulk-reject');

    // ====================
    // ANALYTICS & REPORTING
    // ====================
    Route::get('/heat-maps', [HeatmapController::class, 'index'])->name('heatmaps');

    Route::get('/analytics', function () {
        return view('Analytics.Dashboard');
    })->name('analytics.dashboard');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');

    Route::get('/system-logs', [SystemLogsController::class, 'index'])->name('system.logs');

    // ====================
    // USER MANAGEMENT (Admin Only)
    // ====================
    Route::resource('users', UserController::class);

    // User management actions
    Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
    Route::post('/users/{user}/assign-municipality', [UserController::class, 'assignMunicipality'])->name('users.assign-municipality');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/unlock', [UserController::class, 'unlockAccount'])->name('users.unlock');
    Route::post('/users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');

    // ====================
    // MOBILE ROUTES (Responder Interface)
    // ====================
    Route::prefix('mobile')->group(function () {
        Route::get('/incident-report', function () {
            return view('MobileView.incident-report');
        })->name('mobile.incident-report');

        Route::get('/responder-dashboard', function () {
            return view('MobileView.responder-dashboard');
        })->name('mobile.responder-dashboard');
    });
});

// ====================
// ADMIN TESTING ROUTE (Remove in production)
// ====================
Route::get('/admin', function () {
    return view('User.Admin.AdminDashboard');
});
