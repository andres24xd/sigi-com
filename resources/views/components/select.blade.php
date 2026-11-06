@props(['label' => null, 'name'])

@if ($label)
    <label for="{{ $name }}" class="label">{{ $label }}</label>
@endif

<select id="{{ $name }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'input']) }}>
    {{ $slot }}
</select>

@error($name)
    <p class="error">{{ $message }}</p>
@enderror
