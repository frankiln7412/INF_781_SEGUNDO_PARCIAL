<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Rol</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nombre del rol</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permisos</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($permissions as $permission)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       @checked(in_array($permission->name, old('permissions', [])))>
                                {{ $permission->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Crear
                    </button>
                    <a href="{{ route('roles.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
