<x-layouts.app title="Editar orden de produccion">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('production.update', $order) }}">
            @method('PUT')
            @include('production._form')
        </form>
    </section>
</x-layouts.app>
