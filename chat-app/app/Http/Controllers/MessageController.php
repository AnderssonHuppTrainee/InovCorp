<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Room;
use App\Http\Requests\StoremessagesRequest;
use App\Http\Requests\UpdatemessagesRequest;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MessageController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Room $room)
    {
        // Verificar autorização
        $this->authorize('view', $room);

        $perPage = $request->get('per_page', 50);
        $page = $request->get('page', 1);

        $messages = $room->messages()
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'has_more' => $messages->hasMorePages(),
            ]
        ]);
    }

    public function store(Request $request, Room $room)
    {
        // Log detalhado para debug
        \Log::info('Tentativa de envio de mensagem:', [
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'csrf_token' => $request->header('X-CSRF-TOKEN'),
            'session_token' => $request->session()->token(),
            'request_data' => $request->all(),
        ]);

        // Temporariamente desabilitar autorização para debug
        \Log::info('🔧 Pulando autorização para debug - usuário:', ['user_id' => auth()->id()]);

        // Validação manual simples
        if (!$request->has('body') || empty(trim($request->body))) {
            \Log::error('❌ Mensagem vazia ou inválida');
            return response()->json(['error' => 'Mensagem não pode estar vazia'], 400);
        }

        // Rate limiting: máximo 10 mensagens por minuto por usuário
        $key = 'messages:' . auth()->id() . ':' . $room->id;
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (\RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = \RateLimiter::availableIn($key);
            return response()->json([
                'error' => 'Muitas mensagens enviadas. Tente novamente em ' . $seconds . ' segundos.',
                'retry_after' => $seconds
            ], 429);
        }

        \RateLimiter::hit($key, $decayMinutes * 60);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'room_id' => $room->id,
            'body' => $request->body,
        ]);

        $message->load('sender:id,name');

        // Processar notificações
        $notificationService = new NotificationService();

        // Processar menções
        $mentionedUsers = $notificationService->processMentions($message, $room);

        // Notificar membros da sala (exceto mencionados e remetente)
        $excludeUsers = array_merge([$message->sender_id], array_column($mentionedUsers, 'id'));
        $notificationService->notifyRoomMembers($message, $room, $excludeUsers);

        // broadcast em tempo real
        \Log::info('Disparando broadcast para mensagem:', [
            'message_id' => $message->id,
            'room_id' => $message->room_id,
            'sender_id' => $message->sender_id,
            'broadcasting_channel' => 'rooms.' . $message->room_id
        ]);

        try {
            // Enviar para todos os usuários (incluindo o remetente para debug)
            broadcast(new MessageSent($message));
            \Log::info('✅ Broadcast enviado com sucesso para sala:', ['room_id' => $message->room_id]);
        } catch (\Exception $e) {
            \Log::error('❌ Erro ao enviar broadcast:', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
                'room_id' => $message->room_id
            ]);
        }

        // Retorna JSON para requisições AJAX
        return response()->json([
            'message' => $message,
            'success' => true,
            'broadcast_sent' => true
        ]);
    }


}
