<x-layouts.app title="Nueva persona">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('people.store') }}">
            @include('people._form')
        </form>
    </section>
</x-layouts.app>
