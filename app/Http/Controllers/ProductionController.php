<?php

namespace App\Http\Controllers;

use App\Domains\People\Models\Person;
use App\Domains\Production\Actions\ConfirmProductionOrderAction;
use App\Domains\Production\Enums\ProductionStatus;
use App\Domains\Production\Models\ProductionOrder;
use App\Domains\Recipes\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(Request $request): View
    {
        $orders = ProductionOrder::query()
            ->with(['maker', 'recipe', 'productVariant.product'])
            ->when($request->string('status')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->latest('produced_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('production.index', [
            'orders' => $orders,
            'status' => $request->string('status')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('production.create', $this->formData(new ProductionOrder([
            'produced_at' => now()->toDateString(),
            'status' => ProductionStatus::Draft,
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $recipe = Recipe::query()->findOrFail($validated['recipe_id']);

        $order = ProductionOrder::query()->create($validated + [
            'product_variant_id' => $recipe->product_variant_id,
            'status' => ProductionStatus::Draft,
            'produced_quantity' => 0,
            'real_cost_total' => 0,
        ]);

        return redirect()->route('production.show', $order)->with('status', 'Orden de produccion creada como borrador.');
    }

    public function show(ProductionOrder $productionOrder): View
    {
        $productionOrder->load([
            'maker',
            'recipe.ingredients.inventoryItem',
            'productVariant.inventoryItem',
            'productVariant.product',
            'consumptions.inventoryItem',
            'inventoryMovements.inventoryItem',
        ]);

        return view('production.show', [
            'order' => $productionOrder,
            'requirements' => $this->requirements($productionOrder, (float) $productionOrder->planned_quantity),
        ]);
    }

    public function edit(ProductionOrder $productionOrder): View
    {
        abort_if($productionOrder->status === ProductionStatus::Confirmed, 403);

        return view('production.edit', $this->formData($productionOrder));
    }

    public function update(Request $request, ProductionOrder $productionOrder): RedirectResponse
    {
        abort_if($productionOrder->status === ProductionStatus::Confirmed, 403);

        $validated = $this->validatedData($request);
        $recipe = Recipe::query()->findOrFail($validated['recipe_id']);

        $productionOrder->update($validated + [
            'product_variant_id' => $recipe->product_variant_id,
        ]);

        return redirect()->route('production.show', $productionOrder)->with('status', 'Orden de produccion actualizada.');
    }

    public function confirm(Request $request, ProductionOrder $productionOrder, ConfirmProductionOrderAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'produced_quantity' => ['required', 'numeric', 'min:0.0001', 'max:999999.9999'],
        ]);

        $action->execute($productionOrder, (float) $validated['produced_quantity']);

        return redirect()->route('production.show', $productionOrder)->with('status', 'Produccion confirmada. Kardex y costo real actualizados.');
    }

    private function formData(ProductionOrder $order): array
    {
        return [
            'order' => $order,
            'makers' => Person::query()
                ->where('active', true)
                ->whereHas('roleAssignments', fn ($query) => $query->where('role', 'maker'))
                ->orderBy('name')
                ->get(),
            'recipes' => Recipe::query()
                ->with(['productVariant.product'])
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'maker_id' => ['required', 'exists:people,id'],
            'recipe_id' => ['required', 'exists:recipes,id'],
            'produced_at' => ['required', 'date'],
            'planned_quantity' => ['required', 'numeric', 'min:0.0001', 'max:999999.9999'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function requirements(ProductionOrder $order, float $quantity): array
    {
        $factor = $quantity / (float) $order->recipe->expected_yield;

        return $order->recipe->ingredients
            ->map(function ($ingredient) use ($factor): array {
                $item = $ingredient->inventoryItem;
                $required = round((float) $ingredient->quantity * $factor, 4);
                $available = (float) $item->current_stock;
                $unitCost = (float) $item->average_cost;

                return [
                    'item' => $item,
                    'required' => $required,
                    'available' => $available,
                    'unit_cost' => $unitCost,
                    'total_cost' => round($required * $unitCost, 2),
                    'missing' => max(0, $required - $available),
                ];
            })
            ->all();
    }
}
