@extends('layout')

@section('title', 'Segment Audience - ' . $segment->name)
@section('page_title', 'Segment Audience')

@section('breadcrumbs')
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Engagement</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.index') }}" class="text-decoration-none text-muted">Audience Segments</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.segments.show', $segment->id) }}" class="text-decoration-none text-muted">{{ $segment->name }}</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Audience</span>
@endsection

@section('content')
<div class="segments-page">
    <!-- Header Actions -->
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Segment Audience</h1>
            <p class="page-main-subtitle">Anonymous devices currently matching <strong>{{ $segment->name }}</strong></p>
        </div>
        <div class="header-actions-group">
            <a href="{{ route('admin.segments.show', $segment->id) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Segment</span>
            </a>
            <a href="{{ route('admin.segments.export', array_merge(['id' => $segment->id], request()->query())) }}" class="btn btn-primary">
                <i class="fa-solid fa-download"></i>
                <span>Export Audience</span>
            </a>
        </div>
    </div>

    <!-- Top 5 Stat Cards -->
    <div class="stat-grid-row">
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Matching Devices</div>
                <div class="stat-value">{{ number_format($segment->audience_size) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-mobile-screen"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Deliverable</div>
                <div class="stat-value">{{ number_format($segment->deliverable_count) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-brands fa-android"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Android</div>
                <div class="stat-value">
                    {{ number_format(round($segment->audience_size * (($segment->platform_distribution['android'] ?? 91) / 100))) }}
                    <span class="fs-10 fw-normal text-muted ms-1">{{ $segment->platform_distribution['android'] ?? 91 }}%</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper purple-bg">
                <i class="fa-brands fa-apple"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">iOS</div>
                <div class="stat-value">
                    {{ number_format(round($segment->audience_size * (($segment->platform_distribution['ios'] ?? 9) / 100))) }}
                    <span class="fs-10 fw-normal text-muted ms-1">{{ $segment->platform_distribution['ios'] ?? 9 }}%</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon-wrapper red-bg">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Excluded</div>
                <div class="stat-value">{{ number_format($segment->excluded_count) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Anonymous Audience Devices Table -->
        <div class="col-lg-8">
            <div class="devices-table-card">
                <div class="table-card-top-bar">
                    <h2 class="table-title">Anonymous Audience Devices</h2>
                </div>

                <!-- Filters -->
                <div class="p-3 border-bottom bg-white">
                    <form action="{{ route('admin.segments.audience', $segment->id) }}" method="GET" class="d-flex align-items-center gap-2 flex-nowrap">
                        <div class="position-relative filter-search-wrap">
                            <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                            <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search by ID or device" value="{{ request('search') }}">
                        </div>
                        <select name="platform" class="form-select form-select-sm w-auto fs-12">
                            <option value="All">Platform: All</option>
                            <option value="Android" {{ request('platform') === 'Android' ? 'selected' : '' }}>Android</option>
                            <option value="iOS" {{ request('platform') === 'iOS' ? 'selected' : '' }}>iOS</option>
                        </select>
                        <select name="eligibility" class="form-select form-select-sm w-auto fs-12">
                            <option value="All">Eligibility: All</option>
                            <option value="Eligible" {{ request('eligibility') === 'Eligible' ? 'selected' : '' }}>Eligible</option>
                            <option value="Excluded" {{ request('eligibility') === 'Excluded' ? 'selected' : '' }}>Excluded</option>
                        </select>
                        <div class="d-flex gap-1 ms-auto">
                            <a href="{{ route('admin.segments.audience', $segment->id) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                    </form>
                </div>

                <!-- Devices Table -->
                <div class="table-responsive">
                    <table class="devices-data-table">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Installation ID</th>
                                <th>Platform</th>
                                <th>Location</th>
                                <th>App Version</th>
                                <th>Last Active</th>
                                <th>Permission</th>
                                <th class="th-action text-end">Eligibility</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $index => $device)
                                @php
                                    $isEligible = $device->is_active && $device->notification_status === 'Enabled' && !empty($device->fcm_token);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-mobile-screen text-muted fs-12"></i>
                                            <span class="device-name-text">{{ $device->device_model ?: 'Device ' . ($index + 1) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.devices.show', $device->id) }}" class="installation-id-link">
                                            {{ substr($device->installation_id, 0, 16) }}...
                                        </a>
                                    </td>
                                    <td>
                                        <div class="platform-cell">
                                            @if($device->platform === 'iOS')
                                                <i class="fa-brands fa-apple platform-icon ios-icon"></i> iOS
                                            @else
                                                <i class="fa-brands fa-android platform-icon android-icon"></i> Android
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $device->city ? $device->city . ', ' . $device->country : 'Unknown' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $device->app_version ?: '1.4.2' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $device->last_active_human }}</span>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ $device->notification_status === 'Enabled' ? 'Enabled' : 'Disabled' }}</span>
                                    </td>
                                    <td class="td-action">
                                        @if($isEligible)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Eligible</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Excluded</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-mobile-screen fs-1 mb-2 d-block text-gray-300"></i>
                                        No matching devices found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="table-footer-bar">
                    <div class="footer-left-info">
                        Showing {{ $devices->firstItem() ?? 0 }} to {{ $devices->lastItem() ?? 0 }} of {{ number_format($devices->total()) }} devices
                    </div>
                    <div>
                        {{ $devices->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Breakdown & Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4 p-4">
                <h3 class="h6 fw-bold text-dark mb-3">Platform Breakdown</h3>
                <div class="d-flex align-items-center justify-content-center gap-4 py-3">
                    <div style="width: 85px; height: 85px; border-radius: 50%; background: conic-gradient(#3b82f6 0% {{ $segment->platform_distribution['android'] ?? 91 }}%, #cbd5e1 {{ $segment->platform_distribution['android'] ?? 91 }}% 100%); position: relative;" class="d-flex align-items-center justify-content-center">
                        <div class="bg-white rounded-circle d-flex flex-column align-items-center justify-content-center" style="width: 55px; height: 55px;">
                            <strong class="fs-12 text-dark">{{ number_format($segment->audience_size) }}</strong>
                            <span class="fs-10 text-muted">Total</span>
                        </div>
                    </div>
                    <div class="fs-12">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #3b82f6;"></span>
                            <span class="text-muted">Android</span>
                            <strong>{{ $segment->platform_distribution['android'] ?? 91 }}%</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #cbd5e1;"></span>
                            <span class="text-muted">iOS</span>
                            <strong>{{ $segment->platform_distribution['ios'] ?? 9 }}%</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4 p-4">
                <h3 class="h6 fw-bold text-dark mb-3">Eligibility Summary</h3>
                <div class="d-flex flex-column gap-3 fs-13">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-bell text-primary"></i>
                            <span class="text-muted">Notification Enabled</span>
                        </div>
                        <strong class="text-dark">{{ number_format($segment->deliverable_count) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-success"></i>
                            <span class="text-muted">Valid FCM</span>
                        </div>
                        <strong class="text-dark">{{ number_format($segment->deliverable_count) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-ban text-danger"></i>
                            <span class="text-muted">Excluded</span>
                        </div>
                        <strong class="text-danger">{{ number_format($segment->excluded_count) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
