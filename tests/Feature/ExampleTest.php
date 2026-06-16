<?php

namespace Tests\Feature;

use App\Domains\Customers\Models\Customer;
use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\People\Models\Person;
use App\Domains\Products\Models\Input;
use App\Domains\Products\Models\InputCategory;
use App\Domains\Products\Models\Product;
use App\Domains\Products\Models\ProductCategory;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Purchases\Models\Purchase;
use App\Domains\Purchases\Models\Supplier;
use App\Domains\Recipes\Models\Recipe;
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

    public function test_admin_can_manage_inputs(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $category = InputCategory::query()->first();

        $this->get(route('inputs.index'))->assertOk();

        $response = $this->post(route('inputs.store'), [
            'input_category_id' => $category->id,
            'sku' => 'INS-TEST-001',
            'name' => 'Insumo Test',
            'unit' => 'kg',
            'minimum_stock' => 1.5,
            'active' => '1',
        ]);

        $response->assertRedirect(route('inputs.index'));
        $input = Input::query()->where('name', 'Insumo Test')->first();

        $this->assertNotNull($input);
        $this->assertDatabaseHas('inventory_items', ['input_id' => $input->id, 'sku' => 'INS-TEST-001']);
    }

    public function test_admin_can_manage_products(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $category = ProductCategory::query()->first();

        $this->get(route('products.index'))->assertOk();

        $response = $this->post(route('products.store'), [
            'product_category_id' => $category->id,
            'name' => 'Producto Test',
            'standard_cost' => 1.15,
            'base_price' => 2.50,
            'commission_amount' => 0.25,
            'maker_payment_amount' => 0.35,
            'active' => '1',
            'variant_sku' => 'PROD-TEST-001',
            'variant_name' => 'Producto Test 85g',
            'unit_label' => 'unidad',
            'units_per_variant' => 1,
            'weight_grams' => 85,
            'price' => 2.50,
            'inventory_sku' => 'PT-PROD-TEST-001',
            'minimum_stock' => 10,
        ]);

        $response->assertRedirect(route('products.index'));
        $product = Product::query()->where('name', 'Producto Test')->first();
        $variant = ProductVariant::query()->where('sku', 'PROD-TEST-001')->first();

        $this->assertNotNull($product);
        $this->assertNotNull($variant);
        $this->assertDatabaseHas('inventory_items', ['product_variant_id' => $variant->id, 'sku' => 'PT-PROD-TEST-001']);
    }

    public function test_admin_can_manage_recipes(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $variant = ProductVariant::query()->first();
        $inputItem = InventoryItem::query()->where('item_type', 'input')->first();

        $this->get(route('recipes.index'))->assertOk();

        $response = $this->post(route('recipes.store'), [
            'product_variant_id' => $variant->id,
            'name' => 'Formula Test',
            'expected_yield' => 12,
            'yield_unit' => 'unidad',
            'active' => '1',
            'ingredients' => [
                [
                    'inventory_item_id' => $inputItem->id,
                    'quantity' => 0.5,
                    'unit' => $inputItem->unit,
                ],
            ],
        ]);

        $response->assertRedirect(route('recipes.index'));
        $recipe = Recipe::query()->where('name', 'Formula Test')->first();

        $this->assertNotNull($recipe);
        $this->assertDatabaseHas('recipe_ingredients', ['recipe_id' => $recipe->id, 'inventory_item_id' => $inputItem->id]);
    }

    public function test_admin_can_manage_suppliers(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $this->get(route('suppliers.index'))->assertOk();

        $response = $this->post(route('suppliers.store'), [
            'name' => 'Proveedor Nuevo',
            'phone' => '7000-7777',
            'email' => 'nuevo.proveedor@neriah.test',
            'address' => 'San Salvador',
            'active' => '1',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertInstanceOf(Supplier::class, Supplier::query()->where('email', 'nuevo.proveedor@neriah.test')->first());
    }

    public function test_admin_can_create_and_confirm_purchase_generating_kardex(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $supplier = Supplier::query()->first();
        $item = InventoryItem::query()->where('item_type', 'input')->first();

        $this->get(route('purchases.index'))->assertOk();

        $response = $this->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'purchased_at' => now()->toDateString(),
            'notes' => 'Compra de prueba',
            'lines' => [
                [
                    'inventory_item_id' => $item->id,
                    'quantity' => 10,
                    'unit_cost' => 2.5,
                ],
            ],
        ]);

        $purchase = Purchase::query()->latest('id')->first();

        $response->assertRedirect(route('purchases.show', $purchase));
        $this->assertSame('draft', $purchase->status);
        $this->assertDatabaseHas('purchase_lines', [
            'purchase_id' => $purchase->id,
            'inventory_item_id' => $item->id,
        ]);

        $this->post(route('purchases.confirm', $purchase))
            ->assertRedirect(route('purchases.show', $purchase));

        $purchase->refresh();
        $item->refresh();

        $this->assertSame('confirmed', $purchase->status);
        $this->assertEquals(10.0, (float) $item->current_stock);
        $this->assertEquals(2.5, (float) $item->average_cost);
        $this->assertDatabaseHas('inventory_movements', [
            'purchase_id' => $purchase->id,
            'inventory_item_id' => $item->id,
            'type' => 'purchase',
            'direction' => 'in',
        ]);

        $this->post(route('purchases.confirm', $purchase))
            ->assertSessionHasErrors('purchase');
    }
}
