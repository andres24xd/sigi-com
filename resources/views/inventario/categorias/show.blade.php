@extends('layouts.app')

@section('title', 'Detalle de categoría')
@section('breadcrumb', 'Inventario / Categorías / Detalle')

@section('content')
    <x-page-header
        title="{{ $categoria->nombre }}"
        :description="'Categoría creada el '.formato_fecha_larga($categoria->created_at).'.'"
    >
        <a href="{{ route('inventario.categorias.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
        <a href="{{ route('inventario.categorias.edit', $categoria) }}" class="btn btn-secondary">
            <x-icon name="pencil" class="h-4 w-4" />
            Editar
        </a>
        <form action="{{ route('inventario.categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Eliminar la categoría {{ $categoria->nombre }}?')">
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
            label="Productos asociados"
            :value="$categoria->productos_count"
            hint="Artículos clasificados en esta línea"
            icon="box"
            tone="brand"
        />
        <x-stat-card
            label="Creada"
            :value="formato_fecha($categoria->created_at)"
            hint="Fecha de registro"
            icon="calendar"
            tone="slate"
        />
        <x-stat-card
            label="Última actualización"
            :value="formato_fecha($categoria->updated_at)"
            hint="Fecha del último cambio"
            icon="refresh"
            tone="sky"
        />
        <x-stat-card
            label="Código interno"
            :value="str_pad($categoria->id, 3, '0', STR_PAD_LEFT)"
            hint="Identificador de la categoría"
            icon="tag"
            tone="amber"
        />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Productos de la categoría</h2>
                <p class="text-sm text-slate-500">{{ pluralizar($categoria->productos_count, 'producto registrado', 'productos registrados') }}</p>
            </div>
            <a href="{{ route('inventario.productos.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="h-4 w-4" />
                Nuevo producto
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Producto</th>
                        <th class="th hidden text-right sm:table-cell">Precio venta</th>
                        <th class="th text-center">Stock</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($productos as $producto)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <p class="font-semibold text-slate-900">{{ $producto->nombre }}</p>
                                <p class="text-xs text-slate-400">Código interno #{{ str_pad($producto->id, 3, '0', STR_PAD_LEFT) }}</p>
                            </td>
                            <td class="td hidden text-right font-medium text-slate-900 sm:table-cell">{{ formato_moneda($producto->precio_venta) }}</td>
                            <td class="td text-center">
                                <x-badge :tone="$producto->stock_actual === 0 ? 'rose' : ($producto->stock_actual <= 10 ? 'amber' : 'green')">
                                    {{ $producto->stock_actual }} u.
                                </x-badge>
                            </td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('inventario.productos.show', $producto) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('inventario.productos.edit', $producto) }}" class="btn-icon" title="Editar">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state
                                    icon="box"
                                    title="Esta categoría todavía no tiene productos"
                                    description="Agrega un artículo al catálogo y asígnalo a esta categoría."
                                >
                                    <a href="{{ route('inventario.productos.create') }}" class="btn btn-primary">Nuevo producto</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($productos->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
@endsection
