<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Endpoint de confirmación de entregas para el guard API.
 * Solo el rol 'repartidor' tiene el permiso 'confirmar entrega' en el guard api.
 */
class DeliveryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // auth:api + permission con guard 'api' → aislamiento total del guard web
            new Middleware(['auth:api', 'permission:confirmar entrega,api']),
        ];
    }

    /**
     * POST /api/deliveries/{id}/confirm
     * Confirma la entrega de un movimiento de tipo 'salida'.
     * Devuelve 403 automáticamente si el token no tiene el permiso 'confirmar entrega'.
     */
    public function confirm(int $id)
    {
        $movement = Movement::findOrFail($id);

        if ($movement->type !== 'salida') {
            return response()->json(['message' => 'Solo se pueden confirmar entregas de tipo salida.'], 422);
        }

        $movement->update(['approved' => true]);

        return response()->json([
            'message'    => 'Entrega confirmada correctamente.',
            'movement'   => $movement,
        ]);
    }
}
