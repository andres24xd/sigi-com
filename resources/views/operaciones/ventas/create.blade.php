@extends('layouts.app')

@section('title', 'Nueva venta')
@section('breadcrumb', 'Operaciones / Ventas / Nueva')

@section('content')
    @php
        $lineas = array_values(old('detalles', [['producto_id' => '', 'cantidad' => 1]]));
        $productosData = $productos->map(fn ($producto) => [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'precio' => (float) $producto->precio_venta,
            'stock' => $producto->stock_actual,
            'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
        ])->values();
    @endphp

    <x-page-header
        title="Nueva venta"
        description="Registra los productos despachados. El precio se toma del catálogo y el stock se descuenta al guardar."
    >
        <a href="{{ route('operaciones.ventas.index') }}" class="btn btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" />
            Volver
        </a>
    </x-page-header>

    <form id="form-operacion" action="{{ route('operaciones.ventas.store') }}" method="POST">
        @csrf

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Datos de la venta</h2>
                            <p class="text-sm text-slate-500">Cliente y fecha de facturación</p>
                        </div>
                    </div>
                    <div class="card-body grid gap-5 sm:grid-cols-2">
                        <div>
                            <x-select name="cliente_id" label="Cliente">
                                <option value="">Consumidor final (sin cliente)</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" @selected((string) old('cliente_id') === (string) $cliente->id)>
                                        {{ $cliente->nombre }} · {{ $cliente->telefono ?: 'sin teléfono' }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                        <div>
                            <x-input name="fecha" label="Fecha y hora" type="datetime-local" :value="now()->format('Y-m-d\TH:i')" required />
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900">Productos de la venta</h2>
                            <p class="text-sm text-slate-500">El precio se toma automáticamente del catálogo</p>
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
                                                    @disabled($producto->stock_actual <= 0)
                                                >
                                                    {{ $producto->nombre }} · stock {{ $producto->stock_actual }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <span class="detalle-label">Cantidad</span>
                                        <input type="number" min="1" step="1" name="detalles[{{ $i }}][cantidad]" value="{{ $linea['cantidad'] ?? 1 }}" class="input cantidad">
                                        <p class="stock-hint mt-1 hidden text-xs text-slate-500"></p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <span class="detalle-label">Precio</span>
                                        <input type="number" min="0" step="1" name="detalles[{{ $i }}][precio_unitario]" value="{{ $linea['precio_unitario'] ?? '' }}" class="input precio" readonly placeholder="Precio">
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
                    <p class="text-sm font-medium text-slate-400">Total a cobrar</p>
                    <p id="total" class="mt-2 text-3xl font-bold tracking-tight">$ 0</p>
                    <p class="mt-2 text-xs text-slate-400">El total se calcula automáticamente según la cantidad y el precio del catálogo.</p>
                </div>

                <div class="card p-5">
                    <div class="flex items-start gap-2 text-sm text-slate-600">
                        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                        <p>Al guardar la venta se descuentan las unidades del stock. Si no hay existencias suficientes, la operación se rechaza.</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <x-icon name="check" class="h-4 w-4" />
                    Registrar venta
                </button>

                <a href="{{ route('operaciones.ventas.index') }}" class="btn btn-secondary w-full">Cancelar</a>
            </aside>
        </div>
    </form>

    @include('operaciones._detalles-script', ['esVenta' => true])
@endsection
