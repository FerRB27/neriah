<x-layouts.app title="Editar compra">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('purchases.update', $purchase) }}">
            @method('PUT')
            @include('purchases._form')
        </form>
    </section>
</x-layouts.app>
