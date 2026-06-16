<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ModulePlaceholderController extends Controller
{
    public function __invoke(string $module): View
    {
        $modules = [
            'people' => 'Personas',
            'customers' => 'Clientes',
            'products' => 'Productos',
            'recipes' => 'Formulas',
            'inventory' => 'Inventario',
            'purchases' => 'Compras',
            'production' => 'Produccion',
            'sales' => 'Ventas',
            'commissions' => 'Comisiones',
            'payments' => 'Pagos',
            'assets' => 'Activos',
            'finance' => 'Finanzas',
            'social-fund' => 'Fondo Social',
            'reports' => 'Reportes',
            'security' => 'Seguridad',
        ];

        abort_unless(array_key_exists($module, $modules), 404);

        $permissions = [
            'people' => 'people.manage',
            'customers' => 'customers.manage',
            'products' => 'products.manage',
            'recipes' => 'recipes.manage',
            'inventory' => 'inventory.view',
            'purchases' => 'purchases.manage',
            'production' => 'production.manage',
            'sales' => 'sales.manage',
            'commissions' => 'commissions.manage',
            'payments' => 'payments.manage',
            'assets' => 'assets.manage',
            'finance' => 'finance.view',
            'social-fund' => 'social-fund.manage',
            'reports' => 'reports.view',
            'security' => 'security.manage',
        ];

        abort_unless(auth()->user()?->can($permissions[$module]), 403);

        return view('modules.placeholder', [
            'title' => $modules[$module],
        ]);
    }
}
