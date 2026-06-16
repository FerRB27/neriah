<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Products\Models\Product;
use App\Domains\Products\Models\ProductCategory;
use App\Domains\Products\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'variants.inventoryItem'])
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('variants', fn ($query) => $query->where('sku', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'product' => new Product(['active' => true]),
            'variant' => new ProductVariant(['units_per_variant' => 1, 'active' => true]),
            'inventoryItem' => new InventoryItem(['minimum_stock' => 0]),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated): void {
            $product = Product::query()->create($validated['product']);
            $variant = $product->variants()->create($validated['variant']);

            $variant->inventoryItem()->create($validated['inventory'] + [
                'item_type' => 'finished_good',
                'name' => $variant->name.' terminado',
                'unit' => $variant->unit_label,
                'active' => $variant->active,
            ]);
        });

        return redirect()->route('products.index')->with('status', 'Producto creado.');
    }

    public function edit(Product $product): View
    {
        $product->load('variants.inventoryItem');
        $variant = $product->variants->first() ?? new ProductVariant(['units_per_variant' => 1, 'active' => true]);

        return view('products.edit', [
            'product' => $product,
            'variant' => $variant,
            'inventoryItem' => $variant->inventoryItem ?? new InventoryItem(['minimum_stock' => 0]),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->load('variants.inventoryItem');
        $variant = $product->variants->first();
        $validated = $this->validatedData($request, $variant);

        DB::transaction(function () use ($product, $variant, $validated): void {
            $product->update($validated['product']);
            $variant = $variant
                ? tap($variant)->update($validated['variant'])
                : $product->variants()->create($validated['variant']);

            $variant->inventoryItem()->updateOrCreate(
                ['product_variant_id' => $variant->id],
                $validated['inventory'] + [
                    'input_id' => null,
                    'item_type' => 'finished_good',
                    'name' => $variant->name.' terminado',
                    'unit' => $variant->unit_label,
                    'active' => $variant->active,
                ],
            );
        });

        return redirect()->route('products.index')->with('status', 'Producto actualizado.');
    }

    private function validatedData(Request $request, ?ProductVariant $variant = null): array
    {
        $inventoryItemId = $variant?->inventoryItem?->id;

        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'standard_cost' => ['required', 'numeric', 'min:0', 'max:999999.9999'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'commission_amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'maker_payment_amount' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'active' => ['nullable', 'boolean'],
            'variant_sku' => ['required', 'string', 'max:255', Rule::unique('product_variants', 'sku')->ignore($variant?->id)],
            'variant_name' => ['required', 'string', 'max:255'],
            'unit_label' => ['required', 'string', 'max:30'],
            'units_per_variant' => ['required', 'integer', 'min:1', 'max:9999'],
            'weight_grams' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'inventory_sku' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'sku')->ignore($inventoryItemId)],
            'minimum_stock' => ['required', 'numeric', 'min:0', 'max:999999.9999'],
        ]);

        $active = (bool) ($validated['active'] ?? false);

        return [
            'product' => [
                'product_category_id' => $validated['product_category_id'],
                'name' => $validated['name'],
                'standard_cost' => $validated['standard_cost'],
                'base_price' => $validated['base_price'],
                'commission_amount' => $validated['commission_amount'],
                'maker_payment_amount' => $validated['maker_payment_amount'],
                'active' => $active,
            ],
            'variant' => [
                'sku' => $validated['variant_sku'],
                'name' => $validated['variant_name'],
                'unit_label' => $validated['unit_label'],
                'units_per_variant' => $validated['units_per_variant'],
                'weight_grams' => $validated['weight_grams'],
                'price' => $validated['price'],
                'active' => $active,
            ],
            'inventory' => [
                'sku' => $validated['inventory_sku'],
                'minimum_stock' => $validated['minimum_stock'],
            ],
        ];
    }
}
