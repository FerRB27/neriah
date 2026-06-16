<?php

namespace Tests\Feature;

use App\Domains\Customers\Models\Customer;
use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use App\Domains\People\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_login_page_can_be_rendered(): void
    {
        $this->withoutVite();

        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $this->withoutVite();
        $this->seed();

        $response = $this->post(route('login.store'), [
            'email' => 'admin@neriah.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs(User::query()->where('email', 'admin@neriah.test')->first());

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_admin_can_manage_people(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $this->get(route('people.index'))->assertOk();

        $response = $this->post(route('people.store'), [
            'name' => 'Nueva Elaboradora',
            'phone' => '7000-9999',
            'email' => 'nueva@neriah.test',
            'address' => 'San Salvador',
            'active' => '1',
            'roles' => ['maker'],
        ]);

        $response->assertRedirect(route('people.index'));
        $this->assertDatabaseHas('people', ['email' => 'nueva@neriah.test']);
        $this->assertTrue(Person::query()->where('email', 'nueva@neriah.test')->first()->roleAssignments()->where('role', 'maker')->exists());
    }

    public function test_admin_can_manage_customers(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $this->get(route('customers.index'))->assertOk();

        $response = $this->post(route('customers.store'), [
            'name' => 'Cliente Nuevo',
            'phone' => '7000-8888',
            'city' => 'Santa Tecla',
            'address' => 'Direccion de prueba',
            'notes' => 'Cliente creado desde prueba.',
        ]);

        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['phone' => '7000-8888']);
        $this->assertInstanceOf(Customer::class, Customer::query()->where('phone', '7000-8888')->first());
    }

    public function test_admin_can_register_founder_capital_movement(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $this->get(route('finance.founder-capital.index'))->assertOk();

        $response = $this->post(route('finance.founder-capital.store'), [
            'type' => FounderCapitalMovementType::Contribution->value,
            'amount' => 75,
            'movement_date' => now()->toDateString(),
            'concept' => 'Compra adicional de insumos',
            'notes' => 'Aporte temporal del fundador.',
        ]);

        $response->assertRedirect(route('finance.founder-capital.index'));
        $this->assertTrue(FounderCapitalMovement::query()->where('concept', 'Compra adicional de insumos')->exists());
    }
}
