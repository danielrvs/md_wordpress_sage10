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
        <x-pricing-card 
          title="Básico"
          price="0€"
          description="Presencia esencial en internet."
          ctaText="Comenzar Gratis"
          ctaHref="{{ wp_login_url() }}"
          ctaVariant="secondary"
          :features="[
            'Perfil en el Directorio Médico',
            'Especialidad y Ubicación',
            '-Gestión de Ausencias y Vacaciones',
            '-Reserva de Citas en Tiempo Real'
          ]"
        />

        <x-pricing-card 
          title="Profesional"
          price="29€"
          popular="true"
          description="Agenda interactiva y gestión avanzada."
          ctaText="Suscribirme Ahora"
          ctaHref="{{ wp_login_url() }}"
          ctaVariant="primary"
          :features="[
            'Todo lo del plan Básico',
            '<strong>Gestión de Horarios y Ausencias</strong>',
            'Citas Web en Tiempo Real',
            'Soporte Prioritario'
          ]"
        />

        <x-pricing-card 
          title="Clínicas"
          price="99€"
          description="Solución para centros de salud y múltiples médicos."
          ctaText="Contactar Ventas"
          ctaHref="mailto:ventas@enterprisemedical.com"
          ctaVariant="secondary"
          :features="[
            'Múltiples Médicos Vinculados (hasta 15)',
            'Panel de Administración Centralizado',
            'Integración con APIs Externas / EHR',
            'SLA del 99.9%'
          ]"
        />
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
