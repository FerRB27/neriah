@php
    $existingRows = collect(old('ingredients', $recipe->exists
        ? $recipe->ingredients->map(fn ($ingredient) => [
            'inventory_item_id' => $ingredient->inventory_item_id,
            'quantity' => $ingredient->quantity,
            'unit' => $ingredient->unit,
        ])->all()
        : []
    ))->values()->all();

    $rows = array_pad($existingRows, max(10, count($existingRows)), ['inventory_item_id' => '', 'quantity' => '', 'unit' => '']);
@endphp

@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label for="product_variant_id" class="block text-sm font-medium text-zinc-800">Producto terminado</label>
        <select id="product_variant_id" name="product_variant_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
            @foreach ($variants as $variant)
                <option value="{{ $variant->id }}" @selected((int) old('product_variant_id', $recipe->product_variant_id) === $variant->id)>{{ $variant->product->name }} - {{ $variant->name }}</option>
            @endforeach
        </select>
        @error('product_variant_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-zinc-800">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $recipe->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="expected_yield" class="block text-sm font-medium text-zinc-800">Rendimiento esperado</label>
        <input id="expected_yield" name="expected_yield" type="number" min="0.0001" step="0.0001" value="{{ old('expected_yield', $recipe->expected_yield) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
        @error('expected_yield') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="yield_unit" class="block text-sm font-medium text-zinc-800">Unidad de rendimiento</label>
        <input id="yield_unit" name="yield_unit" value="{{ old('yield_unit', $recipe->yield_unit ?: 'unidad') }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
        @error('yield_unit') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<section class="mt-6 overflow-hidden border border-zinc-200">
    <div class="border-b border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-semibold text-zinc-950">Ingredientes</div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-white text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Insumo</th>
                    <th class="px-4 py-3 w-40">Cantidad</th>
                    <th class="px-4 py-3 w-32">Unidad</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                @foreach ($rows as $index => $row)
                    <tr>
                        <td class="px-4 py-3">
                            <select name="ingredients[{{ $index }}][inventory_item_id]" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
                                <option value="">-</option>
                                @foreach ($inventoryItems as $item)
                                    <option value="{{ $item->id }}" @selected((int) ($row['inventory_item_id'] ?? 0) === $item->id)>{{ $item->name }} ({{ $item->sku }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input name="ingredients[{{ $index }}][quantity]" type="number" min="0.0001" step="0.0001" value="{{ $row['quantity'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
                        </td>
                        <td class="px-4 py-3">
                            <input name="ingredients[{{ $index }}][unit]" value="{{ $row['unit'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
    <input name="active" type="checkbox" value="1" @checked(old('active', $recipe->active)) class="h-4 w-4 text-amber-700 focus:ring-amber-700">
    Formula activa
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('recipes.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
