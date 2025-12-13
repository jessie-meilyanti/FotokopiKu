@props(['type' => 'button', 'color' => 'indigo', 'href' => null])

@php
    $colors = [
        'indigo' => 'bg-indigo-600 text-white hover:bg-indigo-700',
        'gray' => 'bg-gray-100 text-gray-800 hover:bg-gray-200',
        'red' => 'bg-red-500 text-white hover:bg-red-600',
        'green' => 'bg-emerald-600 text-white hover:bg-emerald-700',
    ];
    $colorClass = $colors[$color] ?? $colors['indigo'];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-3 py-2 rounded-lg shadow transition-smooth hover-lift active-press focus-ring $colorClass"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-3 py-2 rounded-lg shadow transition-smooth hover-lift active-press focus-ring $colorClass"]) }}>
        {{ $slot }}
    </button>
@endif
