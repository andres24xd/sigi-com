@extends('layouts.app')

@section('title', 'Nuevo producto')
@section('breadcrumb', 'Inventario / Productos / Nuevo')

@section('content')
    <x-page-header
        title="Nuevo producto"
        description="Agrega un artículo al catálogo con su categoría, precio y existencias iniciales."
    >
        <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('inventario.productos.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                @include('inventario.productos._form')
            </div>
        </form>
    </div>
@endsection
