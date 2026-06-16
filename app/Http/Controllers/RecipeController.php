<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Products\Models\ProductVariant;
use App\Domains\Recipes\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(Request $request): View
    {
        $recipes = Recipe::query()
            ->with(['productVariant.product', 'ingredients'])
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('recipes.index', [
            'recipes' => $recipes,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('recipes.create', $this->formData(new Recipe(['active' => true])));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated): void {
            $recipe = Recipe::query()->create($validated['recipe']);
            $this->syncIngredients($recipe, $validated['ingredients']);
        });

        return redirect()->route('recipes.index')->with('status', 'Formula creada.');
    }

    public function edit(Recipe $recipe): View
    {
        return view('recipes.edit', $this->formData($recipe->load('ingredients')));
    }

    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($recipe, $validated): void {
            $recipe->update($validated['recipe']);
            $this->syncIngredients($recipe, $validated['ingredients']);
        });

        return redirect()->route('recipes.index')->with('status', 'Formula actualizada.');
    }

    private function formData(Recipe $recipe): array
    {
        return [
            'recipe' => $recipe,
            'variants' => ProductVariant::query()->with('product')->orderBy('name')->get(),
            'inventoryItems' => InventoryItem::query()->where('item_type', 'input')->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'name' => ['required', 'string', 'max:255'],
            'expected_yield' => ['required', 'numeric', 'min:0.0001', 'max:999999.9999'],
            'yield_unit' => ['required', 'string', 'max:20'],
            'active' => ['nullable', 'boolean'],
            'ingredients' => ['array'],
            'ingredients.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'ingredients.*.quantity' => ['nullable', 'numeric', 'min:0.0001', 'max:999999.9999'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:20'],
        ]);

        $ingredients = collect($validated['ingredients'] ?? [])
            ->filter(fn (array $ingredient): bool => filled($ingredient['inventory_item_id'] ?? null))
            ->map(fn (array $ingredient): array => [
                'inventory_item_id' => (int) $ingredient['inventory_item_id'],
                'quantity' => (float) ($ingredient['quantity'] ?? 0),
                'unit' => $ingredient['unit'] ?? '',
            ])
            ->filter(fn (array $ingredient): bool => $ingredient['quantity'] > 0 && $ingredient['unit'] !== '')
            ->unique('inventory_item_id')
            ->values()
            ->all();

        return [
            'recipe' => [
                'product_variant_id' => $validated['product_variant_id'],
                'name' => $validated['name'],
                'expected_yield' => $validated['expected_yield'],
                'yield_unit' => $validated['yield_unit'],
                'active' => (bool) ($validated['active'] ?? false),
            ],
            'ingredients' => $ingredients,
        ];
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->delete();

        foreach ($ingredients as $ingredient) {
            $recipe->ingredients()->create($ingredient);
        }
    }
}
