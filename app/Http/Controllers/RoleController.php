<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Solo el admin (con permiso 'gestionar roles') puede acceder
            new Middleware('permission:gestionar roles'),
        ];
    }

    public function index()
    {
        $roles = Role::where('guard_name', 'web')->with('permissions')->get();
        $permissions = Permission::where('guard_name', 'web')->get();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions ?? []);

        // Refrescar caché de Spatie para que los cambios apliquen de inmediato
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Rol '{$role->name}' creado.");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::where('guard_name', 'web')->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => "required|string|unique:roles,name,{$role->id}"]);

        // Proteger el nombre del rol admin
        if ($role->name !== 'admin') {
            $role->update(['name' => $request->name]);
        }

        $role->syncPermissions($request->permissions ?? []);

        // Refrescar caché de Spatie tras cambio de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Rol actualizado correctamente.");
    }

    public function destroy(Role $role)
    {
        // Impedir eliminar el rol crítico 'admin'
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol admin es crítico y no puede eliminarse.');
        }

        $role->delete();

        // Refrescar caché de Spatie tras eliminación
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', "Rol eliminado correctamente.");
    }
}
