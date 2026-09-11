<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of administrators.
     */
    public function index(Request $request): View
    {
        $users = $this->userService->getFilteredUsers($request->all());
        $stats = $this->userService->getAdminStats();
        $roles = Role::orderBy('name')->pluck('name')->toArray() ?: $this->userService->getAvailableRoles();
        $departments = $this->userService->getAvailableDepartments();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'departments'));
    }

    /**
     * Show the Add Administrator form screen (matching reference image 4).
     */
    public function create(): View
    {
        $roles = Role::with('permissions')->where('is_active', true)->orderBy('name')->get();
        $departments = $this->userService->getAvailableDepartments();

        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created administrator.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:100',
            'displayname'              => 'nullable|string|max:100',
            'email'                    => 'required|email|max:150|unique:users,email',
            'mobilenumber'             => 'nullable|string|max:30',
            'department'               => 'nullable|string|max:100',
            'designation'              => 'nullable|string|max:100',
            'role_id'                  => 'required|exists:roles,id',
            'security_mode'            => 'required|in:activation_email,manual_password',
            'manual_password'          => 'required_if:security_mode,manual_password|nullable|string|min:8',
            'two_factor_enabled'       => 'nullable|boolean',
            'require_password_change'  => 'nullable|boolean',
            'account_status'           => 'nullable|boolean',
            'account_expiry'           => 'nullable|string|max:50',
        ]);

        $selectedRole = Role::findOrFail($validated['role_id']);

        $user = new User();
        $user->uuid = (string) Str::uuid();
        $user->name = $validated['name'];
        $user->username = Str::slug($validated['name']) . '_' . rand(100, 999);
        $user->displayname = $validated['displayname'] ?: $validated['name'];
        $user->email = $validated['email'];
        $user->mobilenumber = $validated['mobilenumber'] ?? null;
        $user->department = $validated['department'] ?? 'Operations';
        $user->role = $selectedRole->name; // Sync textual role name
        $user->admin_id = 'ADM-' . strtoupper(Str::random(5));
        
        // Security & Password
        if ($validated['security_mode'] === 'manual_password') {
            $user->password = Hash::make($validated['manual_password']);
            $user->status = $request->boolean('account_status', true) ? User::STATUS_ACTIVE : User::STATUS_PENDING;
        } else {
            // Activation Email mode: temporary random password, status pending activation
            $user->password = Hash::make(Str::random(16));
            $user->status = User::STATUS_PENDING;
        }

        $user->two_factor_enabled = $request->boolean('two_factor_enabled', true);
        $user->doj = now();
        $user->save();

        // Process Avatar Photo
        $this->processAvatar($request, $user);

        // Attach Role in Pivot Table
        $user->roles()->sync([$selectedRole->id]);

        return redirect()->route('admin.users.index')
            ->with('success', "Administrator account '{$user->name}' created successfully with assigned role '{$selectedRole->name}'.");
    }

    /**
     * Show the form for editing an administrator.
     */
    public function edit(int|string $id): View
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::with('permissions')->where('is_active', true)->orderBy('name')->get();
        $departments = $this->userService->getAvailableDepartments();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update the specified administrator.
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'                     => 'required|string|max:100',
            'displayname'              => 'nullable|string|max:100',
            'email'                    => 'required|email|max:150|unique:users,email,' . $user->id,
            'mobilenumber'             => 'nullable|string|max:30',
            'role_id'                  => 'required|exists:roles,id',
            'password'                 => 'nullable|string|min:8',
            'two_factor_enabled'       => 'nullable|boolean',
            'account_status'           => 'nullable|boolean',
        ]);

        $selectedRole = Role::findOrFail($validated['role_id']);

        $user->name = $validated['name'];
        $user->displayname = $validated['displayname'] ?: $validated['name'];
        $user->email = $validated['email'];
        $user->mobilenumber = $validated['mobilenumber'] ?? null;
        $user->role = $selectedRole->name;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->passwordchangedat = now();
        }

        $user->two_factor_enabled = $request->boolean('two_factor_enabled', false);
        $user->status = $request->boolean('account_status', true) ? User::STATUS_ACTIVE : User::STATUS_SUSPENDED;
        $user->save();

        // Process Avatar Photo
        $this->processAvatar($request, $user);

        // Sync Role
        $user->roles()->sync([$selectedRole->id]);

        return redirect()->route('admin.users.index')
            ->with('success', "Administrator account '{$user->name}' updated successfully.");
    }

    /**
     * Process avatar upload (either cropped base64 or raw file).
     */
    protected function processAvatar(Request $request, User $user): void
    {
        if ($request->filled('cropped_photo')) {
            $data = $request->input('cropped_photo');
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $ext = strtolower($type[1]) === 'jpeg' ? 'jpg' : strtolower($type[1]);
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $dir = public_path('uploads/avatars');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;
                    file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $decoded);
                    $user->avatar = 'uploads/avatars/' . $filename;
                    $user->save();
                }
            }
        } elseif ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $dir = public_path('uploads/avatars');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
            $user->save();
        }
    }

    /**
     * Remove the specified administrator.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Protect Super Admin account
        if ($user->role === 'Super Admin' || $user->roles()->where('code', 'SUPER_ADMIN')->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Super Admin account is permanently protected and cannot be deleted.');
        }

        // Prevent self deletion
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own active administrator session account.');
        }

        $userName = $user->name;
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Administrator '{$userName}' has been deleted successfully.");
    }
}
