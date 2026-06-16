@csrf

<div class="grid gap-4 xl:grid-cols-2">
    <section class="space-y-4">
        <h2 class="text-base font-semibold text-zinc-950">Producto base</h2>

        <div>
            <label for="product_category_id" class="block text-sm font-medium text-zinc-800">Categoria</label>
            <select id="product_category_id" name="product_category_id" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('product_category_id', $product->product_category_id) === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('product_category_id') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-zinc-800">Nombre</label>
            <input id="name" name="name" value="{{ old('name', $product->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
            @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="standard_cost" class="block text-sm font-medium text-zinc-800">Costo estandar</label>
                <input id="standard_cost" name="standard_cost" type="number" min="0" step="0.0001" value="{{ old('standard_cost', $product->standard_cost) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('standard_cost') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="base_price" class="block text-sm font-medium text-zinc-800">Precio base</label>
                <input id="base_price" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price', $product->base_price) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('base_price') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="commission_amount" class="block text-sm font-medium text-zinc-800">Comision vendedor</label>
                <input id="commission_amount" name="commission_amount" type="number" min="0" step="0.01" value="{{ old('commission_amount', $product->commission_amount) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('commission_amount') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="maker_payment_amount" class="block text-sm font-medium text-zinc-800">Pago elaborador</label>
                <input id="maker_payment_amount" name="maker_payment_amount" type="number" min="0" step="0.01" value="{{ old('maker_payment_amount', $product->maker_payment_amount) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('maker_payment_amount') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-base font-semibold text-zinc-950">Variante principal</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="variant_sku" class="block text-sm font-medium text-zinc-800">SKU variante</label>
                <input id="variant_sku" name="variant_sku" value="{{ old('variant_sku', $variant->sku) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('variant_sku') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="inventory_sku" class="block text-sm font-medium text-zinc-800">SKU Kardex</label>
                <input id="inventory_sku" name="inventory_sku" value="{{ old('inventory_sku', $inventoryItem?->sku) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('inventory_sku') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="variant_name" class="block text-sm font-medium text-zinc-800">Nombre variante</label>
            <input id="variant_name" name="variant_name" value="{{ old('variant_name', $variant->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
            @error('variant_name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="unit_label" class="block text-sm font-medium text-zinc-800">Unidad</label>
                <input id="unit_label" name="unit_label" value="{{ old('unit_label', $variant->unit_label) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('unit_label') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="units_per_variant" class="block text-sm font-medium text-zinc-800">Unidades por variante</label>
                <input id="units_per_variant" name="units_per_variant" type="number" min="1" step="1" value="{{ old('units_per_variant', $variant->units_per_variant) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('units_per_variant') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="weight_grams" class="block text-sm font-medium text-zinc-800">Peso gramos</label>
                <input id="weight_grams" name="weight_grams" type="number" min="0" step="0.01" value="{{ old('weight_grams', $variant->weight_grams) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('weight_grams') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-zinc-800">Precio</label>
                <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $variant->price) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('price') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="minimum_stock" class="block text-sm font-medium text-zinc-800">Stock minimo</label>
                <input id="minimum_stock" name="minimum_stock" type="number" min="0" step="0.0001" value="{{ old('minimum_stock', $inventoryItem->minimum_stock) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-lime-700 focus:ring-2 focus:ring-lime-700/20">
                @error('minimum_stock') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>
</div>

<label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
    <input name="active" type="checkbox" value="1" @checked(old('active', $product->active)) class="h-4 w-4 text-lime-700 focus:ring-lime-700">
    Producto activo
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
