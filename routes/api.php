<?php

use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — guard: api (Sanctum tokens)
|--------------------------------------------------------------------------
| Los middlewares auth:sanctum + permission se aplican dentro de cada
| controller vía HasMiddleware. El guard 'api' garantiza aislamiento:
| los permisos web (crear/eliminar productos) no existen en el guard api,
| por lo que cualquier token de repartidor recibe 403 automáticamente.
|--------------------------------------------------------------------------
*/

// GET /api/products — guard api, permiso 'ver productos'
Route::get('/products', [ProductApiController::class, 'index']);

// POST /api/deliveries/{id}/confirm — guard api, permiso 'confirmar entrega'
Route::post('/deliveries/{id}/confirm', [DeliveryController::class, 'confirm']);
