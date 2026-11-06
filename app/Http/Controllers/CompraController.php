<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(Request $request): View
    {
        $query = Compra::query()
            ->when($request->filled('proveedor_id'), function ($query) use ($request): void {
                $query->where('proveedor_id', $request->integer('proveedor_id'));
            })
            ->when($request->filled('desde'), function ($query) use ($request): void {
                $query->whereDate('fecha', '>=', $request->input('desde'));
            })
            ->when($request->filled('hasta'), function ($query) use ($request): void {
                $query->whereDate('fecha', '<=', $request->input('hasta'));
            });

        $resumen = [
            'cantidad' => (clone $query)->count(),
            'total' => (float) (clone $query)->sum('total'),
        ];

        $compras = $query->with('proveedor')
            ->withCount('detalles')
            ->latest('fecha')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('operaciones.compras.index', compact('compras', 'proveedores', 'resumen'));
    }

    public function create(): View
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos = Producto::with('categoria')->orderBy('nombre')->get();

        return view('operaciones.compras.create', compact('proveedores', 'productos'));
    }

    public function store(StoreCompraRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $compra = DB::transaction(function () use ($datos): Compra {
            $compra = Compra::create([
                'proveedor_id' => $datos['proveedor_id'],
                'fecha' => $datos['fecha'],
                'total' => 0,
            ]);

            $total = 0;

            foreach ($datos['detalles'] as $linea) {
                $subtotal = (int) $linea['cantidad'] * (float) $linea['precio_unitario'];
                $total += $subtotal;

                $compra->detalles()->create([
                    'producto_id' => $linea['producto_id'],
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                ]);

                Producto::whereKey($linea['producto_id'])
                    ->increment('stock_actual', $linea['cantidad']);
            }

            $compra->update(['total' => $total]);

            return $compra;
        });

        return redirect()
            ->route('operaciones.compras.show', $compra)
            ->with('success', 'Compra registrada y stock actualizado correctamente.');
    }

    public function show(Compra $compra): View
    {
        $compra->load(['proveedor', 'detalles.producto']);

        return view('operaciones.compras.show', compact('compra'));
    }

    public function destroy(Compra $compra): RedirectResponse
    {
        DB::transaction(function () use ($compra): void {
            $compra->load('detalles.producto');

            foreach ($compra->detalles as $detalle) {
                if ($detalle->producto->stock_actual < $detalle->cantidad) {
                    throw ValidationException::withMessages([
                        'compra' => "No se puede eliminar la compra: el stock actual de {$detalle->producto->nombre} "
                            ."es menor a las {$detalle->cantidad} unidades que ingresaron.",
                    ]);
                }
            }

            foreach ($compra->detalles as $detalle) {
                $detalle->producto->decrement('stock_actual', $detalle->cantidad);
            }

            $compra->delete();
        });

        return redirect()
            ->route('operaciones.compras.index')
            ->with('success', 'Compra eliminada y stock revertido correctamente.');
    }
}
