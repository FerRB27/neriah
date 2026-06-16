@php
    $existingRows = collect(old('lines', $sale->exists
        ? $sale->lines->map(fn ($line) => [
            'product_variant_id' => $line->product_variant_id,
            'quantity' => $line->quantity,
            'unit_price' => $line->unit_price,
        ])->all()
        : []
    ))->values()->all();

    $rows = array_pad($existingRows, max(6, count($existingRows)), ['product_variant_id' => '', 'quantity' => '', 'unit_price' => '']);
@endphp

@csrf

@error('lines')
    <div class="mb-4 border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
@enderror

<div class="grid gap-4 lg:grid-cols-3">
    <div>
        <label for="customer_id" class="block text-sm font-medium text-zinc-800">Cliente</label>
        <select id="customer_id" name="customer_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((int) old('customer_id', $sale->customer_id) === $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="seller_id" class="block text-sm font-medium text-zinc-800">Vendedor</label>
        <select id="seller_id" name="seller_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
            <option value="">Sin vendedor</option>
            @foreach ($sellers as $seller)
                <option value="{{ $seller->id }}" @selected((int) old('seller_id', $sale->seller_id) === $seller->id)>{{ $seller->name }}</option>
            @endforeach
        </select>
        @error('seller_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="maker_id" class="block text-sm font-medium text-zinc-800">Elaborador</label>
        <select id="maker_id" name="maker_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
            <option value="">Sin elaborador</option>
            @foreach ($makers as $maker)
                <option value="{{ $maker->id }}" @selected((int) old('maker_id', $sale->maker_id) === $maker->id)>{{ $maker->name }}</option>
            @endforeach
        </select>
        @error('maker_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sales_channel_id" class="block text-sm font-medium text-zinc-800">Canal</label>
        <select id="sales_channel_id" name="sales_channel_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
            <option value="">Sin canal</option>
            @foreach ($channels as $channel)
                <option value="{{ $channel->id }}" @selected((int) old('sales_channel_id', $sale->sales_channel_id) === $channel->id)>{{ $channel->name }}</option>
            @endforeach
        </select>
        @error('sales_channel_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sold_at" class="block text-sm font-medium text-zinc-800">Fecha</label>
        <input id="sold_at" name="sold_at" type="date" value="{{ old('sold_at', optional($sale->sold_at)->toDateString() ?? now()->toDateString()) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
        @error('sold_at') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="discount_total" class="block text-sm font-medium text-zinc-800">Descuento</label>
        <input id="discount_total" name="discount_total" type="number" min="0" step="0.01" value="{{ old('discount_total', $sale->discount_total ?? 0) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
        @error('discount_total') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<section class="mt-6 overflow-hidden border border-zinc-200">
    <div class="border-b border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-semibold text-zinc-950">Lineas de venta</div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-zinc-200 text-sm">
            <thead class="bg-white text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                <tr>
                    <th class="px-4 py-3">Producto</th>
                    <th class="px-4 py-3 w-40">Cantidad</th>
                    <th class="px-4 py-3 w-44">Precio unitario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                @foreach ($rows as $index => $row)
                    <tr>
                        <td class="px-4 py-3">
                            <select name="lines[{{ $index }}][product_variant_id]" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
                                <option value="">-</option>
                                @foreach ($variants as $variant)
                                    <option value="{{ $variant->id }}" @selected((int) ($row['product_variant_id'] ?? 0) === $variant->id)>{{ $variant->product->name }} - {{ $variant->name }} · Stock {{ number_format((float) $variant->inventoryItem->current_stock, 4) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input name="lines[{{ $index }}][quantity]" type="number" min="0.0001" step="0.0001" value="{{ $row['quantity'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
                        </td>
                        <td class="px-4 py-3">
                            <input name="lines[{{ $index }}][unit_price]" type="number" min="0" step="0.01" value="{{ $row['unit_price'] ?? '' }}" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-zinc-800">Observaciones</label>
    <textarea id="notes" name="notes" rows="3" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">{{ old('notes', $sale->notes) }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar borrador</button>
    <a href="{{ route('sales.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
