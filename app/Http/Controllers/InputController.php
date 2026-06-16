<?php

namespace App\Http\Controllers;

use App\Domains\Inventory\Models\InventoryItem;
use App\Domains\Products\Models\Input;
use App\Domains\Products\Models\InputCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InputController extends Controller
{
    public function index(Request $request): View
    {
        $inputs = Input::query()
            ->with(['category', 'inventoryItem'])
            ->when($request->string('q')->toString(), function ($query, string $search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('inputs.index', [
            'inputs' => $inputs,
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function create(): View
    {
        return view('inputs.create', [
            'input' => new Input(['active' => true]),
            'inventoryItem' => new InventoryItem(),
            'categories' => InputCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated): void {
            $input = Input::query()->create($validated['input']);

            $input->inventoryItem()->create($validated['inventory'] + [
                'item_type' => 'input',
                'name' => $input->name,
                'unit' => $input->unit,
                'minimum_stock' => $input->minimum_stock,
                'active' => $input->active,
            ]);
        });

        return redirect()->route('inputs.index')->with('status', 'Insumo creado.');
    }

    public function edit(Input $input): View
    {
        return view('inputs.edit', [
            'input' => $input->load('inventoryItem'),
            'inventoryItem' => $input->inventoryItem,
            'categories' => InputCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Input $input): RedirectResponse
    {
        $validated = $this->validatedData($request, $input);

        DB::transaction(function () use ($input, $validated): void {
            $input->update($validated['input']);

            $input->inventoryItem()->updateOrCreate(
                ['input_id' => $input->id],
                $validated['inventory'] + [
                    'product_variant_id' => null,
                    'item_type' => 'input',
                    'name' => $input->name,
                    'unit' => $input->unit,
                    'minimum_stock' => $input->minimum_stock,
                    'active' => $input->active,
                ],
            );
        });

        return redirect()->route('inputs.index')->with('status', 'Insumo actualizado.');
    }

    private function validatedData(Request $request, ?Input $input = null): array
    {
        $inventoryItemId = $input?->inventoryItem?->id;

        $validated = $request->validate([
            'input_category_id' => ['required', 'exists:input_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'minimum_stock' => ['required', 'numeric', 'min:0', 'max:999999.9999'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('inventory_items', 'sku')->ignore($inventoryItemId)],
            'active' => ['nullable', 'boolean'],
        ]);

        return [
            'input' => [
                'input_category_id' => $validated['input_category_id'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'minimum_stock' => $validated['minimum_stock'],
                'active' => (bool) ($validated['active'] ?? false),
            ],
            'inventory' => [
                'sku' => $validated['sku'],
            ],
        ];
    }
}
