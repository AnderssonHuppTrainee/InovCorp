<?php

namespace App\Observers;

use App\Models\Author;
use App\Helpers\LogHelper;

class AuthorObserver
{
    /**
     * Handle the Author "created" event.
     */
    public function created(Author $author): void
    {
        $dados = [
            'Nome' => $author->name,
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Author', $author->id, "created: $textoDados");
    }

    /**
     * Handle the Author "updated" event.
     */
    public function updated(Author $author): void
    {
        $textoChanges = collect($author->getChanges())->map(function ($novoValor, $campo) use ($author) {
            $antes = $author->getOriginal($campo);
            return ucfirst($campo) . ": de '{$antes}' para '{$novoValor}'";
        })->implode('; ');

        LogHelper::log('Author', $author->id, "updated: $textoChanges");
    }

    /**
     * Handle the Author "deleted" event.
     */
    public function deleted(Author $author): void
    {
        $dados = [
            'Nome' => $author->name,
            'Ação' => 'Autor apagado',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Author', $author->id, "deleted: $textoDados");
    }

    public function restored(Author $author): void
    {
        //
    }

    public function forceDeleted(Author $author): void
    {
        //
    }
}