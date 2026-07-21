@extends('layouts.app')

@section('content')
  <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 relative z-10">
      <!-- Header -->
      <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full">{{ __t('pricing.tag') }}</span>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-emerald-400 mt-4 leading-tight">
          {{ __t('pricing.title_pre') }} <span class="text-emerald-400">{{ __t('pricing.title_highlight') }}</span>
        </h1>
        <p class="mt-4 text-base text-slate-400 leading-relaxed">
          {{ __t('pricing.subtitle') }}
        </p>
      </div>

      <!-- Pricing Cards Grid -->
      <div class="grid md:grid-cols-3 gap-8 items-stretch max-w-5xl mx-auto">
        <x-pricing-card 
          :title="__t('pricing.basic')"
          price="0€"
          :description="__t('pricing.basic_desc')"
          :ctaText="__t('pricing.btn_free')"
          ctaHref="{{ wp_login_url() }}"
          ctaVariant="secondary"
          :features="[
            __t('pricing.feat_profile'),
            __t('pricing.feat_spec_loc'),
            __t('pricing.feat_no_absences'),
            __t('pricing.feat_no_realtime')
          ]"
        />

        <x-pricing-card 
          :title="__t('pricing.pro')"
          price="29€"
          popular="true"
          :description="__t('pricing.pro_desc')"
          :ctaText="__t('pricing.btn_pro')"
          ctaHref="{{ wp_login_url() }}"
          ctaVariant="primary"
          :features="[
            __t('pricing.feat_all_basic'),
            __t('pricing.feat_schedules'),
            __t('pricing.feat_realtime'),
            __t('pricing.feat_priority_support')
          ]"
        />

        <x-pricing-card 
          :title="__t('pricing.clinics')"
          price="99€"
          :description="__t('pricing.clinics_desc')"
          :ctaText="__t('pricing.btn_contact')"
          ctaHref="mailto:ventas@enterprisemedical.com"
          ctaVariant="secondary"
          :features="[
            __t('pricing.feat_multi_doctors'),
            __t('pricing.feat_central_admin'),
            __t('pricing.feat_api_integration'),
            __t('pricing.feat_sla')
          ]"
        />
      </div>

      <!-- FAQ Section (Extra visual polish) -->
      <div class="mt-24 max-w-4xl mx-auto border-t border-white/10 pt-16">
        <h3 class="text-2xl font-bold text-center text-white mb-10">{{ __t('pricing.faq_title') }}</h3>
        <div class="grid md:grid-cols-2 gap-8 text-sm">
          <div>
            <h4 class="font-semibold text-white mb-2">{{ __t('pricing.faq_q1') }}</h4>
            <p class="text-slate-400">{{ __t('pricing.faq_a1') }}</p>
          </div>
          <div>
            <h4 class="font-semibold text-white mb-2">{{ __t('pricing.faq_q2') }}</h4>
            <p class="text-slate-400">{{ __t('pricing.faq_a2') }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection
