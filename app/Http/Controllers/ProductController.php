<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    /**
     * Atributos #[Middleware] a nivel de método — NO se usa $this->middleware() en constructor.
     * role_or_permission permite que el admin entre aunque no tenga el permiso individual.
     */
    public static function middleware(): array
    {
        return [
            // index y show: cualquier rol web con permiso 'ver productos'
            new Middleware('permission:ver productos', only: ['index', 'show']),

            // create/store: solo quien tenga 'crear productos' (únicamente admin)
            new Middleware('permission:crear productos', only: ['create', 'store']),

            // edit/update: admin O quien tenga 'editar productos' (admin + supervisor)
            // Demuestra uso de role_or_permission
            new Middleware('role_or_permission:admin|editar productos', only: ['edit', 'update']),

            // destroy: solo quien tenga 'eliminar productos' (únicamente admin)
            new Middleware('permission:eliminar productos', only: ['destroy']),
        ];
    }

    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'warehouse_id' => 'nullable|integer',
        ]);

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Producto creado.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'warehouse_id' => 'nullable|integer',
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado.');
    }
}
