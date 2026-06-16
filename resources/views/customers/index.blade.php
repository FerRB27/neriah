<x-layouts.app title="Clientes">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('customers.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar cliente" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('customers.create') }}" class="bg-cyan-700 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-800">Nuevo cliente</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Ubicacion</th>
                            <th class="px-4 py-3">Ultima compra</th>
                            <th class="px-4 py-3 text-right">Total comprado</th>
                            <th class="px-4 py-3 text-right">Pedidos</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $customer->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $customer->phone ?: 'Sin telefono' }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $customer->city ?: 'Sin ciudad' }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $customer->last_purchase_date?->format('d/m/Y') ?? 'Sin compras' }}</td>
                                <td class="px-4 py-3 text-right font-medium">${{ number_format((float) $customer->total_purchased, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($customer->orders_count) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('customers.edit', $customer) }}" class="font-semibold text-cyan-700 hover:text-cyan-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-zinc-500">No hay clientes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $customers->links() }}
    </div>
</x-layouts.app>
