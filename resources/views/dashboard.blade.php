@extends('layouts.app')

@section('title', 'Panel de control')
@section('breadcrumb', 'Panel de control')

@section('content')
    <x-page-header
        title="Panel de control"
        description="Resumen operativo de {{ formato_fecha_larga(now()) }}."
    >
        <a href="{{ route('operaciones.compras.create') }}" class="btn btn-secondary">
            <x-icon name="truck" class="h-4 w-4" />
            Registrar compra
        </a>
        <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nueva venta
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card
            label="Ventas de hoy"
            :value="formato_moneda($ventasHoy)"
            hint="Ingresos registrados en el día"
            icon="money"
            tone="brand"
        />
        <x-stat-card
            label="Ventas del mes"
            :value="formato_moneda($ventasMes)"
            :hint="'Compras del mes: '.formato_moneda($comprasMes)"
            icon="chart"
            tone="sky"
        />
        <x-stat-card
            label="Productos en catálogo"
            :value="$totalProductos"
            :hint="$unidadesInventario.' unidades en inventario'"
            icon="box"
            tone="slate"
        />
        <x-stat-card
            label="Alertas de stock"
            :value="$stockBajo->count()"
            :hint="pluralizar($agotados, 'producto agotado', 'productos agotados')"
            icon="alert"
            tone="amber"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="min-w-0 space-y-6 xl:col-span-2">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Ventas de los últimos 7 días</h2>
                        <p class="text-sm text-slate-500">Comportamiento diario de los ingresos</p>
                    </div>
                    <x-badge tone="brand">Total: {{ formato_moneda($serie->sum('total')) }}</x-badge>
                </div>
                <div class="card-body">
                    <div class="flex h-52 items-end gap-2 sm:gap-4">
                        @foreach ($serie as $punto)
                            <div class="group flex flex-1 flex-col items-center gap-2">
                                <div class="flex h-40 w-full items-end">
                                    <div
                                        class="relative w-full rounded-t-lg bg-brand-500/80 transition hover:bg-brand-600"
                                        style="height: {{ max(4, round($punto['total'] / $maxSerie * 100)) }}%"
                                        title="{{ formato_fecha($punto['fecha']) }}: {{ formato_moneda($punto['total']) }}"
                                    >
                                        <span class="absolute -top-5 left-1/2 hidden -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-slate-600 group-hover:block">
                                            {{ formato_moneda($punto['total']) }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-medium uppercase tracking-wide text-slate-400">{{ $punto['etiqueta'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Últimas ventas</h2>
                        <p class="text-sm text-slate-500">Movimientos recientes del punto de venta</p>
                    </div>
                    <a href="{{ route('operaciones.ventas.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">
                        Ver todas
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th class="th">Venta</th>
                                <th class="th">Cliente</th>
                                <th class="th hidden md:table-cell">Cajero</th>
                                <th class="th hidden lg:table-cell">Fecha</th>
                                <th class="th text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ultimasVentas as $venta)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="td font-semibold text-slate-900">
                                        <a href="{{ route('operaciones.ventas.show', $venta) }}" class="hover:text-brand-700">
                                            #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="td">{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
                                    <td class="td hidden md:table-cell">{{ $venta->usuario->nombre ?? '—' }}</td>
                                    <td class="td hidden text-slate-500 lg:table-cell">{{ formato_fecha_hora($venta->fecha) }}</td>
                                    <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state icon="cart" title="Todavía no hay ventas" description="Registra la primera venta desde el botón Nueva venta." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="min-w-0 space-y-6">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Stock bajo</h2>
                        <p class="text-sm text-slate-500">Productos con 10 unidades o menos</p>
                    </div>
                    <a href="{{ route('reportes.inventario') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">
                        Ver reporte
                    </a>
                </div>
                <div class="divide-y divide-slate-100 px-5">
                    @forelse ($stockBajo as $producto)
                        <div class="flex items-center gap-3 py-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-slate-100 text-[11px] font-bold text-slate-500">
                                {{ iniciales($producto->nombre) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $producto->nombre }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $producto->categoria->nombre }}</p>
                            </div>
                            <x-badge :tone="$producto->stock_actual === 0 ? 'rose' : 'amber'">
                                {{ $producto->stock_actual }} u.
                            </x-badge>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-500">Todo el inventario está por encima del mínimo.</p>
                    @endforelse
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="text-base font-semibold text-slate-900">Más vendidos</h2>
                </div>
                <div class="divide-y divide-slate-100 px-5">
                    @forelse ($topProductos as $producto)
                        <div class="flex items-center justify-between gap-3 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $producto->nombre }}</p>
                                <p class="text-xs text-slate-500">{{ $producto->unidades }} unidades</p>
                            </div>
                            <span class="text-sm font-semibold text-slate-900">{{ formato_moneda($producto->ingresos) }}</span>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-500">Aún no hay productos vendidos.</p>
                    @endforelse
                </div>
            </div>

            <div class="card bg-slate-950 p-5 text-white">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white/10">
                        <x-icon name="clipboard" class="h-5 w-5 text-brand-300" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Valor del inventario</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight">{{ formato_moneda($valorInventario) }}</p>
                        <p class="mt-1 text-xs text-slate-400">Calculado sobre el stock actual y el precio de venta.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
