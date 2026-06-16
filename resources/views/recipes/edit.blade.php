<x-layouts.app title="Editar formula">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('recipes.update', $recipe) }}">
            @method('PUT')
            @include('recipes._form')
        </form>
    </section>
</x-layouts.app>
