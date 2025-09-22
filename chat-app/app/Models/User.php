<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'permission',
        'handle',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function rooms()
    {
        //pode particioar de mt rooms
        return $this->belongsToMany(Room::class);
    }


    public function messages()
    {
        //pode enviar mts messages
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function directConversations()
    {
        //pode ter muitas dms
        return $this->belongsToMany(DirectConversation::class, 'direct_conversation_user');
    }


    public function roomInvites()
    {
        return $this->hasMany(RoomInvite::class, 'invited_user_id');
    }


    public function sentRoomInvites()
    {
        return $this->hasMany(RoomInvite::class, 'invited_by');
    }



    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }


    public function receivedFriendships()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }


    public function friends()
    {
        return User::where(function ($query) {
            $query->whereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('friendships')
                    ->where('status', 'accepted')
                    ->where(function ($q) {
                        $q->where('user_id', $this->id)
                            ->whereColumn('friend_id', 'users.id');
                    });
            })->orWhereExists(function ($subQuery) {
                $subQuery->select(DB::raw(1))
                    ->from('friendships')
                    ->where('status', 'accepted')
                    ->where(function ($q) {
                        $q->where('friend_id', $this->id)
                            ->whereColumn('user_id', 'users.id');
                    });
            });
        })->where('id', '!=', $this->id);
    }


    public function pendingFriendships()
    {
        return $this->friendships()->where('status', 'pending');
    }

    public function friendRequests()
    {
        return $this->receivedFriendships()->where('status', 'pending');
    }


    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function isOnline()
    {
        return Cache::has('user-online-' . $this->id);
    }


    public function markAsOnline()
    {
        Cache::put('user-online-' . $this->id, true, now()->addMinutes(5));
        Cache::put('user-last-activity-' . $this->id, now(), now()->addDays(7));
    }


    public function markAsOffline()
    {
        Cache::forget('user-online-' . $this->id);
    }
}