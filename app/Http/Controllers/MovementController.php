<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MovementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:registrar movimiento', only: ['create', 'store']),
            new Middleware('permission:aprobar movimiento',   only: ['approve']),
        ];
    }

    public function index()
    {
        $movements = Movement::with(['product', 'user'])->paginate(10);
        return view('movements.index', compact('movements'));
    }

    public function create()
    {
        $this->authorize('create', Movement::class);
        $products = Product::all();
        return view('movements.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'         => 'required|in:entrada,salida',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string',
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|integer',
        ]);

        $movement = new Movement($data);
        $movement->user_id = auth()->id();

        // Verificar via Gate (policy) que el usuario puede registrar en ese almacén
        $this->authorize('register', $movement);

        $movement->save();
        return redirect()->route('movements.index')->with('success', 'Movimiento registrado.');
    }

    public function approve(Movement $movement)
    {
        // Gate verifica el permiso 'aprobar movimiento' vía MovementPolicy
        $this->authorize('approve', $movement);

        $movement->update(['approved' => true]);
        return redirect()->route('movements.index')->with('success', 'Movimiento aprobado.');
    }
}
