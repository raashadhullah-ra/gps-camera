<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the super admin login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle the admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter your admin email address or username.',
            'password.required' => 'Please enter your password.',
        ]);

        $remember = $request->boolean('remember');

        $result = $this->authService->login($credentials, $remember);

        if (!$result['success']) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => $result['message'],
                ]);
        }

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('status', 'Welcome back, ' . ($result['user']->displayname ?? $result['user']->name));
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot-password');
    }

    /**
     * Log the admin user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()
            ->route('admin.login')
            ->with('status', 'You have been signed out securely.');
    }
}
