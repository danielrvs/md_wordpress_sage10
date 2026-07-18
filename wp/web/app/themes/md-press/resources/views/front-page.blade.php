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
          Encuentra y Conecta con <span class="text-emerald-400">Especialistas Médicos</span>
        </h1>
        <p class="mt-6 text-lg text-slate-400 leading-relaxed">
          Accede al directorio de profesionales de la salud más avanzado. Búsquedas rápidas, perfiles verificados y citas
          simplificadas.
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
          <div class="text-sm text-emerald-400 font-semibold mt-1">Médicos Verificados</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">99.8%</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">Satisfacción</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">120+</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">Especialidades</div>
        </div>
        <div class="text-center">
          <div class="text-3xl font-bold text-white">24/7</div>
          <div class="text-sm text-emerald-400 font-semibold mt-1">Soporte Médico</div>
        </div>
      </div>

      <!-- Blog Section in Homepage -->
      <div class="mt-24 border-t border-white/5 pt-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
          <div>
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Artículos Destacados</span>
            <h2 class="text-3xl font-bold text-white mt-2">Nuestro Blog Médico</h2>
          </div>
          <a href="{{ home_url('/blog') }}" class="mt-4 md:mt-0 flex items-center gap-1.5 text-sm font-semibold text-emerald-400 hover:text-emerald-300 transition-colors group">
            Ver todos los artículos
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
          </a>
        </div>

        <!-- Recent Posts Query Loop -->
        @php
          $recent_posts = new \WP_Query([
            'post_type' => 'post',
            'posts_per_page' => 3,
            'post_status' => 'publish'
          ]);
        @endphp

        @if (!$recent_posts->have_posts())
          <div class="p-8 rounded-2xl bg-white/5 border border-white/10 text-center text-slate-400">
            <p class="text-sm">No hay artículos publicados en este momento.</p>
          </div>
        @else
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @while ($recent_posts->have_posts()) @php($recent_posts->the_post())
              <article class="flex flex-col rounded-2xl bg-white/5 border border-white/10 overflow-hidden hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1">
                <a href="{{ get_permalink() }}" class="block aspect-video relative overflow-hidden bg-slate-900">
                  @if (has_post_thumbnail())
                    {!! get_the_post_thumbnail(null, 'medium_large', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']) !!}
                  @else
                    <div class="w-full h-full bg-gradient-to-br from-emerald-950/80 to-slate-900 flex items-center justify-center p-6 group-hover:scale-105 transition-transform duration-300">
                      <svg class="w-10 h-10 text-emerald-500/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.871 4A17.926 17.926 0 003 12c0 2.871.67 5.587 1.871 8m14.13 0a17.93 17.93 0 001.87-8c0-2.871-.67-5.587-1.871-8m-14.13 0A17.93 17.93 0 0112 3c2.871 0 5.587.67 8 1.87M9.07 9h5.86M9.07 13h5.86m-4.93 4h4" />
                      </svg>
                    </div>
                  @endif
                </a>
                <div class="p-5 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 class="text-lg font-bold text-white mb-2 line-clamp-2 hover:text-emerald-300 transition-colors">
                      <a href="{{ get_permalink() }}">{!! get_the_title() !!}</a>
                    </h3>
                    <p class="text-slate-400 text-xs line-clamp-3 mb-4 leading-relaxed">
                      {!! wp_strip_all_tags(get_the_excerpt()) !!}
                    </p>
                  </div>
                  <div class="flex items-center justify-between text-[11px] text-slate-500 pt-3 border-t border-white/5">
                    <span>{{ get_the_date() }}</span>
                    <a href="{{ get_permalink() }}" class="text-emerald-400 font-semibold hover:text-emerald-300 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                      Leer más &rarr;
                    </a>
                  </div>
                </div>
              </article>
            @endwhile
            @php(wp_reset_postdata())
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection