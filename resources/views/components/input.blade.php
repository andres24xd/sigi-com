@props(['label' => null, 'name', 'type' => 'text', 'value' => null])

@if ($label)
    <label for="{{ $name }}" class="label">{{ $label }}</label>
@endif

<input
    id="{{ $name }}"
    type="{{ $type }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge(['class' => 'input']) }}
>

@error($name)
    <p class="error">{{ $message }}</p>
@enderror
