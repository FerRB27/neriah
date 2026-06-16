<x-layouts.app title="Nuevo cliente">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('customers.store') }}">
            @include('customers._form')
        </form>
    </section>
</x-layouts.app>
