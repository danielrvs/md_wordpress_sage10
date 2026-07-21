<?php

namespace App\Providers;

use App\Domain\Doctors\Repositories\CachedDoctorRepository;
use App\Domain\Doctors\Contracts\DoctorRepositoryInterface;
use App\Domain\Doctors\Repositories\WpQueryDoctorRepository;
use App\Domain\Appointments\Contracts\AppointmentRepositoryInterface;
use App\Domain\Appointments\Repositories\WpDbAppointmentRepository;
use App\Domain\Schedules\Contracts\GenerateDoctorScheduleServiceInterface;
use App\Domain\Schedules\Contracts\ScheduleRepositoryInterface;
use App\Domain\Schedules\Repositories\WpDbScheduleRepository;
use App\Domain\Schedules\Services\CachedGenerateDoctorScheduleService;
use App\Domain\Schedules\Services\GenerateDoctorScheduleService;
use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->booting(function () {
            \Illuminate\Support\Facades\Cache::extend('wordpress', function () {
                return \Illuminate\Support\Facades\Cache::repository(new WordPressCacheStore());
            });
            config([
                'cache.stores.wordpress' => ['driver' => 'wordpress'],
                'cache.default' => 'wordpress'
            ]);
        });

        $this->app->bind(DoctorRepositoryInterface::class, WpQueryDoctorRepository::class);
        $this->app->extend(DoctorRepositoryInterface::class, function ($repository, $app) {
            return new CachedDoctorRepository($repository);
        });

        $this->app->bind(ScheduleRepositoryInterface::class, WpDbScheduleRepository::class);

        $this->app->bind(AppointmentRepositoryInterface::class, WpDbAppointmentRepository::class);
        $this->app->bind(
            \App\Domain\Appointments\Contracts\CreateAppointmentServiceInterface::class,
            \App\Domain\Appointments\Services\CreateAppointmentService::class
        );

        $this->app->singleton(GenerateDoctorScheduleServiceInterface::class, function ($c) {
            return new CachedGenerateDoctorScheduleService(
                new GenerateDoctorScheduleService(
                    $c->make(ScheduleRepositoryInterface::class),
                    $c->make(AppointmentRepositoryInterface::class)
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        if (file_exists($authSetup = dirname(__DIR__) . '/Domain/Auth/setup.php')) {
            require_once $authSetup;
        }

        if (file_exists($appointmentSetup = dirname(__DIR__) . '/Domain/Appointments/setup.php')) {
            require_once $appointmentSetup;
        }

        if (file_exists($doctorSetup = dirname(__DIR__) . '/Domain/Doctors/setup.php')) {
            require_once $doctorSetup;
        }

        if (file_exists($pagesSetup = dirname(__DIR__) . '/Domain/Pages/setup.php')) {
            require_once $pagesSetup;
        }

        if (file_exists($scheduleSetup = dirname(__DIR__) . '/Domain/Schedules/setup.php')) {
            require_once $scheduleSetup;
        }
    }
}
