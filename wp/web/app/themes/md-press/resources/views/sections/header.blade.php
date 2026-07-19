<header
  class="sticky top-0 z-50 bg-slate-950/85 backdrop-blur-md border-b border-white/10 px-6 py-4 transition-all duration-300">
  <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
    <!-- Brand / Logo -->
    <div class="flex items-center gap-6">
      <a class="flex items-center gap-2 group text-white font-extrabold text-2xl tracking-wider"
        href="{{ home_url('/') }}">
        <span
          class="bg-gradient-to-r px-12 from-emerald-400 to-teal-500 text-slate-950 w-10 h-10 rounded-xl flex items-center justify-center font-black shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
          MD
        </span>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
        <a href="{{ home_url('/') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5 {{ is_front_page() ? 'text-emerald-400 bg-white/5' : '' }}">
          Inicio
        </a>
        <a href="{{ home_url('/doctors') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5 {{ is_page('doctors') ? 'text-emerald-400 bg-white/5' : '' }}">
          Directorio
        </a>
        <a href="{{ home_url('/about') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5">
          About
        </a>
        <a href="{{ home_url('/blog') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5">
          Blog
        </a>
      </nav>
    </div>

    <!-- Search & Login Controls -->
    <div class="flex items-center gap-4">
      <!-- Search Input -->
      <form action="{{ home_url('/') }}" method="get" class="relative hidden sm:block">
        <input type="text" name="s" placeholder="Buscar..."
          class="bg-white/5 border border-white/10 rounded-full pl-9 pr-4 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 w-48 hover:bg-white/10 focus:bg-slate-900 transition-all" />
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </form>

      <!-- Login / User Button -->
      <a href="{{ wp_login_url() }}"
        class="flex items-center justify-center w-9 h-9 rounded-full bg-white/5 border border-white/10 hover:border-emerald-500/40 text-slate-300 hover:text-emerald-400 hover:bg-emerald-500/10 active:scale-95 transition-all shadow-lg"
        title="Iniciar Sesión">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </a>

      <!-- Mobile Menu Button (Toggle/Icon for style) -->
      <button
        class="md:hidden flex items-center justify-center w-9 h-9 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-emerald-400 hover:bg-white/10 active:scale-95 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
    </div>
  </div>
</header>