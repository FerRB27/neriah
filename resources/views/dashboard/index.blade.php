<x-layouts.app title="Dashboard">
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
        $cards = [
            ['label' => 'Ventas semana', 'value' => $money($metrics['weeklySales']), 'tone' => 'border-emerald-600'],
            ['label' => 'Ventas mes', 'value' => $money($metrics['monthlySales']), 'tone' => 'border-sky-600'],
            ['label' => 'Ventas anio', 'value' => $money($metrics['yearlySales']), 'tone' => 'border-indigo-600'],
            ['label' => 'Utilidad visible', 'value' => $money($metrics['visibleProfit']), 'tone' => 'border-lime-600'],
            ['label' => 'Utilidad oculta', 'value' => $money($metrics['hiddenProfit']), 'tone' => 'border-amber-600'],
            ['label' => 'Utilidad total', 'value' => $money($metrics['totalProfit']), 'tone' => 'border-zinc-900'],
            ['label' => 'Produccion', 'value' => number_format($metrics['productionConfirmed']), 'tone' => 'border-yellow-600'],
            ['label' => 'Inventario critico', 'value' => number_format($metrics['criticalInventory']), 'tone' => 'border-red-600'],
            ['label' => 'Pagos pendientes', 'value' => $money($metrics['pendingPayments']), 'tone' => 'border-violet-600'],
            ['label' => 'Capital fundador', 'value' => $money($metrics['founderCapitalPending']), 'tone' => 'border-blue-600'],
            ['label' => 'Fondo social', 'value' => $money($metrics['socialFundBalance']), 'tone' => 'border-pink-600'],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <section class="border-l-4 {{ $card['tone'] }} border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                <div class="text-sm font-medium text-zinc-500">{{ $card['label'] }}</div>
                <div class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">{{ $card['value'] }}</div>
            </section>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 xl:grid-cols-3">
        <section class="border border-zinc-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-base font-semibold text-zinc-950">Resumen semanal</h2>
                <div class="text-sm text-zinc-500">
                    {{ $weekRange[0]->format('d/m/Y') }} - {{ $weekRange[1]->format('d/m/Y') }}
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                <div class="bg-zinc-50 p-4">
                    <div class="text-sm text-zinc-500">Ventas</div>
                    <div class="mt-1 text-xl font-semibold">{{ $money($metrics['weeklySales']) }}</div>
                </div>
                <div class="bg-zinc-50 p-4">
                    <div class="text-sm text-zinc-500">Produccion</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format($metrics['productionConfirmed']) }}</div>
                </div>
                <div class="bg-zinc-50 p-4">
                    <div class="text-sm text-zinc-500">Alertas</div>
                    <div class="mt-1 text-xl font-semibold">{{ number_format($metrics['criticalInventory']) }}</div>
                </div>
            </div>
        </section>

        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-zinc-950">Prioridades</h2>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-600">Inventario critico</span>
                    <span class="font-semibold text-zinc-950">{{ number_format($metrics['criticalInventory']) }}</span>
                </div>
                <div class="flex items-center justify-between border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-600">Pagos pendientes</span>
                    <span class="font-semibold text-zinc-950">{{ $money($metrics['pendingPayments']) }}</span>
                </div>
                <div class="flex items-center justify-between border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-600">Capital fundador</span>
                    <span class="font-semibold text-zinc-950">{{ $money($metrics['founderCapitalPending']) }}</span>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
