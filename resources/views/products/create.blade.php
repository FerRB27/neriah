<x-layouts.app title="Nuevo producto">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('products.store') }}">
            @include('products._form')
        </form>
    </section>
</x-layouts.app>
