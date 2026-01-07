@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-4 bg-[#10b981]/10 border border-[#10b981]/20 rounded-lg font-medium text-sm text-[#10b981]']) }}>
        {{ $status }}
    </div>
@endif
