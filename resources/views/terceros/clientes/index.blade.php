@extends('layouts.app')

@section('title', 'Clientes')
@section('breadcrumb', 'Terceros / Clientes')

@section('content')
    <x-page-header
        title="Clientes"
        description="Personas que compran en el negocio y que pueden asociarse a una venta."
    >
        <a href="{{ route('terceros.clientes.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nuevo cliente
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('terceros.clientes.index') }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative w-full sm:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar cliente..." class="input pl-9">
                </div>
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                @if (request()->filled('q'))
                    <a href="{{ route('terceros.clientes.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif
            </form>
            <p class="text-sm text-slate-500">{{ $clientes->total() }} clientes registrados</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Cliente</th>
                        <th class="th">Teléfono</th>
                        <th class="th text-center">Ventas</th>
                        <th class="th text-right">Total comprado</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clientes as $cliente)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-50 text-xs font-bold text-brand-700">
                                        {{ iniciales($cliente->nombre) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $cliente->nombre }}</p>
                                        <p class="text-xs text-slate-400">Código #{{ str_pad($cliente->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="td text-slate-600">{{ $cliente->telefono ?: '—' }}</td>
                            <td class="td text-center">
                                <x-badge :tone="$cliente->ventas_count > 0 ? 'brand' : 'slate'">
                                    {{ $cliente->ventas_count }}
                                </x-badge>
                            </td>
                            <td class="td text-right font-medium text-slate-900">
                                {{ formato_moneda($cliente->ventas_sum_total ?? 0) }}
                            </td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('terceros.clientes.show', $cliente) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('terceros.clientes.edit', $cliente) }}" class="btn-icon" title="Editar">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('terceros.clientes.destroy', $cliente) }}" method="POST" onsubmit="return confirm('¿Eliminar el cliente {{ $cliente->nombre }}? Sus ventas quedarán como consumidor final.')">
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
                            <td colspan="5">
                                <x-empty-state
                                    icon="users"
                                    title="No hay clientes"
                                    description="Registra clientes recurrentes o vende directamente a consumidor final."
                                >
                                    <a href="{{ route('terceros.clientes.create') }}" class="btn btn-primary">Nuevo cliente</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clientes->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
@endsection
