<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'emoji',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getGroupedReactions($messageId)
    {
        return static::where('message_id', $messageId)
            ->with('user:id,name')
            ->get()
            ->groupBy('emoji')
            ->map(function ($reactions) {
                return [
                    'emoji' => $reactions->first()->emoji,
                    'count' => $reactions->count(),
                    'users' => $reactions->pluck('user'),
                    'user_ids' => $reactions->pluck('user_id'),
                ];
            })
            ->values();
    }
}