<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Roles</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Roles del sistema (guard web)</h3>
            <a href="{{ route('roles.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                + Nuevo Rol
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permisos asignados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($roles as $role)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $role->name }}
                            @if($role->name === 'admin')
                                <span class="ml-1 px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded">crítico</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $role->permissions->pluck('name')->join(', ') ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm flex gap-2">
                            <a href="{{ route('roles.edit', $role) }}"
                               class="text-yellow-600 hover:underline">Editar</a>

                            @if($role->name !== 'admin')
                                <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                      onsubmit="return confirm('¿Eliminar rol {{ $role->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
