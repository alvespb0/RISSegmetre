@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-foreground mb-2']) }}>
    {{ $value ?? $slot }}
</label>
