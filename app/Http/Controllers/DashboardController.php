<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        if (! $usuario->esAdministrador()) {
            return $this->panelCajero($usuario);
        }

        $hoy = now();

        $ventasHoy = (float) Venta::whereDate('fecha', $hoy->toDateString())->sum('total');
        $ventasMes = (float) Venta::whereYear('fecha', $hoy->year)
            ->whereMonth('fecha', $hoy->month)
            ->sum('total');
        $comprasMes = (float) Compra::whereYear('fecha', $hoy->year)
            ->whereMonth('fecha', $hoy->month)
            ->sum('total');

        $totalProductos = Producto::count();
        $unidadesInventario = (int) Producto::sum('stock_actual');
        $valorInventario = (float) Producto::query()
            ->selectRaw('COALESCE(SUM(stock_actual * precio_venta), 0) as total')
            ->value('total');

        $stockBajo = Producto::with('categoria')
            ->where('stock_actual', '<=', 10)
            ->orderBy('stock_actual')
            ->take(6)
            ->get();

        $agotados = Producto::where('stock_actual', 0)->count();

        $ultimasVentas = Venta::with(['cliente', 'usuario'])
            ->latest('fecha')
            ->latest('id')
            ->take(6)
            ->get();

        $ultimasCompras = Compra::with('proveedor')
            ->latest('fecha')
            ->latest('id')
            ->take(4)
            ->get();

        $serie = collect(range(6, 0))->map(function (int $dias) use ($hoy): array {
            $fecha = $hoy->copy()->subDays($dias);

            return [
                'etiqueta' => $fecha->locale('es')->isoFormat('ddd'),
                'fecha' => $fecha->toDateString(),
                'total' => (float) Venta::whereDate('fecha', $fecha->toDateString())->sum('total'),
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
            ->orderByDesc('unidades')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'ventasHoy',
            'ventasMes',
            'comprasMes',
            'totalProductos',
            'unidadesInventario',
            'valorInventario',
            'stockBajo',
            'agotados',
            'ultimasVentas',
            'ultimasCompras',
            'serie',
            'maxSerie',
            'topProductos',
        ));
    }

    /**
     * Panel enfocado en el punto de venta para el rol Cajero.
     */
    private function panelCajero(User $usuario): View
    {
        $hoy = now();

        $ventasHoy = Venta::where('usuario_id', $usuario->id)
            ->whereDate('fecha', $hoy->toDateString());

        $resumen = [
            'cantidad' => (clone $ventasHoy)->count(),
            'total' => (float) (clone $ventasHoy)->sum('total'),
            'unidades' => (int) DB::table('detalles_ventas')
                ->whereIn('venta_id', (clone $ventasHoy)->select('id'))
                ->sum('cantidad'),
            'mes' => (float) Venta::where('usuario_id', $usuario->id)
                ->whereYear('fecha', $hoy->year)
                ->whereMonth('fecha', $hoy->month)
                ->sum('total'),
        ];

        $productosDisponibles = Producto::where('stock_actual', '>', 0)->count();
        $agotados = Producto::where('stock_actual', 0)->count();

        $stockBajo = Producto::with('categoria')
            ->whereBetween('stock_actual', [1, 10])
            ->orderBy('stock_actual')
            ->take(5)
            ->get();

        $ultimasVentas = Venta::where('usuario_id', $usuario->id)
            ->with('cliente')
            ->withCount('detalles')
            ->latest('fecha')
            ->latest('id')
            ->take(6)
            ->get();

        return view('dashboard-cajero', compact(
            'usuario',
            'resumen',
            'productosDisponibles',
            'agotados',
            'stockBajo',
            'ultimasVentas',
        ));
    }
}
