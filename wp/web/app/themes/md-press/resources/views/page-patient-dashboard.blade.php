@extends('layouts.app')

@section('content')
  @if(!is_user_logged_in())
    <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans flex items-center justify-center p-6">
      <div class="max-w-md w-full text-center space-y-6 bg-slate-900/60 border border-white/10 p-8 rounded-3xl backdrop-blur-xl shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 mx-auto flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
          </svg>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-white">{{ __t('portal.auth_required_title', 'Acceso Restringido') }}</h2>
          <p class="mt-2 text-slate-400 text-sm">
            {{ __t('portal.auth_required_desc', 'Debes iniciar sesión con tu cuenta de paciente para acceder a tu portal y gestionar tus citas médicas.') }}
          </p>
        </div>
        <a href="{{ wp_login_url() }}"
           class="inline-flex items-center justify-center w-full py-3 px-6 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 font-bold text-sm transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
          {{ __t('nav.login', 'Iniciar Sesión') }}
        </a>
      </div>
    </div>
  @else
    <div id="patient-dashboard-root">
      <!-- Loading fallback before React SPA mounts -->
      <div class="min-h-screen bg-slate-950 flex flex-col items-center justify-center py-20 text-slate-400 font-sans">
        <svg class="animate-spin h-10 w-10 text-emerald-500 mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm font-semibold tracking-wide">{{ __t('portal.loading', 'Cargando tu Portal de Paciente...') }}</p>
      </div>
    </div>
  @endif
@endsection
