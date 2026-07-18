<?php

namespace App\Providers;

use App\Domain\Doctors\Repositories\CachedDoctorRepository;
use App\Domain\Doctors\Repositories\DoctorRepositoryInterface;
use App\Domain\Doctors\Repositories\WpQueryDoctorRepository;
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
        $this->app->bind(DoctorRepositoryInterface::class, WpQueryDoctorRepository::class);
        $this->app->extend(DoctorRepositoryInterface::class, function ($repository, $app) {
            return new CachedDoctorRepository($repository);
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

        if (file_exists($doctorSetup = dirname(__DIR__) . '/Domain/Doctors/setup.php')) {
            require_once $doctorSetup;
        }
    }
}
