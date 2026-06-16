@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label for="input_category_id" class="block text-sm font-medium text-zinc-800">Categoria</label>
        <select id="input_category_id" name="input_category_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('input_category_id', $input->input_category_id) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('input_category_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sku" class="block text-sm font-medium text-zinc-800">SKU Kardex</label>
        <input id="sku" name="sku" value="{{ old('sku', $inventoryItem?->sku) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
        @error('sku') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block text-sm font-medium text-zinc-800">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $input->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="unit" class="block text-sm font-medium text-zinc-800">Unidad</label>
        <input id="unit" name="unit" value="{{ old('unit', $input->unit) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
        @error('unit') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="minimum_stock" class="block text-sm font-medium text-zinc-800">Stock minimo</label>
        <input id="minimum_stock" name="minimum_stock" type="number" min="0" step="0.0001" value="{{ old('minimum_stock', $input->minimum_stock) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20">
        @error('minimum_stock') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
    <input name="active" type="checkbox" value="1" @checked(old('active', $input->active)) class="h-4 w-4 text-teal-700 focus:ring-teal-700">
    Insumo activo
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('inputs.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
