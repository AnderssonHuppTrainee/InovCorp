<?php

namespace App\Observers;

use App\Models\Order;
use App\Helpers\LogHelper;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        LogHelper::log('Order', $order->id, 'created');
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $changes = [];
        foreach ($order->getChanges() as $field => $newValue) {
            if ($field !== 'updated_at') {
                $oldValue = $order->getOriginal($field);
                $changes[] = "$field: $oldValue → $newValue";
            }
        }
        if (!empty($changes)) {
            LogHelper::log('Order', $order->id, 'Updated: ' . implode(', ', $changes));
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        LogHelper::log('Order', $order->id, 'deleted');
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
