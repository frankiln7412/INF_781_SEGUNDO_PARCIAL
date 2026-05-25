<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Movimientos de Inventario</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Lista de Movimientos</h3>
            {{-- Botón registrar: solo quien tenga permiso 'registrar movimiento' --}}
            @can('registrar movimiento')
                <a href="{{ route('movements.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Registrar Movimiento
                </a>
            @endcan
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Almacén</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aprobado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    <tr>
                        <td class="px-4 py-3 text-sm">{{ $movement->id }}</td>
                        <td class="px-4 py-3 text-sm capitalize">{{ $movement->type }}</td>
                        <td class="px-4 py-3 text-sm">{{ $movement->product->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $movement->quantity }}</td>
                        <td class="px-4 py-3 text-sm">{{ $movement->warehouse_id }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($movement->approved)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Sí</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">No</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{-- Botón Aprobar: solo quien tenga 'aprobar movimiento' y el movimiento no esté aprobado --}}
                            @can('approve', $movement)
                                @if(! $movement->approved)
                                    <form method="POST" action="{{ route('movements.approve', $movement) }}">
                                        @csrf
                                        <button class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                            Aprobar
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">No hay movimientos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movements->links() }}</div>
    </div>
</x-app-layout>
