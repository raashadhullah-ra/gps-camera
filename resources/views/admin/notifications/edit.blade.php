@extends('layout')

@section('title', 'Edit Notification - ' . ($campaign->name ?: $campaign->title) . ' - GeoCam Admin')
@section('page_title', 'Edit Notification')

@section('breadcrumbs')
    <span class="text-muted">Engagement</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.notifications.index') }}" class="text-muted text-decoration-none">Notifications</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.notifications.show', $campaign->id) }}" class="text-muted text-decoration-none">{{ $campaign->name ?: 'Notification' }}</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Edit Notification</span>
@endsection

@section('content')
@php
    $schedTz = $campaign->time_zone ?: 'Asia/Kolkata';
    $localScheduledAt = $campaign->scheduled_at ? $campaign->scheduled_at->timezone($schedTz) : null;
@endphp
<div class="create-notification-page">
    <form action="{{ route('admin.notifications.update', $campaign->id) }}" method="POST" id="editNotificationForm" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="campaignStatusInput" value="{{ $campaign->status ?? 'draft' }}">
        <input type="hidden" name="audience_type" id="selectedAudienceTypeInput" value="{{ $campaign->audience_type ?? 'individual' }}">
        <input type="hidden" name="audience_label" id="selectedAudienceLabelInput" value="{{ $campaign->audience_label ?? 'Individual Devices' }}">
        <input type="hidden" name="total_audience" id="hiddenTotalAudience" value="{{ $campaign->total_audience ?? 3 }}">
        <input type="hidden" name="android_count" id="hiddenAndroidCount" value="{{ $campaign->android_count ?? 2 }}">
        <input type="hidden" name="ios_count" id="hiddenIosCount" value="{{ $campaign->ios_count ?? 1 }}">
        <input type="hidden" name="delivery_schedule_mode" id="selectedScheduleModeInput" value="{{ $campaign->status === 'sent' ? 'now' : 'schedule' }}">
        <input type="hidden" name="remove_image" id="hiddenRemoveImage" value="0">

        {{-- 1. Top Header Bar --}}
        <div class="wizard-header-row">
            <div class="header-left-group">
                <a href="{{ route('admin.notifications.show', $campaign->id) }}" class="btn-back-link" title="Back to Notification Details">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="wizard-main-title" id="wizardHeaderTitle">Edit Notification</h1>
                    <p class="wizard-subtitle" id="wizardHeaderSubtitle">Update and adjust push notification settings and delivery details</p>
                </div>
            </div>
            <div class="header-right-actions">
                <a href="{{ route('admin.notifications.show', $campaign->id) }}" class="btn btn-outline-secondary btn-sm" id="topCancelLink" style="display: none;">
                    Cancel
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="topSaveDraftBtn" onclick="saveAsDraft()">
                    Save as Draft
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="topSendTestBtn" data-bs-toggle="modal" data-bs-target="#sendTestNotificationModal">
                    Send Test
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="topActionBtn" onclick="nextWizardStep()">
                    Review & Save
                </button>
            </div>
        </div>

        {{-- 2. 4-Step Stepper Bar --}}
        <div class="wizard-stepper-container">
            <div class="stepper-nav">
                {{-- Step 1 --}}
                <div class="stepper-step active" id="stepNode1" onclick="goToStep(1)">
                    <div class="step-circle" id="stepCircle1">1</div>
                    <span class="step-label" id="stepLabel1">1 Content</span>
                </div>
                <div class="stepper-line" id="stepLine1"></div>

                {{-- Step 2 --}}
                <div class="stepper-step" id="stepNode2" onclick="goToStep(2)">
                    <div class="step-circle" id="stepCircle2">2</div>
                    <span class="step-label" id="stepLabel2">Audience</span>
                </div>
                <div class="stepper-line" id="stepLine2"></div>

                {{-- Step 3 --}}
                <div class="stepper-step" id="stepNode3" onclick="goToStep(3)">
                    <div class="step-circle" id="stepCircle3">3</div>
                    <span class="step-label" id="stepLabel3">Schedule</span>
                </div>
                <div class="stepper-line" id="stepLine3"></div>

                {{-- Step 4 --}}
                <div class="stepper-step" id="stepNode4" onclick="goToStep(4)">
                    <div class="step-circle" id="stepCircle4">4</div>
                    <span class="step-label" id="stepLabel4">Review & Save</span>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 1: CONTENT SCREEN --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel active" id="stepPanel1">
            <div class="row g-3">
                {{-- Left Column: Form Cards --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- Card 1: Campaign & Content --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Campaign & Content</div>
                        
                        {{-- Campaign Name & Notification Title --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fs-12 mb-1" for="inputCampaignName">Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="inputCampaignName" class="form-control form-control-sm" placeholder="e.g. GPS Camera Feature Update" value="{{ old('name', $campaign->name) }}" oninput="clearValidation(this); syncAllWizardFields()">
                                <div class="invalid-feedback">Campaign name is required.</div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label text-dark fs-12 mb-0" for="inputNotificationTitle">Notification Title <span class="text-danger">*</span></label>
                                    <span class="char-counter-text" id="titleCharCount">{{ strlen($campaign->title ?? '') }} / 100</span>
                                </div>
                                <input type="text" name="title" id="inputNotificationTitle" class="form-control form-control-sm" maxlength="100" placeholder="Notification Title" value="{{ old('title', $campaign->title) }}" oninput="clearValidation(this); updateLivePreview()">
                                <div class="invalid-feedback">Notification title is required.</div>
                            </div>
                        </div>

                        {{-- Message Body & Image Upload Dropzone --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label text-dark fs-12 mb-0" for="inputNotificationMessage">Message <span class="text-danger">*</span></label>
                                    <span class="char-counter-text" id="msgCharCount">{{ strlen($campaign->message ?? '') }} / 200</span>
                                </div>
                                <textarea name="message" id="inputNotificationMessage" class="form-control form-control-sm notif-message-textarea" maxlength="200" placeholder="Type notification body..." oninput="clearValidation(this); updateLivePreview()">{{ old('message', $campaign->message) }}</textarea>
                                <div class="invalid-feedback">Message is required.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fs-12 mb-1">Notification Image <span class="text-muted">(Optional • Max 2 MB)</span></label>
                                <div class="image-upload-dropzone position-relative" id="dropzoneContainer" onclick="handleDropzoneClick(event)">
                                    <div id="dropzoneEmptyState" class="{{ $campaign->image_url ? 'd-none' : '' }}">
                                        <i class="fa-solid fa-arrow-up-from-bracket dropzone-icon"></i>
                                        <div class="dropzone-title">Upload PNG or JPG</div>
                                        <div class="dropzone-sub">Max 2 MB • Recommended 1200 × 628 px</div>
                                    </div>
                                    <div id="dropzonePreviewState" class="dropzone-preview-wrap {{ $campaign->image_url ? '' : 'd-none' }}">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <img id="dropzoneThumb" src="{{ $campaign->image_url ?: '' }}" alt="Thumbnail" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                                            <div class="text-start overflow-hidden">
                                                <div class="fs-12 fw-bold text-dark text-truncate" id="dropzoneFileName" style="max-width: 140px;">{{ $campaign->image_url ? basename($campaign->image_url) : 'image.png' }}</div>
                                                <div class="fs-10 text-muted" id="dropzoneFileSize">{{ $campaign->image_url ? 'Attached Image' : '1.2 MB' }}</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2 fs-11" id="btnRemoveImage" onclick="removeSelectedImage(event)" title="Remove image">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                    <input type="file" name="image" id="notifImageInput" class="d-none" accept="image/png, image/jpeg, image/jpg" onchange="handleImageSelected(this)">
                                </div>
                                <div class="text-danger fs-11 mt-1 d-none" id="imageValidationFeedback"></div>
                            </div>
                        </div>

                        {{-- Action & Deep Link --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fs-12 mb-1" for="inputAction">On Tap Action</label>
                                <select name="action" id="inputAction" class="form-select form-select-sm" onchange="syncAllWizardFields()">
                                    <option value="open_app" {{ ($campaign->action ?? 'open_app') === 'open_app' ? 'selected' : '' }}>Open App</option>
                                    <option value="deep_link" {{ ($campaign->action ?? '') === 'deep_link' ? 'selected' : '' }}>Deep Link</option>
                                    <option value="open_url" {{ ($campaign->action ?? '') === 'open_url' ? 'selected' : '' }}>Open URL</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fs-12 mb-1">Deep Link <span class="text-muted">(Optional)</span></label>
                                <select name="deep_link" class="form-select form-select-sm">
                                    <option value="dashboard" {{ ($campaign->action_url ?? 'dashboard') === 'dashboard' ? 'selected' : '' }}>Dashboard</option>
                                    <option value="camera_preview" {{ ($campaign->action_url ?? '') === 'camera_preview' ? 'selected' : '' }}>Camera Preview</option>
                                    <option value="stamp_settings" {{ ($campaign->action_url ?? '') === 'stamp_settings' ? 'selected' : '' }}>Stamp Settings</option>
                                    <option value="location_map" {{ ($campaign->action_url ?? '') === 'location_map' ? 'selected' : '' }}>Location Map</option>
                                </select>
                            </div>
                        </div>

                        {{-- Advanced Data Payload (Accordion) --}}
                        <div class="payload-accordion-container" id="accordionAdvancedPayloadWrap">
                            <div class="accordion accordion-flush" id="accordionAdvancedPayload">
                                <div class="accordion-item border-0 bg-transparent">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0 py-1 fs-12 text-dark shadow-none bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayload" aria-expanded="false" aria-controls="collapsePayload">
                                            <span class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-code text-primary fs-11"></i>
                                                <span>Advanced Data Payload</span>
                                                <span class="badge bg-secondary-subtle text-secondary fs-10 fw-normal">JSON</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapsePayload" class="accordion-collapse collapse">
                                        <div class="accordion-body px-0 py-2">
                                            <div class="fs-11 text-muted mb-1">Custom key-value parameters sent silently to the app for deep-linking, background triggers or in-app routing.</div>
                                            <textarea name="custom_payload" rows="2" class="form-control form-control-sm font-monospace payload-textarea fs-11" placeholder='{"screen": "camera_settings", "promo_id": "AUG2026"}'>{{ old('custom_payload', $campaign->custom_payload) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Live Preview & Campaign Checklist --}}
                <div class="col-lg-5 col-xl-4">
                    {{-- 1. Live Smartphone Preview --}}
                    <div class="smartphone-preview-container">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 fw-bold text-dark">Live Preview</span>
                        </div>

                        <div class="preview-tabs-row">
                            <button type="button" class="preview-tab-btn active" id="btnPreviewAndroid" onclick="switchPreviewPlatform('android')">Android</button>
                            <button type="button" class="preview-tab-btn" id="btnPreviewIos" onclick="switchPreviewPlatform('ios')">iOS</button>
                        </div>

                        <div class="phone-screen-box">
                            <div class="phone-top-bar">
                                <span class="phone-clock">10:30</span>
                                <span>Mon, Aug 19</span>
                                <div>
                                    <i class="fa-solid fa-wifi me-1"></i>
                                    <i class="fa-solid fa-battery-full"></i>
                                </div>
                            </div>

                            <div class="phone-notification-card">
                                <div class="notif-card-header">
                                    <div class="review-app-logo-box" style="width: 18px; height: 18px; font-size: 10px; border-radius: 4px;">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <span class="app-name">GPS Camera</span>
                                    <span class="notif-time">Now</span>
                                </div>
                                <div class="notif-card-title" id="previewCardTitle">{{ $campaign->title ?: 'GPS Camera Update' }}</div>
                                <div class="notif-card-body" id="previewCardBody">{{ $campaign->message ?: 'Notification message...' }}</div>
                                <div id="previewCardImageWrap" class="notif-card-banner {{ $campaign->image_url ? '' : 'd-none' }} mt-2">
                                    <img id="previewCardImgElem" src="{{ $campaign->image_url ?: '' }}" alt="Notification banner" class="img-fluid rounded-2" style="max-height: 120px; width: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Campaign Checklist --}}
                    <div class="checklist-card">
                        <div class="fs-12 fw-bold text-dark mb-2">Campaign Checklist</div>
                        <div class="checklist-item">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>Content completed</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>Audience selected</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>Delivery configured</span>
                        </div>
                        <div class="checklist-item">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span>Open tracking enabled</span>
                        </div>
                        <div class="alert-blue-light mb-0 mt-2">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>You can review all settings before saving.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 2: AUDIENCE SCREEN --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel2">
            <div class="row g-3">
                {{-- Left Column: Audience Selection --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Select Audience Type Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Select Audience</div>
                        <div class="card-section-subtitle">Choose one audience type.</div>

                        {{-- Option 1: All Eligible Installations --}}
                        <div class="audience-option-card {{ ($campaign->audience_type ?? '') === 'all' ? 'selected' : '' }}" id="cardAudienceAll" onclick="selectAudienceMode('all')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceAll" value="all" {{ ($campaign->audience_type ?? '') === 'all' ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-globe"></i>
                                </div>
                                <div>
                                    <div class="option-title">All Eligible Installations</div>
                                    <div class="option-desc">Send to every active device with notification permission</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeAllCount">{{ number_format($audienceMetrics['all']['deliverable'] ?? 7054) }}</div>
                        </div>

                        {{-- Option 2: Audience Segment --}}
                        <div class="audience-option-card {{ ($campaign->audience_type ?? '') === 'segment' ? 'selected' : '' }}" id="cardAudienceSegment" onclick="selectAudienceMode('segment')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceSegment" value="segment" {{ ($campaign->audience_type ?? '') === 'segment' ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="option-title">Audience Segment</div>
                                    <div class="option-desc">Send to a saved group of installations</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeSegmentCount">{{ number_format($segments->first()?->deliverable_count ?? 6842) }}</div>
                        </div>

                        {{-- Option 3: Location --}}
                        <div class="audience-option-card {{ ($campaign->audience_type ?? '') === 'location' ? 'selected' : '' }}" id="cardAudienceLocation" onclick="selectAudienceMode('location')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceLocation" value="location" {{ ($campaign->audience_type ?? '') === 'location' ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="option-title">Location</div>
                                    <div class="option-desc">Target devices from a selected location</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeLocationCount">{{ number_format($locations->first()?->active_devices ?? 6292) }}</div>
                        </div>

                        {{-- Option 4: Individual Devices (Default) --}}
                        <div class="audience-option-card {{ (!in_array($campaign->audience_type ?? 'individual', ['all', 'segment', 'location'])) ? 'selected' : '' }}" id="cardAudienceIndividual" onclick="selectAudienceMode('individual')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceIndividual" value="individual" {{ (!in_array($campaign->audience_type ?? 'individual', ['all', 'segment', 'location'])) ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                </div>
                                <div>
                                    <div class="option-title">Individual Devices</div>
                                    <div class="option-desc">Select specific installations</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeIndividualCount">{{ $campaign->total_audience ?? 3 }}</div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 1: Audience Segment Sub-Panel --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 {{ ($campaign->audience_type ?? '') === 'segment' ? '' : 'd-none' }}" id="subpanelSegment">
                            <label class="form-label text-dark fs-12 mb-1" for="segmentSelectDropdown">Audience Segment</label>
                            <select name="segment_select_id" id="segmentSelectDropdown" class="form-select form-select-sm mb-3" onchange="onSegmentDropdownChange(this.value)">
                                @forelse($segments as $seg)
                                    <option value="{{ $seg->id }}" {{ ($campaign->segment_id == $seg->id || ($loop->first && !$campaign->segment_id)) ? 'selected' : '' }}>{{ $seg->name }}</option>
                                @empty
                                    <option value="1">Active Users – Tirunelveli</option>
                                @endforelse
                            </select>

                            <div class="subpanel-header-title" id="segSubpanelTitle">{{ $campaign->segment?->name ?? ($segments->first()?->name ?? 'Active Users – Tirunelveli') }}</div>
                            <div class="meta-tags-row" id="segSubpanelTags">
                                <span class="tag-pill"><i class="fa-solid fa-location-dot text-primary"></i> <span id="segTagCity">Tirunelveli</span></span>
                                <span class="tag-pill green"><i class="fa-solid fa-circle fs-6"></i> Active</span>
                                <span class="tag-pill"><i class="fa-regular fa-calendar"></i> Last 30 Days</span>
                                <a href="{{ route('admin.segments.index') }}" class="view-link">
                                    View Segment Details <i class="fa-solid fa-arrow-up-right-from-square fs-10"></i>
                                </a>
                            </div>

                            <div class="three-stat-columns">
                                <div class="stat-col">
                                    <div class="stat-col-val" id="segStatAudienceSize">{{ number_format($campaign->segment?->audience_size ?? ($segments->first()?->audience_size ?? 7054)) }}</div>
                                    <div class="stat-col-lbl">Devices</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-success" id="segStatDeliverable">{{ number_format($campaign->segment?->deliverable_count ?? ($segments->first()?->deliverable_count ?? 6842)) }}</div>
                                    <div class="stat-col-lbl">Deliverable</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-muted" id="segStatExcluded">{{ number_format($campaign->segment?->excluded_count ?? ($segments->first()?->excluded_count ?? 212)) }}</div>
                                    <div class="stat-col-lbl">Excluded</div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 2: Location Sub-Panel --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 {{ ($campaign->audience_type ?? '') === 'location' ? '' : 'd-none' }}" id="subpanelLocation">
                            <div class="subpanel-header-title mb-2">Select Location</div>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <label class="form-label text-dark fs-11 mb-1" for="locCountrySelect">Country</label>
                                    <select name="loc_country" id="locCountrySelect" class="form-select form-select-sm" onchange="onLocationCountryChange(this.value)">
                                        @php
                                            $countries = $locations->pluck('country')->unique()->filter();
                                        @endphp
                                        @forelse($countries as $c)
                                            <option value="{{ $c }}" {{ $loop->first ? 'selected' : '' }}>{{ $c }}</option>
                                        @empty
                                            <option value="India" selected>India</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-dark fs-11 mb-1" for="locStateSelect">State / Region</label>
                                    <select name="loc_state" id="locStateSelect" class="form-select form-select-sm" onchange="onLocationStateChange(this.value)">
                                        @php
                                            $states = $locations->pluck('state')->unique()->filter();
                                        @endphp
                                        @forelse($states as $st)
                                            <option value="{{ $st }}" {{ $loop->first ? 'selected' : '' }}>{{ $st }}</option>
                                        @empty
                                            <option value="Tamil Nadu" selected>Tamil Nadu</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-dark fs-11 mb-1" for="locCitySelect">City</label>
                                    <select name="loc_city" id="locCitySelect" class="form-select form-select-sm" onchange="onLocationCityChange(this.value)">
                                        @forelse($locations as $loc)
                                            <option value="{{ $loc->city }}" {{ $loop->first ? 'selected' : '' }}>{{ $loc->city }}</option>
                                        @empty
                                            <option value="Tirunelveli" selected>Tirunelveli</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="subpanel-header-title" id="locDisplayCity">{{ $locations->first()?->city ?? 'Tirunelveli' }}</div>
                            <div class="meta-tags-row">
                                <span class="tag-pill"><i class="fa-solid fa-map-pin text-primary"></i> <span id="locDisplayRegion">{{ $locations->first()?->state ?? 'Tamil Nadu' }}, India</span></span>
                                <span class="tag-pill green"><i class="fa-solid fa-circle fs-6"></i> High Density</span>
                            </div>

                            <div class="three-stat-columns">
                                <div class="stat-col">
                                    <div class="stat-col-val" id="locStatDevices">{{ number_format($locations->first()?->total_installations ?? 6488) }}</div>
                                    <div class="stat-col-lbl">Installations</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-success" id="locStatDeliverable">{{ number_format($locations->first()?->active_devices ?? 6292) }}</div>
                                    <div class="stat-col-lbl">Deliverable</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-muted" id="locStatExcluded">{{ number_format(max(0, ($locations->first()?->total_installations ?? 6488) - ($locations->first()?->active_devices ?? 6292))) }}</div>
                                    <div class="stat-col-lbl">Excluded</div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 3: Individual Devices Selection Table --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 {{ (!in_array($campaign->audience_type ?? 'individual', ['all', 'segment', 'location'])) ? '' : 'd-none' }}" id="subpanelIndividual">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="subpanel-header-title mb-0">Select Target Devices</div>
                                <span class="fs-11 text-muted">Checked devices will receive this push notification</span>
                            </div>

                            @php
                                $existingTargetIds = (array) ($campaign->target_device_ids ?? []);
                            @endphp

                            <div class="table-responsive border rounded-2 mb-2 bg-white" id="deviceTableWrap" style="max-height: 260px; overflow-y: auto;">
                                <table class="table table-hover table-sm align-middle mb-0 fs-12">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th style="width: 36px;" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="selectAllDevicesCheck" onchange="toggleAllDevices(this)">
                                            </th>
                                            <th>Device ID</th>
                                            <th>Platform</th>
                                            <th>Location</th>
                                            <th>Last Active</th>
                                            <th>Perm</th>
                                            <th>FCM</th>
                                        </tr>
                                    </thead>
                                    <tbody id="deviceTableBody">
                                        @forelse($devices as $idx => $dev)
                                            @php
                                                $isTargeted = !empty($existingTargetIds) 
                                                    ? in_array($dev->device_id, $existingTargetIds) 
                                                    : ($idx < 3);
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    <input class="form-check-input individual-device-check" type="checkbox" name="target_device_ids[]" value="{{ $dev->device_id }}" data-platform="{{ $dev->platform }}" {{ $isTargeted ? 'checked' : '' }} onchange="onDeviceCheckChange()">
                                                </td>
                                                <td><span class="font-monospace text-dark fw-bold">{{ $dev->device_id }}</span></td>
                                                <td>
                                                    @if(strtolower($dev->platform) === 'ios')
                                                        <i class="fa-brands fa-apple text-dark me-1"></i> iOS
                                                    @else
                                                        <i class="fa-brands fa-android text-success me-1"></i> Android
                                                    @endif
                                                </td>
                                                <td>{{ $dev->location ? ($dev->location->city . ', ' . $dev->location->country) : 'Tirunelveli, India' }}</td>
                                                <td><span class="text-muted fs-11">{{ $dev->last_active_at ? $dev->last_active_at->diffForHumans() : 'Just now' }}</span></td>
                                                <td>
                                                    <span class="badge bg-success-subtle text-success fs-10 fw-normal">Granted</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary fs-10 fw-normal">Active</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted">No devices found in database.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="invalid-feedback d-none mb-2" id="deviceSelectionError">Please select at least one device to target.</div>

                            <div class="d-flex align-items-center justify-content-between fs-11">
                                <span class="fw-bold text-dark" id="deviceSelectedCountLabel">{{ $campaign->total_audience ?? 3 }} devices selected</span>
                                <a href="javascript:void(0)" class="text-primary fw-semibold text-decoration-none" onclick="clearDeviceSelection()">Clear selection</a>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Delivery Exclusions Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Delivery Exclusions</div>
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-12 text-dark">Exclude inactive installations</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="exclude_inactive_inst" checked>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-12 text-dark">Exclude devices without notification permission</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="exclude_no_perm" checked>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-12 text-dark">Exclude invalid FCM tokens</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="exclude_invalid_fcm" checked>
                                </div>
                            </div>
                        </div>
                        <div class="alert-blue-light mb-0">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>These exclusions keep notification delivery accurate and reliable.</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Step 2 Summary & Hero Stats --}}
                <div class="col-lg-5 col-xl-4">
                    {{-- Hero Estimated Audience Card --}}
                    <div class="estimated-audience-card">
                        <div class="fs-12 fw-bold text-dark mb-1">Estimated Audience</div>

                        <div class="est-hero-number" id="step2HeroCount">{{ $campaign->total_audience ?? 3 }}</div>
                        <div class="est-hero-sub">Deliverable devices</div>

                        <div class="donut-chart-row">
                            <div class="donut-svg-wrap">
                                <svg width="72" height="72" viewBox="0 0 36 36" id="step2DonutSvg">
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#e2e8f0" stroke-width="4"></circle>
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#22c55e" stroke-width="4" stroke-dasharray="67 100" stroke-dashoffset="0" id="step2DonutGreen"></circle>
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#3b82f6" stroke-width="4" stroke-dasharray="33 100" stroke-dashoffset="-67" id="step2DonutBlue"></circle>
                                </svg>
                            </div>
                            <div class="donut-legend">
                                <div class="legend-item">
                                    <span class="dot green"></span> Android
                                    <span class="legend-percent" id="step2AndroidPct">67%</span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot blue"></span> iOS
                                    <span class="legend-percent" id="step2IosPct">33%</span>
                                </div>
                            </div>
                        </div>

                        <div class="spec-summary-list">
                            <div class="spec-item">
                                <span class="spec-lbl" id="specRow1Label">Selected Devices</span>
                                <span class="spec-val" id="specRow1Val">{{ $campaign->total_audience ?? 3 }}</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">Excluded</span>
                                <span class="spec-val" id="specRow2Val">0</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">Estimated Delivery</span>
                                <span class="spec-val fw-bold" id="specRow3Val">{{ $campaign->total_audience ?? 3 }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Audience Summary Specs Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-3">Audience Summary</div>

                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-bullhorn text-muted me-1"></i> Type</span>
                                <span class="spec-val" id="summaryTypeVal">{{ $campaign->audience_label ?? 'Individual Devices' }}</span>
                            </div>
                            <div class="spec-item py-1" id="summarySegmentRow" style="display: {{ ($campaign->audience_type ?? '') === 'segment' ? 'flex' : 'none' }};">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Segment</span>
                                <span class="spec-val" id="summarySegmentVal">{{ $campaign->segment?->name ?? 'Active Users – Tirunelveli' }}</span>
                            </div>
                            <div class="spec-item py-1" id="summarySelectedDevicesRow" style="display: {{ (!in_array($campaign->audience_type ?? 'individual', ['all', 'segment', 'location'])) ? 'flex' : 'none' }};">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen text-muted me-1"></i> Selected Devices</span>
                                <span class="spec-val" id="summarySelectedDevicesVal">{{ $campaign->total_audience ?? 3 }}</span>
                            </div>
                            <div class="spec-item py-1" id="summaryLocationRow" style="display: {{ ($campaign->audience_type ?? '') === 'location' ? 'flex' : 'none' }};">
                                <span class="spec-lbl"><i class="fa-solid fa-location-dot text-muted me-1"></i> Location</span>
                                <span class="spec-val" id="summaryLocationVal">Tirunelveli</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-shield-check text-muted me-1"></i> Notification Permission</span>
                                <span class="spec-val text-success">Enabled</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-key text-muted me-1"></i> FCM Token</span>
                                <span class="spec-val text-success">Valid</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-globe text-muted me-1"></i> Coverage</span>
                                <span class="spec-val" id="summaryCoverageVal">Selected Devices Only</span>
                            </div>
                        </div>
                    </div>

                    {{-- Ready Alert --}}
                    <div class="alert-audience-ready" id="audienceReadyAlert">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <div>Audience is ready</div>
                            <div class="fw-normal fs-11" id="audienceReadyDesc">{{ $campaign->total_audience ?? 3 }} selected devices can receive this notification.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 3: SCHEDULE SCREEN --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel3">
            <div class="row g-3">
                {{-- Left Column: Schedule Selection --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Delivery Schedule Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Delivery Schedule</div>
                        <div class="card-section-subtitle">Choose when to send this notification.</div>

                        {{-- Option 1: Send Now --}}
                        <div class="audience-option-card {{ ($campaign->status === 'sent') ? 'selected' : '' }}" id="cardScheduleNow" onclick="setDeliveryScheduleMode('now')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="schedule_choice" id="radioScheduleNow" value="now" {{ ($campaign->status === 'sent') ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div>
                                    <div class="option-title">Send Now</div>
                                    <div class="option-desc">Send immediately after final confirmation</div>
                                </div>
                            </div>
                        </div>

                        {{-- Option 2: Schedule for Later (Default) --}}
                        <div class="audience-option-card {{ ($campaign->status !== 'sent') ? 'selected' : '' }}" id="cardScheduleLater" onclick="setDeliveryScheduleMode('schedule')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="schedule_choice" id="radioScheduleLater" value="schedule" {{ ($campaign->status !== 'sent') ? 'checked' : '' }}>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-regular fa-calendar-days"></i>
                                </div>
                                <div>
                                    <div class="option-title">Schedule for Later</div>
                                    <div class="option-desc">Choose a specific date and time</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sub-Panel A: Schedule Date & Time (When Schedule for Later selected) --}}
                        <div class="audience-subpanel-card mt-3 {{ ($campaign->status === 'sent') ? 'd-none' : '' }}" id="subpanelScheduleLater">
                            <div class="subpanel-header-title mb-2">Schedule Date & Time</div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fs-11 mb-1" for="inputScheduledDate">Delivery Date <span class="text-danger">*</span></label>
                                    <div class="schedule-input-wrap">
                                        <input type="text" name="scheduled_date" id="inputScheduledDate" class="form-control schedule-input" value="{{ $localScheduledAt ? $localScheduledAt->format('d M Y') : date('d M Y') }}" placeholder="Select Delivery Date" oninput="clearValidation(this); syncScheduleSummary()" onchange="clearValidation(this); syncScheduleSummary()">
                                        <i class="fa-regular fa-calendar schedule-icon-suffix" id="btnDateCalSuffix" title="Open Calendar"></i>
                                    </div>
                                    <div class="invalid-feedback">Delivery date is required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fs-11 mb-1" for="inputScheduledTime">Delivery Time <span class="text-danger">*</span></label>
                                    <div class="schedule-input-wrap">
                                        <input type="text" name="scheduled_time" id="inputScheduledTime" class="form-control schedule-input" value="{{ $localScheduledAt ? $localScheduledAt->format('h:i A') : '10:30 AM' }}" placeholder="Select Delivery Time" oninput="clearValidation(this); syncScheduleSummary()" onchange="clearValidation(this); syncScheduleSummary()">
                                        <i class="fa-regular fa-clock schedule-icon-suffix" id="btnTimeClockSuffix" title="Open Time Picker"></i>
                                    </div>
                                    <div class="invalid-feedback">Delivery time is required.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fs-11 mb-1" for="selectScheduleTimeZone">Time Zone</label>
                                <select name="schedule_time_zone" id="selectScheduleTimeZone" class="form-select form-select-sm" onchange="syncScheduleSummary()">
                                    <option value="Asia/Kolkata" {{ ($campaign->time_zone ?? 'Asia/Kolkata') === 'Asia/Kolkata' ? 'selected' : '' }}>IST - India Standard Time (Asia/Kolkata, UTC+05:30)</option>
                                    <option value="Asia/Dubai" {{ ($campaign->time_zone ?? '') === 'Asia/Dubai' ? 'selected' : '' }}>GST - Gulf Standard Time (Dubai/UAE, UTC+04:00)</option>
                                    <option value="UTC" {{ ($campaign->time_zone ?? '') === 'UTC' ? 'selected' : '' }}>UTC - Universal Coordinated Time (UTC+00:00)</option>
                                    <option value="America/New_York" {{ ($campaign->time_zone ?? '') === 'America/New_York' ? 'selected' : '' }}>EST - Eastern Standard Time (New York, UTC-05:00)</option>
                                    <option value="America/Los_Angeles" {{ ($campaign->time_zone ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>PST - Pacific Standard Time (Los Angeles, UTC-08:00)</option>
                                </select>
                            </div>

                            <div class="alert-blue-light mb-2">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>The notification will be sent according to the selected time zone.</span>
                            </div>

                            <div class="fs-12 fw-bold text-dark" id="scheduledDeliveryNotice">
                                Scheduled delivery: {{ $localScheduledAt ? $localScheduledAt->format('l, d F Y \a\t h:i A') . ' ' . ($schedTz === 'Asia/Kolkata' ? 'IST' : $schedTz) : date('l, d F Y') . ' at 10:30 AM IST' }}
                            </div>
                        </div>

                        {{-- Sub-Panel B: Immediate Delivery Info (When Send Now selected) --}}
                        <div class="audience-subpanel-card mt-3 {{ ($campaign->status === 'sent') ? '' : 'd-none' }}" id="subpanelScheduleNow">
                            <div class="subpanel-header-title mb-2">Immediate Delivery</div>
                            <div class="d-flex align-items-start gap-3 p-2 bg-white rounded-2 border mb-3">
                                <div class="schedule-hero-art-box p-0">
                                    <div class="art-circle-icon" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fs-12 fw-bold text-dark mb-1">Ready to send immediately</div>
                                    <div class="fs-11 text-muted mb-2">The notification will be queued as soon as you confirm it in Review & Save.</div>
                                    <div class="alert-blue-light mb-2 py-1">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>Delivery usually begins within a few seconds. Actual receipt may depend on device connectivity.</span>
                                    </div>
                                    <div class="fs-12 fw-bold text-primary">
                                        Estimated delivery: <span class="step3-dynamic-count">{{ $campaign->total_audience ?? 3 }}</span> eligible devices
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Delivery Settings Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Delivery Settings</div>
                        <div class="d-flex flex-column gap-3 mb-3">
                            {{-- Respect quiet hours --}}
                            <div class="d-flex align-items-center justify-content-between" id="rowQuietHours">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="modal-icon-header-circle icon-blue" style="width: 32px; height: 32px; font-size: 12px;">
                                        <i class="fa-regular fa-bell"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-dark">Respect quiet hours</div>
                                        <div class="fs-11 text-muted" id="quietHoursSubtext">Avoid delivery between 10:00 PM and 8:00 AM</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="respect_quiet_hours" id="swRespectQuietHours" {{ $campaign->quiet_hours_enabled ? 'checked' : '' }}>
                                </div>
                            </div>

                            {{-- Send only to eligible devices --}}
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="modal-icon-header-circle icon-blue" style="width: 32px; height: 32px; font-size: 12px;">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 text-dark">Send only to eligible devices</div>
                                        <div class="fs-11 text-muted">Skip inactive devices, denied permissions and invalid FCM tokens</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="send_only_eligible" checked>
                                </div>
                            </div>

                            {{-- Message Expiry --}}
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-12 text-dark">Message Expiry</span>
                                    <select name="message_expiry" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="24" {{ ($campaign->expiry_hours ?? 24) == 24 ? 'selected' : '' }}>24 hours</option>
                                        <option value="48" {{ ($campaign->expiry_hours ?? '') == 48 ? 'selected' : '' }}>48 hours</option>
                                        <option value="72" {{ ($campaign->expiry_hours ?? '') == 72 ? 'selected' : '' }}>72 hours</option>
                                        <option value="168" {{ ($campaign->expiry_hours ?? '') == 168 ? 'selected' : '' }}>7 days</option>
                                    </select>
                                </div>
                                <div class="fs-11 text-muted">Expired notifications will not be delivered to devices that reconnect later.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Step 3 Summary & Illustration --}}
                <div class="col-lg-5 col-xl-4">
                    {{-- Schedule Summary Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-2" id="step3SummaryCardTitle">{{ ($campaign->status === 'sent') ? 'Delivery Summary' : 'Schedule Summary' }}</div>

                        {{-- Art box (Calendar art vs Lightning Clock art) --}}
                        <div class="schedule-hero-art-box {{ ($campaign->status === 'sent') ? 'd-none' : '' }}" id="step3ArtBoxCalendar">
                            <div class="art-calendar-icon">
                                <div class="cal-header-dots">
                                    <span class="dot-ring"></span>
                                    <span class="dot-ring"></span>
                                </div>
                                <div class="cal-grid-lines">
                                    <span style="height: 3px; background: #93c5fd; border-radius: 1px;"></span>
                                    <span style="height: 3px; background: #93c5fd; border-radius: 1px;"></span>
                                    <span style="height: 3px; background: #93c5fd; border-radius: 1px;"></span>
                                    <span style="height: 3px; background: #93c5fd; border-radius: 1px;"></span>
                                </div>
                                <div class="clock-badge-sub">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="schedule-hero-art-box {{ ($campaign->status === 'sent') ? '' : 'd-none' }}" id="step3ArtBoxLightning">
                            <div class="art-circle-icon">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                        </div>

                        {{-- Specs list --}}
                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-regular fa-calendar text-muted me-1"></i> Delivery Type</span>
                                <span class="spec-val" id="step3DeliveryTypeVal">{{ ($campaign->status === 'sent') ? 'Send Now' : 'Scheduled' }}</span>
                            </div>
                            <div class="spec-item py-1" id="step3DateRow" style="display: {{ ($campaign->status === 'sent') ? 'none' : 'flex' }};">
                                <span class="spec-lbl"><i class="fa-regular fa-calendar-days text-muted me-1"></i> Date</span>
                                <span class="spec-val" id="step3DateVal">{{ $localScheduledAt ? $localScheduledAt->format('d M Y') : date('d M Y') }}</span>
                            </div>
                            <div class="spec-item py-1" id="step3TimeRow">
                                <span class="spec-lbl"><i class="fa-regular fa-clock text-muted me-1"></i> {{ ($campaign->status === 'sent') ? 'Delivery Time' : 'Time' }}</span>
                                <span class="spec-val" id="step3TimeVal">{{ ($campaign->status === 'sent') ? 'Immediately' : ($localScheduledAt ? $localScheduledAt->format('h:i A') : '10:30 AM') }}</span>
                            </div>
                            <div class="spec-item py-1" id="step3TimeZoneRow" style="display: {{ ($campaign->status === 'sent') ? 'none' : 'flex' }};">
                                <span class="spec-lbl"><i class="fa-solid fa-globe text-muted me-1"></i> Time Zone</span>
                                <span class="spec-val" id="step3TimeZoneVal">{{ $campaign->time_zone ?? 'Asia/Kolkata' }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Estimated Delivery</span>
                                <span class="spec-val" id="step3EstDeliveryVal"><span class="step3-dynamic-count">{{ $campaign->total_audience ?? 3 }}</span> devices</span>
                            </div>
                            <div class="spec-item py-1" id="step3PermRow" style="display: none;">
                                <span class="spec-lbl"><i class="fa-solid fa-shield-check text-muted me-1"></i> Notification Permission</span>
                                <span class="spec-val text-success">Enabled</span>
                            </div>
                            <div class="spec-item py-1" id="step3FcmRow" style="display: none;">
                                <span class="spec-lbl"><i class="fa-solid fa-key text-muted me-1"></i> FCM Token</span>
                                <span class="spec-val text-success">Valid</span>
                            </div>
                        </div>
                    </div>

                    {{-- Notification Summary Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-3">Notification Summary</div>
                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-bullhorn text-muted me-1"></i> Title</span>
                                <span class="spec-val" id="step3NotifTitleVal">{{ $campaign->title ?: 'GPS Camera Update' }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Audience</span>
                                <span class="spec-val" id="step3NotifAudienceVal">{{ $campaign->audience_label ?? 'Individual Devices' }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen text-muted me-1"></i> Selected Devices</span>
                                <span class="spec-val"><span class="step3-dynamic-count">{{ $campaign->total_audience ?? 3 }}</span></span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen-button text-muted me-1"></i> Platforms</span>
                                <span class="spec-val" id="step3PlatformsVal"><i class="fa-brands fa-android text-success"></i> Android {{ $campaign->android_count ?? 2 }} · <i class="fa-brands fa-apple text-dark"></i> iOS {{ $campaign->ios_count ?? 1 }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-circle-check text-muted me-1"></i> Status</span>
                                <span class="spec-val text-success">Ready</span>
                            </div>
                        </div>
                    </div>

                    {{-- Ready Alert --}}
                    <div class="alert-audience-ready" id="step3ReadyAlert">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <div id="step3ReadyTitle">{{ ($campaign->status === 'sent') ? 'Ready to send' : 'Schedule is ready' }}</div>
                            <div class="fw-normal fs-11" id="step3ReadyDesc">{{ ($campaign->status === 'sent') ? 'This notification will be sent to ' . ($campaign->total_audience ?? 3) . ' devices immediately after confirmation.' : 'This notification will be sent to ' . ($campaign->total_audience ?? 3) . ' devices on ' . ($localScheduledAt ? $localScheduledAt->format('d M Y \a\t h:i A') : date('d M Y') . ' at 10:30 AM') . ' ' . ($schedTz === 'Asia/Kolkata' ? 'IST' : $schedTz) . '.' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 4: REVIEW & SAVE SCREEN --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel4">
            <div class="row g-3">
                {{-- Left Column: Review Sections --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Campaign Content Review Card --}}
                    <div class="wizard-card">
                        <div class="review-section-header">
                            <div class="card-section-title mb-0">1 Campaign Content</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(1)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Content
                            </a>
                        </div>

                        <div class="review-spec-list">
                            <div class="review-spec-row">
                                <span class="review-spec-label">Campaign Name</span>
                                <span class="review-spec-value fw-semibold text-dark" id="revCampaignName">{{ $campaign->name }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Notification Title</span>
                                <span class="review-spec-value fw-bold text-dark" id="revNotificationTitle">{{ $campaign->title }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Message Body</span>
                                <span class="review-spec-value text-muted" id="revNotificationMessage">{{ $campaign->message }}</span>
                            </div>
                            <div class="review-spec-row" id="revImageRow" style="display: {{ $campaign->image_url ? 'flex' : 'none' }};">
                                <span class="review-spec-label">Notification Image</span>
                                <div class="review-spec-value d-flex align-items-center gap-2">
                                    <img id="revThumbnailImg" src="{{ $campaign->image_url ?: '' }}" alt="Thumbnail" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                    <span class="fs-11 text-muted" id="revImageName">{{ $campaign->image_url ? basename($campaign->image_url) : 'image.png' }}</span>
                                </div>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">On Tap Action</span>
                                <span class="review-spec-value" id="revAction">{{ ucfirst(str_replace('_', ' ', $campaign->action ?? 'open_app')) }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">App Target</span>
                                <span class="review-spec-value">GeoCam (com.geocam.app)</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Target Audience Review Card --}}
                    <div class="wizard-card">
                        <div class="review-section-header">
                            <div class="card-section-title mb-0">2 Target Audience</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(2)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Audience
                            </a>
                        </div>

                        <div class="review-spec-list">
                            <div class="review-spec-row">
                                <span class="review-spec-label">Audience Type</span>
                                <span class="review-spec-value fw-semibold" id="revAudienceType">{{ $campaign->audience_label ?? 'Individual Devices' }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Target Devices</span>
                                <span class="review-spec-value fw-bold text-primary"><span id="revSelectedCount">{{ $campaign->total_audience ?? 3 }}</span> installations</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Platforms</span>
                                <span class="review-spec-value" id="revPlatformsVal"><i class="fa-brands fa-android text-success"></i> Android {{ $campaign->android_count ?? 2 }} · <i class="fa-brands fa-apple text-dark"></i> iOS {{ $campaign->ios_count ?? 1 }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Eligibility Filter</span>
                                <span class="review-spec-value text-success" id="revEligibility">All {{ $campaign->total_audience ?? 3 }} devices eligible</span>
                            </div>
                            <div class="review-spec-row align-items-start" id="revDevicePillsRow">
                                <span class="review-spec-label">Target Device IDs</span>
                                <div class="review-spec-value d-flex flex-wrap gap-1">
                                    @forelse($existingTargetIds as $devId)
                                        <span class="device-id-pill">{{ $devId }}</span>
                                    @empty
                                        <span class="device-id-pill">DEV-001</span>
                                        <span class="device-id-pill">DEV-002</span>
                                        <span class="device-id-pill">DEV-003</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Delivery Schedule Review Card --}}
                    <div class="wizard-card">
                        <div class="review-section-header">
                            <div class="card-section-title mb-0">3 Delivery & Timing</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(3)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Schedule
                            </a>
                        </div>

                        <div class="review-spec-list">
                            <div class="review-spec-row">
                                <span class="review-spec-label">Delivery Mode</span>
                                <span class="review-spec-value fw-semibold text-primary" id="revDeliveryType">
                                    @if($campaign->status === 'sent')
                                        Send Now
                                    @else
                                        Scheduled <i class="fa-regular fa-calendar text-primary ms-1"></i>
                                    @endif
                                </span>
                            </div>
                            <div class="review-spec-row" id="revDateRow" style="display: {{ ($campaign->status === 'sent') ? 'none' : 'flex' }};">
                                <span class="review-spec-label">Delivery Date</span>
                                <span class="review-spec-value fw-bold text-dark" id="revDeliveryDate">{{ $localScheduledAt ? $localScheduledAt->format('d M Y') : date('d M Y') }}</span>
                            </div>
                            <div class="review-spec-row" id="revTimeRow">
                                <span class="review-spec-label">Delivery Time</span>
                                <span class="review-spec-value" id="revDeliveryTime">{{ ($campaign->status === 'sent') ? 'Immediately after confirmation' : ($localScheduledAt ? $localScheduledAt->format('h:i A') . ' ' . ($schedTz === 'Asia/Kolkata' ? 'IST' : $schedTz) : '10:30 AM IST') }}</span>
                            </div>
                            <div class="review-spec-row" id="revTimeZoneRow" style="display: {{ ($campaign->status === 'sent') ? 'none' : 'flex' }};">
                                <span class="review-spec-label">Time Zone</span>
                                <span class="review-spec-value text-muted" id="revTimeZone">{{ $campaign->time_zone ?? 'Asia/Kolkata' }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Quiet Hours</span>
                                <span class="review-spec-value" id="revQuietHours">{{ $campaign->quiet_hours_enabled ? 'Enabled' : 'Disabled' }}</span>
                            </div>
                            <div class="review-spec-row">
                                <span class="review-spec-label">Message Expiry</span>
                                <span class="review-spec-value">{{ $campaign->expiry_hours ?? 24 }} hours</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Final Phone Preview & Pre-send Checklist --}}
                <div class="col-lg-5 col-xl-4">
                    {{-- Live Preview in Step 4 --}}
                    <div class="smartphone-preview-container">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 fw-bold text-dark">Live Preview</span>
                        </div>

                        <div class="preview-tabs-row">
                            <button type="button" class="preview-tab-btn active" id="btnRevPreviewAndroid" onclick="switchRevPreviewPlatform('android')">Android</button>
                            <button type="button" class="preview-tab-btn" id="btnRevPreviewIos" onclick="switchRevPreviewPlatform('ios')">iOS</button>
                        </div>

                        <div class="phone-screen-box">
                            <div class="phone-top-bar">
                                <span class="phone-clock">10:30</span>
                                <span>Mon, Aug 19</span>
                                <div>
                                    <i class="fa-solid fa-wifi me-1"></i>
                                    <i class="fa-solid fa-battery-full"></i>
                                </div>
                            </div>

                            <div class="phone-notification-card">
                                <div class="notif-card-header">
                                    <div class="review-app-logo-box" style="width: 18px; height: 18px; font-size: 10px; border-radius: 4px;">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <span class="app-name">GPS Camera</span>
                                    <span class="notif-time">Now</span>
                                </div>
                                <div class="notif-card-title" id="revPreviewCardTitle">{{ $campaign->title }}</div>
                                <div class="notif-card-body" id="revPreviewCardBody">{{ $campaign->message }}</div>
                                <div id="revPreviewCardImageWrap" class="notif-card-banner {{ $campaign->image_url ? '' : 'd-none' }} mt-2">
                                    <img id="revPreviewCardImgElem" src="{{ $campaign->image_url ?: '' }}" alt="Notification banner" class="img-fluid rounded-2" style="max-height: 120px; width: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pre-Send Summary Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-2">Summary Checklist</div>

                        <div class="checklist-item-row" id="preCheckContentItem">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Content and title are valid</span>
                        </div>
                        <div class="checklist-item-row" id="preCheckAudienceItem">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Target audience is deliverable</span>
                        </div>
                        <div class="checklist-item-row" id="preCheckScheduleItem">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Delivery parameters are configured</span>
                        </div>

                        <div class="spec-summary-list border-top pt-2 mt-2">
                            <div class="spec-item py-1">
                                <span class="spec-lbl">Audience</span>
                                <span class="spec-val" id="revFinalAudience">{{ $campaign->audience_label ?? 'Individual Devices' }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl">Platforms</span>
                                <span class="spec-val" id="revFinalPlatforms"><i class="fa-brands fa-android text-success"></i> Android {{ $campaign->android_count ?? 2 }} · <i class="fa-brands fa-apple text-dark"></i> iOS {{ $campaign->ios_count ?? 1 }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl">Delivery</span>
                                <span class="spec-val fw-bold" id="revFinalDelivery">{{ $campaign->status === 'sent' ? 'Send Now' : ($localScheduledAt ? $localScheduledAt->format('d M Y · h:i A') . ' ' . ($schedTz === 'Asia/Kolkata' ? 'IST' : $schedTz) : date('d M Y') . ' · 10:30 AM IST') }}</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl">Status</span>
                                <span class="spec-val text-primary fw-bold" id="revFinalStatus">{{ ucfirst($campaign->status ?? 'Draft') }}</span>
                            </div>
                        </div>

                        <div class="alert-amber-light mt-3 mb-0" id="revAmberNotice">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span id="revAmberText">Changes will update the campaign configuration in the database.</span>
                        </div>
                    </div>

                    {{-- Ready Alert --}}
                    <div class="alert-audience-ready" id="revReadyAlert">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <div id="revReadyTitle">Ready to update</div>
                            <div class="fw-normal fs-11" id="revReadyDesc">All validation checks passed. Click below to save your changes.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- 3. Sticky Bottom Wizard Footer Actions --}}
        {{-- ====================================================================== --}}
        <div class="wizard-bottom-bar">
            <div class="autosave-indicator">
                <i class="fa-solid fa-check"></i>
                <span id="autoSaveText">Changes ready to save</span>
            </div>

            <div class="bottom-actions-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBottomBack" onclick="handleBottomBack()" style="display: none;">
                    Back
                </button>
                <a href="{{ route('admin.notifications.show', $campaign->id) }}" class="btn btn-sm btn-outline-secondary" id="btnBottomCancel">
                    Cancel
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="saveAsDraft()">
                    Save as Draft
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBottomSendTest" data-bs-toggle="modal" data-bs-target="#sendTestNotificationModal">
                    Send Test
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btnBottomPrimary" onclick="handleBottomPrimary()">
                    Continue to Audience
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ====================================================================== --}}
{{-- MODAL 1: Send Test Notification Modal --}}
{{-- ====================================================================== --}}
<div class="modal fade" id="sendTestNotificationModal" tabindex="-1" aria-labelledby="sendTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon-header-circle icon-blue">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fs-14 fw-bold text-dark" id="sendTestModalLabel">Send Test Notification</h5>
                        <p class="modal-subtitle text-muted fs-11 mb-0">Verify notification delivery and appearance on a specific device</p>
                    </div>
                </div>
                <button type="button" class="btn-close fs-10" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-dark fs-12 mb-1">Target Test Device <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="testDeviceSelect">
                        @forelse($devices as $dev)
                            <option value="{{ $dev->fcm_token ?: $dev->device_id }}">
                                {{ $dev->device_id }} ({{ $dev->platform }}) - {{ $dev->location ? $dev->location->city : 'Online' }}
                            </option>
                        @empty
                            <option value="test_fcm_token_1">Admin Pixel 8 (Android 14) - Active</option>
                        @endforelse
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark fs-12 mb-1">Custom FCM Token (Optional)</label>
                    <input type="text" class="form-control form-control-sm font-monospace fs-11" id="testCustomFcmInput" placeholder="Paste specific FCM device registration token...">
                </div>

                <div class="alert-blue-light mb-0">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>A test push notification with current title and body will be dispatched immediately.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnConfirmSendTest" onclick="sendTestPush()">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send Test Push
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStep = 1;
let currentAudienceMode = @json($campaign->audience_type ?? 'individual');
let currentDeliveryScheduleMode = @json($campaign->status === 'sent' ? 'now' : 'schedule');

const dbAudienceData = @json($audienceMetrics ?? []);
const dbSegments = @json($segments ?? []);
const dbLocations = @json($locations ?? []);

// Validation logic per step
function validateStep(step) {
    let isValid = true;
    let firstErrorField = null;

    if (step === 1) {
        const campInput = document.getElementById('inputCampaignName');
        const titleInput = document.getElementById('inputNotificationTitle');
        const msgInput = document.getElementById('inputNotificationMessage');

        if (!campInput || !campInput.value.trim()) {
            campInput.classList.add('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = campInput;
        } else {
            campInput.classList.remove('is-invalid');
        }

        if (!titleInput || !titleInput.value.trim()) {
            titleInput.classList.add('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = titleInput;
        } else {
            titleInput.classList.remove('is-invalid');
        }

        if (!msgInput || !msgInput.value.trim()) {
            msgInput.classList.add('is-invalid');
            isValid = false;
            if (!firstErrorField) firstErrorField = msgInput;
        } else {
            msgInput.classList.remove('is-invalid');
        }
    } else if (step === 2) {
        if (currentAudienceMode === 'individual') {
            const checkedDevices = document.querySelectorAll('.individual-device-check:checked');
            const tableWrap = document.getElementById('deviceTableWrap');
            const errorMsg = document.getElementById('deviceSelectionError');

            if (checkedDevices.length === 0) {
                if (tableWrap) tableWrap.classList.add('table-is-invalid');
                if (errorMsg) errorMsg.classList.remove('d-none');
                isValid = false;
                if (!firstErrorField) firstErrorField = tableWrap;
            } else {
                if (tableWrap) tableWrap.classList.remove('table-is-invalid');
                if (errorMsg) errorMsg.classList.add('d-none');
            }
        }
    } else if (step === 3) {
        if (currentDeliveryScheduleMode === 'schedule') {
            const dateInput = document.getElementById('inputScheduledDate');
            const timeInput = document.getElementById('inputScheduledTime');

            if (!dateInput || !dateInput.value.trim()) {
                dateInput.classList.add('is-invalid');
                isValid = false;
                if (!firstErrorField) firstErrorField = dateInput;
            } else {
                dateInput.classList.remove('is-invalid');
            }

            if (!timeInput || !timeInput.value.trim()) {
                timeInput.classList.add('is-invalid');
                isValid = false;
                if (!firstErrorField) firstErrorField = timeInput;
            } else {
                timeInput.classList.remove('is-invalid');
            }
        }
    }

    if (!isValid && firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return isValid;
}

// Step Navigation
function goToStep(step) {
    if (step > currentStep) {
        for (let s = currentStep; s < step; s++) {
            if (!validateStep(s)) {
                return false;
            }
        }
    }

    currentStep = step;
    syncAllWizardFields();

    for (let i = 1; i <= 4; i++) {
        const panel = document.getElementById('stepPanel' + i);
        const node = document.getElementById('stepNode' + i);
        const line = document.getElementById('stepLine' + (i - 1));
        const circle = document.getElementById('stepCircle' + i);

        if (panel) {
            if (i === step) {
                panel.classList.remove('d-none');
                panel.classList.add('active');
            } else {
                panel.classList.add('d-none');
                panel.classList.remove('active');
            }
        }

        if (node) {
            if (i < step) {
                node.className = 'stepper-step done';
                if (circle) circle.innerHTML = '<i class="fa-solid fa-check fs-10"></i>';
            } else if (i === step) {
                node.className = 'stepper-step active';
                if (circle) circle.textContent = i;
            } else {
                node.className = 'stepper-step';
                if (circle) circle.textContent = i;
            }
        }

        if (line) {
            if (i <= step) {
                line.classList.add('done');
            } else {
                line.classList.remove('done');
            }
        }
    }

    // Header & button updates
    const titleEl = document.getElementById('wizardHeaderTitle');
    const subEl = document.getElementById('wizardHeaderSubtitle');
    const topBtn = document.getElementById('topActionBtn');
    const bottomPrimary = document.getElementById('btnBottomPrimary');
    const bottomBack = document.getElementById('btnBottomBack');
    const bottomCancel = document.getElementById('btnBottomCancel');
    const sendTestBtn = document.getElementById('btnBottomSendTest');
    const topCancelLink = document.getElementById('topCancelLink');
    const topSaveDraftBtn = document.getElementById('topSaveDraftBtn');
    const topSendTestBtn = document.getElementById('topSendTestBtn');

    if (step === 1) {
        if (titleEl) titleEl.textContent = 'Edit Notification';
        if (subEl) subEl.textContent = 'Update and adjust push notification settings and delivery details';
        if (topBtn) { topBtn.style.display = 'inline-flex'; topBtn.textContent = 'Review & Save'; }
        if (topCancelLink) topCancelLink.style.display = 'none';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'inline-flex';
        if (topSendTestBtn) topSendTestBtn.style.display = 'inline-flex';
        if (bottomPrimary) { bottomPrimary.innerHTML = 'Continue to Audience'; }
        if (bottomBack) bottomBack.style.display = 'none';
        if (bottomCancel) bottomCancel.style.display = 'inline-flex';
        if (sendTestBtn) sendTestBtn.style.display = 'inline-flex';
    } else if (step === 2) {
        if (titleEl) titleEl.textContent = 'Edit Notification';
        if (subEl) subEl.textContent = 'Choose who should receive this notification';
        if (topBtn) { topBtn.style.display = 'none'; }
        if (topCancelLink) topCancelLink.style.display = 'inline-flex';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'none';
        if (topSendTestBtn) topSendTestBtn.style.display = 'none';
        if (bottomPrimary) { bottomPrimary.innerHTML = 'Continue to Schedule'; }
        if (bottomBack) bottomBack.style.display = 'inline-flex';
        if (bottomCancel) bottomCancel.style.display = 'none';
        if (sendTestBtn) sendTestBtn.style.display = 'none';
    } else if (step === 3) {
        if (titleEl) titleEl.textContent = 'Edit Notification';
        if (subEl) subEl.textContent = 'Choose when this notification should be delivered';
        if (topBtn) { topBtn.style.display = 'none'; }
        if (topCancelLink) topCancelLink.style.display = 'inline-flex';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'none';
        if (topSendTestBtn) topSendTestBtn.style.display = 'none';
        if (bottomPrimary) { bottomPrimary.innerHTML = 'Continue to Review & Save'; }
        if (bottomBack) bottomBack.style.display = 'inline-flex';
        if (bottomCancel) bottomCancel.style.display = 'none';
        if (sendTestBtn) sendTestBtn.style.display = 'none';
    } else if (step === 4) {
        if (titleEl) titleEl.textContent = 'Edit Notification';
        if (subEl) {
            subEl.textContent = (currentDeliveryScheduleMode === 'schedule') 
                ? 'Review all details before saving and scheduling this notification' 
                : 'Review all details before saving and sending this notification';
        }
        if (topBtn) { topBtn.style.display = 'none'; }
        if (topCancelLink) topCancelLink.style.display = 'inline-flex';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'none';
        if (topSendTestBtn) topSendTestBtn.style.display = 'none';

        if (bottomPrimary) {
            if (currentDeliveryScheduleMode === 'schedule') {
                bottomPrimary.innerHTML = '<i class="fa-solid fa-calendar-check me-1"></i> Save & Schedule';
            } else {
                bottomPrimary.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Save & Send';
            }
        }
        if (bottomBack) bottomBack.style.display = 'inline-flex';
        if (bottomCancel) bottomCancel.style.display = 'none';
        if (sendTestBtn) sendTestBtn.style.display = 'none';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    return true;
}

function nextWizardStep() {
    if (currentStep < 4) {
        goToStep(4);
    } else {
        submitFinalNotification();
    }
}

function handleBottomPrimary() {
    if (currentStep === 1) {
        goToStep(2);
    } else if (currentStep === 2) {
        goToStep(3);
    } else if (currentStep === 3) {
        goToStep(4);
    } else {
        submitFinalNotification();
    }
}

function handleBottomBack() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
}

function submitFinalNotification() {
    for (let s = 1; s <= 3; s++) {
        if (!validateStep(s)) {
            goToStep(s);
            return;
        }
    }

    if (currentDeliveryScheduleMode === 'schedule') {
        document.getElementById('campaignStatusInput').value = 'scheduled';
    } else {
        document.getElementById('campaignStatusInput').value = 'sent';
    }
    document.getElementById('editNotificationForm').submit();
}

function saveAsDraft() {
    document.getElementById('campaignStatusInput').value = 'draft';
    document.getElementById('editNotificationForm').submit();
}

// Live Update of character count & phone preview
function updateLivePreview() {
    const titleInput = document.getElementById('inputNotificationTitle');
    const msgInput = document.getElementById('inputNotificationMessage');
    const titleCount = document.getElementById('titleCharCount');
    const msgCount = document.getElementById('msgCharCount');
    const previewTitle = document.getElementById('previewCardTitle');
    const previewBody = document.getElementById('previewCardBody');

    if (titleInput && titleCount) {
        titleCount.textContent = titleInput.value.length + ' / 100';
    }
    if (msgInput && msgCount) {
        msgCount.textContent = msgInput.value.length + ' / 200';
    }
    if (previewTitle && titleInput) {
        previewTitle.textContent = titleInput.value || 'GPS Camera Update';
    }
    if (previewBody && msgInput) {
        previewBody.textContent = msgInput.value || 'Notification message...';
    }

    syncAllWizardFields();
}

function switchPreviewPlatform(platform) {
    document.getElementById('btnPreviewAndroid')?.classList.toggle('active', platform === 'android');
    document.getElementById('btnPreviewIos')?.classList.toggle('active', platform === 'ios');
}

function switchRevPreviewPlatform(platform) {
    document.getElementById('btnRevPreviewAndroid')?.classList.toggle('active', platform === 'android');
    document.getElementById('btnRevPreviewIos')?.classList.toggle('active', platform === 'ios');
}

let uploadedImageDataUrl = null;
let uploadedImageFileName = null;

function handleDropzoneClick(e) {
    if (e.target.closest('#btnRemoveImage')) return;
    document.getElementById('notifImageInput').click();
}

function handleImageSelected(input) {
    const errorBox = document.getElementById('imageValidationFeedback');
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const maxBytes = 2 * 1024 * 1024; // 2 MB
    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];

    if (!allowedTypes.includes(file.type)) {
        showImageError('Invalid file type. Please upload a PNG or JPG image.');
        input.value = '';
        return;
    }

    if (file.size > maxBytes) {
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        showImageError(`Image size exceeds 2 MB limit (Selected: ${sizeMb} MB). Please choose an image smaller than 2 MB.`);
        input.value = '';
        return;
    }

    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }
    const dropzone = document.getElementById('dropzoneContainer');
    if (dropzone) dropzone.classList.remove('is-invalid');

    document.getElementById('hiddenRemoveImage').value = '0';

    const reader = new FileReader();
    reader.onload = function(e) {
        uploadedImageDataUrl = e.target.result;
        uploadedImageFileName = file.name;
        
        document.getElementById('dropzoneEmptyState')?.classList.add('d-none');
        const previewState = document.getElementById('dropzonePreviewState');
        if (previewState) {
            previewState.classList.remove('d-none');
            const thumb = document.getElementById('dropzoneThumb');
            if (thumb) thumb.src = uploadedImageDataUrl;
            const fname = document.getElementById('dropzoneFileName');
            if (fname) fname.textContent = file.name;
            const fsize = document.getElementById('dropzoneFileSize');
            if (fsize) fsize.textContent = formatBytes(file.size);
        }

        const previewImgWrap = document.getElementById('previewCardImageWrap');
        const previewImgElem = document.getElementById('previewCardImgElem');
        if (previewImgWrap && previewImgElem) {
            previewImgElem.src = uploadedImageDataUrl;
            previewImgWrap.classList.remove('d-none');
        }

        const revPreviewImgWrap = document.getElementById('revPreviewCardImageWrap');
        const revPreviewImgElem = document.getElementById('revPreviewCardImgElem');
        if (revPreviewImgWrap && revPreviewImgElem) {
            revPreviewImgElem.src = uploadedImageDataUrl;
            revPreviewImgWrap.classList.remove('d-none');
        }

        const revImageRow = document.getElementById('revImageRow');
        const revThumb = document.getElementById('revThumbnailImg');
        const revName = document.getElementById('revImageName');
        if (revImageRow && revThumb) {
            revThumb.src = uploadedImageDataUrl;
            if (revName) revName.textContent = file.name + ' (' + formatBytes(file.size) + ')';
            revImageRow.style.display = 'flex';
        }
    };
    reader.readAsDataURL(file);
}

function removeSelectedImage(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const input = document.getElementById('notifImageInput');
    if (input) input.value = '';
    uploadedImageDataUrl = null;
    uploadedImageFileName = null;
    document.getElementById('hiddenRemoveImage').value = '1';

    document.getElementById('dropzoneEmptyState')?.classList.remove('d-none');
    document.getElementById('dropzonePreviewState')?.classList.add('d-none');

    const previewImgWrap = document.getElementById('previewCardImageWrap');
    if (previewImgWrap) previewImgWrap.classList.add('d-none');

    const revPreviewImgWrap = document.getElementById('revPreviewCardImageWrap');
    if (revPreviewImgWrap) revPreviewImgWrap.classList.add('d-none');

    const revImageRow = document.getElementById('revImageRow');
    if (revImageRow) revImageRow.style.display = 'none';

    const errorBox = document.getElementById('imageValidationFeedback');
    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('d-none');
    }
    document.getElementById('dropzoneContainer')?.classList.remove('is-invalid');
}

function showImageError(msg) {
    const errorBox = document.getElementById('imageValidationFeedback');
    const dropzone = document.getElementById('dropzoneContainer');
    if (errorBox) {
        errorBox.textContent = msg;
        errorBox.classList.remove('d-none');
    }
    if (dropzone) dropzone.classList.add('is-invalid');
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Image Upload Error',
            text: msg,
            confirmButtonColor: '#2563eb'
        });
    } else {
        alert(msg);
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Audience Mode Switching
function selectAudienceMode(mode) {
    currentAudienceMode = mode;
    document.getElementById('selectedAudienceTypeInput').value = mode;

    ['all', 'segment', 'location', 'individual'].forEach(m => {
        const card = document.getElementById('cardAudience' + capitalizeFirstLetter(m));
        const radio = document.getElementById('radioAudience' + capitalizeFirstLetter(m));
        const subpanel = document.getElementById('subpanel' + capitalizeFirstLetter(m));

        if (card) card.classList.toggle('selected', m === mode);
        if (radio) radio.checked = (m === mode);
        if (subpanel) subpanel.classList.toggle('d-none', m !== mode);
    });

    const heroNumber = document.getElementById('step2HeroCount');
    const donutGreen = document.getElementById('step2DonutGreen');
    const donutBlue = document.getElementById('step2DonutBlue');
    const androidPct = document.getElementById('step2AndroidPct');
    const iosPct = document.getElementById('step2IosPct');
    const specRow1Label = document.getElementById('specRow1Label');
    const specRow1Val = document.getElementById('specRow1Val');
    const specRow2Val = document.getElementById('specRow2Val');
    const specRow3Val = document.getElementById('specRow3Val');
    const summaryTypeVal = document.getElementById('summaryTypeVal');
    const summarySegmentRow = document.getElementById('summarySegmentRow');
    const summaryLocationRow = document.getElementById('summaryLocationRow');
    const summarySelectedDevicesRow = document.getElementById('summarySelectedDevicesRow');
    const summaryCoverageVal = document.getElementById('summaryCoverageVal');
    const readyDesc = document.getElementById('audienceReadyDesc');

    let countStr = '0';
    let audLabel = 'All Eligible Installations';
    let androidCount = 0;
    let iosCount = 0;

    if (mode === 'segment') {
        const segId = document.getElementById('segmentSelectDropdown')?.value || (dbSegments[0]?.id ?? '');
        const seg = dbSegments.find(s => s.id == segId) || dbSegments[0] || {};
        const deliverable = Number(seg.deliverable_count || 0);
        const total = Number(seg.audience_size || 0);
        const excluded = Number(seg.excluded_count || Math.max(0, total - deliverable));
        const aPct = Number(seg.android_pct ?? 100);
        const iPct = Number(seg.ios_pct ?? (100 - aPct));

        countStr = deliverable.toLocaleString();
        audLabel = seg.name || 'Audience Segment';
        androidCount = Math.round(deliverable * (aPct / 100));
        iosCount = Math.max(0, deliverable - androidCount);

        const badgeSeg = document.getElementById('badgeSegmentCount');
        if (badgeSeg) badgeSeg.textContent = countStr;

        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', `${aPct} 100`);
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', `${iPct} 100`);
            donutBlue.setAttribute('stroke-dashoffset', `-${aPct}`);
        }
        if (androidPct) androidPct.textContent = `${aPct}%`;
        if (iosPct) iosPct.textContent = `${iPct}%`;
        if (specRow1Label) specRow1Label.textContent = 'Selected Audience';
        if (specRow1Val) specRow1Val.textContent = total.toLocaleString();
        if (specRow2Val) specRow2Val.textContent = excluded.toLocaleString();
        if (specRow3Val) specRow3Val.textContent = countStr;
        if (summaryTypeVal) summaryTypeVal.textContent = 'Audience Segment';
        if (summarySegmentRow) summarySegmentRow.style.display = 'flex';
        if (summaryLocationRow) summaryLocationRow.style.display = 'flex';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = seg.location_summary || 'All Locations';
        if (readyDesc) readyDesc.textContent = `${countStr} devices can receive this notification.`;
    } else if (mode === 'location') {
        const country = document.getElementById('locCountrySelect')?.value || 'India';
        const state = document.getElementById('locStateSelect')?.value || 'Tamil Nadu';
        const city = document.getElementById('locCitySelect')?.value || 'Tirunelveli';

        const locationsList = dbAudienceData.locations || [];
        const loc = locationsList.find(l => l.city === city && l.state === state && l.country === country)
                 || locationsList.find(l => l.city === city)
                 || { total_devices: 0, deliverable_count: 0, excluded_count: 0, android_pct: 100, ios_pct: 0 };

        const total = Number(loc.total_devices || 0);
        const deliverable = Number(loc.deliverable_count || 0);
        const excluded = Number(loc.excluded_count || Math.max(0, total - deliverable));
        const aPct = Number(loc.android_pct ?? 100);
        const iPct = Number(loc.ios_pct ?? (100 - aPct));

        countStr = deliverable.toLocaleString();
        audLabel = 'Location • ' + (city || 'Location');
        androidCount = Math.round(deliverable * (aPct / 100));
        iosCount = Math.max(0, deliverable - androidCount);

        const badgeLoc = document.getElementById('badgeLocationCount');
        if (badgeLoc) badgeLoc.textContent = countStr;

        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', `${aPct} 100`);
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', `${iPct} 100`);
            donutBlue.setAttribute('stroke-dashoffset', `-${aPct}`);
        }
        if (androidPct) androidPct.textContent = `${aPct}%`;
        if (iosPct) iosPct.textContent = `${iPct}%`;
        if (specRow1Label) specRow1Label.textContent = 'Selected Audience';
        if (specRow1Val) specRow1Val.textContent = total.toLocaleString();
        if (specRow2Val) specRow2Val.textContent = excluded.toLocaleString();
        if (specRow3Val) specRow3Val.textContent = countStr;
        if (summaryTypeVal) summaryTypeVal.textContent = 'Location';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'flex';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = `${city}, ${state}, ${country}`;
        if (readyDesc) readyDesc.textContent = `${countStr} devices can receive this notification.`;
    } else if (mode === 'all') {
        const allData = dbAudienceData.all || {};
        const total = Number(allData.total || 0);
        const deliverable = Number(allData.deliverable || 0);
        const excluded = Number(allData.excluded || Math.max(0, total - deliverable));
        const aPct = Number(allData.android_pct ?? 100);
        const iPct = Number(allData.ios_pct ?? (100 - aPct));

        countStr = deliverable.toLocaleString();
        audLabel = 'All Eligible Installations';
        androidCount = Math.round(deliverable * (aPct / 100));
        iosCount = Math.max(0, deliverable - androidCount);

        const badgeAll = document.getElementById('badgeAllCount');
        if (badgeAll) badgeAll.textContent = countStr;

        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', `${aPct} 100`);
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', `${iPct} 100`);
            donutBlue.setAttribute('stroke-dashoffset', `-${aPct}`);
        }
        if (androidPct) androidPct.textContent = `${aPct}%`;
        if (iosPct) iosPct.textContent = `${iPct}%`;
        if (specRow1Label) specRow1Label.textContent = 'Total Installations';
        if (specRow1Val) specRow1Val.textContent = total.toLocaleString();
        if (specRow2Val) specRow2Val.textContent = excluded.toLocaleString();
        if (specRow3Val) specRow3Val.textContent = countStr;
        if (summaryTypeVal) summaryTypeVal.textContent = 'All Eligible Installations';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'none';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'All Locations';
        if (readyDesc) readyDesc.textContent = `${countStr} devices can receive this notification.`;
    } else if (mode === 'individual') {
        const checkedBoxes = document.querySelectorAll('.individual-device-check:checked');
        const checkedCount = checkedBoxes.length;
        countStr = checkedCount.toString();
        audLabel = 'Individual Devices';

        let aCount = 0;
        let iCount = 0;
        checkedBoxes.forEach(cb => {
            const plat = cb.dataset.platform || 'Android';
            if (plat.toLowerCase() === 'ios') {
                iCount++;
            } else {
                aCount++;
            }
        });
        androidCount = aCount;
        iosCount = iCount;

        const aPct = checkedCount > 0 ? Math.round((aCount / checkedCount) * 100) : 100;
        const iPct = 100 - aPct;

        const badgeInd = document.getElementById('badgeIndividualCount');
        if (badgeInd) badgeInd.textContent = countStr;

        if (heroNumber) heroNumber.textContent = checkedCount;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', `${aPct} 100`);
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', `${iPct} 100`);
            donutBlue.setAttribute('stroke-dashoffset', `-${aPct}`);
        }
        if (androidPct) androidPct.textContent = `${aPct}%`;
        if (iosPct) iosPct.textContent = `${iPct}%`;
        if (specRow1Label) specRow1Label.textContent = 'Selected Devices';
        if (specRow1Val) specRow1Val.textContent = checkedCount;
        if (specRow2Val) specRow2Val.textContent = '0';
        if (specRow3Val) specRow3Val.textContent = checkedCount;
        if (summaryTypeVal) summaryTypeVal.textContent = 'Individual Devices';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'none';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'flex';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'Selected Devices Only';
        if (readyDesc) readyDesc.textContent = `${checkedCount} selected devices can receive this notification.`;
    }

    document.querySelectorAll('.step3-dynamic-count').forEach(el => el.textContent = countStr);
    document.getElementById('hiddenTotalAudience').value = countStr.replace(/,/g, '');
    document.getElementById('hiddenAndroidCount').value = androidCount;
    document.getElementById('hiddenIosCount').value = iosCount;
    document.getElementById('selectedAudienceLabelInput').value = audLabel;

    syncAllWizardFields();
}

function capitalizeFirstLetter(string) {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function onSegmentDropdownChange(segId) {
    const seg = dbSegments.find(s => s.id == segId) || dbSegments[0] || {};
    const title = document.getElementById('segSubpanelTitle');
    const size = document.getElementById('segStatAudienceSize');
    const deliv = document.getElementById('segStatDeliverable');
    const excl = document.getElementById('segStatExcluded');

    const audSize = Number(seg.audience_size || 0);
    const delivCount = Number(seg.deliverable_count || 0);
    const exclCount = Number(seg.excluded_count || Math.max(0, audSize - delivCount));

    if (title) title.textContent = seg.name || 'Segment';
    if (size) size.textContent = audSize.toLocaleString();
    if (deliv) deliv.textContent = delivCount.toLocaleString();
    if (excl) excl.textContent = exclCount.toLocaleString();

    const badgeSeg = document.getElementById('badgeSegmentCount');
    if (badgeSeg) badgeSeg.textContent = delivCount.toLocaleString();

    const tagCity = document.getElementById('segTagCity');
    if (tagCity) tagCity.textContent = seg.location_summary || 'All Locations';

    selectAudienceMode('segment');
}

// Cascading Location Logic
function initLocationCascading(defaultCountry = null, defaultState = null, defaultCity = null) {
    const countrySelect = document.getElementById('locCountrySelect');
    const hierarchy = dbAudienceData.location_hierarchy || {};
    const countries = Object.keys(hierarchy);
    if (!countrySelect || countries.length === 0) return;

    const selectedCountry = (defaultCountry && countries.includes(defaultCountry)) ? defaultCountry : countries[0];
    countrySelect.innerHTML = countries.map(c => `<option value="${escapeHtml(c)}" ${c === selectedCountry ? 'selected' : ''}>${escapeHtml(c)}</option>`).join('');

    populateStatesForCountry(selectedCountry, defaultState, defaultCity);
}

function onLocationCountryChange(country) {
    populateStatesForCountry(country);
    updateLocationStatsAndSummary();
    selectAudienceMode('location');
}

function populateStatesForCountry(country, defaultState = null, defaultCity = null) {
    const stateSelect = document.getElementById('locStateSelect');
    if (!stateSelect) return;

    const hierarchy = dbAudienceData.location_hierarchy || {};
    const statesMap = hierarchy[country] || {};
    const states = Object.keys(statesMap);
    if (states.length === 0) {
        stateSelect.innerHTML = '<option value="">No states</option>';
        populateCitiesForState(country, '');
        return;
    }

    const selectedState = (defaultState && states.includes(defaultState)) ? defaultState : states[0];
    stateSelect.innerHTML = states.map(s => `<option value="${escapeHtml(s)}" ${s === selectedState ? 'selected' : ''}>${escapeHtml(s)}</option>`).join('');

    populateCitiesForState(country, selectedState, defaultCity);
}

function onLocationStateChange(state) {
    const country = document.getElementById('locCountrySelect')?.value || '';
    populateCitiesForState(country, state);
    updateLocationStatsAndSummary();
    selectAudienceMode('location');
}

function populateCitiesForState(country, state, defaultCity = null) {
    const citySelect = document.getElementById('locCitySelect');
    if (!citySelect) return;

    const hierarchy = dbAudienceData.location_hierarchy || {};
    const statesMap = hierarchy[country] || {};
    const cities = statesMap[state] || [];
    if (cities.length === 0) {
        citySelect.innerHTML = '<option value="">No cities</option>';
        return;
    }

    const selectedCity = (defaultCity && cities.includes(defaultCity)) ? defaultCity : cities[0];
    citySelect.innerHTML = cities.map(c => `<option value="${escapeHtml(c)}" ${c === selectedCity ? 'selected' : ''}>${escapeHtml(c)}</option>`).join('');

    updateLocationStatsAndSummary();
}

function onLocationCityChange(city) {
    updateLocationStatsAndSummary();
    selectAudienceMode('location');
}

function updateLocationStatsAndSummary() {
    const country = document.getElementById('locCountrySelect')?.value || 'India';
    const state = document.getElementById('locStateSelect')?.value || 'Tamil Nadu';
    const city = document.getElementById('locCitySelect')?.value || 'Tirunelveli';

    const locationsList = dbAudienceData.locations || [];
    const loc = locationsList.find(l => l.city === city && l.state === state && l.country === country)
             || locationsList.find(l => l.city === city)
             || { total_devices: 0, deliverable_count: 0, excluded_count: 0, android_pct: 100, ios_pct: 0 };

    const cityDisp = document.getElementById('locDisplayCity');
    const regionDisp = document.getElementById('locDisplayRegion');
    const dev = document.getElementById('locStatDevices');
    const deliv = document.getElementById('locStatDeliverable');
    const excl = document.getElementById('locStatExcluded');

    const total = Number(loc.total_devices || 0);
    const deliverable = Number(loc.deliverable_count || 0);
    const excluded = Number(loc.excluded_count || Math.max(0, total - deliverable));

    if (cityDisp) cityDisp.textContent = city;
    if (regionDisp) regionDisp.textContent = `${state}, ${country}`;
    if (dev) dev.textContent = total.toLocaleString();
    if (deliv) deliv.textContent = deliverable.toLocaleString();
    if (excl) excl.textContent = excluded.toLocaleString();

    const badgeLoc = document.getElementById('badgeLocationCount');
    if (badgeLoc) badgeLoc.textContent = deliverable.toLocaleString();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Delivery Schedule Mode
function setDeliveryScheduleMode(mode) {
    currentDeliveryScheduleMode = mode;
    document.getElementById('selectedScheduleModeInput').value = mode;

    const cardNow = document.getElementById('cardScheduleNow');
    const cardLater = document.getElementById('cardScheduleLater');
    const radioNow = document.getElementById('radioScheduleNow');
    const radioLater = document.getElementById('radioScheduleLater');
    const subpanelNow = document.getElementById('subpanelScheduleNow');
    const subpanelLater = document.getElementById('subpanelScheduleLater');

    const artCalendar = document.getElementById('step3ArtBoxCalendar');
    const artLightning = document.getElementById('step3ArtBoxLightning');
    const summaryCardTitle = document.getElementById('step3SummaryCardTitle');

    const delTypeVal = document.getElementById('step3DeliveryTypeVal');
    const dateRow = document.getElementById('step3DateRow');
    const timeRow = document.getElementById('step3TimeRow');
    const timeZoneRow = document.getElementById('step3TimeZoneRow');
    const permRow = document.getElementById('step3PermRow');
    const fcmRow = document.getElementById('step3FcmRow');

    const readyTitle = document.getElementById('step3ReadyTitle');
    const readyDesc = document.getElementById('step3ReadyDesc');
    const quietHoursSwitch = document.getElementById('swRespectQuietHours');
    const quietHoursSub = document.getElementById('quietHoursSubtext');

    const count = document.querySelectorAll('.step3-dynamic-count')[0]?.textContent || '3';

    if (mode === 'now') {
        if (cardNow) cardNow.classList.add('selected');
        if (cardLater) cardLater.classList.remove('selected');
        if (radioNow) radioNow.checked = true;
        if (radioLater) radioLater.checked = false;
        if (subpanelNow) subpanelNow.classList.remove('d-none');
        if (subpanelLater) subpanelLater.classList.add('d-none');

        if (artCalendar) artCalendar.classList.add('d-none');
        if (artLightning) artLightning.classList.remove('d-none');
        if (summaryCardTitle) summaryCardTitle.textContent = 'Delivery Summary';

        if (delTypeVal) delTypeVal.textContent = 'Send Now';
        if (dateRow) dateRow.style.display = 'none';
        if (timeRow) {
            timeRow.style.display = 'flex';
            timeRow.querySelector('.spec-lbl').innerHTML = '<i class="fa-regular fa-clock text-muted me-1"></i> Delivery Time';
            document.getElementById('step3TimeVal').textContent = 'Immediately';
        }
        if (timeZoneRow) timeZoneRow.style.display = 'none';
        if (permRow) permRow.style.display = 'flex';
        if (fcmRow) fcmRow.style.display = 'flex';

        if (readyTitle) readyTitle.textContent = 'Ready to send';
        if (readyDesc) readyDesc.textContent = 'This notification will be sent to ' + count + ' devices immediately after final confirmation.';

        if (quietHoursSwitch) { quietHoursSwitch.checked = false; quietHoursSwitch.disabled = true; }
        if (quietHoursSub) quietHoursSub.textContent = 'Not applied for immediate sends';
    } else {
        if (cardNow) cardNow.classList.remove('selected');
        if (cardLater) cardLater.classList.add('selected');
        if (radioNow) radioNow.checked = false;
        if (radioLater) radioLater.checked = true;
        if (subpanelNow) subpanelNow.classList.add('d-none');
        if (subpanelLater) subpanelLater.classList.remove('d-none');

        if (artCalendar) artCalendar.classList.remove('d-none');
        if (artLightning) artLightning.classList.add('d-none');
        if (summaryCardTitle) summaryCardTitle.textContent = 'Schedule Summary';

        if (delTypeVal) delTypeVal.textContent = 'Scheduled';
        if (dateRow) dateRow.style.display = 'flex';
        if (timeRow) {
            timeRow.style.display = 'flex';
            timeRow.querySelector('.spec-lbl').innerHTML = '<i class="fa-regular fa-clock text-muted me-1"></i> Time';
        }
        if (timeZoneRow) timeZoneRow.style.display = 'flex';
        if (permRow) permRow.style.display = 'none';
        if (fcmRow) fcmRow.style.display = 'none';

        if (readyTitle) readyTitle.textContent = 'Schedule is ready';

        if (quietHoursSwitch) { quietHoursSwitch.disabled = false; }
        if (quietHoursSub) quietHoursSub.textContent = 'Avoid delivery between 10:00 PM and 8:00 AM';

        syncScheduleSummary();
    }

    syncAllWizardFields();
}

function getTzAbbr(tz) {
    const map = {
        'Asia/Kolkata': 'IST',
        'Asia/Dubai': 'GST',
        'UTC': 'UTC',
        'America/New_York': 'EST',
        'America/Los_Angeles': 'PST'
    };
    return map[tz] || tz;
}

function syncScheduleSummary() {
    const dateInput = document.getElementById('inputScheduledDate');
    const timeInput = document.getElementById('inputScheduledTime');
    const tzSelect = document.getElementById('selectScheduleTimeZone');
    const notice = document.getElementById('scheduledDeliveryNotice');
    const step3DateVal = document.getElementById('step3DateVal');
    const step3TimeVal = document.getElementById('step3TimeVal');
    const step3TzVal = document.getElementById('step3TimeZoneVal');
    const readyDesc = document.getElementById('step3ReadyDesc');
    const count = document.querySelectorAll('.step3-dynamic-count')[0]?.textContent || '3';

    const dateVal = dateInput?.value || '{{ date("d M Y") }}';
    const timeVal = timeInput?.value || '10:30 AM';
    const tzVal = tzSelect?.value || 'Asia/Kolkata';
    const tzAbbr = getTzAbbr(tzVal);

    if (notice) notice.textContent = 'Scheduled delivery: ' + dateVal + ' at ' + timeVal + ' ' + tzAbbr;
    if (step3DateVal) step3DateVal.textContent = dateVal;
    if (step3TimeVal) step3TimeVal.textContent = timeVal;
    if (step3TzVal) step3TzVal.textContent = tzVal + ' (' + tzAbbr + ')';
    if (readyDesc) {
        readyDesc.textContent = 'This notification will be sent to ' + count + ' devices on ' + dateVal + ' at ' + timeVal + ' ' + tzAbbr + '.';
    }
}

// Master Sync to Step 4 (Review & Save)
function syncAllWizardFields() {
    const campName = document.getElementById('inputCampaignName')?.value || '{{ $campaign->name }}';
    const notifTitle = document.getElementById('inputNotificationTitle')?.value || '{{ $campaign->title }}';
    const notifMsg = document.getElementById('inputNotificationMessage')?.value || '{{ $campaign->message }}';
    const actionVal = document.getElementById('inputAction')?.value || 'open_app';
    const dateVal = document.getElementById('inputScheduledDate')?.value || '{{ date("d M Y") }}';
    const timeVal = document.getElementById('inputScheduledTime')?.value || '10:30 AM';
    const tzVal = document.getElementById('selectScheduleTimeZone')?.value || 'Asia/Kolkata';

    // Step 4 Left column
    if (document.getElementById('revCampaignName')) document.getElementById('revCampaignName').textContent = campName;
    if (document.getElementById('revNotificationTitle')) document.getElementById('revNotificationTitle').textContent = notifTitle;
    if (document.getElementById('revNotificationMessage')) document.getElementById('revNotificationMessage').textContent = notifMsg;
    if (document.getElementById('revAction')) {
        document.getElementById('revAction').textContent = (actionVal === 'open_app') ? 'Open App' : (actionVal === 'deep_link' ? 'Deep Link' : 'Open URL');
    }

    // Step 4 Preview Card
    if (document.getElementById('revPreviewCardTitle')) document.getElementById('revPreviewCardTitle').textContent = notifTitle;
    if (document.getElementById('revPreviewCardBody')) document.getElementById('revPreviewCardBody').textContent = notifMsg;

    // Audience labels & counts
    let audLabel = 'Individual Devices';
    let count = '3';
    let aCount = 2;
    let iCount = 1;

    if (currentAudienceMode === 'segment') {
        const segId = document.getElementById('segmentSelectDropdown')?.value;
        const seg = dbSegments.find(s => s.id == segId) || dbSegments[0] || {};
        audLabel = seg.name || 'Audience Segment';
        count = Number(seg.deliverable_count || 6842).toLocaleString();
        aCount = Math.round((seg.deliverable_count || 6842) * 0.91);
        iCount = (seg.deliverable_count || 6842) - aCount;
    } else if (currentAudienceMode === 'location') {
        const city = document.getElementById('locCitySelect')?.value || 'Tirunelveli';
        const loc = dbLocations.find(l => l.city === city) || dbLocations[0] || {};
        audLabel = 'Location • ' + (loc.city || 'Tirunelveli');
        count = Number(loc.active_devices || 6292).toLocaleString();
        aCount = Math.round((loc.active_devices || 6292) * 0.92);
        iCount = (loc.active_devices || 6292) - aCount;
    } else if (currentAudienceMode === 'all') {
        audLabel = 'All Eligible Installations';
        count = Number(dbAudienceData.all?.deliverable || 7054).toLocaleString();
        aCount = Math.round((dbAudienceData.all?.deliverable || 7054) * 0.91);
        iCount = (dbAudienceData.all?.deliverable || 7054) - aCount;
    } else if (currentAudienceMode === 'individual') {
        const checked = document.querySelectorAll('.individual-device-check:checked');
        count = checked.length.toString();
        let ac = 0;
        let ic = 0;
        checked.forEach(cb => {
            if (cb.dataset.platform === 'iOS') ic++;
            else ac++;
        });
        aCount = ac;
        iCount = ic;
    }

    if (document.getElementById('revAudienceType')) document.getElementById('revAudienceType').textContent = audLabel;
    if (document.getElementById('revSelectedCount')) document.getElementById('revSelectedCount').textContent = count;
    if (document.getElementById('revEligibility')) document.getElementById('revEligibility').textContent = 'All ' + count + ' devices eligible';
    if (document.getElementById('revFinalAudience')) document.getElementById('revFinalAudience').textContent = audLabel;
    if (document.getElementById('step3NotifAudienceVal')) document.getElementById('step3NotifAudienceVal').textContent = audLabel;
    if (document.getElementById('step3PlatformsVal')) {
        document.getElementById('step3PlatformsVal').innerHTML = `<i class="fa-brands fa-android text-success"></i> Android ${aCount} · <i class="fa-brands fa-apple text-dark"></i> iOS ${iCount}`;
    }
    if (document.getElementById('revPlatformsVal')) {
        document.getElementById('revPlatformsVal').innerHTML = `<i class="fa-brands fa-android text-success"></i> Android ${aCount} · <i class="fa-brands fa-apple text-dark"></i> iOS ${iCount}`;
    }
    if (document.getElementById('revFinalPlatforms')) {
        document.getElementById('revFinalPlatforms').innerHTML = `<i class="fa-brands fa-android text-success"></i> Android ${aCount} · <i class="fa-brands fa-apple text-dark"></i> iOS ${iCount}`;
    }

    // Dynamic Device Pills in Review
    const pillsRow = document.getElementById('revDevicePillsRow');
    if (pillsRow) {
        if (currentAudienceMode === 'individual') {
            const checked = document.querySelectorAll('.individual-device-check:checked');
            let pillsHtml = '';
            checked.forEach((cb, idx) => {
                if (idx < 6) {
                    pillsHtml += `<span class="device-id-pill">${cb.value}</span>`;
                }
            });
            if (checked.length > 6) {
                pillsHtml += `<span class="badge bg-light text-muted border">+${checked.length - 6} more</span>`;
            }
            pillsRow.innerHTML = pillsHtml || '<span class="text-muted fs-11">No devices selected</span>';
            pillsRow.style.display = 'flex';
        } else {
            pillsRow.style.display = 'none';
        }
    }

    // Delivery in Review
    const revDelType = document.getElementById('revDeliveryType');
    const revDateRow = document.getElementById('revDateRow');
    const revTimeRow = document.getElementById('revTimeRow');
    const revTimeZoneRow = document.getElementById('revTimeZoneRow');
    const revQuietHours = document.getElementById('revQuietHours');
    const revPreCheckSchedule = document.getElementById('preCheckScheduleItem');
    const revFinalDelivery = document.getElementById('revFinalDelivery');
    const revFinalStatus = document.getElementById('revFinalStatus');
    const revAmberText = document.getElementById('revAmberText');
    const revReadyTitle = document.getElementById('revReadyTitle');
    const revReadyDesc = document.getElementById('revReadyDesc');

    if (currentDeliveryScheduleMode === 'now') {
        if (revDelType) revDelType.innerHTML = 'Send Now';
        if (revDateRow) revDateRow.style.display = 'none';
        if (revTimeRow) {
            revTimeRow.style.display = 'flex';
            revTimeRow.querySelector('.review-spec-label').textContent = 'Delivery Time';
            document.getElementById('revDeliveryTime').textContent = 'Immediately after confirmation';
        }
        if (revTimeZoneRow) revTimeZoneRow.style.display = 'none';
        if (revQuietHours) revQuietHours.textContent = 'Not Applied';
        if (revPreCheckSchedule) revPreCheckSchedule.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Notification permissions are enabled</span>';

        if (revFinalDelivery) revFinalDelivery.textContent = 'Send Now';
        if (revFinalStatus) revFinalStatus.textContent = 'Ready to Send';
        if (revAmberText) revAmberText.textContent = 'Updating will trigger immediate delivery to targeted devices.';
        if (revReadyTitle) revReadyTitle.textContent = 'Ready to send';
        if (revReadyDesc) revReadyDesc.textContent = 'All checks passed. This notification is ready for immediate delivery to ' + count + ' devices.';
    } else {
        if (revDelType) revDelType.innerHTML = 'Scheduled <i class="fa-regular fa-calendar text-primary ms-1"></i>';
        if (revDateRow) {
            revDateRow.style.display = 'flex';
            document.getElementById('revDeliveryDate').textContent = dateVal;
        }
        if (revTimeRow) {
            revTimeRow.style.display = 'flex';
            revTimeRow.querySelector('.review-spec-label').textContent = 'Delivery Time';
            document.getElementById('revDeliveryTime').textContent = timeVal + ' ' + getTzAbbr(tzVal);
        }
        if (revTimeZoneRow) {
            revTimeZoneRow.style.display = 'flex';
            document.getElementById('revTimeZone').textContent = tzVal + ' (' + getTzAbbr(tzVal) + ')';
        }
        if (revQuietHours) revQuietHours.textContent = document.getElementById('swRespectQuietHours')?.checked ? 'Enabled' : 'Disabled';
        if (revPreCheckSchedule) revPreCheckSchedule.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Scheduled date and time are valid</span>';

        if (revFinalDelivery) revFinalDelivery.textContent = dateVal + ' · ' + timeVal + ' ' + getTzAbbr(tzVal);
        if (revFinalStatus) revFinalStatus.textContent = 'Scheduled';
        if (revAmberText) revAmberText.textContent = 'This notification will be queued for scheduled delivery. You can edit or cancel it before the send time.';
        if (revReadyTitle) revReadyTitle.textContent = 'Ready to update';
        if (revReadyDesc) revReadyDesc.textContent = 'All checks passed. This notification will be scheduled for delivery to ' + count + ' devices on ' + dateVal + ' at ' + timeVal + ' ' + getTzAbbr(tzVal) + '.';
    }
}

// Device table interactions
function toggleAllDevices(masterCheck) {
    const isChecked = masterCheck.checked;
    document.querySelectorAll('.individual-device-check').forEach(cb => {
        cb.checked = isChecked;
    });
    onDeviceCheckChange();
}

function onDeviceCheckChange() {
    const allChecks = document.querySelectorAll('.individual-device-check');
    const checked = document.querySelectorAll('.individual-device-check:checked');
    const master = document.getElementById('selectAllDevicesCheck');
    const tableWrap = document.getElementById('deviceTableWrap');
    const errorMsg = document.getElementById('deviceSelectionError');
    const label = document.getElementById('deviceSelectedCountLabel');

    if (master) {
        master.checked = (checked.length === allChecks.length && allChecks.length > 0);
        master.indeterminate = (checked.length > 0 && checked.length < allChecks.length);
    }

    if (label) {
        label.textContent = `${checked.length} devices selected`;
    }

    if (checked.length > 0) {
        if (tableWrap) tableWrap.classList.remove('table-is-invalid');
        if (errorMsg) errorMsg.classList.add('d-none');
    }

    selectAudienceMode('individual');
}

function clearDeviceSelection() {
    document.querySelectorAll('.individual-device-check').forEach(cb => cb.checked = false);
    const master = document.getElementById('selectAllDevicesCheck');
    if (master) {
        master.checked = false;
        master.indeterminate = false;
    }
    onDeviceCheckChange();
}

function clearValidation(input) {
    if (input) input.classList.remove('is-invalid');
}

function sendTestPush() {
    const select = document.getElementById('testDeviceSelect');
    const custom = document.getElementById('testCustomFcmInput');
    const target = custom?.value.trim() || select?.value || 'Selected Device';

    const btn = document.getElementById('btnConfirmSendTest');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
    }

    setTimeout(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Send Test Push';
        }

        const modalEl = document.getElementById('sendTestNotificationModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Test Push Sent',
                text: 'Notification dispatched successfully to ' + target,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            alert('Test push notification sent successfully to ' + target);
        }
    }, 900);
}

// Flatpickr & Initialization
document.addEventListener('DOMContentLoaded', function() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#inputScheduledDate", {
            dateFormat: "d M Y",
            minDate: "today",
            defaultDate: "{{ $localScheduledAt ? $localScheduledAt->format('d M Y') : date('d M Y') }}",
            onChange: function() {
                syncScheduleSummary();
            }
        });

        flatpickr("#inputScheduledTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K",
            defaultDate: "{{ $localScheduledAt ? $localScheduledAt->format('h:i A') : '10:30 AM' }}",
            onChange: function() {
                syncScheduleSummary();
            }
        });

        document.getElementById('btnDateCalSuffix')?.addEventListener('click', function() {
            document.getElementById('inputScheduledDate')?._flatpickr?.open();
        });

        document.getElementById('btnTimeClockSuffix')?.addEventListener('click', function() {
            document.getElementById('inputScheduledTime')?._flatpickr?.open();
        });
    }

    // Initial setup
    initLocationCascading();
    selectAudienceMode(currentAudienceMode);
    setDeliveryScheduleMode(currentDeliveryScheduleMode);
    syncAllWizardFields();
});
</script>
@endpush
@endsection
