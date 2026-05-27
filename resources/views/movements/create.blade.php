<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('movements.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Registrar Movimiento</h2>
                <p class="text-sm text-gray-500 mt-0.5">Entrada o salida de mercancía</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Datos del movimiento</span>
            </div>
            <form method="POST" action="{{ route('movements.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de movimiento <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors
                                      {{ old('type') === 'entrada' || old('type') === null ? '' : '' }}
                                      hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                            <input type="radio" name="type" value="entrada" class="text-emerald-600"
                                   {{ old('type', 'entrada') === 'entrada' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-semibold text-gray-800">Entrada</span>
                                <span class="text-xs text-gray-500">Ingreso de mercancía</span>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors
                                      hover:border-amber-300 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="type" value="salida" class="text-amber-600"
                                   {{ old('type') === 'salida' ? 'checked' : '' }}>
                            <div>
                                <span class="block text-sm font-semibold text-gray-800">Salida</span>
                                <span class="text-xs text-gray-500">Despacho de mercancía</span>
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Producto <span class="text-red-500">*</span>
                    </label>
                    <select name="product_id" required
                            class="block w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('product_id') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">— Seleccionar producto —</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="block w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('quantity') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ID Almacén <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="warehouse_id" min="1" required
                               value="{{ old('warehouse_id', auth()->user()->warehouse_id ?? '') }}"
                               class="block w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('warehouse_id') ? 'border-red-400' : 'border-gray-300' }}">
                        @error('warehouse_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea name="notes" rows="2"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Registrar movimiento
                    </button>
                    <a href="{{ route('movements.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
