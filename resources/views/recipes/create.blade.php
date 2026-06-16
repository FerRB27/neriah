<x-layouts.app title="Nueva formula">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('recipes.store') }}">
            @include('recipes._form')
        </form>
    </section>
</x-layouts.app>
