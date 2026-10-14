<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Ids numéricos: un valor como "abc" responde 404 en vez de un error 500 por el tipo int del controlador
        foreach (['id', 'eventoId', 'categoriaId', 'programaId'] as $parametro) {
            \Illuminate\Support\Facades\Route::pattern($parametro, '[0-9]+');
        }

        if ($this->app->environment('local', 'testing')) {
            Model::preventLazyLoading();
        }

        \App\Infrastructure\Pagos\Models\Pago::observe(\App\Infrastructure\Pagos\Observers\PagoObserver::class);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        RateLimiter::for('forgot-password', function ($request) {
            return Limit::perMinutes(10, 3)->by($request->ip());
        });
    }
}
