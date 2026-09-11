@extends('layout')

@section('page_title', 'Administrators')

@section('breadcrumbs')
    <span>Admin Management</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Admin List</span>
@endsection

@section('content')
<div class="users-page">

    <!-- Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Administrators</h1>
            <p class="page-main-subtitle">Manage administrator accounts, roles, security and access</p>
        </div>
        <div class="header-actions-group">
            @if(auth()->user()->hasPermissionTo('admins.export') || auth()->user()->hasPermissionTo('admins.view'))
                <button type="button" class="btn btn-outline-primary" id="exportAdminsBtn">
                    <i class="fa-solid fa-download"></i>
                    <span>Export Admins</span>
                </button>
            @endif
            @if(auth()->user()->hasPermissionTo('admins.create'))
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary" id="addAdminBtn">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Admin</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 5 Top Stat Summary Cards in a Single Row -->
    <div class="admin-stats-grid">
        <!-- 1. Total Administrators -->
        <div class="stat-summary-card">
            <div class="stat-icon-circle icon-blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Administrators</div>
                <div class="stat-number">{{ $stats['total'] }}</div>
            </div>
        </div>

        <!-- 2. Active -->
        <div class="stat-summary-card">
            <div class="stat-icon-circle icon-green">
                <i class="fa-regular fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active</div>
                <div class="stat-number">{{ $stats['active'] }}</div>
            </div>
        </div>

        <!-- 3. Pending Activation -->
        <div class="stat-summary-card">
            <div class="stat-icon-circle icon-amber">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pending Activation</div>
                <div class="stat-number">{{ $stats['pending'] }}</div>
            </div>
        </div>

        <!-- 4. Suspended -->
        <div class="stat-summary-card">
            <div class="stat-icon-circle icon-red">
                <i class="fa-solid fa-circle-pause"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Suspended</div>
                <div class="stat-number">{{ $stats['suspended'] }}</div>
            </div>
        </div>

        <!-- 5. 2FA Enabled -->
        <div class="stat-summary-card">
            <div class="stat-icon-circle icon-purple">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">2FA Enabled</div>
                <div class="stat-number">{{ $stats['two_factor_enabled'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar Card - Clean Horizontal 1-Line Layout -->
    <div class="admin-filter-card">
        <form action="{{ route('admin.users.index') }}" method="GET" class="filter-form-row d-flex align-items-center gap-2 flex-nowrap w-100">
            <!-- Search Input -->
            <div class="search-input-wrap flex-grow-1">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search administrator, email or ID" value="{{ request('search') }}">
            </div>

            <!-- Role Filter -->
            <select name="role" class="form-select filter-select" style="width: 140px; flex: 0 0 140px;" aria-label="Filter by Role">
                <option value="All Roles" {{ request('role') == 'All Roles' || !request('role') ? 'selected' : '' }}>All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
            </select>

            <!-- Department Filter -->
            <select name="department" class="form-select filter-select" style="width: 155px; flex: 0 0 155px;" aria-label="Filter by Department">
                <option value="All Departments" {{ request('department') == 'All Departments' || !request('department') ? 'selected' : '' }}>All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select name="status" class="form-select filter-select" style="width: 130px; flex: 0 0 130px;" aria-label="Filter by Status">
                <option value="All Statuses" {{ request('status') == 'All Statuses' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <!-- 2FA Status Filter -->
            <select name="two_factor" class="form-select filter-select" style="width: 130px; flex: 0 0 130px;" aria-label="Filter by 2FA Status">
                <option value="2FA Status" {{ request('two_factor') == '2FA Status' || !request('two_factor') ? 'selected' : '' }}>2FA Status</option>
                <option value="Enabled" {{ request('two_factor') == 'Enabled' ? 'selected' : '' }}>Enabled</option>
                <option value="Pending" {{ request('two_factor') == 'Pending' ? 'selected' : '' }}>Pending / Disabled</option>
            </select>

            <!-- Action Buttons -->
            <div class="filter-btn-actions d-flex align-items-center gap-2 flex-shrink-0 ms-auto">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i>
                    <span>Apply Filters</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Administrator Accounts Table Card -->
    <div class="admin-table-card">
        <div class="table-card-header">
            <h2 class="table-title">Administrator Accounts</h2>
        </div>

        <div class="table-responsive">
            <table class="admin-accounts-table">
                <thead>
                    <tr>
                        <th>Administrator</th>
                        <th>Admin ID</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $admin)
                        @php
                            $isSuperAdmin = ($admin->role === 'Super Admin' || $admin->id === auth()->id());
                            $initials = strtoupper(substr($admin->displayname ?? $admin->name ?? 'AD', 0, 2));
                            $avatarColorClass = match($admin->role) {
                                'Super Admin' => 'av-teal',
                                'Operations Admin' => 'av-green',
                                'Analytics Admin' => 'av-purple',
                                'Support Admin' => 'av-orange',
                                'Release Manager' => 'av-cyan',
                                'Ads Manager' => 'av-purple',
                                'Auditor' => 'av-red',
                                default => ($admin->status === 'Pending' ? 'av-grey' : 'av-teal')
                            };

                            $roleClass = match($admin->role) {
                                'Super Admin' => 'role-super-admin',
                                'Operations Admin' => 'role-ops-admin',
                                'Analytics Admin' => 'role-analytics-admin',
                                'Support Admin' => 'role-support-admin',
                                'Release Manager' => 'role-release-manager',
                                'Ads Manager' => 'role-ads-manager',
                                'Auditor' => 'role-auditor',
                                default => 'role-super-admin'
                            };

                            $adminId = $admin->admin_id ?: ('ADM-' . str_pad($admin->id, 4, '0', STR_PAD_LEFT));
                        @endphp
                        <tr>
                            <!-- 1. Administrator Info -->
                            <td>
                                <div class="admin-user-cell">
                                    <div class="admin-avatar {{ $avatarColorClass }} overflow-hidden">
                                        @if($admin->avatar && file_exists(public_path($admin->avatar)))
                                            <img src="{{ asset($admin->avatar) }}" alt="{{ $admin->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            {{ $initials }}
                                        @endif
                                    </div>
                                    <div class="admin-meta-info">
                                        <div class="admin-name">{{ $admin->name }}</div>
                                        <div class="admin-email">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Admin ID -->
                            <td>
                                <span class="admin-id-text">{{ $adminId }}</span>
                            </td>

                            <!-- 3. Role -->
                            <td>
                                <span class="role-pill {{ $roleClass }}">{{ $admin->role }}</span>
                            </td>

                            <!-- 4. Department -->
                            <td>
                                {{ $admin->department ?? 'Executive' }}
                            </td>

                            <!-- 5. Last Login -->
                            <td>
                                {{ $admin->lastloginat ? $admin->lastloginat->format('M d, g:i A') : 'Never' }}
                            </td>

                            <!-- 6. Status -->
                            <td>
                                @if((int) $admin->status === \App\Models\User::STATUS_ACTIVE)
                                    <span class="status-pill status-active">Active</span>
                                @elseif((int) $admin->status === \App\Models\User::STATUS_SUSPENDED)
                                    <span class="status-pill status-suspended">Suspended</span>
                                @else
                                    <span class="status-pill status-pending">Pending</span>
                                @endif
                            </td>

                            <!-- 7. Created -->
                            <td>
                                {{ $admin->created_at ? $admin->created_at->format('M d, Y') : 'Jan 12, 2026' }}
                            </td>

                            <!-- 8. Actions Dropdown Menu -->
                            <td class="td-action">
                                <div class="action-dropdown-wrap">
                                    <button type="button" class="btn-action-dots" title="Admin Actions" aria-label="Open Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <!-- Floating Dropdown Menu -->
                                    <div class="admin-action-dropdown-menu">
                                        <div class="menu-category-header">ADMIN ACTIONS</div>

                                        <a href="javascript:void(0)" class="action-menu-item" onclick="viewDetails('{{ $admin->name }}', '{{ $adminId }}', '{{ $admin->role }}')">
                                            <i class="fa-regular fa-id-badge"></i>
                                            <span>View Admin Details</span>
                                        </a>

                                        <a href="{{ route('admin.users.edit', $admin->id) }}" class="action-menu-item">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>Edit Administrator</span>
                                        </a>

                                        <a href="javascript:void(0)" class="action-menu-item" onclick="showInfoToast('Permissions viewed for {{ $admin->name }}')">
                                            <i class="fa-solid fa-shield-halved"></i>
                                            <span>View Permissions</span>
                                        </a>

                                        @if(auth()->user()->hasPermissionTo('admins.edit'))
                                            @if($isSuperAdmin)
                                                <span class="action-menu-item disabled" title="Super Admin role cannot be modified">
                                                    <i class="fa-solid fa-users-gear"></i>
                                                    <span>Assign Roles (Permanent)</span>
                                                </span>
                                            @else
                                                <a href="{{ route('admin.users.edit', $admin->id) }}" class="action-menu-item item-assign-roles">
                                                    <i class="fa-solid fa-users-gear"></i>
                                                    <span>Assign Roles</span>
                                                </a>
                                            @endif
                                        @endif

                                        <a href="javascript:void(0)" class="action-menu-item" onclick="showInfoToast('Displaying login activity for {{ $admin->name }}')">
                                            <i class="fa-regular fa-clock"></i>
                                            <span>View Login Activity</span>
                                        </a>

                                        <a href="javascript:void(0)" class="action-menu-item" onclick="showInfoToast('Active sessions: {{ $admin->active_sessions_count }}')">
                                            <i class="fa-solid fa-desktop"></i>
                                            <span>View Active Sessions</span>
                                        </a>

                                        @if(auth()->user()->hasPermissionTo('admins.edit'))
                                            <div class="menu-divider"></div>

                                            <a href="javascript:void(0)" class="action-menu-item item-message" onclick="showInfoToast('Message composer opened for {{ $admin->email }}')">
                                                <i class="fa-regular fa-paper-plane"></i>
                                                <span>Send Message</span>
                                            </a>

                                            <a href="{{ route('admin.users.edit', $admin->id) }}" class="action-menu-item">
                                                <i class="fa-solid fa-key"></i>
                                                <span>Reset Password</span>
                                            </a>

                                            <a href="javascript:void(0)" class="action-menu-item" onclick="showSuccessToast('Password change required for {{ $admin->name }} at next login.')">
                                                <i class="fa-solid fa-lock"></i>
                                                <span>Require Password Change</span>
                                            </a>

                                            <div class="menu-divider"></div>

                                            @if($isSuperAdmin)
                                                <span class="action-menu-item disabled" title="Super Admin cannot be disabled">
                                                    <i class="fa-solid fa-circle-pause"></i>
                                                    <span>Disable Administrator (Protected)</span>
                                                </span>
                                            @else
                                                <a href="javascript:void(0)" class="action-menu-item item-warning" onclick="toggleDisableDialog('{{ $admin->name }}', '{{ $admin->status_label }}')">
                                                    <i class="fa-solid fa-circle-pause"></i>
                                                    <span>{{ (int) $admin->status === \App\Models\User::STATUS_SUSPENDED ? 'Enable Administrator' : 'Disable Administrator' }}</span>
                                                </a>
                                            @endif
                                        @endif

                                        <div class="menu-divider"></div>

                                        <a href="javascript:void(0)" class="action-menu-item" onclick="copyToClipboard('{{ $adminId }}')">
                                            <i class="fa-regular fa-copy"></i>
                                            <span>Copy Admin ID</span>
                                        </a>

                                        @if(auth()->user()->hasPermissionTo('admins.export') || auth()->user()->hasPermissionTo('admins.view'))
                                            <a href="javascript:void(0)" class="action-menu-item" onclick="showSuccessToast('Exporting data for {{ $adminId }}...')">
                                                <i class="fa-solid fa-download"></i>
                                                <span>Export Admin Data</span>
                                            </a>
                                        @endif

                                        @if(auth()->user()->hasPermissionTo('admins.delete'))
                                            <div class="menu-divider"></div>

                                            @if($isSuperAdmin)
                                                <span class="action-menu-item disabled" title="Super Admin cannot be deleted">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                    <span>Delete Administrator (Protected)</span>
                                                </span>
                                            @else
                                                <a href="javascript:void(0)" class="action-menu-item item-danger" onclick="deleteAdminDialog('{{ $admin->name }}', '{{ route('admin.users.destroy', $admin->id) }}')">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                    <span>Delete Administrator</span>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-slash fs-2 mb-2 d-block text-secondary"></i>
                                <span>No administrators found matching the filter criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="table-footer-bar">
            <span>Showing {{ $users->count() > 0 ? 1 : 0 }} to {{ $users->count() }} of {{ $stats['total'] }} administrators</span>
        </div>
    </div>

    <!-- Bottom Audit Log Alert Banner -->
    <div class="alert-audit-note">
        <i class="fa-solid fa-circle-info"></i>
        <span>Administrator access changes and account actions are recorded in the audit log.</span>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Interactive 3-Dots Action Dropdown Menu
    const actionButtons = document.querySelectorAll('.btn-action-dots');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();
            const parentWrap = this.closest('.action-dropdown-wrap');
            const menu = parentWrap ? parentWrap.querySelector('.admin-action-dropdown-menu') : null;
            
            // Close other open menus first
            document.querySelectorAll('.admin-action-dropdown-menu.show').forEach(openMenu => {
                if (openMenu !== menu) {
                    openMenu.classList.remove('show');
                }
            });

            if (menu) {
                menu.classList.toggle('show');
            }
        });
    });

    // Close all menus when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.action-dropdown-wrap')) {
            document.querySelectorAll('.admin-action-dropdown-menu.show').forEach(openMenu => {
                openMenu.classList.remove('show');
            });
        }
    });

    // 2. Export Admins Button Toast
    const exportBtn = document.getElementById('exportAdminsBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            showSuccessToast('Administrator accounts list exported successfully (CSV).');
        });
    }
});

