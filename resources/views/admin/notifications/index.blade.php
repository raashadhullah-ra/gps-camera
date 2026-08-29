@extends('layout')

@section('title', 'Notifications - GeoCam Admin')
@section('page_title', 'Notifications')

@section('breadcrumbs')
    <span class="text-muted">Engagement</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Notifications</span>
@endsection

@section('content')
<div class="notifications-page">

    {{-- 1. Page Header Row --}}
    <div class="page-header-row">
        <div class="header-title-group">
            <h1 class="page-main-title">Notifications</h1>
            <p class="page-main-subtitle">Create, schedule and track push notification campaigns</p>
        </div>
        <div class="header-actions-group">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Create Notification</span>
            </a>
        </div>
    </div>

    {{-- 2. 5 Top KPI Metric Cards --}}
    <div class="stat-grid-row">
        {{-- Card 1: Total Campaigns --}}
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total Campaigns</div>
                <div class="stat-value">{{ $stats['total']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['total']['change'] }}
                </div>
            </div>
        </div>

        {{-- Card 2: Sent --}}
        <div class="stat-card">
            <div class="stat-icon-wrapper green-bg">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Sent</div>
                <div class="stat-value">{{ $stats['sent']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['sent']['change'] }}
                </div>
            </div>
        </div>

        {{-- Card 3: Scheduled --}}
        <div class="stat-card">
            <div class="stat-icon-wrapper blue-bg">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Scheduled</div>
                <div class="stat-value">{{ $stats['scheduled']['count'] }}</div>
                <div class="stat-trend trend-up">
                    <i class="fa-solid fa-arrow-up fs-10"></i> {{ $stats['scheduled']['change'] }}
                </div>
            </div>
        </div>

        {{-- Card 4: Drafts --}}
        <div class="stat-card">
            <div class="stat-icon-wrapper gray-bg">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Drafts</div>
                <div class="stat-value">{{ $stats['drafts']['count'] }}</div>
                <div class="stat-trend trend-down">
                    <i class="fa-solid fa-arrow-down fs-10"></i> {{ $stats['drafts']['change'] }}
                </div>
            </div>
        </div>

        {{-- Card 5: Failed --}}
        <div class="stat-card">
            <div class="stat-icon-wrapper red-bg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Failed</div>
                <div class="stat-value">{{ $stats['failed']['count'] }}</div>
                <div class="stat-trend trend-down">
                    <i class="fa-solid fa-arrow-down fs-10"></i> {{ $stats['failed']['change'] }}
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Filters Toolbar Card (Exact Installed Devices Page Pattern - Single Horizontal Row) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-2 p-3 bg-white">
        <div class="fs-13 fw-bold text-dark mb-2">Filters</div>
        <form action="{{ route('admin.notifications.index') }}" method="GET" id="notificationsFilterForm" class="d-flex align-items-center gap-2 w-100 flex-nowrap overflow-x-auto">
            <input type="hidden" name="tab" value="{{ request('tab', $tab) }}">

            {{-- 1. Compact Search Box --}}
            <div class="position-relative filter-search-wrap">
                <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-12 filter-search-icon"></i>
                <input type="text" name="search" class="form-control form-control-sm rounded-2 fs-12 filter-search-input" placeholder="Search campaign title or ID" value="{{ request('search') }}">
            </div>

            {{-- 2. Status Dropdown (Bootstrap) --}}
            <div class="dropdown">
                <input type="hidden" name="status" id="filter_status" value="{{ request('status', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-status" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownStatusBtn">
                    <span class="filter-box-label">Status</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_status">{{ ucfirst(request('status', 'All')) }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownStatusBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'All' || !request('status') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'scheduled' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'Scheduled')">Scheduled</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'sent' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'Sent')">Sent</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'draft' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'Draft')">Draft</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('status') == 'failed' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('status', 'Failed')">Failed</a></li>
                </ul>
            </div>

            {{-- 3. Audience Dropdown (Bootstrap) --}}
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
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'Individual Devices' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'Individual Devices')">Individual Devices</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'Audience Segments' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'Audience Segments')">Audience Segments</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('audience') == 'All Installations' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('audience', 'All Installations')">All Installations</a></li>
                </ul>
            </div>

            {{-- 4. Platform Dropdown (Bootstrap) --}}
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
                    <li><a class="dropdown-item py-1 px-3 {{ request('platform') == 'Android' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', 'Android')">Android</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('platform') == 'iOS' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', 'iOS')">iOS</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('platform') == 'Android + iOS' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('platform', 'Android + iOS')">Android + iOS</a></li>
                </ul>
            </div>

            {{-- 5. Created By Dropdown (Bootstrap) --}}
            <div class="dropdown">
                <input type="hidden" name="created_by" id="filter_created_by" value="{{ request('created_by', 'All') }}">
                <button class="btn filter-dropdown-btn w-dropdown-created-by" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownCreatedByBtn">
                    <span class="filter-box-label">Created By</span>
                    <div class="filter-box-val-row">
                        <span class="filter-val-text" id="label_created_by">{{ request('created_by', 'All') }}</span>
                        <i class="fa-solid fa-chevron-down filter-chevron"></i>
                    </div>
                </button>
                <ul class="dropdown-menu shadow-sm border rounded-2 py-1 fs-12 w-dropdown-menu-sm" aria-labelledby="dropdownCreatedByBtn">
                    <li><a class="dropdown-item py-1 px-3 {{ request('created_by') == 'All' || !request('created_by') ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('created_by', 'All')">All</a></li>
                    <li><a class="dropdown-item py-1 px-3 {{ request('created_by') == 'Super Admin' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilterOption('created_by', 'Super Admin')">Super Admin</a></li>
                </ul>
            </div>

            {{-- 6. Sent / Scheduled Date Box --}}
            <div class="filter-date-box w-date-box-md" onclick="document.getElementById('notificationDateRangeInput').focus()">
                <span class="filter-box-label">Sent / Scheduled Date</span>
                <div class="d-flex align-items-center gap-1">
                    <i class="fa-regular fa-calendar text-muted"></i>
                    <input type="text" name="date_range" id="notificationDateRangeInput" class="filter-date-input" placeholder="Select date range" value="{{ request('date_range') }}" readonly>
                </div>
            </div>

            {{-- 7. Reset & Apply Action Buttons --}}
            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Subtext: Campaigns Found Count Bar --}}
    <div class="devices-found-bar">
        <span>{{ number_format($campaigns->total()) }} {{ Str::plural('campaign', $campaigns->total()) }} found</span>
    </div>

    {{-- 4. Main Table Container Card --}}
    <div class="table-container-card">
        <div class="card-header-bar d-flex align-items-center justify-content-between">
            <h2 class="table-title">Notification Campaigns</h2>
            <a href="{{ route('admin.notifications.export') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                <span>Export</span>
            </a>
        </div>

        {{-- Filter Tabs --}}
        <div class="filter-tabs-row">
            <a href="{{ route('admin.notifications.index', ['tab' => 'all'] + request()->except('tab', 'page')) }}" class="filter-tab {{ $tab === 'all' ? 'active' : '' }}">
                All <span class="tab-count">{{ $tabCounts['all'] }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['tab' => 'sent'] + request()->except('tab', 'page')) }}" class="filter-tab {{ $tab === 'sent' ? 'active' : '' }}">
                Sent <span class="tab-count">{{ $tabCounts['sent'] }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['tab' => 'scheduled'] + request()->except('tab', 'page')) }}" class="filter-tab {{ $tab === 'scheduled' ? 'active' : '' }}">
                Scheduled <span class="tab-count">{{ $tabCounts['scheduled'] }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['tab' => 'drafts'] + request()->except('tab', 'page')) }}" class="filter-tab {{ $tab === 'drafts' ? 'active' : '' }}">
                Drafts <span class="tab-count">{{ $tabCounts['drafts'] }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['tab' => 'failed'] + request()->except('tab', 'page')) }}" class="filter-tab {{ $tab === 'failed' ? 'active' : '' }}">
                Failed <span class="tab-count">{{ $tabCounts['failed'] }}</span>
            </a>
        </div>

        {{-- Notifications Table --}}
        <div class="table-responsive">
            <table class="notifications-table">
                <thead>
                    <tr>
                        <th class="th-checkbox">
                            <input type="checkbox" class="form-check-input" id="selectAllNotifications">
                        </th>
                        <th>Campaign</th>
                        <th>Audience</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Delivered</th>
                        <th>Open Rate</th>
                        <th>Created</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $camp)
                        <tr>
                            <td class="td-checkbox">
                                <input type="checkbox" class="form-check-input notification-row-checkbox" value="{{ $camp->id }}">
                            </td>
                            <td>
                                <a href="{{ route('admin.notifications.show', $camp->id) }}" class="campaign-title-text fw-bold text-dark text-decoration-none">
                                    {{ $camp->name ?: $camp->title }}
                                </a>
                                @if($camp->name && $camp->title && $camp->name !== $camp->title)
                                    <div class="fs-11 text-muted">{{ $camp->title }}</div>
                                @endif
                            </td>
                            <td class="text-muted">
                                {{ $camp->audience_label ?: ($camp->audience_type === 'individual' ? 'Individual Devices • ' . $camp->total_audience : 'All Eligible Installations') }}
                            </td>
                            <td>
                                <div class="delivery-info">
                                    @if($camp->status === 'sent')
                                        <i class="fa-solid fa-paper-plane text-success"></i>
                                        <span>{{ $camp->delivery_formatted }}</span>
                                    @elseif($camp->status === 'scheduled')
                                        <i class="fa-regular fa-calendar text-primary"></i>
                                        <span>{{ $camp->delivery_formatted }}</span>
                                    @elseif($camp->status === 'failed')
                                        <i class="fa-solid fa-paper-plane text-danger"></i>
                                        <span>{{ $camp->delivery_formatted }}</span>
                                    @else
                                        <i class="fa-solid fa-ban text-muted"></i>
                                        <span class="text-muted">Not scheduled</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="{{ $camp->status_badge_class }}">{{ ucfirst($camp->status) }}</span>
                            </td>
                            <td class="text-muted">
                                {{ $camp->delivered_formatted }}
                            </td>
                            <td>
                                {{ $camp->open_rate_formatted }}
                            </td>
                            <td class="text-muted">
                                {{ $camp->created_at->format('d M Y') }}
                            </td>
                            <td class="td-actions">
                                <div class="dropdown">
                                    <button class="action-dots-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Campaign Actions">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu">
                                        <li class="dropdown-header-campaign">{{ $camp->name ?: $camp->title }}</li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.notifications.show', $camp->id) }}">
                                                <i class="fa-regular fa-eye text-muted"></i> View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.notifications.edit', $camp->id) }}">
                                                <i class="fa-solid fa-pen text-muted"></i> Edit Notification
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#duplicateNotificationModal{{ $camp->id }}">
                                                <i class="fa-regular fa-copy text-muted"></i> Duplicate
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.segments.index') }}">
                                                <i class="fa-solid fa-users text-muted"></i> View Audience
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rescheduleNotificationModal{{ $camp->id }}">
                                                <i class="fa-regular fa-calendar-days text-muted"></i> Reschedule
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal" data-bs-target="#sendNowNotificationModal{{ $camp->id }}">
                                                <i class="fa-solid fa-paper-plane"></i> Send Now
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.notifications.show', $camp->id) }}">
                                                <i class="fa-solid fa-chart-simple text-muted"></i> Delivery Report
                                            </a>
                                        </li>
                                        <li class="dropdown-divider my-1"></li>
                                        @if($camp->status === 'scheduled')
                                            <li>
                                                <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#cancelScheduleModal{{ $camp->id }}">
                                                    <i class="fa-regular fa-circle-xmark"></i> Cancel Schedule
                                                </button>
                                            </li>
                                        @endif
                                        <li>
                                            <form action="{{ route('admin.notifications.archive', $camp->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fa-solid fa-box-archive text-muted"></i> Archive
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteNotificationModal{{ $camp->id }}">
                                                <i class="fa-regular fa-trash-can"></i> Delete Notification
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL 1: Duplicate Notification Modal --}}
                        <div class="modal fade" id="duplicateNotificationModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notifications.duplicate', $camp->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header modal-header-custom">
                                            <div>
                                                <h5 class="modal-header-title">Duplicate Notification</h5>
                                                <p class="modal-header-subtitle">Create a new draft using this campaign's content and settings.</p>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body modal-body-custom">
                                            {{-- Meta summary pill bar --}}
                                            <div class="meta-pill-bar">
                                                <span><i class="fa-solid fa-bullhorn text-primary me-1"></i> {{ $camp->title }}</span>
                                                <span class="dot-separator">•</span>
                                                <span><i class="fa-regular fa-calendar me-1"></i> {{ ucfirst($camp->status) }}</span>
                                                <span class="dot-separator">•</span>
                                                <span><i class="fa-solid fa-users me-1"></i> {{ $camp->audience_label ?: ($camp->total_audience . ' devices') }}</span>
                                                @if($camp->scheduled_at)
                                                    <span class="dot-separator">•</span>
                                                    <span><i class="fa-regular fa-clock me-1"></i> {{ $camp->scheduled_at->format('d M Y, h:i A') }}</span>
                                                @endif
                                            </div>

                                            {{-- New Campaign Name --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark mb-1">New Campaign Name</label>
                                                <input type="text" name="name" class="form-control form-control-sm" value="Copy of {{ $camp->name ?: $camp->title }}" required>
                                            </div>

                                            {{-- Option Checkboxes --}}
                                            <div class="d-flex flex-column gap-2 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_content" id="copyContent{{ $camp->id }}" value="1" checked>
                                                    <label class="form-check-label text-dark" for="copyContent{{ $camp->id }}">Copy notification content</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_audience" id="copyAudience{{ $camp->id }}" value="1" checked>
                                                    <label class="form-check-label text-dark" for="copyAudience{{ $camp->id }}">Copy audience selection</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="copy_delivery" id="copyDelivery{{ $camp->id }}" value="1" checked>
                                                    <label class="form-check-label text-dark" for="copyDelivery{{ $camp->id }}">Copy delivery settings</label>
                                                </div>
                                            </div>

                                            {{-- Info Alert --}}
                                            <div class="alert-blue-light">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>The duplicate will be saved as a draft. It will not be sent or scheduled automatically.</span>
                                            </div>

                                            {{-- Duplicate Preview Card --}}
                                            <div class="duplicate-preview-box">
                                                <div class="preview-box-title">Duplicate Preview</div>
                                                <div class="row g-2 text-start">
                                                    <div class="col-4">
                                                        <div class="preview-item-label">Title</div>
                                                        <div class="preview-item-val">{{ $camp->title }}</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="preview-item-label">Audience</div>
                                                        <div class="preview-item-val">{{ $camp->total_audience ?: 3 }} devices</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="preview-item-label">Delivery</div>
                                                        <div class="preview-item-val">Schedule copied</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-custom">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Create Duplicate</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL 2: Reschedule Notification Modal --}}
                        <div class="modal fade" id="rescheduleNotificationModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notifications.reschedule', $camp->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header modal-header-custom">
                                            <div>
                                                <h5 class="modal-header-title">Reschedule Notification</h5>
                                                <p class="modal-header-subtitle">Choose a new delivery date and time for {{ $camp->title }}.</p>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body modal-body-custom">
                                            @php
                                                $reschedTz = $camp->time_zone ?: 'Asia/Kolkata';
                                                $localSched = $camp->scheduled_at ? $camp->scheduled_at->timezone($reschedTz) : null;
                                            @endphp
                                            {{-- Current Schedule Alert --}}
                                            <div class="alert-blue-light">
                                                <i class="fa-regular fa-calendar-days text-primary"></i>
                                                <span>Current schedule: {{ $localSched ? $localSched->format('d M Y \a\t h:i A') . ' ' . ($reschedTz === 'Asia/Kolkata' ? 'IST' : $reschedTz) : 'Not scheduled' }}</span>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-4">
                                                    <label class="form-label fw-bold text-dark mb-1">New Delivery Date</label>
                                                    <input type="date" name="delivery_date" class="form-control form-control-sm" value="{{ $localSched ? $localSched->format('Y-m-d') : date('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-bold text-dark mb-1">New Delivery Time</label>
                                                    <input type="time" name="delivery_time" class="form-control form-control-sm" value="{{ $localSched ? $localSched->format('H:i') : '09:00' }}" required>
                                                </div>
                                                <div class="col-4">
                                                    <label class="form-label fw-bold text-dark mb-1">Time Zone</label>
                                                    <select name="time_zone" class="form-select form-select-sm">
                                                        <option value="Asia/Kolkata" {{ $reschedTz === 'Asia/Kolkata' ? 'selected' : '' }}>IST - India Standard Time (Asia/Kolkata, UTC+05:30)</option>
                                                        <option value="Asia/Dubai" {{ $reschedTz === 'Asia/Dubai' ? 'selected' : '' }}>GST - Gulf Standard Time (Dubai/UAE, UTC+04:00)</option>
                                                        <option value="UTC" {{ $reschedTz === 'UTC' ? 'selected' : '' }}>UTC - Universal Coordinated Time (UTC+00:00)</option>
                                                        <option value="America/New_York" {{ $reschedTz === 'America/New_York' ? 'selected' : '' }}>EST - Eastern Standard Time (New York, UTC-05:00)</option>
                                                        <option value="America/Los_Angeles" {{ $reschedTz === 'America/Los_Angeles' ? 'selected' : '' }}>PST - Pacific Standard Time (Los Angeles, UTC-08:00)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Quiet Hours Toggle --}}
                                            <div class="quiet-hours-row">
                                                <div>
                                                    <div class="quiet-hours-title">Respect quiet hours</div>
                                                    <div class="quiet-hours-subtitle">Avoid delivery between 10:00 PM and 8:00 AM.</div>
                                                </div>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" name="quiet_hours" value="1" id="quietHoursSwitch{{ $camp->id }}" checked>
                                                </div>
                                            </div>

                                            {{-- Preview Info Box --}}
                                            <div class="scheduled-delivery-preview-bar">
                                                <i class="fa-regular fa-calendar-check"></i>
                                                <span>New scheduled delivery: <strong>Friday, 28 August 2026 at 09:00 AM IST</strong></span>
                                            </div>

                                            {{-- Info Alert --}}
                                            <div class="alert-blue-light">
                                                <i class="fa-solid fa-circle-info"></i>
                                                <span>The audience and notification content will remain unchanged.</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-custom">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Update Schedule</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL 3: Send Notification Now Modal --}}
                        <div class="modal fade" id="sendNowNotificationModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notifications.send-now', $camp->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header modal-header-custom">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="modal-icon-header-circle icon-blue">
                                                    <i class="fa-solid fa-bolt"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-header-title">Send Notification Now</h5>
                                                    <p class="modal-header-subtitle">Send <strong class="text-primary">{{ $camp->title }}</strong> immediately instead of the scheduled time?</p>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body modal-body-custom">
                                            {{-- Transformation Card --}}
                                            <div class="schedule-comparison-card">
                                                <div class="schedule-box-node">
                                                    <div class="node-icon-square">
                                                        <i class="fa-regular fa-calendar"></i>
                                                    </div>
                                                    <div>
                                                        <div class="node-label">Current Schedule</div>
                                                        <div class="node-value">{{ $camp->scheduled_at?->format('d M Y, h:i A') ?? '26 Aug 2026, 10:30 AM' }} IST</div>
                                                    </div>
                                                </div>

                                                <i class="fa-solid fa-arrow-right text-muted mx-2"></i>

                                                <div class="schedule-box-node">
                                                    <div class="node-icon-square">
                                                        <i class="fa-solid fa-bolt"></i>
                                                    </div>
                                                    <div>
                                                        <div class="node-label text-primary-label">New Delivery</div>
                                                        <div class="node-value">Immediately after confirmation</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Spec List --}}
                                            <div class="modal-spec-list">
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">
                                                        <i class="fa-solid fa-users"></i> Audience
                                                    </span>
                                                    <span class="modal-spec-val">{{ $camp->audience_type === 'individual' ? 'Individual Devices' : ($camp->audience_label ?: 'All Devices') }}</span>
                                                </div>
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">
                                                        <i class="fa-regular fa-calendar"></i> Eligible Devices
                                                    </span>
                                                    <span class="modal-spec-val">{{ $camp->total_audience ?: 3 }} eligible devices</span>
                                                </div>
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">
                                                        <i class="fa-solid fa-mobile-screen"></i> Platform Breakdown
                                                    </span>
                                                    <span class="modal-spec-val">Android {{ $camp->android_count ?: 2 }} • iOS {{ $camp->ios_count ?: 1 }}</span>
                                                </div>
                                            </div>

                                            {{-- Amber Warning Alert --}}
                                            <div class="alert-amber-light">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                                <span>This will cancel the existing schedule and queue the notification for immediate delivery. Sending cannot be undone.</span>
                                            </div>

                                            {{-- Checkbox --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="confirmSendNowCheck{{ $camp->id }}" required onchange="document.getElementById('sendNowSubmitBtn{{ $camp->id }}').disabled = !this.checked;">
                                                <label class="form-check-label text-dark fw-semibold" for="confirmSendNowCheck{{ $camp->id }}">
                                                    I understand this notification will be sent immediately.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-custom space-between">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-dismiss="modal">Keep Scheduled</button>
                                            <button type="submit" id="sendNowSubmitBtn{{ $camp->id }}" class="btn btn-sm btn-primary" disabled>Send Now</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL 4: Cancel Scheduled Notification Modal (Matches User Screenshot 2) --}}
                        <div class="modal fade" id="cancelScheduleModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notifications.cancel-schedule', $camp->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header modal-header-custom">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="modal-icon-header-circle icon-amber">
                                                    <i class="fa-regular fa-calendar-xmark"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-header-title">Cancel Scheduled Notification</h5>
                                                    <p class="modal-header-subtitle">Cancel the scheduled delivery for {{ $camp->title }}?</p>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body modal-body-custom">
                                            {{-- Summary Details Box --}}
                                            <div class="cancel-summary-box">
                                                <div class="cancel-summary-row">
                                                    <i class="fa-regular fa-calendar"></i>
                                                    <div>
                                                        <div class="summary-meta-label">Scheduled for</div>
                                                        <div class="summary-meta-val">{{ $camp->scheduled_at?->format('d M Y \a\t h:i A') ?? '26 Aug 2026 at 10:30 AM' }} IST</div>
                                                    </div>
                                                </div>
                                                <div class="cancel-summary-row">
                                                    <i class="fa-solid fa-users"></i>
                                                    <div>
                                                        <div class="summary-meta-label">Audience</div>
                                                        <div class="summary-meta-val">{{ $camp->audience_type === 'individual' ? 'Individual Devices · ' . ($camp->total_audience ?: 3) : ($camp->audience_label ?: 'All Devices') }}</div>
                                                        <div class="summary-meta-sub">Android {{ $camp->android_count ?: 2 }}, iOS {{ $camp->ios_count ?: 1 }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- After cancellation Radio Selection --}}
                                            <div class="cancellation-radio-group">
                                                <div class="radio-group-title">After cancellation</div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="after_cancellation" id="cancelMoveDraft{{ $camp->id }}" value="draft" checked>
                                                    <label class="form-check-label" for="cancelMoveDraft{{ $camp->id }}">
                                                        Move to Drafts
                                                        <span class="radio-subtext">Keep the notification so it can be edited or scheduled again</span>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="after_cancellation" id="cancelMoveArchive{{ $camp->id }}" value="archive">
                                                    <label class="form-check-label" for="cancelMoveArchive{{ $camp->id }}">
                                                        Archive Notification
                                                        <span class="radio-subtext">Remove it from active campaign lists.</span>
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Reason for cancellation --}}
                                            <div class="mb-3">
                                                <label class="form-label text-muted fw-semibold mb-1 fs-11">Reason for cancellation (optional)</label>
                                                <textarea name="cancel_reason" rows="2" class="form-control form-control-sm" placeholder="Enter a reason (optional)"></textarea>
                                            </div>

                                            {{-- Amber Alert --}}
                                            <div class="alert-amber-light">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                                <span>The notification will not be delivered at the scheduled time.</span>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-custom space-between">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Keep Schedule</button>
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel Schedule</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL 5: Delete Notification Modal (Matches User Screenshot 3) --}}
                        <div class="modal fade" id="deleteNotificationModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notifications.destroy', $camp->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header modal-header-custom">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="modal-icon-header-circle icon-red">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </div>
                                                <div>
                                                    <h5 class="modal-header-title">Delete Notification</h5>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body modal-body-custom">
                                            <p class="text-dark fw-semibold mb-2">Permanently delete <strong>{{ $camp->title }}</strong>?</p>

                                            {{-- Red Alert Warning Box --}}
                                            <div class="alert-danger-light">
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                                <span>This action cannot be undone. The scheduled delivery will be cancelled and the campaign record will be permanently removed.</span>
                                            </div>

                                            {{-- Spec Table --}}
                                            <div class="modal-spec-list">
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">Campaign ID</span>
                                                    <span class="modal-spec-val font-monospace">{{ $camp->campaign_id }}</span>
                                                </div>
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">Status</span>
                                                    <span class="modal-spec-val">
                                                        <span class="{{ $camp->status_badge_class }}">{{ ucfirst($camp->status) }}</span>
                                                    </span>
                                                </div>
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">Audience</span>
                                                    <span class="modal-spec-val">{{ $camp->total_audience ?: 3 }} devices</span>
                                                </div>
                                                <div class="modal-spec-row">
                                                    <span class="modal-spec-label">Delivery</span>
                                                    <span class="modal-spec-val">{{ $camp->scheduled_at?->format('d M Y \a\t h:i A') ?? ($camp->sent_at?->format('d M Y \a\t h:i A') ?? 'Not scheduled') }} IST</span>
                                                </div>
                                            </div>

                                            {{-- Confirmation Input "DELETE" --}}
                                            <div class="mb-3">
                                                <label class="form-label text-dark fw-bold mb-1 fs-11" for="deleteConfirmInput{{ $camp->id }}">
                                                    Type <span class="text-dark fw-bold">DELETE</span> to confirm
                                                </label>
                                                <div class="delete-confirm-wrap">
                                                    <input type="text" id="deleteConfirmInput{{ $camp->id }}" class="form-control form-control-sm delete-confirm-input" placeholder="DELETE" autocomplete="off" oninput="checkDeleteSafety({{ $camp->id }})">
                                                    <i class="fa-solid fa-check delete-check-icon" id="deleteCheckIcon{{ $camp->id }}"></i>
                                                </div>
                                            </div>

                                            {{-- Checkbox --}}
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="deleteConfirmCheckbox{{ $camp->id }}" onchange="checkDeleteSafety({{ $camp->id }})">
                                                <label class="form-check-label text-dark fw-semibold" for="deleteConfirmCheckbox{{ $camp->id }}" style="font-size: 11.5px;">
                                                    I understand this notification and its schedule will be permanently deleted.
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-custom">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" id="deleteSubmitBtn{{ $camp->id }}" class="btn btn-sm btn-danger" disabled>Delete Permanently</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-regular fa-bell-slash fs-1 mb-2 d-block text-secondary"></i>
                                No notification campaigns found matching the filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer & Custom Pagination --}}
        <div class="table-footer-pagination">
            <div>
                Showing {{ $campaigns->firstItem() ?? 0 }} to {{ $campaigns->lastItem() ?? 0 }} of {{ number_format($campaigns->total()) }} campaigns
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-12 text-muted">Per page:</span>
                    <select name="per_page" class="form-select form-select-sm per-page-select" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <!-- Custom Dynamic Pagination -->
                <div class="custom-pagination">
                    {{-- Previous Page Link --}}
                    @if ($campaigns->onFirstPage())
                        <button type="button" class="page-btn prev-btn" disabled aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $campaigns->previousPageUrl() }}" class="page-btn prev-btn" aria-label="Previous Page">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $curPage = $campaigns->currentPage();
                        $lastPage = $campaigns->lastPage();
                    @endphp

                    @if ($lastPage <= 7)
                        @for ($page = 1; $page <= $lastPage; $page++)
                            @if ($page == $curPage)
                                <button type="button" class="page-btn page-num active">{{ $page }}</button>
                            @else
                                <a href="{{ $campaigns->url($page) }}" class="page-btn page-num">{{ $page }}</a>
                            @endif
                        @endfor
                    @else
                        {{-- 1st Page --}}
                        @if ($curPage == 1)
                            <button type="button" class="page-btn page-num active">1</button>
                        @else
                            <a href="{{ $campaigns->url(1) }}" class="page-btn page-num">1</a>
                        @endif

                        @if ($curPage > 3)
                            <span class="page-ellipsis">...</span>
                        @endif

                        {{-- Middle Pages --}}
                        @for ($page = max(2, $curPage - 1); $page <= min($lastPage - 1, $curPage + 1); $page++)
                            @if ($page == $curPage)
                                <button type="button" class="page-btn page-num active">{{ $page }}</button>
                            @else
                                <a href="{{ $campaigns->url($page) }}" class="page-btn page-num">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($curPage < $lastPage - 2)
                            <span class="page-ellipsis">...</span>
                        @endif

                        {{-- Last Page --}}
                        @if ($curPage == $lastPage)
                            <button type="button" class="page-btn page-num active">{{ $lastPage }}</button>
                        @else
                            <a href="{{ $campaigns->url($lastPage) }}" class="page-btn page-num">{{ $lastPage }}</a>
                        @endif
                    @endif

                    {{-- Next Page Link --}}
                    @if ($campaigns->hasMorePages())
                        <a href="{{ $campaigns->nextPageUrl() }}" class="page-btn next-btn" aria-label="Next Page">
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
@endsection

