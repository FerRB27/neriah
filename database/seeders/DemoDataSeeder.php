<?php

namespace Database\Seeders;

use App\Domains\Customers\Models\Customer;
use App\Domains\People\Enums\PersonRole;
use App\Domains\People\Models\Person;
use App\Domains\Purchases\Models\Supplier;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $maker = Person::query()->updateOrCreate(
            ['email' => 'elaborador@neriah.test'],
            [
                'name' => 'Elaborador Demo',
                'phone' => '7000-0001',
                'address' => 'San Salvador',
                'active' => true,
            ],
        );

        $maker->roleAssignments()->updateOrCreate(['role' => PersonRole::Maker]);

        $seller = Person::query()->updateOrCreate(
            ['email' => 'vendedor@neriah.test'],
            [
                'name' => 'Vendedor Demo',
                'phone' => '7000-0002',
                'address' => 'San Salvador',
                'active' => true,
            ],
        );

        $seller->roleAssignments()->updateOrCreate(['role' => PersonRole::Seller]);

        Person::query()->updateOrCreate(
            ['email' => 'distribuidor@neriah.test'],
            [
                'name' => 'Distribuidor Demo',
                'phone' => '7000-0003',
                'address' => 'Santa Tecla',
                'active' => true,
            ],
        )->roleAssignments()->updateOrCreate(['role' => PersonRole::Distributor]);

        Customer::query()->updateOrCreate(
            ['phone' => '7000-0100'],
            [
                'name' => 'Cliente Demo',
                'city' => 'San Salvador',
                'address' => 'Direccion demo',
                'notes' => 'Cliente inicial para pruebas de ventas.',
            ],
        );

        Supplier::query()->updateOrCreate(
            ['name' => 'Proveedor Demo'],
            [
                'phone' => '7000-0200',
                'email' => 'proveedor@neriah.test',
                'address' => 'San Salvador',
                'active' => true,
            ],
        );
    }
}
