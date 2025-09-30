<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get all users with sorting
     */
    public function getAllWithSorting(string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->orderBy($sortBy, $direction)
            ->get();
    }

    /**
     * Get users by role with sorting
     */
    public function getByRoleWithSorting(string $role, string $sortBy = 'created_at', string $direction = 'desc'): Collection
    {
        return $this->model
            ->where('role', $role)
            ->orderBy($sortBy, $direction)
            ->get();
    }

    /**
     * Get user by ID
     */
    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Get users count by role
     */
    public function countByRole(string $role): int
    {
        return $this->model->where('role', $role)->count();
    }

    /**
     * Get recent users (last 7 days)
     */
    public function getRecentUsers(int $days = 7): Collection
    {
        return $this->model
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update user role
     */
    public function updateRole(int $userId, string $role): bool
    {
        return $this->model->where('id', $userId)->update(['role' => $role]);
    }
}
