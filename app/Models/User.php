<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; 

    public const PERMISSION_HOST_PENDING = 'host_request_pending';
    public const PERMISSION_HOST_REJECTED = 'host_request_rejected';
    public const PERMISSION_ACCOUNT_LOCKED = 'account_locked';
    

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'birthday',
        'address',
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
            'birthday' => 'date',
        ];
    }

    public function isLocked(): bool
    {
        return $this->hasDirectPermissionNamed(self::PERMISSION_ACCOUNT_LOCKED);
    }

    public function hostApprovalStatus(): string
    {
        if ($this->hasRole('host')) {
            return 'approved';
        }

        if ($this->hasDirectPermissionNamed(self::PERMISSION_HOST_PENDING)) {
            return 'pending';
        }

        if ($this->hasDirectPermissionNamed(self::PERMISSION_HOST_REJECTED)) {
            return 'rejected';
        }

        return 'none';
    }

    public function hasPendingHostRequest(): bool
    {
        return $this->hostApprovalStatus() === 'pending';
    }

    public function avatarUrl(int $size = 200): string
    {
        $fallback = asset('images/static/default-avatar.png');

        if (! $this->avatar) {
            return $fallback;
        }

        $path = ltrim($this->avatar, '/');

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return $fallback;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function hostedTours()
    {
        return $this->hasMany(Tour::class, 'host_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteTours()
    {
        return $this->belongsToMany(Tour::class, 'favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    private function hasDirectPermissionNamed(string $permission): bool
    {
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->contains('name', $permission);
        }

        return $this->permissions()->where('name', $permission)->exists();
    }
}
