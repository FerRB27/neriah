@php
    $roleLabels = [
        'administrator' => 'Administrador',
        'maker' => 'Elaborador',
        'seller' => 'Vendedor',
        'distributor' => 'Distribuidor',
        'viewer' => 'Consulta',
    ];
@endphp

@csrf

<div class="grid gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-zinc-800" for="name">Nombre</label>
        <input id="name" name="name" value="{{ old('name', $person->name) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-sky-700 focus:ring-2 focus:ring-sky-700/20">
        @error('name') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="phone">Telefono</label>
        <input id="phone" name="phone" value="{{ old('phone', $person->phone) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-sky-700 focus:ring-2 focus:ring-sky-700/20">
        @error('phone') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="email">Correo</label>
        <input id="email" name="email" type="email" value="{{ old('email', $person->email) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-sky-700 focus:ring-2 focus:ring-sky-700/20">
        @error('email') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-zinc-800" for="address">Direccion</label>
        <input id="address" name="address" value="{{ old('address', $person->address) }}" class="mt-2 w-full border border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-sky-700 focus:ring-2 focus:ring-sky-700/20">
        @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-5">
    <div class="text-sm font-medium text-zinc-800">Roles operativos</div>
    <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ($roles as $role)
            <label class="flex items-center gap-2 border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm text-zinc-800">
                <input name="roles[]" type="checkbox" value="{{ $role->value }}" @checked(in_array($role->value, old('roles', $selectedRoles), true)) class="h-4 w-4 text-sky-700 focus:ring-sky-700">
                {{ $roleLabels[$role->value] ?? $role->value }}
            </label>
        @endforeach
    </div>
    @error('roles') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
</div>

<label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
    <input name="active" type="checkbox" value="1" @checked(old('active', $person->active)) class="h-4 w-4 text-sky-700 focus:ring-sky-700">
    Persona activa
</label>

<div class="mt-6 flex items-center gap-3">
    <button class="bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800">Guardar</button>
    <a href="{{ route('people.index') }}" class="px-4 py-2 text-sm font-semibold text-zinc-700 hover:text-zinc-950">Cancelar</a>
</div>
