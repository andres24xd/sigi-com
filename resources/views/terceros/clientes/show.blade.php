@extends('layouts.app')

@section('title', 'Detalle de cliente')
@section('breadcrumb', 'Terceros / Clientes / Detalle')

@section('content')
    <x-page-header
        title="{{ $cliente->nombre }}"
        :description="'Cliente registrado el '.formato_fecha_larga($cliente->created_at).'.'"
    >
        <a href="{{ route('terceros.clientes.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
        <a href="{{ route('terceros.clientes.edit', $cliente) }}" class="btn btn-secondary">
            <x-icon name="pencil" class="h-4 w-4" />
            Editar
        </a>
        <form action="{{ route('terceros.clientes.destroy', $cliente) }}" method="POST" onsubmit="return confirm('¿Eliminar el cliente {{ $cliente->nombre }}? Sus ventas quedarán como consumidor final.')">
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
            label="Ventas registradas"
            :value="$cliente->ventas_count"
            hint="Compras asociadas a este cliente"
            icon="cart"
            tone="brand"
        />
        <x-stat-card
            label="Total comprado"
            :value="formato_moneda($cliente->ventas_sum_total ?? 0)"
            hint="Suma histórica de sus ventas"
            icon="money"
            tone="sky"
        />
        <x-stat-card
            label="Teléfono"
            :value="$cliente->telefono ?: '—'"
            hint="Contacto del cliente"
            icon="users"
            tone="slate"
        />
        <x-stat-card
            label="Código interno"
            :value="str_pad($cliente->id, 3, '0', STR_PAD_LEFT)"
            hint="Identificador del cliente"
            icon="tag"
            tone="amber"
        />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Historial de ventas</h2>
                <p class="text-sm text-slate-500">Documentos de venta asociados a este cliente</p>
            </div>
            <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Registrar venta
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Documento</th>
                        <th class="th hidden sm:table-cell">Fecha</th>
                        <th class="th hidden md:table-cell">Cajero</th>
                        <th class="th text-center">Productos</th>
                        <th class="th text-right">Total</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ventas as $venta)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <a href="{{ route('operaciones.ventas.show', $venta) }}" class="font-semibold text-brand-700 hover:underline">
                                    Venta #{{ str_pad($venta->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                                <p class="text-xs text-slate-400 sm:hidden">{{ formato_fecha_hora($venta->fecha) }}</p>
                            </td>
                            <td class="td hidden text-slate-600 sm:table-cell">{{ formato_fecha_hora($venta->fecha) }}</td>
                            <td class="td hidden text-slate-600 md:table-cell">{{ $venta->usuario?->nombre ?? '—' }}</td>
                            <td class="td text-center">
                                <x-badge tone="slate">{{ pluralizar($venta->detalles_count, 'producto', 'productos') }}</x-badge>
                            </td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($venta->total) }}</td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('operaciones.ventas.show', $venta) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    icon="cart"
                                    title="Sin ventas registradas"
                                    description="Este cliente todavía no tiene compras en el historial."
                                >
                                    <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary">Registrar venta</a>
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
