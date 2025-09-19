<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Http\Requests\StoreFriendshipRequest;
use App\Http\Requests\UpdateFriendshipRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;



class FriendshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $friends = Auth::user()->friends()->get();
        return response()->json($friends);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function requests()
    {
        $requests = Auth::user()->friendRequests()->with('user')->get();
        return response()->json($requests);
    }

    public function sendRequest(User $user)
    {
        $authUser = Auth::id();

        if ($authUser === $user->id) {
            return response()->json(['message' => 'Você não pode adicionar a si mesmo.'], 400);
        }

        // Verificar se já existe uma relação
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

    /**
     * Aceitar amizade.
     */
    public function accept(Friendship $friendship)
    {
        if ($friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $friendship->update(['status' => 'accepted']);
        return response()->json(['message' => 'Amizade aceita.', 'friendship' => $friendship]);
    }

    /**
     * Rejeitar amizade.
     */
    public function reject(Friendship $friendship)
    {
        if ($friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $friendship->update(['status' => 'rejected']);
        return response()->json(['message' => 'Pedido de amizade rejeitado.']);
    }

    /**
     * Remover amizade (tanto quem enviou quanto quem recebeu pode remover).
     */
    public function remove(Friendship $friendship)
    {
        if ($friendship->user_id !== Auth::id() && $friendship->friend_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $friendship->delete();
        return response()->json(['message' => 'Amizade removida.']);
    }

    /**
     * Buscar usuários para adicionar como amigos.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');
        $currentUserId = Auth::id();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Buscar usuários que não são amigos e não é o próprio usuário
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

        // Adicionar informações sobre solicitações pendentes
        $users->each(function ($user) use ($currentUserId) {
            $user->has_pending_request = $this->hasPendingRequest($currentUserId, $user->id);
            $user->has_sent_request = $this->hasSentRequest($currentUserId, $user->id);
        });

        return response()->json($users);
    }

    /**
     * Buscar usuário por handle específico.
     */
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

        // Verificar se já são amigos
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

    /**
     * Convidar usuário por handle.
     */
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

    /**
     * Verificar se existe solicitação pendente do usuário atual para outro usuário.
     */
    private function hasSentRequest(int $currentUserId, int $targetUserId): bool
    {
        return Friendship::where('user_id', $currentUserId)
            ->where('friend_id', $targetUserId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Verificar se existe solicitação pendente de outro usuário para o usuário atual.
     */
    private function hasPendingRequest(int $currentUserId, int $targetUserId): bool
    {
        return Friendship::where('user_id', $targetUserId)
            ->where('friend_id', $currentUserId)
            ->where('status', 'pending')
            ->exists();
    }
}
