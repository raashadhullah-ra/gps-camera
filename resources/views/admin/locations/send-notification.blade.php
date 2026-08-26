@extends('layout')

@section('page_title', $location ? 'Send Notification to ' . $location->city : 'Send Notification - Global')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Installations</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('admin.locations.index') }}">Locations</a>
    <span class="breadcrumb-separator">/</span>
    @if($location)
        <a href="{{ route('admin.locations.show', $location->id) }}">{{ $location->city }}</a>
        <span class="breadcrumb-separator">/</span>
    @endif
    <span class="active-crumb">Send Notification</span>
@endsection

@section('content')
<div class="locations-page">

    <!-- Top Header Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                {{ $location ? 'Send Notification to ' . $location->city : 'Send Notification' }}
            </h1>
            <p class="text-secondary fs-13 mb-0">
                {{ $location ? 'Target notification-enabled devices within this aggregated location' : 'Target notification-enabled devices across all locations or selected regions' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $location ? route('admin.locations.show', $location->id) : route('admin.locations.index') }}" class="btn btn-outline-secondary px-3 py-2 fs-13 rounded-2 fw-medium">Cancel</a>
            <button type="button" class="btn btn-primary px-3 py-2 fs-13 rounded-2 fw-medium" onclick="submitNotificationForm()">Send Now</button>
        </div>
    </div>

    <!-- Location Hero Bar -->
    <div class="location-hero-bar">
        <div class="hero-left-section">
            <div class="hero-pin-circle">
                @if($location)
                    <i class="fa-solid fa-location-dot"></i>
                @else
                    <i class="fa-solid fa-globe"></i>
                @endif
            </div>
            <div>
                <h6 class="hero-city-title">{{ $location ? $location->city : 'All Locations' }}</h6>
                <p class="hero-region-sub">
                    {{ $location ? ($location->state . ', ' . $location->country) : 'Global Telemetry • 142 Countries, 12,594 Cities' }}
                </p>
            </div>
        </div>

        <div class="hero-right-metrics">
            <div class="hero-stat-block">
                <span class="hero-stat-label d-block">Audience</span>
                <div class="hero-stat-value-group">
                    <span class="hero-stat-num">{{ number_format($estimate['eligible_devices']) }}</span>
                    <span class="hero-stat-desc">Eligible devices</span>
                </div>
            </div>

            <div class="hero-platform-item">
                <i class="fa-brands fa-android hero-platform-icon-android"></i>
                <div class="hero-platform-meta">
                    <span class="hero-platform-name d-block">Android</span>
                    <span class="hero-platform-pct">{{ $estimate['android_pct'] }}%</span>
                </div>
            </div>

            <div class="hero-platform-item">
                <i class="fa-brands fa-apple hero-platform-icon-ios"></i>
                <div class="hero-platform-meta">
                    <span class="hero-platform-name d-block">iOS</span>
                    <span class="hero-platform-pct">{{ $estimate['ios_pct'] }}%</span>
                </div>
            </div>

            <button type="button" class="btn btn-link hero-change-location-btn p-0 border-0" data-bs-toggle="modal" data-bs-target="#changeLocationModal">
                <span>Change Location</span>
                <i class="fa-solid fa-pen fs-11"></i>
            </button>
        </div>
    </div>

    <form method="POST" action="{{ $location ? route('admin.locations.submit-notification', $location->id) : route('admin.locations.submit-notification-global') }}" id="sendNotificationForm">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Notification Content Composer -->
            <div class="col-lg-7">
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Notification Content</span>
                    </div>

                    <!-- Notification Title -->
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Notification Title <span class="text-danger">*</span></label>
                        <div class="input-counter-wrap">
                            <input type="text" name="title" id="notifTitleInput" class="form-control rounded-2 fs-13 pe-5" placeholder="e.g. New GPS Camera update available" value="New GPS Camera update available" maxlength="100" required oninput="syncPreviewTitle(this)">
                            <span class="input-counter-badge" id="titleCounter">{{ strlen('New GPS Camera update available') }} / 100</span>
                        </div>
                    </div>

                    <!-- Message Body (4 Rows) -->
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Message <span class="text-danger">*</span></label>
                        <div class="input-counter-wrap textarea-counter-wrap">
                            <textarea name="message" id="notifMessageInput" class="form-control rounded-2 fs-13 pb-4" rows="4" placeholder="Enter push notification message..." maxlength="200" required oninput="syncPreviewMessage(this)">Explore improved location accuracy and new photo stamp styles.</textarea>
                            <span class="input-counter-badge" id="messageCounter">{{ strlen('Explore improved location accuracy and new photo stamp styles.') }} / 200</span>
                        </div>
                    </div>

                    <!-- Image URL -->
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold text-dark mb-1">Image URL (Optional)</label>
                        <div class="input-counter-wrap">
                            <input type="url" name="image_url" id="notifImageUrlInput" class="form-control rounded-2 fs-13 pe-5" placeholder="https://example.com/image.png" maxlength="500" oninput="updateCharCount(this, 'imgCounter', 500)">
                            <span class="input-counter-badge" id="imgCounter">0 / 500</span>
                        </div>
                        <span class="fs-11 text-muted mt-1 d-block">Recommended size: 512x256px (16:9)</span>
                    </div>

                    <!-- Action & Priority Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">On Tap Action</label>
                            <select class="form-select fs-13 rounded-2" name="on_tap_action">
                                <option value="open_app" selected>Open App</option>
                                <option value="open_camera">Open GPS Camera</option>
                                <option value="open_url">Open Web Link</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fs-12 fw-semibold text-muted mb-1">Priority</label>
                            <select class="form-select fs-13 rounded-2" name="priority">
                                <option value="high" selected>High</option>
                                <option value="normal">Normal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Delivery Timing -->
                    <div class="mb-3">
                        <label class="form-label fs-12 fw-semibold text-muted mb-1">Delivery Time</label>
                        <div class="d-flex align-items-center gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_time" id="deliveryNow" value="now" checked>
                                <label class="form-check-label fs-13 text-dark fw-medium" for="deliveryNow">Send Now</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery_time" id="deliveryLater" value="schedule">
                                <label class="form-check-label fs-13 text-dark fw-medium" for="deliveryLater">Schedule for Later</label>
                            </div>
                        </div>
                    </div>

                    <!-- Switches Row -->
                    <div class="d-flex align-items-center gap-4 flex-wrap mb-4 pt-2 border-top">
                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="swSound" name="sound" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="swSound">Sound</label>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="swTrack" name="track_opens" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="swTrack">Track Opens</label>
                        </div>

                        <div class="form-check form-switch d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="swExInactive" name="exclude_inactive" checked>
                            <label class="form-check-label fs-13 text-dark fw-medium" for="swExInactive">
                                Exclude Inactive Devices <i class="fa-solid fa-circle-info text-muted fs-12"></i>
                            </label>
                        </div>
                    </div>

                    <!-- Advanced Targeting Card -->
                    <div class="advanced-targeting-card">
                        <button class="adv-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdv" aria-expanded="false">
                            <div>
                                <div class="adv-title">Advanced Targeting</div>
                                <div class="adv-sub">Current target: {{ $location ? $location->city : 'All Locations (Global)' }}</div>
                            </div>
                            <i class="fa-solid fa-chevron-down adv-chevron"></i>
                        </button>
                        <div id="collapseAdv" class="collapse">
                            <div class="adv-content-body">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label fs-11 text-muted mb-1">Target Minimum App Version</label>
                                        <select class="form-select form-select-sm fs-12">
                                            <option selected>All versions</option>
                                            <option>v3.0.0+</option>
                                            <option>v3.2.0+</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label fs-11 text-muted mb-1">Target Language</label>
                                        <select class="form-select form-select-sm fs-12">
                                            <option selected>All Languages</option>
                                            <option>English</option>
                                            <option>Tamil</option>
                                            <option>Hindi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 fs-13 rounded-2 fw-medium d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Send Notification
                    </button>
                </div>
            </div>

            <!-- Right Column: Live Mockup & Audience Summary -->
            <div class="col-lg-5">
                <!-- 1. Notification Live Preview Phone Mockup -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Notification Preview</span>
                    </div>

                    <div class="mobile-preview-frame">
                        <!-- Dynamic Island -->
                        <div class="mobile-dynamic-island"></div>

                        <!-- Status Bar -->
                        <div class="mobile-status-bar">
                            <span>9:41</span>
                            <div class="d-flex align-items-center gap-1 fs-10">
                                <i class="fa-solid fa-signal"></i>
                                <i class="fa-solid fa-wifi"></i>
                                <i class="fa-solid fa-battery-full"></i>
                            </div>
                        </div>

                        <!-- Phone Wallpaper & Live Push Notification Card -->
                        <div class="mobile-wallpaper-area">
                            <div class="live-push-card">
                                <div class="push-app-icon">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <div class="push-content">
                                    <div class="push-header-row">
                                        <span class="push-app-name">GPS Camera</span>
                                        <span class="push-time">now</span>
                                    </div>
                                    <div class="push-title" id="previewTitle">New GPS Camera update available</div>
                                    <div class="push-body" id="previewMessage">Explore improved location accuracy and new photo stamp styles.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Audience Summary Card -->
                <div class="segment-form-card">
                    <div class="segment-section-header">
                        <span>Audience Summary</span>
                    </div>

                    <table class="audience-summary-table">
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <i class="fa-solid fa-location-dot text-primary"></i> Location
                                </span>
                            </td>
                            <td class="summary-val">{{ $location ? $location->city : 'All Locations (Global)' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <i class="fa-solid fa-users text-purple"></i> Eligible Devices
                                </span>
                            </td>
                            <td class="summary-val">{{ number_format($estimate['eligible_devices']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <i class="fa-solid fa-shield-check text-success"></i> Notification Permission
                                </span>
                            </td>
                            <td class="summary-val text-success">Granted</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <i class="fa-solid fa-paper-plane text-info"></i> Estimated Delivery
                                </span>
                            </td>
                            <td class="summary-val">{{ number_format($estimate['deliverable_devices']) }}</td>
                        </tr>
                        <tr>
                            <td>
                                <span class="summary-label">
                                    <i class="fa-solid fa-ban text-danger"></i> Excluded (No Permission / Inactive / Invalid Tokens)
                                </span>
                            </td>
                            <td class="summary-val text-danger">{{ number_format($estimate['excluded_devices']) }}</td>
                        </tr>
                    </table>

                    <!-- Warning Callout Banner -->
                    <div class="amber-warning-callout">
                        <i class="fa-solid fa-triangle-exclamation fs-14 flex-shrink-0 mt-1"></i>
                        <span>Location targeting uses aggregated city-level data and current FCM token availability.</span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Change Location Modal -->
    <div class="modal fade" id="changeLocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark">Select Target Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="search-box mb-3 w-100">
                        <input type="text" class="form-control fs-13" placeholder="Search city, state or country..." id="locSearchInput" oninput="filterLocationList(this.value)">
                    </div>

                    <div class="list-group list-group-flush border rounded-2" style="max-height: 320px; overflow-y: auto;" id="locationSelectList">
                        <!-- Global Option -->
                        <a href="{{ route('admin.locations.send-notification-global') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 {{ !$location ? 'active' : '' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-globe text-primary"></i>
                                <div>
                                    <span class="fw-bold fs-13 d-block">All Locations (Global)</span>
                                    <span class="fs-11 text-muted">91,240 devices &bull; 142 countries</span>
                                </div>
                            </div>
                            @if(!$location)
                                <i class="fa-solid fa-check text-primary"></i>
                            @endif
                        </a>

                        <!-- City Options -->
                        @foreach($allLocations as $locItem)
                        <a href="{{ route('admin.locations.send-notification', $locItem->id) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 loc-item-row {{ ($location && $location->id == $locItem->id) ? 'active' : '' }}" data-search="{{ strtolower($locItem->city . ' ' . $locItem->state . ' ' . $locItem->country) }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-location-dot text-muted"></i>
                                <div>
                                    <span class="fw-bold fs-13 d-block">{{ $locItem->city }}</span>
                                    <span class="fs-11 text-muted">{{ $locItem->state }}, {{ $locItem->country }} &bull; {{ number_format($locItem->devices_count) }} devices</span>
                                </div>
                            </div>
                            @if($location && $location->id == $locItem->id)
                                <i class="fa-solid fa-check text-primary"></i>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function syncPreviewTitle(input) {
    const preview = document.getElementById('previewTitle');
    const counter = document.getElementById('titleCounter');
    if (preview) {
        preview.textContent = input.value.trim() || 'Notification Title';
    }
    if (counter) {
        counter.textContent = input.value.length + ' / 100';
    }
}

function syncPreviewMessage(input) {
    const preview = document.getElementById('previewMessage');
    const counter = document.getElementById('messageCounter');
    if (preview) {
        preview.textContent = input.value.trim() || 'Notification message text...';
    }
    if (counter) {
        counter.textContent = input.value.length + ' / 200';
    }
}

function updateCharCount(input, counterId, max) {
    const counter = document.getElementById(counterId);
    if (counter) {
        counter.textContent = input.value.length + ' / ' + max;
    }
}

function submitNotificationForm() {
    document.getElementById('sendNotificationForm').submit();
}

function filterLocationList(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.loc-item-row');
    rows.forEach(r => {
        const text = r.getAttribute('data-search') || '';
        r.style.display = text.includes(q) ? 'flex' : 'none';
    });
}
</script>
@endpush
