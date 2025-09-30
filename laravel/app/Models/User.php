<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified',
        'avatar_path',
    ];
    /**
     * Hidden attributes for arrays / JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'email_verified'    => 'boolean',
        ];
    }

    // ------------------------------
    // Role constants & helpers
    // ------------------------------
    public const ROLE_USER  = 'user';
    public const ROLE_ADMIN = 'admin';

    /** All available roles (for UIs/dropdowns) */
    public static function getRoles(): array
    {
        return [
            self::ROLE_USER  => 'User',
            self::ROLE_ADMIN => 'Admin',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isUser(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    // ------------------------------
    // Relationships
    // ------------------------------

    /** A user can have many login logs */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    /** A user can organize many events */
    public function events()
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    /** A user can have many tickets */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /** A user can receive many notifications */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /** Get unread notifications count */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
