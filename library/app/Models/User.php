<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Cart;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'role' => 'string'
        ];
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCitizen()
    {
        return $this->role === 'citizen';
    }

    public function requests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function hasPendingFines()
    {
        return $this->requests()
            ->whereHas('fines', function ($query) {
                $query->whereNull('paid_at')
                    ->orWhere('status', 'pending');
            })
            ->exists();
    }
    public function canRequestMoreBooks()
    {
        if ($this->hasPendingFines()) {
            return false;
        }

        $activeRequests = $this->requests()
            ->whereIn('status', ['pending', 'approved', 'pending_returned'])
            ->count();

        return $activeRequests < 3;

    }

    public function totalFines(): float
    {
        return $this->requests()
            ->with('fines')
            ->get()
            ->pluck('fines')
            ->flatten()
            ->whereNull('paid_at')
            ->where('status', 'pending')
            ->sum('amount');
    }

}
