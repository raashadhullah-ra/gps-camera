<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Authenticate an admin user with credentials and remember option.
     *
     * @param array $credentials ['email' => string, 'password' => string]
     * @param bool $remember
     * @return array ['success' => bool, 'message' => string|null, 'user' => User|null]
     */
    public function login(array $credentials, bool $remember = false): array
    {
        $loginInput = trim($credentials['email'] ?? '');
        $password = $credentials['password'] ?? '';

        // Allow login via email or username
        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'user' => null,
            ];
        }

        // Check if account is blocked or suspended
        if ($user->isblocked || $user->status === User::STATUS_SUSPENDED) {
            return [
                'success' => false,
                'message' => 'This account has been blocked or suspended. Please contact the system administrator.',
                'user' => null,
            ];
        }

        // Log the user into the application
        Auth::login($user, $remember);

        // Update last login timestamp
        $user->lastloginat = now();
        $user->saveQuietly();

        // Regenerate session for security
        request()->session()->regenerate();

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user' => $user,
        ];
    }

    /**
     * Log the user out of the application and invalidate the session.
     */
    public function logout(): void
    {
        Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}
