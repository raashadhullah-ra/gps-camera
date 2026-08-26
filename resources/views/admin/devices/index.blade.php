@extends('layout')

@section('title', 'Installed Devices - GeoCam Admin')
@section('page_title', 'Installed Devices')

@section('breadcrumbs')
    <span>Installations</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Installed Devices</span>
@endsection

@section('content')
<div class="devices-page">

    <!-- 1. Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Installed Devices</h1>
            <p class="page-main-subtitle">Monitor anonymous installations, device activity and notification availability</p>
        </div>
        <div class="header-actions-group">
            <a href="{{ route('admin.devices.export') }}" class="btn btn-outline-primary" id="exportDevicesCsvBtn">
                <i class="fa-solid fa-download"></i>
                <span>Export CSV</span>
            </a>
            <button type="button" class="btn btn-primary" id="openSendNotificationModalBtn">
                <i class="fa-solid fa-plus"></i>
                <span>Send Notification</span>
            </button>
        </div>
    </div>

    <!-- 2. 5 Top Metric Stat Summary Cards (Exact Dashboard Style & Hover) -->
    <div class="stat-grid-row">
        <!-- 1. Total Devices -->
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Devices</div>
                <div class="stat-value">{{ $stats['total']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['total']['change'] }}
                </div>
            </div>
        </div>

        <!-- 2. Active Today -->
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Today</div>
                <div class="stat-value">{{ $stats['active_today']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['active_today']['change'] }}
                </div>
            </div>
        </div>

        <!-- 3. Inactive Devices -->
        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-solid fa-phone-slash"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Inactive Devices</div>
                <div class="stat-value">{{ $stats['inactive']['count'] }}</div>
                <div class="stat-trend trend-down">
                    <i class="fa-solid fa-arrow-down fs-10"></i> {{ $stats['inactive']['change'] }}
                </div>
            </div>
        </div>

        <!-- 4. Notifications Enabled -->
        <div class="stat-card">
            <div class="stat-icon-wrapper orange-bg">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Notifications Enabled</div>
                <div class="stat-value">{{ $stats['notifications_enabled']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['notifications_enabled']['change'] }}
                </div>
            </div>
        </div>

        <!-- 5. Invalid Tokens -->
        <div class="stat-card">
            <div class="stat-icon-wrapper red-bg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Invalid Tokens</div>
                <div class="stat-value">{{ $stats['invalid_tokens']['count'] }}</div>
                <div class="stat-trend trend-down">
                    <i class="fa-solid fa-arrow-down fs-10"></i> {{ $stats['invalid_tokens']['change'] }}
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filters Toolbar Card (Single Inline Row: 7 Filters + Reset & Apply Buttons) -->
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form action="{{ route('admin.devices.index') }}" method="GET" id="devicesFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap">
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">

            <!-- 1. Compact Search Box -->
            <div class="position-relative filter-search-wrap">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search by ID or model" value="{{ request('search') }}">
            </div>

            <!-- 2. Platform Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="platform" id="filter_platform" value="{{ request('platform', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-platform" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownPlatformBtn">
                    <span class="filter-box-label">Platform</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_platform">{{ request('platform', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownPlatformBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('platform') == 'All' || !request('platform') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', 'All')">All</a></li>
                    @foreach($filterOptions['platforms'] as $platform)
                        <li><a class="dropdown-item py-1 px-3 {{ request('platform') == $platform ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', '{{ $platform }}')">{{ $platform }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 3. Status Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="status" id="filter_status" value="{{ request('status', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-status" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownStatusBtn">
                    <span class="filter-box-label">Status</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_status">{{ request('status', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownStatusBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'All' || !request('status') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'All')">All</a></li>
                    @foreach($filterOptions['statuses'] as $status)
                        <li><a class="dropdown-item py-1 px-3 {{ request('status') == $status ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', '{{ $status }}')">{{ $status }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 4. App Version Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="app_version" id="filter_app_version" value="{{ request('app_version', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-version" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownAppVersionBtn">
                    <span class="filter-box-label">App Version</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_app_version">{{ request('app_version', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownAppVersionBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('app_version') == 'All' || !request('app_version') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('app_version', 'All')">All</a></li>
                    @foreach($filterOptions['versions'] as $ver)
                        <li><a class="dropdown-item py-1 px-3 {{ request('app_version') == $ver ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('app_version', '{{ $ver }}')">{{ $ver }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 5. Location Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="location" id="filter_location" value="{{ request('location', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-location" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownLocationBtn">
                    <span class="filter-box-label">Location</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_location">{{ request('location', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-md" aria-labelledby="dropdownLocationBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('location') == 'All' || !request('location') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location', 'All')">All</a></li>
                    @foreach($filterOptions['locations'] as $loc)
                        <li><a class="dropdown-item py-1 px-3 {{ request('location') == $loc ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location', '{{ $loc }}')">{{ $loc }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 6. Permission Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="permission" id="filter_permission" value="{{ request('permission', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-permission" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownPermissionBtn">
                    <span class="filter-box-label">Permission</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_permission">{{ request('permission', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-lg" aria-labelledby="dropdownPermissionBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('permission') == 'All' || !request('permission') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('permission', 'All')">All</a></li>
                    @foreach($filterOptions['permissions'] as $perm)
                        <li><a class="dropdown-item py-1 px-3 {{ request('permission') == $perm ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('permission', '{{ $perm }}')">{{ $perm }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 7. Installed Date Box (Flatpickr Calendar Popup) -->
            <div class="filter-date-box" onclick="document.getElementById('installedDateInput').focus()">
                <span class="filter-box-label">Installed Date</span>
                <div class="d-flex align-items-center gap-1">
                    <i class="fa-regular fa-calendar text-muted"></i>
                    <input type="text" name="installed_date" id="installedDateInput" class="filter-date-input" placeholder="Select date range" value="{{ request('installed_date') }}" readonly>
                </div>
            </div>

            <!-- 8. Reset & Apply Action Buttons (Inline Next to Installed Date) -->
            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Devices Found Subtext -->
    <div class="devices-found-bar">
        <span>{{ number_format($devices->total()) }} {{ Str::plural('device', $devices->total()) }} found</span>
    </div>

    <!-- 4. Device Installations Table Card -->
    <div class="devices-table-card">
        <div class="table-card-top-bar">
            <h2 class="table-title">Device Installations</h2>
            <div class="table-header-tools">
                <button type="button" class="btn-tool" onclick="showInfoToast('Column customization modal')">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Columns</span>
                </button>
                <button type="button" class="btn-tool" onclick="showInfoToast('Advanced Filter Builder')">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filters</span>
                </button>
            </div>
        </div>

        <!-- Filter Tabs Row -->
        <div class="devices-tabs-nav">
            @php
                $activeTab = request('tab', 'all');
            @endphp
            <a href="{{ route('admin.devices.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}" class="tab-item {{ $activeTab == 'all' ? 'active' : '' }}">
                All Devices
            </a>
            <a href="{{ route('admin.devices.index', array_merge(request()->except('tab'), ['tab' => 'active'])) }}" class="tab-item {{ $activeTab == 'active' ? 'active' : '' }}">
                Active
            </a>
            <a href="{{ route('admin.devices.index', array_merge(request()->except('tab'), ['tab' => 'inactive'])) }}" class="tab-item {{ $activeTab == 'inactive' ? 'active' : '' }}">
                Inactive
            </a>
            <a href="{{ route('admin.devices.index', array_merge(request()->except('tab'), ['tab' => 'notifications_off'])) }}" class="tab-item {{ $activeTab == 'notifications_off' ? 'active' : '' }}">
                Notifications Off
            </a>
            <a href="{{ route('admin.devices.index', array_merge(request()->except('tab'), ['tab' => 'invalid_token'])) }}" class="tab-item {{ $activeTab == 'invalid_token' ? 'active' : '' }}">
                Invalid Token
            </a>
        </div>

        <!-- Devices Table -->
        <div class="table-responsive">
            <table class="devices-data-table">
                <thead>
                    <tr>
                        <th class="th-checkbox">
                            <input type="checkbox" class="form-check-input" id="selectAllDevices">
                        </th>
                        <th>Installation ID</th>
                        <th>Device</th>
                        <th>Platform / OS</th>
                        <th>App Version</th>
                        <th>Location</th>
                        <th>Permissions</th>
                        <th>First Installed</th>
                        <th>Last Active</th>
                        <th>Notification</th>
                        <th>Status</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                        <tr>
                            <!-- 1. Checkbox -->
                            <td class="td-checkbox">
                                <input type="checkbox" class="form-check-input device-row-checkbox" value="{{ $device->id }}">
                            </td>

                            <!-- 2. Installation ID (Blue Monospace Link) -->
                            <td>
                                <a href="{{ route('admin.devices.show', $device->id) }}" class="installation-id-link">
                                    {{ $device->installation_id }}
                                </a>
                            </td>

                            <!-- 3. Device Model -->
                            <td class="device-model-cell">
                                <span class="device-name-text">{{ $device->device_model }}</span>
                            </td>

                            <!-- 4. Platform / OS with Icons -->
                            <td>
                                <div class="platform-cell">
                                    @if(strtolower($device->platform) === 'ios' || str_contains(strtolower($device->os_version), 'ios'))
                                        <i class="fa-brands fa-apple platform-icon ios-icon"></i>
                                    @else
                                        <i class="fa-brands fa-android platform-icon android-icon"></i>
                                    @endif
                                    <span>{{ $device->os_version }}</span>
                                </div>
                            </td>

                            <!-- 5. App Version -->
                            <td>
                                <span class="app-version-text">{{ $device->app_version }}</span>
                            </td>

                            <!-- 6. Location (Without flag) -->
                            <td>
                                <div class="location-cell">
                                    <span>{{ $device->location_formatted }}</span>
                                </div>
                            </td>

                            <!-- 7. Permissions Separate Badges (2x2 Layout with Distinct Colors) -->
                            <td>
                                <div class="perm-badges-container">
                                    <!-- Line 1: Location (Blue/Amber) & Camera (Emerald Green) -->
                                    <div class="perm-badges-row">
                                        <span class="perm-mini-badge perm-badge-location {{ $device->location_badge_class }}" title="{{ $device->location_permission_label }}">
                                            <i class="fa-solid fa-location-dot"></i> Location
                                        </span>
                                        <span class="perm-mini-badge perm-badge-camera {{ $device->camera_granted ? '' : 'is-denied' }}" title="Camera: {{ $device->camera_granted ? 'Granted' : 'Denied' }}">
                                            <i class="fa-solid fa-camera"></i> Camera
                                        </span>
                                    </div>
                                    <!-- Line 2: Notification (Violet) & Storage (Cyan/Teal) -->
                                    <div class="perm-badges-row">
                                        <span class="perm-mini-badge perm-badge-notification {{ $device->notifications_granted ? '' : 'is-denied' }}" title="Notification: {{ $device->notifications_granted ? 'Enabled' : 'Disabled' }}">
                                            <i class="fa-solid fa-bell"></i> Notification
                                        </span>
                                        <span class="perm-mini-badge perm-badge-storage {{ $device->storage_granted ? '' : 'is-denied' }}" title="Storage & Photos: {{ $device->storage_granted ? 'Granted' : 'Denied' }}">
                                            <i class="fa-solid fa-hard-drive"></i> Storage
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 8. First Installed -->
                            <td>
                                <span class="date-text">
                                    {{ $device->first_installed_at ? $device->first_installed_at->format('M d, Y') : 'Aug 18, 2026' }}
                                </span>
                            </td>

                            <!-- 9. Last Active -->
                            <td>
                                <span class="last-active-text">{{ $device->last_active_human }}</span>
                            </td>

                            <!-- 10. Notification Status -->
                            <td>
                                @if($device->notification_status === 'Enabled')
                                    <span class="notif-text enabled">Enabled</span>
                                @elseif($device->notification_status === 'Invalid Token')
                                    <span class="notif-text invalid">Invalid Token</span>
                                @else
                                    <span class="notif-text disabled">Disabled</span>
                                @endif
                            </td>

                            <!-- 11. Status (Dot + Text) -->
                            <td>
                                @if($device->is_active)
                                    <span class="status-indicator active">
                                        <span class="status-dot"></span> Active
                                    </span>
                                @else
                                    <span class="status-indicator inactive">
                                        <span class="status-dot"></span> Inactive
                                    </span>
                                @endif
                            </td>

                            <!-- 12. Actions (Eye + 3-dots Dropdown) -->
                            <td class="td-action">
                                <div class="action-cell-wrap">
                                    <!-- Quick View Eye Button -->
                                    <a href="{{ route('admin.devices.show', $device->id) }}" class="btn-action-icon" title="View Device Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>

                                    <!-- 3-Dots Dropdown Trigger -->
                                    <div class="action-dropdown-wrap">
                                        <button type="button" class="btn-action-dots" title="Device Actions" aria-label="Open Actions">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div class="device-action-dropdown-menu">
                                            <div class="menu-category-header">DEVICE ACTIONS</div>

                                            <a href="{{ route('admin.devices.show', $device->id) }}" class="action-menu-item">
                                                <i class="fa-regular fa-eye"></i>
                                                <span>View Device Details</span>
                                            </a>

                                            <a href="javascript:void(0)" class="action-menu-item" onclick="sendDirectNotification({{ $device->id }}, '{{ $device->installation_id }}', '{{ $device->device_model }}')">
                                                <i class="fa-regular fa-bell"></i>
                                                <span>Send Notification</span>
                                            </a>

                                            <a href="{{ route('admin.devices.show', $device->id) }}" class="action-menu-item">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                                <span>View Activity History</span>
                                            </a>

                                            <a href="{{ route('admin.devices.show', $device->id) }}" class="action-menu-item">
                                                <i class="fa-solid fa-location-dot"></i>
                                                <span>View Location</span>
                                            </a>

                                            <a href="{{ route('admin.devices.show', $device->id) }}" class="action-menu-item">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                                <span>View Crash History</span>
                                            </a>

                                            <a href="javascript:void(0)" class="action-menu-item" onclick="copyToClipboard('{{ $device->installation_id }}')">
                                                <i class="fa-regular fa-copy"></i>
                                                <span>Copy Installation ID</span>
                                            </a>

                                            <a href="{{ route('admin.devices.export') }}" class="action-menu-item">
                                                <i class="fa-solid fa-download"></i>
                                                <span>Export Device Data</span>
                                            </a>

                                            <div class="menu-divider"></div>

                                            @if($device->is_active)
                                                <button type="button" class="action-menu-item item-warning border-0 bg-transparent w-100 text-start" onclick="triggerMarkInactiveModal('{{ $device->id }}', '{{ $device->installation_id }}', '{{ $device->device_model }}', '{{ $device->platform }} {{ $device->os_version }}', '{{ $device->location_formatted }}')">
                                                    <i class="fa-solid fa-circle-pause"></i>
                                                    <span>Mark as Inactive</span>
                                                </button>
                                            @else
                                                <form action="{{ route('admin.devices.toggle-status', $device->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="action-menu-item text-success border-0 bg-transparent w-100 text-start">
                                                        <i class="fa-solid fa-circle-play"></i>
                                                        <span>Reactivate Device</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" class="action-menu-item item-danger border-0 bg-transparent w-100 text-start" onclick="triggerDeleteModal('{{ $device->id }}', '{{ $device->installation_id }}', '{{ $device->device_model }}', '{{ $device->platform }} {{ $device->os_version }}', '{{ $device->location_formatted }}')">
                                                <i class="fa-regular fa-trash-can"></i>
                                                <span>Delete Device Record</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-mobile-screen-button fs-2 mb-2 d-block text-secondary"></i>
                                <span>No installed devices found matching the filter criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 5. Table Footer Bar -->
        <div class="table-footer-bar">
            <div class="footer-left-info">
                @if($devices->total() > 0)
                    <span>Showing {{ $devices->firstItem() }} to {{ $devices->lastItem() }} of {{ number_format($devices->total()) }} {{ Str::plural('device', $devices->total()) }}</span>
                @else
                    <span>Showing 0 devices</span>
                @endif
            </div>

            <div class="footer-right-controls">
                <div class="rows-per-page-group">
                    <span>Rows per page</span>
                    <select class="form-select rows-select" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Custom Dynamic Pagination -->
                <div class="custom-pagination">
                    {{-- Previous Page Link --}}
                    @if ($devices->onFirstPage())
                        <button type="button" class="page-btn prev-btn" disabled aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $devices->previousPageUrl() }}" class="page-btn prev-btn" aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $curPage = $devices->currentPage();
                        $lastPage = $devices->lastPage();
                    @endphp

                    @if ($lastPage <= 7)
                        @for ($page = 1; $page <= $lastPage; $page++)
                            @if ($page == $curPage)
                                <button type="button" class="page-btn page-num active">{{ $page }}</button>
                            @else
                                <a href="{{ $devices->url($page) }}" class="page-btn page-num">{{ $page }}</a>
                            @endif
                        @endfor
                    @else
                        {{-- 1st Page --}}
                        @if ($curPage == 1)
                            <button type="button" class="page-btn page-num active">1</button>
                        @else
                            <a href="{{ $devices->url(1) }}" class="page-btn page-num">1</a>
                        @endif

                        @if ($curPage > 3)
                            <span class="page-ellipsis">...</span>
                        @endif

                        {{-- Middle Pages --}}
                        @for ($page = max(2, $curPage - 1); $page <= min($lastPage - 1, $curPage + 1); $page++)
                            @if ($page == $curPage)
                                <button type="button" class="page-btn page-num active">{{ $page }}</button>
                            @else
                                <a href="{{ $devices->url($page) }}" class="page-btn page-num">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($curPage < $lastPage - 2)
                            <span class="page-ellipsis">...</span>
                        @endif

                        {{-- Last Page --}}
                        @if ($curPage == $lastPage)
                            <button type="button" class="page-btn page-num active">{{ $lastPage }}</button>
                        @else
                            <a href="{{ $devices->url($lastPage) }}" class="page-btn page-num">{{ $lastPage }}</a>
                        @endif
                    @endif

                    {{-- Next Page Link --}}
                    @if ($devices->hasMorePages())
                        <a href="{{ $devices->nextPageUrl() }}" class="page-btn next-btn" aria-label="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <button type="button" class="page-btn next-btn" disabled aria-label="Next Page">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal 1: Send Push Campaign Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon-circle icon-blue" style="width: 36px; height: 36px; font-size: 14px;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark fs-16 mb-0" id="sendNotificationModalLabel">Send Push Notification</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="pushNotificationForm" onsubmit="handleSendNotification(event)">
                    <input type="hidden" id="notifDeviceId" value="">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-13">Target Audience</label>
                        <select class="form-select fs-13" id="notifTarget">
                            <option value="single">Single Target (Selected Device)</option>
                            <option value="all">All Installed Devices</option>
                            <option value="active">Active Devices Only</option>
                            <option value="android">Android Devices Only</option>
                            <option value="ios">iOS Devices Only</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-13">Notification Title *</label>
                        <input type="text" class="form-control fs-13" placeholder="e.g. New GPS Watermark Template Available!" required id="notifTitle">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-13">Message Body *</label>
                        <textarea class="form-control fs-13" rows="4" placeholder="Upgrade your GeoCam photos with our new real-time altitude & map stamp..." required id="notifBody"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark fs-13">Deep Link URL / Action (Optional)</label>
                        <input type="text" class="form-control fs-13" placeholder="geocam://watermark/styles" id="notifAction">
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn btn-outline-secondary fs-13 px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary fs-13 px-4 d-inline-flex align-items-center gap-2" id="btnSendCampaignSubmit">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Send Campaign</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Mark As Inactive Modal (Exact Screenshot 3 Design) -->
<div class="modal fade" id="modalMarkInactiveIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 rounded-4 shadow-lg p-4 position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

            <!-- Top Amber Icon Circle -->
            <div class="text-center mb-3">
                <div class="modal-icon-circle-amber">
                    <i class="fa-solid fa-pause"></i>
                </div>
                <h4 class="modal-title-custom">Mark Device as Inactive</h4>
                <p class="modal-subtitle-custom">This installation will remain in the system but will no longer be counted as an active device.</p>
            </div>

            <!-- Device Mini Pill Box -->
            <div class="device-pill-card mb-3">
                <div class="device-mini-icon">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <div class="device-mini-details">
                    <div class="fw-bold text-primary fs-13" id="inactiveModalInstallId">INS-8F29A1</div>
                    <div class="text-dark fs-12" id="inactiveModalDeviceName">Samsung Galaxy S24</div>
                </div>
                <div class="device-mini-meta ms-auto text-end">
                    <div class="fs-12 text-muted" id="inactiveModalPlatform">Android 15</div>
                    <div class="fs-11 text-muted" id="inactiveModalLocation">Tirunelveli, India</div>
                </div>
                <div class="ms-2">
                    <span class="status-dot-active">● Active</span>
                </div>
            </div>

            <form action="" method="POST" id="formMarkInactiveIndex">
                @csrf
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark">Reason *</label>
                    <select name="reason" class="form-select fs-13" required id="inactiveReasonSelectIndex">
                        <option value="" disabled selected>Select a reason</option>
                        <option value="User Uninstalled App">User Uninstalled App</option>
                        <option value="Device Inactive for 30+ Days">Device Inactive for 30+ Days</option>
                        <option value="Duplicate Installation">Duplicate Installation</option>
                        <option value="Suspected Fraud / Abuse">Suspected Fraud / Abuse</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark">Admin Note (Optional)</label>
                    <textarea name="admin_notes" class="form-control fs-13" rows="4" placeholder="Add an internal note" maxlength="500" id="inactiveNotesTextIndex" oninput="updateCharCountIndex(this)"></textarea>
                    <div class="text-end text-muted fs-11 mt-1" id="charCountLabelIndex">0/500</div>
                </div>

                <!-- Amber Callout Box -->
                <div class="callout-box-amber mb-3">
                    <div class="fw-bold fs-12 text-amber-dark mb-1">What happens next?</div>
                    <ul class="mb-0 ps-3 fs-12 text-amber-body">
                        <li>Device status changes to Inactive</li>
                        <li>Push notifications to this installation are paused</li>
                        <li>Historical activity and reports are preserved</li>
                        <li>The device can be reactivated later</li>
                    </ul>
                </div>

                <!-- Checkbox -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmInactiveCheckIndex" required onchange="toggleInactiveSubmitIndex()">
                    <label class="form-check-label fs-12 text-dark" for="confirmInactiveCheckIndex">
                        I understand the impact of this action.
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary fs-13 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fs-13 px-3 text-white" id="btnSubmitInactiveIndex" disabled>
                        Mark as Inactive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Delete Device Record Modal (Exact Screenshot 2 Design) -->
<div class="modal fade" id="modalDeleteDeviceIndex" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 rounded-4 shadow-lg p-4 position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

            <!-- Top Red Trash Icon Circle -->
            <div class="text-center mb-3">
                <div class="modal-icon-circle-red">
                    <i class="fa-regular fa-trash-can"></i>
                </div>
                <h4 class="modal-title-custom">Delete Device Record</h4>
                <p class="modal-subtitle-custom text-danger fw-semibold">This action cannot be undone.</p>
            </div>

            <!-- Summary Horizontal Box -->
            <div class="device-delete-summary mb-3">
                <div class="sum-col">
                    <div class="text-primary fw-bold fs-13" id="delModalInstallId">INS-8F29A1</div>
                    <div class="fs-11 text-muted">Installation ID</div>
                </div>
                <div class="sum-col">
                    <div class="fw-semibold text-dark fs-12" id="delModalDevice">Samsung Galaxy S24</div>
                    <div class="fs-11 text-muted">Device</div>
                </div>
                <div class="sum-col">
                    <div class="fs-12 text-dark" id="delModalPlatform">Android 15</div>
                    <div class="fs-11 text-muted">Platform</div>
                </div>
                <div class="sum-col">
                    <div class="fs-12 text-dark" id="delModalLocation">Tirunelveli, India</div>
                    <div class="fs-11 text-muted">Location</div>
                </div>
            </div>

            <!-- Red Danger Alert Box -->
            <div class="callout-box-red mb-3">
                <div class="fw-bold fs-12 text-danger mb-1">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> The following data will be permanently deleted:
                </div>
                <ul class="mb-0 ps-3 fs-12 text-danger-body">
                    <li>Installation and device information</li>
                    <li>FCM token and notification history</li>
                    <li>Location and permission records</li>
                    <li>Activity and device-specific crash history</li>
                </ul>
            </div>

            <form action="" method="POST" id="formDeleteDeviceIndex">
                @csrf
                @method('DELETE')

                <div class="mb-3">
                    <label class="form-label fs-12 fw-semibold text-dark">
                        Type <span class="text-danger fw-bold" id="delExpectedSpan">INS-8F29A1</span> to confirm
                    </label>
                    <input type="text" class="form-control fs-13" placeholder="Enter Installation ID" id="delConfirmInputIndex" oninput="checkDelMatchIndex()">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="delConfirmCheckIndex" onchange="checkDelMatchIndex()">
                    <label class="form-check-label fs-12 text-dark" for="delConfirmCheckIndex">
                        I understand that this record cannot be recovered.
                    </label>
                </div>

                <!-- Blue info note -->
                <div class="blue-hint-note mb-3">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span>Consider marking the device inactive if you may need its history later.</span>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary fs-13 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fs-13 px-3" id="btnConfirmDeleteFinalIndex" disabled>
                        <i class="fa-regular fa-trash-can me-1"></i> Delete Device Record
                    </button>
                </div>
            </form>
        </div>
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
            const menu = parentWrap ? parentWrap.querySelector('.device-action-dropdown-menu') : null;
            
            // Close other open menus first
            document.querySelectorAll('.device-action-dropdown-menu.show').forEach(openMenu => {
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
            document.querySelectorAll('.device-action-dropdown-menu.show').forEach(openMenu => {
                openMenu.classList.remove('show');
            });
        }
    });

    // 2. Select All Checkbox Handler
    const selectAllCheckbox = document.getElementById('selectAllDevices');
    const rowCheckboxes = document.querySelectorAll('.device-row-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        });
    }

    // 3. Initialize Bootstrap Dropdown Toggles
    const dropdownToggleList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownToggleList.forEach(dropdownToggleEl => {
        if (window.bootstrap && window.bootstrap.Dropdown) {
            bootstrap.Dropdown.getOrCreateInstance(dropdownToggleEl);
        }
    });

    // 4. Send Notification Modal Trigger
    const openNotifBtn = document.getElementById('openSendNotificationModalBtn');
    const notifModalEl = document.getElementById('sendNotificationModal');
    if (openNotifBtn && notifModalEl && window.bootstrap) {
        openNotifBtn.addEventListener('click', function () {
            const modal = bootstrap.Modal.getOrCreateInstance(notifModalEl);
            modal.show();
        });
    }

    // 5. Initialize Flatpickr Date Range Calendar
    if (window.flatpickr) {
        flatpickr('#installedDateInput', {
            mode: 'range',
            dateFormat: 'M d, Y',
            allowInput: true,
            defaultDate: @json(request('installed_date') ? explode(' to ', request('installed_date')) : null)
        });
    }
});

