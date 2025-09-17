<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectConversationController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\FriendshipController;
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
Route::middleware(['auth'])->group(function () {

    // Rota principal para listar salas (usando Main.vue como layout principal)
    Route::get('/rooms', function () {
        return Inertia::render('Rooms/Main', [
            'user' => auth()->user(),
        ]);
    })->name('rooms.index');

    // API para buscar salas
    Route::get('/api/rooms', [RoomController::class, 'index'])
        ->name('rooms.api.index');

    // Criar nova sala
    Route::post('/rooms', [RoomController::class, 'store'])
        ->name('rooms.store');

    // Rota para chat individual (manter para compatibilidade)
    Route::get('/rooms/{room}/chat', function (Room $room) {
        return Inertia::render('Rooms/Chat', [
            'room' => $room,
            'user' => auth()->user(),
        ]);
    })->name('rooms.chat');

    Route::post('/rooms/{room}/add-user', [RoomController::class, 'addUser']);
    Route::delete('/rooms/{room}/remove-user/{user}', [RoomController::class, 'removeUser']);

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
        Route::post('{conversation}/messages', [DirectMessageController::class, 'store']); // enviar mensagem
    });


    //friendships

    Route::get('/friends', function () {
        return Inertia::render('Friendships/Index');
    })->name('friends.index');

    Route::prefix('/api')->group(function () {
        Route::get('/friends', [FriendshipController::class, 'index']); // lista de amigos
        Route::get('/friends/requests', [FriendshipController::class, 'requests']); // solicitações recebidas
        Route::post('/friends/{user}', [FriendshipController::class, 'sendRequest']); // enviar pedido
        Route::post('/friends/{friendship}/accept', [FriendshipController::class, 'accept']); // aceitar pedido
        Route::post('/friends/{friendship}/reject', [FriendshipController::class, 'reject']); // rejeitar pedido
        Route::delete('/friends/{friendship}', [FriendshipController::class, 'remove']); // remover amizade
    });
});
