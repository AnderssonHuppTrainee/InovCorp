<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BookRequest;

class ReturnReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $bookRequest;
    /**
     * Create a new notification instance.
     */
    public function __construct(BookRequest $bookRequest)
    {
        $this->bookRequest = $bookRequest;
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
            ->subject('Lembrete: Devolução do livro amanhã!')
            ->greeting('Olá ' . $this->bookRequest->user->name . '!')
            ->line('Este é um lembrete que o livro abaixo deve ser devolvido **amanhã**:')
            ->line('**Livro:** ' . $this->bookRequest->book->title)
            ->line('**Data de devolução:** ' . $this->bookRequest->expected_return_date->format('d/m/Y'))
            ->action('Ver detalhes da requisição', route('requests.show', $this->bookRequest))
            ->line('Agradecemos pela sua atenção!');

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
