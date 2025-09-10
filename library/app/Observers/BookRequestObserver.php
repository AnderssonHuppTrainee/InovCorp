<?php

namespace App\Observers;

use App\Models\BookRequest;
use App\Helpers\LogHelper;

class BookRequestObserver
{
    /**
     * Handle the BookRequest "created" event.
     */
    public function created(BookRequest $bookRequest): void
    {
        $dados = [
            'Utilizador' => $bookRequest->user->name ?? $bookRequest->user_id,
            'Livro' => $bookRequest->book->name ?? $bookRequest->book_id,
            'Data Requisição' => $bookRequest->request_date->format('d/m/Y'),
            'Data Esperada' => $bookRequest->expected_return_date->format('d/m/Y'),
            'Status' => $bookRequest->status,
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('BookRequest', $bookRequest->id, "created: $textoDados");
    }

    /**
     * Handle the BookRequest "updated" event.
     */
    public function updated(BookRequest $bookRequest): void
    {
        $textoChanges = collect($bookRequest->getChanges())->map(function ($novoValor, $campo) use ($bookRequest) {
            $antes = $bookRequest->getOriginal($campo);
            return ucfirst($campo) . ": de '{$antes}' para '{$novoValor}'";
        })->implode('; ');

        LogHelper::log('BookRequest', $bookRequest->id, "updated: $textoChanges");
    }

    /**
     * Handle the BookRequest "deleted" event.
     */
    public function deleted(BookRequest $bookRequest): void
    {
        $dados = [
            'Utilizador' => $bookRequest->user->name ?? $bookRequest->user_id,
            'Livro' => $bookRequest->book->name ?? $bookRequest->book_id,
            'Ação' => 'Requisição apagada',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('BookRequest', $bookRequest->id, "deleted: $textoDados");
    }
}
