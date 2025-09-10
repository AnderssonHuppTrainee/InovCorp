<?php

namespace App\Observers;

use App\Models\Publisher;
use App\Helpers\LogHelper;

namespace App\Observers;

use App\Models\Publisher;
use App\Helpers\LogHelper;

class PublisherObserver
{
    /**
     * Handle the Publisher "created" event.
     */
    public function created(Publisher $publisher): void
    {
        $dados = [
            'Nome' => $publisher->name,
            'Descrição' => $publisher->description ?? '-',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Publisher', $publisher->id, "created: $textoDados");
    }

    /**
     * Handle the Publisher "updated" event.
     */
    public function updated(Publisher $publisher): void
    {
        $textoChanges = collect($publisher->getChanges())->filter(fn($_, $campo) => $campo !== 'updated_at')
            ->map(function ($novoValor, $campo) use ($publisher) {
                $antes = $publisher->getOriginal($campo);
                return ucfirst($campo) . ": '{$antes}' → '{$novoValor}'";
            })->implode('; ');

        LogHelper::log('Publisher', $publisher->id, "updated: $textoChanges");
    }

    /**
     * Handle the Publisher "deleted" event.
     */
    public function deleted(Publisher $publisher): void
    {
        $dados = [
            'Nome' => $publisher->name,
            'Ação' => 'Editora apagada',
        ];

        $textoDados = collect($dados)->map(fn($valor, $chave) => "$chave: $valor")->implode('; ');

        LogHelper::log('Publisher', $publisher->id, "deleted: $textoDados");
    }

    public function restored(Publisher $publisher): void
    {
        //
    }

    public function forceDeleted(Publisher $publisher): void
    {
        //
    }
}