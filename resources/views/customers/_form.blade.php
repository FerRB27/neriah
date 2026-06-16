@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-zinc-800" for="name">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $customer->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">
        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="phone">Telefono</label>
        <input id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">
        @error('phone') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="city">Ciudad</label>
        <input id="city" name="city" value="{{ old('city', $customer->city) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">
        @error('city') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="address">Direccion</label>
        <input id="address" name="address" value="{{ old('address', $customer->address) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">
        @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-4">
    <label class="block text-sm font-medium text-zinc-800" for="notes">Observaciones</label>
    <textarea id="notes" name="notes" rows="4" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-cyan-700 focus:ring-2 focus:ring-cyan-700/20">{{ old('notes', $customer->notes) }}</textarea>
    @error('notes') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('customers.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
