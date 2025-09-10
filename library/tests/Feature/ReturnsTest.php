<?php

use App\Models\BookRequest;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('pode devolver um livro', function () {

    $user = User::factory()->create();
    $book = Book::factory()->create();
    $bookRequest = BookRequest::factory()
        ->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'approved'
        ]);

    $photo = UploadedFile::fake()->image('book.jpg');

    $response = $this->actingAs($user)
        ->post(route('returns.store', $bookRequest), [
            'returned_date' => now(),
            'return_photo' => $photo,
        ]);

    $response->assertStatus(302);

    //verfica a bd
    $this->assertDatabaseHas('book_requests', [
        'id' => $bookRequest->id,
        'status' => 'pending_returned', //devolucao pendente pq o admin precisa aprovar
    ]);
});

