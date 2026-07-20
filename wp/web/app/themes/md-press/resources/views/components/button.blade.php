@props([
  'variant' => 'primary',
  'href' => null,
])

@php
  $baseClasses = 'inline-flex items-center justify-center font-bold rounded-xl active:scale-[0.98] transition-all duration-200 text-sm cursor-pointer text-center';
  
  $variants = [
      'primary' => 'bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 shadow-lg hover:shadow-emerald-500/20',
      'secondary' => 'bg-white/10 hover:bg-white/15 text-white',
      'glass' => 'bg-white/5 hover:bg-white/10 border border-white/10 text-white',
  ];

  $variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseClasses $variantClass"]) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => "$baseClasses $variantClass"]) }}>
    {{ $slot }}
  </button>
@endif
