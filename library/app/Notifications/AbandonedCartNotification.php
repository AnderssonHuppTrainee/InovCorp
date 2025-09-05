<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Cart;

class AbandonedCartNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $cart;
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Precisa de ajuda com seus livros?')
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Notamos que você adicionou alguns livros ao seu carrinho mas ainda não finalizou sua compra.')
            ->line('Se você está com dificuldades ou tem alguma dúvida, estamos aqui para ajudar!')
            ->action('Finalizar Compra', url('/cart'))
            ->line('Seus itens selecionados:');

        foreach ($this->cart->items as $item) {
            $mail->line("• {$item->book->name} - €" . number_format($item->book->price, 2));
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'cart_id' => $this->cart->id,
            'message' => 'Lembrete de carrinho abandonado',
            'items_count' => $this->cart->items->count(),
        ];
    }

}
