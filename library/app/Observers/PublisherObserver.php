<?php

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
        LogHelper::log('Publisher', $publisher->id, $publisher->toArray());
    }

    /**
     * Handle the Publisher "updated" event.
     */
    public function updated(Publisher $publisher): void
    {
        LogHelper::log('Publisher', $publisher->id, $publisher->getChanges());
    }

    /**
     * Handle the Publisher "deleted" event.
     */
    public function deleted(Publisher $publisher): void
    {
        LogHelper::log('Publisher', $publisher->id, 'deleted');
    }

    /**
     * Handle the Publisher "restored" event.
     */
    public function restored(Publisher $publisher): void
    {
        //
    }

    /**
     * Handle the Publisher "force deleted" event.
     */
    public function forceDeleted(Publisher $publisher): void
    {
        //
    }
}
