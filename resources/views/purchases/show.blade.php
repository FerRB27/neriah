<x-layouts.app title="Detalle de compra">
    @php
        $money = fn ($value, $decimals = 2) => '$'.number_format((float) $value, $decimals);
    @endphp

    <div class="space-y-4">
        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        @error('purchase')
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
        @enderror

        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-[0.14em] text-zinc-500">Compra #{{ $purchase->id }}</div>
                    <h2 class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">{{ $purchase->supplier?->name ?? 'Sin proveedor' }}</h2>
                    <div class="mt-2 text-sm text-zinc-600">{{ $purchase->purchased_at->format('d/m/Y') }}</div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="{{ $purchase->status === 'confirmed' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-3 py-2 text-sm font-semibold">
                        {{ $purchase->status === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                    </span>
                    @if ($purchase->status !== 'confirmed')
                        <a href="{{ route('purchases.edit', $purchase) }}" class="border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-50">Editar</a>
                        <form method="POST" action="{{ route('purchases.confirm', $purchase) }}">
                            @csrf
                            <button class="bg-orange-700 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-800">Confirmar compra</button>
                        </form>
                    @endif
                </div>
            </div>

            @if ($purchase->notes)
                <div class="mt-4 border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-700">{{ $purchase->notes }}</div>
            @endif
        </section>

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="border-b border-zinc-200 px-4 py-3">
                <h2 class="text-base font-semibold">Lineas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-right">Costo unitario</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach ($purchase->lines as $line)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $line->inventoryItem->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $line->inventoryItem->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $line->quantity, 4) }} {{ $line->inventoryItem->unit }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($line->unit_cost, 6) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $money($line->total_cost) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-zinc-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-semibold">Total</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ $money($purchase->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        @if ($purchase->inventoryMovements->isNotEmpty())
            <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 px-4 py-3">
                    <h2 class="text-base font-semibold">Movimientos Kardex generados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3 text-right">Entrada</th>
                                <th class="px-4 py-3 text-right">Costo promedio</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @foreach ($purchase->inventoryMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-zinc-600">{{ $movement->movement_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $movement->inventoryItem->name }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format((float) $movement->quantity, 4) }}</td>
                                    <td class="px-4 py-3 text-right">{{ $money($movement->running_average_cost, 6) }}</td>
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
