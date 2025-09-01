<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\PublicBookController;
use App\Http\Controllers\FineController;



// Rotas publicas
Route::name('public.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/catalog', [PublicBookController::class, 'index'])->name('books.index');
    Route::get('/catalog/{book}', [PublicBookController::class, 'show'])->name('books.show');
});

//auth
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    //requisicao
    Route::resource('requests', BookRequestController::class)->except(['create', 'show']);
    Route::get('/requests/create/{book}', [BookRequestController::class, 'create'])
        ->name('requests.create');
    Route::put('/requests/{bookRequest}/return', [BookRequestController::class, 'cancel'])
        ->name('requests.cancel');

    //review
    Route::prefix('book-requests/{bookRequest}')->group(function () {
        Route::get('reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
        Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    // devolução
    Route::prefix('returns')->group(function () {
        Route::get('{bookRequest}/return', [ReturnsController::class, 'returnForm'])->name('returns.returnForm');
        Route::post('{bookRequest}/return', [ReturnsController::class, 'submitReturn'])->name('returns.submitReturn');
    });


    //multas
    Route::get('/my-fines', [FineController::class, 'index'])->name('fines.index');
    Route::post('/fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');

    //notificacao
    Route::post('books/{book}/notify', [BookController::class, 'notify'])->name('books.notify');



    //carinho
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');



});

// Rotas  para admin
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin'
])->group(function () {

    //gestao requisicoes
    Route::prefix('requests')->group(function () {
        Route::get('/{bookRequest}', [BookRequestController::class, 'show'])->name('requests.show');
        Route::post('/{bookRequest}/approve', [BookRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/{bookRequest}/reject', [BookRequestController::class, 'reject'])->name('requests.reject');
    });

    //gestao de devoluçao
    Route::prefix('returns')->group(function () {
        Route::get('{bookRequest}/review-return', [ReturnsController::class, 'reviewReturn'])->name('returns.reviewReturn');
        Route::post('{bookRequest}/approve-return', [ReturnsController::class, 'approveReturn'])->name('returns.approveReturn');
        Route::post('{bookRequest}/reject-return', [ReturnsController::class, 'rejectReturn'])->name('returns.rejectReturn');
    });
    Route::resource('returns', ReturnsController::class);

    //Moderação de avaliacoes
    Route::prefix('admin/reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/{review}', [ReviewController::class, 'show'])->name('reviews.show');
        Route::patch('/{review}/status', [ReviewController::class, 'update'])->name('reviews.update');
    });

    //exportacoes
    Route::prefix('export')->group(function () {
        Route::get('books', [DashboardController::class, 'exportBooks'])->name('export.books');
        Route::get('authors', [DashboardController::class, 'exportAuthors'])->name('export.authors');
        Route::get('publishers', [DashboardController::class, 'exportPublishers'])->name('export.publishers');

    });

    Route::get('books/import', [BookController::class, 'import'])->name('books.import');
    Route::get('books/search-google', [BookController::class, 'searchGoogle'])->name('books.searchGoogle');
    Route::post('books/store-google', [BookController::class, 'storeGoogle'])->name('books.storeGoogle');
    Route::resource('books', BookController::class)->except(['import', 'notify']);

    Route::resource('authors', AuthorController::class);
    Route::resource('publishers', PublisherController::class);

    // getao de users
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::resource('users', UserController::class)->except(['create']);
});

