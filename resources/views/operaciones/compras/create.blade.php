@extends('layouts.app')

@section('title', 'Registrar compra')
@section('breadcrumb', 'Operaciones / Compras / Nueva')

@section('content')
    @php
        $lineas = array_values(old('detalles', [['producto_id' => '', 'cantidad' => 1, 'precio_unitario' => '']]));
        $productosData = $productos->map(fn ($producto) => [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'precio' => (float) $producto->precio_venta,
            'stock' => $producto->stock_actual,
            'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
        ])->values();
    @endphp

    <x-page-header
        title="Registrar compra"
        description="Selecciona el proveedor y los productos recibidos. El stock se incrementará automáticamente."
    >
        <a href="{{ route('operaciones.compras.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <form id="form-operacion" action="{{ route('operaciones.compras.store') }}" method="POST">
        @csrf

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Datos de la compra</h2>
                            <p class="text-sm text-slate-500">Proveedor y fecha de la transacción</p>
                        </div>
                    </div>
                    <div class="card-body grid gap-5 sm:grid-cols-2">
                        <div>
                            <x-select name="proveedor_id" label="Proveedor" required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" @selected((string) old('proveedor_id') === (string) $proveedor->id)>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                        <div>
                            <x-input name="fecha" label="Fecha de compra" type="date" :value="now()->toDateString()" required />
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Productos recibidos</h2>
                            <p class="text-sm text-slate-500">Costo unitario y cantidad por cada artículo</p>
                        </div>
                        <button type="button" id="agregar-linea" class="btn btn-secondary">
                            <x-icon name="plus" class="h-4 w-4" />
                            Agregar producto
                        </button>
                    </div>

                    <div class="card-body">
                        <div id="detalles" class="space-y-3">
                            @foreach ($lineas as $i => $linea)
                                <div class="detalle-linea grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-3 sm:grid-cols-12 sm:items-end">
                                    <div class="sm:col-span-5">
                                        <span class="detalle-label">Producto</span>
                                        <select name="detalles[{{ $i }}][producto_id]" class="input producto">
                                            <option value="">Seleccione un producto</option>
                                            @foreach ($productos as $producto)
                                                <option
                                                    value="{{ $producto->id }}"
                                                    data-precio="{{ $producto->precio_venta }}"
                                                    data-stock="{{ $producto->stock_actual }}"
                                                    @selected((string) ($linea['producto_id'] ?? '') === (string) $producto->id)
                                                >
                                                    {{ $producto->nombre }} · {{ $producto->categoria->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <span class="detalle-label">Cantidad</span>
                                        <input type="number" min="1" step="1" name="detalles[{{ $i }}][cantidad]" value="{{ $linea['cantidad'] ?? 1 }}" class="input cantidad">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <span class="detalle-label">Costo unitario</span>
                                        <input type="number" min="0" step="1" name="detalles[{{ $i }}][precio_unitario]" value="{{ $linea['precio_unitario'] ?? '' }}" class="input precio" placeholder="Costo">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <span class="detalle-label">Subtotal</span>
                                        <div class="subtotal flex h-[42px] items-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800">$ 0</div>
                                    </div>

                                    <div class="sm:col-span-1 flex justify-end">
                                        <button type="button" class="btn-icon btn-icon-danger quitar" title="Quitar producto">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="card bg-slate-950 p-6 text-white">
                    <p class="text-sm font-medium text-slate-400">Total de la compra</p>
                    <p id="total" class="mt-2 text-3xl font-bold tracking-tight">$ 0</p>
                    <p class="mt-2 text-xs text-slate-400">El total se calcula automáticamente con las cantidades y costos.</p>
                </div>

                <div class="card p-5">
                    <div class="flex items-start gap-2 text-sm text-slate-600">
                        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                        <p>Al guardar la compra, cada producto sumará las unidades recibidas al stock actual.</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <x-icon name="check" class="h-4 w-4" />
                    Registrar compra
                </button>

                <a href="{{ route('operaciones.compras.index') }}" class="btn btn-secondary w-full">Cancelar</a>
            </aside>
        </div>
    </form>

    @include('operaciones._detalles-script', ['esVenta' => false])
@endsection
