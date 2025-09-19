<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Listar notificações do usuário.
     */
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

    /**
     * Marcar notificação como lida.
     */
    public function markAsRead(AppNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notificação marcada como lida.']);
    }

    /**
     * Marcar todas as notificações como lidas.
     */
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

    /**
     * Contar notificações não lidas.
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Deletar notificação.
     */
    public function destroy(AppNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notificação removida.']);
    }
}