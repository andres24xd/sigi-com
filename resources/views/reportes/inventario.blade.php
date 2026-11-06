@extends('layouts.app')

@section('title', 'Reporte de inventario')
@section('breadcrumb', 'Análisis / Reportes / Inventario')

@section('content')
    <x-page-header
        title="Reporte de inventario"
        description="Existencias, valorización y alertas de stock por producto."
    />

    @include('reportes._tabs')

    <form method="GET" action="{{ route('reportes.inventario') }}" class="card mb-6 p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-4">
                <label class="label" for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="input">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected((string) request('categoria_id') === (string) $categoria->id)>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-4">
                <label class="label" for="estado">Estado del stock</label>
                <select id="estado" name="estado" class="input">
                    <option value="">Todos</option>
                    <option value="disponible" @selected(request('estado') === 'disponible')>Disponible (más de 10)</option>
                    <option value="bajo" @selected(request('estado') === 'bajo')>Stock bajo (1 a 10)</option>
                    <option value="agotado" @selected(request('estado') === 'agotado')>Agotado (0)</option>
                </select>
            </div>
            <div class="flex gap-2 lg:col-span-4">
                <button type="submit" class="btn btn-primary flex-1">Aplicar</button>
                <a href="{{ route('reportes.inventario') }}" class="btn btn-secondary" title="Restablecer">
                    <x-icon name="refresh" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </form>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Productos" :value="$resumen['total']" icon="box" tone="brand" />
        <x-stat-card label="Unidades en stock" :value="$resumen['unidades']" icon="clipboard" tone="sky" />
        <x-stat-card label="Valor del inventario" :value="formato_moneda($resumen['valor'])" icon="money" tone="slate" />
        <x-stat-card label="Con stock bajo" :value="$resumen['bajo']" hint="Incluye agotados" icon="alert" tone="amber" />
    </div>

    <div class="card mt-6">
        <div class="card-header">
            <h2 class="text-base font-semibold text-slate-900">Existencias por producto</h2>
            <p class="text-sm text-slate-500">{{ $productos->total() }} productos</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Producto</th>
                        <th class="th">Categoría</th>
                        <th class="th text-center">Stock</th>
                        <th class="th text-right">Precio venta</th>
                        <th class="th text-right">Valor stock</th>
                        <th class="th text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($productos as $producto)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td font-medium text-slate-900">{{ $producto->nombre }}</td>
                            <td class="td">
                                <x-badge tone="slate">{{ $producto->categoria->nombre }}</x-badge>
                            </td>
                            <td class="td text-center font-semibold">{{ $producto->stock_actual }}</td>
                            <td class="td text-right">{{ formato_moneda($producto->precio_venta) }}</td>
                            <td class="td text-right font-medium text-slate-900">
                                {{ formato_moneda($producto->stock_actual * $producto->precio_venta) }}
                            </td>
                            <td class="td text-center">
                                @if ($producto->stock_actual === 0)
                                    <x-badge tone="rose">Agotado</x-badge>
                                @elseif ($producto->stock_actual <= 10)
                                    <x-badge tone="amber">Stock bajo</x-badge>
                                @else
                                    <x-badge tone="green">Disponible</x-badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="box" title="Sin productos" description="No hay productos que coincidan con los filtros." />
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
