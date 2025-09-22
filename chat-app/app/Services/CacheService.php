<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Room;
use App\Models\DirectConversation;

class CacheService
{
    /**
     * Cache duration in seconds
     */
    const CACHE_DURATION = 300; // 5 minutes
    const USER_CACHE_DURATION = 600; // 10 minutes
    const ROOM_CACHE_DURATION = 180; // 3 minutes

    /**
     * Get user's rooms with caching
     */
    public function getUserRooms(int $userId): array
    {
        $cacheKey = "user_rooms_{$userId}";

        return Cache::remember($cacheKey, self::ROOM_CACHE_DURATION, function () use ($userId) {
            return Room::with([
                'creator:id,name,avatar',
                'users:id,name,avatar',
                'messages' => function ($query) {
                    $query->latest()->limit(1)->with('sender:id,name');
                }
            ])
                ->where(function ($query) use ($userId) {
                    $query->where('private', false)
                        ->orWhereHas('users', function ($q) use ($userId) {
                            $q->where('user_id', $userId);
                        })
                        ->orWhere('created_by', $userId);
                })
                ->orderBy('updated_at', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get user's direct conversations with caching
     */
    public function getUserDirectConversations(int $userId): array
    {
        $cacheKey = "user_direct_conversations_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($userId) {
            $user = User::find($userId);

            $conversations = $user->directConversations()
                ->with([
                    'users:id,name,avatar',
                    'messages' => function ($query) {
                        $query->latest()->limit(1)->with('sender:id,name');
                    }
                ])->withCount([
                        'messages as unread_count' => function ($q) use ($user) {
                            $q->whereNull('read_at')->where('sender_id', '!=', $user->id);
                        }
                    ])
                ->orderBy('updated_at', 'desc')
                ->get();
            return $conversations->map(function ($conv) {
                $conv->last_message = $conv->messages->first();
                unset($conv->messages);
                return $conv;
            })->toArray();
        });
    }

    /**
     * Get user's friends with caching
     */
    public function getUserFriends(int $userId): array
    {
        $cacheKey = "user_friends_{$userId}";

        return Cache::remember($cacheKey, self::USER_CACHE_DURATION, function () use ($userId) {
            $user = User::find($userId);

            return $user->friends()
                ->select('id', 'name', 'email', 'avatar', 'handle')
                ->orderBy('name')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get room messages with caching
     */
    public function getRoomMessages(int $roomId, int $page = 1, int $perPage = 50): array
    {
        $cacheKey = "room_messages_{$roomId}_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 60, function () use ($roomId, $page, $perPage) {
            $room = Room::find($roomId);

            $messages = $room->messages()
                ->with(['sender:id,name,avatar'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'messages' => $messages->items(),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                    'has_more' => $messages->hasMorePages(),
                ]
            ];
        });
    }

    /**
     * Get direct conversation messages with caching
     */
    public function getDirectConversationMessages(int $conversationId, int $page = 1, int $perPage = 50): array
    {
        $cacheKey = "direct_conversation_messages_{$conversationId}_page_{$page}_per_{$perPage}";

        return Cache::remember($cacheKey, 60, function () use ($conversationId, $page, $perPage) {
            $conversation = DirectConversation::find($conversationId);

            $messages = $conversation->messages()
                ->with('sender:id,name,avatar')
                ->orderBy('created_at', 'asc')
                ->paginate($perPage, ['*'], 'page', $page);

            return $messages->toArray();
        });
    }

    /**
     * Clear user-related cache
     */
    public function clearUserCache(int $userId): void
    {
        Cache::forget("user_rooms_{$userId}");
        Cache::forget("user_direct_conversations_{$userId}");
        Cache::forget("user_friends_{$userId}");
    }

    /**
     * Clear room-related cache
     */
    public function clearRoomCache(int $roomId): void
    {
        // Clear room messages cache (all pages)
        $pattern = "room_messages_{$roomId}_*";
        $this->clearCacheByPattern($pattern);

        // Clear user rooms cache for all users in the room
        $room = Room::with('users:id')->find($roomId);
        if ($room) {
            foreach ($room->users as $user) {
                Cache::forget("user_rooms_{$user->id}");
            }
        }
    }

    /**
     * Clear direct conversation cache
     */
    public function clearDirectConversationCache(int $conversationId): void
    {
        // Clear conversation messages cache
        $pattern = "direct_conversation_messages_{$conversationId}_*";
        $this->clearCacheByPattern($pattern);

        // Clear user conversations cache
        $conversation = DirectConversation::with('users:id')->find($conversationId);
        if ($conversation) {
            foreach ($conversation->users as $user) {
                Cache::forget("user_direct_conversations_{$user->id}");
            }
        }
    }

    /**
     * Clear friendship cache
     */
    public function clearFriendshipCache(int $userId): void
    {
        Cache::forget("user_friends_{$userId}");
    }

    /**
     * Clear cache by pattern (for Redis)
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // For now, we'll skip pattern-based cache clearing
        // This can be implemented later with proper Redis connection handling
        // or by maintaining a list of cache keys to clear
    }

    /**
     * Warm up cache for a user
     */
    public function warmUpUserCache(int $userId): void
    {
        $this->getUserRooms($userId);
        $this->getUserDirectConversations($userId);
        $this->getUserFriends($userId);
    }
}
