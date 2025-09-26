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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified',
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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_verified' => 'boolean',
        ];
    }

    // ------------------------------
    // Role constants and helpers
    // ------------------------------
    public const ROLE_USER = 'user';
    public const ROLE_ORGANIZER = 'organizer';
    public const ROLE_ADMIN = 'admin';

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ORGANIZER => 'Organizer',
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

    public function isOrganizer(): bool
    {
        return $this->hasRole(self::ROLE_ORGANIZER);
    }

    public function isUser(): bool
    {
        return $this->hasRole(self::ROLE_USER);
    }

    // ------------------------------
    // Relationships
    // ------------------------------

    /**
     * A user can have many login logs
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }
}
