<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\DirectMessage;

class DirectMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(DirectMessage $message)
    {
        $this->message = $message;
        \Log::info('Broadcasting DirectMessageSent', [
            'message_id' => $message->id,
            'message_body' => $message->body,
            'conversation_id' => $message->direct_conversation_id,
            'sender_id' => $message->sender_id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('direct-conversation.' . $this->message->direct_conversation_id),
        ];
    }
    public function broadcastAs()
    {
        return 'direct-message.sent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'direct_conversation_id' => $this->message->direct_conversation_id,
                'body' => $this->message->body,
                'sender_id' => $this->message->sender_id,
                'created_at' => $this->message->created_at,
                'updated_at' => $this->message->updated_at,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                ]
            ]
        ];
    }
}
