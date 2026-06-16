<x-layouts.app title="Detalle de venta">
    @php
        $money = fn ($value, $decimals = 2) => '$'.number_format((float) $value, $decimals);
    @endphp

    <div class="space-y-4">
        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        @error('sale')
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ $message }}</div>
        @enderror

        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col justify-between gap-4 xl:flex-row xl:items-start">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-[0.14em] text-zinc-500">Venta #{{ $sale->id }}</div>
                    <h2 class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">{{ $sale->customer->name }}</h2>
                    <div class="mt-2 text-sm text-zinc-600">{{ $sale->sold_at->format('d/m/Y') }} · {{ $sale->salesChannel?->name ?? 'Sin canal' }}</div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="{{ $sale->status->value === 'confirmed' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-3 py-2 text-sm font-semibold">
                        {{ $sale->status->value === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                    </span>
                    @if ($sale->status->value !== 'confirmed')
                        <a href="{{ route('sales.edit', $sale) }}" class="border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-50">Editar</a>
                        <form method="POST" action="{{ route('sales.confirm', $sale) }}">
                            @csrf
                            <button class="bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800">Confirmar venta</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3 xl:grid-cols-6">
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Total</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->total_amount) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Visible</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->visible_profit) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Oculta</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->hidden_profit) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Utilidad total</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money((float) $sale->visible_profit + (float) $sale->hidden_profit) }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Vendedor</div>
                    <div class="mt-1 text-sm font-semibold">{{ $sale->seller?->name ?? '-' }}</div>
                </div>
                <div class="border border-zinc-200 bg-zinc-50 px-4 py-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">Elaborador</div>
                    <div class="mt-1 text-sm font-semibold">{{ $sale->maker?->name ?? '-' }}</div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="border-b border-zinc-200 px-4 py-3">
                <h2 class="text-base font-semibold">Lineas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3 text-right">Precio</th>
                            <th class="px-4 py-3 text-right">Costo est.</th>
                            <th class="px-4 py-3 text-right">Costo real</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Utilidad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach ($sale->lines as $line)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $line->productVariant->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $line->inventoryItem->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $line->quantity, 4) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($line->unit_price) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($line->standard_unit_cost, 6) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money($line->real_unit_cost, 6) }}</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $money($line->line_total) }}</td>
                                <td class="px-4 py-3 text-right">{{ $money((float) $line->visible_profit + (float) $line->hidden_profit) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if ($sale->commissionEntries->isNotEmpty())
            <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 px-4 py-3">
                    <h2 class="text-base font-semibold">Comisiones y pagos generados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Persona</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @foreach ($sale->commissionEntries as $entry)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-zinc-950">{{ $entry->person->name }}</td>
                                    <td class="px-4 py-3 text-zinc-600">{{ $entry->type === 'seller' ? 'Comision vendedor' : 'Pago elaborador' }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $money($entry->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($sale->profitAllocation)
            <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="border-l-4 border-pink-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Fondo social</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->profitAllocation->social_fund_amount) }}</div>
                </div>
                <div class="border-l-4 border-lime-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Reinversion</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->profitAllocation->reinvestment_amount) }}</div>
                </div>
                <div class="border-l-4 border-blue-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Recuperacion fundador</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->profitAllocation->founder_recovery_amount) }}</div>
                </div>
                <div class="border-l-4 border-zinc-950 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Reserva</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($sale->profitAllocation->reserve_amount) }}</div>
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
