@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label for="maker_id" class="block text-sm font-medium text-zinc-800">Elaborador</label>
        <select id="maker_id" name="maker_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">
            @foreach ($makers as $maker)
                <option value="{{ $maker->id }}" @selected((int) old('maker_id', $order->maker_id) === $maker->id)>{{ $maker->name }}</option>
            @endforeach
        </select>
        @error('maker_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="recipe_id" class="block text-sm font-medium text-zinc-800">Formula</label>
        <select id="recipe_id" name="recipe_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">
            @foreach ($recipes as $recipe)
                <option value="{{ $recipe->id }}" @selected((int) old('recipe_id', $order->recipe_id) === $recipe->id)>{{ $recipe->name }} - {{ $recipe->productVariant->name }}</option>
            @endforeach
        </select>
        @error('recipe_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="produced_at" class="block text-sm font-medium text-zinc-800">Fecha</label>
        <input id="produced_at" name="produced_at" type="date" value="{{ old('produced_at', optional($order->produced_at)->toDateString() ?? now()->toDateString()) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">
        @error('produced_at') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="planned_quantity" class="block text-sm font-medium text-zinc-800">Cantidad planificada</label>
        <input id="planned_quantity" name="planned_quantity" type="number" min="0.0001" step="0.0001" value="{{ old('planned_quantity', $order->planned_quantity) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">
        @error('planned_quantity') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label for="notes" class="block text-sm font-medium text-zinc-800">Observaciones</label>
    <textarea id="notes" name="notes" rows="3" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-yellow-700 focus:ring-2 focus:ring-yellow-700/20">{{ old('notes', $order->notes) }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar borrador</button>
    <a href="{{ route('production.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
