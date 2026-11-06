@extends('layouts.app')

@section('title', 'Editar producto')
@section('breadcrumb', 'Inventario / Productos / Editar')

@section('content')
    <x-page-header
        title="Editar producto"
        :description="'Actualiza la información de '.$producto->nombre.'.'"
    >
        <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('inventario.productos.update', $producto) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('inventario.productos._form')
            </div>
        </form>
    </div>

    <div class="mt-6 max-w-3xl">
        <div class="card border-rose-200 p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">Eliminar producto</p>
                    <p class="text-sm text-slate-500">Solo es posible si el producto no tiene compras ni ventas asociadas.</p>
                </div>
                <form action="{{ route('inventario.productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Eliminar el producto {{ $producto->nombre }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <x-icon name="trash" class="h-4 w-4" />
                        Eliminar producto
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
