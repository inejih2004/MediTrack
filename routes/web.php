<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ExpirationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/dispose/{expirationId}', [DashboardController::class, 'disposeMaterial'])->name('dashboard.dispose');
    Route::post('/dashboard/return/{expirationId}', [DashboardController::class, 'returnMaterial'])->name('dashboard.return');
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/expirations', [ExpirationController::class, 'index'])->name('expirations.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});