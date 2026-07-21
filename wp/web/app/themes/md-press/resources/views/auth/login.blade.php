<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso Profesional - Directorio Médico</title>
  @php(wp_head())
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-white antialiased overflow-hidden relative flex items-center justify-center">

  <!-- Decorative background blobs -->
  <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
  <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

  <div class="w-full max-w-md p-6 relative z-10">
    <!-- Brand / Logo -->
    <div class="flex flex-col items-center mb-8">
      <a href="{{ home_url('/') }}" class="flex items-center gap-2 group text-white font-extrabold text-3xl tracking-wider mb-2">
        <span class="bg-gradient-to-r px-10 from-emerald-400 to-teal-500 text-slate-950 w-12 h-12 rounded-xl flex items-center justify-center font-black shadow-lg shadow-emerald-500/20">
          MD
        </span>
      </a>
      <h2 class="text-sm font-semibold tracking-widest text-emerald-400 uppercase">Directorio Médico</h2>
    </div>

    <!-- Login Card -->
    <div class="p-8 rounded-3xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-2xl relative group">
      <!-- Glow effect on hover -->
      <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl blur opacity-10 group-hover:opacity-15 transition duration-300"></div>

      <div class="relative">
        <h3 class="text-xl font-bold text-center mb-6">{{ __t('login.title') }}</h3>

        @if($error)
          <div class="mb-5 p-4 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-semibold leading-relaxed">
            {{ $error }}
          </div>
        @endif

        <form method="POST" action="{{ $actionUrl }}" class="space-y-5">
          {!! wp_nonce_field('custom_login_action', 'custom_login_nonce', true, false) !!}

          <div>
            <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __t('login.username') }}</label>
            <input 
              type="text" 
              name="log" 
              id="username" 
              value="{{ $username }}"
              required 
              placeholder="ej. dr_alejandro"
              class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
            />
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __t('login.password') }}</label>
            </div>
            <input 
              type="password" 
              name="pwd" 
              id="password" 
              required 
              placeholder="••••••••"
              class="w-full bg-slate-900/60 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
            />
          </div>

          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer select-none">
              <input type="checkbox" name="rememberme" id="rememberme" class="rounded border-white/10 bg-slate-900/60 text-emerald-500 focus:ring-emerald-500/20 w-4 h-4">
              {{ __t('login.remember') }}
            </label>
          </div>

          <div class="pt-2">
            <x-button type="submit" class="w-full py-3">
              {{ __t('login.submit') }}
            </x-button>
          </div>
        </form>
      </div>
    </div>

    <!-- Back to home link -->
    <p class="text-center text-xs text-slate-500 mt-6">
      <a href="{{ home_url('/') }}" class="hover:text-emerald-400 transition-colors">
        &larr; {{ __t('login.back') }}
      </a>
    </p>
  </div>

  @php(wp_footer())
</body>
</html>
