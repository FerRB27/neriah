<x-layouts.app title="Detalle de produccion">
    @php
        $money = fn ($value, $decimals = 2) => '$'.number_format((float) $value, $decimals);
        $hasMissing = collect($requirements)->contains(fn ($row) => $row['missing'] > 0);
    @endphp

    <div class="space-y-4">
        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        @error('production')
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
        @enderror

        @error('produced_quantity')
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
        @enderror

        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 xl:flex-row xl:items-start">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-[0.14em] text-zinc-500">Orden #{{ $order->id }}</div>
                    <h2 class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">{{ $order->recipe->name }}</h2>
                    <div class="mt-2 text-sm text-zinc-600">{{ $order->productVariant->name }} · {{ $order->maker->name }} · {{ $order->produced_at->format('d/m/Y') }}</div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="{{ $order->status->value === 'confirmed' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-3 py-2 text-sm font-semibold">
                        {{ $order->status->value === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                    </span>
                    @if ($order->status->value !== 'confirmed')
                        <a href="{{ route('production.edit', $order) }}" class="border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-50">Editar</a>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Planificado</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format((float) $order->planned_quantity, 4) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Producido</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format((float) $order->produced_quantity, 4) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Costo real</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($order->real_cost_total) }}</div>
                </div>
            </div>
        </section>

        @if ($order->status->value !== 'confirmed')
            <section class="border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                    <div>
                        <h2 class="text-base font-semibold text-zinc-950">Confirmar produccion</h2>
                        <p class="mt-1 text-sm text-zinc-600">La confirmacion consumira insumos y generara producto terminado en Kardex.</p>
                    </div>
                    <form method="POST" action="{{ route('production.confirm', $order) }}" class="flex flex-col gap-2 sm:flex-row">
                        @csrf
                        <input name="produced_quantity" type="number" min="0.0001" step="0.0001" value="{{ old('produced_quantity', $order->planned_quantity) }}" class="border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">
                        <button class="bg-yellow-700 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-800">Confirmar</button>
                    </form>
                </div>
                @if ($hasMissing)
                    <div class="mt-4 border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">Hay insumos insuficientes para confirmar la cantidad planificada.</div>
                @endif
            </section>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="border-b border-zinc-200 px-4 py-3">
                <h2 class="text-base font-semibold">Requerimientos de insumos</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Insumo</th>
                            <th class="px-4 py-3 text-right">Requerido</th>
                            <th class="px-4 py-3 text-right">Disponible</th>
                            <th class="px-4 py-3 text-right">Faltante</th>
                            <th class="px-4 py-3 text-right">Costo estimado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach ($requirements as $row)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $row['item']->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $row['item']->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['required'], 4) }} {{ $row['item']->unit }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($row['available'], 4) }}</td>
                                <td class="px-4 py-3 text-right {{ $row['missing'] > 0 ? 'font-semibold text-red-700' : 'text-zinc-500' }}">{{ number_format($row['missing'], 4) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($row['total_cost']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if ($order->consumptions->isNotEmpty())
            <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 px-4 py-3">
                    <h2 class="text-base font-semibold">Consumos reales</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Insumo</th>
                                <th class="px-4 py-3 text-right">Consumido</th>
                                <th class="px-4 py-3 text-right">Costo unitario</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @foreach ($order->consumptions as $consumption)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $consumption->inventoryItem->name }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $consumption->consumed_quantity, 4) }}</td>
                                    <td class="px-4 py-3 text-right">{{ $money($consumption->unit_cost, 6) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $money($consumption->total_cost) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($order->inventoryMovements->isNotEmpty())
            <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 px-4 py-3">
                    <h2 class="text-base font-semibold">Movimientos Kardex generados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3">Movimiento</th>
                                <th class="px-4 py-3 text-right">Cantidad</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @foreach ($order->inventoryMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $movement->inventoryItem->name }}</td>
                                    <td class="px-4 py-3 text-zinc-600">{{ $movement->direction->value === 'in' ? 'Entrada' : 'Salida' }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $movement->quantity, 4) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $movement->running_quantity, 4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
