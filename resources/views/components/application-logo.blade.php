@props(['showText' => true, 'inheritColor' => false])
{{-- Flat&Bill SaaS Logo - Building + Text Mark --}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <svg class="shrink-0 flex-shrink-0" width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="6" y="4" width="24" height="28" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
        <line x1="10" y1="10" x2="26" y2="10" stroke="currentColor" stroke-width="1.5"/>
        <line x1="10" y1="16" x2="26" y2="16" stroke="currentColor" stroke-width="1.5"/>
        <line x1="10" y1="22" x2="26" y2="22" stroke="currentColor" stroke-width="1.5"/>
        <path d="M18 4V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        <path d="M12 32V34" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
        <path d="M24 32V34" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
    </svg>
    @if($showText)
    <span class="font-bold text-lg tracking-tight {{ $inheritColor ? 'text-inherit' : 'text-slate-800' }}">Flat<span class="{{ $inheritColor ? 'text-inherit' : 'text-brand-600' }}">&</span>Bill</span>
    @endif
</div>
