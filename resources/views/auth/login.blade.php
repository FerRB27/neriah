<x-layouts.auth>
    <div class="w-full max-w-sm">
        <div class="mb-8 lg:hidden">
            <div class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-700">Neriah ERP</div>
            <h1 class="mt-3 text-3xl font-semibold tracking-normal text-zinc-950">Gestion artesanal con control financiero.</h1>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="border border-zinc-200 bg-white p-6 shadow-sm">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-zinc-800">Correo</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    autofocus
                    class="mt-2 block w-full border border-zinc-300 bg-white px-3 py-2.5 text-base text-zinc-950 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="password" class="block text-sm font-medium text-zinc-800">Contrasena</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    class="mt-2 block w-full border border-zinc-300 bg-white px-3 py-2.5 text-base text-zinc-950 outline-none focus:border-emerald-700 focus:ring-2 focus:ring-emerald-700/20"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <label class="mt-5 flex items-center gap-2 text-sm text-zinc-700">
                <input name="remember" type="checkbox" class="h-4 w-4 border-zinc-300 text-emerald-700 focus:ring-emerald-700">
                Mantener sesion
            </label>

            <button type="submit" class="mt-6 w-full bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2">
                Ingresar
            </button>
        </form>
    </div>
</x-layouts.auth>
