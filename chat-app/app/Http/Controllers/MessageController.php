<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Room;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Services\NotificationService;
use App\Services\CacheService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    use AuthorizesRequests;

    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request, Room $room)
    {

        \Log::info('MessageController::index called', [
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'request_params' => $request->all()
        ]);

        try {
            $this->authorize('view', $room);
        } catch (\Exception $e) {
            \Log::error('Authorization failed in MessageController::index', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        $perPage = min($request->get('per_page', 50), 100);
        $page = $request->get('page', 1);

        try {

            $messages = $room->messages()
                ->with(['sender:id,name,avatar']) // Load apenas o necessario
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            \Log::info('Messages loaded successfully', [
                'room_id' => $room->id,
                'messages_count' => $messages->count(),
                'total_messages' => $messages->total()
            ]);

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
        } catch (\Exception $e) {
            \Log::error('Error loading messages in MessageController::index', [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function store(StoreMessageRequest $request, Room $room)
    {

        $this->authorize('create', Message::class);


        $key = 'messages:' . auth()->id() . ':' . $room->id;
        RateLimiter::hit($key, 60); // 1 minute decay


        $message = DB::transaction(function () use ($request, $room) {
            $message = Message::create([
                'sender_id' => auth()->id(),
                'room_id' => $room->id,
                'body' => trim($request->body),
            ]);


            $message->load('sender:id,name,avatar');


            $notificationService = new NotificationService();
            $mentionedUsers = $notificationService->processMentions($message, $room);
            $excludeUsers = array_merge([$message->sender_id], array_column($mentionedUsers, 'id'));
            $notificationService->notifyRoomMembers($message, $room, $excludeUsers);

            return $message;
        });


        $this->cacheService->clearRoomCache($room->id);

        // broadcast mensagem
        try {
            broadcast(new MessageSent($message))->toOthers();

            // Log only in development environment
            if (app()->environment('local')) {
                Log::info('Message broadcast sent', [
                    'message_id' => $message->id,
                    'room_id' => $message->room_id,
                    'sender_id' => $message->sender_id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to broadcast message', [
                'error' => $e->getMessage(),
                'message_id' => $message->id,
                'room_id' => $message->room_id
            ]);


        }

        return response()->json([
            'message' => $message,
            'success' => true
        ]);
    }


}
