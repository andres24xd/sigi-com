<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->user()->esAdministrador()) {
            return redirect()->route('reportes.ventas');
        }

        $hoy = now();
        $inicioMes = $hoy->copy()->startOfMonth();

        $ventasMes = (float) Venta::whereDate('fecha', '>=', $inicioMes->toDateString())
            ->whereDate('fecha', '<=', $hoy->toDateString())
            ->sum('total');

        $comprasMes = (float) Compra::whereDate('fecha', '>=', $inicioMes->toDateString())
            ->whereDate('fecha', '<=', $hoy->toDateString())
            ->sum('total');

        $valorInventario = (float) Producto::query()
            ->selectRaw('COALESCE(SUM(stock_actual * precio_venta), 0) as total')
            ->value('total');

        $stockBajo = Producto::where('stock_actual', '<=', 10)->count();

        $serie = collect(range(5, 0))->map(function (int $meses) use ($hoy): array {
            $mes = $hoy->copy()->subMonths($meses);

            return [
                'etiqueta' => ucfirst($mes->locale('es')->isoFormat('MMM')),
                'total' => (float) Venta::whereYear('fecha', $mes->year)
                    ->whereMonth('fecha', $mes->month)
                    ->sum('total'),
            ];
        });

        $maxSerie = max(1, (float) $serie->max('total'));

        $topProductos = DB::table('detalles_ventas')
            ->join('productos', 'productos.id', '=', 'detalles_ventas.producto_id')
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalles_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalles_ventas.cantidad * detalles_ventas.precio_unitario) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('ingresos')
            ->limit(6)
            ->get();

        $porCategoria = DB::table('detalles_ventas')
            ->join('productos', 'productos.id', '=', 'detalles_ventas.producto_id')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->select(
                'categorias.nombre',
                DB::raw('SUM(detalles_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalles_ventas.cantidad * detalles_ventas.precio_unitario) as ingresos')
            )
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('ingresos')
            ->get();

        return view('reportes.index', compact(
            'ventasMes',
            'comprasMes',
            'valorInventario',
            'stockBajo',
            'serie',
            'maxSerie',
            'topProductos',
            'porCategoria',
        ));
    }

    public function ventas(Request $request): View
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());
        $usuarioActual = $request->user();
        $esAdmin = $usuarioActual->esAdministrador();

        $base = Venta::query()
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when(! $esAdmin, function ($query) use ($usuarioActual): void {
                $query->where('usuario_id', $usuarioActual->id);
            })
            ->when($esAdmin && $request->filled('usuario_id'), function ($query) use ($request): void {
                $query->where('usuario_id', $request->integer('usuario_id'));
            })
            ->when($request->filled('cliente_id'), function ($query) use ($request): void {
                $query->where('cliente_id', $request->integer('cliente_id'));
            });

        $resumen = [
            'cantidad' => (clone $base)->count(),
            'total' => (float) (clone $base)->sum('total'),
            'unidades' => (int) DB::table('detalles_ventas')
                ->whereIn('venta_id', (clone $base)->select('id'))
                ->sum('cantidad'),
        ];

        $porDia = (clone $base)
            ->selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $maxDia = max(1, (float) $porDia->max('total'));

        $ventas = $base->with(['cliente', 'usuario'])
            ->withCount('detalles')
            ->latest('fecha')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = $esAdmin ? User::orderBy('nombre')->get() : collect([$usuarioActual]);

        return view('reportes.ventas', compact(
            'ventas',
            'resumen',
            'porDia',
            'maxDia',
            'desde',
            'hasta',
            'clientes',
            'usuarios',
            'esAdmin',
        ));
    }

    public function compras(Request $request): View
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = $request->input('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now()->toDateString());

        $base = Compra::query()
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when($request->filled('proveedor_id'), function ($query) use ($request): void {
                $query->where('proveedor_id', $request->integer('proveedor_id'));
            });

        $resumen = [
            'cantidad' => (clone $base)->count(),
            'total' => (float) (clone $base)->sum('total'),
        ];

        $porDia = (clone $base)
            ->selectRaw('DATE(fecha) as dia, SUM(total) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        $maxDia = max(1, (float) $porDia->max('total'));

        $compras = $base->with('proveedor')
            ->withCount('detalles')
            ->latest('fecha')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $proveedores = Proveedor::orderBy('nombre')->get();

        return view('reportes.compras', compact(
            'compras',
            'resumen',
            'porDia',
            'maxDia',
            'desde',
            'hasta',
            'proveedores',
        ));
    }

    public function inventario(Request $request): View
    {
        $request->validate([
            'estado' => ['nullable', 'in:disponible,bajo,agotado'],
            'categoria_id' => ['nullable', 'integer', 'exists:categorias,id'],
        ]);

        $query = Producto::with('categoria')
            ->when($request->input('estado') === 'bajo', function ($query): void {
                $query->whereBetween('stock_actual', [1, 10]);
            })
            ->when($request->input('estado') === 'agotado', function ($query): void {
                $query->where('stock_actual', 0);
            })
            ->when($request->input('estado') === 'disponible', function ($query): void {
                $query->where('stock_actual', '>', 10);
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request): void {
                $query->where('categoria_id', $request->integer('categoria_id'));
            });

        $productos = $query->orderBy('stock_actual')->paginate(20)->withQueryString();

        $resumen = [
            'total' => Producto::count(),
            'unidades' => (int) Producto::sum('stock_actual'),
            'valor' => (float) Producto::query()
                ->selectRaw('COALESCE(SUM(stock_actual * precio_venta), 0) as total')
                ->value('total'),
            'bajo' => Producto::where('stock_actual', '<=', 10)->count(),
        ];

        $categorias = Categoria::orderBy('nombre')->get();

        return view('reportes.inventario', compact('productos', 'resumen', 'categorias'));
    }
}
