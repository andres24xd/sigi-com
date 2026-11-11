<div class="space-y-5">
    <div>
        <x-input
            name="nombre"
            label="Nombre completo"
            :value="$cliente->nombre ?? ''"
            placeholder="Ej. María Pérez"
            required
            autofocus
        />
    </div>

    <div>
        <x-input
            name="telefono"
            label="Teléfono de contacto"
            :value="$cliente->telefono ?? ''"
            placeholder="Ej. 3101112233"
        />
        <p class="help">Opcional. Entre 7 y 20 caracteres: números, espacios, +, - y paréntesis.</p>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('terceros.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <x-icon name="check" class="h-4 w-4" />
        {{ isset($cliente) ? 'Actualizar cliente' : 'Guardar cliente' }}
    </button>
</div>
