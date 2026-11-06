<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-slate-950 text-slate-300 transition-transform duration-300 ease-out lg:translate-x-0">
    <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
        <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-500 text-sm font-bold text-white shadow-lg shadow-brand-500/20">S</span>
        <div class="min-w-0">
            <p class="truncate text-sm font-bold tracking-tight text-white">SIGI-COM</p>
            <p class="truncate text-[11px] text-slate-400">Inventario y ventas</p>
        </div>
        <button id="sidebar-close" type="button" class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Cerrar menú">
            <x-icon name="x" class="h-5 w-5" />
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Principal</p>
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
                Panel de control
            </x-nav-link>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Inventario</p>
            <div class="space-y-1">
                <x-nav-link :href="route('inventario.categorias.index')" :active="request()->routeIs('inventario.categorias.*')" icon="tag">
                    Categorías
                </x-nav-link>
                <x-nav-link :href="route('inventario.productos.index')" :active="request()->routeIs('inventario.productos.*')" icon="box">
                    Productos
                </x-nav-link>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Operaciones</p>
            <div class="space-y-1">
                <x-nav-link :href="route('operaciones.compras.index')" :active="request()->routeIs('operaciones.compras.*')" icon="truck">
                    Compras
                </x-nav-link>
                <x-nav-link :href="route('operaciones.ventas.index')" :active="request()->routeIs('operaciones.ventas.*')" icon="cart">
                    Ventas
                </x-nav-link>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Terceros</p>
            <div class="space-y-1">
                <x-nav-link :href="route('terceros.proveedores.index')" :active="request()->routeIs('terceros.proveedores.*')" icon="building">
                    Proveedores
                </x-nav-link>
                <x-nav-link :href="route('terceros.clientes.index')" :active="request()->routeIs('terceros.clientes.*')" icon="users">
                    Clientes
                </x-nav-link>
            </div>
        </div>

        <div>
            <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Análisis</p>
            <x-nav-link :href="route('reportes.index')" :active="request()->routeIs('reportes.*')" icon="chart">
                Reportes
            </x-nav-link>
        </div>
    </nav>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-xl bg-white/5 px-3 py-2.5">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-500/15 text-xs font-bold text-brand-300 ring-1 ring-brand-500/30">
                {{ iniciales($usuarioActual->nombre ?? 'Administrador') }}
            </span>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white">{{ $usuarioActual->nombre ?? 'Administrador' }}</p>
                <p class="truncate text-[11px] text-slate-400">{{ $usuarioActual->rol ?? 'Administrador' }}</p>
            </div>
        </div>
        <p class="mt-3 px-1 text-[10px] uppercase tracking-wider text-slate-600">SIGI-COM · v1.0 · Actividad 3</p>
    </div>
</aside>
