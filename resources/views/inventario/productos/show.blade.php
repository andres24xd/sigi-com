@extends('layouts.app')

@section('title', 'Detalle de producto')
@section('breadcrumb', 'Inventario / Productos / Detalle')

@section('content')
    <x-page-header
        title="{{ $producto->nombre }}"
        :description="'Categoría: '.$producto->categoria->nombre.'. Código interno #'.str_pad($producto->id, 3, '0', STR_PAD_LEFT).'.'"
    >
        <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
        <a href="{{ route('inventario.productos.edit', $producto) }}" class="btn btn-secondary">
            <x-icon name="pencil" class="h-4 w-4" />
            Editar
        </a>
        <form action="{{ route('inventario.productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Eliminar el producto {{ $producto->nombre }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <x-icon name="trash" class="h-4 w-4" />
                Eliminar
            </button>
        </form>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card
            label="Precio de venta"
            :value="formato_moneda($producto->precio_venta)"
            hint="Valor unitario en el catálogo"
            icon="money"
            tone="brand"
        />
        <x-stat-card
            label="Stock actual"
            :value="$producto->stock_actual.' u.'"
            hint="{{ $producto->stock_actual === 0 ? 'Producto agotado' : ($producto->stock_actual <= 10 ? 'Nivel de stock bajo' : 'Disponible para la venta') }}"
            icon="box"
            :tone="$producto->stock_actual === 0 ? 'rose' : ($producto->stock_actual <= 10 ? 'amber' : 'sky')"
        />
        <x-stat-card
            label="Valor en inventario"
            :value="formato_moneda($producto->stock_actual * $producto->precio_venta)"
            hint="Stock por precio de venta"
            icon="chart"
            tone="sky"
        />
        <x-stat-card
            label="Categoría"
            :value="$producto->categoria->nombre"
            hint="Línea de negocio"
            icon="tag"
            tone="slate"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Últimas compras</h2>
                    <p class="text-sm text-slate-500">Entradas de inventario más recientes</p>
                </div>
                <x-badge tone="sky">Compras</x-badge>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="th">Documento</th>
                            <th class="th hidden sm:table-cell">Proveedor</th>
                            <th class="th text-center">Cantidad</th>
                            <th class="th text-right">Costo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientosCompra as $detalle)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="td">
                                    <a href="{{ route('operaciones.compras.show', $detalle->compra) }}" class="font-semibold text-brand-700 hover:underline">
                                        Compra #{{ str_pad($detalle->compra->id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <p class="text-xs text-slate-400">{{ formato_fecha($detalle->compra->fecha) }}</p>
                                </td>
                                <td class="td hidden text-slate-600 sm:table-cell">{{ $detalle->compra->proveedor->nombre }}</td>
                                <td class="td text-center">{{ $detalle->cantidad }}</td>
                                <td class="td text-right font-medium text-slate-900">{{ formato_moneda($detalle->precio_unitario) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state
                                        icon="truck"
                                        title="Sin compras registradas"
                                        description="Este producto todavía no tiene entradas de inventario."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Últimas ventas</h2>
                    <p class="text-sm text-slate-500">Salidas de inventario más recientes</p>
                </div>
                <x-badge tone="brand">Ventas</x-badge>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="th">Documento</th>
                            <th class="th hidden sm:table-cell">Cliente</th>
                            <th class="th text-center">Cantidad</th>
                            <th class="th text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movimientosVenta as $detalle)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="td">
                                    <a href="{{ route('operaciones.ventas.show', $detalle->venta) }}" class="font-semibold text-brand-700 hover:underline">
                                        Venta #{{ str_pad($detalle->venta->id, 4, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <p class="text-xs text-slate-400">{{ formato_fecha_hora($detalle->venta->fecha) }}</p>
                                </td>
                                <td class="td hidden text-slate-600 sm:table-cell">{{ $detalle->venta->cliente?->nombre ?? 'Consumidor final' }}</td>
                                <td class="td text-center">{{ $detalle->cantidad }}</td>
                                <td class="td text-right font-medium text-slate-900">{{ formato_moneda($detalle->precio_unitario) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-empty-state
                                        icon="cart"
                                        title="Sin ventas registradas"
                                        description="Este producto todavía no tiene salidas de inventario."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        <div class="flex items-start gap-2">
            <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
            <p>El stock se recalcula automáticamente con cada compra y venta. Solo ajusta el stock desde la edición del producto cuando corrijas un inventario físico.</p>
        </div>
    </div>
@endsection
