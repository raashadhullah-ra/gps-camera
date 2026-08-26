<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
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
        $roles = $this->userService->getAvailableRoles();
        $departments = $this->userService->getAvailableDepartments();

        return view('admin.users.index', compact('users', 'stats', 'roles', 'departments'));
    }
}
