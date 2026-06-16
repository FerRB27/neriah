<x-layouts.app title="Editar cliente">
    <section class="grid gap-4 xl:grid-cols-[1fr_320px]">
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="border border-zinc-200 bg-white p-6 shadow-sm">
            @method('PUT')
            @include('customers._form')
        </form>

        <aside class="border border-zinc-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-zinc-950">Indicadores</h2>
            <div class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4 border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-500">Primer compra</span>
                    <span class="font-semibold">{{ $customer->first_purchase_date?->format('d/m/Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4 border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-500">Ultima compra</span>
                    <span class="font-semibold">{{ $customer->last_purchase_date?->format('d/m/Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between gap-4 border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-500">Total comprado</span>
                    <span class="font-semibold">${{ number_format((float) $customer->total_purchased, 2) }}</span>
                </div>
                <div class="flex justify-between gap-4 border border-zinc-200 px-3 py-2">
                    <span class="text-zinc-500">Pedidos</span>
                    <span class="font-semibold">{{ number_format($customer->orders_count) }}</span>
                </div>
            </div>
        </aside>
    </section>
</x-layouts.app>
