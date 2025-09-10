<?php

namespace App\Observers;

use App\Helpers\LogHelper;
use App\Models\Book;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     */
    public function created(Book $book): void
    {
        $dados = [
            'Nome' => $book->name,
            'ISBN' => $book->isbn,
            'Preço' => $book->price ?? 'N/A',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Book', $book->id, "created: $textoDados");
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        $textoChanges = collect($book->getChanges())->map(function ($novoValor, $campo) use ($book) {
            $antes = $book->getOriginal($campo);
            return ucfirst($campo) . ": de '{$antes}' para '{$novoValor}'";
        })->implode('; ');

        LogHelper::log('Book', $book->id, "updated: $textoChanges");
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        $dados = [
            'Nome' => $book->name,
            'ISBN' => $book->isbn,
            'Ação' => 'Livro apagado',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Book', $book->id, "deleted: $textoDados");
    }

    public function restored(Book $book): void
    {
        //
    }

    public function forceDeleted(Book $book): void
    {
        //
    }
}


