@extends('layouts.app')

@section('title', 'Reportes')
@section('breadcrumb', 'Análisis / Reportes')

@section('content')
    <x-page-header
        title="Reportes e indicadores"
        description="Balance general del negocio, comportamiento de ventas y estado del inventario."
    />

    @include('reportes._tabs')

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Ventas del mes" :value="formato_moneda($ventasMes)" hint="Ingresos acumulados del mes" icon="money" tone="brand" />
        <x-stat-card label="Compras del mes" :value="formato_moneda($comprasMes)" hint="Inversión en abastecimiento" icon="truck" tone="sky" />
        <x-stat-card label="Valor del inventario" :value="formato_moneda($valorInventario)" hint="Stock por precio de venta" icon="box" tone="slate" />
        <x-stat-card label="Stock bajo" :value="$stockBajo" hint="10 unidades o menos" icon="alert" tone="amber" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="card-header">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Ventas de los últimos 6 meses</h2>
                    <p class="text-sm text-slate-500">Evolución mensual de los ingresos</p>
                </div>
                <x-badge tone="brand">{{ formato_moneda($serie->sum('total')) }}</x-badge>
            </div>
            <div class="card-body">
                <div class="flex h-56 items-end gap-3 sm:gap-5">
                    @foreach ($serie as $punto)
                        <div class="group flex flex-1 flex-col items-center gap-2">
                            <div class="flex h-44 w-full items-end">
                                <div
                                    class="relative w-full rounded-t-xl bg-brand-500/80 transition hover:bg-brand-600"
                                    style="height: {{ max(4, round($punto['total'] / $maxSerie * 100)) }}%"
                                    title="{{ $punto['etiqueta'] }}: {{ formato_moneda($punto['total']) }}"
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
                    <h2 class="text-base font-semibold text-slate-900">Ventas por categoría</h2>
                    <p class="text-sm text-slate-500">Participación en ingresos</p>
                </div>
            </div>
            <div class="card-body space-y-4">
                @php $maxCategoria = max(1, (float) $porCategoria->max('ingresos')); @endphp

                @forelse ($porCategoria as $categoria)
                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-2 text-sm">
                            <span class="font-medium text-slate-700">{{ $categoria->nombre }}</span>
                            <span class="text-slate-500">{{ formato_moneda($categoria->ingresos) }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ max(3, round($categoria->ingresos / $maxCategoria * 100)) }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">{{ $categoria->unidades }} unidades vendidas</p>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">Todavía no hay ventas por categoría.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Productos más vendidos</h2>
                <p class="text-sm text-slate-500">Ranking por ingresos generados</p>
            </div>
            <a href="{{ route('reportes.ventas') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Ver reporte de ventas</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">#</th>
                        <th class="th">Producto</th>
                        <th class="th text-center">Unidades</th>
                        <th class="th text-right">Ingresos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topProductos as $index => $producto)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td text-slate-400">{{ $index + 1 }}</td>
                            <td class="td font-medium text-slate-900">{{ $producto->nombre }}</td>
                            <td class="td text-center">{{ $producto->unidades }}</td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($producto->ingresos) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="chart" title="Sin datos de ventas" description="Cuando registres ventas verás aquí el ranking de productos." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
