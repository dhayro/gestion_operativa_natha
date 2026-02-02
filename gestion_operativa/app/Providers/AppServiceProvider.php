<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\FichaActividadRepository;
use App\Repositories\Contracts\FichaActividadRepositoryContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el repositorio de FichaActividad
        $this->app->bind(
            FichaActividadRepositoryContract::class,
            FichaActividadRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
