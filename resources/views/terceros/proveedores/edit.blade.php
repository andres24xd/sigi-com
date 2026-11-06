@extends('layouts.app')

@section('title', 'Editar proveedor')
@section('breadcrumb', 'Terceros / Proveedores / Editar')

@section('content')
    <x-page-header
        title="Editar proveedor"
        :description="'Actualiza los datos de '.$proveedor->nombre.'.'"
    >
        <a href="{{ route('terceros.proveedores.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('terceros.proveedores.update', $proveedor) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('terceros.proveedores._form')
            </div>
        </form>
    </div>
@endsection
