@extends('layouts.app')

@section('title', 'Nuevo cliente')
@section('breadcrumb', 'Terceros / Clientes / Nuevo')

@section('content')
    <x-page-header
        title="Nuevo cliente"
        description="Registra los datos de contacto de un cliente recurrente."
    >
        <a href="{{ route('terceros.clientes.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <div class="max-w-2xl">
        <form action="{{ route('terceros.clientes.store') }}" method="POST" class="card">
            @csrf
            <div class="card-body">
                @include('terceros.clientes._form')
            </div>
        </form>
    </div>
@endsection
