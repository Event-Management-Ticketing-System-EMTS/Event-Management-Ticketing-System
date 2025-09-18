<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\UserCreation\UserFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    protected $userFactory;

    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        try {
            // Get the selected role (default to 'user' if not provided)
            $role = $request->input('role', User::ROLE_USER);
            
            // Validate that the role is allowed
            $allowedRoles = [User::ROLE_USER, User::ROLE_ORGANIZER];
            if (!in_array($role, $allowedRoles)) {
                $role = User::ROLE_USER; // Default to user for invalid roles
            }

            // Create the user using the factory
            $user = $this->userFactory->createUser($request->all(), $role);

            // Log the user in
            Auth::login($user);

            // Redirect to home page with success message
            $roleDisplay = ucfirst($role);
            return redirect('/')->with('success', "Registration successful! Welcome to Event Management System as a {$roleDisplay}.");
            
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }
}
