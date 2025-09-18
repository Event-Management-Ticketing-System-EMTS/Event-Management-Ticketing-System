<?php

namespace App\Services\UserCreation;

use App\Models\User;

interface UserFactoryInterface
{
    /**
     * Create a user with the specified role and data
     *
     * @param array $userData
     * @param string $role
     * @return User
     */
    public function createUser(array $userData, string $role): User;

    /**
     * Validate user data for the specific role
     *
     * @param array $userData
     * @param string $role
     * @return array
     */
    public function validateUserData(array $userData, string $role): array;
}
