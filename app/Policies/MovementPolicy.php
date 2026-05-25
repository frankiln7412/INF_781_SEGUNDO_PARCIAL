<?php

namespace App\Policies;

use App\Models\Movement;
use App\Models\User;

class MovementPolicy
{
    /**
     * Solo quien tenga el permiso 'aprobar movimiento' puede aprobar.
     * Admin y supervisor lo tienen; almacenista no.
     */
    public function approve(User $user, Movement $movement): bool
    {
        return $user->can('aprobar movimiento');
    }

    /**
     * Registrar movimiento:
     * - Admin y supervisor pueden registrar en cualquier almacén.
     * - Almacenista solo puede registrar movimientos de SU propio almacén (warehouse_id).
     */
    public function register(User $user, Movement $movement): bool
    {
        if (! $user->can('registrar movimiento')) {
            return false;
        }

        // Admin/supervisor tienen 'aprobar movimiento', pueden registrar en cualquier almacén
        if ($user->can('aprobar movimiento')) {
            return true;
        }

        // Almacenista: solo su warehouse
        return $user->warehouse_id === $movement->warehouse_id;
    }

    /**
     * Crear movimiento nuevo: misma lógica que register pero sin movimiento existente.
     * Usamos warehouse_id del request pasado como movimiento temporal.
     */
    public function create(User $user): bool
    {
        return $user->can('registrar movimiento');
    }
}
