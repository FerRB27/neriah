<x-layouts.app title="Editar insumo">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('inputs.update', $input) }}">
            @method('PUT')
            @include('inputs._form')
        </form>
    </section>
</x-layouts.app>
