<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimplePasswordResetService;

/**
 * Simple Password Reset Controller
 *
 * Handles password reset requests using the Command Pattern
 * Commands: SendResetTokenCommand, VerifyResetTokenCommand, ResetPasswordCommand
 */
class SimplePasswordResetController extends Controller
{
    private $passwordResetService;

    public function __construct(SimplePasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     * Command: SendResetTokenCommand
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        // Check rate limiting
        if (!$this->passwordResetService->canRequestReset($request->email)) {
            return back()->withErrors(['email' => 'Please wait 5 minutes before requesting another reset.']);
        }

        $result = $this->passwordResetService->sendResetToken($request->email);

        if ($result['success']) {
            return back()->with('status', $result['message']);
        }

        return back()->withErrors(['email' => $result['message']]);
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle password reset
     * Command: ResetPasswordCommand
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // First verify the token
        $verification = $this->passwordResetService->verifyResetToken(
            $request->token,
            $request->email
        );

        if (!$verification['success']) {
            return back()->withErrors(['token' => $verification['message']]);
        }

        // Reset the password
        $result = $this->passwordResetService->resetPassword(
            $request->token,
            $request->email,
            $request->password
        );

        if ($result['success']) {
            return redirect()->route('login')->with('status', $result['message']);
        }

        return back()->withErrors(['email' => $result['message']]);
    }

    /**
     * Admin: View reset statistics
     */
    public function adminStats()
    {
        $stats = $this->passwordResetService->getResetStats();

        return view('admin.password-reset-stats', compact('stats'));
    }

    /**
     * Admin: Clean expired tokens
     * Command: CleanupExpiredTokensCommand
     */
    public function adminCleanup()
    {
        $result = $this->passwordResetService->cleanupExpiredTokens();

        return back()->with('status', $result['message']);
    }
}
