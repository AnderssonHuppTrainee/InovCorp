<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $notifications = Auth::user()
            ->notifications()
            ->with('fromUser:id,name,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more' => $notifications->hasMorePages(),
            ]
        ]);
    }


    public function markAsRead(AppNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notificação marcada como lida.']);
    }


    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->unread()
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['message' => 'Todas as notificações foram marcadas como lidas.']);
    }


    public function unreadCount()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }


            $notifications = $user->notifications();
            $unreadNotifications = $notifications->where('read', false);
            $count = $unreadNotifications->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Erro ao obter contagem de notificações não lidas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }


    public function destroy(AppNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notificação removida.']);
    }
}