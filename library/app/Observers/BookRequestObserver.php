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
        LogHelper::log('BookRequest', $bookRequest->id, $bookRequest->toArray());
    }

    /**
     * Handle the BookRequest "updated" event.
     */
    public function updated(BookRequest $bookRequest): void
    {
        LogHelper::log('BookRequest', $bookRequest->id, $bookRequest->getChanges());
    }

    /**
     * Handle the BookRequest "deleted" event.
     */
    public function deleted(BookRequest $bookRequest): void
    {
        LogHelper::log('BookRequest', $bookRequest->id, 'deleted');
    }

    /**
     * Handle the BookRequest "restored" event.
     */
    public function restored(BookRequest $bookRequest): void
    {
        //
    }

    /**
     * Handle the BookRequest "force deleted" event.
     */
    public function forceDeleted(BookRequest $bookRequest): void
    {
        //
    }
}
