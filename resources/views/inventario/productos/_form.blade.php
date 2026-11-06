<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input
            name="nombre"
            label="Nombre del producto"
            :value="$producto->nombre ?? ''"
            placeholder="Ej. Martillo de uña 16 oz"
            required
            autofocus
        />
    </div>

    <div>
        <x-select name="categoria_id" label="Categoría" required>
            <option value="">Seleccione una categoría</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $producto->categoria_id ?? '') === (string) $categoria->id)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </x-select>
    </div>

    <div>
        <x-input
            name="precio_venta"
            label="Precio de venta"
            type="number"
            min="0"
            step="1"
            :value="$producto->precio_venta ?? ''"
            placeholder="0"
            required
        />
        <p class="help">Valor en pesos colombianos, sin decimales.</p>
    </div>

    <div>
        <x-input
            name="stock_actual"
            label="Stock actual"
            type="number"
            min="0"
            step="1"
            :value="$producto->stock_actual ?? 0"
            required
        />
        <p class="help">Las compras suman y las ventas descuentan automáticamente.</p>
    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 sm:col-span-2">
        <div class="flex items-start gap-2">
            <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
            <p>El stock también se modifica al registrar compras y ventas. Ajusta este valor solo para corregir inventario físico.</p>
        </div>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('inventario.productos.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <x-icon name="check" class="h-4 w-4" />
        {{ isset($producto) ? 'Actualizar producto' : 'Guardar producto' }}
    </button>
</div>
