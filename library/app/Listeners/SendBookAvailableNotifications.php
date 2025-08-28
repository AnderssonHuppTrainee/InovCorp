<?php

namespace App\Listeners;

use App\Events\BookAvailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BookNotification;
use App\Notifications\BookAvailableNotification;

class SendBookAvailableNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookAvailable $event): void
    {
        $notifications = BookNotification::where('book_id', $event->book->id)->get();

        foreach ($notifications as $notification) {
            $notification->user->notify(new BookAvailableNotification($event->book));

            //remover para ñ notificar de novo
            $notification->delete();
        }
    }
}
