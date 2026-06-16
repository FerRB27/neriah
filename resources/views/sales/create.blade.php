<x-layouts.app title="Nueva venta">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('sales.store') }}">
            @include('sales._form')
        </form>
    </section>
</x-layouts.app>
