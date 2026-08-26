<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the super admin profile page.
     */
    public function index(): View
    {
        $user = Auth::user();

        return view('admin.profile.profile', compact('user'));
    }

    /**
     * Update super admin profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'displayname' => ['nullable', 'string', 'max:255'],
            'mobilenumber' => ['nullable', 'string', 'max:30'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Profile details updated successfully.');
    }

    /**
     * Display the change password view.
     */
    public function changePassword(): View
    {
        $user = Auth::user();

        return view('admin.profile.change-password', compact('user'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // lowercase
                'regex:/[A-Z]/',      // uppercase
                'regex:/[0-9]/',      // number
                'regex:/[@$!%*#?&_\-+=~^]/', // special char
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->email && str_contains(strtolower($value), strtolower(explode('@', $user->email)[0]))) {
                        $fail('The password cannot contain your email or username.');
                    }
                },
            ],
        ], [
            'current_password.current_password' => 'The current password provided is incorrect.',
            'new_password.min' => 'The password must be at least 8 characters.',
            'new_password.regex' => 'The password must contain uppercase and lowercase letters, at least one number, and at least one special character.',
            'new_password.confirmed' => 'The new password and confirmation do not match.',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->passwordchangedat = now();
        $user->save();

        if ($request->boolean('sign_out_other_devices')) {
            Auth::logoutOtherDevices($request->new_password);
        }

        return back()->with('status', 'Password updated successfully.');
    }
}
