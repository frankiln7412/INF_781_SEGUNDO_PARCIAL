<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- User info card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-bold shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">Bienvenido, {{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach(Auth::user()->getRoleNames() as $rol)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $rol === 'admin' ? 'bg-red-100 text-red-700 ring-1 ring-red-200' :
                               ($rol === 'supervisor' ? 'bg-purple-100 text-purple-700 ring-1 ring-purple-200' :
                               ($rol === 'almacenista' ? 'bg-blue-100 text-blue-700 ring-1 ring-blue-200' :
                               'bg-green-100 text-green-700 ring-1 ring-green-200')) }}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            {{ ucfirst($rol) }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="text-right text-sm text-gray-400 hidden sm:block">
                <p>Guard activo</p>
                <p class="font-medium text-gray-600">web (sesión)</p>
            </div>
        </div>

        {{-- Acciones disponibles según permisos --}}
        <div>
            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Acciones disponibles</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                @can('ver productos')
                <a href="{{ route('products.index') }}"
                   class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-blue-300 hover:shadow-md transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-blue-700 transition-colors">Ver Productos</p>
                            <p class="text-xs text-gray-500 mt-0.5">Consultar catálogo de inventario</p>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1">
                        @can('crear productos')
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">+ Crear</span>
                        @endcan
                        @can('editar productos')
                            <span class="text-xs bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded">✎ Editar</span>
                        @endcan
                        @can('eliminar productos')
                            <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded">✕ Eliminar</span>
                        @endcan
                    </div>
                </a>
                @endcan

                @can('crear productos')
                <a href="{{ route('products.create') }}"
                   class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-green-300 hover:shadow-md transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-green-700 transition-colors">Crear Producto</p>
                            <p class="text-xs text-gray-500 mt-0.5">Agregar nuevo ítem al inventario</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('registrar movimiento')
                <a href="{{ route('movements.create') }}"
                   class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-indigo-300 hover:shadow-md transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-indigo-700 transition-colors">Registrar Movimiento</p>
                            <p class="text-xs text-gray-500 mt-0.5">Entrada o salida de mercancía</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('aprobar movimiento')
                <a href="{{ route('movements.index') }}"
                   class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-300 hover:shadow-md transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-emerald-700 transition-colors">Aprobar Movimientos</p>
                            <p class="text-xs text-gray-500 mt-0.5">Revisar y autorizar movimientos pendientes</p>
                        </div>
                    </div>
                </a>
                @endcan

                @can('gestionar roles')
                <a href="{{ route('roles.index') }}"
                   class="group bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-purple-300 hover:shadow-md transition-all">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 group-hover:text-purple-700 transition-colors">Gestionar Roles</p>
                            <p class="text-xs text-gray-500 mt-0.5">CRUD de roles y permisos del sistema</p>
                        </div>
                    </div>
                </a>
                @endcan

            </div>
        </div>

        {{-- Permisos del usuario actual --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Permisos activos en este guard (web)
            </h4>
            @php $perms = Auth::user()->getAllPermissions()->where('guard_name', 'web')->pluck('name'); @endphp
            @if($perms->isEmpty())
                <p class="text-sm text-gray-400 italic">Sin permisos asignados.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($perms as $perm)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $perm }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
