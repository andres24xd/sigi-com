@extends('layouts.app')

@section('title', 'Panel de cajero')
@section('breadcrumb', 'Panel de cajero')

@section('content')
    <x-page-header
        title="Panel de cajero"
        description="Hola, {{ $usuario->nombre }}. Este es tu resumen de caja para {{ formato_fecha_larga(now()) }}."
    >
        <a href="{{ route('terceros.clientes.create') }}" class="btn btn-secondary">
            <x-icon name="users" class="h-4 w-4" />
            Nuevo cliente
        </a>
        <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nueva venta
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card
            label="Ventas de hoy"
            :value="$resumen['cantidad']"
            :hint="pluralizar($resumen['cantidad'], 'comprobante emitido', 'comprobantes emitidos')"
            icon="receipt"
            tone="brand"
        />
        <x-stat-card
            label="Recaudado hoy"
            :value="formato_moneda($resumen['total'])"
            hint="Total de tus ventas del día"
            icon="money"
            tone="sky"
        />
        <x-stat-card
            label="Unidades vendidas"
            :value="$resumen['unidades']"
            hint="Artículos despachados hoy"
            icon="box"
            tone="slate"
        />
        <x-stat-card
            label="Ventas del mes"
            :value="formato_moneda($resumen['mes'])"
            hint="Acumulado del mes en curso"
            icon="chart"
            tone="amber"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="min-w-0 space-y-6 xl:col-span-2">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Mis últimas ventas</h2>
                        <p class="text-sm text-slate-500">Comprobantes que registraste recientemente</p>
                    </div>
                    <a href="{{ route('operaciones.ventas.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">
                        Ver todas
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70">
                            <tr>
                                <th class="th">Venta</th>
                                <th class="th">Cliente</th>
                                <th class="th hidden sm:table-cell">Fecha</th>
                                <th class="th text-center">Ítems</th>
                                <th class="th text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ultimasVentas as $venta)
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="td">
                                        <a href="{{ route('operaciones.ventas.show', $venta) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                            #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td class="td">{{ $venta->cliente?->nombre ?? 'Consumidor final' }}</td>
                                    <td class="td hidden text-slate-500 sm:table-cell">{{ formato_fecha_hora($venta->fecha) }}</td>
                                    <td class="td text-center">{{ $venta->detalles_count }}</td>
                                    <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state
                                            icon="cart"
                                            title="Todavía no registraste ventas"
                                            description="Cuando cobres la primera venta aparecerá aquí."
                                        >
                                            <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">Nueva venta</a>
                                        </x-empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-start gap-3 text-sm text-slate-600">
                    <x-icon name="clipboard" class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                    <p>
                        Tu rol permite registrar ventas, consultar productos y administrar clientes.
                        Las compras, los proveedores y los reportes generales los gestiona el administrador.
                    </p>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Accesos rápidos</h2>
                        <p class="text-sm text-slate-500">Lo más usado en caja</p>
                    </div>
                </div>
                <div class="card-body space-y-2">
                    <a href="{{ route('operaciones.ventas.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-800">
                        <x-icon name="cart" class="h-5 w-5 text-brand-600" />
                        Registrar una venta
                        <x-icon name="arrow-right" class="ml-auto h-4 w-4 text-slate-400" />
                    </a>
                    <a href="{{ route('inventario.productos.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-800">
                        <x-icon name="search" class="h-5 w-5 text-slate-500" />
                        Consultar productos
                        <x-icon name="arrow-right" class="ml-auto h-4 w-4 text-slate-400" />
                    </a>
                    <a href="{{ route('terceros.clientes.create') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-800">
                        <x-icon name="users" class="h-5 w-5 text-slate-500" />
                        Registrar un cliente
                        <x-icon name="arrow-right" class="ml-auto h-4 w-4 text-slate-400" />
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Stock bajo</h2>
                        <p class="text-sm text-slate-500">Avisa al administrador</p>
                    </div>
                    <x-badge tone="amber">{{ $stockBajo->count() }}</x-badge>
                </div>

                <div class="card-body space-y-3">
                    @forelse ($stockBajo as $producto)
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $producto->nombre }}</p>
                                <p class="text-xs text-slate-400">{{ $producto->categoria->nombre }}</p>
                            </div>
                            <x-badge tone="amber">{{ $producto->stock_actual }} u.</x-badge>
                        </div>
                    @empty
                        <p class="py-3 text-center text-sm text-slate-500">Sin alertas de stock por ahora.</p>
                    @endforelse

                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Productos disponibles</span>
                            <span class="font-semibold text-slate-900">{{ $productosDisponibles }}</span>
                        </div>
                        <div class="mt-1 flex items-center justify-between text-sm">
                            <span class="text-slate-500">Agotados</span>
                            <span class="font-semibold {{ $agotados > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $agotados }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
