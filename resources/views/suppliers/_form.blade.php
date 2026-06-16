@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium text-zinc-800">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $supplier->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-zinc-800">Telefono</label>
        <input id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
        @error('phone') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-zinc-800">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email', $supplier->email) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
        @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium text-zinc-800">Direccion</label>
        <input id="address" name="address" value="{{ old('address', $supplier->address) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-orange-700 focus:ring-2 focus:ring-orange-700/20">
        @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
    <input name="active" type="checkbox" value="1" @checked(old('active', $supplier->active)) class="h-4 w-4 text-orange-700 focus:ring-orange-700">
    Proveedor activo
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('suppliers.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
