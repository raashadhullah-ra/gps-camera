@extends('layout')

@section('page_title', $location ? 'Create Audience Segment - ' . $location->city : 'Create Audience Segment')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.index') }}">Locations</a>
    <span class="breadcrumb-separator">/</span>
    @if($location)
        <a href="{{ route('admin.locations.show', $location->id) }}">{{ $location->city }}</a>
        <span class="breadcrumb-separator">/</span>
    @endif
    <span class="active-crumb">Create Audience Segment</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- Top Action Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Create Audience Segment</h1>
            <p class="text-secondary fs-13 mb-0">
                {{ $location ? ('Save ' . $location->city . ' users and devices as a reusable notification audience') : 'Save filtered users and devices across locations as a reusable notification audience' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $location ? route('admin.locations.show', $location->id) : route('admin.locations.index') }}" class="btn btn-outline-secondary px-3 py-2 fs-13 rounded-2 fw-medium">Cancel</a>
            <button type="button" class="btn btn-primary px-3 py-2 fs-13 rounded-2 fw-medium" onclick="submitSegmentForm()">Create Segment</button>
        </div>
    </div>

    <form method="POST" action="{{ $location ? route('admin.locations.store-segment', $location->id) : route('admin.locations.store-segment-global') }}" id="createSegmentForm">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Configuration Forms -->
            <div class="col-lg-8">
                <!-- 1. Segment Details -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Segment Details</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Segment Name <span class="text-danger">*</span></label>
                            <div class="input-counter-wrap">
                                <input type="text" name="segment_name" id="segmentNameInput" class="form-control rounded-2 fs-13 pe-5" placeholder="e.g. {{ $location ? $location->city : 'Global' }} Active Users" value="{{ $location ? $location->city : 'Global' }} Active Users" maxlength="80" required oninput="updateCharCount(this, 'nameCounter', 80)">
                                <span class="input-counter-badge" id="nameCounter">{{ strlen(($location ? $location->city : 'Global') . ' Active Users') }} / 80</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold text-dark mb-1">Description (optional)</label>
                            <div class="input-counter-wrap textarea-counter-wrap">
                                <textarea name="description" id="segmentDescInput" class="form-control rounded-2 fs-13 pb-4" rows="4" placeholder="Describe the targeting objective..." maxlength="200" oninput="updateCharCount(this, 'descCounter', 200)">Active GPS Camera users in {{ $location ? $location->city : 'all locations' }} with notifications enabled.</textarea>
                                <span class="input-counter-badge" id="descCounter">{{ strlen('Active GPS Camera users in ' . ($location ? $location->city : 'all locations') . ' with notifications enabled.') }} / 200</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Location Criteria (Dynamic from DB) -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Location Criteria</span>
                    </div>

                    <div class="row g-3">
                        <!-- Country Select -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Country</label>
                            <select class="form-select fs-13 rounded-2" name="country" id="criteriaCountry" onchange="handleCriteriaChange()">
                                <option value="all">All Countries</option>
                                @foreach($filterOptions['countries'] ?? [] as $country)
                                    <option value="{{ $country }}" {{ ($location && $location->country == $country) ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- State / Region Select -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">State / Region</label>
                            <select class="form-select fs-13 rounded-2" name="state" id="criteriaState" onchange="handleCriteriaChange()">
                                <option value="all">All States / Regions</option>
                                @foreach($filterOptions['states'] ?? [] as $state)
                                    <option value="{{ $state }}" {{ ($location && $location->state == $state) ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Select -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">City</label>
                            <select class="form-select fs-13 rounded-2" name="city" id="criteriaCity" onchange="handleCriteriaChange()">
                                <option value="all">All Cities</option>
                                @foreach($filterOptions['cities'] ?? [] as $city)
                                    <option value="{{ $city }}" {{ ($location && $location->city == $city) ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Location Level -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Location Level</label>
                            <select class="form-select fs-13 rounded-2" name="location_level" id="criteriaLevel">
                                <option value="city" {{ $location ? 'selected' : '' }}>City</option>
                                <option value="state">State / Region</option>
                                <option value="country">Country</option>
                                <option value="radius">Custom Radius (km)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 3. Audience Filters -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Audience Filters</span>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Platform</label>
                            <select class="form-select fs-13 rounded-2" name="platform" id="criteriaPlatform" onchange="handleCriteriaChange()">
                                <option value="all" selected>All</option>
                                <option value="android">Android</option>
                                <option value="ios">iOS</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Activity Status</label>
                            <select class="form-select fs-13 rounded-2" name="activity_status" id="criteriaStatus" onchange="handleCriteriaChange()">
                                <option value="all">All</option>
                                <option value="Active" selected>Active</option>
                                <option value="High Activity">High Activity</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Last Active</label>
                            <select class="form-select fs-13 rounded-2" name="last_active" id="criteriaLastActive" onchange="handleCriteriaChange()">
                                <option value="30" selected>Within 30 days</option>
                                <option value="7">Within 7 days</option>
                                <option value="90">Within 90 days</option>
                                <option value="any">Any Time</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1 text-nowrap">Notification Permission</label>
                            <select class="form-select fs-13 rounded-2" name="notification_permission" id="criteriaPermission" onchange="handleCriteriaChange()">
                                <option value="all">All</option>
                                <option value="Enabled" selected>Enabled</option>
                                <option value="Disabled">Disabled</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">App Version</label>
                            <select class="form-select fs-13 rounded-2" name="app_version">
                                <option value="all" selected>All</option>
                                <option value="v3.2.0">v3.2.0+</option>
                                <option value="v3.1.0">v3.1.0+</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Analytics Consent</label>
                            <select class="form-select fs-13 rounded-2" name="analytics_consent">
                                <option value="any" selected>Any</option>
                                <option value="opted_in">Opted In</option>
                                <option value="opted_out">Opted Out</option>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 d-flex align-items-end">
                            <button type="button" class="btn-add-filter w-100 justify-content-center" onclick="showInfoToast('Custom filter attribute dialog')">
                                <i class="fa-solid fa-plus fs-11"></i> Add Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. Exclusions -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Exclusions</span>
                    </div>

                    <div class="d-flex align-items-center gap-4 flex-wrap mb-2">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="exInactive" name="exclude_inactive" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="exInactive">
                                Exclude inactive devices <i class="fa-solid fa-circle-info text-muted fs-12" title="Devices with no check-in over 60 days"></i>
                            </label>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="exInvalidFCM" name="exclude_invalid_fcm" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="exInvalidFCM">
                                Exclude invalid FCM tokens <i class="fa-solid fa-circle-info text-muted fs-12" title="Unreachable or expired push tokens"></i>
                            </label>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="exNotifDenied" name="exclude_notif_denied" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="exNotifDenied">
                                Exclude notification denied <i class="fa-solid fa-circle-info text-muted fs-12" title="Users who revoked notification permission"></i>
                            </label>
                        </div>
                    </div>

                    <div class="segment-info-banner">
                        <i class="fa-solid fa-circle-info fs-14 flex-shrink-0"></i>
                        <span>Segments update automatically when devices meet or leave these conditions.</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Estimated Audience & Summary -->
            <div class="col-lg-4">
                <!-- Estimated Audience Card -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Estimated Audience</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-around text-center py-2 mb-3 border-bottom pb-3">
                        <div>
                            <div class="h3 fw-bold text-dark mb-0" id="statDevicesNum">{{ number_format($estimate['eligible_devices']) }}</div>
                            <span class="fs-12 text-muted">Devices <i class="fa-solid fa-circle-info fs-11"></i></span>
                        </div>
                        <div class="border-end h-100" style="min-height: 40px;"></div>
                        <div>
                            <div class="h3 fw-bold text-dark mb-0" id="statDeliverableNum">{{ number_format($estimate['deliverable_devices']) }}</div>
                            <span class="fs-12 text-muted">Deliverable <i class="fa-solid fa-circle-info fs-11"></i></span>
                        </div>
                    </div>

                    <!-- Donut Chart -->
                    <div class="d-flex align-items-center justify-content-center gap-4 my-4">
                        <div style="width: 100px; height: 100px; position: relative;">
                            <canvas id="audienceDonutChart" width="100" height="100"></canvas>
                        </div>
                        <div class="d-flex flex-column gap-2 fs-12">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                <span class="fw-semibold text-dark">Android</span>
                                <span class="text-muted ms-auto fw-bold" id="donutAndroidPct">{{ $estimate['android_pct'] }}%</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                                <span class="fw-semibold text-dark">iOS</span>
                                <span class="text-muted ms-auto fw-bold" id="donutIosPct">{{ $estimate['ios_pct'] }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Audience Breakdown Metrics -->
                    <table class="audience-summary-table">
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <span class="p-1 rounded-2" style="background: #faf5ff; color: #7e22ce;"><i class="fa-solid fa-user-secret fs-12"></i></span>
                                    Anonymous Users
                                </span>
                            </td>
                            <td class="summary-val" id="statUsersNum">{{ number_format($estimate['anonymous_users']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <span class="p-1 rounded-2" style="background: #ecfdf5; color: #047857;"><i class="fa-solid fa-mobile-screen-button fs-12"></i></span>
                                    Active Devices
                                </span>
                            </td>
                            <td class="summary-val" id="statActiveNum">{{ number_format($estimate['active_devices']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <span class="p-1 rounded-2" style="background: #fff7ed; color: #c2410c;"><i class="fa-solid fa-shield-halved fs-12"></i></span>
                                    Excluded (Estimated)
                                </span>
                            </td>
                            <td class="summary-val" id="statExcludedNum">{{ number_format($estimate['excluded_devices']) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Criteria Summary Card -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Criteria Summary</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2" id="criteriaSummaryBadges">
                        <span class="summary-tag-blue" id="summaryBadgeLoc">
                            <i class="fa-solid fa-location-dot fs-11"></i> <span id="summaryBadgeLocText">{{ $location ? $location->city : 'All Locations' }}</span>
                        </span>
                        <span class="summary-tag-green" id="summaryBadgeStatus">Active</span>
                        <span class="summary-tag-purple" id="summaryBadgePerm">Notifications Enabled</span>
                        <span class="summary-tag-cyan" id="summaryBadgeTime"><i class="fa-regular fa-calendar fs-11"></i> <span id="summaryBadgeTimeText">Last 30 Days</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Action Controls -->
        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-outline-secondary px-4 py-2 fs-13 rounded-2 fw-medium" onclick="saveAsDraft()">Save as Draft</button>
            <button type="submit" class="btn btn-primary px-4 py-2 fs-13 rounded-2 fw-medium">Create Segment</button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function updateCharCount(input, counterId, max) {
    const counter = document.getElementById(counterId);
    if (counter) {
        counter.textContent = input.value.length + ' / ' + max;
    }
}

function handleCriteriaChange() {
    const city = document.getElementById('criteriaCity').value;
    const state = document.getElementById('criteriaState').value;
    const country = document.getElementById('criteriaCountry').value;
    const status = document.getElementById('criteriaStatus').value;
    const permission = document.getElementById('criteriaPermission').value;
    const lastActive = document.getElementById('criteriaLastActive').value;

    let locLabel = 'All Locations';
    if (city !== 'all') {
        locLabel = city;
    } else if (state !== 'all') {
        locLabel = state;
    } else if (country !== 'all') {
        locLabel = country;
    }

    const badgeLocText = document.getElementById('summaryBadgeLocText');
    if (badgeLocText) badgeLocText.textContent = locLabel;

    const badgeStatus = document.getElementById('summaryBadgeStatus');
    if (badgeStatus) badgeStatus.textContent = status === 'all' ? 'All Statuses' : status;

    const badgePerm = document.getElementById('summaryBadgePerm');
    if (badgePerm) badgePerm.textContent = permission === 'all' ? 'Any Permission' : (permission === 'Enabled' ? 'Notifications Enabled' : 'Notifications Disabled');

    const badgeTimeText = document.getElementById('summaryBadgeTimeText');
    if (badgeTimeText) {
        badgeTimeText.textContent = lastActive === '30' ? 'Last 30 Days' : (lastActive === '7' ? 'Last 7 Days' : (lastActive === '90' ? 'Last 90 Days' : 'All Time'));
    }
}

function submitSegmentForm() {
    document.getElementById('createSegmentForm').submit();
}

function saveAsDraft() {
    window.showToast('success', 'Segment saved as draft', 'Audience segment draft saved successfully.');
    setTimeout(() => {
        window.location.href = "{{ route('admin.locations.index') }}";
    }, 1200);
}

document.addEventListener('DOMContentLoaded', () => {
    // Donut chart
    const canvas = document.getElementById('audienceDonutChart');
    if (canvas && window.Chart) {
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: ['Android', 'iOS'],
                datasets: [{
                    data: [{{ $estimate['android_pct'] }}, {{ $estimate['ios_pct'] }}],
                    backgroundColor: ['#10b981', '#3b82f6'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
