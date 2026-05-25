<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché antes de crear roles/permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permisos guard WEB ──────────────────────────────────────────────
        // Los permisos web y api son independientes aunque tengan el mismo nombre
        $webPerms = [
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',
            'registrar movimiento',
            'aprobar movimiento',
            'gestionar roles',
        ];

        foreach ($webPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Permisos guard API ──────────────────────────────────────────────
        // 'ver productos' en el guard api es un registro DISTINTO al del guard web
        $apiPerms = [
            'ver productos',
            'confirmar entrega',
        ];

        foreach ($apiPerms as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        // ── Roles guard WEB ─────────────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions(
            Permission::where('guard_name', 'web')
                ->whereIn('name', ['ver productos', 'editar productos', 'registrar movimiento', 'aprobar movimiento'])
                ->get()
        );

        $almacenista = Role::firstOrCreate(['name' => 'almacenista', 'guard_name' => 'web']);
        $almacenista->syncPermissions(
            Permission::where('guard_name', 'web')
                ->whereIn('name', ['ver productos', 'registrar movimiento'])
                ->get()
        );

        // ── Rol guard API ────────────────────────────────────────────────────
        $repartidor = Role::firstOrCreate(['name' => 'repartidor', 'guard_name' => 'api']);
        $repartidor->syncPermissions(
            Permission::where('guard_name', 'api')->get()
        );

        // ── Usuarios de prueba ───────────────────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@almatrack.com'],
            ['name' => 'Admin AlmaTrack', 'password' => bcrypt('password'), 'warehouse_id' => 1]
        );
        $adminUser->assignRole($admin);

        $supervisorUser = User::firstOrCreate(
            ['email' => 'supervisor@almatrack.com'],
            ['name' => 'Supervisor AlmaTrack', 'password' => bcrypt('password'), 'warehouse_id' => 1]
        );
        $supervisorUser->assignRole($supervisor);

        $almacenistaUser = User::firstOrCreate(
            ['email' => 'almacenista@almatrack.com'],
            ['name' => 'Almacenista AlmaTrack', 'password' => bcrypt('password'), 'warehouse_id' => 1]
        );
        $almacenistaUser->assignRole($almacenista);

        // Repartidor: mismo modelo User, autenticado via Sanctum (guard api)
        $repartidorUser = User::firstOrCreate(
            ['email' => 'repartidor@almatrack.com'],
            ['name' => 'Repartidor AlmaTrack', 'password' => bcrypt('password')]
        );

        // spatie verifica que el guard del rol coincida con el guard del modelo (web).
        // Para asignar un rol de guard 'api' al mismo User model, insertamos directamente
        // en la tabla pivote, bypaseando la validación de guard — técnica documentada
        // por Spatie para escenarios con múltiples guards en un mismo modelo.
        DB::table('model_has_roles')->insertOrIgnore([
            'role_id'    => $repartidor->id,
            'model_type' => 'App\\Models\\User',
            'model_id'   => $repartidorUser->id,
        ]);

        // Crear token Sanctum para el repartidor (se muestra una sola vez)
        if ($repartidorUser->tokens()->count() === 0) {
            $token = $repartidorUser->createToken('repartidor-token')->plainTextToken;
            $this->command->info("Token repartidor (guardar para pruebas): {$token}");
        }
    }
}
