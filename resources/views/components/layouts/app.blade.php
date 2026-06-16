<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' - ' : '' }}{{ config('app.name', 'Neriah ERP') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[#f4f5f2] text-zinc-950 antialiased">
        @php
            $navigation = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'permission' => 'dashboard.view', 'accent' => 'bg-emerald-500'],
                ['label' => 'Personas', 'route' => 'modules.show', 'params' => ['people'], 'permission' => 'people.manage', 'accent' => 'bg-sky-500'],
                ['label' => 'Clientes', 'route' => 'modules.show', 'params' => ['customers'], 'permission' => 'customers.manage', 'accent' => 'bg-cyan-500'],
                ['label' => 'Productos', 'route' => 'modules.show', 'params' => ['products'], 'permission' => 'products.manage', 'accent' => 'bg-lime-500'],
                ['label' => 'Formulas', 'route' => 'modules.show', 'params' => ['recipes'], 'permission' => 'recipes.manage', 'accent' => 'bg-amber-500'],
                ['label' => 'Inventario', 'route' => 'modules.show', 'params' => ['inventory'], 'permission' => 'inventory.view', 'accent' => 'bg-teal-500'],
                ['label' => 'Compras', 'route' => 'modules.show', 'params' => ['purchases'], 'permission' => 'purchases.manage', 'accent' => 'bg-orange-500'],
                ['label' => 'Produccion', 'route' => 'modules.show', 'params' => ['production'], 'permission' => 'production.manage', 'accent' => 'bg-yellow-500'],
                ['label' => 'Ventas', 'route' => 'modules.show', 'params' => ['sales'], 'permission' => 'sales.manage', 'accent' => 'bg-rose-500'],
                ['label' => 'Comisiones', 'route' => 'modules.show', 'params' => ['commissions'], 'permission' => 'commissions.manage', 'accent' => 'bg-fuchsia-500'],
                ['label' => 'Pagos', 'route' => 'modules.show', 'params' => ['payments'], 'permission' => 'payments.manage', 'accent' => 'bg-violet-500'],
                ['label' => 'Activos', 'route' => 'modules.show', 'params' => ['assets'], 'permission' => 'assets.manage', 'accent' => 'bg-stone-500'],
                ['label' => 'Finanzas', 'route' => 'modules.show', 'params' => ['finance'], 'permission' => 'finance.view', 'accent' => 'bg-blue-500'],
                ['label' => 'Fondo Social', 'route' => 'modules.show', 'params' => ['social-fund'], 'permission' => 'social-fund.manage', 'accent' => 'bg-pink-500'],
                ['label' => 'Reportes', 'route' => 'modules.show', 'params' => ['reports'], 'permission' => 'reports.view', 'accent' => 'bg-indigo-500'],
                ['label' => 'Seguridad', 'route' => 'modules.show', 'params' => ['security'], 'permission' => 'security.manage', 'accent' => 'bg-red-500'],
            ];
        @endphp

        <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
            <aside class="hidden border-r border-zinc-200 bg-zinc-950 text-white lg:flex lg:min-h-screen lg:flex-col">
                <div class="border-b border-white/10 px-6 py-6">
                    <div class="text-lg font-semibold">Neriah ERP</div>
                    <div class="mt-1 text-sm text-zinc-400">{{ auth()->user()->name }}</div>
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                    @foreach ($navigation as $item)
                        @can($item['permission'])
                            @php
                                $href = isset($item['params']) ? route($item['route'], $item['params']) : route($item['route']);
                                $active = request()->url() === $href;
                            @endphp
                            <a href="{{ $href }}" class="{{ $active ? 'bg-white text-zinc-950' : 'text-zinc-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 text-sm font-medium">
                                <span class="{{ $item['accent'] }} h-2.5 w-2.5 shrink-0"></span>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endcan
                    @endforeach
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 p-3">
                    @csrf
                    <button type="submit" class="w-full bg-white px-3 py-2.5 text-left text-sm font-semibold text-zinc-950 hover:bg-zinc-200">
                        Cerrar sesion
                    </button>
                </form>
            </aside>

            <div class="min-w-0">
                <header class="sticky top-0 z-10 border-b border-zinc-200 bg-white/95 backdrop-blur">
                    <div class="flex min-h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.14em] text-zinc-500">Neriah ERP</div>
                            <h1 class="text-lg font-semibold text-zinc-950 sm:text-xl">{{ $title ?? 'Dashboard' }}</h1>
                        </div>

                        <details class="relative lg:hidden">
                            <summary class="cursor-pointer border border-zinc-300 bg-white px-3 py-2 text-sm font-semibold text-zinc-900 marker:content-none">Menu</summary>
                            <div class="absolute right-0 mt-2 w-64 border border-zinc-200 bg-white p-2 shadow-lg">
                                @foreach ($navigation as $item)
                                    @can($item['permission'])
                                        @php
                                            $href = isset($item['params']) ? route($item['route'], $item['params']) : route($item['route']);
                                        @endphp
                                        <a href="{{ $href }}" class="block px-3 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-100">{{ $item['label'] }}</a>
                                    @endcan
                                @endforeach
                                <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-zinc-200 pt-2">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 text-left text-sm font-semibold text-zinc-950 hover:bg-zinc-100">Cerrar sesion</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </header>

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
