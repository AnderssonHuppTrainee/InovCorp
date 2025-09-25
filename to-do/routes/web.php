<?php

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
    Route::get('/tasks', function () {
        return Inertia::render('tasks/Index'); // Caminho relativo a resources/js/pages/
    })->name('tasks.index');

    Route::get('/tasks/create', function () {
        return Inertia::render('tasks/Create');
    })->name('tasks.create');

    Route::get('/tasks/{task}/edit', function () {
        return Inertia::render('tasks/Edit');
    })->name('tasks.edit');
});