<article @php(post_class('max-w-3xl mx-auto px-6 py-12 text-slate-100'))>
  <header class="mb-10 text-center">
    <!-- Back button -->
    <a href="{{ home_url('/blog') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 uppercase tracking-wider mb-6 transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
      </svg>
      Volver al Blog
    </a>

    <!-- Categories -->
    @php($categories = get_the_category())
    @if (!empty($categories))
      <div class="flex justify-center gap-2 mb-4">
        @foreach($categories as $category)
          <span class="text-xs uppercase font-bold tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded border border-emerald-500/20">
            {{ $category->name }}
          </span>
        @endforeach
      </div>
    @endif

    <!-- Title -->
    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-white mb-6 leading-tight">
      {!! $title !!}
    </h1>

    <!-- Meta -->
    <div class="flex items-center justify-center gap-3 text-sm text-slate-400">
      <span class="font-medium text-slate-300">{{ get_the_author() }}</span>
      <span>•</span>
      <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
    </div>
  </header>

  <!-- Featured Image -->
  @if (has_post_thumbnail())
    <div class="mb-12 rounded-2xl overflow-hidden border border-white/10 shadow-2xl relative aspect-[21/9] bg-slate-900">
      {!! get_the_post_thumbnail(null, 'full', ['class' => 'w-full h-full object-cover']) !!}
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 to-transparent"></div>
    </div>
  @endif

  <!-- Gutenberg Content Wrapper -->
  <div class="e-content block-editor-content">
    @php(the_content())
  </div>

  @if ($pagination())
    <footer class="mt-12 pt-6 border-t border-white/10">
      <nav class="page-nav" aria-label="Page">
        {!! $pagination !!}
      </nav>
    </footer>
  @endif

  <!-- Comments Section -->
  @if (comments_open() || get_comments_number())
    <div class="mt-16 pt-12 border-t border-white/10">
      @php(comments_template())
    </div>
  @endif
</article>
