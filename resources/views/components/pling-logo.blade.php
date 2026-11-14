@props(['size' => 28, 'withText' => true, 'textClass' => 'text-lg'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" fill="none" aria-hidden="true" class="shrink-0">
        <circle cx="24" cy="24" r="20" stroke="#1565C0" stroke-width="4.5" stroke-linecap="round" stroke-dasharray="26 5.4" transform="rotate(-90 24 24)"/>
        <circle cx="24" cy="24" r="12.5" stroke="#5B87A6" stroke-width="4" stroke-linecap="round" stroke-dasharray="16 3.6" transform="rotate(20 24 24)"/>
        <circle cx="24" cy="24" r="5.5" fill="#FF8A80"/>
    </svg>
    @if($withText)
        <span class="font-extrabold tracking-tight text-pling-primary {{ $textClass }}">PlingPager</span>
    @endif
</span>