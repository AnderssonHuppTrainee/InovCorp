<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->whereNumber('task')
        ->name('tasks.show');

    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])
        ->whereNumber('task')
        ->name('tasks.edit');

    Route::patch('tasks/{task}', [TaskController::class, 'update'])
        ->whereNumber('task')
        ->name('tasks.update');

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->whereNumber('task')
        ->name('tasks.complete');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->whereNumber('task')
        ->name('tasks.destroy');


});