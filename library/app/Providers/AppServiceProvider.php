<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //desativa o lazy loading (carregamento preguiçoso) para produção
        //Model::preventLazyLoading();
        // Remminder de devolucoes
        $this->app['router']->aliasMiddleware('admin', \App\Http\Middleware\AdminMiddleware::class);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('reminders:send')
                ->dailyAt('09:00')
                ->timezone('Europe/Lisbon');
        });
    }
}
