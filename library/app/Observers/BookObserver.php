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
        LogHelper::log('Book', $book->id, $book->toArray());
    }

    /**
     * Handle the Book "updated" event.
     */
    public function updated(Book $book): void
    {
        LogHelper::log('Book', $book->id, $book->getChanges());
    }

    /**
     * Handle the Book "deleted" event.
     */
    public function deleted(Book $book): void
    {
        LogHelper::log('Book', $book->id, 'deleted');
    }

    /**
     * Handle the Book "restored" event.
     */
    public function restored(Book $book): void
    {
        //
    }

    /**
     * Handle the Book "force deleted" event.
     */
    public function forceDeleted(Book $book): void
    {
        //
    }
}
