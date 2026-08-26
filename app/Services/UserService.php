<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /**
     * Get filtered administrators list based on filter criteria.
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, User>
     */
    public function getFilteredUsers(array $filters = []): Collection
    {
        $query = User::query();

        // 1. Search Query (Name, Email, Admin ID)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('admin_id', 'like', "%{$search}%");
            });
        }

        // 2. Role Filter
        if (!empty($filters['role']) && $filters['role'] !== 'All Roles') {
            $query->where('role', $filters['role']);
        }

        // 3. Department Filter
        if (!empty($filters['department']) && $filters['department'] !== 'All Departments') {
            $query->where('department', $filters['department']);
        }

        // 4. Status Filter (active = 1, pending = 0, suspended/blocked = 2)
        if (isset($filters['status']) && $filters['status'] !== 'All Statuses') {
            $statusMap = [
                'Active' => User::STATUS_ACTIVE,
                'Pending' => User::STATUS_PENDING,
                'Suspended' => User::STATUS_SUSPENDED,
            ];

            if (isset($statusMap[$filters['status']])) {
                $query->where('status', $statusMap[$filters['status']]);
            }
        }

        // 5. 2FA Status Filter
        if (!empty($filters['two_factor']) && $filters['two_factor'] !== '2FA Status') {
            if ($filters['two_factor'] === 'Enabled') {
                $query->where('two_factor_enabled', true);
            } elseif ($filters['two_factor'] === 'Pending' || $filters['two_factor'] === 'Disabled') {
                $query->where('two_factor_enabled', false);
            }
        }

        return $query->orderBy('id', 'asc')->get();
    }

    /**
     * Get summary metrics for top administrator stat cards.
     *
     * @return array<string, int>
     */
    public function getAdminStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', User::STATUS_ACTIVE)->count(),
            'pending' => User::where('status', User::STATUS_PENDING)->count(),
            'suspended' => User::where('status', User::STATUS_SUSPENDED)->count(),
            'two_factor_enabled' => User::where('two_factor_enabled', true)->count(),
        ];
    }

    /**
     * Get available role options for dropdown filtering.
     *
     * @return array<int, string>
     */
    public function getAvailableRoles(): array
    {
        return [
            'Super Admin',
            'Operations Admin',
            'Analytics Admin',
            'Support Admin',
            'Release Manager',
            'Ads Manager',
            'Auditor',
        ];
    }

    /**
     * Get available department options for dropdown filtering.
     *
     * @return array<int, string>
     */
    public function getAvailableDepartments(): array
    {
        return [
            'Executive',
            'Operations',
            'Analytics',
            'Support',
            'Engineering',
            'Monetization',
            'Security',
            'Legal & Compliance',
        ];
    }
}
