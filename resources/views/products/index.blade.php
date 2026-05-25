<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Productos
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Lista de Productos</h3>
            {{-- Botón Crear: solo visible para quien tenga el permiso 'crear productos' --}}
            @can('crear productos')
                <a href="{{ route('products.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Crear Producto
                </a>
            @endcan
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Bs. {{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-sm flex gap-2">
                            <a href="{{ route('products.show', $product) }}"
                               class="text-blue-600 hover:underline">Ver</a>

                            {{-- Editar: admin O supervisor (editar productos) --}}
                            @can('editar productos')
                                <a href="{{ route('products.edit', $product) }}"
                                   class="text-yellow-600 hover:underline">Editar</a>
                            @endcan

                            {{-- Eliminar: solo admin (eliminar productos) --}}
                            @can('eliminar productos')
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('¿Eliminar producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay productos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</x-app-layout>
