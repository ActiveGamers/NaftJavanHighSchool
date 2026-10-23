@props([
    'value' => null,
    'suffix' => null,
])

@php
    $text = \App\Support\PersianDigits::toPersian($value);
@endphp

<span {{ $attributes->merge(['class' => 'tabular-nums']) }}>{{ $text }}@if (filled($suffix))<span class="ms-1 font-normal">{{ $suffix }}</span>@endif</span>
