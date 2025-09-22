<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use App\Models\DirectConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDirectMessageRequest;
use App\Http\Requests\UpdateDirectMessageRequest;
use App\Events\DirectMessageSent;
use App\Services\CacheService;

class DirectMessageController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(DirectConversation $conversation)
    {
        $this->authorizeAccess($conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }


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

        // carregar o relacionamento sender para o broadcast
        $message->load('sender');

        // limpar cache 
        $this->cacheService->clearDirectConversationCache($conversation->id);

        broadcast(new DirectMessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->load('sender')
        ]);
    }

    protected function authorizeAccess(DirectConversation $conversation)
    {
        if (!$conversation->users->contains(Auth::id())) {
            abort(403, 'Você não faz parte desta conversa.');
        }
    }
}
