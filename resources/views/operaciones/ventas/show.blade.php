@extends('layouts.app')

@section('title', 'Detalle de venta')
@section('breadcrumb', 'Operaciones / Ventas / Detalle')

@section('content')
    <x-page-header
        title="Venta #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}"
        :description="'Registrada el '.formato_fecha_hora($venta->fecha).' por '.($venta->usuario->nombre ?? 'un cajero').'.'"
    >
        <a href="{{ route('operaciones.ventas.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
        <form action="{{ route('operaciones.ventas.destroy', $venta) }}" method="POST" onsubmit="return confirm('¿Eliminar esta venta? Se restaurará el stock vendido.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <x-icon name="trash" class="h-4 w-4" />
                Eliminar
            </button>
        </form>
    </x-page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="card-header">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Productos vendidos</h2>
                    <p class="text-sm text-slate-500">{{ pluralizar($venta->detalles->count(), 'línea de detalle', 'líneas de detalle') }}</p>
                </div>
                <x-badge tone="brand">Salida de inventario</x-badge>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="th">Producto</th>
                            <th class="th">Categoría</th>
                            <th class="th text-center">Cantidad</th>
                            <th class="th text-right">Precio unitario</th>
                            <th class="th text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($venta->detalles as $detalle)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="td font-medium text-slate-900">{{ $detalle->producto->nombre }}</td>
                                <td class="td">
                                    <x-badge tone="slate">{{ $detalle->producto->categoria->nombre }}</x-badge>
                                </td>
                                <td class="td text-center">{{ $detalle->cantidad }}</td>
                                <td class="td text-right">{{ formato_moneda($detalle->precio_unitario) }}</td>
                                <td class="td text-right font-semibold text-slate-900">
                                    {{ formato_moneda($detalle->cantidad * $detalle->precio_unitario) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="4" class="td text-right font-semibold text-slate-600">Total de la venta</td>
                            <td class="td text-right text-base font-bold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-base font-semibold text-slate-900">Resumen</h2>
                </div>
                <div class="card-body space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-slate-500">Cliente</span>
                        <span class="text-right font-semibold text-slate-900">
                            {{ $venta->cliente->nombre ?? 'Consumidor final' }}
                        </span>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-slate-500">Teléfono</span>
                        <span class="text-right font-medium text-slate-800">{{ $venta->cliente->telefono ?? '—' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-slate-500">Cajero</span>
                        <span class="text-right font-medium text-slate-800">{{ $venta->usuario->nombre ?? '—' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-slate-500">Fecha</span>
                        <span class="text-right font-medium text-slate-800">{{ formato_fecha_hora($venta->fecha) }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <span class="text-slate-500">Unidades</span>
                        <span class="text-right font-medium text-slate-800">{{ $venta->detalles->sum('cantidad') }}</span>
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-700">Total</span>
                            <span class="text-xl font-bold text-slate-900">{{ formato_moneda($venta->total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-start gap-2 text-sm text-slate-600">
                    <x-icon name="box" class="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                    <p>Esta venta descontó {{ $venta->detalles->sum('cantidad') }} unidades del inventario. Si se elimina, el stock se restaurará.</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
