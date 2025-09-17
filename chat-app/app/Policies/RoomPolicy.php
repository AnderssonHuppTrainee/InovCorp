<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Usuários autenticados podem ver a lista de salas
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Room $room): bool
    {
        // Salas públicas: qualquer usuário pode ver
        if (!$room->private) {
            return true;
        }

        // Salas privadas: apenas membros podem ver
        return $room->users->contains($user) || $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Usuários autenticados podem criar salas
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        // Apenas o criador da sala pode editá-la
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Room $room): bool
    {
        // Apenas o criador da sala pode deletá-la
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can send messages to the room.
     */
    public function sendMessage(User $user, Room $room): bool
    {
        // Carregar relacionamentos se não estiverem carregados
        if (!$room->relationLoaded('users')) {
            $room->load('users');
        }

        // Salas públicas: qualquer usuário pode enviar mensagens
        if (!$room->private) {
            return true;
        }

        // Salas privadas: apenas membros podem enviar mensagens
        return $room->users->contains($user) || $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can add users to the room.
     */
    public function addUser(User $user, Room $room): bool
    {
        // Apenas o criador da sala pode adicionar usuários
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can remove users from the room.
     */
    public function removeUser(User $user, Room $room): bool
    {
        // Apenas o criador da sala pode remover usuários
        return $room->created_by === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Room $room): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Room $room): bool
    {
        return false;
    }
}
