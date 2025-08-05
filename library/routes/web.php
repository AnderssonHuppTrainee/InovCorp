<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::prefix('export')->group(function () {
        Route::get('books', [DashboardController::class, 'exportBooks'])->name('export.books');
        Route::get('authors', [DashboardController::class, 'exportAuthors'])->name('export.authors');
        Route::get('publishers', [DashboardController::class, 'exportPublishers'])->name('export.publishers');

    });
});

Route::resource('books', BookController::class);

Route::resource('authors', AuthorController::class);

Route::resource('publishers', PublisherController::class);