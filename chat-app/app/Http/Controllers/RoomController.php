<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\RoomInvite;
use App\Http\Requests\StoreRoomRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoomController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        $rooms = Room::with(['creator:id,name', 'users:id,name'])
            ->where('private', false) // salas públicas
            ->orWhereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId); // salas privadas do usuário
            })
            ->orWhere('created_by', $userId) // salas criadas pelo usuário
            ->orderBy('created_at', 'desc')
            ->get();

        return $rooms;
    }

    public function store(StoreRoomRequest $request)
    {
        // Verificar autorização usando Policy
        $this->authorize('create', Room::class);

        try {
            $validated = $request->validated();

            $room = Room::create([
                'name' => $validated['name'],
                'private' => $validated['private'] ?? false,
                'created_by' => auth()->id(),
            ]);

            // Adicionar o criador como membro da sala
            $room->users()->syncWithoutDetaching([auth()->id()]);

            // Processar convites se fornecidos
            $invites = [];
            if (isset($validated['invite_users']) && is_array($validated['invite_users'])) {
                foreach ($validated['invite_users'] as $userId) {
                    $user = User::find($userId);
                    if ($user && !$room->hasUser($user)) {
                        $invite = RoomInvite::createForUser($room, $user, auth()->user());
                        $invites[] = $invite;
                    }
                }
            }

            if (isset($validated['invite_emails']) && is_array($validated['invite_emails'])) {
                foreach ($validated['invite_emails'] as $email) {
                    $invite = RoomInvite::createForEmail($room, $email, auth()->user());
                    $invites[] = $invite;
                }
            }

            // Carregar relacionamentos para retorno
            $room->load(['creator:id,name', 'users:id,name']);

            return response()->json([
                'room' => $room,
                'invites' => $invites,
                'message' => 'Sala criada com sucesso!'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }


    public function users(Room $room)
    {
        $this->authorize('view', $room);

        $users = $room->users()
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        // Adicionar o criador da sala se não estiver na lista
        if (!$users->contains('id', $room->created_by)) {
            $creator = User::select('id', 'name', 'email')
                ->find($room->created_by);
            if ($creator) {
                $users->push($creator);
            }
        }

        return response()->json($users);
    }

    /**
     * Adicionar usuário à sala.
     */
    public function addUser(Request $request, Room $room)
    {
        $this->authorize('manage', $room);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // Verificar se o usuário já é membro
        if ($room->hasUser(User::find($userId))) {
            return response()->json([
                'message' => 'Usuário já é membro desta sala.'
            ], 400);
        }

        // Adicionar usuário à sala
        $room->users()->syncWithoutDetaching([$userId]);

        return response()->json([
            'message' => 'Usuário adicionado à sala com sucesso!'
        ]);
    }

    /**
     * Remover usuário da sala.
     */
    public function removeUser(Room $room, User $user)
    {
        $this->authorize('manage', $room);

        // Não permitir remover o criador da sala
        if ($room->created_by === $user->id) {
            return response()->json([
                'message' => 'Não é possível remover o criador da sala.'
            ], 400);
        }

        // Remover usuário da sala
        $room->users()->detach($user->id);

        return response()->json([
            'message' => 'Usuário removido da sala com sucesso!'
        ]);
    }

    /**
     * Convidar usuário para a sala.
     */
    public function inviteUser(Request $request, Room $room)
    {
        $this->authorize('manage', $room);

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'email' => 'nullable|email',
        ]);

        $userId = $request->user_id;
        $email = $request->email;

        if (!$userId && !$email) {
            return response()->json([
                'message' => 'É necessário fornecer um ID de usuário ou email.'
            ], 400);
        }

        // Se foi fornecido um user_id
        if ($userId) {
            $user = User::find($userId);

            // Verificar se o usuário já é membro
            if ($room->hasUser($user)) {
                return response()->json([
                    'message' => 'Usuário já é membro desta sala.'
                ], 400);
            }

            // Verificar se já existe convite pendente
            $existingInvite = $room->invites()
                ->where('invited_user_id', $userId)
                ->pending()
                ->first();

            if ($existingInvite) {
                return response()->json([
                    'message' => 'Já existe um convite pendente para este usuário.',
                    'invite_url' => $existingInvite->getInviteUrl()
                ], 400);
            }

            // Criar convite
            $invite = RoomInvite::createForUser($room, $user, auth()->user());
        } else {
            // Verificar se já existe convite pendente para este email
            $existingInvite = $room->invites()
                ->where('email', $email)
                ->pending()
                ->first();

            if ($existingInvite) {
                return response()->json([
                    'message' => 'Já existe um convite pendente para este email.',
                    'invite_url' => $existingInvite->getInviteUrl()
                ], 400);
            }

            // Criar convite por email
            $invite = RoomInvite::createForEmail($room, $email, auth()->user());
        }

        return response()->json([
            'message' => 'Convite enviado com sucesso!',
            'invite' => $invite,
            'invite_url' => $invite->getInviteUrl()
        ]);
    }

    /**
     * Listar convites da sala.
     */
    public function invites(Room $room)
    {
        $this->authorize('view', $room);

        $invites = $room->invites()
            ->with(['invitedBy:id,name', 'invitedUser:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($invites);
    }

    /**
     * Cancelar convite.
     */
    public function cancelInvite(Room $room, RoomInvite $invite)
    {
        $this->authorize('manage', $room);

        if ($invite->room_id !== $room->id) {
            return response()->json([
                'message' => 'Convite não pertence a esta sala.'
            ], 400);
        }

        $invite->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Convite cancelado com sucesso!'
        ]);
    }

    /**
     * Aceitar convite por token.
     */
    public function acceptInvite(Request $request, string $token)
    {
        $invite = RoomInvite::where('invite_token', $token)->first();

        if (!$invite) {
            return response()->json([
                'message' => 'Convite não encontrado ou inválido.'
            ], 404);
        }

        if (!$invite->isValid()) {
            return response()->json([
                'message' => 'Convite expirado ou já foi processado.'
            ], 400);
        }

        // Se o usuário não estiver logado, redirecionar para login
        if (!auth()->check()) {
            return response()->json([
                'message' => 'É necessário fazer login para aceitar o convite.',
                'login_required' => true
            ], 401);
        }

        $user = auth()->user();

        // Verificar se o convite é para este usuário
        if ($invite->invited_user_id && $invite->invited_user_id !== $user->id) {
            return response()->json([
                'message' => 'Este convite não é para você.'
            ], 403);
        }

        // Se o convite é por email, verificar se o email corresponde
        if ($invite->email && $invite->email !== $user->email) {
            return response()->json([
                'message' => 'Este convite não é para seu email.'
            ], 403);
        }

        // Aceitar o convite
        if ($invite->accept()) {
            return response()->json([
                'message' => 'Convite aceito com sucesso!',
                'room' => $invite->room
            ]);
        }

        return response()->json([
            'message' => 'Erro ao aceitar o convite.'
        ], 500);
    }

    /**
     * Rejeitar convite por token.
     */
    public function rejectInvite(Request $request, string $token)
    {
        $invite = RoomInvite::where('invite_token', $token)->first();

        if (!$invite) {
            return response()->json([
                'message' => 'Convite não encontrado ou inválido.'
            ], 404);
        }

        if (!$invite->isValid()) {
            return response()->json([
                'message' => 'Convite expirado ou já foi processado.'
            ], 400);
        }

        // Rejeitar o convite
        if ($invite->reject()) {
            return response()->json([
                'message' => 'Convite rejeitado com sucesso!'
            ]);
        }

        return response()->json([
            'message' => 'Erro ao rejeitar o convite.'
        ], 500);
    }
}
