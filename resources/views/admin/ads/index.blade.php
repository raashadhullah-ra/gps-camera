@extends('layout')

@section('title', 'Ads Management - GeoCam Admin')
@section('page_title', 'Ads Management')

@section('breadcrumbs')
    <span>App Control</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Ads Management</span>
@endsection

@section('content')
<div class="ads-page">

    <!-- 1. Page Header Row -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Ads Management</h1>
            <p class="page-main-subtitle">Manage Google AdMob units, custom campaigns, placements and delivery performance</p>
        </div>
        <div class="header-actions-group">
            <a href="{{ route('admin.ads.configuration') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-gear"></i>
                <span>Ad Settings</span>
            </a>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#placementManagerModal">
                <i class="fa-regular fa-clone"></i>
                <span>Placement Manager</span>
            </button>
            <a href="{{ route('admin.ads.custom.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Create Ad</span>
            </a>
        </div>
    </div>

    <!-- 2. Integration / Status Alert Banner -->
    <div class="ad-connection-banner {{ !empty($stats['is_expired']) ? 'border-warning bg-warning-subtle' : '' }}">
        <div class="banner-left">
            @if(!empty($stats['is_expired']))
                <i class="fa-solid fa-triangle-exclamation banner-icon text-warning"></i>
            @else
                <i class="fa-solid fa-circle-info banner-icon text-primary"></i>
            @endif
            <span>{{ $stats['status_message'] ?? 'Google AdMob is connected and reporting normally.' }}</span>
        </div>
        <div class="banner-right">
            @if(!empty($stats['is_expired']))
                <span class="badge bg-warning text-dark px-2 py-1 fs-12">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Session Expired
                </span>
                <a href="{{ route('admin.ads.configuration') }}" class="btn btn-warning btn-sm fw-bold text-dark text-decoration-none px-3">
                    Reconnect Google Account
                </a>
            @else
                <span class="badge-connected">
                    <span class="status-dot"></span>
                    <span>{{ !empty($stats['is_live']) ? 'Live Sync' : 'Connected' }}</span>
                </span>
                <a href="{{ route('admin.ads.configuration') }}" class="btn-manage-integration text-decoration-none">
                    Manage Integration
                </a>
            @endif
        </div>
    </div>

    <!-- 3. Top 5 Metric Stat Summary Cards -->
    <div class="ad-stat-grid">
        <!-- 1. Estimated Revenue -->
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <span class="currency-symbol">₹</span>
            </div>
            <div class="stat-info">
                <div class="stat-label">Estimated Revenue</div>
                <div class="stat-value">{{ $stats['revenue'] }}</div>
                <div class="stat-subtext">{{ $stats['revenue_subtext'] }}</div>
            </div>
        </div>

        <!-- 2. Impressions -->
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Impressions</div>
                <div class="stat-value">{{ $stats['impressions'] }}</div>
            </div>
        </div>

        <!-- 3. Fill Rate -->
        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Fill Rate</div>
                <div class="stat-value">{{ $stats['fill_rate'] }}</div>
            </div>
        </div>

        <!-- 4. Click-through Rate -->
        <div class="stat-card">
            <div class="stat-icon-wrapper teal-bg">
                <i class="fa-solid fa-arrow-pointer"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Click-through Rate</div>
                <div class="stat-value">{{ $stats['ctr'] }}</div>
            </div>
        </div>

        <!-- 5. Active Ads -->
        <div class="stat-card">
            <div class="stat-icon-wrapper orange-bg">
                <i class="fa-solid fa-rectangle-ad"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Active Ads</div>
                <div class="stat-value">{{ $stats['active_ads'] }}</div>
            </div>
        </div>
    </div>

    <!-- 4. Filters Toolbar Card (Single Inline Row: 6 Filters + Reset & Apply Buttons) -->
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form action="{{ route('admin.ads.index') }}" method="GET" id="adsFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap">
            <input type="hidden" name="tab" value="{{ $currentTab }}">

            <!-- 1. Compact Search Box (Expands dynamically to absorb all empty space) -->
            <div class="position-relative filter-search-wrap flex-grow-1">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input w-100" placeholder="Search by name, ID or placement" value="{{ request('search') }}">
            </div>

            <!-- 2. Provider Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="provider" id="filter_provider" value="{{ request('provider', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-provider" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownProviderBtn">
                    <span class="filter-box-label">Provider</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_provider">{{ request('provider', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownProviderBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('provider') == 'All' || !request('provider') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('provider', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('provider') == 'Google AdMob' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('provider', 'Google AdMob')">Google AdMob</a></li>
                </ul>
            </div>

            <!-- 3. Format Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="format" id="filter_format" value="{{ request('format', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-format" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownFormatBtn">
                    <span class="filter-box-label">Format</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_format">{{ request('format', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownFormatBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('format') == 'All' || !request('format') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('format', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('format') == 'Banner' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('format', 'Banner')">Banner</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('format') == 'App open' || request('format') == 'App Open' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('format', 'App open')">App open</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('format') == 'Native' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('format', 'Native')">Native</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('format') == 'Interstitial' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('format', 'Interstitial')">Interstitial</a></li>
                </ul>
            </div>

            <!-- 4. Placement Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="placement" id="filter_placement" value="{{ request('placement', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-placement" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownPlacementBtn">
                    <span class="filter-box-label">Placement</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_placement">{{ request('placement', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-md" aria-labelledby="dropdownPlacementBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'All' || !request('placement') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Camera home' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Camera home')">Camera home</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Photo preview' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Photo preview')">Photo preview</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'After every 3 saves' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'After every 3 saves')">After every 3 saves</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Settings' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Settings')">Settings</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Gallery screen' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Gallery screen')">Gallery screen</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Share success' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Share success')">Share success</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'Camera settings' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'Camera settings')">Camera settings</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('placement') == 'App launch' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('placement', 'App launch')">App launch</a></li>
                </ul>
            </div>

            <!-- 5. Audience Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="audience" id="filter_audience" value="{{ request('audience', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-audience" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownAudienceBtn">
                    <span class="filter-box-label">Audience</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_audience">{{ request('audience', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-md" aria-labelledby="dropdownAudienceBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'All' || !request('audience') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'Free users' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'Free users')">Free users</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'India' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'India')">India</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'High-frequency users' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'High-frequency users')">High-frequency users</a></li>
                </ul>
            </div>

            <!-- 6. Date Range Dropdown (Bootstrap) -->
            <div class="dropdown">
                <input type="hidden" name="date_range" id="filter_date_range" value="{{ request('date_range', 'Last 30 days') }}">
                <button class="btn filter-dropdown-btn w-dropdown-date" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownDateRangeBtn">
                    <span class="filter-box-label">Date Range</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_date_range">{{ request('date_range', 'Last 30 days') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownDateRangeBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('date_range') == 'Today' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('date_range', 'Today')">Today</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('date_range') == 'Last 7 days' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('date_range', 'Last 7 days')">Last 7 days</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('date_range') == 'Last 30 days' || !request('date_range') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('date_range', 'Last 30 days')">Last 30 days</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('date_range') == 'This Month' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('date_range', 'This Month')">This Month</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('date_range') == 'All Time' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('date_range', 'All Time')">All Time</a></li>
                </ul>
            </div>

            <!-- 7. Reset & Apply Action Buttons (Inline Next to Date Range) -->
            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- 5. Ads & Campaigns Table Card -->
    <div class="ads-table-card">
        <!-- Table Top Section -->
        <div class="table-top-section">
            <h2 class="table-title">Ads & Campaigns</h2>
        </div>

        <!-- Filter Tabs Row -->
        <div class="ads-tabs-nav">
            <a href="{{ url('/ad-management?tab=all') }}" class="tab-item {{ $currentTab === 'all' ? 'active' : '' }}">
                All Ads
            </a>
            <a href="{{ url('/ad-management?tab=admob') }}" class="tab-item {{ $currentTab === 'admob' ? 'active' : '' }}">
                AdMob Units
            </a>
            <a href="{{ url('/ad-management?tab=paused') }}" class="tab-item {{ $currentTab === 'paused' ? 'active' : '' }}">
                Paused
            </a>
        </div>

        <!-- Responsive Table -->
        <div class="table-responsive">
            <table class="ads-table">
                <thead>
                    <tr>
                        <th>Ad / Campaign</th>
                        <th>Provider</th>
                        <th>Format</th>
                        <th>Placement</th>
                        <th>Audience</th>
                        <th>
                            Impressions
                            <i class="fa-regular fa-circle-question th-info-icon" title="Delivered impressions count"></i>
                        </th>
                        <th>CTR</th>
                        <th>
                            Revenue / Result
                            <i class="fa-regular fa-circle-question th-info-icon" title="Estimated AdMob earnings or conversions"></i>
                        </th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th class="th-action"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paginatedAds as $ad)
                        <tr>
                            <!-- 1. Ad / Campaign -->
                            <td class="ad-title-cell">
                                <div class="ad-name">{{ $ad['name'] }}</div>
                                <div class="ad-unit-id">{{ $ad['unit_id'] }}</div>
                            </td>

                            <!-- 2. Provider -->
                            <td>
                                <div class="provider-cell">
                                    @if($ad['provider_type'] === 'admob')
                                        <i class="fa-brands fa-google provider-icon-admob"></i>
                                        <span>Google AdMob</span>
                                    @else
                                        <i class="fa-regular fa-circle-dot provider-icon-custom"></i>
                                        <span>Custom</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 3. Format -->
                            <td>
                                @if($ad['format'] === 'Banner')
                                    <span class="badge-format badge-banner">Banner</span>
                                @elseif($ad['format'] === 'App open' || $ad['format'] === 'App Open')
                                    <span class="badge-format badge-app-open">App open</span>
                                @elseif($ad['format'] === 'Native')
                                    <span class="badge-format badge-native">Native</span>
                                @elseif($ad['format'] === 'Interstitial')
                                    <span class="badge-format badge-interstitial">Interstitial</span>
                                @else
                                    <span class="badge-format badge-banner">{{ $ad['format'] }}</span>
                                @endif
                            </td>

                            <!-- 4. Placement -->
                            <td>
                                <span>{{ $ad['placement'] }}</span>
                            </td>

                            <!-- 5. Audience -->
                            <td>
                                <span>{{ $ad['audience'] }}</span>
                            </td>

                            <!-- 6. Impressions -->
                            <td>
                                <span class="metric-value">{{ $ad['impressions'] }}</span>
                            </td>

                            <!-- 7. CTR -->
                            <td>
                                <span class="metric-value">{{ $ad['ctr'] }}</span>
                            </td>

                            <!-- 8. Revenue / Result -->
                            <td>
                                <span class="revenue-value">{{ $ad['revenue'] }}</span>
                            </td>

                            <!-- 9. Schedule -->
                            <td>
                                <span>{{ $ad['schedule'] }}</span>
                            </td>

                            <!-- 10. Status -->
                            <td>
                                @if($ad['status'] === 'Active')
                                    <span class="ad-status-badge status-active">
                                        <span class="status-dot"></span>
                                        <span>Active</span>
                                    </span>
                                @elseif($ad['status'] === 'Paused')
                                    <span class="ad-status-badge status-paused">
                                        <span class="status-dot"></span>
                                        <span>Paused</span>
                                    </span>
                                @elseif($ad['status'] === 'Scheduled')
                                    <span class="ad-status-badge status-scheduled">
                                        <span class="status-dot"></span>
                                        <span>Scheduled</span>
                                    </span>
                                @elseif($ad['status'] === 'Draft')
                                    <span class="ad-status-badge status-draft">
                                        <span class="status-dot"></span>
                                        <span>Draft</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 11. Actions (3-Dots Dropdown Trigger) -->
                            <td class="td-action">
                                <div class="action-dropdown-wrap">
                                    <button type="button" class="btn-action-dots" title="Ad Actions" onclick="toggleAdMenu(this, event)">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div class="ad-action-dropdown-menu">
                                        <div class="menu-category-header">AD ACTIONS</div>

                                        <button type="button" class="action-menu-item item-highlight" onclick="showAdDetails('{{ $ad['name'] }}', '{{ $ad['unit_id'] }}', '{{ $ad['provider'] }}', '{{ $ad['format'] }}', '{{ $ad['placement'] }}', '{{ $ad['audience'] }}', '{{ $ad['impressions'] }}', '{{ $ad['ctr'] }}', '{{ $ad['revenue'] }}', '{{ $ad['schedule'] }}', '{{ $ad['status'] }}')">
                                            <i class="fa-regular fa-file-lines"></i>
                                            <span>View Ad Details</span>
                                        </button>

                                        {{-- Ad unit management (create, pause, archive, etc.) must be done
                                             directly in the AdMob console — the AdMob API is read-only for
                                             unit configuration. Only informational/navigational actions are available here. --}}
                                        <a href="https://admob.google.com/" target="_blank" class="action-menu-item">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Edit in AdMob Console</span>
                                        </a>

                                        <button type="button" class="action-menu-item" onclick="window.location.href='{{ route('admin.devices.index') }}'">
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                            <span>View Installed Devices</span>
                                        </button>

                                        <div class="menu-divider"></div>

                                        <button type="button" class="action-menu-item" onclick="copyAdUnitId('{{ $ad['unit_id'] }}')">
                                            <i class="fa-regular fa-clipboard"></i>
                                            <span>Copy Ad Unit ID</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-rectangle-ad fs-2 mb-2 d-block text-secondary"></i>
                                <span>No ads or campaigns found matching your filter criteria.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 6. Table Footer Bar -->
        <div class="table-footer-bar">
            <div class="footer-left-info">
                <span>Showing {{ $paginatedAds->firstItem() ?? 0 }} to {{ $paginatedAds->lastItem() ?? 0 }} of {{ $totalAllAds }} ads</span>
            </div>

            <div class="footer-right-controls">
                <div class="rows-per-page-group">
                    <span>Rows per page</span>
                    <select class="form-select rows-select" onchange="changeAdPerPage(this.value)">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>

                <!-- Real Laravel Pagination -->
                <div class="custom-pagination">
                    {{ $paginatedAds->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Bottom Policy & Privacy Notice Cards -->
    <div class="ad-bottom-notices">
        <!-- Amber Alert -->
        <div class="notice-card notice-warning">
            <i class="fa-solid fa-triangle-exclamation notice-icon"></i>
            <div>Follow Google AdMob policies and avoid disruptive ad frequency on camera and capture flows.</div>
        </div>

        <!-- Blue Alert -->
        <div class="notice-card notice-info">
            <i class="fa-solid fa-shield-halved notice-icon"></i>
            <div>Personalized ad delivery must respect consent status and regional privacy requirements.</div>
        </div>
    </div>

</div>

<!-- Modal: Create Ad -->
<div class="modal fade" id="createAdModal" tabindex="-1" aria-labelledby="createAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold" id="createAdModalLabel">Create New Ad or Campaign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-12 fw-semibold text-dark">Ad Unit Name</label>
                        <input type="text" class="form-control rounded-2 fs-13" placeholder="e.g. Camera Home Banner">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-12 fw-semibold text-dark">Ad Provider</label>
                        <select class="form-select rounded-2 fs-13">
                            <option value="admob">Google AdMob</option>
                            <option value="custom">Custom Internal Campaign</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-12 fw-semibold text-dark">Format</label>
                        <select class="form-select rounded-2 fs-13">
                            <option value="banner">Banner (320x50 / Adaptive)</option>
                            <option value="native">Native Advanced</option>
                            <option value="interstitial">Interstitial Full Screen</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-12 fw-semibold text-dark">App Placement</label>
                        <select class="form-select rounded-2 fs-13">
                            <option value="Camera home">Camera home · Bottom</option>
                            <option value="Photo preview">Photo preview</option>
                            <option value="After every 3 saves">After every 3 saves</option>
                            <option value="Settings">Settings · Bottom</option>
                            <option value="Gallery screen">Gallery screen</option>
                            <option value="App launch">App launch</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fs-12 fw-semibold text-dark">AdMob Unit ID / Campaign Code</label>
                        <input type="text" class="form-control rounded-2 fs-13" placeholder="ca-app-pub-3940256099942544/6300978111">
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light rounded-bottom border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="https://admob.google.com/" target="_blank" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Create in AdMob Console
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Ad Delivery Settings modal removed: frequency capping, GDPR/UMP consent, and
     debug test-ad mode are configured directly in the Flutter/mobile app code and
     in the Google AdMob console — they cannot be controlled from this admin panel.
     These settings will be restored once the mobile app integration is ready. --}}

