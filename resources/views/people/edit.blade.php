<x-layouts.app title="Editar persona">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('people.update', $person) }}">
            @method('PUT')
            @include('people._form')
        </form>
    </section>
</x-layouts.app>
