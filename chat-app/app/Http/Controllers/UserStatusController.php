<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserStatusController extends Controller
{

    public function show(User $user)
    {
        try {

            if (!$user || !$user->id) {
                return response()->json(['error' => 'Usuário não encontrado'], 404);
            }

            // chamo metodo criado no modelo
            $isOnline = $user->isOnline();
            $lastSeen = null;

            if (!$isOnline) {
                // tentar obter ultima atividade do cache
                $lastActivity = Cache::get('user-last-activity-' . $user->id);
                if ($lastActivity) {
                    $lastSeen = $lastActivity;
                }
            }

            return response()->json([
                'is_online' => $isOnline,
                'last_seen' => $lastSeen,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao obter status do usuário', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }


    public function batch(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Erro ao obter status em lote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_ids' => $request->get('user_ids', [])
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }
}