<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Producto: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6 space-y-3">
            <p><strong>ID:</strong> {{ $product->id }}</p>
            <p><strong>Nombre:</strong> {{ $product->name }}</p>
            <p><strong>Descripción:</strong> {{ $product->description ?? '—' }}</p>
            <p><strong>Precio:</strong> Bs. {{ number_format($product->price, 2) }}</p>
            <p><strong>Stock:</strong> {{ $product->stock }}</p>
            <p><strong>Almacén ID:</strong> {{ $product->warehouse_id ?? '—' }}</p>
        </div>

        <div class="mt-4 flex gap-2">
            @can('editar productos')
                <a href="{{ route('products.edit', $product) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Editar
                </a>
            @endcan

            @can('eliminar productos')
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('¿Eliminar este producto?')">
                    @csrf
                    @method('DELETE')
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </form>
            @endcan

            <a href="{{ route('products.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Volver
            </a>
        </div>
    </div>
</x-app-layout>
