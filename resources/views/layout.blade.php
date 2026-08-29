<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'GPS Camera Admin')</title>

    <!-- Vite SCSS & JS Bundles (Includes Bootstrap 5, FontAwesome, Inter Font, Chart.js, SweetAlert2, Flatpickr) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="@yield('body-class', '')">

    <!-- Admin Shell Layout (Stable Sidebar & Top Navbar) -->
    <div class="admin-wrapper" id="adminWrapper">
        
        <!-- Fixed Left Sidebar Panel -->
        <aside class="sidebar-panel">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('assets/Logos/weblogo.png') }}" alt="GeoCam Logo" class="logo-img">
                    <span class="logo-text">Raiyaan apps Admin</span>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-header">OVERVIEW</div>
                <div class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
                    <a href="{{ url('/admin') }}" class="nav-link">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="menu-header">INSTALLATIONS</div>
                <div class="nav-item {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.devices.index') }}" class="nav-link">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <span>Installed Devices</span>
                    </a>
                </div>
                
                <div class="nav-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.locations.index') }}" class="nav-link">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Locations</span>
                    </a>
                </div>

                <div class="menu-header">ENGAGEMENT</div>
                <div class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.notifications.index') }}" class="nav-link">
                        <i class="fa-solid fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </div>
                <div class="nav-item {{ request()->routeIs('admin.segments.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.segments.index') }}" class="nav-link">
                        <i class="fa-solid fa-users"></i>
                        <span>Audience Segments</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Analytics</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Crash Reports</span>
                    </a>
                </div>

                <div class="menu-header">APP CONTROL</div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Remote Config</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>App Versions</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-rectangle-ad"></i>
                        <span>Ads Management</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Permissions</span>
                    </a>
                </div>

                <div class="menu-header">SYSTEM</div>
                <div class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-gear"></i>
                        <span>Admin Users</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-key"></i>
                        <span>Roles & Access</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('admin.settings.firebase.index') }}" class="nav-link {{ request()->routeIs('admin.settings.firebase.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-fire"></i>
                        <span>Firebase Settings</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Page Workspace Area -->
        <main class="admin-main">
            <!-- Sticky Top Navbar Header -->
            <header class="top-navbar">
                <div class="navbar-left">
                    <button class="sidebar-toggle" id="sidebarToggleBtn" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="breadcrumb-trail">
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <span>Overview</span>
                            <i class="fa-solid fa-chevron-right fs-10"></i>
                            <span class="active-crumb">@yield('page_title', 'Dashboard')</span>
                        @endif
                    </div>
                </div>

                <div class="navbar-right">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" placeholder="Search notifications, installations...">
                    </div>

                    <!-- <button class="icon-btn" title="Messages">
                        <i class="fa-regular fa-envelope"></i>
                        <span class="badge-count badge-blue">5</span>
                    </button> -->

                    <button class="icon-btn" title="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="badge-count">8</span>
                    </button>

                    <div class="user-profile-menu dropdown position-relative">
                        <button type="button" class="btn p-0 border-0 bg-transparent d-flex align-items-center text-decoration-none dropdown-toggle text-dark shadow-none" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-circle">{{ strtoupper(substr(auth()->user()->name ?? 'SA', 0, 2)) }}</div>
                            <span class="user-name ms-2">{{ auth()->user()->displayname ?? auth()->user()->name ?? 'Super Admin' }}</span>
                            <i class="fa-solid fa-chevron-down fs-11 text-muted ms-1"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 mt-2 py-2" id="userProfileMenu" aria-labelledby="userProfileDropdown" style="min-width: 230px; font-size: 13.5px; z-index: 1060;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-semibold text-dark">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                                <div class="text-muted fs-12">{{ auth()->user()->email ?? 'admin@gpscamera.app' }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.profile') }}">
                                    <i class="fa-solid fa-user text-secondary" style="width: 16px;"></i> Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.profile.change-password') }}">
                                    <i class="fa-solid fa-key text-secondary" style="width: 16px;"></i> Change Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" id="openLogoutModalBtn" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                                    <i class="fa-solid fa-arrow-right-from-bracket" style="width: 16px;"></i> Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Workspace Content -->
            @yield('content')
        </main>

    </div>

    @stack('modals')

    <!-- Confirm Logout Modal (Matches Reference Image) -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content logout-modal-content position-relative">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <!-- Top Red Logout Icon Circle -->
                <div class="logout-icon-circle">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 17L21 12L16 7" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12H9" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <!-- Modal Title & Subtitle -->
                <h4 class="logout-modal-title" id="logoutConfirmModalLabel">Confirm Logout</h4>
                <p class="logout-modal-subtitle">Are you sure you want to sign out of the Super Admin Portal?</p>

                <!-- Current Session Box -->
                <div class="current-session-box">
                    <div class="session-title">Current Session</div>
                    <div class="session-item">
                        <i class="fa-solid fa-display"></i>
                        <span>Windows 11 · Chrome</span>
                    </div>
                    <div class="session-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Tirunelveli, Tamil Nadu</span>
                    </div>
                    <div class="session-item">
                        <i class="fa-regular fa-clock"></i>
                        <span>Last activity: Just now</span>
                    </div>
                </div>

                <!-- Sign out active devices checkbox -->
                <div class="devices-checkbox-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="signOutAllDevices" name="all_devices">
                        <label class="form-check-label" for="signOutAllDevices">
                            Sign out from all active devices
                            <span class="subtext">This will also end your other 2 active sessions.</span>
                        </label>
                    </div>
                </div>

                <!-- Unsaved drafts warning alert -->
                <div class="alert-warning-draft">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>Any unsaved configuration changes will remain as drafts.</div>
                </div>

                <!-- Buttons Row -->
                <div class="logout-btn-group">
                    <button type="button" class="btn btn-cancel-logout" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-confirm-logout">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Yes, Logout</span>
                        </button>
                    </form>
                </div>

                <!-- Bottom Hint -->
                <div class="logout-bottom-hint">
                    You'll need your password and two-factor code to sign in again.
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Session Flash Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Global SweetAlert2 Top-Right Toast Notifications
            @if (session('status'))
                window.showToast ? window.showToast('success', @json(session('status'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('status')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
            @endif

            @if (session('success'))
                window.showToast ? window.showToast('success', @json(session('success'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
            @endif

            @if (session('error'))
                window.showToast ? window.showToast('error', @json(session('error'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
            @endif

            @if (isset($errors) && $errors->any())
                window.showToast ? window.showToast('error', @json($errors->first())) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json($errors->first()), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
            @endif
        });
    </script>
</body>
</html>
