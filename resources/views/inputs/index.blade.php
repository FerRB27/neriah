<x-layouts.app title="Insumos">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('inputs.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar insumo" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('inputs.create') }}" class="bg-teal-700 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-800">Nuevo insumo</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Insumo</th>
                            <th class="px-4 py-3">Categoria</th>
                            <th class="px-4 py-3">SKU Kardex</th>
                            <th class="px-4 py-3 text-right">Stock</th>
                            <th class="px-4 py-3 text-right">Minimo</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($inputs as $input)
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $input->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $input->category->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">{{ $input->inventoryItem?->sku ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) ($input->inventoryItem?->current_stock ?? 0), 4) }} {{ $input->unit }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $input->minimum_stock, 4) }} {{ $input->unit }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $input->active ? 'bg-emerald-50 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }} px-2 py-1 text-xs font-semibold">{{ $input->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('inputs.edit', $input) }}" class="font-semibold text-teal-700 hover:text-teal-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-zinc-500">No hay insumos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $inputs->links() }}
    </div>
</x-layouts.app>
