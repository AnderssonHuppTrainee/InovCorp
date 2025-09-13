<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Room;

Broadcast::channel('rooms.{roomId}', function ($user, $roomId) {
    return true;
});
