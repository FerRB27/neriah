<x-layouts.app title="Capital fundador">
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
        $typeLabels = [
            'contribution' => 'Aporte',
            'reimbursement' => 'Reintegro',
            'adjustment' => 'Ajuste',
        ];
    @endphp

    <div class="grid gap-4 xl:grid-cols-[1fr_380px]">
        <div class="space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <section class="border-l-4 border-blue-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Aportes</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $money($summary['contributions']) }}</div>
                </section>
                <section class="border-l-4 border-emerald-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Reintegros</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $money($summary['reimbursements']) }}</div>
                </section>
                <section class="border-l-4 border-amber-600 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Ajustes</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $money($summary['adjustments']) }}</div>
                </section>
                <section class="border-l-4 border-zinc-950 border-y border-r border-zinc-200 bg-white p-4 shadow-sm">
                    <div class="text-sm text-zinc-500">Saldo pendiente</div>
                    <div class="mt-1 text-2xl font-semibold">{{ $money($summary['pending']) }}</div>
                </section>
            </div>

            @if (session('status'))
                <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
            @endif

            <section class="overflow-hidden border border-zinc-200 bg-white shadow-sm">
                <div class="border-b border-zinc-200 px-4 py-3">
                    <h2 class="text-base font-semibold">Movimientos</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 text-sm">
                        <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-[0.08em] text-zinc-500">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Concepto</th>
                                <th class="px-4 py-3 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-zinc-600">{{ $movement->movement_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="border border-zinc-200 bg-zinc-50 px-2 py-1 text-xs font-semibold text-zinc-700">{{ $typeLabels[$movement->type->value] }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-zinc-950">{{ $movement->concept }}</div>
                                        @if ($movement->notes)
                                            <div class="mt-1 text-xs text-zinc-500">{{ $movement->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $money($movement->amount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-zinc-500">No hay movimientos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{ $movements->links() }}
        </div>

        <section class="border border-zinc-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-zinc-950">Registrar movimiento</h2>

            <form method="POST" action="{{ route('finance.founder-capital.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="type" class="block text-sm font-medium text-zinc-800">Tipo</label>
                    <select id="type" name="type" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20">
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $typeLabels[$type->value] }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-zinc-800">Monto</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20">
                    @error('amount') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="movement_date" class="block text-sm font-medium text-zinc-800">Fecha</label>
                    <input id="movement_date" name="movement_date" type="date" value="{{ old('movement_date', now()->toDateString()) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20">
                    @error('movement_date') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="concept" class="block text-sm font-medium text-zinc-800">Concepto</label>
                    <input id="concept" name="concept" value="{{ old('concept') }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20">
                    @error('concept') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-zinc-800">Notas</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-700/20">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <button class="w-full bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Registrar</button>
            </form>
        </section>
    </div>
</x-layouts.app>
