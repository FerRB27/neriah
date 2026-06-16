<x-layouts.app title="Nueva compra">
    <section class="border border-zinc-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('purchases.store') }}">
            @include('purchases._form')
        </form>
    </section>
</x-layouts.app>
