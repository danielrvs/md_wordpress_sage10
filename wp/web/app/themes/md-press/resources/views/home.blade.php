@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
  <!-- Decorative background elements -->
  <div class="absolute top-0 right-1/4 w-96.5 h-96.5 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
  <div class="absolute bottom-10 left-1/4 w-96.5 h-96.5 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>

  <div class="max-w-6xl mx-auto px-6 py-16 relative z-10">
    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-16">
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 backdrop-blur-md mb-4">
        Noticias & Consejos
      </span>
      <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300">
        Blog de <span class="text-emerald-400">Salud & Bienestar</span>
      </h1>
      <p class="mt-4 text-slate-400 text-sm md:text-base">
        Artículos informativos redactados por profesionales de la salud. Mantente al día con consejos médicos prácticos y avances de la medicina.
      </p>
    </div>

    <!-- Blog Posts Grid -->
    @if (! have_posts())
      <div class="text-center py-20 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-xl">
        <svg class="mx-auto h-12 w-12 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 2v6h6" />
        </svg>
        <h3 class="mt-4 text-lg font-semibold text-white">No hay artículos disponibles</h3>
        <p class="mt-2 text-slate-400 text-sm">Pronto publicaremos nuevo contenido de interés médico.</p>
      </div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @while(have_posts()) @php(the_post())
          <article class="flex flex-col rounded-2xl bg-white/5 border border-white/10 overflow-hidden hover:border-emerald-500/30 transition-all duration-300 group hover:-translate-y-1 shadow-xl">
            <!-- Post Thumbnail / Fallback image -->
            <a href="{{ get_permalink() }}" class="block aspect-video relative overflow-hidden bg-slate-900">
              @if (has_post_thumbnail())
                {!! get_the_post_thumbnail(null, 'medium_large', ['class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300']) !!}
              @else
                <!-- Fallback abstract medical SVG background -->
                <div class="w-full h-full bg-gradient-to-br from-emerald-950/80 to-slate-900 flex items-center justify-center p-6 group-hover:scale-105 transition-transform duration-300">
                  <svg class="w-12 h-12 text-emerald-500/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.871 4A17.926 17.926 0 003 12c0 2.871.67 5.587 1.871 8m14.13 0a17.93 17.93 0 001.87-8c0-2.871-.67-5.587-1.871-8m-14.13 0A17.93 17.93 0 0112 3c2.871 0 5.587.67 8 1.87M9.07 9h5.86M9.07 13h5.86m-4.93 4h4" />
                  </svg>
                </div>
              @endif
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 to-transparent"></div>
            </a>

            <!-- Post Content -->
            <div class="flex-1 p-6 flex flex-col justify-between">
              <div>
                <!-- Category badge -->
                @php($categories = get_the_category())
                @if (!empty($categories))
                  <span class="inline-block text-[10px] uppercase font-bold tracking-wider text-emerald-400 mb-3 bg-emerald-500/10 px-2 py-0.5 rounded">
                    {{ $categories[0]->name }}
                  </span>
                @endif

                <h2 class="text-xl font-bold text-white mb-2 line-clamp-2 hover:text-emerald-300 transition-colors">
                  <a href="{{ get_permalink() }}">{!! get_the_title() !!}</a>
                </h2>

                <p class="text-slate-400 text-sm line-clamp-3 mb-4 leading-relaxed">
                  {!! wp_strip_all_tags(get_the_excerpt()) !!}
                </p>
              </div>

              <!-- Post Meta / CTA -->
              <div class="pt-4 border-t border-white/5 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2">
                  <span class="font-medium text-slate-300">{{ get_the_author() }}</span>
                  <span>•</span>
                  <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
                </div>
                <a href="{{ get_permalink() }}" class="flex items-center gap-1 font-semibold text-emerald-400 hover:text-emerald-300 group-hover:translate-x-1 transition-transform">
                  Leer más
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                  </svg>
                </a>
              </div>
            </div>
          </article>
        @endwhile
      </div>

      <!-- Pagination -->
      <div class="mt-16 flex justify-center pagination-links">
        {!! paginate_links([
          'prev_text' => '&laquo;',
          'next_text' => '&raquo;',
          'type'      => 'plain'
        ]) !!}
      </div>
    @endif
  </div>
</div>
@endsection
