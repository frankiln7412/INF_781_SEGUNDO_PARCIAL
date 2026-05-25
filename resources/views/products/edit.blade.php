<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Producto</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Precio</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">ID Almacén</label>
                    <input type="number" name="warehouse_id" value="{{ old('warehouse_id', $product->warehouse_id) }}" min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Actualizar
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
