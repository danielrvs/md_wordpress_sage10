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
          {{ __t('nav.home') }}
        </a>
        <a href="{{ home_url('/doctors') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5 {{ is_page('doctors') ? 'text-emerald-400 bg-white/5' : '' }}">
          {{ __t('nav.doctors') }}
        </a>
        <a href="{{ home_url('/pricing') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5 {{ is_page('pricing') ? 'text-emerald-400 bg-white/5' : '' }}">
          {{ __t('nav.pricing') }}
        </a>
        <a href="{{ home_url('/blog') }}"
          class="hover:text-emerald-400 transition-colors py-1.5 px-2.5 rounded-lg hover:bg-white/5">
          {{ __t('nav.blog') }}
        </a>
      </nav>
    </div>

    <!-- Controls & Language Switcher -->
    <div class="flex items-center gap-3">
      <!-- Auth Controls (Login / Register / Admin / Logout) -->
      @if(is_user_logged_in())
        <div class="hidden md:flex items-center gap-2">
          <a href="{{ admin_url() }}"
            class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20 text-xs font-semibold active:scale-95 transition-all shadow-md shrink-0 whitespace-nowrap">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>{{ __t('nav.admin') }}</span>
          </a>
          <a href="{{ wp_logout_url() }}"
            class="flex items-center justify-center w-9 h-9 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 active:scale-95 transition-all shadow-md shrink-0"
            title="{{ __t('nav.logout') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </a>
        </div>
      @else
        <div class="hidden md:flex items-center gap-2.5">
          <!-- Iniciar Sesión -->
          <a href="{{ wp_login_url() }}"
            class="px-4 py-2 rounded-xl bg-white/5 border border-white/10 hover:border-emerald-500/40 text-slate-200 hover:text-white hover:bg-white/10 text-xs font-semibold transition-all shadow-sm shrink-0 whitespace-nowrap">
            {{ __t('nav.login') }}
          </a>

          <!-- Registrarse -->
          <a href="{{ home_url('/auth/register') }}"
            class="px-4 py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-bold transition-all shadow-md shadow-emerald-500/20 active:scale-95 shrink-0 whitespace-nowrap">
            {{ __t('nav.register') }}
          </a>
        </div>
      @endif

      <!-- Language Switcher (Flag Icon) - Furthest to the right -->
      @php
        $currentLocale = __locale();
        $otherLocale = $currentLocale === 'es' ? 'en' : 'es';
        $currentUrl = preg_replace('/[?&]lang=[^&]*/', '', $_SERVER['REQUEST_URI'] ?? '/');
        $separator = str_contains($currentUrl, '?') ? '&' : '?';
        $switchUrl = $currentUrl . $separator . 'lang=' . $otherLocale;
      @endphp
      <a href="{{ $switchUrl }}"
        class="flex items-center justify-center w-9 h-9 rounded-full bg-white/5 border border-white/10 hover:border-emerald-500/40 text-base active:scale-95 transition-all shadow-lg shrink-0"
        title="{{ $currentLocale === 'es' ? 'Switch to English' : 'Cambiar a Español' }}">
        @if($currentLocale === 'es')
          <span class="leading-none text-sm">🇪🇸</span>
        @else
          <span class="leading-none text-sm">🇬🇧</span>
        @endif
      </a>

      <!-- Mobile Menu Button -->
      <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
        class="md:hidden flex items-center justify-center w-9 h-9 rounded-full bg-white/5 border border-white/10 text-slate-300 hover:text-emerald-400 hover:bg-white/10 active:scale-95 transition-all shrink-0"
        aria-label="Toggle Navigation">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile Navigation Drawer -->
  <div id="mobile-menu"
    class="hidden md:hidden border-t border-white/10 bg-slate-950/95 backdrop-blur-xl px-6 py-4 mt-4 -mx-6 space-y-2">
    <a href="{{ home_url('/') }}"
      class="block hover:text-emerald-400 text-sm font-medium transition-colors py-2 px-3 rounded-xl hover:bg-white/5 {{ is_front_page() ? 'text-emerald-400 bg-white/5' : 'text-slate-300' }}">
      {{ __t('nav.home') }}
    </a>
    <a href="{{ home_url('/doctors') }}"
      class="block hover:text-emerald-400 text-sm font-medium transition-colors py-2 px-3 rounded-xl hover:bg-white/5 {{ is_page('doctors') ? 'text-emerald-400 bg-white/5' : 'text-slate-300' }}">
      {{ __t('nav.doctors') }}
    </a>
    <a href="{{ home_url('/pricing') }}"
      class="block hover:text-emerald-400 text-sm font-medium transition-colors py-2 px-3 rounded-xl hover:bg-white/5 {{ is_page('pricing') ? 'text-emerald-400 bg-white/5' : 'text-slate-300' }}">
      {{ __t('nav.pricing') }}
    </a>
    <a href="{{ home_url('/blog') }}"
      class="block hover:text-emerald-400 text-sm font-medium transition-colors py-2 px-3 rounded-xl hover:bg-white/5 text-slate-300">
      {{ __t('nav.blog') }}
    </a>

    <div class="border-t border-white/10 pt-3 mt-2 flex flex-col gap-2">
      @if(is_user_logged_in())
        <a href="{{ admin_url() }}"
          class="block text-center text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 py-2 rounded-xl">
          {{ __t('nav.admin') }}
        </a>
        <a href="{{ wp_logout_url() }}"
          class="block text-center text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/20 py-2 rounded-xl">
          {{ __t('nav.logout') }}
        </a>
      @else
        <a href="{{ wp_login_url() }}"
          class="block text-center text-xs font-medium text-slate-300 bg-white/5 border border-white/10 py-2 rounded-xl">
          {{ __t('nav.login') }}
        </a>
        <a href="{{ home_url('/auth/register') }}"
          class="block text-center text-xs font-bold text-slate-950 bg-emerald-400 py-2 rounded-xl">
          {{ __t('nav.register') }}
        </a>
      @endif
    </div>
  </div>
</header>