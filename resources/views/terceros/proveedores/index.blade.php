@extends('layouts.app')

@section('title', 'Proveedores')
@section('breadcrumb', 'Terceros / Proveedores')

@section('content')
    <x-page-header
        title="Proveedores"
        description="Directorio de distribuidores que abastecen el inventario del negocio."
    >
        <a href="{{ route('terceros.proveedores.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nuevo proveedor
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('terceros.proveedores.index') }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative w-full sm:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar proveedor..." class="input pl-9">
                </div>
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                @if (request()->filled('q'))
                    <a href="{{ route('terceros.proveedores.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif
            </form>
            <p class="text-sm text-slate-500">{{ $proveedores->total() }} proveedores registrados</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Proveedor</th>
                        <th class="th">Teléfono</th>
                        <th class="th text-center">Compras</th>
                        <th class="th text-right">Total comprado</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($proveedores as $proveedor)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-sky-50 text-xs font-bold text-sky-700">
                                        {{ iniciales($proveedor->nombre) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $proveedor->nombre }}</p>
                                        <p class="text-xs text-slate-400">Código #{{ str_pad($proveedor->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="td text-slate-600">{{ $proveedor->telefono ?: '—' }}</td>
                            <td class="td text-center">
                                <x-badge :tone="$proveedor->compras_count > 0 ? 'sky' : 'slate'">
                                    {{ $proveedor->compras_count }}
                                </x-badge>
                            </td>
                            <td class="td text-right font-medium text-slate-900">
                                {{ formato_moneda($proveedor->compras_sum_total ?? 0) }}
                            </td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('terceros.proveedores.edit', $proveedor) }}" class="btn-icon" title="Editar">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('terceros.proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('¿Eliminar el proveedor {{ $proveedor->nombre }}?')">
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
                                    icon="building"
                                    title="No hay proveedores"
                                    description="Registra los distribuidores para poder cargar compras de abastecimiento."
                                >
                                    <a href="{{ route('terceros.proveedores.create') }}" class="btn btn-primary">Nuevo proveedor</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($proveedores->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $proveedores->links() }}
            </div>
        @endif
    </div>
@endsection
