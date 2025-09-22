<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Http\Requests\StoreFriendshipRequest;
use App\Http\Requests\UpdateFriendshipRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\CacheService;



class FriendshipController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }

            // tenta usar cache primeiro
            try {
                $friends = $this->cacheService->getUserFriends($user->id);
                return response()->json($friends);
            } catch (\Exception $cacheError) {
                Log::warning('Erro ao obter amigos do cache, buscando diretamente', [
                    'error' => $cacheError->getMessage(),
                    'user_id' => $user->id
                ]);

                // Fallback: busca no banco
                $friends = $user->friends()
                    ->select('id', 'name', 'email', 'avatar', 'handle')
                    ->orderBy('name')
                    ->get();

                return response()->json($friends);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao obter lista de amigos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }


    public function requests()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }


            $requests = Friendship::where('friend_id', $user->id)
                ->where('status', 'pending')
                ->with(['user:id,name,email,avatar'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requests);
        } catch (\Exception $e) {
            Log::error('Erro ao obter solicitações de amizade', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    public function sendRequest(User $user)
    {
        $authUser = Auth::id();

        if ($authUser === $user->id) {
            return response()->json(['message' => 'Você não pode adicionar a si mesmo.'], 400);
        }


        $exists = Friendship::where(function ($q) use ($authUser, $user) {
            $q->where('user_id', $authUser)->where('friend_id', $user->id);
        })->orWhere(function ($q) use ($authUser, $user) {
            $q->where('user_id', $user->id)->where('friend_id', $authUser);
        })->first();

        if ($exists) {
            return response()->json(['message' => 'Amizade já existente ou pendente.'], 400);
        }

        $friendship = Friendship::create([
            'user_id' => $authUser,
            'friend_id' => $user->id,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Pedido de amizade enviado.', 'friendship' => $friendship]);
    }


    public function accept(Friendship $friendship)
    {
        if ($friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $friendship->update(['status' => 'accepted']);


        try {
            $this->cacheService->clearFriendshipCache($friendship->user_id);
            $this->cacheService->clearFriendshipCache($friendship->friend_id);
        } catch (\Exception $cacheError) {
            Log::warning('Erro ao limpar cache de amizade', [
                'error' => $cacheError->getMessage(),
                'user_id' => $friendship->user_id,
                'friend_id' => $friendship->friend_id
            ]);
        }

        return response()->json(['message' => 'Amizade aceita.', 'friendship' => $friendship]);
    }

    public function reject(Friendship $friendship)
    {
        if ($friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $friendship->update(['status' => 'rejected']);
        return response()->json(['message' => 'Pedido de amizade rejeitado.']);
    }


    public function remove(Friendship $friendship)
    {
        if ($friendship->user_id !== Auth::id() && $friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $userId = $friendship->user_id;
        $friendId = $friendship->friend_id;

        $friendship->delete();

        try {
            $this->cacheService->clearFriendshipCache($userId);
            $this->cacheService->clearFriendshipCache($friendId);
        } catch (\Exception $cacheError) {
            Log::warning('Erro ao limpar cache de amizade', [
                'error' => $cacheError->getMessage(),
                'user_id' => $userId,
                'friend_id' => $friendId
            ]);
        }

        return response()->json(['message' => 'Amizade removida.']);
    }


    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        $currentUserId = Auth::id();

        if (strlen($query) < 2) {
            return response()->json([]);
        }


        $users = User::where('id', '!=', $currentUserId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('handle', 'LIKE', "%{$query}%");
            })
            ->whereNotExists(function ($q) use ($currentUserId) {
                $q->select(\DB::raw(1))
                    ->from('friendships')
                    ->where(function ($subQ) use ($currentUserId) {
                        $subQ->where('user_id', $currentUserId)
                            ->whereColumn('friend_id', 'users.id');
                    })
                    ->orWhere(function ($subQ) use ($currentUserId) {
                        $subQ->where('friend_id', $currentUserId)
                            ->whereColumn('user_id', 'users.id');
                    });
            })
            ->select('id', 'name', 'email', 'handle', 'avatar')
            ->limit(20)
            ->get();


        $users->each(function ($user) use ($currentUserId) {
            $user->has_pending_request = $this->hasPendingRequest($currentUserId, $user->id);
            $user->has_sent_request = $this->hasSentRequest($currentUserId, $user->id);
        });

        return response()->json($users);
    }


    public function findByHandle(string $handle)
    {
        $currentUserId = Auth::id();

        $user = User::where('handle', $handle)
            ->where('id', '!=', $currentUserId)
            ->select('id', 'name', 'email', 'handle', 'avatar')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }


        $isFriend = Friendship::where(function ($q) use ($currentUserId, $user) {
            $q->where('user_id', $currentUserId)
                ->where('friend_id', $user->id)
                ->where('status', 'accepted');
        })->orWhere(function ($q) use ($currentUserId, $user) {
            $q->where('user_id', $user->id)
                ->where('friend_id', $currentUserId)
                ->where('status', 'accepted');
        })->exists();

        $user->is_friend = $isFriend;
        $user->has_pending_request = $this->hasPendingRequest($currentUserId, $user->id);
        $user->has_sent_request = $this->hasSentRequest($currentUserId, $user->id);

        return response()->json($user);
    }


    public function inviteByHandle(Request $request)
    {
        $request->validate([
            'handle' => 'required|string',
        ]);

        $handle = $request->handle;
        $currentUserId = Auth::id();

        $user = User::where('handle', $handle)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        return $this->sendRequest($user);
    }


    private function hasSentRequest(int $currentUserId, int $targetUserId): bool
    {
        return Friendship::where('user_id', $currentUserId)
            ->where('friend_id', $targetUserId)
            ->where('status', 'pending')
            ->exists();
    }


    private function hasPendingRequest(int $currentUserId, int $targetUserId): bool
    {
        return Friendship::where('user_id', $targetUserId)
            ->where('friend_id', $currentUserId)
            ->where('status', 'pending')
            ->exists();
    }
}
