<?php

use App\Models\BookRequest;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;


it('mostra o form de requisição ', function () {
    //cria o user e book
    $user = User::factory()->create();
    $book = Book::factory()->create();
    //login
    $this->actingAs($user);

    //enviar request via rota
    $response = $this->get(route('requests.create', $book));

    //redirecina 200 ok
    $response->assertStatus(200);
    $response->assertSee($book->name);
});


it('pode requisitar um livro', function () {

    $user = User::factory()->create();

    $book = Book::factory()->create();
    //photo fake
    $photo = UploadedFile::fake()->image('id.jpg');

    //login
    $this->actingAs($user);


    $response = $this->post(route('requests.store'), [
        'book_id' => $book->id,
        'request_date' => now(),
        'photo' => $photo
    ]);
    // redireciona found
    $response->assertStatus(302);

    //verfica a bd
    $this->assertDatabaseHas('book_requests', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'pending',
    ]);

});

it('pode cancelar uma requisição', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $bookRequest = BookRequest::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user);

    $response = $this->put(route('requests.cancel', $bookRequest));

    $response->assertStatus(302); //found
    //verfica se existe na bd
    $this->assertDatabaseHas('book_requests', [
        'id' => $bookRequest->id,
        'status' => 'cancelled',
    ]);
});

it('nao pode criar uma requisicao sem um livro valido', function () {
    $user = User::factory()->create();

    $photo = UploadedFile::fake()->image('id.jpg');

    $response = $this->actingAs($user)->post(route('requests.store'), [
        'book_id' => 999, //id falso q n existe
        'request_date' => now(),
        'photo' => $photo
    ]);

    // verifica se houve erro de validação
    $response->assertSessionHasErrors([
        'book_id' => 'O campo book id selecionado é inválido.'
    ]);

});

it('lista apenas requisições do user logado', function () {

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $book1 = Book::factory()->create([
        'name' => 'Livro User1'
    ]);
    $book2 = Book::factory()->create([
        'name' => 'Livro User2'
    ]);
    $book3 = Book::factory()->create([
        'name' => 'Livro User3'
    ]);


    //requisicoes

    $bookRequest1 = BookRequest::factory()->create([
        'user_id' => $user1->id,
        'book_id' => $book1->id,
        'status' => 'approved',
    ]);

    $bookRequest2 = BookRequest::factory()->create([
        'user_id' => $user1->id,
        'book_id' => $book2->id,
        'status' => 'approved',
    ]);

    $bookRequest3 = BookRequest::factory()->create([
        'user_id' => $user2->id,
        'book_id' => $book3->id,
        'status' => 'approved',
    ]);

    //LOGIN USER 1
    $response = $this->actingAs($user1)->get(route('requests.index'));

    $response->assertStatus(200);
    // verifica se o conteúdo retornado contém apenas as requisições do user1
    $response->assertSeeText($bookRequest1->book->name);
    $response->assertSeeText($bookRequest2->book->name);

    // verifica que n vê a requisição de outro user
    $response->assertDontSeeText($bookRequest3->number);


});


it('nao pode requisitar um livro sem stock', function () {

    $user = User::factory()->create();
    //foto do id mandatorio
    $photo = UploadedFile::fake()->image('id.jpg');

    //criar livro com stock zero  ou indisponivel (em aluguer)
    $book = Book::factory()->create([
        'stock' => 0,
        'available' => 'false',
    ]);

    $response = $this->actingAs($user)->post(route('requests.store'), [
        'book_id' => $book->id,
        'request_date' => now(),
        'photo' => $photo,
    ]);

    expect(session('error'))->toBe('Este livro não está disponível.');

    expect(BookRequest::count())->toBe(0);
});



