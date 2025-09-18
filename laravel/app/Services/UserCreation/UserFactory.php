<?php

namespace App\Services\UserCreation;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserFactory implements UserFactoryInterface
{
    /**
     * Create a user with the specified role and data
     *
     * @param array $userData
     * @param string $role
     * @return User
     * @throws ValidationException
     */
    public function createUser(array $userData, string $role): User
    {
        // Validate the user data
        $validationRules = $this->validateUserData($userData, $role);
        
        $validator = Validator::make($userData, $validationRules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Hash the password
        $userData['password'] = Hash::make($userData['password']);
        
        // Set the role
        $userData['role'] = $role;
        
        // Set default email verification status
        $userData['email_verified'] = false;

        // Create and return the user
        return User::create($userData);
    }

    /**
     * Get validation rules for the specific role
     *
     * @param array $userData
     * @param string $role
     * @return array
     */
    public function validateUserData(array $userData, string $role): array
    {
        $baseRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Add role-specific validation rules
        switch ($role) {
            case User::ROLE_ORGANIZER:
                $baseRules['organization'] = 'nullable|string|max:255';
                $baseRules['phone'] = 'nullable|string|max:20';
                break;
                
            case User::ROLE_ADMIN:
                $baseRules['admin_code'] = 'required|string'; // Special code for admin registration
                break;
                
            case User::ROLE_USER:
            default:
                // Default user rules (already covered in base rules)
                break;
        }

        return $baseRules;
    }

    /**
     * Create a regular user
     *
     * @param array $userData
     * @return User
     */
    public function createRegularUser(array $userData): User
    {
        return $this->createUser($userData, User::ROLE_USER);
    }

    /**
     * Create an organizer user
     *
     * @param array $userData
     * @return User
     */
    public function createOrganizer(array $userData): User
    {
        return $this->createUser($userData, User::ROLE_ORGANIZER);
    }

    /**
     * Create an admin user
     *
     * @param array $userData
     * @return User
     */
    public function createAdmin(array $userData): User
    {
        return $this->createUser($userData, User::ROLE_ADMIN);
    }
}
