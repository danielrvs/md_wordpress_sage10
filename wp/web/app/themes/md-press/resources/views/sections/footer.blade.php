<footer class="bg-slate-950 border-t border-white/10 text-slate-400 font-sans pt-12 pb-8 px-6 relative overflow-hidden">
  <div
    class="absolute bottom-0 left-1/2 -translate-x-1/2 w-3/4 h-32 bg-emerald-500/5 filter blur-3xl pointer-events-none" />

  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 mb-10 relative z-10">
    {/* Brand Info */}
    <div class="space-y-3 md:col-span-1">
      <a class="flex items-center gap-2 text-white font-extrabold text-xl tracking-wider" href="{{ home_url('/') }}">
        <span
          class="bg-gradient-to-r px-3 py-1 from-emerald-400 to-teal-500 text-slate-950 rounded-xl font-black shadow-md shadow-emerald-500/20">
          MD
        </span>
        <span>MD Press</span>
      </a>
      <p class="text-xs text-slate-400 leading-relaxed">
        Plataforma médica enterprise para la búsqueda de especialistas y reserva de citas de salud en tiempo real.
      </p>
    </div>

    {/* Navigation Links */}
    <div class="space-y-3">
      <h4 class="text-xs font-bold text-white uppercase tracking-wider">{{ __t('footer.nav_title', 'Navegación') }}</h4>
      <ul class="space-y-2 text-xs">
        <li>
          <a href="{{ home_url('/') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.home', 'Inicio') }}
          </a>
        </li>
        <li>
          <a href="{{ home_url('/doctors') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.doctors', 'Directorio Médico') }}
          </a>
        </li>
        <li>
          <a href="{{ home_url('/pricing') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.pricing', 'Planes y Tarifas') }}
          </a>
        </li>
        <li>
          <a href="{{ home_url('/blog') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.blog', 'Blog de Salud') }}
          </a>
        </li>
      </ul>
    </div>

    {/* Patient Portal Links */}
    <div class="space-y-3">
      <h4 class="text-xs font-bold text-white uppercase tracking-wider">
        {{ __t('footer.portal_title', 'Portal de Pacientes') }}</h4>
      <ul class="space-y-2 text-xs">
        <li>
          <a href="{{ home_url('/patient-dashboard') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.portal', 'Mi Portal') }}
          </a>
        </li>
        <li>
          <a href="{{ wp_login_url() }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.login', 'Iniciar Sesión') }}
          </a>
        </li>
        <li>
          <a href="{{ home_url('/auth/register') }}" class="hover:text-emerald-400 transition-colors">
            {{ __t('nav.register', 'Crear Cuenta') }}
          </a>
        </li>
      </ul>
    </div>

    {/* Legal & Support */}
    <div class="space-y-3">
      <h4 class="text-xs font-bold text-white uppercase tracking-wider">
        {{ __t('footer.support_title', 'Soporte y Legal') }}</h4>
      <ul class="space-y-2 text-xs">
        <li class="flex items-center gap-2 text-slate-400">
          <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>Atención al Cliente 24/7</span>
        </li>
        <li class="text-slate-400">Términos y Condiciones</li>
        <li class="text-slate-400">Política de Privacidad</li>
      </ul>
    </div>
  </div>

  {/* Copyright Bottom Bar */}
  <div
    class="max-w-6xl mx-auto pt-6 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500 relative z-10">
    <p>© {{ date('Y') }} MD Press Enterprise. Todos los derechos reservados.</p>
    <div class="flex items-center gap-4">
      <span>Español</span>
      <span>•</span>
      <span>v1.0.0</span>
    </div>
  </div>
</footer>