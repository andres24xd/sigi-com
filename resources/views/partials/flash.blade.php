@if (session('success'))
    <div data-flash class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800 shadow-sm">
        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p class="flex-1">{{ session('success') }}</p>
        <button type="button" data-dismiss class="rounded-lg p-0.5 text-emerald-600/70 transition hover:bg-emerald-100 hover:text-emerald-800" aria-label="Cerrar">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div data-flash class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-800 shadow-sm">
        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
        <p class="flex-1">{{ session('error') }}</p>
        <button type="button" data-dismiss class="rounded-lg p-0.5 text-rose-600/70 transition hover:bg-rose-100 hover:text-rose-800" aria-label="Cerrar">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm text-rose-800 shadow-sm">
        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
        <div class="min-w-0">
            <p class="font-semibold">Revisa la información ingresada</p>
            <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
