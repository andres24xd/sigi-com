<header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-[1500px] items-center gap-3 px-4 sm:px-6 lg:px-8">
        <button id="sidebar-toggle" type="button" class="btn-icon lg:hidden" aria-label="Abrir menú">
            <x-icon name="menu" class="h-5 w-5" />
        </button>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-slate-500">@yield('breadcrumb', 'Panel de control')</p>
        </div>

        <div class="hidden items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 sm:flex">
            <x-icon name="calendar" class="h-4 w-4 text-slate-400" />
            {{ ucfirst(now()->locale('es')->isoFormat('ddd, D MMM YYYY')) }}
        </div>

        <a href="{{ route('operaciones.ventas.create') }}" class="btn btn-primary hidden sm:inline-flex">
            <x-icon name="plus" class="h-4 w-4" />
            Nueva venta
        </a>

        <div class="flex items-center gap-2 border-l border-slate-200 pl-3">
            <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">
                {{ iniciales($usuarioActual->nombre ?? 'Administrador') }}
            </span>
            <div class="hidden leading-tight md:block">
                <p class="max-w-[200px] truncate text-sm font-semibold text-slate-800">{{ $usuarioActual->nombre ?? 'Administrador' }}</p>
                <p class="text-xs text-slate-500">{{ $usuarioActual->rol ?? 'Administrador' }}</p>
            </div>
        </div>
    </div>
</header>
