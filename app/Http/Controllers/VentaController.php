<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Venta::query()
            ->when($request->filled('cliente_id'), function ($query) use ($request): void {
                $query->where('cliente_id', $request->integer('cliente_id'));
            })
            ->when($request->filled('usuario_id'), function ($query) use ($request): void {
                $query->where('usuario_id', $request->integer('usuario_id'));
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
            'unidades' => (int) DB::table('detalles_ventas')
                ->whereIn('venta_id', (clone $query)->select('id'))
                ->sum('cantidad'),
        ];

        $ventas = $query->with(['cliente', 'usuario'])
            ->withCount('detalles')
            ->latest('fecha')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('nombre')->get();

        return view('operaciones.ventas.index', compact('ventas', 'clientes', 'usuarios', 'resumen'));
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::with('categoria')->orderBy('nombre')->get();

        return view('operaciones.ventas.create', compact('clientes', 'productos'));
    }

    public function store(StoreVentaRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $venta = DB::transaction(function () use ($datos): Venta {
            $productos = Producto::whereIn('id', collect($datos['detalles'])->pluck('producto_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lineas = [];
            $total = 0;

            foreach ($datos['detalles'] as $linea) {
                $producto = $productos->get($linea['producto_id']);

                if ($producto === null) {
                    throw ValidationException::withMessages([
                        'detalles' => 'Uno de los productos seleccionados ya no existe.',
                    ]);
                }

                if ($producto->stock_actual < $linea['cantidad']) {
                    throw ValidationException::withMessages([
                        'detalles' => "Stock insuficiente para {$producto->nombre}. "
                            ."Disponible: {$producto->stock_actual} unidades.",
                    ]);
                }

                $precio = (float) $producto->precio_venta;
                $total += $linea['cantidad'] * $precio;

                $lineas[] = [
                    'producto' => $producto,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $precio,
                ];
            }

            $usuarioId = auth()->id() ?? User::query()->orderBy('id')->value('id');

            if ($usuarioId === null) {
                throw ValidationException::withMessages([
                    'general' => 'No hay un usuario registrado para asociar la venta.',
                ]);
            }

            $venta = Venta::create([
                'cliente_id' => $datos['cliente_id'] ?? null,
                'usuario_id' => $usuarioId,
                'fecha' => $datos['fecha'],
                'total' => $total,
            ]);

            foreach ($lineas as $linea) {
                $venta->detalles()->create([
                    'producto_id' => $linea['producto']->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['precio_unitario'],
                ]);

                $linea['producto']->decrement('stock_actual', $linea['cantidad']);
            }

            return $venta;
        });

        return redirect()
            ->route('operaciones.ventas.show', $venta)
            ->with('success', 'Venta registrada y stock descontado correctamente.');
    }

    public function show(Venta $venta): View
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);

        return view('operaciones.ventas.show', compact('venta'));
    }

    public function destroy(Venta $venta): RedirectResponse
    {
        DB::transaction(function () use ($venta): void {
            $venta->load('detalles');

            foreach ($venta->detalles as $detalle) {
                Producto::whereKey($detalle->producto_id)
                    ->increment('stock_actual', $detalle->cantidad);
            }

            $venta->delete();
        });

        return redirect()
            ->route('operaciones.ventas.index')
            ->with('success', 'Venta eliminada y stock restaurado correctamente.');
    }
}
