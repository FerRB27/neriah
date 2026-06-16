<x-layouts.app title="Inventario">
    @php
        $money = fn ($value, $decimals = 2) => '$'.number_format((float) $value, $decimals);
        $typeLabels = [
            'input' => 'Insumo',
            'finished_good' => 'Producto terminado',
        ];
    @endphp

    <div class="space-y-4">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <section class="border-l-4 border-zinc-950 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-500">Items</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($summary['items']) }}</div>
            </section>
            <section class="border-l-4 border-red-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-500">Criticos</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($summary['critical']) }}</div>
            </section>
            <section class="border-l-4 border-teal-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-500">Insumos</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($summary['inputs']) }}</div>
            </section>
            <section class="border-l-4 border-lime-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-500">Terminados</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($summary['finishedGoods']) }}</div>
            </section>
            <section class="border-l-4 border-blue-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm text-zinc-500">Valor inventario</div>
                <div class="mt-1 text-2xl font-semibold">{{ $money($summary['value']) }}</div>
            </section>
        </div>

        <form method="GET" action="{{ route('inventory.index') }}" class="grid gap-2 border border-zinc-200 bg-white p-4 shadow-sm lg:grid-cols-[1fr_220px_160px_auto]">
            <input name="q" value="{{ $search }}" placeholder="Buscar por nombre o SKU" class="border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
            <select name="type" class="border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
                <option value="">Todos los tipos</option>
                <option value="input" @selected($type === 'input')>Insumos</option>
                <option value="finished_good" @selected($type === 'finished_good')>Producto terminado</option>
            </select>
            <label class="flex items-center gap-2 border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium text-zinc-700">
                <input name="critical" type="checkbox" value="1" @checked($critical) class="h-4 w-4 text-teal-700 focus:ring-teal-700">
                Criticos
            </label>
            <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Filtrar</button>
        </form>

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3 text-right">Minimo</th>
                            <th class="px-4 py-3 text-right">Costo promedio</th>
                            <th class="px-4 py-3 text-right">Valor</th>
                            <th class="px-4 py-3 text-right">Movimientos</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($items as $item)
                            @php
                                $isCritical = (float) $item->current_stock <= (float) $item->minimum_stock;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $item->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $item->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $typeLabels[$item->item_type] ?? $item->item_type }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $isCritical ? 'text-red-700' : 'text-zinc-950' }}">
                                    {{ number_format((float) $item->current_stock, 4) }} {{ $item->unit }}
                                </td>
                                <td class="px-4 py-3 text-right text-zinc-600">{{ number_format((float) $item->minimum_stock, 4) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($item->average_cost, 6) }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ $money((float) $item->current_stock * (float) $item->average_cost) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->movements_count) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('inventory.show', $item) }}" class="font-semibold text-teal-700 hover:text-teal-900">Kardex</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-zinc-500">No hay items de inventario.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $items->links() }}
    </div>
</x-layouts.app>
