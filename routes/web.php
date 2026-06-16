<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FounderCapitalController;
use App\Http\Controllers\ModulePlaceholderController;
use App\Http\Controllers\PeopleController;
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

    Route::resource('/personas', PeopleController::class)
        ->except(['show', 'destroy'])
        ->parameters(['personas' => 'person'])
        ->middleware('can:people.manage')
        ->names('people');

    Route::resource('/clientes', CustomerController::class)
        ->except(['show', 'destroy'])
        ->parameters(['clientes' => 'customer'])
        ->middleware('can:customers.manage')
        ->names('customers');

    Route::get('/finanzas/capital-fundador', [FounderCapitalController::class, 'index'])
        ->middleware('can:finance.manage')
        ->name('finance.founder-capital.index');

    Route::post('/finanzas/capital-fundador', [FounderCapitalController::class, 'store'])
        ->middleware('can:finance.manage')
        ->name('finance.founder-capital.store');

    Route::get('/modulos/{module}', ModulePlaceholderController::class)
        ->whereIn('module', [
            'products',
            'recipes',
            'inventory',
            'purchases',
            'production',
            'sales',
            'commissions',
            'payments',
            'assets',
            'social-fund',
            'reports',
            'security',
        ])
        ->name('modules.show');
});
