@extends('layouts.app')

@section('content')
  <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 relative z-10">
      <!-- Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full">Planes y Tarifas</span>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-emerald-400 mt-4 leading-tight">
          Planes diseñados para tu <span class="text-emerald-400">crecimiento profesional</span>
        </h1>
        <p class="mt-4 text-base text-slate-400 leading-relaxed">
          Digitaliza tu consulta médica hoy mismo. Elige el plan que mejor se adapte a tus necesidades y empieza a gestionar tus citas de forma eficiente.
        </p>
      </div>

      <!-- Pricing Cards Grid -->
      <div class="grid md:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto">
        
        <!-- Plan 1: Básico -->
        <div class="relative flex flex-col p-8 rounded-3xl bg-white/5 border border-white/10 hover:border-emerald-500/30 transition-all duration-300 group">
          <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-300">Básico</h3>
            <p class="mt-2 text-xs text-slate-400">Presencia esencial en internet.</p>
            <div class="mt-4 flex items-baseline text-white">
              <span class="text-4xl font-extrabold tracking-tight">0€</span>
              <span class="ml-1 text-sm font-semibold text-slate-400">/ mes</span>
            </div>
          </div>
          
          <ul class="space-y-4 mb-8 flex-1 text-sm text-slate-300">
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Perfil en el Directorio Médico
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Especialidad y Ubicación
            </li>
            <li class="flex items-center gap-3 text-slate-500">
              <svg class="h-5 w-5 text-slate-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Gestión de Ausencias y Vacaciones
            </li>
            <li class="flex items-center gap-3 text-slate-500">
              <svg class="h-5 w-5 text-slate-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Reserva de Citas en Tiempo Real
            </li>
          </ul>
          
          <a href="{{ wp_login_url() }}" class="w-full text-center bg-white/10 hover:bg-white/15 text-white font-bold py-3 px-6 rounded-xl active:scale-[0.98] transition-all duration-200 text-sm">
            Comenzar Gratis
          </a>
        </div>

        <!-- Plan 2: Profesional (Popular) -->
        <div class="relative flex flex-col p-8 rounded-3xl bg-gradient-to-b from-white/10 to-white/[0.02] border-2 border-emerald-500 shadow-2xl shadow-emerald-500/5 hover:scale-[1.02] transition-all duration-300 group">
          <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-emerald-500 to-teal-600 text-slate-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Más Popular
          </div>
          
          <div class="mb-6">
            <h3 class="text-lg font-bold text-white">Profesional</h3>
            <p class="mt-2 text-xs text-slate-300">Agenda interactiva y gestión avanzada.</p>
            <div class="mt-4 flex items-baseline text-white">
              <span class="text-4xl font-extrabold tracking-tight">29€</span>
              <span class="ml-1 text-sm font-semibold text-emerald-400">/ mes</span>
            </div>
          </div>
          
          <ul class="space-y-4 mb-8 flex-1 text-sm text-slate-200">
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Todo lo del plan Básico
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              <strong>Gestión de Horarios y Ausencias</strong>
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Citas Web en Tiempo Real (React Component)
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Soporte Prioritario
            </li>
          </ul>
          
          <a href="{{ wp_login_url() }}" class="w-full text-center bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-emerald-500/20 active:scale-[0.98] transition-all duration-200 text-sm">
            Suscribirme Ahora
          </a>
        </div>

        <!-- Plan 3: Clínicas / Enterprise -->
        <div class="relative flex flex-col p-8 rounded-3xl bg-white/5 border border-white/10 hover:border-emerald-500/30 transition-all duration-300 group">
          <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-300">Clínicas</h3>
            <p class="mt-2 text-xs text-slate-400">Solución para centros de salud y múltiples médicos.</p>
            <div class="mt-4 flex items-baseline text-white">
              <span class="text-4xl font-extrabold tracking-tight">99€</span>
              <span class="ml-1 text-sm font-semibold text-slate-400">/ mes</span>
            </div>
          </div>
          
          <ul class="space-y-4 mb-8 flex-1 text-sm text-slate-300">
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Múltiples Médicos Vinculados (hasta 15)
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Panel de Administración Centralizado
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Integración con APIs Externas / EHR
            </li>
            <li class="flex items-center gap-3">
              <svg class="h-5 w-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              SLA del 99.9%
            </li>
          </ul>
          
          <a href="mailto:ventas@enterprisemedical.com" class="w-full text-center bg-white/10 hover:bg-white/15 text-white font-bold py-3 px-6 rounded-xl active:scale-[0.98] transition-all duration-200 text-sm">
            Contactar Ventas
          </a>
        </div>

      </div>

      <!-- FAQ Section (Extra visual polish) -->
      <div class="mt-24 max-w-4xl mx-auto border-t border-white/10 pt-16">
        <h3 class="text-2xl font-bold text-center text-white mb-10">Preguntas Frecuentes</h3>
        <div class="grid md:grid-cols-2 gap-8 text-sm">
          <div>
            <h4 class="font-semibold text-white mb-2">¿Puedo cambiar de plan en cualquier momento?</h4>
            <p class="text-slate-400">Sí, puedes subir o bajar de nivel tu plan cuando quieras. Los cambios se aplicarán de inmediato en tu ciclo de facturación.</p>
          </div>
          <div>
            <h4 class="font-semibold text-white mb-2">¿Cómo funciona la reserva de citas en tiempo real?</h4>
            <p class="text-slate-400">El sistema genera automáticamente tramos horarios libres y ocupados en base a tu configuración. Los pacientes reservan y el sistema actualiza tu disponibilidad sin solapamientos.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection
