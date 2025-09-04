<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use App\Models\CartItem;
use View;

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

        View::composer('*', function ($view) {
            $cartCount = 0;
            if (auth()->check()) {
                // Carregar usuário com a contagem de itens do carrinho
                $user = auth()->user()->load('cart.items');
                $cartCount = $user->cart ? $user->cart->items->count() : 0;
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
