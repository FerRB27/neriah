<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModulePlaceholderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('can:dashboard.view')
        ->name('dashboard');

    Route::get('/modulos/{module}', ModulePlaceholderController::class)
        ->whereIn('module', [
            'people',
            'customers',
            'products',
            'recipes',
            'inventory',
            'purchases',
            'production',
            'sales',
            'commissions',
            'payments',
            'assets',
            'finance',
            'social-fund',
            'reports',
            'security',
        ])
        ->name('modules.show');
});
