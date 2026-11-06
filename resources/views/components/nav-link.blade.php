@props(['href', 'active' => false, 'icon' => 'dashboard'])

<a href="{{ $href }}" @class([
    'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
    'bg-brand-600 text-white shadow-sm' => $active,
    'text-slate-300 hover:bg-white/5 hover:text-white' => ! $active,
])>
    <x-icon :name="$icon" class="h-5 w-5 shrink-0" />
    <span>{{ $slot }}</span>
    @if ($active)
        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/80"></span>
    @endif
</a>
