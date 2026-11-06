<div class="space-y-5">
    <div>
        <x-input
            name="nombre"
            label="Nombre de la categoría"
            :value="$categoria->nombre ?? ''"
            placeholder="Ej. Herramientas"
            required
            autofocus
        />
        <p class="help">Máximo 100 caracteres. No puede repetirse con otra categoría.</p>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ route('inventario.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <x-icon name="check" class="h-4 w-4" />
        {{ isset($categoria) ? 'Actualizar categoría' : 'Guardar categoría' }}
    </button>
</div>
