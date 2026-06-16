<x-layouts.app title="Ventas">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('sales.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <select name="status" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-rose-700 focus:ring-2 focus:ring-rose-700/20">
                    <option value="">Todos los estados</option>
                    <option value="draft" @selected($status === 'draft')>Borrador</option>
                    <option value="confirmed" @selected($status === 'confirmed')>Confirmada</option>
                    <option value="cancelled" @selected($status === 'cancelled')>Cancelada</option>
                </select>
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Filtrar</button>
            </form>

            <a href="{{ route('sales.create') }}" class="bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800">Nueva venta</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Vendedor</th>
                            <th class="px-4 py-3">Canal</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Utilidad</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 text-zinc-600">{{ $sale->sold_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $sale->customer->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $sale->seller?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $sale->salesChannel?->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $sale->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float) $sale->visible_profit + (float) $sale->hidden_profit, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $sale->status->value === 'confirmed' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-2 py-1 text-xs font-semibold">
                                        {{ $sale->status->value === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="font-semibold text-rose-700 hover:text-rose-900">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-zinc-500">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $sales->links() }}
    </div>
</x-layouts.app>
