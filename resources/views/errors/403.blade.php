@extends('layouts.app')

@section('title', 'Acceso restringido')
@section('breadcrumb', 'Acceso restringido')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="card p-8 text-center sm:p-10">
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-rose-50 text-rose-600">
                <x-icon name="alert" class="h-7 w-7" />
            </span>

            <p class="mt-5 text-[11px] font-semibold uppercase tracking-[0.16em] text-rose-600">Error 403</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Acceso restringido</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-500">
                Tu rol actual no tiene permiso para entrar a esta sección. Si necesitas acceso,
                solicítalo al administrador del sistema.
            </p>

            <div class="mt-7 flex flex-col justify-center gap-2 sm:flex-row">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <x-icon name="dashboard" class="h-4 w-4" />
                    Volver al panel
                </a>
                <a href="{{ route('operaciones.ventas.index') }}" class="btn btn-secondary">
                    <x-icon name="cart" class="h-4 w-4" />
                    Ir a ventas
                </a>
            </div>
        </div>
    </div>
@endsection
