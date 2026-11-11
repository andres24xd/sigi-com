@extends('layouts.app')

@section('title', 'Productos')
@section('breadcrumb', 'Inventario / Productos')

@section('content')
    <x-page-header
        title="Productos"
        description="Catálogo de artículos, precios de venta y existencias disponibles."
    >
        <a href="{{ route('inventario.productos.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nuevo producto
        </a>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Productos" :value="$resumen['total']" hint="Artículos en catálogo" icon="box" tone="brand" />
        <x-stat-card label="Stock bajo" :value="$resumen['bajo']" hint="Entre 1 y 10 unidades" icon="alert" tone="amber" />
        <x-stat-card label="Agotados" :value="$resumen['agotados']" hint="Sin unidades disponibles" icon="x" tone="rose" />
        <x-stat-card label="Valor del inventario" :value="formato_moneda($resumen['valor'])" hint="Stock por precio de venta" icon="money" tone="sky" />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <form method="GET" action="{{ route('inventario.productos.index') }}" class="grid w-full gap-2 sm:grid-cols-2 lg:grid-cols-12">
                <div class="relative lg:col-span-4">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar producto..." class="input pl-9">
                </div>

                <div class="lg:col-span-3">
                    <select name="categoria_id" class="input">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected((string) request('categoria_id') === (string) $categoria->id)>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select name="estado" class="input">
                        <option value="">Todo el stock</option>
                        <option value="bajo" @selected(request('estado') === 'bajo')>Stock bajo</option>
                        <option value="agotado" @selected(request('estado') === 'agotado')>Agotados</option>
                    </select>
                </div>

                <div class="flex gap-2 lg:col-span-3">
                    <button type="submit" class="btn btn-secondary flex-1">Filtrar</button>
                    @if (request()->hasAny(['q', 'categoria_id', 'estado']))
                        <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Producto</th>
                        <th class="th hidden sm:table-cell">Categoría</th>
                        <th class="th hidden text-right md:table-cell">Precio venta</th>
                        <th class="th text-center">Stock</th>
                        <th class="th hidden text-right lg:table-cell">Valor stock</th>
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
                            <td class="td hidden sm:table-cell">
                                <x-badge tone="slate">{{ $producto->categoria->nombre }}</x-badge>
                            </td>
                            <td class="td hidden text-right font-medium text-slate-900 md:table-cell">{{ formato_moneda($producto->precio_venta) }}</td>
                            <td class="td text-center">
                                <x-badge :tone="$producto->stock_actual === 0 ? 'rose' : ($producto->stock_actual <= 10 ? 'amber' : 'green')">
                                    {{ $producto->stock_actual }} u.
                                </x-badge>
                            </td>
                            <td class="td hidden text-right text-slate-600 lg:table-cell">
                                {{ formato_moneda($producto->stock_actual * $producto->precio_venta) }}
                            </td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('inventario.productos.show', $producto) }}" class="btn-icon" title="Ver detalle">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('inventario.productos.edit', $producto) }}" class="btn-icon" title="Editar">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('inventario.productos.destroy', $producto) }}" method="POST" class="hidden sm:block" onsubmit="return confirm('¿Eliminar el producto {{ $producto->nombre }}?')">
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
                                    icon="box"
                                    title="No se encontraron productos"
                                    description="Ajusta los filtros o registra un nuevo artículo en el catálogo."
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