// Toast Helper Functions
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
        window.showToast('success', 'Installation ID copied', text + ' copied to clipboard');
    }).catch(() => {
        window.showToast('info', 'Installation ID', text);
    });
}

function setFilterOption(fieldName, value) {
    const input = document.getElementById('filter_' + fieldName);
    const label = document.getElementById('label_' + fieldName);
    if (input) input.value = value;
    if (label) label.textContent = value;

    // Highlight selected item in dropdown
    const dropdown = input ? input.closest('.dropdown') : null;
    if (dropdown) {
        dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            if (item.textContent.trim() === value.trim()) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }
}

function viewDeviceDetails(installId, model, os, location, version, perm, notif, lastActive) {
    if (window.Swal) {
        Swal.fire({
            title: '<span style="font-size: 19px; font-weight: 700; color: #0f172a;">Device Information</span>',
            html: `
                <div style="text-align: left; font-size: 13.5px; line-height: 1.8; color: #475569; padding: 10px 0;">
                    <div style="margin-bottom: 6px;"><strong>Installation ID:</strong> <span style="font-family: monospace; color: #2563eb; font-weight: 600;">${installId}</span></div>
                    <div style="margin-bottom: 6px;"><strong>Device Model:</strong> ${model}</div>
                    <div style="margin-bottom: 6px;"><strong>Platform / OS:</strong> ${os}</div>
                    <div style="margin-bottom: 6px;"><strong>Location:</strong> ${location}</div>
                    <div style="margin-bottom: 6px;"><strong>App Version:</strong> ${version}</div>
                    <div style="margin-bottom: 6px;"><strong>Permissions:</strong> ${perm}</div>
                    <div style="margin-bottom: 6px;"><strong>Push Notification:</strong> ${notif}</div>
                    <div><strong>Last Activity:</strong> ${lastActive}</div>
                </div>
            `,
            confirmButtonText: 'Close',
            confirmButtonColor: '#2563eb',
            customClass: {
                popup: 'rounded-4 p-4 border-0 shadow-lg'
            }
        });
    }
}

