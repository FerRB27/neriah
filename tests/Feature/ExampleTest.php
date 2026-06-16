<?php

namespace Tests\Feature;

use App\Domains\Customers\Models\Customer;
use App\Domains\Finance\Enums\FounderCapitalMovementType;
use App\Domains\Finance\Models\FounderCapitalMovement;
use App\Domains\Inventory\DTOs\InventoryMovementData;
use App\Domains\Inventory\Enums\InventoryMovementDirection;
use App\Domains\Inventory\Enums\InventoryMovementType;
use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Inventory\Services\KardexService;
use App\Domains\Payments\Models\Payment;
use App\Domains\People\Models\Person;
use App\Domains\Products\Models\Input;
use App\Domains\Products\Models\InputCategory;
use App\Domains\Products\Models\Product;
use App\Domains\Products\Models\ProductCategory;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Production\Models\ProductionOrder;
use App\Domains\Purchases\Models\Purchase;
use App\Domains\Purchases\Models\Supplier;
use App\Domains\Recipes\Models\Recipe;
use App\Domains\Sales\Models\Sale;
use App\Domains\Sales\Models\SalesChannel;
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

    public function test_admin_can_view_inventory_and_kardex(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $item = InventoryItem::query()->where('item_type', 'input')->first();

        $this->get(route('inventory.index'))->assertOk();
        $this->get(route('inventory.index', ['critical' => 1]))->assertOk();
        $this->get(route('inventory.show', $item))->assertOk();

        $supplier = Supplier::query()->first();
        $purchase = Purchase::query()->create([
            'supplier_id' => $supplier->id,
            'purchased_at' => now()->toDateString(),
            'status' => 'draft',
            'total_amount' => 15,
        ]);
        $purchase->lines()->create([
            'inventory_item_id' => $item->id,
            'quantity' => 3,
            'unit_cost' => 5,
            'total_cost' => 15,
        ]);

        $this->post(route('purchases.confirm', $purchase))->assertRedirect(route('purchases.show', $purchase));

        $this->get(route('inventory.show', $item))
            ->assertOk()
            ->assertSee('Compra #'.$purchase->id);
    }

    public function test_admin_can_create_and_confirm_production_order(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $recipe = Recipe::query()->with(['ingredients.inventoryItem', 'productVariant.inventoryItem'])->first();
        $maker = Person::query()->whereHas('roleAssignments', fn ($query) => $query->where('role', 'maker'))->first();
        $supplier = Supplier::query()->first();

        $purchase = Purchase::query()->create([
            'supplier_id' => $supplier->id,
            'purchased_at' => now()->toDateString(),
            'status' => 'draft',
            'total_amount' => 0,
        ]);

        foreach ($recipe->ingredients as $ingredient) {
            $quantity = max(100, (float) $ingredient->quantity * 20);
            $purchase->lines()->create([
                'inventory_item_id' => $ingredient->inventory_item_id,
                'quantity' => $quantity,
                'unit_cost' => 1,
                'total_cost' => $quantity,
            ]);
        }

        $purchase->forceFill(['total_amount' => $purchase->lines()->sum('total_cost')])->save();
        $this->post(route('purchases.confirm', $purchase))->assertRedirect(route('purchases.show', $purchase));

        $outputItem = $recipe->productVariant->inventoryItem;
        $this->assertEquals(0.0, (float) $outputItem->current_stock);

        $this->get(route('production.index'))->assertOk();

        $response = $this->post(route('production.store'), [
            'maker_id' => $maker->id,
            'recipe_id' => $recipe->id,
            'produced_at' => now()->toDateString(),
            'planned_quantity' => 10,
            'notes' => 'Produccion de prueba',
        ]);

        $order = ProductionOrder::query()->latest('id')->first();

        $response->assertRedirect(route('production.show', $order));
        $this->assertSame('draft', $order->status->value);

        $this->post(route('production.confirm', $order), [
            'produced_quantity' => 10,
        ])->assertRedirect(route('production.show', $order));

        $order->refresh();
        $outputItem->refresh();

        $this->assertSame('confirmed', $order->status->value);
        $this->assertEquals(10.0, (float) $order->produced_quantity);
        $this->assertEquals(10.0, (float) $outputItem->current_stock);
        $this->assertGreaterThan(0, (float) $order->real_cost_total);
        $this->assertDatabaseCount('production_consumptions', $recipe->ingredients->count());
        $this->assertDatabaseHas('inventory_movements', [
            'production_order_id' => $order->id,
            'inventory_item_id' => $outputItem->id,
            'type' => 'production_output',
            'direction' => 'in',
        ]);

        foreach ($recipe->ingredients as $ingredient) {
            $this->assertDatabaseHas('inventory_movements', [
                'production_order_id' => $order->id,
                'inventory_item_id' => $ingredient->inventory_item_id,
                'type' => 'production_consumption',
                'direction' => 'out',
            ]);
        }

        $this->post(route('production.confirm', $order), [
            'produced_quantity' => 10,
        ])->assertSessionHasErrors('production');
    }

    public function test_admin_can_create_and_confirm_sale_generating_kardex_profit_commissions_and_payments(): void
    {
        $this->withoutVite();
        $this->seed();
        $this->actingAs(User::query()->where('email', 'admin@neriah.test')->first());

        $variant = ProductVariant::query()->with(['product', 'inventoryItem'])->where('sku', 'JAB-MELON-85G')->first();
        $inventoryItem = $variant->inventoryItem;

        app(KardexService::class)->record(new InventoryMovementData(
            inventoryItemId: $inventoryItem->id,
            type: InventoryMovementType::Adjustment,
            direction: InventoryMovementDirection::In,
            quantity: 10,
            unitCost: 1.02,
            movementDate: now(),
            notes: 'Stock inicial de prueba para venta',
        ));

        $customer = Customer::query()->first();
        $seller = Person::query()->whereHas('roleAssignments', fn ($query) => $query->where('role', 'seller'))->first();
        $maker = Person::query()->whereHas('roleAssignments', fn ($query) => $query->where('role', 'maker'))->first();
        $channel = SalesChannel::query()->first();

        $this->get(route('sales.index'))->assertOk();

        $response = $this->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'seller_id' => $seller->id,
            'maker_id' => $maker->id,
            'sales_channel_id' => $channel->id,
            'sold_at' => now()->toDateString(),
            'discount_total' => 0,
            'notes' => 'Venta de prueba',
            'lines' => [
                [
                    'product_variant_id' => $variant->id,
                    'quantity' => 2,
                    'unit_price' => 2.50,
                ],
            ],
        ]);

        $sale = Sale::query()->latest('id')->first();

        $response->assertRedirect(route('sales.show', $sale));
        $this->assertSame('draft', $sale->status->value);

        $this->post(route('sales.confirm', $sale))->assertRedirect(route('sales.show', $sale));

        $sale->refresh();
        $inventoryItem->refresh();

        $this->assertSame('confirmed', $sale->status->value);
        $this->assertEquals(8.0, (float) $inventoryItem->current_stock);
        $this->assertEquals(5.0, (float) $sale->total_amount);
        $this->assertEquals(2.52, (float) $sale->visible_profit);
        $this->assertEquals(0.44, (float) $sale->hidden_profit);
        $this->assertDatabaseHas('inventory_movements', [
            'sale_id' => $sale->id,
            'inventory_item_id' => $inventoryItem->id,
            'type' => 'sale',
            'direction' => 'out',
        ]);
        $this->assertDatabaseHas('commission_entries', [
            'sale_id' => $sale->id,
            'person_id' => $seller->id,
            'type' => 'seller',
            'amount' => 0.50,
        ]);
        $this->assertDatabaseHas('commission_entries', [
            'sale_id' => $sale->id,
            'person_id' => $maker->id,
            'type' => 'maker_payment',
            'amount' => 0.70,
        ]);
        $this->assertTrue(Payment::query()->where('person_id', $seller->id)->where('amount', 0.50)->exists());
        $this->assertTrue(Payment::query()->where('person_id', $maker->id)->where('amount', 0.70)->exists());
        $this->assertDatabaseHas('financial_profit_allocations', [
            'sale_id' => $sale->id,
            'profit_amount' => 2.96,
            'social_fund_amount' => 0.30,
        ]);

        $this->post(route('sales.confirm', $sale))->assertSessionHasErrors('sale');
    }
}
