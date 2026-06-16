<x-layouts.app title="Compras">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('purchases.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <select name="status" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
                    <option value="">Todos los estados</option>
                    <option value="draft" @selected($status === 'draft')>Borrador</option>
                    <option value="confirmed" @selected($status === 'confirmed')>Confirmada</option>
                </select>
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Filtrar</button>
            </form>

            <a href="{{ route('purchases.create') }}" class="bg-orange-700 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-800">Nueva compra</a>
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
                            <th class="px-4 py-3">Proveedor</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td class="px-4 py-3 text-zinc-600">{{ $purchase->purchased_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $purchase->supplier?->name ?? 'Sin proveedor' }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $purchase->status === 'confirmed' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800' }} px-2 py-1 text-xs font-semibold">
                                        {{ $purchase->status === 'confirmed' ? 'Confirmada' : 'Borrador' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">${{ number_format((float) $purchase->total_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="font-semibold text-orange-700 hover:text-orange-900">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">No hay compras registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $purchases->links() }}
    </div>
</x-layouts.app>
