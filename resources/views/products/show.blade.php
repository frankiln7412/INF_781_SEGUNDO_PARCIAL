<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">Detalle del producto</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Información del producto</span>
            </div>
            <dl class="divide-y divide-gray-50">
                <div class="px-6 py-3.5 flex justify-between">
                    <dt class="text-sm text-gray-500">ID</dt>
                    <dd class="text-sm font-mono text-gray-700">#{{ $product->id }}</dd>
                </div>
                <div class="px-6 py-3.5 flex justify-between">
                    <dt class="text-sm text-gray-500">Nombre</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $product->name }}</dd>
                </div>
                <div class="px-6 py-3.5 flex justify-between">
                    <dt class="text-sm text-gray-500">Descripción</dt>
                    <dd class="text-sm text-gray-700 text-right max-w-xs">{{ $product->description ?: '—' }}</dd>
                </div>
                <div class="px-6 py-3.5 flex justify-between">
                    <dt class="text-sm text-gray-500">Precio</dt>
                    <dd class="text-sm font-bold text-gray-900">Bs. {{ number_format($product->price, 2) }}</dd>
                </div>
                <div class="px-6 py-3.5 flex justify-between items-center">
                    <dt class="text-sm text-gray-500">Stock</dt>
                    <dd>
                        @php $s = $product->stock; @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $s === 0 ? 'bg-red-100 text-red-700' : ($s <= 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ $s }} unidades{{ $s === 0 ? ' — Sin stock' : ($s <= 5 ? ' — Stock bajo' : '') }}
                        </span>
                    </dd>
                </div>
                <div class="px-6 py-3.5 flex justify-between">
                    <dt class="text-sm text-gray-500">Almacén</dt>
                    <dd class="text-sm text-gray-700">{{ $product->warehouse_id ? 'Almacén '.$product->warehouse_id : '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Botones de acción según permisos --}}
        <div class="flex flex-wrap gap-3">
            @can('editar productos')
                <a href="{{ route('products.edit', $product) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-yellow-600 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar producto
                </a>
            @endcan

            @can('eliminar productos')
                <form method="POST" action="{{ route('products.destroy', $product) }}"
                      onsubmit="return confirm('¿Eliminar «{{ $product->name }}»? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-red-700 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar producto
                    </button>
                </form>
            @endcan

            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 active:scale-95 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al listado
            </a>
        </div>

        {{-- Aviso si no tiene permisos de edición --}}
        @cannot('editar productos')
        @cannot('eliminar productos')
        <p class="text-xs text-gray-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            Solo tienes permiso de lectura sobre este producto.
        </p>
        @endcannot
        @endcannot

    </div>
</x-app-layout>
