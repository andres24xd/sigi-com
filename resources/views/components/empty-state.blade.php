@props(['icon' => 'box', 'title' => 'Sin registros', 'description' => null])

<div class="flex flex-col items-center justify-center px-6 py-16 text-center">
    <span class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <p class="mt-4 text-sm font-semibold text-slate-700">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @endif
    <div class="mt-4">{{ $slot }}</div>
</div>
