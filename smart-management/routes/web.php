<?php

use App\Http\Controllers\Core\EntityController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/entities', [EntityController::class, 'index'])
        ->name('entities.index');
    Route::get('/entities/create', [EntityController::class, 'create'])
        ->name('entities.create');
    Route::post('/entities', [EntityController::class, 'store'])
        ->name('entities.store');
    Route::get('/entities/{entity}/show', [EntityController::class, 'show'])
        ->name('entities.show');
    Route::get('/entities/{entity}/edit', [EntityController::class, 'edit'])
        ->name('entities.edit');
    Route::patch('/entities/{entity}', [EntityController::class, 'update'])
        ->name('entities.update');

    Route::post('/entities/vies-check', [EntityController::class, 'viesCheck'])
        ->name('entities.vies-check');
});