<!-- Modal: Placement Manager -->
<div class="modal fade" id="placementManagerModal" tabindex="-1" aria-labelledby="placementManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold" id="placementManagerModalLabel">Placement Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info d-flex align-items-start gap-3 mb-0">
                    <i class="fa-solid fa-circle-info fs-5 mt-1 text-primary flex-shrink-0"></i>
                    <div>
                        <div class="fw-semibold mb-1">Placement configuration is managed in the Flutter app</div>
                        <p class="mb-2 fs-13">Ad placements (Camera home, Photo preview, After every 3 saves, etc.) are defined in the mobile app code — not in this admin panel. The AdMob API does not expose placement configuration for read or write.</p>
                        <p class="mb-0 fs-13">To change which ad unit appears at a given placement, update the ad unit ID constants in your Flutter project and redeploy. Once the mobile API endpoint for AdMob config is live, this panel will be connected to it.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light rounded-bottom border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="https://admob.google.com/" target="_blank" class="btn btn-outline-primary">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open AdMob Console
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Manage Integration — bound to real DB values, not hardcoded -->
<div class="modal fade" id="manageIntegrationModal" tabindex="-1" aria-labelledby="manageIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold" id="manageIntegrationModalLabel">Google AdMob Integration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3">
                    <i class="fa-brands fa-google fs-2 {{ !empty($config['is_google_connected']) ? 'text-primary' : 'text-secondary' }}"></i>
                    <div>
                        @if(!empty($config['is_google_connected']))
                            <div class="fw-bold text-dark fs-13">AdMob Account Connected</div>
                            <div class="text-muted fs-11">{{ $config['connected_email'] ?? 'Google Account' }}</div>
                            @if(!empty($config['publisher_id']))
                                <div class="text-muted fs-11 font-monospace">{{ $config['publisher_id'] }}</div>
                            @endif
                        @else
                            <div class="fw-bold text-dark fs-13">Not Connected</div>
                            <div class="text-muted fs-11">Connect your Google account in <a href="{{ route('admin.ads.configuration') }}">Ad Settings</a></div>
                        @endif
                    </div>
                </div>
                @if(!empty($config['android_app_id']))
                <div class="mb-3">
                    <label class="form-label fs-12 fw-semibold text-dark">Android App ID</label>
                    <input type="text" class="form-control rounded-2 fs-12 font-monospace" value="{{ $config['android_app_id'] }}" readonly>
                </div>
                @endif
                @if(!empty($config['ios_app_id']))
                <div>
                    <label class="form-label fs-12 fw-semibold text-dark">iOS App ID</label>
                    <input type="text" class="form-control rounded-2 fs-12 font-monospace" value="{{ $config['ios_app_id'] }}" readonly>
                </div>
                @endif
                @if(empty($config['android_app_id']) && empty($config['ios_app_id']))
                <div class="text-muted fs-12">No app IDs configured yet. Add them in <a href="{{ route('admin.ads.configuration') }}">Ad Settings</a>.</div>
                @endif
            </div>
            <div class="modal-footer px-4 py-3 bg-light rounded-bottom border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                {{-- "Sync" means reload this page to pick up latest data from AdMob API --}}
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                    <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh from AdMob
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: View Ad Details -->
<div class="modal fade" id="viewAdModal" tabindex="-1" aria-labelledby="viewAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold" id="viewAdModalLabel">Ad Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="viewAdModalBody">
                <!-- Dynamically filled by showAdDetails() -->
            </div>
            <div class="modal-footer px-4 py-3 bg-light rounded-bottom border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle 3-dots action menu for ad rows
    function toggleAdMenu(btn, e) {
        e.stopPropagation();
        const menu = btn.nextElementSibling;
        const isShown = menu.classList.contains('show');

        // Close any other open menus
        document.querySelectorAll('.ad-action-dropdown-menu.show').forEach(m => m.classList.remove('show'));

        if (!isShown) {
            menu.classList.add('show');
        }
    }

    // Close open action menus when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.ad-action-dropdown-menu.show').forEach(m => m.classList.remove('show'));
    });

    // Copy Ad Unit ID to clipboard
    function copyAdUnitId(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
        }
        triggerToast('Copied Ad Unit ID: ' + text);
    }

    // Set filter dropdown selection and update UI label
    function setFilterOption(field, value) {
        const input = document.getElementById('filter_' + field);
        const label = document.getElementById('label_' + field);
        if (input) input.value = value;
        if (label) label.textContent = value;
    }

    // Dynamic Toast Notification using SweetAlert2 if present, or fallback
    function triggerToast(msg) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        } else {
            alert(msg);
        }
    }

    // Display Ad Details in modal
    function showAdDetails(name, unitId, provider, format, placement, audience, impressions, ctr, revenue, schedule, status) {
        const body = document.getElementById('viewAdModalBody');
        body.innerHTML = `
            <div class="d-flex flex-column gap-2 fs-13">
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Ad / Campaign</span>
                    <span class="fw-bold text-dark">${name}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Ad Unit ID</span>
                    <span class="font-monospace text-primary">${unitId}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Provider</span>
                    <span>${provider}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Format</span>
                    <span class="fw-semibold">${format}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Placement</span>
                    <span>${placement}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Audience</span>
                    <span>${audience}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Impressions</span>
                    <span class="fw-bold">${impressions}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">CTR</span>
                    <span class="fw-bold">${ctr}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Revenue / Result</span>
                    <span class="fw-bold text-success">${revenue}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2">
                    <span class="text-muted">Schedule</span>
                    <span>${schedule}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Status</span>
                    <span class="fw-bold">${status}</span>
                </div>
            </div>
        `;
        const modal = new bootstrap.Modal(document.getElementById('viewAdModal'));
        modal.show();
    }

    function changeAdPerPage(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        window.location.href = url.toString();
    }
</script>
@endsection
