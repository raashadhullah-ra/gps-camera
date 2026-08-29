@extends('layout')

@section('title', "Device Details - {$device->installation_id}")
@section('page_title', 'Device Details')

@section('breadcrumbs')
    <a href="{{ route('admin.devices.index') }}" class="text-decoration-none text-muted">Installations</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.devices.index') }}" class="text-decoration-none text-muted">Installed Devices</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Device Details</span>
@endsection

@section('content')
<div class="device-details-page">

    <!-- 1. Header with Back Button and Quick Actions -->
    <div class="details-header-row">
        <div class="header-left">
            <a href="{{ route('admin.devices.index') }}" class="btn-back-arrow" title="Back to Installed Devices">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="header-titles">
                <h1 class="details-main-title">Device Details</h1>
                <div class="details-sub-meta">
                    <span class="install-id-label">Installation ID: <strong>{{ $device->installation_id }}</strong></span>
                    @if($device->is_active)
                        <span class="badge-status-pill active">Active</span>
                    @else
                        <span class="badge-status-pill inactive">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="header-right">
            <button type="button" class="btn btn-primary btn-send-notif" onclick="openSendNotificationModal()">
                <i class="fa-solid fa-plus"></i>
                <span>Send Notification</span>
            </button>

            <!-- Dropdown Options -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn-header-more" data-bs-toggle="dropdown" aria-expanded="false" title="More Options">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 py-2">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="copyToClipboard('{{ $device->installation_id }}')">
                            <i class="fa-regular fa-copy text-secondary" style="width: 16px;"></i> Copy Installation ID
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.devices.export') }}">
                            <i class="fa-solid fa-download text-secondary" style="width: 16px;"></i> Export Telemetry
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    @if($device->is_active)
                        <li>
                            <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2 text-warning" onclick="openMarkInactiveModal('{{ $device->id }}', '{{ $device->installation_id }}', '{{ $device->device_model }}', '{{ $device->platform }} {{ $device->os_version }}', '{{ $device->location_formatted }}')">
                                <i class="fa-solid fa-circle-pause" style="width: 16px;"></i> Mark as Inactive
                            </button>
                        </li>
                    @else
                        <li>
                            <form action="{{ route('admin.devices.toggle-status', $device->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 text-success">
                                    <i class="fa-solid fa-circle-play" style="width: 16px;"></i> Reactivate Device
                                </button>
                            </form>
                        </li>
                    @endif
                    <li>
                        <button type="button" class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" onclick="openDeleteModal('{{ $device->id }}', '{{ $device->installation_id }}', '{{ $device->device_model }}', '{{ $device->platform }} {{ $device->os_version }}', '{{ $device->location_formatted }}')">
                            <i class="fa-regular fa-trash-can" style="width: 16px;"></i> Delete Device Record
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 2. Device Profile Banner Card -->
    <div class="device-profile-banner">
        <div class="device-icon-box">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <div class="device-info-main">
            <h2 class="device-title">{{ $device->device_model }}</h2>
            <div class="device-badges-row">
                <div class="badge-item">
                    @if(strtolower($device->platform) === 'ios')
                        <i class="fa-brands fa-apple platform-icon ios-icon"></i>
                    @else
                        <i class="fa-brands fa-android platform-icon android-icon"></i>
                    @endif
                    <span>{{ $device->os_version }}</span>
                </div>

                <div class="badge-item">
                    <i class="fa-solid fa-code-branch text-secondary"></i>
                    <span>v{{ $device->app_version }}</span>
                </div>

                <div class="badge-item">
                    <i class="fa-solid fa-location-dot text-secondary"></i>
                    <span>{{ $device->location_formatted }}</span>
                </div>

                <div class="badge-item">
                    <i class="fa-regular fa-clock text-secondary"></i>
                    <span>Last active: {{ $device->last_active_human }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Navigation Tabs -->
    <div class="details-tabs-nav">
        <button type="button" class="tab-btn active" onclick="switchDetailTab('overview', this)">Overview</button>
        <button type="button" class="tab-btn" onclick="switchDetailTab('permissions', this)">Permissions</button>
        <button type="button" class="tab-btn" onclick="switchDetailTab('notification', this)">Notification</button>
        <button type="button" class="tab-btn" onclick="switchDetailTab('activity', this)">Activity History</button>
        <button type="button" class="tab-btn" onclick="switchDetailTab('crash', this)">Crash History</button>
    </div>

    <!-- 4. Tab Content: Overview (6 Cards Grid) -->
    <div class="tab-content-panel" id="tabContentOverview">
        <div class="details-cards-grid">

            <!-- Card 1: Installation Information -->
            <div class="detail-card">
                <div class="card-header-clean">
                    <h3 class="card-title">Installation Information</h3>
                </div>
                <div class="card-body-spec">
                    <div class="spec-row">
                        <span class="spec-label">Installation ID</span>
                        <div class="spec-value-copy">
                            <span class="spec-highlight-blue">{{ $device->installation_id }}</span>
                            <button type="button" class="btn-copy-mini" onclick="copyToClipboard('{{ $device->installation_id }}')" title="Copy">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Firebase Installation ID</span>
                        <div class="spec-value-copy">
                            <span>{{ $device->short_fid }}</span>
                            <button type="button" class="btn-copy-mini" onclick="copyToClipboard('{{ $device->firebase_installation_id ?? $device->short_fid }}')" title="Copy">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">First Installed</span>
                        <span class="spec-value">{{ $device->first_installed_at ? $device->first_installed_at->format('M d, Y g:i A') : 'Aug 18, 2026 10:24 AM' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Last Updated</span>
                        <span class="spec-value">{{ $device->updated_at ? $device->updated_at->format('M d, Y g:i A') : 'Aug 18, 2026 6:42 PM' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Device Information -->
            <div class="detail-card">
                <div class="card-header-clean">
                    <h3 class="card-title">Device Information</h3>
                </div>
                <div class="card-body-spec">
                    <div class="spec-row">
                        <span class="spec-label">Manufacturer</span>
                        <span class="spec-value">{{ $device->device_manufacturer ?? $device->device_brand ?? 'Samsung' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Model</span>
                        <span class="spec-value">{{ $device->device_model }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Device</span>
                        <span class="spec-value">{{ $device->device_code ?? 'SM-S921B' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Architecture</span>
                        <span class="spec-value">{{ $device->cpu_architecture ?? 'arm64-v8a' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Screen</span>
                        <span class="spec-value">{{ $device->screen_resolution ?? '1080 x 2340' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 3: System & App -->
            <div class="detail-card">
                <div class="card-header-clean">
                    <h3 class="card-title">System & App</h3>
                </div>
                <div class="card-body-spec">
                    <div class="spec-row">
                        <span class="spec-label">Platform</span>
                        <div class="spec-value d-flex align-items-center gap-2">
                            @if(strtolower($device->platform) === 'ios')
                                <i class="fa-brands fa-apple text-dark"></i>
                                <span>iOS</span>
                            @else
                                <i class="fa-brands fa-android text-success"></i>
                                <span>Android</span>
                            @endif
                        </div>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">OS Version</span>
                        <span class="spec-value">{{ $device->os_version }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">SDK</span>
                        <span class="spec-value">{{ $device->sdk_version ?? 35 }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">App Version</span>
                        <span class="spec-value">{{ $device->app_version }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Build</span>
                        <span class="spec-value">{{ $device->app_build_number ?? 42 }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Language</span>
                        <span class="spec-value">{{ $device->language ?? 'English' }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Timezone</span>
                        <span class="spec-value">{{ $device->timezone ?? 'Asia/Kolkata' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 4: Granular Permissions List -->
            <div class="detail-card">
                <div class="card-header-clean">
                    <h3 class="card-title">Permissions</h3>
                </div>
                <div class="card-body-spec">
                    <!-- Camera -->
                    <div class="spec-row">
                        <div class="perm-title-group">
                            <i class="fa-regular fa-circle-check text-success"></i>
                            <span>Camera</span>
                        </div>
                        <span class="perm-status-badge granted">
                            {{ $device->camera_granted ? 'Granted' : 'Denied' }}
                        </span>
                    </div>

                    <!-- Location -->
                    <div class="spec-row">
                        <div class="perm-title-group">
                            <i class="fa-regular fa-circle-check text-success"></i>
                            <span>{{ $device->location_permission_label }}</span>
                        </div>
                        <span class="perm-status-badge {{ $device->location_granted ? 'granted' : 'denied' }}">
                            {{ $device->location_granted ? 'Granted' : 'Denied' }}
                        </span>
                    </div>

                    <!-- Notifications -->
                    <div class="spec-row">
                        <div class="perm-title-group">
                            <i class="fa-regular fa-circle-check text-success"></i>
                            <span>Notifications</span>
                        </div>
                        <span class="perm-status-badge {{ $device->notifications_granted ? 'granted' : 'denied' }}">
                            {{ $device->notifications_granted ? 'Granted' : 'Denied' }}
                        </span>
                    </div>

                    <!-- Photos & Media -->
                    <div class="spec-row">
                        <div class="perm-title-group">
                            <i class="fa-regular fa-circle-check text-success"></i>
                            <span>Photos & Media</span>
                        </div>
                        <span class="perm-status-badge {{ $device->photos_granted ? 'granted' : 'denied' }}">
                            {{ $device->photos_granted ? 'Granted' : 'Denied' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 5: Notification Status & Telemetry -->
            <div class="detail-card">
                <div class="card-header-clean">
                    <h3 class="card-title">Notification Status</h3>
                </div>
                <div class="card-body-spec">
                    <div class="spec-row">
                        <span class="spec-label">FCM Token</span>
                        <span class="spec-value text-success fw-semibold">Active</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Token Updated</span>
                        <span class="spec-value">{{ $device->token_updated_human }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Last Delivered</span>
                        <span class="spec-value">{{ $device->last_delivered_human }}</span>
                    </div>

                    <div class="spec-row">
                        <span class="spec-label">Last Opened</span>
                        <span class="spec-value">{{ $device->last_opened_human }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 6: Real Interactive Location Map Card -->
            <div class="detail-card card-location-map">
                <div class="card-header-clean d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Location</h3>
                    @if($device->has_coordinates)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 px-2 py-0">
                            <i class="fa-solid fa-satellite-dish me-1"></i> Live GPS
                        </span>
                    @endif
                </div>
                <div class="card-body-spec">
                    @if($device->has_location || $device->has_coordinates)
                        <div class="location-title-text fw-medium text-dark">{{ $device->full_location_formatted }}</div>

                        @if($device->has_coordinates)
                            <div class="fs-12 text-muted mb-2 d-flex align-items-center gap-1">
                                <i class="fa-solid fa-location-crosshairs text-primary"></i>
                                <span>{{ $device->coordinates_formatted }}</span>
                            </div>

                            <!-- Real Interactive Leaflet OpenStreetMap -->
                            <div id="deviceLeafletMap"></div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-view-map-link w-100" onclick="openDeviceFullMapModal()">
                                    <span>View Full Map</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square fs-12 ms-1"></i>
                                </button>
                            </div>
                        @else
                            <!-- City available without exact GPS coordinates -->
                            <div class="empty-location-box">
                                <div class="empty-loc-icon text-primary">
                                    <i class="fa-solid fa-city"></i>
                                </div>
                                <div class="empty-loc-title">{{ $device->location_formatted }}</div>
                                <div class="empty-loc-desc">City identified, but precise GPS coordinates not sent.</div>
                                <div class="empty-loc-badge">
                                    <span class="badge-loc-status granted">
                                        <i class="fa-regular fa-circle-check me-1"></i> {{ $device->location_permission_label }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-view-map-link w-100" onclick="openDeviceFullMapModal()">
                                    <span>View Full Map</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square fs-12 ms-1"></i>
                                </button>
                            </div>
                        @endif
                    @else
                        <!-- Clean Empty State when Location is NULL -->
                        <div class="location-title-text text-muted mb-2">
                            <i class="fa-solid fa-location-slash me-1 text-secondary"></i> Location Unavailable
                        </div>

                        <div class="empty-location-box">
                            <div class="empty-loc-icon">
                                <i class="fa-solid fa-location-crosshairs"></i>
                            </div>
                            <div class="empty-loc-title">No GPS Coordinates Reported</div>
                            <div class="empty-loc-desc">Device has not transmitted GPS location telemetry.</div>
                            <div class="empty-loc-badge">
                                @if($device->location_granted)
                                    <span class="badge-loc-status granted">
                                        <i class="fa-regular fa-circle-check me-1"></i> {{ $device->location_permission_label }}: Granted
                                    </span>
                                @else
                                    <span class="badge-loc-status denied">
                                        <i class="fa-regular fa-circle-xmark me-1"></i> Location Permission: Denied
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-view-map-link w-100 disabled" disabled>
                                <i class="fa-solid fa-map-location-dot me-1"></i>
                                <span>Map Unavailable (No GPS Data)</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Bottom Informational Subtitle -->
        <div class="details-bottom-note">
            <span>Device record created from first app launch</span>
        </div>
    </div>

</div>

<!-- Modal 1: Send Push Notification Modal -->
<div class="modal fade" id="directNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 rounded-4 shadow-lg p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">Send Notification to Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <p class="text-muted fs-13 mb-3">Targeting: <strong>{{ $device->device_model }}</strong> ({{ $device->installation_id }})</p>
            <form id="directPushForm" onsubmit="handleSendDirectPush(event)">
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold">Title *</label>
                    <input type="text" class="form-control fs-13" required placeholder="e.g. New Watermark Template" id="directNotifTitle">
                </div>
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold">Message Body *</label>
                    <textarea class="form-control fs-13" rows="4" style="min-height: 104px;" required placeholder="Type push message..." id="directNotifBody"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold">Deep Link URL / Action (Optional)</label>
                    <input type="text" class="form-control fs-13" placeholder="geocam://watermark/styles" id="directNotifAction">
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary fs-13" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fs-13 d-inline-flex align-items-center gap-2" id="btnSendDirectFcm">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Send via FCM</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Mark As Inactive Modal (Exact Screenshot 3 Design) -->
<div class="modal fade" id="markInactiveModal" tabindex="-1" aria-hidden="true">
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
                    <div class="fw-bold text-primary fs-13">{{ $device->installation_id }}</div>
                    <div class="text-dark fs-12">{{ $device->device_model }}</div>
                </div>
                <div class="device-mini-meta ms-auto text-end">
                    <div class="fs-12 text-muted">{{ $device->platform }} {{ $device->os_version }}</div>
                    <div class="fs-11 text-muted">{{ $device->location_formatted }}</div>
                </div>
                <div class="ms-2">
                    <span class="status-dot-active">● Active</span>
                </div>
            </div>

            <form action="{{ route('admin.devices.mark-inactive', $device->id) }}" method="POST" id="inactiveForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fs-13 fw-semibold text-dark">Reason *</label>
                    <select name="reason" class="form-select fs-13" required id="inactiveReasonSelect">
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
                    <textarea name="admin_notes" class="form-control fs-13" rows="4" style="min-height: 104px;" placeholder="Add an internal note" maxlength="500" id="inactiveNotesText" oninput="updateCharCount(this)"></textarea>
                    <div class="text-end text-muted fs-11 mt-1" id="charCountLabel">0/500</div>
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
                    <input class="form-check-input" type="checkbox" id="confirmInactiveCheck" required onchange="toggleInactiveSubmit(this)">
                    <label class="form-check-label fs-12 text-dark" for="confirmInactiveCheck">
                        I understand the impact of this action.
                    </label>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary fs-13 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fs-13 px-3 text-white" id="btnSubmitInactive" disabled>
                        Mark as Inactive
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Delete Device Record Modal (Exact Screenshot 2 Design) -->
<div class="modal fade" id="deleteDeviceModal" tabindex="-1" aria-hidden="true">
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
                    <div class="text-primary fw-bold fs-13">{{ $device->installation_id }}</div>
                    <div class="fs-11 text-muted">Installation ID</div>
                </div>
                <div class="sum-col">
                    <div class="fw-semibold text-dark fs-12">{{ $device->device_model }}</div>
                    <div class="fs-11 text-muted">Device</div>
                </div>
                <div class="sum-col">
                    <div class="fs-12 text-dark">{{ $device->platform }} {{ $device->os_version }}</div>
                    <div class="fs-11 text-muted">Platform</div>
                </div>
                <div class="sum-col">
                    <div class="fs-12 text-dark">{{ $device->location_formatted }}</div>
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

            <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" id="deleteFormSubmit">
                @csrf
                @method('DELETE')

                <div class="mb-3">
                    <label class="form-label fs-12 fw-semibold text-dark">
                        Type <span class="text-danger fw-bold">{{ $device->installation_id }}</span> to confirm
                    </label>
                    <input type="text" class="form-control fs-13" placeholder="Enter Installation ID" id="confirmDeleteInput" oninput="checkDeleteMatch(this, '{{ $device->installation_id }}')">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmDeleteCheck" onchange="checkDeleteReady('{{ $device->installation_id }}')">
                    <label class="form-check-label fs-12 text-dark" for="confirmDeleteCheck">
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
                    <button type="submit" class="btn btn-danger fs-13 px-3" id="btnConfirmDeleteFinal" disabled>
                        <i class="fa-regular fa-trash-can me-1"></i> Delete Device Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Device Full Map Modal (Interactive Leaflet Map) -->
<div class="modal fade" id="deviceFullMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 820px;">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-bottom px-4 py-3 bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-location-dot text-primary"></i>
                        <span>{{ $device->city ?? $device->device_model }} GPS Location Map</span>
                    </h5>
                    <p class="text-muted fs-12 mb-0">{{ $device->full_location_formatted }} @if($device->coordinates_formatted)• <span class="fw-medium text-dark">{{ $device->coordinates_formatted }}</span>@endif</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="btnModalTileOsm" onclick="setModalMapTile('osm')">Map</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnModalTileSat" onclick="setModalMapTile('sat')">Satellite</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative">
                <div id="deviceModalLeafletMap" style="height: 480px; width: 100%;"></div>
            </div>
            <div class="modal-footer border-top px-4 py-2 bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3 fs-12 text-muted">
                    <span><i class="fa-solid fa-mobile-screen text-primary me-1"></i> {{ $device->device_model }}</span>
                    <span><i class="fa-solid fa-fingerprint text-secondary me-1"></i> {{ $device->installation_id }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($device->latitude && $device->longitude)
                        <a href="https://www.openstreetmap.org/?mlat={{ $device->latitude }}&mlon={{ $device->longitude }}#map=16/{{ $device->latitude }}/{{ $device->longitude }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                            <span>OpenStreetMap</span>
                            <i class="fa-solid fa-arrow-up-right-from-square fs-10"></i>
                        </a>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        window.showToast('success', 'Copied to clipboard', text + ' copied to clipboard');
    }).catch(() => {
        window.showToast('info', 'Device ID', text);
    });
}

function openSendNotificationModal() {
    const modalEl = document.getElementById('directNotificationModal');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function handleSendDirectPush(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSendDirectFcm');
    const originalBtnHtml = btn.innerHTML;
    const title = document.getElementById('directNotifTitle').value.trim();
    const body = document.getElementById('directNotifBody').value.trim();
    const deepLink = document.getElementById('directNotifAction')?.value.trim() || '';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

    fetch('/admin/devices/{{ $device->id }}/send-push', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
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

        const modalEl = document.getElementById('directNotificationModal');
        if (modalEl && window.bootstrap) {
            bootstrap.Modal.getInstance(modalEl).hide();
        }
        document.getElementById('directPushForm').reset();

        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: 'Notification Sent!',
                text: data.message || 'Push notification dispatched via Google Firebase Cloud Messaging.',
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

function openMarkInactiveModal() {
    const modalEl = document.getElementById('markInactiveModal');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function openDeleteModal() {
    const modalEl = document.getElementById('deleteDeviceModal');
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
}

function updateCharCount(el) {
    const count = el.value.length;
    document.getElementById('charCountLabel').textContent = count + '/500';
}

function toggleInactiveSubmit(checkEl) {
    const btn = document.getElementById('btnSubmitInactive');
    const select = document.getElementById('inactiveReasonSelect');
    btn.disabled = !(checkEl.checked && select.value);
}

document.getElementById('inactiveReasonSelect')?.addEventListener('change', function () {
    const checkEl = document.getElementById('confirmInactiveCheck');
    const btn = document.getElementById('btnSubmitInactive');
    btn.disabled = !(checkEl.checked && this.value);
});

function checkDeleteMatch(inputEl, expectedId) {
    checkDeleteReady(expectedId);
}

function checkDeleteReady(expectedId) {
    const inputVal = document.getElementById('confirmDeleteInput').value.trim();
    const checked = document.getElementById('confirmDeleteCheck').checked;
    const btn = document.getElementById('btnConfirmDeleteFinal');
    btn.disabled = !(inputVal === expectedId && checked);
}

function switchDetailTab(tabKey, btnEl) {
    document.querySelectorAll('.details-tabs-nav .tab-btn').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');

    if (tabKey === 'overview') {
        document.getElementById('tabContentOverview').style.display = 'block';
    } else {
        if (window.Swal) {
            Swal.mixin({ toast: true, position: 'top-end', timer: 2500, showConfirmButton: false }).fire({
                icon: 'info',
                title: 'Viewing ' + tabKey.charAt(0).toUpperCase() + tabKey.slice(1) + ' telemetry logs'
            });
        }
    }
}

@if($device->has_coordinates)
document.addEventListener('DOMContentLoaded', function() {
    if (window.L && document.getElementById('deviceLeafletMap')) {
        const lat = {{ (float) $device->latitude }};
        const lng = {{ (float) $device->longitude }};
        const map = L.map('deviceLeafletMap', {
            center: [lat, lng],
            zoom: 13,
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: false,
            dragging: true,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        const customIcon = L.divIcon({
            className: 'custom-map-pin',
            html: '<div class="map-leaflet-marker"><div class="pin-pulse"></div><i class="fa-solid fa-location-dot"></i></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 28],
            popupAnchor: [0, -28]
        });

        const marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
        marker.bindPopup('<b>{{ addslashes($device->city ?? $device->device_model) }}</b><br><span style="font-size: 11px; color: #64748b;">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>');

        setTimeout(function() {
            map.invalidateSize();
        }, 300);
    }
});
@endif

let deviceModalMap = null;
let modalOsmLayer = null;
let modalSatLayer = null;

function openDeviceFullMapModal() {
    const modalEl = document.getElementById('deviceFullMapModal');
    if (!modalEl || !window.bootstrap) return;

    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    bsModal.show();

    modalEl.addEventListener('shown.bs.modal', function onModalOpen() {
        modalEl.removeEventListener('shown.bs.modal', onModalOpen);

        if (window.L && document.getElementById('deviceModalLeafletMap')) {
            const lat = {{ (float) ($device->latitude ?? 13.0827) }};
            const lng = {{ (float) ($device->longitude ?? 80.2707) }};

            if (!deviceModalMap) {
                deviceModalMap = L.map('deviceModalLeafletMap', {
                    center: [lat, lng],
                    zoom: 14,
                    zoomControl: true,
                    scrollWheelZoom: true,
                });

                modalOsmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(deviceModalMap);

                modalSatLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: '&copy; Esri, Maxar, Earthstar Geographics',
                    maxZoom: 19,
                });

                const customIcon = L.divIcon({
                    className: 'custom-map-pin',
                    html: '<div class="map-leaflet-marker"><div class="pin-pulse"></div><i class="fa-solid fa-location-dot"></i></div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 28],
                    popupAnchor: [0, -28]
                });

                const marker = L.marker([lat, lng], { icon: customIcon }).addTo(deviceModalMap);
                marker.bindPopup('<b>{{ addslashes($device->city ?? $device->device_model) }}</b><br><span style="font-size: 11px; color: #64748b;">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span><br><span class="badge bg-primary text-white mt-1">{{ $device->installation_id }}</span>').openPopup();
            } else {
                deviceModalMap.setView([lat, lng], 14);
            }

            setTimeout(function() {
                deviceModalMap.invalidateSize();
            }, 250);
        }
    });
}

function setModalMapTile(type) {
    if (!deviceModalMap) return;
    const btnOsm = document.getElementById('btnModalTileOsm');
    const btnSat = document.getElementById('btnModalTileSat');

    if (type === 'sat') {
        if (modalOsmLayer) deviceModalMap.removeLayer(modalOsmLayer);
        if (modalSatLayer) deviceModalMap.addLayer(modalSatLayer);
        btnOsm?.classList.remove('active');
        btnSat?.classList.add('active');
    } else {
        if (modalSatLayer) deviceModalMap.removeLayer(modalSatLayer);
        if (modalOsmLayer) deviceModalMap.addLayer(modalOsmLayer);
        btnSat?.classList.remove('active');
        btnOsm?.classList.add('active');
    }
}
</script>
@endpush
