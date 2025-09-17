<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Http\Requests\StoreFriendshipRequest;
use App\Http\Requests\UpdateFriendshipRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


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
}
