<x-layouts.app title="Productos">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('products.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar producto" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('products.create') }}" class="bg-lime-700 px-4 py-2 text-sm font-semibold text-white hover:bg-lime-800">Nuevo producto</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Variante</th>
                            <th class="px-4 py-3 text-right">Precio</th>
                            <th class="px-4 py-3 text-right">Costo estandar</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($products as $product)
                            @php $variant = $product->variants->first(); @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-950">{{ $product->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $product->category->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">
                                    <div>{{ $variant?->name ?? '-' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $variant?->sku ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">${{ number_format((float) ($variant?->price ?? $product->base_price), 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format((float) $product->standard_cost, 4) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) ($variant?->inventoryItem?->current_stock ?? 0), 4) }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $product->active ? 'bg-emerald-50 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }} px-2 py-1 text-xs font-semibold">{{ $product->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('products.edit', $product) }}" class="font-semibold text-lime-700 hover:text-lime-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-zinc-500">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $products->links() }}
    </div>
</x-layouts.app>
