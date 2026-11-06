@extends('layouts.app')

@section('title', 'Compras')
@section('breadcrumb', 'Operaciones / Compras')

@section('content')
    <x-page-header
        title="Compras"
        description="Registro de adquisiciones a proveedores y entradas de mercancía al inventario."
    >
        <a href="{{ route('operaciones.compras.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Registrar compra
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-stat-card label="Compras filtradas" :value="$resumen['cantidad']" hint="Órdenes de compra encontradas" icon="truck" tone="sky" />
        <x-stat-card label="Valor total" :value="formato_moneda($resumen['total'])" hint="Suma de las compras filtradas" icon="money" tone="brand" />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <form method="GET" action="{{ route('operaciones.compras.index') }}" class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <select name="proveedor_id" class="input">
                        <option value="">Todos los proveedores</option>
                        @foreach ($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" @selected((string) request('proveedor_id') === (string) $proveedor->id)>
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <input type="date" name="desde" value="{{ request('desde') }}" class="input" title="Desde">
                </div>
                <div class="lg:col-span-3">
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="input" title="Hasta">
                </div>
                <div class="flex gap-2 lg:col-span-2">
                    <button type="submit" class="btn btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['proveedor_id', 'desde', 'hasta']))
                        <a href="{{ route('operaciones.compras.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Compra</th>
                        <th class="th">Proveedor</th>
                        <th class="th hidden md:table-cell">Fecha</th>
                        <th class="th hidden text-center sm:table-cell">Ítems</th>
                        <th class="th text-right">Total</th>
                        <th class="th hidden text-right sm:table-cell">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($compras as $compra)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <a href="{{ route('operaciones.compras.show', $compra) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                    #{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="td">
                                <p class="font-medium text-slate-800">{{ $compra->proveedor->nombre }}</p>
                                <p class="text-xs text-slate-400">{{ $compra->proveedor->telefono ?: 'Sin teléfono' }}</p>
                            </td>
                            <td class="td hidden text-slate-600 md:table-cell">{{ formato_fecha($compra->fecha) }}</td>
                            <td class="td hidden text-center sm:table-cell">
                                <x-badge tone="slate">{{ pluralizar($compra->detalles_count, 'producto', 'productos') }}</x-badge>
                            </td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($compra->total) }}</td>
                            <td class="td hidden sm:table-cell">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('operaciones.compras.show', $compra) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('operaciones.compras.destroy', $compra) }}" method="POST" onsubmit="return confirm('¿Eliminar la compra #{{ $compra->id }}? Se revertirá el stock ingresado.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Eliminar">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    icon="truck"
                                    title="No hay compras registradas"
                                    description="Registra la primera compra para ingresar mercancía al inventario."
                                >
                                    <a href="{{ route('operaciones.compras.create') }}" class="btn btn-primary">Registrar compra</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($compras->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $compras->links() }}
            </div>
        @endif
    </div>
@endsection
