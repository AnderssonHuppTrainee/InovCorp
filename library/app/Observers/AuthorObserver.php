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
        LogHelper::log('Author', $author->id, $author->toArray());
    }

    /**
     * Handle the Author "updated" event.
     */
    public function updated(Author $author): void
    {
        LogHelper::log('Author', $author->id, $author->getChanges());
    }

    /**
     * Handle the Author "deleted" event.
     */
    public function deleted(Author $author): void
    {
        LogHelper::log('Author', $author->id, 'deleted');
    }

    /**
     * Handle the Author "restored" event.
     */
    public function restored(Author $author): void
    {
        //
    }

    /**
     * Handle the Author "force deleted" event.
     */
    public function forceDeleted(Author $author): void
    {
        //
    }
}
