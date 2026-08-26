@extends('layout')

@section('title', $location->city . ' Location Overview - GPS Camera Admin')
@section('page_title', $location->city . ' Details')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.index') }}">Locations</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">{{ $location->city }}</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- 1. Top Header with Back Navigation -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.locations.index') }}" class="btn btn-sm btn-outline-secondary btn-icon rounded-circle" title="Back to Locations">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h3 fw-bold text-dark mb-0">{{ $location->city }}</h1>
                    <span class="status-badge-custom {{ $location->status_badge_class }}">{{ $location->status }}</span>
                </div>
                <p class="text-secondary fs-13 mb-0">Location overview • {{ $location->state }}, {{ $location->country }}</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.locations.send-notification', $location->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Send Notification</span>
            </a>
            <a href="{{ route('admin.locations.create-segment', $location->id) }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Create Segment</span>
            </a>
            <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-2" onclick="openExportModal({{ $location->id }}, '{{ addslashes($location->city) }}', '{{ addslashes($location->state) }}', '{{ addslashes($location->country) }}', '{{ number_format($location->anonymous_users_count) }}', '{{ number_format($location->devices_count) }}', '{{ $location->status }}')">
                <i class="fa-solid fa-download"></i>
                <span>Export Data</span>
            </button>
        </div>
    </div>

    <!-- 2. Top 5 Metric Stat Cards -->
    <div class="locations-stats-grid">
        <!-- 1. Anonymous Users -->
        <div class="stat-card-location">
            <div class="stat-icon-circle purple">
                <i class="fa-regular fa-user"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Anonymous Users</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->anonymous_users_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Installed Devices -->
        <div class="stat-card-location">
            <div class="stat-icon-circle blue">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Installed Devices</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->devices_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Photos Captured -->
        <div class="stat-card-location">
            <div class="stat-icon-circle green">
                <i class="fa-regular fa-image"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Photos Captured</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->photos_captured_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 4. New Installs -->
        <div class="stat-card-location">
            <div class="stat-icon-circle orange">
                <i class="fa-solid fa-arrow-down-to-bracket"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">New Installs</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->new_installs_count) }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Notification Enabled -->
        <div class="stat-card-location">
            <div class="stat-icon-circle orange">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <div class="stat-meta-block">
                <span class="stat-title">Notification Enabled</span>
                <div class="stat-value-group">
                    <span class="stat-number">{{ number_format($location->notification_enabled_percentage, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Navigation Tabs Bar -->
    <div class="devices-tabs-nav mb-4 bg-white border rounded-3 p-1">
        <a href="{{ route('admin.locations.show', $location->id) }}" class="tab-item active">Overview</a>
        <a href="{{ route('admin.devices.index', ['location' => $location->city]) }}" class="tab-item">Users</a>
        <a href="{{ route('admin.devices.index', ['location' => $location->city]) }}" class="tab-item">Devices</a>
        <a href="{{ route('admin.locations.heatmap', $location->id) }}" class="tab-item">Activity</a>
        <a href="{{ route('admin.locations.map', $location->id) }}" class="tab-item">Installation Trend</a>
    </div>

    <!-- 4. Main Analytics 3-Column Grid -->
    <div class="row g-3">
        
        <!-- Left Column: Interactive City Map Card -->
        <div class="col-lg-5">
            <div class="card border rounded-3 h-100 shadow-sm overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark mb-0">Location Map ({{ $location->city }})</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="btnTileOsm">Map</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnTileSat">Satellite</button>
                    </div>
                </div>
                <div class="card-body p-0 position-relative" style="min-height: 420px;">
                    <!-- Leaflet Container -->
                    <div id="cityLeafletMap" style="height: 420px; width: 100%;"></div>

                    <!-- View Full Map Button Overlay -->
                    <div class="position-absolute bottom-0 end-0 m-3" style="z-index: 1000;">
                        <a href="{{ route('admin.locations.map', $location->id) }}" class="btn btn-sm btn-white shadow-sm border fw-semibold d-inline-flex align-items-center gap-1">
                            <span>View Full Map</span>
                            <i class="fa-solid fa-arrow-up-right-from-square fs-10"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column: Location Information & Permission Breakdown -->
        <div class="col-lg-4">
            <!-- 1. Location Information -->
            <div class="location-analytics-card">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Location Information</span>
                </h6>
                <div class="d-flex flex-column gap-2 fs-12">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-solid fa-city me-1 text-muted"></i> City</span>
                        <span class="fw-semibold text-dark">{{ $location->city }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-solid fa-map me-1 text-muted"></i> State</span>
                        <span class="fw-semibold text-dark">{{ $location->state }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-solid fa-globe me-1 text-muted"></i> Country</span>
                        <span class="fw-semibold text-dark">{{ $location->country }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-solid fa-flag me-1 text-muted"></i> Country Code</span>
                        <span class="fw-semibold text-dark">{{ $location->country_code }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-regular fa-clock me-1 text-muted"></i> Timezone</span>
                        <span class="fw-semibold text-dark">{{ $location->timezone }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-secondary"><i class="fa-regular fa-user me-1 text-muted"></i> Location Level</span>
                        <span class="fw-semibold text-dark">{{ $location->location_level }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-secondary"><i class="fa-solid fa-clock-rotate-left me-1 text-muted"></i> Last Activity</span>
                        <span class="fw-semibold text-success"><span class="status-dot"></span> {{ $location->last_activity_human }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Permission Breakdown -->
            <div class="location-analytics-card">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Permission Breakdown</span>
                </h6>
                
                @php $perms = $location->permission_breakdown ?? ['precise' => 62, 'approximate' => 21, 'denied' => 17]; @endphp
                
                <div class="progress-stat-row">
                    <div class="stat-label-row">
                        <span>Precise Granted</span>
                        <span class="stat-pct">{{ $perms['precise'] ?? 62 }}%</span>
                    </div>
                    <div class="custom-progress-track">
                        <div class="progress-fill blue" style="width: {{ $perms['precise'] ?? 62 }}%;"></div>
                    </div>
                </div>

                <div class="progress-stat-row">
                    <div class="stat-label-row">
                        <span>Approximate Granted</span>
                        <span class="stat-pct">{{ $perms['approximate'] ?? 21 }}%</span>
                    </div>
                    <div class="custom-progress-track">
                        <div class="progress-fill green" style="width: {{ $perms['approximate'] ?? 21 }}%;"></div>
                    </div>
                </div>

                <div class="progress-stat-row">
                    <div class="stat-label-row">
                        <span>Denied</span>
                        <span class="stat-pct">{{ $perms['denied'] ?? 17 }}%</span>
                    </div>
                    <div class="custom-progress-track">
                        <div class="progress-fill orange" style="width: {{ $perms['denied'] ?? 17 }}%;"></div>
                    </div>
                </div>
            </div>

            <!-- 3. Top App Versions -->
            <div class="location-analytics-card">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-code"></i>
                    <span>Top App Versions</span>
                </h6>
                @php $appVers = $location->top_app_versions ?? ['v1.4.2' => 48, 'v1.4.1' => 31, 'v1.4.0' => 14, 'older' => 7]; @endphp
                
                <div class="row g-2">
                    <div class="col-6">
                        <div class="progress-stat-row">
                            <div class="stat-label-row">
                                <span>v1.4.2</span>
                                <span class="stat-pct">{{ $appVers['v1.4.2'] ?? 48 }}%</span>
                            </div>
                            <div class="custom-progress-track">
                                <div class="progress-fill blue" style="width: {{ $appVers['v1.4.2'] ?? 48 }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="progress-stat-row">
                            <div class="stat-label-row">
                                <span>v1.4.0</span>
                                <span class="stat-pct">{{ $appVers['v1.4.0'] ?? 14 }}%</span>
                            </div>
                            <div class="custom-progress-track">
                                <div class="progress-fill blue" style="width: {{ $appVers['v1.4.0'] ?? 14 }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="progress-stat-row">
                            <div class="stat-label-row">
                                <span>v1.4.1</span>
                                <span class="stat-pct">{{ $appVers['v1.4.1'] ?? 31 }}%</span>
                            </div>
                            <div class="custom-progress-track">
                                <div class="progress-fill blue" style="width: {{ $appVers['v1.4.1'] ?? 31 }}%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="progress-stat-row">
                            <div class="stat-label-row">
                                <span>Older</span>
                                <span class="stat-pct">{{ $appVers['older'] ?? 7 }}%</span>
                            </div>
                            <div class="custom-progress-track">
                                <div class="progress-fill blue" style="width: {{ $appVers['older'] ?? 7 }}%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Location Sources Donut & Platform Distribution -->
        <div class="col-lg-3">
            <!-- 1. Location Sources (Donut Chart) -->
            <div class="location-analytics-card text-center">
                <h6 class="card-top-title text-start">
                    <i class="fa-solid fa-location-crosshairs"></i>
                    <span>Location Sources</span>
                </h6>

                <!-- Canvas Chart with Center Icon -->
                <div class="position-relative d-inline-block mx-auto my-2" style="width: 140px; height: 140px;">
                    <canvas id="locationSourcesDonut"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle text-primary fs-4">
                        <i class="fa-solid fa-tower-broadcast"></i>
                    </div>
                </div>

                @php $sources = $location->location_sources ?? ['gps' => 68, 'network' => 24, 'approximate' => 8]; @endphp
                <div class="d-flex flex-column gap-2 text-start fs-12 mt-2 px-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #2563eb;"></span>
                            <span class="text-dark">GPS</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $sources['gps'] ?? 68 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                            <span class="text-dark">Network</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $sources['network'] ?? 24 }}%</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f97316;"></span>
                            <span class="text-dark">Approximate</span>
                        </div>
                        <span class="fw-semibold text-dark">{{ $sources['approximate'] ?? 8 }}%</span>
                    </div>
                </div>
            </div>

            <!-- 2. Platform Distribution -->
            <div class="location-analytics-card">
                <h6 class="card-top-title">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>Platform Distribution</span>
                </h6>
                
                @php $plat = $location->platform_distribution ?? ['android' => 91, 'ios' => 9]; @endphp

                <div class="progress-stat-row">
                    <div class="stat-label-row">
                        <span><i class="fa-brands fa-android text-success me-1"></i> Android</span>
                        <span class="stat-pct">{{ $plat['android'] ?? 91 }}%</span>
                    </div>
                    <div class="custom-progress-track">
                        <div class="progress-fill green" style="width: {{ $plat['android'] ?? 91 }}%;"></div>
                    </div>
                </div>

                <div class="progress-stat-row">
                    <div class="stat-label-row">
                        <span><i class="fa-brands fa-apple text-dark me-1"></i> iOS</span>
                        <span class="stat-pct">{{ $plat['ios'] ?? 9 }}%</span>
                    </div>
                    <div class="custom-progress-track">
                        <div class="progress-fill gray" style="width: {{ $plat['ios'] ?? 9 }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Disclaimer Note -->
    <div class="table-footer-disclaimer mt-3">
        <i class="fa-solid fa-circle-info"></i>
        <span>Statistics are aggregated at city level. Individual precise coordinates are not shown.</span>
    </div>

    <!-- Export Location Data Modal (Screen 2) -->
    <div class="modal fade" id="exportLocationModal" tabindex="-1" aria-labelledby="exportLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form method="POST" id="exportModalForm" action="{{ route('admin.locations.export-modal', $location->id) }}">
                    @csrf
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-1" id="exportModalTitle">Export Location Data</h5>
                            <p class="text-muted fs-12 mb-0" id="exportModalSubtitle">Choose the information and format to export for {{ $location->city }}</p>
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
                                    <span class="meta-loc-title d-block" id="modalMetaCity">{{ $location->city }}</span>
                                    <span class="meta-loc-sub" id="modalMetaRegion">{{ $location->state }}, {{ $location->country }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-4 flex-wrap">
                                <div>
                                    <span class="fw-bold fs-13 text-dark d-block" id="modalMetaUsers">{{ number_format($location->anonymous_users_count) }}</span>
                                    <span class="fs-11 text-muted">Anonymous Users</span>
                                </div>
                                <div>
                                    <span class="fw-bold fs-13 text-dark d-block" id="modalMetaDevices">{{ number_format($location->devices_count) }}</span>
                                    <span class="fs-11 text-muted">Devices</span>
                                </div>
                                <div>
                                    <span class="badge bg-success-subtle text-success fs-11" id="modalMetaStatus">{{ $location->status }}</span>
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
<!-- Leaflet & Chart.js Initialization -->
<script>
function selectExportFormat(format, cardEl) {
    document.getElementById('selectedExportFormat').value = format;
    document.querySelectorAll('.format-card').forEach(c => c.classList.remove('selected'));
    cardEl.classList.add('selected');
}

function openExportModal(id, city, state, country, users, devices, status) {
    document.getElementById('modalMetaCity').textContent = city;
    document.getElementById('modalMetaRegion').textContent = state + (country ? ', ' + country : '');
    document.getElementById('modalMetaUsers').textContent = users;
    document.getElementById('modalMetaDevices').textContent = devices;
    document.getElementById('modalMetaStatus').textContent = status;
    document.getElementById('exportModalSubtitle').textContent = 'Choose the information and format to export for ' + city;

    const form = document.getElementById('exportModalForm');
    form.action = `/admin/locations/${id}/export-modal`;

    const modalEl = document.getElementById('exportLocationModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize City Leaflet Map
    const cityLat = {{ $location->latitude }};
    const cityLng = {{ $location->longitude }};
    const cityName = "{{ $location->city }}";

    if (window.L) {
        const map = L.map('cityLeafletMap', {
            center: [cityLat, cityLng],
            zoom: 12,
            zoomControl: true,
        });

        // OSM Tiles Layer
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Satellite Layer (CartoDB / Esri imagery fallback)
        const satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        });

        // Toggle Tile Buttons
        document.getElementById('btnTileOsm')?.addEventListener('click', function() {
            map.removeLayer(satLayer);
            map.addLayer(osmLayer);
            this.classList.add('active');
            document.getElementById('btnTileSat')?.classList.remove('active');
        });

        document.getElementById('btnTileSat')?.addEventListener('click', function() {
            map.removeLayer(osmLayer);
            map.addLayer(satLayer);
            this.classList.add('active');
            document.getElementById('btnTileOsm')?.classList.remove('active');
        });

        // Custom City Pin Marker
        const pinIcon = L.divIcon({
            className: 'custom-city-pin',
            html: '<div style="background: #2563eb; color: #fff; width: 34px; height: 34px; border-radius: 50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(37,99,235,0.4); border: 2px solid #fff;"><i class="fa-solid fa-location-dot fs-6"></i></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        L.marker([cityLat, cityLng], { icon: pinIcon })
            .addTo(map)
            .bindPopup('<strong>' + cityName + '</strong><br>Center Activity Point');

        // Circular Coverage Glows
        L.circle([cityLat, cityLng], {
            color: '#3b82f6',
            fillColor: '#60a5fa',
            fillOpacity: 0.2,
            radius: 3500,
            weight: 1
        }).addTo(map);
    }

    // 2. Initialize Location Sources Donut Chart
    if (window.Chart) {
        const ctx = document.getElementById('locationSourcesDonut');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['GPS', 'Network', 'Approximate'],
                    datasets: [{
                        data: [
                            {{ $sources['gps'] ?? 68 }},
                            {{ $sources['network'] ?? 24 }},
                            {{ $sources['approximate'] ?? 8 }}
                        ],
                        backgroundColor: ['#2563eb', '#10b981', '#f97316'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.label}: ${ctx.raw}%`
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
