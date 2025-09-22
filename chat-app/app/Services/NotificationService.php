<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use App\Models\Message;
use App\Models\Room;

class NotificationService
{
    //mencao notificacao
    public function createMentionNotification(User $mentionedUser, User $fromUser, Message $message, Room $room)
    {
        return AppNotification::create([
            'user_id' => $mentionedUser->id,
            'from_user_id' => $fromUser->id,
            'type' => 'mention',
            'title' => 'Você foi mencionado',
            'message' => "{$fromUser->name} mencionou você em {$room->name}",
            'data' => [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'message_id' => $message->id,
                'message_body' => $message->body,
            ],
        ]);
    }

    //mençao de nova msg
    public function createMessageNotification(User $user, User $fromUser, Message $message, Room $room)
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'type' => 'message',
            'title' => 'Nova mensagem',
            'message' => "{$fromUser->name} enviou uma mensagem em {$room->name}",
            'data' => [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'message_id' => $message->id,
            ],
        ]);
    }

    //mencao de friend request
    public function createFriendshipRequestNotification(User $user, User $fromUser)
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'type' => 'friendship_request',
            'title' => 'Solicitação de amizade',
            'message' => "{$fromUser->name} enviou uma solicitação de amizade",
            'data' => [
                'from_user_name' => $fromUser->name,
            ],
        ]);
    }

    //accept notificacation
    public function createFriendshipAcceptedNotification(User $user, User $fromUser)
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'type' => 'friendship_accepted',
            'title' => 'Amizade aceita',
            'message' => "{$fromUser->name} aceitou sua solicitação de amizade",
            'data' => [
                'from_user_name' => $fromUser->name,
            ],
        ]);
    }


    public function createRoomInviteNotification(User $user, User $fromUser, Room $room, $invite)
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'from_user_id' => $fromUser->id,
            'type' => 'room_invite',
            'title' => 'Convite para sala',
            'message' => "{$fromUser->name} convidou você para a sala {$room->name}",
            'data' => [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'invite_id' => $invite->id,
                'invite_token' => $invite->invite_token,
            ],
        ]);
    }


    public function processMentions(Message $message, Room $room)
    {
        $body = $message->body;
        $mentionedUsers = [];

        // encontra  mencoes no formato @username
        preg_match_all('/@(\w+)/', $body, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $username) {
                $user = User::where('name', 'LIKE', "%{$username}%")
                    ->orWhere('email', 'LIKE', "%{$username}%")
                    ->first();

                if ($user && $user->id !== $message->sender_id) {

                    if ($room->users()->where('user_id', $user->id)->exists() || $room->created_by === $user->id) {
                        $this->createMentionNotification($user, $message->sender, $message, $room);
                        $mentionedUsers[] = $user;
                    }
                }
            }
        }

        return $mentionedUsers;
    }


    public function notifyRoomMembers(Message $message, Room $room, array $excludeUsers = [])
    {
        $excludeUsers[] = $message->sender_id;

        $roomMembers = $room->users()
            ->whereNotIn('user_id', $excludeUsers)
            ->get();

        foreach ($roomMembers as $member) {
            $this->createMessageNotification($member, $message->sender, $message, $room);
        }
    }
}