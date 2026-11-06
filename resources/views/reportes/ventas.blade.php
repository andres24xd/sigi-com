@extends('layouts.app')

@section('title', 'Reporte de ventas')
@section('breadcrumb', 'Análisis / Reportes / Ventas')

@section('content')
    <x-page-header
        title="Reporte de ventas"
        :description="'Movimientos del '.formato_fecha($desde).' al '.formato_fecha($hasta).'.'"
    />

    @include('reportes._tabs')

    <form method="GET" action="{{ route('reportes.ventas') }}" class="card mb-6 p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-2">
                <label class="label" for="desde">Desde</label>
                <input type="date" id="desde" name="desde" value="{{ $desde }}" class="input">
            </div>
            <div class="lg:col-span-2">
                <label class="label" for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ $hasta }}" class="input">
            </div>
            <div class="lg:col-span-3">
                <label class="label" for="usuario_id">Cajero</label>
                <select id="usuario_id" name="usuario_id" class="input">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected((string) request('usuario_id') === (string) $usuario->id)>{{ $usuario->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="label" for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" class="input">
                    <option value="">Todos</option>
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}" @selected((string) request('cliente_id') === (string) $cliente->id)>{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 lg:col-span-2">
                <button type="submit" class="btn btn-primary flex-1">Aplicar</button>
                <a href="{{ route('reportes.ventas') }}" class="btn btn-secondary" title="Restablecer">
                    <x-icon name="refresh" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total vendido" :value="formato_moneda($resumen['total'])" icon="money" tone="brand" />
        <x-stat-card label="Comprobantes" :value="$resumen['cantidad']" hint="Ventas en el rango" icon="receipt" tone="sky" />
        <x-stat-card label="Unidades" :value="$resumen['unidades']" hint="Artículos despachados" icon="box" tone="slate" />
        <x-stat-card
            label="Ticket promedio"
            :value="formato_moneda($resumen['cantidad'] > 0 ? $resumen['total'] / $resumen['cantidad'] : 0)"
            icon="chart"
            tone="amber"
        />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ventas por día</h2>
                <p class="text-sm text-slate-500">Distribución diaria en el rango seleccionado</p>
            </div>
            <x-badge tone="brand">{{ pluralizar($porDia->count(), 'día con ventas', 'días con ventas') }}</x-badge>
        </div>
        <div class="card-body">
            @if ($porDia->isEmpty())
                <p class="py-8 text-center text-sm text-slate-500">No hay ventas en el rango seleccionado.</p>
            @else
                <div class="overflow-x-auto">
                    <div class="flex h-44 min-w-[520px] items-end gap-1.5">
                        @foreach ($porDia as $dia)
                            <div class="group flex flex-1 flex-col items-center gap-1.5">
                                <div class="flex h-36 w-full items-end">
                                    <div
                                        class="relative w-full rounded-t-md bg-brand-500/80 transition hover:bg-brand-600"
                                        style="height: {{ max(4, round($dia->total / $maxDia * 100)) }}%"
                                        title="{{ formato_fecha($dia->dia) }}: {{ formato_moneda($dia->total) }}"
                                    >
                                        <span class="absolute -top-5 left-1/2 hidden -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-slate-600 group-hover:block">
                                            {{ formato_moneda($dia->total) }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($dia->dia)->format('d/m') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <h2 class="text-base font-semibold text-slate-900">Detalle de ventas</h2>
            <p class="text-sm text-slate-500">{{ $ventas->total() }} resultados</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Venta</th>
                        <th class="th">Fecha</th>
                        <th class="th">Cliente</th>
                        <th class="th">Cajero</th>
                        <th class="th text-center">Ítems</th>
                        <th class="th text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ventas as $venta)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <a href="{{ route('operaciones.ventas.show', $venta) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                    #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="td text-slate-600">{{ formato_fecha_hora($venta->fecha) }}</td>
                            <td class="td">{{ $venta->cliente->nombre ?? 'Consumidor final' }}</td>
                            <td class="td text-slate-600">{{ $venta->usuario->nombre ?? '—' }}</td>
                            <td class="td text-center">{{ $venta->detalles_count }}</td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="chart" title="Sin ventas en el rango" description="Prueba con otras fechas o registra una nueva venta." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ventas->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $ventas->links() }}
            </div>
        @endif
    </div>
@endsection
