<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogHelper
{

    public static function log($module, $objectId, $changes)
    {
        //cria um log
        Log::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'object_id' => $objectId,
            'changes' => $changes ? json_encode($changes, JSON_UNESCAPED_UNICODE) : NULL,
            'ip' => Request::ip(),
            'browser' => Request::header('User-Agent')
        ]);
    }
}
