<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Editar Rol: <span class="text-purple-700">{{ $role->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Guard: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">web</code></p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

        @if($role->name === 'admin')
            <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold">Rol protegido</p>
                    <p class="text-xs mt-0.5">El nombre del rol <strong>admin</strong> no puede modificarse. Solo puedes ajustar sus permisos.</p>
                </div>
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Modificar rol</span>
            </div>
            <form method="POST" action="{{ route('roles.update', $role) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del rol <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                           @if($role->name === 'admin') readonly @endif
                           class="block w-full px-3 py-2 border rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400
                                  {{ $role->name === 'admin' ? 'bg-gray-100 text-gray-500 cursor-not-allowed border-gray-200' : ($errors->has('name') ? 'border-red-400' : 'border-gray-300') }}">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Permisos del guard <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">web</code>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($permissions as $permission)
                            @php $checked = in_array($permission->name, $rolePermissions); @endphp
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors
                                          {{ $checked ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200' }}">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400"
                                       @checked($checked)>
                                <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                @if($checked)
                                    <svg class="w-3.5 h-3.5 text-yellow-500 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-yellow-600 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar cambios
                    </button>
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
