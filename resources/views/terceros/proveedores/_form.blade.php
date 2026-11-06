<div class="space-y-5">
    <div>
        <x-input
            name="nombre"
            label="Nombre o razón social"
            :value="$proveedor->nombre ?? ''"
            placeholder="Ej. Distribuidora Central S.A.S."
            required
            autofocus
        />
    </div>

    <div>
        <x-input
            name="telefono"
            label="Teléfono de contacto"
            :value="$proveedor->telefono ?? ''"
            placeholder="Ej. 3001234567"
        />
        <p class="help">Opcional. Máximo 20 caracteres.</p>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('terceros.proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <x-icon name="check" class="h-4 w-4" />
        {{ isset($proveedor) ? 'Actualizar proveedor' : 'Guardar proveedor' }}
    </button>
</div>
