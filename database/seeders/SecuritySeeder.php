<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@neriah.test'],
            [
                'name' => 'Administrador Neriah',
                'password' => Hash::make('password'),
            ],
        );
    }
}
