<?php

namespace App\Http\Controllers;

use App\Models\DirectConversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CacheService;

class DirectConversationController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    public function index()
    {
        $userId = Auth::id();

        // uso do cache service for better performance
        $conversations = $this->cacheService->getUserDirectConversations($userId);

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
