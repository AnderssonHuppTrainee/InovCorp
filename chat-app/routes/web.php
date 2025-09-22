<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectConversationController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\MessageReactionController;
use App\Models\Room;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';



// Rotas  
Route::middleware(['auth', 'verified'])->group(function () {


    Route::get('/rooms', function () {
        return Inertia::render('Rooms/Main', [
            'user' => auth()->user(),
        ]);
    })->name('rooms.index');

    // Rota para acessar uma sala específica (usando Main.vue com sala pré-selecionada)
    Route::get('/rooms/{room}', function (Room $room) {
        return Inertia::render('Rooms/Main', [
            'user' => auth()->user(),
            'initialRoom' => $room,
        ]);
    })->name('rooms.show');

    // API para buscar salas
    Route::get('/api/rooms', [RoomController::class, 'index'])
        ->name('rooms.api.index');

    // Criar nova sala
    Route::post('/rooms', [RoomController::class, 'store'])
        ->name('rooms.store');

    // Rota para chat individual (manter para compatibilidade)
    Route::get('/rooms/{room}/chat', function (Room $room) {
        return Inertia::render('Rooms/Main', [
            'room' => $room,
            'user' => auth()->user(),
        ]);
    })->name('rooms.chat');

    // Gerenciamento de usuários da sala
    Route::post('/rooms/{room}/add-user', [RoomController::class, 'addUser']);
    Route::delete('/rooms/{room}/remove-user/{user}', [RoomController::class, 'removeUser']);
    Route::post('/api/rooms/{room}/users/{user}/make-admin', [RoomController::class, 'makeAdmin']);
    Route::get('/api/rooms/{room}/users', [RoomController::class, 'users']);

    // Sistema de convites
    Route::post('/rooms/{room}/invite', [RoomController::class, 'inviteUser']);
    Route::get('/api/rooms/{room}/invites', [RoomController::class, 'invites']);
    Route::delete('/rooms/{room}/invites/{invite}', [RoomController::class, 'cancelInvite']);

    // Aceitar/rejeitar convites por token (público)
    Route::post('/room/invite/{token}/accept', [RoomController::class, 'acceptInvite'])->name('room.invite.accept');
    Route::post('/room/invite/{token}/reject', [RoomController::class, 'rejectInvite'])->name('room.invite.reject');

    // Busca global de usuários e sistema de amizades
    Route::get('/api/users/search', [App\Http\Controllers\FriendshipController::class, 'searchUsers']);
    Route::get('/api/users/handle/{handle}', [App\Http\Controllers\FriendshipController::class, 'findByHandle']);
    Route::post('/api/users/invite-by-handle', [App\Http\Controllers\FriendshipController::class, 'inviteByHandle']);

    // Endpoint para obter token CSRF
    Route::get('/api/csrf-token', function () {
        return response()->json([
            'csrf_token' => csrf_token(),
            'session_id' => session()->getId(),
        ]);
    })->name('csrf.token');

    // Messages (salas)
    Route::get('/api/rooms/{room}/messages', [MessageController::class, 'index'])
        ->name('messages.api.index');

    Route::post('/rooms/{room}/messages', [MessageController::class, 'store'])
        ->middleware('message.rate.limit:10,1')
        ->name('messages.store');

    Route::get('/dm', function () {
        return Inertia::render('DirectMessages/Index', [
            'user' => auth()->user(),
        ]);
    })->name('dm.index');

    // Conversas diretas (DMs)
    Route::prefix('api/dm')->group(function () {
        Route::get('/', [DirectConversationController::class, 'index']); // listar DMs
        Route::post('/', [DirectConversationController::class, 'store']); // criar nova DM
        Route::get('{conversation}/messages', [DirectMessageController::class, 'index']); // mensagens
        Route::post('{conversation}/messages', [DirectMessageController::class, 'store'])
            ->middleware('message.rate.limit:10,1'); // enviar mensagem
    });


    //friendships

    Route::get('/friends', function () {
        return Inertia::render('Friendships/Index', [
            'user' => Auth::user(),
        ]);
    })->name('friends.index');
    //notifications
    Route::get('/notifications', function () {
        return Inertia::render('Notifications/Index', [
            'user' => auth()->user(),
        ]);
    })->name('notifications.index');
    Route::prefix('/api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']); // lista de notificações
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']); // contador de não lidas
        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']); // marcar como lida
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']); // marcar todas como lidas
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']); // deletar notificação
    });

    Route::prefix('/api')->group(function () {
        Route::get('/friends', [FriendshipController::class, 'index']); // lista de amigos
        Route::get('/friends/requests', [FriendshipController::class, 'requests']); // solicitações recebidas
        Route::get('/friends/search-users', [FriendshipController::class, 'searchUsers']); // buscar usuários
        Route::post('/friends/{user}/send-request', [FriendshipController::class, 'sendRequest']); // enviar pedido
        Route::post('/friends/{friendship}/accept', [FriendshipController::class, 'accept']); // aceitar pedido
        Route::post('/friends/{friendship}/reject', [FriendshipController::class, 'reject']); // rejeitar pedido
        Route::delete('/friends/{friendship}', [FriendshipController::class, 'remove']); // remover amizade



        // Upload de arquivos
        Route::post('/files/upload', [FileUploadController::class, 'store']); // upload de arquivo
        Route::get('/files/{fileUpload}', [FileUploadController::class, 'show']); // visualizar arquivo
        Route::delete('/files/{fileUpload}', [FileUploadController::class, 'destroy']); // deletar arquivo

        // Status de usuário
        Route::get('/users/{user}/status', [UserStatusController::class, 'show']); // status de usuário
        Route::post('/users/status/batch', [UserStatusController::class, 'batch']); // status em lote

    });
});
