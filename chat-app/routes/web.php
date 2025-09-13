<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectConversationController;
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

    Route::get('/rooms', function () {
        return Inertia::render('Rooms/Index');
    })->name('rooms.index');

    Route::get('/api/rooms', [RoomController::class, 'index'])
        ->name('rooms.api.index');

    Route::post('/rooms', [RoomController::class, 'store'])
        ->name('rooms.store');

    Route::get('/rooms/{room}', function (Room $room) {
        return Inertia::render('Rooms/Chat', [
            'room' => $room,
            'user' => auth()->user(),
        ]);
    })->name('rooms.show');

    Route::post('/rooms/{room}/add-user', [RoomController::class, 'addUser']);
    Route::delete('/rooms/{room}/remove-user/{user}', [RoomController::class, 'removeUser']);

    // Messages (salas)
    Route::get('/api/rooms/{room}/messages', [MessageController::class, 'index'])
        ->name('messages.api.index');

    Route::post('/rooms/{room}/messages', [MessageController::class, 'store'])
        ->name('messages.store');

    // Conversas diretas (DMs)
    Route::get('/dm', [DirectConversationController::class, 'index']);
    Route::post('/dm', [DirectConversationController::class, 'store']);
    Route::get('/dm/{conversation}', [DirectConversationController::class, 'show']);

    // Mensagens em DMs
    //Route::get('/dm/{conversation}/messages', [DirectMessageController::class, 'index']);
    //Route::post('/dm/{conversation}/messages', [DirectMessageController::class, 'store']);*/

});
