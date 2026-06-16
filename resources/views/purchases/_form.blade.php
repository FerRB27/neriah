@php
    $existingRows = collect(old('lines', $purchase->exists
        ? $purchase->lines->map(fn ($line) => [
            'inventory_item_id' => $line->inventory_item_id,
            'quantity' => $line->quantity,
            'unit_cost' => $line->unit_cost,
        ])->all()
        : []
    ))->values()->all();

    $rows = array_pad($existingRows, max(8, count($existingRows)), ['inventory_item_id' => '', 'quantity' => '', 'unit_cost' => '']);
@endphp

@csrf

@error('lines')
    <div class="mb-4 border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
@enderror

<div class="grid gap-4 lg:grid-cols-3">
    <div>
        <label for="supplier_id" class="block text-sm font-medium text-zinc-800">Proveedor</label>
        <select id="supplier_id" name="supplier_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
            <option value="">Sin proveedor</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((int) old('supplier_id', $purchase->supplier_id) === $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
        @error('supplier_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="purchased_at" class="block text-sm font-medium text-zinc-800">Fecha</label>
        <input id="purchased_at" name="purchased_at" type="date" value="{{ old('purchased_at', optional($purchase->purchased_at)->toDateString() ?? now()->toDateString()) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
        @error('purchased_at') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800">Estado</label>
        <div class="mt-2 border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-semibold text-zinc-700">Borrador</div>
    </div>
</div>

<section class="mt-6 overflow-hidden border border-zinc-200">
    <div class="border-b border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-semibold text-zinc-950">Lineas de compra</div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-white text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Item inventario</th>
                    <th class="px-4 py-3 w-40">Cantidad</th>
                    <th class="px-4 py-3 w-44">Costo unitario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                @foreach ($rows as $index => $row)
                    <tr>
                        <td class="px-4 py-3">
                            <select name="lines[{{ $index }}][inventory_item_id]" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
                                <option value="">-</option>
                                @foreach ($inventoryItems as $item)
                                    <option value="{{ $item->id }}" @selected((int) ($row['inventory_item_id'] ?? 0) === $item->id)>{{ $item->name }} ({{ $item->sku }}) - {{ $item->unit }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input name="lines[{{ $index }}][quantity]" type="number" min="0.0001" step="0.0001" value="{{ $row['quantity'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
                        </td>
                        <td class="px-4 py-3">
                            <input name="lines[{{ $index }}][unit_cost]" type="number" min="0" step="0.000001" value="{{ $row['unit_cost'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-zinc-800">Observaciones</label>
    <textarea id="notes" name="notes" rows="3" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">{{ old('notes', $purchase->notes) }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar borrador</button>
    <a href="{{ route('purchases.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
