<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Neriah ERP') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[#f7f7f4] text-zinc-950 antialiased">
        <main class="grid min-h-screen grid-cols-1 lg:grid-cols-[1fr_480px]">
            <section class="hidden bg-zinc-950 text-white lg:block">
                <div class="flex h-full flex-col justify-between px-12 py-10">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-300">Neriah ERP</div>
                        <h1 class="mt-8 max-w-xl text-5xl font-semibold leading-tight tracking-normal">Gestion para produccion artesanal con inventario y finanzas al centro.</h1>
                    </div>

                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div class="border border-white/15 bg-white/5 p-4">
                            <div class="text-2xl font-semibold text-emerald-300">K</div>
                            <div class="mt-2 text-zinc-300">Kardex</div>
                        </div>
                        <div class="border border-white/15 bg-white/5 p-4">
                            <div class="text-2xl font-semibold text-amber-300">P</div>
                            <div class="mt-2 text-zinc-300">Produccion</div>
                        </div>
                        <div class="border border-white/15 bg-white/5 p-4">
                            <div class="text-2xl font-semibold text-sky-300">F</div>
                            <div class="mt-2 text-zinc-300">Finanzas</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="flex items-center justify-center px-5 py-10 sm:px-8">
                {{ $slot }}
            </section>
        </main>

        @livewireScripts
    </body>
</html>
