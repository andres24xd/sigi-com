@extends('layouts.app')

@section('title', 'Categorías')
@section('breadcrumb', 'Inventario / Categorías')

@section('content')
    <x-page-header
        title="Categorías"
        description="Organiza el catálogo de productos por líneas de negocio."
    >
        <a href="{{ route('inventario.categorias.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Nueva categoría
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('inventario.categorias.index') }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative w-full sm:max-w-xs">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <x-icon name="search" class="h-4 w-4" />
                    </span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar categoría..." class="input pl-9">
                </div>
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                @if (request()->filled('q'))
                    <a href="{{ route('inventario.categorias.index') }}" class="btn btn-secondary">Limpiar</a>
                @endif
            </form>

            <p class="text-sm text-slate-500">{{ $categorias->total() }} categorías registradas</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/70">
                    <tr>
                        <th class="th">Categoría</th>
                        <th class="th">Productos</th>
                        <th class="th">Creada</th>
                        <th class="th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categorias as $categoria)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="td">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-50 text-sm font-bold text-brand-700">
                                        {{ mb_substr($categoria->nombre, 0, 1) }}
                                    </span>
                                    <span class="font-semibold text-slate-900">{{ $categoria->nombre }}</span>
                                </div>
                            </td>
                            <td class="td">
                                <x-badge :tone="$categoria->productos_count > 0 ? 'brand' : 'slate'">
                                    {{ pluralizar($categoria->productos_count, 'producto', 'productos') }}
                                </x-badge>
                            </td>
                            <td class="td text-slate-500">{{ formato_fecha($categoria->created_at) }}</td>
                            <td class="td">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('inventario.categorias.edit', $categoria) }}" class="btn-icon" title="Editar">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('inventario.categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Eliminar la categoría {{ $categoria->nombre }}?')">
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
                            <td colspan="4">
                                <x-empty-state
                                    icon="tag"
                                    title="No hay categorías"
                                    description="Crea la primera categoría para empezar a clasificar tus productos."
                                >
                                    <a href="{{ route('inventario.categorias.create') }}" class="btn btn-primary">Nueva categoría</a>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categorias->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $categorias->links() }}
            </div>
        @endif
    </div>
@endsection
