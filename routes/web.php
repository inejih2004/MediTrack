<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

// Page d'accueil
/*Route::get('/', function () {
    return view('welcome');
});*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/stock/dashboard', function () {
        return view('dashboards.stock');
    })->name('stock.dashboard');
     Route::get('/accountant/dashboard', function () {
        return view('dashboards.accountant');
    })->name('accountant.dashboard');


    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');

    Route::get('/service/dashboard', function () {
        return view('dashboards.service');
    })->name('service.dashboard');
});
// Routes protégées par middleware 'auth'
