<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Producto::with('categoria')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('nombre', 'like', '%'.trim((string) $request->input('q')).'%');
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request): void {
                $query->where('categoria_id', $request->integer('categoria_id'));
            })
            ->when($request->input('estado') === 'bajo', function ($query): void {
                $query->whereBetween('stock_actual', [1, 10]);
            })
            ->when($request->input('estado') === 'agotado', function ($query): void {
                $query->where('stock_actual', 0);
            });

        $productos = $query->orderBy('nombre')->paginate(10)->withQueryString();

        $resumen = [
            'total' => Producto::count(),
            'bajo' => Producto::whereBetween('stock_actual', [1, 10])->count(),
            'agotados' => Producto::where('stock_actual', 0)->count(),
            'valor' => (float) Producto::query()
                ->selectRaw('COALESCE(SUM(stock_actual * precio_venta), 0) as total')
                ->value('total'),
        ];

        $categorias = Categoria::orderBy('nombre')->get();

        return view('inventario.productos.index', compact('productos', 'resumen', 'categorias'));
    }

    public function create(): View
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('inventario.productos.create', compact('categorias'));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()
            ->route('inventario.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto): View
    {
        $categorias = Categoria::orderBy('nombre')->get();

        return view('inventario.productos.edit', compact('producto', 'categorias'));
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()
            ->route('inventario.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        if ($producto->detallesCompra()->exists() || $producto->detallesVenta()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar el producto porque tiene movimientos de compra o venta asociados.'
            );
        }

        $producto->delete();

        return redirect()
            ->route('inventario.productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
