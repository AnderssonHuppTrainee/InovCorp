<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RoomInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'invited_by',
        'invited_user_id',
        'email',
        'invite_token',
        'status',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    //gerar token unico
    public static function generateToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('invite_token', $token)->exists());

        return $token;
    }


    public static function createForUser(Room $room, User $invitedUser, User $invitedBy, int $expirationDays = 7): self
    {
        return static::create([
            'room_id' => $room->id,
            'invited_by' => $invitedBy->id,
            'invited_user_id' => $invitedUser->id,
            'email' => $invitedUser->email,
            'invite_token' => static::generateToken(),
            'status' => 'pending',
            'expires_at' => now()->addDays($expirationDays),
        ]);
    }

    //convite por email
    public static function createForEmail(Room $room, string $email, User $invitedBy, int $expirationDays = 7): self
    {
        return static::create([
            'room_id' => $room->id,
            'invited_by' => $invitedBy->id,
            'email' => $email,
            'invite_token' => static::generateToken(),
            'status' => 'pending',
            'expires_at' => now()->addDays($expirationDays),
        ]);
    }


    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }


    public function accept(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // add user a sala
        if ($this->invited_user_id) {
            $this->room->users()->syncWithoutDetaching([$this->invited_user_id]);
        }

        return true;
    }


    public function reject(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            'status' => 'rejected',
        ]);

        return true;
    }


    public function getInviteUrl(): string
    {
        return route('room.invite.accept', ['token' => $this->invite_token]);
    }


    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }


    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<=', now());
    }
}