<x-layouts.app title="Editar producto">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @method('PUT')
            @include('products._form')
        </form>
    </section>
</x-layouts.app>
