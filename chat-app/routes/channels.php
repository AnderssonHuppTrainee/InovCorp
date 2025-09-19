<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;
use App\Models\DirectConversation;

// Canal público para salas (não requer autenticação)
Broadcast::channel('rooms.{roomId}', function ($user, $roomId) {
    // Verificar se o usuário tem acesso à sala
    $room = Room::find($roomId);
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

// Canal privado para conversas diretas
Broadcast::channel('direct-conversation.{conversationId}', function ($user, $conversationId) {
    // Verificar se o usuário faz parte da conversa
    $conversation = DirectConversation::find($conversationId);
    if (!$conversation) {
        return false;
    }

    // Verificar se o usuário é participante da conversa
    return $conversation->users->contains($user->id);
});