function sendDirectNotification(deviceId, installId, model) {
    document.getElementById('notifDeviceId').value = deviceId || '';
    const targetSelect = document.getElementById('notifTarget');
    if (targetSelect) {
        targetSelect.value = 'single';
    }
    const notifModalEl = document.getElementById('sendNotificationModal');
    if (notifModalEl && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(notifModalEl);
        modal.show();
    }
}

// Mark Inactive Modal Trigger (Screenshot 3)
function triggerMarkInactiveModal(id, installId, deviceName, platform, location) {
    document.getElementById('inactiveModalInstallId').textContent = installId;
    document.getElementById('inactiveModalDeviceName').textContent = deviceName;
    document.getElementById('inactiveModalPlatform').textContent = platform;
    document.getElementById('inactiveModalLocation').textContent = location;
    
    const form = document.getElementById('formMarkInactiveIndex');
    form.action = `/admin/devices/${id}/mark-inactive`;
    form.reset();
    document.getElementById('charCountLabelIndex').textContent = '0/500';
    document.getElementById('btnSubmitInactiveIndex').disabled = true;

    const modalEl = document.getElementById('modalMarkInactiveIndex');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function updateCharCountIndex(el) {
    document.getElementById('charCountLabelIndex').textContent = el.value.length + '/500';
}

function toggleInactiveSubmitIndex() {
    const select = document.getElementById('inactiveReasonSelectIndex');
    const check = document.getElementById('confirmInactiveCheckIndex');
    const btn = document.getElementById('btnSubmitInactiveIndex');
    btn.disabled = !(check.checked && select.value);
}

document.getElementById('inactiveReasonSelectIndex')?.addEventListener('change', toggleInactiveSubmitIndex);

// Delete Modal Trigger (Screenshot 2)
let currentDelInstallId = '';

function triggerDeleteModal(id, installId, deviceName, platform, location) {
    currentDelInstallId = installId;
    document.getElementById('delModalInstallId').textContent = installId;
    document.getElementById('delModalDevice').textContent = deviceName;
    document.getElementById('delModalPlatform').textContent = platform;
    document.getElementById('delModalLocation').textContent = location;
    document.getElementById('delExpectedSpan').textContent = installId;

    const form = document.getElementById('formDeleteDeviceIndex');
    form.action = `/admin/devices/${id}`;
    form.reset();
    document.getElementById('btnConfirmDeleteFinalIndex').disabled = true;

    const modalEl = document.getElementById('modalDeleteDeviceIndex');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function checkDelMatchIndex() {
    const inputVal = document.getElementById('delConfirmInputIndex').value.trim();
    const check = document.getElementById('delConfirmCheckIndex').checked;
    const btn = document.getElementById('btnConfirmDeleteFinalIndex');
    btn.disabled = !(inputVal === currentDelInstallId && check);
}

function handleSendNotification(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSendCampaignSubmit');
    const originalBtnHtml = btn.innerHTML;
    const target = document.getElementById('notifTarget').value;
    const deviceId = document.getElementById('notifDeviceId').value;
    const title = document.getElementById('notifTitle').value.trim();
    const body = document.getElementById('notifBody').value.trim();
    const deepLink = document.getElementById('notifAction')?.value.trim() || '';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    fetch('{{ route("admin.devices.send-campaign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            target: target,
            device_id: deviceId || null,
            title: title,
            body: body,
            deep_link: deepLink,
        }),
    })
    .then(async (res) => {
        const data = await res.json();
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;

        if (!res.ok || data.status === 'error') {
            throw new Error(data.message || 'Push dispatch failed');
        }

        const notifModalEl = document.getElementById('sendNotificationModal');
        if (notifModalEl && window.bootstrap) {
            const modal = bootstrap.Modal.getInstance(notifModalEl);
            if (modal) modal.hide();
        }
        document.getElementById('pushNotificationForm').reset();

        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Campaign Dispatched!',
                text: data.message || `Push notification dispatched via Firebase Cloud Messaging!`,
                confirmButtonColor: '#2563eb'
            });
        }
    })
    .catch((err) => {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;

        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'FCM Dispatch Error',
                text: err.message,
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert('Error: ' + err.message);
        }
    });
}

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    window.location.href = url.toString();
}
</script>
@endpush
