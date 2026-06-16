<x-layouts.app title="Proveedores">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar proveedor" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('suppliers.create') }}" class="bg-orange-700 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-800">Nuevo proveedor</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Proveedor</th>
                            <th class="px-4 py-3">Contacto</th>
                            <th class="px-4 py-3">Direccion</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $supplier->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">
                                    <div>{{ $supplier->phone ?: 'Sin telefono' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $supplier->email ?: 'Sin correo' }}</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-600">{{ $supplier->address ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="{{ $supplier->active ? 'bg-emerald-50 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }} px-2 py-1 text-xs font-semibold">{{ $supplier->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="font-semibold text-orange-700 hover:text-orange-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $suppliers->links() }}
    </div>
</x-layouts.app>
