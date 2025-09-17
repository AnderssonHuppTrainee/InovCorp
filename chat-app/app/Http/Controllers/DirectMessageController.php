<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use App\Models\DirectConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDirectMessageRequest;
use App\Http\Requests\UpdateDirectMessageRequest;

class DirectMessageController extends Controller
{
    // Lista mensagens de uma conversa
    public function index(DirectConversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Enviar mensagem em uma conversa
    public function store(Request $request, DirectConversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return response()->json([
            'message' => $message->load('sender')
        ]);
    }

    // Garante que o usuário autenticado faz parte da conversa
    protected function authorizeAccess(DirectConversation $conversation)
    {
        if (!$conversation->users->contains(Auth::id())) {
            abort(403, 'Você não faz parte desta conversa.');
        }
    }
}
