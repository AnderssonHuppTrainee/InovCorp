<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Order;
use App\Observers\BookObserver;
use App\Observers\AuthorObserver;
use App\Observers\BookRequestObserver;
use App\Observers\OrderObserver;
use App\Observers\PublisherObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
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

            $schedule->command('carts:check-abandoned')->hourly();
        });
        //art
        View::composer('*', function ($view) {
            $cartCount = 0;
            if (auth()->check()) {
                // Carregar user com a contagem de itens do carrinho
                $user = auth()->user()->load('cart.items');
                $cartCount = $user->cart ? $user->cart->items->count() : 0;
            }
            $view->with('cartCount', $cartCount);
        });

        //Observers
        Book::observe(BookObserver::class);
        Author::observe(AuthorObserver::class);
        Publisher::observe(PublisherObserver::class);
        BookRequest::observe(BookRequestObserver::class);
        Order::observe(OrderObserver::class);

    }
}
