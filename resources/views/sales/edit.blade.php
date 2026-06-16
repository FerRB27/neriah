<x-layouts.app title="Editar venta">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('sales.update', $sale) }}">
            @method('PUT')
            @include('sales._form')
        </form>
    </section>
</x-layouts.app>
