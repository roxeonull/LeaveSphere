<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DelegationController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/spikes-data', [DashboardController::class, 'spikesData'])->name('dashboard.spikes-data');
    Route::get('/dashboard/department-load-data', [DashboardController::class, 'departmentLoadData'])->name('dashboard.department-load-data');

    // Leave Approvals
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/data', [ApprovalController::class, 'data'])->name('data');
        Route::post('/{id}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::get('/{id}', [ApprovalController::class, 'show'])->name('show');
    });

    // Smart Recommendations
    Route::prefix('recommendations')->name('recommendations.')->group(function () {
        Route::get('/', [RecommendationController::class, 'index'])->name('index');
        Route::get('/pattern-data', [RecommendationController::class, 'patternData'])->name('pattern-data');
    });

    // Team Calendar
    Route::prefix('calendar')->name('calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::get('/events', [CalendarController::class, 'events'])->name('events');
    });

    // Delegation Management
    Route::prefix('delegation')->name('delegation.')->group(function () {
        Route::get('/', [DelegationController::class, 'index'])->name('index');
        Route::post('/', [DelegationController::class, 'store'])->name('store');
        Route::put('/{id}', [DelegationController::class, 'update'])->name('update');
        Route::delete('/{id}', [DelegationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/revoke', [DelegationController::class, 'revoke'])->name('revoke');
    });

    // SLA Monitoring
    Route::prefix('sla')->name('sla.')->group(function () {
        Route::get('/', [SlaController::class, 'index'])->name('index');
    });

    // Analytics & Insights
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'index'])->name('index');
        Route::get('/monthly-trends', [AnalyticsController::class, 'monthlyTrends'])->name('monthly-trends');
        Route::get('/department-comparison', [AnalyticsController::class, 'departmentComparison'])->name('department-comparison');
        Route::get('/leave-type-distribution', [AnalyticsController::class, 'leaveTypeDistribution'])->name('leave-type-distribution');
        Route::get('/export/{format}', [AnalyticsController::class, 'export'])->name('export');
    });

    // Workflows
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/', [WorkflowController::class, 'index'])->name('index');
        Route::post('/', [WorkflowController::class, 'store'])->name('store');
        Route::put('/{id}', [WorkflowController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkflowController::class, 'destroy'])->name('destroy');
    });

    // Users (Super Admin only)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('/export/{format}', [UserController::class, 'export'])->name('export');
        Route::post('/import', [UserController::class, 'import'])->name('import');
    });

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Notifications (header dropdown)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });
});
