@extends('layouts.app')

@section('title', 'Editar cliente')
@section('breadcrumb', 'Terceros / Clientes / Editar')

@section('content')
    <x-page-header
        title="Editar cliente"
        :description="'Actualiza los datos de '.$cliente->nombre.'.'"
    >
        <a href="{{ route('terceros.clientes.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('terceros.clientes.update', $cliente) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-body">
                @include('terceros.clientes._form')
            </div>
        </form>
    </div>
@endsection
