<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\RoomInvite;
use App\Http\Requests\StoreRoomRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\CacheService;
use App\Services\NotificationService;

class RoomController extends Controller
{
    use AuthorizesRequests;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        $userId = auth()->id();

        // Use cache service for better performance
        $rooms = $this->cacheService->getUserRooms($userId);

        return response()->json($rooms);
    }

    public function store(StoreRoomRequest $request)
    {

        $this->authorize('create', Room::class);

        try {
            $validated = $request->validated();

            $room = Room::create([
                'name' => $validated['name'],
                'private' => $validated['private'] ?? false,
                'created_by' => auth()->id(),
            ]);

            // add o criador como membro da sala e admin
            $room->users()->syncWithoutDetaching([
                auth()->id() => ['is_admin' => true]
            ]);

            // processa convites se fornecidos
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
            ->select('users.id', 'users.name', 'users.email', 'users.avatar', 'room_user.is_admin')
            ->get();

        if (!$users->contains('id', $room->created_by)) {
            $creator = User::select('id', 'name', 'email', 'avatar')
                ->find($room->created_by);
            if ($creator) {
                $creator->is_admin = true;
                $users->push($creator);
            }
        } else {

            $users->transform(function ($user) use ($room) {
                if ($user->id === $room->created_by) {
                    $user->is_admin = true;
                }
                return $user;
            });
        }

        return response()->json($users);
    }


    public function addUser(Request $request, Room $room)
    {
        $this->authorize('manage', $room);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // verifica se o user já eh membro
        if ($room->hasUser(User::find($userId))) {
            return response()->json([
                'message' => 'Usuário já é membro desta sala.'
            ], 400);
        }

        // Adicionar user a sala
        $room->users()->syncWithoutDetaching([$userId]);

        return response()->json([
            'message' => 'Usuário adicionado à sala com sucesso!'
        ]);
    }


    public function removeUser(Room $room, User $user)
    {
        $this->authorize('manage', $room);


        if ($room->created_by === $user->id) {
            return response()->json([
                'message' => 'Não é possível remover o criador da sala.'
            ], 400);
        }


        $room->users()->detach($user->id);

        return response()->json([
            'message' => 'Usuário removido da sala com sucesso!'
        ]);
    }

    public function makeAdmin(Room $room, User $user)
    {
        $this->authorize('manage', $room);

        if (!$room->hasUser($user)) {
            return response()->json([
                'message' => 'Usuário não é membro desta sala.'
            ], 400);
        }


        $room->users()->updateExistingPivot($user->id, ['is_admin' => true]);

        return response()->json([
            'message' => 'Usuário promovido a admin com sucesso!'
        ]);
    }


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


        if ($userId) {
            $user = User::find($userId);

            if ($room->hasUser($user)) {
                return response()->json([
                    'message' => 'Usuário já é membro desta sala.'
                ], 400);
            }

            $existingInvite = $room->invites()
                ->where('invited_user_id', $userId)
                ->where('status', 'pending')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->first();

            if ($existingInvite) {
                return response()->json([
                    'message' => 'Já existe um convite pendente para este usuário.',
                    'invite_url' => $existingInvite->getInviteUrl()
                ], 400);
            }

            $invite = RoomInvite::createForUser($room, $user, auth()->user());

            $notificationService = new NotificationService();
            $notificationService->createRoomInviteNotification($user, auth()->user(), $room, $invite);
        } else {

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


            $invite = RoomInvite::createForEmail($room, $email, auth()->user());
        }

        return response()->json([
            'message' => 'Convite enviado com sucesso!',
            'invite' => $invite,
            'invite_url' => $invite->getInviteUrl()
        ]);
    }


    public function invites(Room $room)
    {
        $this->authorize('view', $room);

        $invites = $room->invites()
            ->with(['invitedBy:id,name', 'invitedUser:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($invites);
    }


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

        if (!auth()->check()) {
            return response()->json([
                'message' => 'É necessário fazer login para aceitar o convite.',
                'login_required' => true
            ], 401);
        }

        $user = auth()->user();

        if ($invite->invited_user_id && $invite->invited_user_id !== $user->id) {
            return response()->json([
                'message' => 'Este convite não é para você.'
            ], 403);
        }

        if ($invite->email && $invite->email !== $user->email) {
            return response()->json([
                'message' => 'Este convite não é para seu email.'
            ], 403);
        }

        if ($invite->accept()) {
            return response()->json([
                'message' => 'Convite aceito com sucesso!',
                'room' => $invite->room,
                'redirect_url' => route('rooms.show', $invite->room)
            ]);
        }

        return response()->json([
            'message' => 'Erro ao aceitar o convite.'
        ], 500);
    }


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
