@extends('layouts.app')

@section('title', 'Editar categoría')
@section('breadcrumb', 'Inventario / Categorías / Editar')

@section('content')
    <x-page-header
        title="Editar categoría"
        :description="'Actualiza la información de '.$categoria->nombre.'.'"
    >
        <a href="{{ route('inventario.categorias.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('inventario.categorias.update', $categoria) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('inventario.categorias._form')
            </div>
        </form>
    </div>
@endsection
