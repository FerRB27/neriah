<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'security.manage',
            'people.manage',
            'customers.manage',
            'products.manage',
            'recipes.manage',
            'inventory.view',
            'inventory.adjust',
            'purchases.manage',
            'production.manage',
            'sales.manage',
            'commissions.manage',
            'payments.manage',
            'assets.manage',
            'finance.view',
            'finance.manage',
            'social-fund.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $administrator = Role::query()->firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $maker = Role::query()->firstOrCreate(['name' => 'Elaborador', 'guard_name' => 'web']);
        $seller = Role::query()->firstOrCreate(['name' => 'Vendedor', 'guard_name' => 'web']);
        $distributor = Role::query()->firstOrCreate(['name' => 'Distribuidor', 'guard_name' => 'web']);
        $viewer = Role::query()->firstOrCreate(['name' => 'Consulta', 'guard_name' => 'web']);

        $administrator->syncPermissions($permissions);
        $maker->syncPermissions(['dashboard.view', 'recipes.manage', 'inventory.view', 'production.manage']);
        $seller->syncPermissions(['dashboard.view', 'customers.manage', 'inventory.view', 'sales.manage']);
        $distributor->syncPermissions(['dashboard.view', 'customers.manage', 'inventory.view', 'sales.manage']);
        $viewer->syncPermissions(['dashboard.view', 'inventory.view', 'finance.view', 'reports.view']);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@neriah.test'],
            [
                'name' => 'Administrador Neriah',
                'password' => Hash::make('password'),
            ],
        );

        $admin->syncRoles([$administrator]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
