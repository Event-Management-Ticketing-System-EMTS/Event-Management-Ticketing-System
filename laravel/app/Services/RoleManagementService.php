<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleManagementService
{
    // Available roles and their allowed transitions
    public const ROLE_TRANSITIONS = [
        'user' => ['organizer'],
        'organizer' => ['user', 'admin'],
        'admin' => ['organizer']
    ];

    public const ROLE_COLORS = [
        'user' => 'bg-blue-100 text-blue-800',
        'organizer' => 'bg-green-100 text-green-800',
        'admin' => 'bg-red-100 text-red-800'
    ];

    public const ROLE_ICONS = [
        'user' => '👤',
        'organizer' => '🎪',
        'admin' => '⚡'
    ];

    /**
     * Change user role with validation
     */
    public function changeUserRole(User $user, string $newRole, User $admin): bool
    {
        // Validate admin permissions
        if ($admin->role !== 'admin') {
            throw new \Exception('Only admins can change user roles');
        }

        // Prevent self-role changes
        if ($user->id === $admin->id) {
            throw new \Exception('Cannot change your own role');
        }

        // Validate role transition
        if (!$this->canTransitionToRole($user->role, $newRole)) {
            throw new \Exception("Cannot transition from {$user->role} to {$newRole}");
        }

        // Update role
        return DB::transaction(function () use ($user, $newRole) {
            return $user->update(['role' => $newRole]);
        });
    }

    /**
     * Check if role transition is allowed
     */
    private function canTransitionToRole(string $currentRole, string $newRole): bool
    {
        return in_array($newRole, self::ROLE_TRANSITIONS[$currentRole] ?? []);
    }

    /**
     * Get available roles for transition
     */
    public function getAvailableRoles(string $currentRole): array
    {
        return self::ROLE_TRANSITIONS[$currentRole] ?? [];
    }

    /**
     * Get role styling class
     */
    public function getRoleColor(string $role): string
    {
        return self::ROLE_COLORS[$role] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get role icon
     */
    public function getRoleIcon(string $role): string
    {
        return self::ROLE_ICONS[$role] ?? '👤';
    }
}
