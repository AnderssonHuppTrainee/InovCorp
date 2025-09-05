<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Cart;
use App\Notifications\AbandonedCartNotification;

class CheckAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-abandoned-carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verfica os carrinhos abandonados e envia uma notificação';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificação de carrinhos abandonados...');

        $abandonedCarts = Cart::with(['user', 'items.book'])
            ->whereHas('items', function ($query) {
                $query->where('updated_at', '<=', Carbon::now()->subHour());
            })
            ->whereNull('abandoned_notified_at')
            ->get();

        $this->info('Carrinhos abandonados encontrados: ' . $abandonedCarts->count());

        foreach ($abandonedCarts as $cart) {
            try {
                if ($cart->user && $cart->user->email) {
                    \Log::info('Enviando notificação para carrinho abandonado', [
                        'cart_id' => $cart->id,
                        'user_id' => $cart->user->id,
                        'user_email' => $cart->user->email
                    ]);

                    $cart->user->notify(new AbandonedCartNotification($cart));
                    $cart->update(['abandoned_notified_at' => now()]);

                    $this->info("Notificação enviada para carrinho #{$cart->id}");
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao notificar carrinho abandonado', [
                    'cart_id' => $cart->id,
                    'error' => $e->getMessage()
                ]);
                $this->error("Erro com carrinho #{$cart->id}: " . $e->getMessage());
            }
        }

        $this->info('Processamento concluído!');
    }
}
