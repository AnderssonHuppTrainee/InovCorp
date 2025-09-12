<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DirectConversationController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';


// Rooms

Route::get('/rooms', function () {
    return Inertia::render('Rooms/Index');
})->middleware(['auth'])->name('rooms.index');

Route::get('/api/rooms', [RoomController::class, 'index'])
    ->middleware(['auth'])->name('rooms.api.index');

Route::post('/rooms', [RoomController::class, 'store'])
    ->middleware(['auth'])->name('rooms.store');


/*Route::get('/rooms/{room}', [RoomController::class, 'show']);
Route::post('/rooms/{room}/add-user', [RoomController::class, 'addUser']);
Route::delete('/rooms/{room}/remove-user/{user}', [RoomController::class, 'removeUser']);

// Messages (salas)
Route::get('/rooms/{room}/messages', [MessageController::class, 'index']);
Route::post('/rooms/{room}/messages', [MessageController::class, 'store']);

// Conversas diretas (DMs)
Route::get('/dm', [DirectConversationController::class, 'index']);
Route::post('/dm', [DirectConversationController::class, 'store']);
Route::get('/dm/{conversation}', [DirectConversationController::class, 'show']);

// Mensagens em DMs
//Route::get('/dm/{conversation}/messages', [DirectMessageController::class, 'index']);
//Route::post('/dm/{conversation}/messages', [DirectMessageController::class, 'store']);*/
