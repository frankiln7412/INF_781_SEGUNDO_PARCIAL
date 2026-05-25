<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * Endpoints de productos para el guard API (tokens Sanctum).
 * El permiso 'ver productos' del guard api es independiente del guard web:
 * aunque tengan el mismo nombre, son registros distintos en la BD.
 */
class ProductApiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // auth:api resuelve el usuario via Sanctum con el guard 'api'
            // permission:ver productos,api → spatie verifica el permiso en guard api
            new Middleware(['auth:api', 'permission:ver productos,api']),
        ];
    }

    /**
     * GET /api/products
     * Solo accesible con token de repartidor (guard api, permiso 'ver productos').
     * Un token de repartidor NO puede crear/eliminar productos porque esos permisos
     * solo existen en el guard web — la separación de guards lo garantiza sin lógica extra.
     */
    public function index()
    {
        return response()->json([
            'data' => Product::select('id', 'name', 'stock', 'price')->get(),
        ]);
    }
}