@push('scripts')
<script>
function changePerPage(perPage) {
    const form = document.getElementById('notificationsFilterForm');
    if (!form) return;
    let input = form.querySelector('input[name="per_page"]');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'per_page';
        form.appendChild(input);
    }
    input.value = perPage;
    form.submit();
}

function setFilterOption(field, value) {
    const input = document.getElementById('filter_' + field);
    const label = document.getElementById('label_' + field);
    if (input) input.value = value;
    if (label) label.textContent = value;
}

// Safety check for Delete modal confirmation input & checkbox
function checkDeleteSafety(id) {
    const input = document.getElementById('deleteConfirmInput' + id);
    const icon = document.getElementById('deleteCheckIcon' + id);
    const check = document.getElementById('deleteConfirmCheckbox' + id);
    const btn = document.getElementById('deleteSubmitBtn' + id);

    if (!input || !check || !btn) return;

    const isMatch = (input.value.trim() === 'DELETE');
    if (icon) {
        if (isMatch) {
            icon.classList.add('show');
            input.classList.add('is-valid-delete');
        } else {
            icon.classList.remove('show');
            input.classList.remove('is-valid-delete');
        }
    }

    btn.disabled = !(isMatch && check.checked);
}

document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes toggle
    const selectAll = document.getElementById('selectAllNotifications');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.notification-row-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
        });
    }

    // Flatpickr Range for Sent/Scheduled Date
    if (typeof flatpickr !== 'undefined' && document.getElementById('notificationDateRangeInput')) {
        flatpickr('#notificationDateRangeInput', {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            placeholder: 'Sent / Scheduled Date',
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById('notificationsFilterForm').submit();
                }
            }
        });
    }
});
</script>
@endpush
