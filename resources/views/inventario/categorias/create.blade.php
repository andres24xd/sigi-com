@extends('layouts.app')

@section('title', 'Nueva categoría')
@section('breadcrumb', 'Inventario / Categorías / Nueva')

@section('content')
    <x-page-header
        title="Nueva categoría"
        description="Registra una nueva línea para clasificar los productos del inventario."
    >
        <a href="{{ route('inventario.categorias.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('inventario.categorias.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                @include('inventario.categorias._form')
            </div>
        </form>
    </div>
@endsection
