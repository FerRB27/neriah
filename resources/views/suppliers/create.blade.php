<x-layouts.app title="Nuevo proveedor">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @include('suppliers._form')
        </form>
    </section>
</x-layouts.app>
