@extends('layouts.app')

@section('title', 'Nuevo proveedor')
@section('breadcrumb', 'Terceros / Proveedores / Nuevo')

@section('content')
    <x-page-header
        title="Nuevo proveedor"
        description="Registra los datos de contacto de un distribuidor."
    >
        <a href="{{ route('terceros.proveedores.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('terceros.proveedores.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                @include('terceros.proveedores._form')
            </div>
        </form>
    </div>
@endsection
