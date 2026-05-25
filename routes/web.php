<?php

use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Productos — protección por permiso vía #[Middleware] en el controller
    Route::resource('products', ProductController::class);

    // Movimientos
    Route::resource('movements', MovementController::class)->only(['index', 'create', 'store']);
    Route::post('movements/{movement}/approve', [MovementController::class, 'approve'])
        ->name('movements.approve');

    // Gestión de roles — solo admin con permiso 'gestionar roles'
    Route::resource('roles', RoleController::class);
});

require __DIR__.'/auth.php';
