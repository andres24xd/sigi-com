@props(['tone' => 'slate'])

@php
    $tonos = [
        'slate' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-200',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
    ];
@endphp

<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $tonos[$tone] ?? $tonos['slate'] }}">
    {{ $slot }}
</span>
