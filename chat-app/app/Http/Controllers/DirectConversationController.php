<?php

namespace App\Http\Controllers;

use App\Models\DirectConversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversations = $user->directConversations()->with('users')->get();
        /*$conversations = DirectConversation::whereHas('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })
            ->with([
                'users' => function ($q) use ($user) {
                    $q->where('users.id', '!=', $user->id); // traz só o "outro" usuário
                },
                'lastMessage.sender'
            ])
            ->get();*/

        return response()->json($conversations);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|not_in:' . Auth::id(),
        ]);

        $authUser = Auth::user();
        $otherUserId = $request->user_id;

        // verifica se ja existe uma conversa entre os dois
        $conversation = DirectConversation::whereHas('users', fn($q)
            => $q->where('user_id', $authUser->id))
            ->whereHas('users', fn($q)
                => $q->where('user_id', $otherUserId))
            ->first();

        if (!$conversation) {
            $conversation = DirectConversation::create([
                'created_by' => $authUser->id
            ]);

            $conversation->users()->attach([$authUser->id, $otherUserId]);
        }

        return response()->json([
            'conversation' => $conversation->load('users')
        ]);
    }
}
