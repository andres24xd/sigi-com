@extends('layouts.app')

@section('title', 'Ventas')
@section('breadcrumb', 'Operaciones / Ventas')

@section('content')
    <x-page-header
        title="Ventas"
        description="Historial de facturación del punto de venta y salidas de inventario."
    >
        <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nueva venta
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Ventas filtradas" :value="$resumen['cantidad']" hint="Comprobantes encontrados" icon="receipt" tone="brand" />
        <x-stat-card label="Valor total" :value="formato_moneda($resumen['total'])" hint="Suma de las ventas filtradas" icon="money" tone="sky" />
        <x-stat-card label="Unidades vendidas" :value="$resumen['unidades']" hint="Artículos despachados" icon="box" tone="slate" />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <form method="GET" action="{{ route('operaciones.ventas.index') }}" class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <select name="cliente_id" class="input">
                        <option value="">Todos los clientes</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected((string) request('cliente_id') === (string) $cliente->id)>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-3">
                    <select name="usuario_id" class="input">
                        <option value="">Todos los cajeros</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" @selected((string) request('usuario_id') === (string) $usuario->id)>
                                {{ $usuario->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <input type="date" name="desde" value="{{ request('desde') }}" class="input" title="Desde">
                </div>
                <div class="lg:col-span-2">
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="input" title="Hasta">
                </div>
                <div class="flex gap-2 lg:col-span-2">
                    <button type="submit" class="btn btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['cliente_id', 'usuario_id', 'desde', 'hasta']))
                        <a href="{{ route('operaciones.ventas.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Venta</th>
                        <th class="th">Cliente</th>
                        <th class="th hidden md:table-cell">Cajero</th>
                        <th class="th hidden lg:table-cell">Fecha</th>
                        <th class="th hidden text-center sm:table-cell">Ítems</th>
                        <th class="th text-right">Total</th>
                        <th class="th hidden text-right sm:table-cell">Acciones</th>
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
                            <td class="td">
                                <p class="font-medium text-slate-800">{{ $venta->cliente->nombre ?? 'Consumidor final' }}</p>
                                <p class="text-xs text-slate-400">{{ $venta->cliente->telefono ?? 'Sin cliente registrado' }}</p>
                            </td>
                            <td class="td hidden text-slate-600 md:table-cell">{{ $venta->usuario->nombre ?? '—' }}</td>
                            <td class="td hidden text-slate-600 lg:table-cell">{{ formato_fecha_hora($venta->fecha) }}</td>
                            <td class="td hidden text-center sm:table-cell">
                                <x-badge tone="slate">{{ pluralizar($venta->detalles_count, 'producto', 'productos') }}</x-badge>
                            </td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                            <td class="td hidden sm:table-cell">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('operaciones.ventas.show', $venta) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('operaciones.ventas.destroy', $venta) }}" method="POST" onsubmit="return confirm('¿Eliminar la venta #{{ $venta->id }}? Se restaurará el stock vendido.')">
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
                            <td colspan="7">
                                <x-empty-state
                                    icon="cart"
                                    title="No hay ventas registradas"
                                    description="Registra la primera venta desde el punto de venta."
                                >
                                    <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">Nueva venta</a>
                                </x-empty-state>
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
