<x-layouts.app title="Personas">
    <div class="space-y-4">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('people.index') }}" class="flex w-full gap-2 sm:max-w-md">
                <input name="q" value="{{ $search }}" placeholder="Buscar persona" class="w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-sky-700 focus:ring-2 focus:ring-sky-700/20">
                <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Buscar</button>
            </form>

            <a href="{{ route('people.create') }}" class="bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">Nueva persona</a>
        </div>

        @if (session('status'))
            <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Contacto</th>
                            <th class="px-4 py-3">Roles</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @forelse ($people as $person)
                            <tr>
                                <td class="px-4 py-3 font-medium text-zinc-950">{{ $person->name }}</td>
                                <td class="px-4 py-3 text-zinc-600">
                                    <div>{{ $person->phone ?: 'Sin telefono' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $person->email ?: 'Sin correo' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($person->roleAssignments as $assignment)
                                            <span class="border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-medium text-zinc-700">{{ ucfirst(str_replace('_', ' ', $assignment->role->value)) }}</span>
                                        @empty
                                            <span class="text-zinc-500">Sin roles</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="{{ $person->active ? 'bg-emerald-50 text-emerald-800' : 'bg-zinc-100 text-zinc-600' }} px-2 py-1 text-xs font-semibold">
                                        {{ $person->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('people.edit', $person) }}" class="font-semibold text-sky-700 hover:text-sky-900">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">No hay personas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $people->links() }}
    </div>
</x-layouts.app>
