<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{

    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Room $room): bool
    {

        return $room->created_by === $user->id ||
            $room->hasUser($user) ||
            !$room->private;
    }


    public function create(User $user): bool
    {
        return true;
    }


    public function update(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }


    public function delete(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }


    public function manage(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }


    public function sendMessage(User $user, Room $room): bool
    {
        return $room->hasUser($user);
    }
}