@extends('layouts.app')

@section('title', 'Detalle de proveedor')
@section('breadcrumb', 'Terceros / Proveedores / Detalle')

@section('content')
    <x-page-header
        title="{{ $proveedor->nombre }}"
        :description="'Proveedor registrado el '.formato_fecha_larga($proveedor->created_at).'.'"
    >
        <a href="{{ route('terceros.proveedores.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
        <a href="{{ route('terceros.proveedores.edit', $proveedor) }}" class="btn btn-secondary">
            <x-icon name="pencil" class="h-4 w-4" />
            Editar
        </a>
        <form action="{{ route('terceros.proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('¿Eliminar el proveedor {{ $proveedor->nombre }}?')">
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
            label="Compras registradas"
            :value="$proveedor->compras_count"
            hint="Documentos de abastecimiento"
            icon="truck"
            tone="sky"
        />
        <x-stat-card
            label="Total comprado"
            :value="formato_moneda($proveedor->compras_sum_total ?? 0)"
            hint="Suma histórica de las compras"
            icon="money"
            tone="brand"
        />
        <x-stat-card
            label="Teléfono"
            :value="$proveedor->telefono ?: '—'"
            hint="Contacto del proveedor"
            icon="users"
            tone="slate"
        />
        <x-stat-card
            label="Código interno"
            :value="str_pad($proveedor->id, 3, '0', STR_PAD_LEFT)"
            hint="Identificador del proveedor"
            icon="building"
            tone="amber"
        />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Historial de compras</h2>
                <p class="text-sm text-slate-500">Documentos de abastecimiento asociados a este proveedor</p>
            </div>
            <a href="{{ route('operaciones.compras.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Registrar compra
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Documento</th>
                        <th class="th hidden sm:table-cell">Fecha</th>
                        <th class="th text-center">Productos</th>
                        <th class="th text-right">Total</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($compras as $compra)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <a href="{{ route('operaciones.compras.show', $compra) }}" class="font-semibold text-brand-700 hover:underline">
                                    Compra #{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                                <p class="text-xs text-slate-400 sm:hidden">{{ formato_fecha($compra->fecha) }}</p>
                            </td>
                            <td class="td hidden text-slate-600 sm:table-cell">{{ formato_fecha($compra->fecha) }}</td>
                            <td class="td text-center">
                                <x-badge tone="slate">{{ pluralizar($compra->detalles_count, 'producto', 'productos') }}</x-badge>
                            </td>
                            <td class="td text-right font-semibold text-slate-900">{{ formato_moneda($compra->total) }}</td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('operaciones.compras.show', $compra) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state
                                    icon="truck"
                                    title="Sin compras registradas"
                                    description="Registra la primera compra para abastecer el inventario."
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
