<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    /**
     * Amizades enviadas (eu pedi amizade).
     */
    public function friendships()
    {
        return $this->hasMany(Friendship::class, 'user_id');
    }

    /**
     * Amizades recebidas (me pediram amizade).
     */
    public function receivedFriendships()
    {
        return $this->hasMany(Friendship::class, 'friend_id');
    }

    /**
     * Amigos aceitos (bidirecional).
     */
    public function friends()
    {
        return $this->belongsToMany(
            User::class,
            'friendships',
            'user_id',
            'friend_id'
        )->wherePivot('status', 'accepted')
            ->withTimestamps();
    }

    /**
     * Solicitações de amizade pendentes que eu enviei.
     */
    public function pendingFriendships()
    {
        return $this->friendships()->where('status', 'pending');
    }

    /**
     * Solicitações de amizade recebidas que ainda não respondi.
     */
    public function friendRequests()
    {
        return $this->receivedFriendships()->where('status', 'pending');
    }
}