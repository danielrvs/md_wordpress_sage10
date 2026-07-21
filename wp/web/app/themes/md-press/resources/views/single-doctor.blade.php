@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    <?php
      $rawSpecialty = get_field('medical_specialty');
      if (is_array($rawSpecialty)) {
        $specialty = implode(', ', $rawSpecialty);
      } else {
        $specialty = $rawSpecialty ?: 'General';
      }
      $location = isset($doctor) && isset($doctor['location']) ? $doctor['location'] : (get_post_meta(get_the_ID(), 'medical_location', true) ?: 'No especificada');
      $availability = isset($doctor) && isset($doctor['availability']) ? $doctor['availability'] : (get_post_meta(get_the_ID(), 'medical_availability', true) ?: 'Bajo consulta');
      $rating = isset($doctor) && isset($doctor['rating']) ? floatval($doctor['rating']) : floatval(get_post_meta(get_the_ID(), 'medical_rating', true) ?: 5.0);
      $name = isset($doctor) && isset($doctor['name']) ? $doctor['name'] : get_the_title();
      $avatarUrl = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: (get_post_meta(get_the_ID(), '_mock_avatar_url', true) ?: null);
      
      $doctorId = get_the_ID();
      $currentDate = date('Y-m-d');
      $scheduleService = app(\App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface::class);
      $initialSchedule = null;
      try {
          $initialSchedule = $scheduleService->execute($doctorId, $currentDate);
      } catch (\Exception $e) {
          // Ignorar error
      }
    ?>

    <div class="relative min-h-screen bg-slate-950 text-white overflow-hidden font-sans">
      <!-- Decorative background elements -->
      <div class="absolute top-0 right-1/4 w-96.5 h-96.5 bg-emerald-500/10 rounded-full filter blur-3xl animate-pulse"></div>
      <div class="absolute bottom-10 left-1/4 w-96.5 h-96.5 bg-teal-500/10 rounded-full filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>

      <div class="max-w-5xl mx-auto px-6 py-12 relative z-10">
        <!-- Back Navigation -->
        <a href="{{ home_url('/doctors') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 uppercase tracking-wider mb-8 transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
          </svg>
          Volver al Directorio
        </a>

        <!-- Main Doctor Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
          
          <!-- Left Column (Profile card) -->
          <div class="md:col-span-1 p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-xl flex flex-col items-center text-center">
            <!-- Profile Thumbnail -->
            <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-emerald-500/30 mb-4 bg-slate-900 flex items-center justify-center shadow-lg shadow-emerald-500/10 shrink-0">
              @if ($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $name }}" class="w-full h-full object-cover" />
              @else
                <!-- Doctor Avatar Placeholder -->
                <svg class="w-16 h-16 text-emerald-500/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              @endif
            </div>

            <!-- Name and Title -->
            <h1 class="text-2xl font-extrabold text-white leading-tight mb-1">{!! $name !!}</h1>
            <span class="text-sm font-semibold text-emerald-400 mb-3 bg-emerald-500/10 px-3 py-0.5 rounded-full border border-emerald-500/20">
              {!! $specialty !!}
            </span>

            <!-- Rating Stars -->
            <div class="mb-4 flex flex-col items-center gap-1">
              <?php
                $full_stars = floor($rating);
                $has_half = ($rating - $full_stars) >= 0.5;
              ?>
              <div class="flex items-center gap-0.5 text-amber-400">
                @for ($i = 1; $i <= 5; $i++)
                  @if ($i <= $full_stars)
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  @elseif ($i == $full_stars + 1 && $has_half)
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#475569" stop-opacity="1"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  @else
                    <svg class="w-4 h-4 text-slate-600 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  @endif
                @endfor
              </div>
              <span class="text-xs text-slate-400">Puntuación: {{ number_format($rating, 1) }} / 5.0</span>
            </div>

            <!-- Details Block -->
            <div class="w-full border-t border-white/5 pt-4 text-left space-y-3.5 text-sm text-slate-300">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-emerald-400 shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Ubicación</div>
                  <div class="font-medium text-white">{!! $location !!}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column (Details / Biography & Custom schedules) -->
          <div class="md:col-span-2 space-y-8">
            
            <!-- Biography Card -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-xl">
              <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 border-b border-white/10 pb-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                {{ __t('doctor.profile') }}
              </h2>
              <div class="e-content block-editor-content">
                @if (get_the_content())
                  @php(the_content())
                @else
                  <p class="text-slate-400 italic">{{ __t('doctor.no_desc') }}</p>
                @endif
              </div>
            </div>

            <!-- Schedules / Consultation slots React Card -->
            <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-xl">
              <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 border-b border-white/10 pb-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Horarios Disponibles
              </h2>
              
              <!-- React App Mount Point for Booking -->
              <div id="doctor-booking-root"
                   data-doctor-id="{{ $doctorId }}"
                   data-initial-date="{{ $currentDate }}"
                   data-initial-schedule='{!! json_encode($initialSchedule ? $initialSchedule->toArray() : null) !!}'>
                
                <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                  <svg class="animate-spin h-8 w-8 text-emerald-500 mb-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <p class="text-xs font-semibold">Cargando selector de horarios...</p>
                </div>
              </div>
            </div>

            <!-- Reviews/Comments section -->
            @if (comments_open() || get_comments_number())
              <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xl shadow-xl">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 border-b border-white/10 pb-2">
                  <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  Opiniones de Pacientes
                </h2>
                @php(comments_template())
              </div>
            @endif

          </div>
        </div>
      </div>
    </div>
  @endwhile
@endsection
