<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registrar Movimiento</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('movements.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tipo</label>
                    <select name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="entrada" @selected(old('type') === 'entrada')>Entrada</option>
                        <option value="salida"  @selected(old('type') === 'salida')>Salida</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Producto</label>
                    <select name="product_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Seleccionar --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">ID Almacén</label>
                    <input type="number" name="warehouse_id"
                           value="{{ old('warehouse_id', auth()->user()->warehouse_id ?? '') }}"
                           min="1" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Notas</label>
                    <textarea name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Registrar
                    </button>
                    <a href="{{ route('movements.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
