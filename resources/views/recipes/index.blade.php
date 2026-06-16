<x-layouts.app title="Formulas">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('recipes.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar formula" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('recipes.create') }}" class="bg-amber-700 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-800">Nueva formula</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Formula</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-right">Rendimiento</th>
                            <th class="px-4 py-3 text-right">Ingredientes</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($recipes as $recipe)
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $recipe->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">
                                    <div>{{ $recipe->productVariant->name }}</div>
                                    <div class="text-xs text-zinc-500">{{ $recipe->productVariant->product->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format((float) $recipe->expected_yield, 4) }} {{ $recipe->yield_unit }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($recipe->ingredients_count ?? $recipe->ingredients->count()) }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $recipe->active ? 'bg-emerald-50 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }} px-2 py-1 text-xs font-semibold">{{ $recipe->active ? 'Activa' : 'Inactiva' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('recipes.edit', $recipe) }}" class="font-semibold text-amber-700 hover:text-amber-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-zinc-500">No hay formulas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $recipes->links() }}
    </div>
</x-layouts.app>
