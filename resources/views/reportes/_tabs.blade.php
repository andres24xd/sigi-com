@php
    $esAdmin = auth()->user()?->esAdministrador() ?? false;

    $tabs = $esAdmin
        ? [
            ['route' => 'reportes.index', 'label' => 'Resumen'],
            ['route' => 'reportes.ventas', 'label' => 'Ventas'],
            ['route' => 'reportes.compras', 'label' => 'Compras'],
            ['route' => 'reportes.inventario', 'label' => 'Inventario'],
        ]
        : [
            ['route' => 'reportes.ventas', 'label' => 'Mis ventas'],
        ];
@endphp

<div class="mb-6 flex flex-wrap gap-1.5 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm">
    @foreach ($tabs as $tab)
        <a
            href="{{ route($tab['route']) }}"
            @class([
                'rounded-xl px-4 py-2 text-sm font-semibold transition',
                'bg-brand-600 text-white shadow-sm' => request()->routeIs($tab['route']),
                'text-slate-600 hover:bg-slate-100 hover:text-slate-900' => ! request()->routeIs($tab['route']),
            ])
        >
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
