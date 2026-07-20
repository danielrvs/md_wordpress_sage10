@props([
  'title',
  'price',
  'period' => '/ mes',
  'description',
  'popular' => false,
  'features' => [],
  'ctaText',
  'ctaHref',
  'ctaVariant' => 'secondary',
])

<div {{ $attributes->merge([
  'class' => $popular 
    ? 'relative flex flex-col p-8 rounded-3xl bg-gradient-to-b from-white/10 to-white/[0.02] border-2 border-emerald-500 shadow-2xl shadow-emerald-500/5 hover:scale-[1.02] transition-all duration-300 group' 
    : 'relative flex flex-col p-8 rounded-3xl bg-white/5 border border-white/10 hover:border-emerald-500/30 transition-all duration-300 group'
]) }}>
  
  @if($popular)
    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-emerald-500 to-teal-600 text-slate-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
      Más Popular
    </div>
  @endif
  
  <div class="mb-6">
    <h3 class="text-lg font-bold {{ $popular ? 'text-white' : 'text-slate-300' }}">{{ $title }}</h3>
    <p class="mt-2 text-xs {{ $popular ? 'text-slate-300' : 'text-slate-400' }}">{{ $description }}</p>
    <div class="mt-4 flex items-baseline text-white">
      <span class="text-4xl font-extrabold tracking-tight">{{ $price }}</span>
      <span class="ml-1 text-sm font-semibold {{ $popular ? 'text-emerald-400' : 'text-slate-400' }}">{{ $period }}</span>
    </div>
  </div>
  
  <ul class="space-y-4 mb-8 flex-1 text-sm {{ $popular ? 'text-slate-200' : 'text-slate-300' }}">
    @foreach($features as $feature)
      @php
        $isNegative = str_starts_with($feature, '-');
        $cleanFeature = $isNegative ? ltrim($feature, '-') : $feature;
      @endphp
      
      @if($isNegative)
        <li class="flex items-center gap-3 text-slate-500">
          <svg class="h-5 w-5 text-slate-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
          {{ $cleanFeature }}
        </li>
      @else
        <li class="flex items-center gap-3">
          <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
          </svg>
          {!! $cleanFeature !!}
        </li>
      @endif
    @endforeach
  </ul>
  
  <x-button :variant="$ctaVariant" :href="$ctaHref" class="w-full py-3">
    {{ $ctaText }}
  </x-button>
</div>
