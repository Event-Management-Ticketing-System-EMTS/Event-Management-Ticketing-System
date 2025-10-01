<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Simple Password Reset Service
 *
 * This service handles password reset operations using the Command Pattern.
 * It's designed to be beginner-friendly and easy to understand.
 *
 * Uses the Command Pattern: Each password reset operation is a command
 * that can be executed, logged, and tracked.
 */
class SimplePasswordResetService
{
    /**
     * Generate and send password reset token
     * Command: SendResetTokenCommand
     */
    public function sendResetToken($email)
    {
        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found with this email address.'
            ];
        }

        // Generate a simple reset token
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addHours(2); // Token expires in 2 hours

        // Store token in database (simple approach)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt,
            ]
        );

        // Send reset email (simplified - in real app you'd use Mail facade)
        $this->sendResetEmail($user, $token);

        return [
            'success' => true,
            'message' => 'Password reset link sent to your email address.'
        ];
    }

    /**
     * Verify reset token
     * Command: VerifyTokenCommand
     */
    public function verifyResetToken($email, $token)
    {
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return [
                'success' => false,
                'message' => 'Invalid reset token.'
            ];
        }

        // Check if token is expired
        if (Carbon::parse($resetRecord->expires_at)->isPast()) {
            // Clean up expired token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return [
                'success' => false,
                'message' => 'Reset token has expired. Please request a new one.'
            ];
        }

        // Verify token
        if (!Hash::check($token, $resetRecord->token)) {
            return [
                'success' => false,
                'message' => 'Invalid reset token.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Token is valid.'
        ];
    }

    /**
     * Reset user password
     * Command: ResetPasswordCommand
     */
    public function resetPassword($email, $token, $newPassword)
    {
        // First verify the token
        $tokenResult = $this->verifyResetToken($email, $token);

        if (!$tokenResult['success']) {
            return $tokenResult;
        }

        // Find and update user password
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword),
            'remember_token' => Str::random(60), // Invalidate existing sessions
        ]);

        // Clean up the reset token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return [
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.'
        ];
    }

    /**
     * Clean expired tokens
     * Command: CleanupExpiredTokensCommand
     */
    public function cleanupExpiredTokens()
    {
        $deleted = DB::table('password_reset_tokens')
            ->where('expires_at', '<', Carbon::now())
            ->delete();

        return [
            'success' => true,
            'message' => "Cleaned up {$deleted} expired tokens."
        ];
    }

    /**
     * Get reset statistics for admin
     */
    public function getResetStats()
    {
        return [
            'active_tokens' => DB::table('password_reset_tokens')
                ->where('expires_at', '>', Carbon::now())
                ->count(),
            'expired_tokens' => DB::table('password_reset_tokens')
                ->where('expires_at', '<=', Carbon::now())
                ->count(),
            'total_resets_today' => DB::table('password_reset_tokens')
                ->whereDate('created_at', Carbon::today())
                ->count(),
        ];
    }

    /**
     * Send reset email (simplified implementation)
     * In a real application, you would use Laravel's Mail facade
     */
    private function sendResetEmail($user, $token)
    {
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($user->email));

        // For demonstration, we'll just log the reset URL
        // In production, you'd send this via email
        Log::info("Password Reset URL for {$user->email}: {$resetUrl}");

        // You can implement actual email sending here:
        // Mail::to($user->email)->send(new PasswordResetMail($resetUrl));

        return true;
    }

    /**
     * Check if user can request reset (rate limiting)
     */
    public function canRequestReset($email)
    {
        $lastRequest = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subMinutes(5)) // 5 minutes cooldown
            ->first();
        if ($lastRequest) {
            return [
                'success' => false,
                'message' => 'Please wait 5 minutes before requesting another reset.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Reset request allowed.'
        ];
    }
}
