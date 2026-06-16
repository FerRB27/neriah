<x-layouts.app title="Kardex">
    @php
        $money = fn ($value, $decimals = 2) => '$'.number_format((float) $value, $decimals);
        $typeLabels = [
            'purchase' => 'Compra',
            'production_consumption' => 'Consumo produccion',
            'production_output' => 'Produccion',
            'sale' => 'Venta',
            'adjustment' => 'Ajuste',
            'waste' => 'Merma',
            'delivery' => 'Entrega',
            'return' => 'Devolucion',
        ];
    @endphp

    <div class="space-y-4">
        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                <div>
                    <a href="{{ route('inventory.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">Volver a inventario</a>
                    <h2 class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">{{ $item->name }}</h2>
                    <div class="mt-1 text-sm text-zinc-500">{{ $item->sku }} · {{ $item->unit }}</div>
                </div>

                <div class="grid gap-2 sm:grid-cols-3">
                    <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Stock</div>
                        <div class="mt-1 text-xl font-semibold">{{ number_format((float) $item->current_stock, 4) }}</div>
                    </div>
                    <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Minimo</div>
                        <div class="mt-1 text-xl font-semibold">{{ number_format((float) $item->minimum_stock, 4) }}</div>
                    </div>
                    <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Costo promedio</div>
                        <div class="mt-1 text-xl font-semibold">{{ $money($item->average_cost, 6) }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid gap-3 md:grid-cols-3">
                <div class="border border-zinc-200 px-4 py-3 text-sm">
                    <div class="text-zinc-500">Tipo</div>
                    <div class="mt-1 font-semibold text-zinc-950">{{ $item->item_type === 'finished_good' ? 'Producto terminado' : 'Insumo' }}</div>
                </div>
                <div class="border border-zinc-200 px-4 py-3 text-sm">
                    <div class="text-zinc-500">Origen</div>
                    <div class="mt-1 font-semibold text-zinc-950">
                        {{ $item->input?->category?->name ?? $item->productVariant?->product?->name ?? '-' }}
                    </div>
                </div>
                <div class="border border-zinc-200 px-4 py-3 text-sm">
                    <div class="text-zinc-500">Valor actual</div>
                    <div class="mt-1 font-semibold text-zinc-950">{{ $money((float) $item->current_stock * (float) $item->average_cost) }}</div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="border-b border-zinc-200 px-4 py-3">
                <h2 class="text-base font-semibold">Movimientos Kardex</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Origen</th>
                            <th class="px-4 py-3 text-right">Entrada</th>
                            <th class="px-4 py-3 text-right">Salida</th>
                            <th class="px-4 py-3 text-right">Costo unitario</th>
                            <th class="px-4 py-3 text-right">Saldo</th>
                            <th class="px-4 py-3 text-right">Costo prom.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-4 py-3 text-zinc-600">{{ $movement->movement_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $typeLabels[$movement->type->value] ?? $movement->type->value }}</td>
                                <td class="px-4 py-3 text-zinc-600">
                                    @if ($movement->purchase)
                                        Compra #{{ $movement->purchase->id }}
                                    @elseif ($movement->productionOrder)
                                        Produccion #{{ $movement->productionOrder->id }}
                                    @elseif ($movement->sale)
                                        Venta #{{ $movement->sale->id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-emerald-700">
                                    {{ $movement->direction->value === 'in' ? number_format((float) $movement->quantity, 4) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right text-red-700">
                                    {{ $movement->direction->value === 'out' ? number_format((float) $movement->quantity, 4) : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">{{ $money($movement->unit_cost, 6) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format((float) $movement->running_quantity, 4) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($movement->running_average_cost, 6) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-zinc-500">Este item aun no tiene movimientos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $movements->links() }}
    </div>
</x-layouts.app>
