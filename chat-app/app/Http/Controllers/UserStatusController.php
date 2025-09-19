<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserStatusController extends Controller
{
    /**
     * Obter status de um usuário.
     */
    public function show(User $user)
    {
        $isOnline = $user->isOnline();
        $lastSeen = null;

        if (!$isOnline) {
            // Tentar obter última atividade do cache
            $lastActivity = Cache::get('user-last-activity-' . $user->id);
            if ($lastActivity) {
                $lastSeen = $lastActivity;
            }
        }

        return response()->json([
            'is_online' => $isOnline,
            'last_seen' => $lastSeen,
        ]);
    }

    /**
     * Obter status de múltiplos usuários.
     */
    public function batch(Request $request)
    {
        $userIds = $request->get('user_ids', []);

        if (empty($userIds)) {
            return response()->json([]);
        }

        $statuses = [];
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $statuses[$userId] = [
                    'is_online' => $user->isOnline(),
                    'last_seen' => Cache::get('user-last-activity-' . $userId),
                ];
            }
        }

        return response()->json($statuses);
    }
}