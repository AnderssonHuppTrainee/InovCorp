<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        // Usuário pode ver a sala se:
        // 1. É o criador da sala
        // 2. É membro da sala
        // 3. A sala é pública
        return $room->created_by === $user->id ||
            $room->hasUser($user) ||
            !$room->private;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Qualquer usuário autenticado pode criar salas
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can manage the room (add/remove users, send invites).
     */
    public function manage(User $user, Room $room): bool
    {
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can send messages in the room.
     */
    public function sendMessage(User $user, Room $room): bool
    {
        return $room->hasUser($user);
    }
}