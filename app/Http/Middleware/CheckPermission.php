<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request and check module-level permission.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Super Admin bypasses all checks
        if ($user->role === 'Super Admin' || $user->roles()->where('code', 'SUPER_ADMIN')->exists()) {
            return $next($request);
        }

        // Check if user possesses ANY of the required permissions
        foreach ($permissions as $perm) {
            if ($user->hasPermissionTo($perm)) {
                return $next($request);
            }
        }

        // Denied: abort 403 with clear explanation
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You do not possess the required permission (' . implode(', ', $permissions) . ') to perform this action.',
            ], 403);
        }

        return abort(403, 'Unauthorized: Your administrator role does not have permission [' . implode(', ', $permissions) . '] to access this resource.');
    }
}
