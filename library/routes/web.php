<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturnsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\PublicBookController;


// Rotas Públicas (sem autenticação)
Route::name('public.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Rota pública para catálogo de livros
    Route::get('/catalog', [PublicBookController::class, 'index'])->name('books.index');

    // Detalhe público do livro
    Route::get('/catalog/{book}', [PublicBookController::class, 'show'])->name('books.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('requests', BookRequestController::class)->except(['create']);
    Route::get('/requests/create/{book}', [BookRequestController::class, 'create'])
        ->name('requests.create');

    //  registrar devolução
    Route::get('/requests/{bookRequest}/return', [BookRequestController::class, 'returnForm'])
        ->name('requests.returnForm');

    Route::post('/requests/{bookRequest}/return', [BookRequestController::class, 'submitReturn'])
        ->name('requests.submitReturn');
    Route::put('/requests/{bookRequest}/return', [BookRequestController::class, 'cancel'])
        ->name('requests.cancel');

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
    Route::get('/requests/{request}', [BookRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{request}/approve', [BookRequestController::class, 'approve'])
        ->name('requests.approve');
    Route::post('/requests/{bookRequest}/reject', [BookRequestController::class, 'reject'])
        ->name('requests.reject');

    Route::get('/requests/{bookRequest}/review-return', [BookRequestController::class, 'reviewReturn'])
        ->name('requests.reviewReturn');

    Route::post('/requests/{bookRequest}/approve-return', [BookRequestController::class, 'approveReturn'])
        ->name('requests.approveReturn');

    Route::post('/requests/{bookRequest}/reject-return', [BookRequestController::class, 'rejectReturn'])
        ->name('requests.rejectReturn');

    Route::resource('/returns', ReturnsController::class);

    //exportacoes
    Route::prefix('export')->middleware('admin')->group(function () {
        Route::get('books', [DashboardController::class, 'exportBooks'])->name('export.books');
        Route::get('authors', [DashboardController::class, 'exportAuthors'])->name('export.authors');
        Route::get('publishers', [DashboardController::class, 'exportPublishers'])->name('export.publishers');

    });

    Route::resource('books', BookController::class)->except(['show']);
    Route::resource('authors', AuthorController::class);
    Route::resource('publishers', PublisherController::class);

    // getao de users
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::resource('users', UserController::class)->except(['create']);
});

