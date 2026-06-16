<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FounderCapitalController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ModulePlaceholderController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SupplierController;
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

    Route::resource('/insumos', InputController::class)
        ->except(['show', 'destroy'])
        ->parameters(['insumos' => 'input'])
        ->middleware('can:products.manage')
        ->names('inputs');

    Route::resource('/productos', ProductController::class)
        ->except(['show', 'destroy'])
        ->parameters(['productos' => 'product'])
        ->middleware('can:products.manage')
        ->names('products');

    Route::resource('/formulas', RecipeController::class)
        ->except(['show', 'destroy'])
        ->parameters(['formulas' => 'recipe'])
        ->middleware('can:recipes.manage')
        ->names('recipes');

    Route::resource('/proveedores', SupplierController::class)
        ->except(['show', 'destroy'])
        ->parameters(['proveedores' => 'supplier'])
        ->middleware('can:purchases.manage')
        ->names('suppliers');

    Route::resource('/compras', PurchaseController::class)
        ->except(['destroy'])
        ->parameters(['compras' => 'purchase'])
        ->middleware('can:purchases.manage')
        ->names('purchases');

    Route::post('/compras/{purchase}/confirmar', [PurchaseController::class, 'confirm'])
        ->middleware('can:purchases.manage')
        ->name('purchases.confirm');

    Route::resource('/produccion', ProductionController::class)
        ->except(['destroy'])
        ->parameters(['produccion' => 'productionOrder'])
        ->middleware('can:production.manage')
        ->names('production');

    Route::post('/produccion/{productionOrder}/confirmar', [ProductionController::class, 'confirm'])
        ->middleware('can:production.manage')
        ->name('production.confirm');

    Route::get('/inventario', [InventoryController::class, 'index'])
        ->middleware('can:inventory.view')
        ->name('inventory.index');

    Route::get('/inventario/{inventoryItem}', [InventoryController::class, 'show'])
        ->middleware('can:inventory.view')
        ->name('inventory.show');

    Route::get('/finanzas/capital-fundador', [FounderCapitalController::class, 'index'])
        ->middleware('can:finance.manage')
        ->name('finance.founder-capital.index');

    Route::post('/finanzas/capital-fundador', [FounderCapitalController::class, 'store'])
        ->middleware('can:finance.manage')
        ->name('finance.founder-capital.store');

    Route::get('/modulos/{module}', ModulePlaceholderController::class)
        ->whereIn('module', [
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
