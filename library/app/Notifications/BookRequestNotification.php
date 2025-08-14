<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BookRequest;

class BookRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $bookRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(BookRequest $bookRequest)
    {
        $this->bookRequest = $bookRequest->load(['book', 'user']);
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

        return (new MailMessage)
            ->subject('Nova Requisição de Livro - #' . $this->bookRequest->number)
            ->greeting('Olá Administrador!')
            ->line('Uma nova requisição de livro foi realizada!')
            ->line('**Livro:** ' . $this->bookRequest->book->name)
            ->line('**Requisitante:** ' . $this->bookRequest->user->name)
            ->line('**Data da Requisição:** ' . $this->bookRequest->request_date->format('d/m/Y'))
            ->line('**Data Prevista para Devolução:** ' . $this->bookRequest->expected_return_date->format('d/m/Y'))
            ->action('Ver Requisição', route('requests.show', $this->bookRequest->book_id))
            ->line('Por favor, revise a requisição e aprove ou rejeite conforme necessário.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
