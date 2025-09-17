<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;

// Canal público para salas (não requer autenticação)
Broadcast::channel('rooms.{roomId}', function ($user, $roomId) {
    // Verificar se o usuário tem acesso à sala
    $room = \App\Models\Room::find($roomId);
    if (!$room) {
        return false;
    }

    // Salas públicas: qualquer usuário pode acessar
    if (!$room->private) {
        return true;
    }

    // Salas privadas: apenas membros podem acessar
    return $room->users->contains($user) || $room->created_by === $user->id;
});
