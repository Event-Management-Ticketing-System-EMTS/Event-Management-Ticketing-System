<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleManagementService
{
    /**
     * Available role transitions
     */
    public const ROLE_TRANSITIONS = [
        User::ROLE_USER => [User::ROLE_ADMIN],
        User::ROLE_ADMIN => [User::ROLE_USER],
    ];

    /**
     * Check if current user can manage roles
     */
    public function canManageRoles(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Check if role transition is allowed
     */
    public function canChangeRole(string $fromRole, string $toRole): bool
    {
        if (!isset(self::ROLE_TRANSITIONS[$fromRole])) {
            return false;
        }

        return in_array($toRole, self::ROLE_TRANSITIONS[$fromRole]);
    }

    /**
     * Change user role with validation
     */
    public function changeUserRole(User $user, string $newRole): array
    {
        // Check if current user has permission
        if (!$this->canManageRoles()) {
            return [
                'success' => false,
                'message' => 'Access denied. Admin privileges required.'
            ];
        }

        // Prevent self role change
        if ($user->id === Auth::id()) {
            return [
                'success' => false,
                'message' => 'Cannot change your own role.'
            ];
        }

        // Check if role transition is valid
        if (!$this->canChangeRole($user->role, $newRole)) {
            return [
                'success' => false,
                'message' => 'Invalid role transition.'
            ];
        }

        // Update role
        $oldRole = $user->role;
        $user->role = $newRole;
        $user->save();

        return [
            'success' => true,
            'message' => "User role changed from {$oldRole} to {$newRole} successfully.",
            'old_role' => $oldRole,
            'new_role' => $newRole
        ];
    }

    /**
     * Get available roles for dropdown
     */
    public function getAvailableRoles(): array
    {
        return User::getRoles();
    }

    /**
     * Get role badge class for UI
     */
    public function getRoleBadgeClass(string $role): string
    {
        return match ($role) {
            User::ROLE_ADMIN => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            User::ROLE_USER => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
        };
    }

    /**
     * Get role icon for UI
     */
    public function getRoleIcon(string $role): string
    {
        return match ($role) {
            User::ROLE_ADMIN => '👑',
            User::ROLE_USER => '👤',
            default => '❓'
        };
    }
}
