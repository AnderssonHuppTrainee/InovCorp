<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageReactionController extends Controller
{
    /**
     * Adicionar ou remover reação.
     */
    public function toggle(Request $request, Message $message)
    {
        $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        $emoji = $request->emoji;
        $userId = Auth::id();

        // Verificar se a reação já existe
        $existingReaction = MessageReaction::where([
            'message_id' => $message->id,
            'user_id' => $userId,
            'emoji' => $emoji,
        ])->first();

        if ($existingReaction) {
            // Remover reação existente
            $existingReaction->delete();
            $action = 'removed';
        } else {
            // Adicionar nova reação
            MessageReaction::create([
                'message_id' => $message->id,
                'user_id' => $userId,
                'emoji' => $emoji,
            ]);
            $action = 'added';
        }

        // Retornar reações agrupadas
        $reactions = MessageReaction::getGroupedReactions($message->id);

        return response()->json([
            'action' => $action,
            'reactions' => $reactions,
        ]);
    }

    /**
     * Obter reações de uma mensagem.
     */
    public function index(Message $message)
    {
        $reactions = MessageReaction::getGroupedReactions($message->id);

        return response()->json($reactions);
    }
}