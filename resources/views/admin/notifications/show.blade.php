@extends('layout')

@section('title', ($campaign->name ?: $campaign->title) . ' - Notification Details')
@section('page_title', 'Notification Details')

@section('breadcrumbs')
    <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none text-muted">Engagement</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none text-muted">Notifications</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="text-muted">{{ $campaign->name ?: $campaign->title }}</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Details</span>
@endsection

@section('content')
<div class="notification-details-page">

    {{-- Top Header Row: Title, Subtitle, Badges & Action Buttons --}}
    <div class="details-header-section">
        <div class="header-title-area">
            <h1 class="details-main-title">{{ $campaign->name ?: $campaign->title }}</h1>
            <p class="details-subtitle">Notification campaign details and delivery information</p>
            <div class="header-badges-row">
                <span class="{{ $campaign->status_badge_class }}">
                    @if($campaign->status === 'scheduled')
                        <i class="fa-regular fa-calendar me-1"></i>
                    @elseif($campaign->status === 'sent')
                        <i class="fa-solid fa-check me-1"></i>
                    @endif
                    {{ ucfirst($campaign->status) }}
                </span>
                <span class="campaign-id-badge">
                    Campaign ID: {{ $campaign->campaign_id }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Notifications</span>
            </a>
            <a href="{{ route('admin.locations.send-notification-global') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-pen"></i>
                <span>Edit Notification</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column (col-lg-7) --}}
        <div class="col-lg-7">

            {{-- 1. Notification Content Card --}}
            <div class="details-card">
                <div class="card-header-icon-title">
                    <div class="card-header-icon">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <h2>Notification Content</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Campaign Name</span>
                        <span class="spec-table-val fw-semibold">{{ $campaign->name }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Notification Title</span>
                        <span class="spec-table-val fw-semibold text-primary">{{ $campaign->title }}</span>
                    </div>
                    <div class="spec-table-row align-items-start">
                        <span class="spec-table-label">Message</span>
                        <span class="spec-table-val text-muted">{{ $campaign->message }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Action</span>
                        <span class="spec-table-val fw-semibold text-dark">{{ $campaign->action ?: 'Open App' }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Audience Details Card --}}
            <div class="details-card">
                <div class="card-header-icon-title">
                    <div class="card-header-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h2>Audience Details</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Audience Type</span>
                        <span class="spec-table-val">{{ $campaign->audience_type === 'individual' ? 'Individual Devices' : ($campaign->audience_label ?: 'Audience Segment') }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Selected Devices</span>
                        <span class="spec-table-val fw-semibold">{{ $campaign->total_audience ?: 3 }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Android Devices</span>
                        <span class="spec-table-val text-muted">{{ $campaign->android_count ?: 2 }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">iOS Devices</span>
                        <span class="spec-table-val text-muted">{{ $campaign->ios_count ?: 1 }}</span>
                    </div>
                    <div class="spec-table-row align-items-center">
                        <span class="spec-table-label">Installation IDs</span>
                        <div class="spec-table-val d-flex flex-wrap gap-2">
                            @if(!empty($campaign->target_device_ids) && is_array($campaign->target_device_ids))
                                @foreach($campaign->target_device_ids as $devId)
                                    <span class="device-id-badge">{{ $devId }}</span>
                                @endforeach
                            @else
                                <span class="device-id-badge">ANON-DEVICE-001</span>
                                <span class="device-id-badge">ANON-DEVICE-002</span>
                                <span class="device-id-badge">ANON-DEVICE-003</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Delivery Schedule Card --}}
            <div class="details-card">
                <div class="card-header-icon-title">
                    <div class="card-header-icon">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <h2>Delivery Schedule</h2>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Scheduled Date</span>
                        <span class="spec-table-val fw-semibold">{{ $campaign->scheduled_at?->format('d M Y') ?? '26 Aug 2026' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Scheduled Time</span>
                        <span class="spec-table-val fw-semibold">{{ $campaign->scheduled_at?->format('h:i A') ?? '10:30 AM' }} {{ $campaign->time_zone === 'Asia/Kolkata' ? 'IST' : '' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Time Zone</span>
                        <span class="spec-table-val text-muted">{{ $campaign->time_zone ?: 'Asia/Kolkata' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Expiry</span>
                        <span class="spec-table-val text-muted">{{ $campaign->expiry_hours }} hours after scheduled time</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Quiet Hours</span>
                        <span class="spec-table-val text-muted">{{ $campaign->quiet_hours_enabled ? 'Enabled (10:00 PM – 8:00 AM)' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column (col-lg-5) --}}
        <div class="col-lg-5">

            {{-- 4. Android / iOS Notification Preview Card --}}
            <div class="details-card">
                <h3 class="preview-section-title">Android / iOS Notification Preview</h3>

                <div class="row g-3">
                    {{-- Android Preview --}}
                    <div class="col-12 col-xl-6">
                        <div class="text-muted fw-bold mb-2 fs-11">Android Preview</div>
                        <div class="preview-card-item">
                            <div class="preview-header">
                                <img src="{{ asset('assets/Logos/Geo icon.png') }}" alt="GeoCam" class="app-icon">
                                <span class="app-name">{{ $campaign->title }}</span>
                                <span class="preview-time">now</span>
                            </div>
                            <div class="preview-body">
                                {{ $campaign->message }}
                            </div>
                            <a href="#" class="preview-action-link">{{ $campaign->action ?: 'Open App' }}</a>
                        </div>
                    </div>

                    {{-- iOS Preview --}}
                    <div class="col-12 col-xl-6">
                        <div class="text-muted fw-bold mb-2 fs-11">iOS Preview</div>
                        <div class="preview-card-item">
                            <div class="preview-header">
                                <img src="{{ asset('assets/Logos/Geo icon.png') }}" alt="GeoCam" class="app-icon">
                                <span class="app-name">{{ $campaign->title }}</span>
                                <span class="preview-time">now</span>
                            </div>
                            <div class="preview-body">
                                {{ $campaign->message }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. Campaign Summary Card --}}
            <div class="details-card">
                <div class="card-header-icon-title">
                    <div class="card-header-icon">
                        <i class="fa-regular fa-file-lines"></i>
                    </div>
                    <h3>Campaign Summary</h3>
                </div>
                <div class="spec-table-list">
                    <div class="spec-table-row">
                        <span class="spec-table-label">Created</span>
                        <span class="spec-table-val text-muted">{{ $campaign->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Created By</span>
                        <span class="spec-table-val text-muted">{{ $campaign->creator?->name ?? 'Super Admin' }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Last Updated</span>
                        <span class="spec-table-val text-muted">{{ $campaign->updated_at->format('d M Y') }}</span>
                    </div>
                    <div class="spec-table-row">
                        <span class="spec-table-label">Status</span>
                        <span class="spec-table-val">
                            <span class="{{ $campaign->status_badge_class }}">
                                @if($campaign->status === 'scheduled')
                                    <i class="fa-regular fa-calendar me-1"></i>
                                @elseif($campaign->status === 'sent')
                                    <i class="fa-solid fa-check me-1"></i>
                                @endif
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Ready Scheduled Banner --}}
            @if($campaign->status === 'scheduled')
                <div class="alert-delivery-ready">
                    <div class="ready-icon-circle">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <strong class="ready-title">Ready for scheduled delivery</strong>
                        <span class="ready-subtitle">This campaign is scheduled and will be delivered at the specified time.</span>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- Bottom Section: Process Timeline --}}
    <div class="details-card mt-2">
        <div class="timeline-steps-container">
            @if(!empty($campaign->timeline_steps) && is_array($campaign->timeline_steps))
                @foreach($campaign->timeline_steps as $stepIndex => $step)
                    <div class="timeline-step-item">
                        <div class="step-node-icon {{ !empty($step['done']) ? 'done' : '' }}">
                            <i class="{{ !empty($step['done']) ? 'fa-solid fa-check' : ($step['icon'] ?? 'fa-regular fa-clock') }}"></i>
                        </div>
                        <div class="step-meta">
                            <div class="step-title">{{ $step['title'] ?? 'Step' }}</div>
                            <div class="step-time">{{ $step['time'] ?? '' }}</div>
                        </div>
                    </div>

                    @if(!$loop->last)
                        <div class="timeline-connector {{ !empty($step['done']) ? 'active' : '' }}"></div>
                    @endif
                @endforeach
            @else
                <div class="timeline-step-item">
                    <div class="step-node-icon done">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="step-meta">
                        <div class="step-title">Draft Created</div>
                        <div class="step-time">24 Aug 2026, 09:45 AM</div>
                    </div>
                </div>

                <div class="timeline-connector active"></div>

                <div class="timeline-step-item">
                    <div class="step-node-icon done">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="step-meta">
                        <div class="step-title">Audience Selected</div>
                        <div class="step-time">24 Aug 2026, 09:55 AM</div>
                    </div>
                </div>

                <div class="timeline-connector active"></div>

                <div class="timeline-step-item">
                    <div class="step-node-icon done">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div class="step-meta">
                        <div class="step-title">Scheduled</div>
                        <div class="step-time">24 Aug 2026, 10:00 AM</div>
                    </div>
                </div>

                <div class="timeline-connector"></div>

                <div class="timeline-step-item">
                    <div class="step-node-icon">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div class="step-meta">
                        <div class="step-title">Awaiting Delivery</div>
                        <div class="step-time">26 Aug 2026, 10:30 AM</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
