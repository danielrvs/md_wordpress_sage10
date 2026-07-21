@extends('layouts.app')

@section('content')
  <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-1/4 w-96.5 h-96.5 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-1/4 w-96.5 h-96.5 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse"
      style="animation-delay: 2s;"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 relative z-10">

      <!-- Hero Title -->
      <div class="text-center max-w-3xl mx-auto mb-12">
        <h1
          class="text-5xl md:text-6xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-emerald-400 leading-tight">
          {{ __t('hero.title_pre') }} <span class="text-emerald-400">{{ __t('hero.title_highlight') }}</span>
        </h1>
        <p class="mt-6 text-lg text-slate-400 leading-relaxed">
          {{ __t('hero.subtitle') }}
        </p>
      </div>

      <!-- React App Root Container (styled with Glassmorphism) -->
      <div class="max-w-xl mx-auto mb-16">
        <div
          class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl relative group hover:border-emerald-500/30 transition-all duration-300">
          <!-- Glow Effect -->
          <div
            class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-10 group-hover:opacity-20 transition duration-300">
          </div>

          <div class="relative">
            <!-- React Mount Point -->
            <div id="medical-search-root">
              <!-- Loading state fallback before React mounts -->
              <div class="flex flex-col items-center justify-center py-6 text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-400 mb-3" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                  </path>
                </svg>
                <p class="text-sm font-medium tracking-wide">Cargando directorio interactivo...</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Trust Stats Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto pt-8 border-t border-slate-800">
        <div class="text-center">
          <div class="text-3xl font-bold text-white">15,000+</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">{{ __t('stats.doctors') }}</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">99.8%</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">{{ __t('stats.satisfaction') }}</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">120+</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">{{ __t('stats.specialties') }}</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">24/7</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">{{ __t('stats.support') }}</div>
        </div>
      </div>

      <!-- Features Section (Nuestros Servicios) -->
      <div class="mt-32 border-t border-white/5 pt-20">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">{{ __t('services.tag') }}</span>
          <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-4">{{ __t('services.title') }}</h2>
          <p class="mt-4 text-slate-400 text-sm md:text-base leading-relaxed">
            {{ __t('services.subtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 relative">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-3">{{ __t('services.f1_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              {{ __t('services.f1_desc') }}
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 relative">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-3">{{ __t('services.f2_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              {{ __t('services.f2_desc') }}
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 relative">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 00-2 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-3">{{ __t('services.f3_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed">
              {{ __t('services.f3_desc') }}
            </p>
          </div>
        </div>
      </div>

      <!-- How it works Section (Cómo Funciona) -->
      <div class="mt-32 border-t border-white/5 pt-20">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">{{ __t('how.tag') }}</span>
          <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-4">{{ __t('how.title') }}</h2>
          <p class="mt-4 text-slate-400 text-sm md:text-base leading-relaxed">
            {{ __t('how.subtitle') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
          <!-- Step 1 -->
          <div class="text-center p-6 relative">
            <div class="w-16 h-16 rounded-full bg-slate-900 border-2 border-emerald-500/30 flex items-center justify-center text-xl font-bold text-white mx-auto mb-6 shadow-lg shadow-emerald-500/5">
              1
            </div>
            <h3 class="text-lg font-bold text-white mb-2">{{ __t('how.s1_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed max-w-xs mx-auto">
              {{ __t('how.s1_desc') }}
            </p>
          </div>

          <!-- Step 2 -->
          <div class="text-center p-6 relative">
            <div class="w-16 h-16 rounded-full bg-slate-900 border-2 border-emerald-500/30 flex items-center justify-center text-xl font-bold text-white mx-auto mb-6 shadow-lg shadow-emerald-500/5">
              2
            </div>
            <h3 class="text-lg font-bold text-white mb-2">{{ __t('how.s2_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed max-w-xs mx-auto">
              {{ __t('how.s2_desc') }}
            </p>
          </div>

          <!-- Step 3 -->
          <div class="text-center p-6 relative">
            <div class="w-16 h-16 rounded-full bg-slate-900 border-2 border-emerald-500/30 flex items-center justify-center text-xl font-bold text-white mx-auto mb-6 shadow-lg shadow-emerald-500/5">
              3
            </div>
            <h3 class="text-lg font-bold text-white mb-2">{{ __t('how.s3_title') }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed max-w-xs mx-auto">
              {{ __t('how.s3_desc') }}
            </p>
          </div>
        </div>
      </div>

      <!-- CTA Section (Únete como profesional) -->
      <div class="mt-32">
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-slate-900 to-slate-950 border border-white/10 p-10 md:p-16 shadow-2xl group">
          <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full filter blur-3xl -mr-20 -mt-20"></div>
          
          <div class="relative z-10 max-w-2xl">
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">{{ __t('cta_pro.tag') }}</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-4 mb-6 leading-tight">
              {{ __t('cta_pro.title') }}
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed mb-8">
              {{ __t('cta_pro.desc') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <x-button :href="wp_login_url()" class="py-3 px-6">
                {{ __t('cta.register') }}
              </x-button>
              <x-button variant="glass" :href="home_url('/pricing')" class="py-3 px-6">
                {{ __t('cta.plans') }}
              </x-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection