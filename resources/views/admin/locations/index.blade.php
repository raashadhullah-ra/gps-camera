@extends('layout')

@section('title', 'Locations - GPS Camera Admin')
@section('page_title', 'Locations')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">Locations</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- 1. Top Header Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Locations</h1>
            <p class="text-secondary fs-13 mb-0">Monitor aggregated installations, users and GPS camera activity by location</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" onclick="openExportAllModal('{{ number_format($metrics['countries']) }} Countries', '{{ number_format($metrics['cities']) }} Cities', '{{ number_format($metrics['total_db_users']) }}', '{{ number_format($metrics['total_db_devices']) }}', 'Active')">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </button>
            <a href="{{ route('admin.locations.send-notification-global') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Send Notification</span>
            </a>
        </div>
    </div>

    <!-- 2. Global Metric Stat Cards (5 Cards Row) -->
    <div class="locations-stats-grid">
        <!-- 1. Countries -->
        <div class="stat-card-location">
            <div class="stat-icon-circle blue">
                <i class="fa-solid fa-globe"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Countries</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($metrics['countries']) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. States / Regions -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-solid fa-map"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">States / Regions</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($metrics['states_regions']) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Cities -->
        <div class="stat-card-location">
            <div class="stat-icon-circle green">
                <i class="fa-solid fa-city"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Cities</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($metrics['cities']) }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Location-Enabled Devices -->
        <div class="stat-card-location">
            <div class="stat-icon-circle cyan">
                <i class="fa-solid fa-location-crosshairs"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Location-Enabled Devices</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($metrics['location_enabled_devices']) }}</span>
                    <span class="stat-growth">↑ {{ $metrics['location_enabled_growth'] }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Permission Denied -->
        <div class="stat-card-location">
            <div class="stat-icon-circle orange">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Permission Denied</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($metrics['permission_denied']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar Card (Single Inline Row: Filters + Reset & Apply Buttons) -->
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form method="GET" action="{{ route('admin.locations.index') }}" id="locationsFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap">
            <input type="hidden" name="tab" value="{{ request('tab', 'all') }}">

            <!-- 1. Compact Search input -->
            <div class="position-relative filter-search-wrap flex-grow-1">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search city, state or country" value="{{ request('search') }}">
            </div>

            <!-- 2. Country Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="country" id="filter_country" value="{{ request('country', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-location" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownCountryBtn">
                    <span class="filter-box-label">Country</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_country">{{ request('country', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownCountryBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('country') == 'All' || !request('country') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('country', 'All')">All</a></li>
                    @foreach($filterOptions['countries'] as $c)
                        <li><a class="dropdown-item py-1 px-3 {{ request('country') == $c ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('country', '{{ $c }}')">{{ $c }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 3. State Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="state" id="filter_state" value="{{ request('state', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-location" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownStateBtn">
                    <span class="filter-box-label">State / Region</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_state">{{ request('state', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-md" aria-labelledby="dropdownStateBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('state') == 'All' || !request('state') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('state', 'All')">All</a></li>
                    @foreach($filterOptions['states'] as $st)
                        <li><a class="dropdown-item py-1 px-3 {{ request('state') == $st ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('state', '{{ $st }}')">{{ $st }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 4. Location Source Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="location_source" id="filter_source" value="{{ request('location_source', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-version" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownSourceBtn">
                    <span class="filter-box-label">Source</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_source">{{ request('location_source', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownSourceBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('location_source') == 'All' || !request('location_source') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location_source', 'All')">All</a></li>
                    @foreach($filterOptions['sources'] as $src)
                        <li><a class="dropdown-item py-1 px-3 {{ request('location_source') == $src ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('location_source', '{{ $src }}')">{{ $src }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 5. Permission Dropdown -->
            <div class="dropdown">
                <input type="hidden" name="permission" id="filter_perm" value="{{ request('permission', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-permission" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownPermBtn">
                    <span class="filter-box-label">Permission</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_perm">{{ request('permission', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownPermBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('permission') == 'All' || !request('permission') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('permission', 'All')">All</a></li>
                    @foreach($filterOptions['permissions'] as $p)
                        <li><a class="dropdown-item py-1 px-3 {{ request('permission') == $p ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('permission', '{{ $p }}')">{{ $p }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 6. Activity Status Dropdown -->
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
                    @foreach($filterOptions['statuses'] as $st)
                        <li><a class="dropdown-item py-1 px-3 {{ request('status') == $st ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', '{{ $st }}')">{{ $st }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- 7. Last Activity Date Box -->
            <div class="filter-date-box" onclick="document.getElementById('locationsDateInput').focus()">
                <span class="filter-box-label">Last Activity</span>
                <div class="d-flex align-items-center gap-1">
                    <i class="fa-regular fa-calendar text-muted fs-11"></i>
                    <input type="text" name="date_range" id="locationsDateInput" class="filter-date-input" placeholder="Select date range" value="{{ request('date_range') }}" readonly>
                </div>
            </div>

            <!-- 8. Action Buttons -->
            <div class="d-flex align-items-center gap-2 flex-nowrap ms-auto">
                <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary btn-sm px-3" style="height: 38px;">Reset</a>
                <button type="submit" class="btn btn-primary btn-sm px-3" style="height: 38px;">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Total Locations Bar -->
    <div class="devices-found-bar mb-3">
        <span>{{ number_format($metrics['cities']) }} locations found</span>
    </div>

    <!-- 4. Location Overview Table Card -->
    <div class="devices-table-card">
        <div class="table-card-top-bar">
            <h2 class="table-title">Location Overview</h2>
            <div class="table-header-tools">
                <a href="{{ route('admin.locations.map', $locations->first()?->id ?? 1) }}" class="btn-tool text-decoration-none">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Map View</span>
                </a>
                <button type="button" class="btn-tool" onclick="showInfoToast('Column customization')">
                    <i class="fa-solid fa-table-columns"></i>
                    <span>Columns</span>
                </button>
                <button type="button" class="btn-tool" onclick="showInfoToast('Advanced Filter Options')">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filters</span>
                </button>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="devices-tabs-nav">
            @php $activeTab = request('tab', 'all'); @endphp
            <a href="{{ route('admin.locations.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}" class="tab-item {{ $activeTab == 'all' ? 'active' : '' }}">All Locations</a>
            <a href="{{ route('admin.locations.index', array_merge(request()->except('tab'), ['tab' => 'countries'])) }}" class="tab-item {{ $activeTab == 'countries' ? 'active' : '' }}">Countries</a>
            <a href="{{ route('admin.locations.index', array_merge(request()->except('tab'), ['tab' => 'states'])) }}" class="tab-item {{ $activeTab == 'states' ? 'active' : '' }}">States / Regions</a>
            <a href="{{ route('admin.locations.index', array_merge(request()->except('tab'), ['tab' => 'cities'])) }}" class="tab-item {{ $activeTab == 'cities' ? 'active' : '' }}">Cities</a>
            <a href="{{ route('admin.locations.index', array_merge(request()->except('tab'), ['tab' => 'high_activity'])) }}" class="tab-item {{ $activeTab == 'high_activity' ? 'active' : '' }}">High Activity</a>
        </div>

        <!-- Locations Data Table -->
        <div class="table-responsive">
            <table class="devices-data-table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>State / Region</th>
                        <th>Country</th>
                        <th>Anonymous Users</th>
                        <th>Devices</th>
                        <th>Photos Captured</th>
                        <th>New Installs</th>
                        <th>Last Activity</th>
                        <th>Location Source</th>
                        <th>Status</th>
                        <th class="th-action text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $loc)
                        <tr>
                            <!-- 1. Location (Link with Map Pin) -->
                            <td>
                                <a href="{{ route('admin.locations.show', $loc->id) }}" class="location-name-link">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $loc->city }}</span>
                                </a>
                            </td>

                            <!-- 2. State / Region -->
                            <td>
                                <span class="text-dark fw-normal">{{ $loc->state }}</span>
                            </td>

                            <!-- 3. Country (clean text, no code abbreviation) -->
                            <td>
                                <span class="text-dark">{{ $loc->country }}</span>
                            </td>

                            <!-- 4. Anonymous Users -->
                            <td>
                                <span class="fw-semibold text-dark">{{ number_format($loc->anonymous_users_count) }}</span>
                            </td>

                            <!-- 5. Devices -->
                            <td>
                                <span class="text-dark">{{ number_format($loc->devices_count) }}</span>
                            </td>

                            <!-- 6. Photos Captured -->
                            <td>
                                <span class="text-dark">{{ number_format($loc->photos_captured_count) }}</span>
                            </td>

                            <!-- 7. New Installs -->
                            <td>
                                <span class="text-dark">{{ number_format($loc->new_installs_count) }}</span>
                            </td>

                            <!-- 8. Last Activity -->
                            <td>
                                <span class="text-secondary fs-12">{{ $loc->last_activity_human }}</span>
                            </td>

                            <!-- 9. Location Source -->
                            <td>
                                <span class="text-dark fs-12">{{ $loc->location_source }}</span>
                            </td>

                            <!-- 10. Status Pill -->
                            <td>
                                <span class="status-badge-custom {{ $loc->status_badge_class }}">{{ $loc->status }}</span>
                            </td>

                            <!-- 11. Actions Dropdown -->
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-action-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-2 py-1 fs-12" style="min-width: 210px;">
                                        <li class="dropdown-header text-uppercase fs-10 fw-bold text-muted px-3 py-1">Location Actions</li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2" href="{{ route('admin.locations.show', $loc->id) }}">
                                                <i class="fa-regular fa-eye text-primary"></i> View Location Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2" href="{{ route('admin.locations.map', $loc->id) }}">
                                                <i class="fa-solid fa-map-location-dot text-primary"></i> View on Map
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2" href="{{ route('admin.devices.index', ['location' => $loc->city]) }}">
                                                <i class="fa-solid fa-mobile-screen-button text-muted"></i> View Installed Devices
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2" href="{{ route('admin.locations.heatmap', $loc->id) }}">
                                                <i class="fa-solid fa-fire text-danger"></i> View Activity Heatmap
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2" href="{{ route('admin.locations.show', $loc->id) }}#trend">
                                                <i class="fa-solid fa-chart-line text-success"></i> View Installation Trend
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2 text-primary" href="{{ route('admin.locations.send-notification', $loc->id) }}">
                                                <i class="fa-regular fa-bell"></i> Send Notification
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2 text-purple" href="{{ route('admin.locations.create-segment', $loc->id) }}">
                                                <i class="fa-solid fa-user-plus"></i> Create Audience Segment
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2 text-muted" href="javascript:void(0)" onclick="copyLocationName('{{ $loc->city }}, {{ $loc->state }}, {{ $loc->country }}')">
                                                <i class="fa-regular fa-copy"></i> Copy Location Name
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1 px-3 d-flex align-items-center gap-2 text-muted" href="javascript:void(0)" onclick="openExportModal({{ $loc->id }}, '{{ addslashes($loc->city) }}', '{{ addslashes($loc->state) }}', '{{ addslashes($loc->country) }}', '{{ number_format($loc->anonymous_users_count) }}', '{{ number_format($loc->devices_count) }}', '{{ $loc->status }}')">
                                                <i class="fa-solid fa-download"></i> Export Location Data
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-location-dot fs-1 text-muted opacity-50 mb-2"></i>
                                <p class="mb-0">No locations matched your filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>        <!-- 5. Table Footer Bar -->
        <div class="table-footer-bar">
            <div class="footer-left-info">
                @if($locations->total() > 0)
                    <span>Showing {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ number_format($locations->total()) }} locations</span>
                @else
                    <span>Showing 0 locations</span>
                @endif
            </div>

            <div class="footer-right-controls">
                <div class="rows-per-page-group">
                    <span>Rows per page</span>
                    <select class="form-select rows-select" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Custom Dynamic Pagination -->
                <div class="custom-pagination">
                    {{-- Previous Page Link --}}
                    @if ($locations->onFirstPage())
                        <button type="button" class="page-btn prev-btn" disabled aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $locations->previousPageUrl() }}" class="page-btn prev-btn" aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $curPage = $locations->currentPage();
                        $lastPage = $locations->lastPage();
                    @endphp

                    @for ($p = 1; $p <= $lastPage; $p++)
                        @if ($p == $curPage)
                            <button type="button" class="page-btn page-num active">{{ $p }}</button>
                        @else
                            <a href="{{ $locations->url($p) }}" class="page-btn page-num">{{ $p }}</a>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($locations->hasMorePages())
                        <a href="{{ $locations->nextPageUrl() }}" class="page-btn next-btn" aria-label="Next Page">
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

    <!-- Disclaimer Note (Below Table Card) -->
    <div class="table-footer-disclaimer">
        <i class="fa-solid fa-circle-info"></i>
        <span>Location statistics are aggregated. Individual precise coordinates are available only with appropriate permission.</span>
    </div>

    <!-- Export Location Data Modal (Screen 2) -->
    <div class="modal fade" id="exportLocationModal" tabindex="-1" aria-labelledby="exportLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form method="POST" id="exportModalForm" action="">
                    @csrf
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-1" id="exportModalTitle">Export Location Data</h5>
                            <p class="text-muted fs-12 mb-0" id="exportModalSubtitle">Choose the information and format to export</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 py-3">
                        <!-- Location Meta Bar -->
                        <div class="export-location-meta-bar">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon-circle blue" style="width: 34px; height: 34px; font-size: 14px;">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <span class="meta-loc-title d-block" id="modalMetaCity">Tirunelveli</span>
                                    <span class="meta-loc-sub" id="modalMetaRegion">Tamil Nadu, India</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-4 flex-wrap">
                                <div>
                                    <span class="fw-bold fs-13 text-dark d-block" id="modalMetaUsers">8,426</span>
                                    <span class="fs-11 text-muted">Anonymous Users</span>
                                </div>
                                <div>
                                    <span class="fw-bold fs-13 text-dark d-block" id="modalMetaDevices">9,184</span>
                                    <span class="fs-11 text-muted">Devices</span>
                                </div>
                                <div>
                                    <span class="badge bg-success-subtle text-success fs-11" id="modalMetaStatus">High Activity</span>
                                    <span class="fs-11 text-muted d-block mt-1">Activity Status</span>
                                </div>
                            </div>
                        </div>

                        <!-- Export Format Selection -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-dark mb-2">Export Format</label>
                            <input type="hidden" name="export_format" id="selectedExportFormat" value="csv">
                            <div class="export-format-grid">
                                <!-- Format 1: CSV -->
                                <div class="format-card selected" onclick="selectExportFormat('csv', this)">
                                    <div class="format-check-badge"><i class="fa-solid fa-check"></i></div>
                                    <i class="fa-solid fa-file-csv format-icon text-primary"></i>
                                    <div class="format-info">
                                        <div class="format-title">CSV</div>
                                        <div class="format-desc">Comma-separated values</div>
                                    </div>
                                </div>

                                <!-- Format 2: JSON -->
                                <div class="format-card" onclick="selectExportFormat('json', this)">
                                    <div class="format-check-badge"><i class="fa-solid fa-check"></i></div>
                                    <i class="fa-solid fa-file-code format-icon text-purple"></i>
                                    <div class="format-info">
                                        <div class="format-title">JSON</div>
                                        <div class="format-desc">JavaScript Object Notation</div>
                                    </div>
                                </div>

                                <!-- Format 3: PDF Report -->
                                <div class="format-card" onclick="selectExportFormat('pdf', this)">
                                    <div class="format-check-badge"><i class="fa-solid fa-check"></i></div>
                                    <i class="fa-solid fa-file-pdf format-icon text-danger"></i>
                                    <div class="format-info">
                                        <div class="format-title">PDF Report</div>
                                        <div class="format-desc">Summary report with charts</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data to Include -->
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-bold text-dark mb-2">Data to Include</label>
                            <div class="export-includes-grid">
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="location_overview" id="incOverview" checked>
                                    <label class="include-label" for="incOverview">Location Overview</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="installation_trend" id="incTrend" checked>
                                    <label class="include-label" for="incTrend">Installation Trend</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="user_stats" id="incUsers" checked>
                                    <label class="include-label" for="incUsers">Aggregated User Statistics</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="platform_versions" id="incPlatform" checked>
                                    <label class="include-label" for="incPlatform">Platform & App Versions</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="device_stats" id="incDevices" checked>
                                    <label class="include-label" for="incDevices">Aggregated Device Statistics</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="precise_coords" id="incCoords">
                                    <div>
                                        <label class="include-label" for="incCoords">Individual Precise Coordinates</label>
                                        <span class="include-sub">Contains privacy-sensitive data</span>
                                    </div>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="photo_activity" id="incPhotos" checked>
                                    <label class="include-label" for="incPhotos">Photo Activity</label>
                                </div>
                                <div class="include-item">
                                    <input class="form-check-input" type="checkbox" name="data_include[]" value="installation_ids" id="incIds">
                                    <div>
                                        <label class="include-label" for="incIds">Installation Identifiers</label>
                                        <span class="include-sub">Contains device-level identifiers</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range & Switches -->
                        <div class="mb-3">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Date Range</label>
                            <select class="form-select fs-13 rounded-2 w-auto" name="date_range">
                                <option value="30" selected>Last 30 days</option>
                                <option value="7">Last 7 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="all">All Time</option>
                            </select>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="swAggSmall" name="aggregate_small" checked>
                                <div>
                                    <label class="form-check-label fs-13 text-dark fw-medium" for="swAggSmall">
                                        Aggregate small result groups <i class="fa-solid fa-circle-info text-muted fs-12"></i>
                                    </label>
                                    <span class="fs-11 text-muted d-block">Low-count groups will be combined to reduce re-identification risk.</span>
                                </div>
                            </div>

                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="swIncCharts" name="include_charts" checked>
                                <label class="form-check-label fs-13 text-dark fw-medium" for="swIncCharts">
                                    Include charts in report <i class="fa-solid fa-circle-info text-muted fs-12"></i>
                                </label>
                            </div>
                        </div>

                        <!-- Audit Info Banner -->
                        <div class="segment-info-banner mt-0">
                            <i class="fa-solid fa-shield-halved fs-14 flex-shrink-0"></i>
                            <span>Exports contain aggregated location data and are recorded in the admin audit log.</span>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-outline-secondary px-3 py-2 fs-13 rounded-2 fw-medium" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-3 py-2 fs-13 rounded-2 fw-medium d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-download"></i> Export Location Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function setFilterOption(field, value) {
    document.getElementById('filter_' + field).value = value;
    document.getElementById('locationsFilterForm').submit();
}

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

function copyLocationName(text) {
    navigator.clipboard.writeText(text);
    window.showToast('success', 'Location name copied', text + ' copied to clipboard');
}

function selectExportFormat(format, cardEl) {
    document.getElementById('selectedExportFormat').value = format;
    document.querySelectorAll('.format-card').forEach(c => c.classList.remove('selected'));
    cardEl.classList.add('selected');
}

function openExportAllModal(countries, cities, users, devices, status) {
    document.getElementById('modalMetaCity').textContent = 'All Locations';
    document.getElementById('modalMetaRegion').textContent = countries + ' • ' + cities;
    document.getElementById('modalMetaUsers').textContent = users;
    document.getElementById('modalMetaDevices').textContent = devices;
    document.getElementById('modalMetaStatus').textContent = status;
    document.getElementById('exportModalTitle').textContent = 'Export Global Location Data';
    document.getElementById('exportModalSubtitle').textContent = 'Choose the information and format to export for all locations';

    const form = document.getElementById('exportModalForm');
    form.action = "{{ route('admin.locations.export-all') }}";

    const modalEl = document.getElementById('exportLocationModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function openExportModal(id, city, state, country, users, devices, status) {
    document.getElementById('modalMetaCity').textContent = city;
    document.getElementById('modalMetaRegion').textContent = state + (country ? ', ' + country : '');
    document.getElementById('modalMetaUsers').textContent = users;
    document.getElementById('modalMetaDevices').textContent = devices;
    document.getElementById('modalMetaStatus').textContent = status;
    document.getElementById('exportModalTitle').textContent = 'Export Location Data';
    document.getElementById('exportModalSubtitle').textContent = 'Choose the information and format to export for ' + city;

    const form = document.getElementById('exportModalForm');
    form.action = `/admin/locations/${id}/export-modal`;

    const modalEl = document.getElementById('exportLocationModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}
</script>
@endpush
