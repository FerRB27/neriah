<?php

namespace Tests\Feature;

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
}
