@extends('layouts.app')

@section('content')
  <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-1/4 w-96.5 h-96.5 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-1/4 w-96.5 h-96.5 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse"
      style="animation-delay: 2s;"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 relative z-10">

      <!-- Title -->
      <div class="text-center max-w-3xl mx-auto mb-12">
        <h1
          class="text-4xl md:text-5xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-emerald-400 leading-tight">
          {{ __t('archive.title_pre') }} <span class="text-emerald-400">{{ __t('archive.title_highlight') }}</span>
        </h1>
        <p class="mt-4 text-base text-slate-400 leading-relaxed">
          {{ __t('archive.subtitle') }}
        </p>
      </div>

      <!-- React App Mount Point (Full Width) -->
      <div id="medical-search-root">
        <!-- Loading state fallback before React mounts -->
        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
          <svg class="animate-spin h-10 w-10 text-emerald-500 mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
          </svg>
          <p class="text-sm font-semibold tracking-wide">{{ __t('archive.loading') }}</p>
        </div>
      </div>

    </div>
  </div>
@endsection
