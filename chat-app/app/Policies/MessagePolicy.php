<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;


    public function create(User $user, Room $room = null): bool
    {
        // If room is provided, check if user is a member
        if ($room) {
            return $room->users->contains($user->id) || $room->created_by === $user->id;
        }

        return true;
    }

    public function view(User $user, Message $message): bool
    {

        if ($message->room_id) {
            $room = Room::find($message->room_id);
            return $room && ($room->users->contains($user->id) || $room->created_by === $user->id);
        }


        if ($message->direct_conversation_id) {
            $conversation = $message->directConversation;
            return $conversation && $conversation->users->contains($user->id);
        }

        return false;
    }


    public function update(User $user, Message $message): bool
    {

        return $message->sender_id === $user->id;
    }


    public function delete(User $user, Message $message): bool
    {

        return $message->sender_id === $user->id;
    }
}


