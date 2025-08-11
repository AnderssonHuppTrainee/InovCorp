<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookRequestController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('requests', BookRequestController::class)->except(['create']);
    Route::get('/requests/create/{book}', [BookRequestController::class, 'create'])->name('requests.create');



    Route::prefix('export')->middleware('admin')->group(function () {
        Route::get('books', [DashboardController::class, 'exportBooks'])->name('export.books');
        Route::get('authors', [DashboardController::class, 'exportAuthors'])->name('export.authors');
        Route::get('publishers', [DashboardController::class, 'exportPublishers'])->name('export.publishers');

    });

    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
});


// Rotas protegidas apenas para admin
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin'
])->group(function () {
    //gestao requisicoes
    Route::post('/requests/{request}/approve', [BookRequestController::class, 'approve'])
        ->name('requests.approve');

    Route::resource('books', BookController::class)->except(['show']);
    Route::resource('authors', AuthorController::class);
    Route::resource('publishers', PublisherController::class);

    // getao de usuários
    Route::resource('users', UserController::class);
    //Route::get('/users/create-admin', [UserController::class, 'createAdmin'])->name('users.create-admin');
    //Route::post('/users/admin', [UserController::class, 'storeAdmin'])->name('users.store-admin');
});