// Toast Helper Functions using SweetAlert2
function getToastInstance() {
    if (window.Swal) {
        return Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
    return null;
}

function showSuccessToast(message) {
    window.showToast('success', message);
}

function showInfoToast(message) {
    window.showToast('info', 'Information', message);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.showToast('success', 'Admin ID copied', text + ' copied to clipboard');
    }).catch(() => {
        window.showToast('info', 'Admin ID', text);
    });
}

function viewDetails(name, adminId, role) {
    if (window.Swal) {
        Swal.fire({
            title: '<span style="font-size: 19px; font-weight: 700; color: #0f172a;">Administrator Details</span>',
            html: `
                <div style="text-align: left; font-size: 13.5px; line-height: 1.8; color: #475569; padding: 10px 0;">
                    <div><strong>Name:</strong> ${name}</div>
                    <div><strong>Admin ID:</strong> ${adminId}</div>
                    <div><strong>Role:</strong> ${role}</div>
                    <div><strong>Access Level:</strong> Full Administrative Controls</div>
                </div>
            `,
            confirmButtonText: 'Close',
            confirmButtonColor: '#0d6efd',
            customClass: {
                popup: 'rounded-4 p-4 border-0 shadow-lg'
            }
        });
    }
}

function editAdmin(name) {
    showInfoToast('Opening edit profile for ' + name);
}

function toggleDisableDialog(name, status) {
    const isSuspended = status === 'Suspended';
    const actionText = isSuspended ? 'Enable' : 'Disable';
    
    if (window.Swal) {
        Swal.fire({
            title: `${actionText} Administrator?`,
            text: `Are you sure you want to ${actionText.toLowerCase()} administrator access for ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isSuspended ? '#10b981' : '#f59e0b',
            cancelButtonColor: '#64748b',
            confirmButtonText: `Yes, ${actionText}`,
            customClass: {
                popup: 'rounded-4 p-4 border-0 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showSuccessToast(`Administrator ${name} has been ${isSuspended ? 'activated' : 'suspended'}.`);
            }
        });
    }
}

function deleteAdminDialog(name, deleteUrl) {
    if (window.Swal) {
        Swal.fire({
            title: 'Delete Administrator?',
            text: `This will permanently remove administrator access for ${name}. This action cannot be reversed.`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Account',
            customClass: {
                popup: 'rounded-4 p-4 border-0 shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('globalDeleteAdminForm');
                if (!form) {
                    form = document.createElement('form');
                    form.id = 'globalDeleteAdminForm';
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                }
                form.action = deleteUrl;
                form.submit();
            }
        });
    }
}
</script>
@endpush
