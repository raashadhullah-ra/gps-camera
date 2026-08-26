@extends('layout')

@section('title', 'Create Notification - GeoCam Admin')
@section('page_title', 'Create Notification')

@section('breadcrumbs')
    <span class="text-muted">Engagement</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.notifications.index') }}" class="text-muted text-decoration-none">Notifications</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Create Notification</span>
@endsection

@section('content')
<div class="create-notification-page">
    <form action="{{ route('admin.notifications.store') }}" method="POST" id="createNotificationForm">
        @csrf
        <input type="hidden" name="status" id="campaignStatusInput" value="draft">

        {{-- 1. Top Header Bar --}}
        <div class="wizard-header-row">
            <div class="header-left-group">
                <a href="{{ route('admin.notifications.index') }}" class="btn-back-link" title="Back to Notifications">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="wizard-main-title" id="wizardHeaderTitle">Create Notification</h1>
                    <p class="wizard-subtitle" id="wizardHeaderSubtitle">Compose and deliver a targeted Firebase push notification</p>
                </div>
            </div>
            <div class="header-right-actions">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm" id="topCancelLink" style="display: none;">
                    Cancel
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="topSaveDraftBtn" onclick="saveAsDraft()">
                    Save as Draft
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="topSendTestBtn" data-bs-toggle="modal" data-bs-target="#sendTestNotificationModal">
                    Send Test
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="topActionBtn" onclick="nextWizardStep()">
                    Review & Send
                </button>
            </div>
        </div>

        {{-- 2. 4-Step Stepper Bar (Matching User Screenshot) --}}
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
                    <span class="step-label" id="stepLabel4">Review & Send</span>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 1: CONTENT SCREEN (Screenshot 1) --}}
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
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Campaign Name</label>
                                <input type="text" name="name" id="inputCampaignName" class="form-control form-control-sm" placeholder="e.g. GPS Camera Feature Update" value="GPS Camera Feature Update" oninput="syncAllWizardFields()" required>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label text-dark fw-bold fs-12 mb-0">Notification Title</label>
                                    <span class="char-counter-text" id="titleCharCount">17 / 100</span>
                                </div>
                                <input type="text" name="title" id="inputNotificationTitle" class="form-control form-control-sm" maxlength="100" placeholder="Notification Title" value="GPS Camera Update" oninput="updateLivePreview()" required>
                            </div>
                        </div>

                        {{-- Message Body & Image Upload Dropzone --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label text-dark fw-bold fs-12 mb-0">Message</label>
                                    <span class="char-counter-text" id="msgCharCount">89 / 200</span>
                                </div>
                                <textarea name="message" id="inputNotificationMessage" rows="4" class="form-control form-control-sm" maxlength="200" placeholder="Type notification body..." oninput="updateLivePreview()" required>New GPS Camera features are now available. Explore improved location stamps and better performance.</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Notification Image <span class="text-muted fw-normal">(Optional)</span></label>
                                <div class="image-upload-dropzone" onclick="document.getElementById('notifImageInput').click()">
                                    <i class="fa-solid fa-arrow-up-from-bracket dropzone-icon"></i>
                                    <div class="dropzone-title">Upload PNG or JPG</div>
                                    <div class="dropzone-sub">Recommended 1200 × 628 px</div>
                                    <input type="file" id="notifImageInput" class="d-none" accept="image/png, image/jpeg" onchange="handleImageSelected(this)">
                                </div>
                            </div>
                        </div>

                        {{-- Action & Deep Link --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">On Tap Action</label>
                                <select name="action" id="inputAction" class="form-select form-select-sm" onchange="syncAllWizardFields()">
                                    <option value="open_app" selected>Open App</option>
                                    <option value="deep_link">Deep Link</option>
                                    <option value="open_url">Open URL</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Deep Link <span class="text-muted fw-normal">(Optional)</span></label>
                                <select name="deep_link" class="form-select form-select-sm">
                                    <option value="dashboard" selected>Dashboard</option>
                                    <option value="camera_preview">Camera Preview</option>
                                    <option value="stamp_settings">Stamp Settings</option>
                                    <option value="location_map">Location Map</option>
                                </select>
                            </div>
                        </div>

                        {{-- Advanced Data Payload (Accordion) --}}
                        <div class="accordion accordion-flush" id="accordionAdvancedPayload">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed px-0 py-2 fs-12 fw-bold text-dark shadow-none bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayload">
                                        Advanced Data Payload
                                    </button>
                                </h2>
                                <div id="collapsePayload" class="accordion-collapse collapse">
                                    <div class="accordion-body px-0 py-2">
                                        <textarea name="custom_payload" rows="2" class="form-control form-control-sm font-monospace fs-11" placeholder='{"key": "value", "screen": "updates"}'></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Audience Targeting Quick Selector --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Audience Targeting</div>
                        <div class="card-section-subtitle">Target Method</div>

                        {{-- Segmented Buttons --}}
                        <div class="target-method-btn-group">
                            <button type="button" class="btn-target-method" onclick="switchQuickTarget('all', this)">
                                <i class="fa-solid fa-globe"></i> All Devices
                            </button>
                            <button type="button" class="btn-target-method" onclick="switchQuickTarget('segment', this)">
                                <i class="fa-solid fa-users"></i> Audience Segment
                            </button>
                            <button type="button" class="btn-target-method" onclick="switchQuickTarget('location', this)">
                                <i class="fa-solid fa-location-dot"></i> Location
                            </button>
                            <button type="button" class="btn-target-method active" onclick="switchQuickTarget('individual', this)">
                                <i class="fa-solid fa-mobile-screen"></i> Specific Device
                            </button>
                        </div>

                        {{-- Dropdown & Meta Badges --}}
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Select Audience Segment</label>
                                <select name="segment_id" class="form-select form-select-sm">
                                    <option value="1">Tirunelveli Active Users</option>
                                    <option value="2">Active Users - Chennai</option>
                                    <option value="3">Tamil Nadu Android Beta</option>
                                    <option value="4">All High Activity Users</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Audience Segment Details</label>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="tag-pill">Tirunelveli</span>
                                    <span class="tag-pill green"><i class="fa-solid fa-circle fs-6"></i> Active</span>
                                    <span class="tag-pill green">Notifications Enabled</span>
                                    <span class="tag-pill">Last 30 Days</span>
                                </div>
                            </div>
                        </div>

                        {{-- Platforms & Link --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-12 fw-bold text-dark">Platform</span>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="platform_android" value="1" id="platAndroidCheck1" checked>
                                    <label class="form-check-label fs-12 text-dark" for="platAndroidCheck1">Android</label>
                                </div>
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="platform_ios" value="1" id="platIosCheck1" checked>
                                    <label class="form-check-label fs-12 text-dark" for="platIosCheck1">iOS</label>
                                </div>
                            </div>
                            <a href="{{ route('admin.segments.index') }}" class="fs-11 fw-semibold text-primary text-decoration-none">
                                View Segment Details <i class="fa-solid fa-arrow-up-right-from-square fs-10"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Card 3: Delivery Settings Quick Selector --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Delivery Settings</div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="d-flex flex-column gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="quick_delivery_type" id="delQuickSendNow" value="now" checked onchange="setDeliveryScheduleMode('now')">
                                        <label class="form-check-label fs-12 fw-bold text-dark" for="delQuickSendNow">Send Now</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="quick_delivery_type" id="delQuickSchedule" value="schedule" onchange="setDeliveryScheduleMode('schedule')">
                                        <label class="form-check-label fs-12 text-dark" for="delQuickSchedule">Schedule for Later</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Timezone</label>
                                <select name="quick_time_zone" class="form-select form-select-sm">
                                    <option value="Asia/Kolkata" selected>Asia/Kolkata (IST)</option>
                                    <option value="UTC">UTC (GMT+0)</option>
                                    <option value="America/New_York">America/New_York (EST)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-bold fs-12 mb-1">Priority</label>
                                <select name="priority" class="form-select form-select-sm">
                                    <option value="high" selected>High</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                        </div>

                        {{-- Switches Row --}}
                        <div class="d-flex align-items-center gap-4 flex-wrap mb-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="sound" id="swSound" checked>
                                <label class="form-check-label fs-12 text-dark" for="swSound">Sound</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="vibration" id="swVibration" checked>
                                <label class="form-check-label fs-12 text-dark" for="swVibration">Vibration</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="track_opens" id="swTrackOpens" checked>
                                <label class="form-check-label fs-12 text-dark" for="swTrackOpens">Track Opens</label>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" name="exclude_inactive" id="swExcludeInactive" checked>
                                <label class="form-check-label fs-12 text-dark" for="swExcludeInactive">Exclude Inactive Devices</label>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="alert-blue-light mb-0">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Only devices with active FCM tokens and notification permission will be targeted.</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Live Preview, Estimated Audience & Checklist --}}
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
                                <div class="notif-card-title" id="previewCardTitle">GPS Camera Update</div>
                                <div class="notif-card-body" id="previewCardBody">New GPS Camera features are now available. Explore improved location stamps and better performance.</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Estimated Audience Card --}}
                    <div class="estimated-audience-card">
                        <div class="fs-12 fw-bold text-dark mb-2">Estimated Audience</div>

                        <div class="row g-2 text-center mb-3">
                            <div class="col-4">
                                <div class="text-muted fs-10 fw-semibold">Eligible Devices</div>
                                <div class="fs-14 fw-bold text-primary" id="cardEligibleCount">3</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted fs-10 fw-semibold">Estimated Delivery</div>
                                <div class="fs-14 fw-bold text-success" id="cardDeliveryCount">3</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted fs-10 fw-semibold">Excluded</div>
                                <div class="fs-14 fw-bold text-danger" id="cardExcludedCount">0</div>
                            </div>
                        </div>

                        <div class="donut-chart-row">
                            <div class="donut-svg-wrap">
                                <svg width="72" height="72" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#e2e8f0" stroke-width="4"></circle>
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#22c55e" stroke-width="4" stroke-dasharray="67 100" stroke-dashoffset="0"></circle>
                                    <circle cx="18" cy="18" r="14" fill="none" stroke="#3b82f6" stroke-width="4" stroke-dasharray="33 100" stroke-dashoffset="-67"></circle>
                                </svg>
                            </div>
                            <div class="donut-legend">
                                <div class="legend-item">
                                    <span class="dot green"></span> Android
                                    <span class="legend-percent" id="legendAndroidPct">67%</span>
                                </div>
                                <div class="legend-item">
                                    <span class="dot blue"></span> iOS
                                    <span class="legend-percent" id="legendIosPct">33%</span>
                                </div>
                            </div>
                        </div>

                        <div class="spec-summary-list">
                            <div class="spec-item">
                                <span class="spec-lbl">Location</span>
                                <span class="spec-val" id="specSummaryLocation">Selected Devices Only</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">Segment</span>
                                <span class="spec-val" id="specSummarySegment">Individual</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">FCM Status</span>
                                <span class="spec-val text-success">Active Tokens Only</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Campaign Checklist --}}
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
                            <span>You can review all settings before sending.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 2: AUDIENCE SCREEN (Screenshots 2, 3, 4, 5) --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel2">
            <div class="row g-3">
                {{-- Left Column: Audience Selection --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Select Audience Type Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Select Audience</div>
                        <div class="card-section-subtitle">Choose one audience type.</div>

                        {{-- Hidden Input for form submission --}}
                        <input type="hidden" name="audience_type" id="selectedAudienceTypeInput" value="individual">

                        {{-- Option 1: All Eligible Installations (Screenshot 4) --}}
                        <div class="audience-option-card" id="cardAudienceAll" onclick="selectAudienceMode('all')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceAll" value="all">
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-globe"></i>
                                </div>
                                <div>
                                    <div class="option-title">All Eligible Installations</div>
                                    <div class="option-desc">Send to every active device with notification permission</div>
                                </div>
                            </div>
                            <div class="option-count-badge">7,054</div>
                        </div>

                        {{-- Option 2: Audience Segment (Screenshot 2) --}}
                        <div class="audience-option-card" id="cardAudienceSegment" onclick="selectAudienceMode('segment')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceSegment" value="segment">
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div>
                                    <div class="option-title">Audience Segment</div>
                                    <div class="option-desc">Send to a saved group of installations</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeSegmentCount">6,842</div>
                        </div>

                        {{-- Option 3: Location (Screenshot 3) --}}
                        <div class="audience-option-card" id="cardAudienceLocation" onclick="selectAudienceMode('location')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceLocation" value="location">
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="option-title">Location</div>
                                    <div class="option-desc">Target devices from a selected location</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeLocationCount">6,292</div>
                        </div>

                        {{-- Option 4: Individual Devices (Screenshot 5 - Default) --}}
                        <div class="audience-option-card selected" id="cardAudienceIndividual" onclick="selectAudienceMode('individual')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="audience_choice" id="radioAudienceIndividual" value="individual" checked>
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-mobile-screen"></i>
                                </div>
                                <div>
                                    <div class="option-title">Individual Devices</div>
                                    <div class="option-desc">Select specific installations</div>
                                </div>
                            </div>
                            <div class="option-count-badge" id="badgeIndividualCount">3</div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 1: Audience Segment Sub-Panel (Screenshot 2) --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 d-none" id="subpanelSegment">
                            <label class="form-label text-dark fw-bold fs-12 mb-1">Audience Segment</label>
                            <select name="segment_select_id" class="form-select form-select-sm mb-3">
                                <option value="1" selected>Active Users – Tirunelveli</option>
                                <option value="2">Chennai Camera Active Users</option>
                                <option value="3">Tamil Nadu Android Beta Users</option>
                            </select>

                            <div class="subpanel-header-title">Active Users – Tirunelveli</div>
                            <div class="meta-tags-row">
                                <span class="tag-pill"><i class="fa-solid fa-location-dot text-primary"></i> Tirunelveli</span>
                                <span class="tag-pill green"><i class="fa-solid fa-circle fs-6"></i> Active</span>
                                <span class="tag-pill"><i class="fa-regular fa-calendar"></i> Last 30 Days</span>
                                <a href="{{ route('admin.segments.index') }}" class="view-link">
                                    View Segment Details <i class="fa-solid fa-arrow-up-right-from-square fs-10"></i>
                                </a>
                            </div>

                            <div class="three-stat-columns">
                                <div class="stat-col">
                                    <div class="stat-col-val">7,054</div>
                                    <div class="stat-col-lbl">Devices</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-success">6,842</div>
                                    <div class="stat-col-lbl">Deliverable</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-muted">212</div>
                                    <div class="stat-col-lbl">Excluded</div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 2: Location Sub-Panel (Screenshot 3) --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 d-none" id="subpanelLocation">
                            <div class="subpanel-header-title mb-2">Select Location</div>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <label class="form-label text-dark fw-bold fs-11 mb-1">Country</label>
                                    <select name="loc_country" class="form-select form-select-sm">
                                        <option value="India" selected>India</option>
                                        <option value="United States">United States</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-dark fw-bold fs-11 mb-1">State / Region</label>
                                    <select name="loc_state" class="form-select form-select-sm">
                                        <option value="Tamil Nadu" selected>Tamil Nadu</option>
                                        <option value="Kerala">Kerala</option>
                                        <option value="Karnataka">Karnataka</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label text-dark fw-bold fs-11 mb-1">City</label>
                                    <select name="loc_city" class="form-select form-select-sm">
                                        <option value="Tirunelveli" selected>Tirunelveli</option>
                                        <option value="Chennai">Chennai</option>
                                        <option value="Coimbatore">Coimbatore</option>
                                    </select>
                                </div>
                            </div>

                            <div class="p-3 bg-white border rounded-2 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="modal-icon-header-circle icon-blue" style="width: 28px; height: 28px; font-size: 11px;">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div>
                                            <div class="fs-12 fw-bold text-dark">Tirunelveli</div>
                                            <div class="fs-11 text-muted">Tamil Nadu, India</div>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0)" class="fs-11 fw-semibold text-primary text-decoration-none">Change Location</a>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="tag-pill blue">City-level targeting</span>
                                    <span class="tag-pill green">Location data available</span>
                                </div>
                                <div class="three-stat-columns">
                                    <div class="stat-col">
                                        <div class="stat-col-val">6,488</div>
                                        <div class="stat-col-lbl">Devices</div>
                                    </div>
                                    <div class="stat-col">
                                        <div class="stat-col-val text-success">6,292</div>
                                        <div class="stat-col-lbl">Deliverable</div>
                                    </div>
                                    <div class="stat-col">
                                        <div class="stat-col-val text-muted">196</div>
                                        <div class="stat-col-lbl">Excluded</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 3: All Eligible Installations (Screenshot 4) --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3 d-none" id="subpanelAll">
                            <div class="subpanel-header-title">All Eligible Installations</div>
                            <div class="fs-11 text-muted mb-3">Every active installation that can receive push notifications</div>

                            <div class="three-stat-columns mb-3">
                                <div class="stat-col">
                                    <div class="stat-col-val">9,184</div>
                                    <div class="stat-col-lbl">Total Installations</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-success">7,054</div>
                                    <div class="stat-col-lbl">Eligible</div>
                                </div>
                                <div class="stat-col">
                                    <div class="stat-col-val text-muted">2,130</div>
                                    <div class="stat-col-lbl">Excluded</div>
                                </div>
                            </div>

                            <div class="fs-11 fw-bold text-dark mb-1">Included devices</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                <span class="tag-pill green"><i class="fa-solid fa-check fs-8"></i> Active installations</span>
                                <span class="tag-pill green"><i class="fa-solid fa-check fs-8"></i> Notification permission enabled</span>
                                <span class="tag-pill green"><i class="fa-solid fa-check fs-8"></i> Valid FCM token</span>
                            </div>

                            <div class="alert-blue-light mb-0">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>New eligible installations will be included automatically before delivery.</span>
                            </div>
                        </div>

                        {{-- ============================================================== --}}
                        {{-- DYNAMIC SUB-PANEL 4: Individual Devices Sub-Panel (Screenshot 5) --}}
                        {{-- ============================================================== --}}
                        <div class="audience-subpanel-card mt-3" id="subpanelIndividual">
                            <div class="subpanel-header-title">Select Individual Devices</div>
                            <div class="fs-11 text-muted mb-2">Search and select specific app installations</div>

                            {{-- Search and platform toolbar --}}
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="position-relative flex-grow-1">
                                    <i class="fa-solid fa-magnifying-glass position-absolute text-muted fs-11 filter-search-icon" style="left: 9px; top: 50%; transform: translateY(-50%);"></i>
                                    <input type="text" id="individualDeviceSearch" class="form-control form-control-sm ps-4" placeholder="Search by Installation ID, device model or location" onkeyup="filterDeviceTable()">
                                </div>
                                <select class="form-select form-select-sm" style="width: 130px;" onchange="filterDevicePlatform(this.value)">
                                    <option value="All" selected>All Platforms</option>
                                    <option value="Android">Android</option>
                                    <option value="iOS">iOS</option>
                                </select>
                            </div>

                            {{-- Device table --}}
                            <div class="table-responsive border rounded-2 bg-white mb-2">
                                <table class="mini-devices-table" id="deviceSelectTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 36px; text-align: center;">
                                                <input type="checkbox" class="form-check-input" id="checkAllDevices" checked onchange="toggleAllDevices(this)">
                                            </th>
                                            <th>Device</th>
                                            <th>Installation ID</th>
                                            <th>Platform</th>
                                            <th>Location</th>
                                            <th>Last Active</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="device-row" data-platform="Android">
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="target_device_ids[]" value="INS-A7K4-92PQ" class="form-check-input individual-device-check" checked onchange="updateIndividualSelection()">
                                            </td>
                                            <td class="device-model-name">Samsung Galaxy S24</td>
                                            <td class="install-id-code">INS-A7K4-92PQ</td>
                                            <td><i class="fa-brands fa-android text-success me-1"></i> Android</td>
                                            <td>Tirunelveli</td>
                                            <td class="text-muted">2 min ago</td>
                                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Eligible</span></td>
                                        </tr>
                                        <tr class="device-row" data-platform="iOS">
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="target_device_ids[]" value="INS-M3X8-71LR" class="form-check-input individual-device-check" checked onchange="updateIndividualSelection()">
                                            </td>
                                            <td class="device-model-name">iPhone 16 Pro</td>
                                            <td class="install-id-code">INS-M3X8-71LR</td>
                                            <td><i class="fa-brands fa-apple text-dark me-1"></i> iOS</td>
                                            <td>Chennai</td>
                                            <td class="text-muted">18 min ago</td>
                                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Eligible</span></td>
                                        </tr>
                                        <tr class="device-row" data-platform="Android">
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="target_device_ids[]" value="INS-H5T7-83KC" class="form-check-input individual-device-check" checked onchange="updateIndividualSelection()">
                                            </td>
                                            <td class="device-model-name">Google Pixel 9</td>
                                            <td class="install-id-code">INS-H5T7-83KC</td>
                                            <td><i class="fa-brands fa-android text-success me-1"></i> Android</td>
                                            <td>Coimbatore</td>
                                            <td class="text-muted">3 hours ago</td>
                                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Eligible</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex align-items-center justify-content-between fs-11">
                                <span class="fw-bold text-dark" id="deviceSelectedCountLabel">3 devices selected</span>
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

                        <div class="est-hero-number" id="step2HeroCount">3</div>
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
                                <span class="spec-val" id="specRow1Val">3</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">Excluded</span>
                                <span class="spec-val" id="specRow2Val">0</span>
                            </div>
                            <div class="spec-item">
                                <span class="spec-lbl">Estimated Delivery</span>
                                <span class="spec-val fw-bold" id="specRow3Val">3</span>
                            </div>
                        </div>
                    </div>

                    {{-- Audience Summary Specs Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-3">Audience Summary</div>

                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-bullhorn text-muted me-1"></i> Type</span>
                                <span class="spec-val" id="summaryTypeVal">Individual Devices</span>
                            </div>
                            <div class="spec-item py-1" id="summarySegmentRow" style="display: none;">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Segment</span>
                                <span class="spec-val" id="summarySegmentVal">Active Users – Tirunelveli</span>
                            </div>
                            <div class="spec-item py-1" id="summarySelectedDevicesRow">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen text-muted me-1"></i> Selected Devices</span>
                                <span class="spec-val" id="summarySelectedDevicesVal">3</span>
                            </div>
                            <div class="spec-item py-1" id="summaryLocationRow" style="display: none;">
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
                            <div class="fw-normal fs-11" id="audienceReadyDesc">3 selected devices can receive this notification.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 3: SCHEDULE SCREEN (Screenshots 1 & 2) --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel3">
            <div class="row g-3">
                {{-- Left Column: Schedule Selection --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Delivery Schedule Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title">Delivery Schedule</div>
                        <div class="card-section-subtitle">Choose when to send this notification.</div>

                        {{-- Hidden Input for form submission --}}
                        <input type="hidden" name="delivery_schedule_mode" id="selectedScheduleModeInput" value="schedule">

                        {{-- Option 1: Send Now (Screenshot 2) --}}
                        <div class="audience-option-card" id="cardScheduleNow" onclick="setDeliveryScheduleMode('now')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="schedule_choice" id="radioScheduleNow" value="now">
                                </div>
                                <div class="option-icon-square blue">
                                    <i class="fa-solid fa-bolt"></i>
                                </div>
                                <div>
                                    <div class="option-title">Send Now</div>
                                    <div class="option-desc">Send immediately after final review</div>
                                </div>
                            </div>
                        </div>

                        {{-- Option 2: Schedule for Later (Screenshot 1 - Default Selected) --}}
                        <div class="audience-option-card selected" id="cardScheduleLater" onclick="setDeliveryScheduleMode('schedule')">
                            <div class="option-card-left">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="radio" name="schedule_choice" id="radioScheduleLater" value="schedule" checked>
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
                        <div class="audience-subpanel-card mt-3" id="subpanelScheduleLater">
                            <div class="subpanel-header-title mb-2">Schedule Date & Time</div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold fs-11 mb-1">Delivery Date</label>
                                    <div class="schedule-input-wrap">
                                        <input type="text" name="scheduled_date" id="inputScheduledDate" class="form-control schedule-input" value="26 Aug 2026" onchange="syncScheduleSummary()">
                                        <i class="fa-regular fa-calendar schedule-icon-suffix"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-bold fs-11 mb-1">Delivery Time</label>
                                    <div class="schedule-input-wrap">
                                        <input type="text" name="scheduled_time" id="inputScheduledTime" class="form-control schedule-input" value="10:30 AM" onchange="syncScheduleSummary()">
                                        <i class="fa-regular fa-clock schedule-icon-suffix"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold fs-11 mb-1">Time Zone</label>
                                <select name="schedule_time_zone" id="selectScheduleTimeZone" class="form-select form-select-sm" onchange="syncScheduleSummary()">
                                    <option value="Asia/Kolkata" selected>(UTC+05:30) Asia/Kolkata</option>
                                    <option value="UTC">(UTC+00:00) UTC</option>
                                    <option value="America/New_York">(UTC-05:00) America/New_York</option>
                                </select>
                            </div>

                            <div class="alert-blue-light mb-2">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>The notification will be sent according to the selected time zone.</span>
                            </div>

                            <div class="fs-12 fw-bold text-dark" id="scheduledDeliveryNotice">
                                Scheduled delivery: Wednesday, 26 August 2026 at 10:30 AM IST
                            </div>
                        </div>

                        {{-- Sub-Panel B: Immediate Delivery Info (When Send Now selected) --}}
                        <div class="audience-subpanel-card mt-3 d-none" id="subpanelScheduleNow">
                            <div class="subpanel-header-title mb-2">Immediate Delivery</div>
                            <div class="d-flex align-items-start gap-3 p-2 bg-white rounded-2 border mb-3">
                                <div class="schedule-hero-art-box p-0">
                                    <div class="art-circle-icon" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fs-12 fw-bold text-dark mb-1">Ready to send immediately</div>
                                    <div class="fs-11 text-muted mb-2">The notification will be queued as soon as you confirm it in Review & Send.</div>
                                    <div class="alert-blue-light mb-2 py-1">
                                        <i class="fa-solid fa-circle-info"></i>
                                        <span>Delivery usually begins within a few seconds. Actual receipt may depend on device connectivity.</span>
                                    </div>
                                    <div class="fs-12 fw-bold text-primary">
                                        Estimated delivery: <span class="step3-dynamic-count">3</span> eligible devices
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
                                        <div class="fs-12 fw-bold text-dark">Respect quiet hours</div>
                                        <div class="fs-11 text-muted" id="quietHoursSubtext">Avoid delivery between 10:00 PM and 8:00 AM</div>
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="respect_quiet_hours" id="swRespectQuietHours" checked>
                                </div>
                            </div>

                            {{-- Send only to eligible devices --}}
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="modal-icon-header-circle icon-blue" style="width: 32px; height: 32px; font-size: 12px;">
                                        <i class="fa-solid fa-mobile-screen"></i>
                                    </div>
                                    <div>
                                        <div class="fs-12 fw-bold text-dark">Send only to eligible devices</div>
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
                                    <span class="fs-12 fw-bold text-dark">Message Expiry</span>
                                    <select name="message_expiry" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="24" selected>24 hours</option>
                                        <option value="48">48 hours</option>
                                        <option value="72">72 hours</option>
                                        <option value="168">7 days</option>
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
                        <div class="fs-12 fw-bold text-dark mb-2" id="step3SummaryCardTitle">Schedule Summary</div>

                        {{-- Art box (Calendar art vs Lightning Clock art) --}}
                        <div class="schedule-hero-art-box" id="step3ArtBoxCalendar">
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

                        <div class="schedule-hero-art-box d-none" id="step3ArtBoxLightning">
                            <div class="art-circle-icon">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                        </div>

                        {{-- Specs list --}}
                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-regular fa-calendar text-muted me-1"></i> Delivery Type</span>
                                <span class="spec-val" id="step3DeliveryTypeVal">Scheduled</span>
                            </div>
                            <div class="spec-item py-1" id="step3DateRow">
                                <span class="spec-lbl"><i class="fa-regular fa-calendar-days text-muted me-1"></i> Date</span>
                                <span class="spec-val" id="step3DateVal">26 Aug 2026</span>
                            </div>
                            <div class="spec-item py-1" id="step3TimeRow">
                                <span class="spec-lbl"><i class="fa-regular fa-clock text-muted me-1"></i> Time</span>
                                <span class="spec-val" id="step3TimeVal">10:30 AM</span>
                            </div>
                            <div class="spec-item py-1" id="step3TimeZoneRow">
                                <span class="spec-lbl"><i class="fa-solid fa-globe text-muted me-1"></i> Time Zone</span>
                                <span class="spec-val" id="step3TimeZoneVal">Asia/Kolkata</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Estimated Delivery</span>
                                <span class="spec-val" id="step3EstDeliveryVal"><span class="step3-dynamic-count">3</span> devices</span>
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
                                <span class="spec-val" id="step3NotifTitleVal">GPS Camera Update</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Audience</span>
                                <span class="spec-val" id="step3NotifAudienceVal">Individual Devices</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen text-muted me-1"></i> Selected Devices</span>
                                <span class="spec-val"><span class="step3-dynamic-count">3</span></span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen-button text-muted me-1"></i> Platforms</span>
                                <span class="spec-val"><i class="fa-brands fa-android text-success"></i> Android 2 · <i class="fa-brands fa-apple text-dark"></i> iOS 1</span>
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
                            <div id="step3ReadyTitle">Schedule is ready</div>
                            <div class="fw-normal fs-11" id="step3ReadyDesc">This notification will be sent to 3 devices on 26 Aug 2026 at 10:30 AM IST.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================================== --}}
        {{-- STEP 4: REVIEW & SEND SCREEN (Screenshots 3 & 4) --}}
        {{-- ====================================================================== --}}
        <div class="wizard-step-panel d-none" id="stepPanel4">
            <div class="row g-3">
                {{-- Left Column: Review Sections --}}
                <div class="col-lg-7 col-xl-8">
                    {{-- 1. Notification Content Card --}}
                    <div class="wizard-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="card-section-title mb-0">Notification Content</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(1)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Content
                            </a>
                        </div>

                        <div class="d-flex align-items-start gap-3">
                            <div class="review-app-logo-box">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="review-spec-grid flex-grow-1">
                                <div class="review-spec-row">
                                    <div class="review-spec-label">Campaign Name</div>
                                    <div class="review-spec-val" id="revCampaignName">GPS Camera Feature Update</div>
                                </div>
                                <div class="review-spec-row">
                                    <div class="review-spec-label">Notification Title</div>
                                    <div class="review-spec-val" id="revNotificationTitle">GPS Camera Update</div>
                                </div>
                                <div class="review-spec-row">
                                    <div class="review-spec-label">Message</div>
                                    <div class="review-spec-val" id="revNotificationMessage">New GPS Camera features are now available. Explore improved location stamps and better performance.</div>
                                </div>
                                <div class="review-spec-row">
                                    <div class="review-spec-label">Action</div>
                                    <div class="review-spec-val" id="revAction">Open App</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Audience Card --}}
                    <div class="wizard-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="card-section-title mb-0">Audience</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(2)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Audience
                            </a>
                        </div>

                        <div class="review-spec-grid">
                            <div class="review-spec-row">
                                <div class="review-spec-label">Audience Type</div>
                                <div class="review-spec-val" id="revAudienceType">Individual Devices</div>
                            </div>
                            <div class="review-spec-row">
                                <div class="review-spec-label">Selected Devices</div>
                                <div class="review-spec-val" id="revSelectedCount">3</div>
                            </div>
                            <div class="review-spec-row">
                                <div class="review-spec-label">Platforms</div>
                                <div class="review-spec-val"><i class="fa-brands fa-android text-success"></i> Android 2 · <i class="fa-brands fa-apple text-dark"></i> iOS 1</div>
                            </div>
                            <div class="review-spec-row">
                                <div class="review-spec-label">Eligibility</div>
                                <div class="review-spec-val text-success" id="revEligibility">All 3 devices eligible</div>
                            </div>
                        </div>

                        {{-- Device ID Pills --}}
                        <div class="device-id-pills-row" id="revDevicePillsRow">
                            <span class="device-id-pill">INS-A7K4-92PQ</span>
                            <span class="device-id-pill">INS-M3X8-71LR</span>
                            <span class="device-id-pill">INS-H5T7-83KC</span>
                        </div>
                    </div>

                    {{-- 3. Delivery Card --}}
                    <div class="wizard-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="card-section-title mb-0">Delivery</div>
                            <a href="javascript:void(0)" class="review-edit-link" onclick="goToStep(3)">
                                <i class="fa-solid fa-pen fs-10"></i> Edit Schedule
                            </a>
                        </div>

                        <div class="review-spec-grid">
                            <div class="review-spec-row">
                                <div class="review-spec-label">Delivery Type</div>
                                <div class="review-spec-val" id="revDeliveryType">Scheduled <i class="fa-regular fa-calendar text-primary ms-1"></i></div>
                            </div>
                            <div class="review-spec-row" id="revDateRow">
                                <div class="review-spec-label">Delivery Date</div>
                                <div class="review-spec-val" id="revDeliveryDate">26 Aug 2026</div>
                            </div>
                            <div class="review-spec-row" id="revTimeRow">
                                <div class="review-spec-label">Delivery Time</div>
                                <div class="review-spec-val" id="revDeliveryTime">10:30 AM IST</div>
                            </div>
                            <div class="review-spec-row" id="revTimeZoneRow">
                                <div class="review-spec-label">Time Zone</div>
                                <div class="review-spec-val" id="revTimeZone">Asia/Kolkata</div>
                            </div>
                            <div class="review-spec-row">
                                <div class="review-spec-label">Message Expiry</div>
                                <div class="review-spec-val">24 hours</div>
                            </div>
                            <div class="review-spec-row">
                                <div class="review-spec-label">Quiet Hours</div>
                                <div class="review-spec-val" id="revQuietHours">Enabled</div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Pre-send Checks Card --}}
                    <div class="wizard-card">
                        <div class="card-section-title mb-3">Pre-send Checks</div>
                        <div class="pre-send-checklist-row">
                            <div class="pre-check-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Notification content is complete</span>
                            </div>
                            <div class="pre-check-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span><span class="step3-dynamic-count">3</span> selected devices are eligible</span>
                            </div>
                            <div class="pre-check-item" id="preCheckScheduleItem">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Scheduled date and time are valid</span>
                            </div>
                            <div class="pre-check-item">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Notification permissions and FCM tokens are valid</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Preview, Final Summary & Action Alerts --}}
                <div class="col-lg-5 col-xl-4">
                    {{-- 1. Compact Notification Preview Card --}}
                    <div class="wizard-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 fw-bold text-dark">Notification Preview</span>
                        </div>

                        <div class="preview-tabs-row mb-3">
                            <button type="button" class="preview-tab-btn active" id="btnRevPreviewAndroid" onclick="switchRevPreviewPlatform('android')">
                                <i class="fa-brands fa-android me-1"></i> Android
                            </button>
                            <button type="button" class="preview-tab-btn" id="btnRevPreviewIos" onclick="switchRevPreviewPlatform('ios')">
                                <i class="fa-brands fa-apple me-1"></i> iOS
                            </button>
                        </div>

                        <div class="review-preview-compact-card">
                            <div class="compact-app-logo">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="compact-preview-content">
                                <div class="compact-top-row">
                                    <span class="compact-app-name">GPS Camera</span>
                                    <span class="compact-time">now</span>
                                </div>
                                <div class="compact-title" id="revPreviewCardTitle">GPS Camera Update</div>
                                <div class="compact-body" id="revPreviewCardBody">New GPS Camera features are now available. Explore improved location stamps and better performance.</div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Final Summary Card --}}
                    <div class="wizard-card">
                        <div class="fs-12 fw-bold text-dark mb-3">Final Summary</div>
                        <div class="spec-summary-list border-0 pt-0">
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-users text-muted me-1"></i> Audience</span>
                                <span class="spec-val" id="revFinalAudience">Individual Devices</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-paper-plane text-muted me-1"></i> Estimated Delivery</span>
                                <span class="spec-val"><span class="step3-dynamic-count">3</span> devices</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-regular fa-calendar text-muted me-1"></i> Delivery</span>
                                <span class="spec-val" id="revFinalDelivery">26 Aug 2026 · 10:30 AM IST</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-mobile-screen text-muted me-1"></i> Platforms</span>
                                <span class="spec-val"><i class="fa-brands fa-android text-success"></i> Android 2 · <i class="fa-brands fa-apple text-dark"></i> iOS 1</span>
                            </div>
                            <div class="spec-item py-1">
                                <span class="spec-lbl"><i class="fa-solid fa-circle-check text-muted me-1"></i> Status</span>
                                <span class="spec-val text-success" id="revFinalStatus">Ready to Schedule</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Amber Notice Alert --}}
                    <div class="alert-amber-light" id="revAmberAlert">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span id="revAmberText">This notification will be queued for scheduled delivery. You can edit or cancel it before the send time.</span>
                    </div>

                    {{-- 4. Green Ready Callout --}}
                    <div class="alert-audience-ready" id="revReadyAlert">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <div id="revReadyTitle">Ready to schedule</div>
                            <div class="fw-normal fs-11" id="revReadyDesc">All checks passed. This notification will be sent to 3 devices on 26 Aug 2026 at 10:30 AM IST.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Sticky Bottom Footer Bar --}}
        <div class="wizard-bottom-bar">
            <div class="autosave-indicator">
                <i class="fa-solid fa-check"></i>
                <span id="autoSaveText">Campaign auto-saved just now</span>
            </div>

            <div class="bottom-actions-group">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnBottomBack" onclick="handleBottomBack()" style="display: none;">
                    Back
                </button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary" id="btnBottomCancel">
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

{{-- MODAL: Send Test Notification Modal --}}
<div class="modal fade" id="sendTestNotificationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered notification-modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon-header-circle icon-blue">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h5 class="modal-header-title">Send Test Notification</h5>
                        <p class="modal-header-subtitle">Send a test push notification to a specific test device.</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-custom">
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold fs-11 mb-1">Target Test Device ID or FCM Token</label>
                    <input type="text" class="form-control form-control-sm" placeholder="e.g. INS-A7K4-92PQ or FCM token..." value="INS-A7K4-92PQ">
                </div>
                <div class="alert-blue-light mb-0">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>This test notification will be sent immediately using the current title and body preview.</span>
                </div>
            </div>
            <div class="modal-footer modal-footer-custom">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal" onclick="alert('Test notification sent successfully!')">
                    Send Test Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStep = 1;
let currentAudienceMode = 'individual';
let currentDeliveryScheduleMode = 'schedule'; // 'now' or 'schedule'

// Step Navigation
function goToStep(step) {
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
        if (titleEl) titleEl.textContent = 'Create Notification';
        if (subEl) subEl.textContent = 'Compose and deliver a targeted Firebase push notification';
        if (topBtn) { topBtn.style.display = 'inline-flex'; topBtn.textContent = 'Review & Send'; }
        if (topCancelLink) topCancelLink.style.display = 'none';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'inline-flex';
        if (topSendTestBtn) topSendTestBtn.style.display = 'inline-flex';
        if (bottomPrimary) { bottomPrimary.innerHTML = 'Continue to Audience'; }
        if (bottomBack) bottomBack.style.display = 'none';
        if (bottomCancel) bottomCancel.style.display = 'inline-flex';
        if (sendTestBtn) sendTestBtn.style.display = 'inline-flex';
    } else if (step === 2) {
        if (titleEl) titleEl.textContent = 'Create Notification';
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
        if (titleEl) titleEl.textContent = 'Create Notification';
        if (subEl) subEl.textContent = 'Choose when this notification should be delivered';
        if (topBtn) { topBtn.style.display = 'none'; }
        if (topCancelLink) topCancelLink.style.display = 'inline-flex';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'none';
        if (topSendTestBtn) topSendTestBtn.style.display = 'none';
        if (bottomPrimary) { bottomPrimary.innerHTML = 'Continue to Review & Send'; }
        if (bottomBack) bottomBack.style.display = 'inline-flex';
        if (bottomCancel) bottomCancel.style.display = 'none';
        if (sendTestBtn) sendTestBtn.style.display = 'none';
    } else if (step === 4) {
        if (titleEl) titleEl.textContent = 'Create Notification';
        if (subEl) {
            subEl.textContent = (currentDeliveryScheduleMode === 'schedule') 
                ? 'Review all details before scheduling this notification' 
                : 'Review all details before sending this notification';
        }
        if (topBtn) { topBtn.style.display = 'none'; }
        if (topCancelLink) topCancelLink.style.display = 'inline-flex';
        if (topSaveDraftBtn) topSaveDraftBtn.style.display = 'none';
        if (topSendTestBtn) topSendTestBtn.style.display = 'none';

        if (bottomPrimary) {
            if (currentDeliveryScheduleMode === 'schedule') {
                bottomPrimary.innerHTML = '<i class="fa-solid fa-calendar-check me-1"></i> Schedule Notification';
            } else {
                bottomPrimary.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Send Notification';
            }
        }
        if (bottomBack) bottomBack.style.display = 'inline-flex';
        if (bottomCancel) bottomCancel.style.display = 'none';
        if (sendTestBtn) sendTestBtn.style.display = 'none';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
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
    if (currentDeliveryScheduleMode === 'schedule') {
        document.getElementById('campaignStatusInput').value = 'scheduled';
    } else {
        document.getElementById('campaignStatusInput').value = 'sent';
    }
    document.getElementById('createNotificationForm').submit();
}

function saveAsDraft() {
    document.getElementById('campaignStatusInput').value = 'draft';
    document.getElementById('createNotificationForm').submit();
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

function handleImageSelected(input) {
    if (input.files && input.files[0]) {
        alert('Image selected: ' + input.files[0].name);
    }
}

// Audience Mode Switching (Screenshots 2, 3, 4, 5)
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

    let countStr = '3';

    if (mode === 'segment') {
        countStr = '6,842';
        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', '91 100');
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', '9 100');
            donutBlue.setAttribute('stroke-dashoffset', '-91');
        }
        if (androidPct) androidPct.textContent = '91%';
        if (iosPct) iosPct.textContent = '9%';
        if (specRow1Label) specRow1Label.textContent = 'Selected Audience';
        if (specRow1Val) specRow1Val.textContent = '7,054';
        if (specRow2Val) specRow2Val.textContent = '212';
        if (specRow3Val) specRow3Val.textContent = '6,842';
        if (summaryTypeVal) summaryTypeVal.textContent = 'Audience Segment';
        if (summarySegmentRow) summarySegmentRow.style.display = 'flex';
        if (summaryLocationRow) summaryLocationRow.style.display = 'flex';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'Tirunelveli';
        if (readyDesc) readyDesc.textContent = '6,842 devices can receive this notification.';
    } else if (mode === 'location') {
        countStr = '6,292';
        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', '92 100');
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', '8 100');
            donutBlue.setAttribute('stroke-dashoffset', '-92');
        }
        if (androidPct) androidPct.textContent = '92%';
        if (iosPct) iosPct.textContent = '8%';
        if (specRow1Label) specRow1Label.textContent = 'Selected Audience';
        if (specRow1Val) specRow1Val.textContent = '6,488';
        if (specRow2Val) specRow2Val.textContent = '196';
        if (specRow3Val) specRow3Val.textContent = '6,292';
        if (summaryTypeVal) summaryTypeVal.textContent = 'Location';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'flex';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'Tirunelveli, Tamil Nadu, India';
        if (readyDesc) readyDesc.textContent = '6,292 devices can receive this notification.';
    } else if (mode === 'all') {
        countStr = '7,054';
        if (heroNumber) heroNumber.textContent = countStr;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', '91 100');
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', '9 100');
            donutBlue.setAttribute('stroke-dashoffset', '-91');
        }
        if (androidPct) androidPct.textContent = '91%';
        if (iosPct) iosPct.textContent = '9%';
        if (specRow1Label) specRow1Label.textContent = 'Total Installations';
        if (specRow1Val) specRow1Val.textContent = '9,184';
        if (specRow2Val) specRow2Val.textContent = '2,130';
        if (specRow3Val) specRow3Val.textContent = '7,054';
        if (summaryTypeVal) summaryTypeVal.textContent = 'All Eligible Installations';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'none';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'none';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'All Locations';
        if (readyDesc) readyDesc.textContent = '7,054 devices can receive this notification.';
    } else if (mode === 'individual') {
        const checkedCount = document.querySelectorAll('.individual-device-check:checked').length;
        countStr = checkedCount.toString();
        if (heroNumber) heroNumber.textContent = checkedCount;
        if (donutGreen) donutGreen.setAttribute('stroke-dasharray', '67 100');
        if (donutBlue) {
            donutBlue.setAttribute('stroke-dasharray', '33 100');
            donutBlue.setAttribute('stroke-dashoffset', '-67');
        }
        if (androidPct) androidPct.textContent = '67%';
        if (iosPct) iosPct.textContent = '33%';
        if (specRow1Label) specRow1Label.textContent = 'Selected Devices';
        if (specRow1Val) specRow1Val.textContent = checkedCount;
        if (specRow2Val) specRow2Val.textContent = '0';
        if (specRow3Val) specRow3Val.textContent = checkedCount;
        if (summaryTypeVal) summaryTypeVal.textContent = 'Individual Devices';
        if (summarySegmentRow) summarySegmentRow.style.display = 'none';
        if (summaryLocationRow) summaryLocationRow.style.display = 'none';
        if (summarySelectedDevicesRow) summarySelectedDevicesRow.style.display = 'flex';
        if (summaryCoverageVal) summaryCoverageVal.textContent = 'Selected Devices Only';
        if (readyDesc) readyDesc.textContent = checkedCount + ' selected devices can receive this notification.';
    }

    document.querySelectorAll('.step3-dynamic-count').forEach(el => el.textContent = countStr);
    syncAllWizardFields();
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function switchQuickTarget(target, btn) {
    document.querySelectorAll('.btn-target-method').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    selectAudienceMode(target);
}

// Delivery Schedule Mode (Screenshots 1 & 2 for Step 3)
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
        if (quietHoursSub) quietHoursSub.textContent = 'Not applied when Send Now is selected';
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
            document.getElementById('step3TimeVal').textContent = document.getElementById('inputScheduledTime')?.value || '10:30 AM';
        }
        if (timeZoneRow) timeZoneRow.style.display = 'flex';
        if (permRow) permRow.style.display = 'none';
        if (fcmRow) fcmRow.style.display = 'none';

        if (readyTitle) readyTitle.textContent = 'Schedule is ready';
        if (readyDesc) readyDesc.textContent = 'This notification will be sent to ' + count + ' devices on 26 Aug 2026 at 10:30 AM IST.';

        if (quietHoursSwitch) { quietHoursSwitch.checked = true; quietHoursSwitch.disabled = false; }
        if (quietHoursSub) quietHoursSub.textContent = 'Avoid delivery between 10:00 PM and 8:00 AM';
    }

    syncScheduleSummary();
    syncAllWizardFields();
}

function syncScheduleSummary() {
    const dateVal = document.getElementById('inputScheduledDate')?.value || '26 Aug 2026';
    const timeVal = document.getElementById('inputScheduledTime')?.value || '10:30 AM';
    const tzVal = document.getElementById('selectScheduleTimeZone')?.value || 'Asia/Kolkata';
    const notice = document.getElementById('scheduledDeliveryNotice');
    const readyDesc = document.getElementById('step3ReadyDesc');
    const count = document.querySelectorAll('.step3-dynamic-count')[0]?.textContent || '3';

    if (notice) notice.textContent = 'Scheduled delivery: Wednesday, ' + dateVal + ' at ' + timeVal + ' IST';
    if (document.getElementById('step3DateVal')) document.getElementById('step3DateVal').textContent = dateVal;
    if (document.getElementById('step3TimeVal') && currentDeliveryScheduleMode === 'schedule') {
        document.getElementById('step3TimeVal').textContent = timeVal;
    }
    if (document.getElementById('step3TimeZoneVal')) document.getElementById('step3TimeZoneVal').textContent = tzVal;
    if (readyDesc && currentDeliveryScheduleMode === 'schedule') {
        readyDesc.textContent = 'This notification will be sent to ' + count + ' devices on ' + dateVal + ' at ' + timeVal + ' IST.';
    }
}

// Master Sync to Step 4 (Review & Send)
function syncAllWizardFields() {
    const campName = document.getElementById('inputCampaignName')?.value || 'GPS Camera Feature Update';
    const notifTitle = document.getElementById('inputNotificationTitle')?.value || 'GPS Camera Update';
    const notifMsg = document.getElementById('inputNotificationMessage')?.value || 'New GPS Camera features are now available. Explore improved location stamps and better performance.';
    const actionVal = document.getElementById('inputAction')?.value || 'open_app';
    const dateVal = document.getElementById('inputScheduledDate')?.value || '26 Aug 2026';
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

    // Audience labels in Review
    let audLabel = 'Individual Devices';
    let count = '3';
    if (currentAudienceMode === 'segment') {
        audLabel = 'Audience Segment';
        count = '6,842';
    } else if (currentAudienceMode === 'location') {
        audLabel = 'Location';
        count = '6,292';
    } else if (currentAudienceMode === 'all') {
        audLabel = 'All Eligible Installations';
        count = '7,054';
    }

    if (document.getElementById('revAudienceType')) document.getElementById('revAudienceType').textContent = audLabel;
    if (document.getElementById('revSelectedCount')) document.getElementById('revSelectedCount').textContent = count;
    if (document.getElementById('revEligibility')) document.getElementById('revEligibility').textContent = 'All ' + count + ' devices eligible';
    if (document.getElementById('revFinalAudience')) document.getElementById('revFinalAudience').textContent = audLabel;

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
        if (revAmberText) revAmberText.textContent = 'Sending cannot be undone. Confirm all details before continuing.';
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
            document.getElementById('revDeliveryTime').textContent = timeVal + ' IST';
        }
        if (revTimeZoneRow) {
            revTimeZoneRow.style.display = 'flex';
            document.getElementById('revTimeZone').textContent = tzVal;
        }
        if (revQuietHours) revQuietHours.textContent = 'Enabled';
        if (revPreCheckSchedule) revPreCheckSchedule.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Scheduled date and time are valid</span>';

        if (revFinalDelivery) revFinalDelivery.textContent = dateVal + ' · ' + timeVal + ' IST';
        if (revFinalStatus) revFinalStatus.textContent = 'Scheduled';
        if (revAmberText) revAmberText.textContent = 'This notification will be queued for scheduled delivery. You can edit or cancel it before the send time.';
        if (revReadyTitle) revReadyTitle.textContent = 'Ready to schedule';
        if (revReadyDesc) revReadyDesc.textContent = 'All checks passed. This notification will be sent to ' + count + ' devices on ' + dateVal + ' at ' + timeVal + ' IST.';
    }
}

// Individual Device Selection helpers
function toggleAllDevices(master) {
    document.querySelectorAll('.individual-device-check').forEach(cb => {
        cb.checked = master.checked;
    });
    updateIndividualSelection();
}

function updateIndividualSelection() {
    const count = document.querySelectorAll('.individual-device-check:checked').length;
    const label = document.getElementById('deviceSelectedCountLabel');
    const badge = document.getElementById('badgeIndividualCount');
    if (label) label.textContent = count + ' devices selected';
    if (badge) badge.textContent = count;

    if (currentAudienceMode === 'individual') {
        const hero = document.getElementById('step2HeroCount');
        const specVal = document.getElementById('specRow1Val');
        const estVal = document.getElementById('specRow3Val');
        const readyDesc = document.getElementById('audienceReadyDesc');
        if (hero) hero.textContent = count;
        if (specVal) specVal.textContent = count;
        if (estVal) estVal.textContent = count;
        if (readyDesc) readyDesc.textContent = count + ' selected devices can receive this notification.';
        document.querySelectorAll('.step3-dynamic-count').forEach(el => el.textContent = count);
    }
    syncAllWizardFields();
}

function clearDeviceSelection() {
    document.querySelectorAll('.individual-device-check').forEach(cb => cb.checked = false);
    const master = document.getElementById('checkAllDevices');
    if (master) master.checked = false;
    updateIndividualSelection();
}

function filterDeviceTable() {
    const query = document.getElementById('individualDeviceSearch').value.toLowerCase();
    document.querySelectorAll('.device-row').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}

function filterDevicePlatform(platform) {
    document.querySelectorAll('.device-row').forEach(row => {
        if (platform === 'All') {
            row.style.display = '';
        } else {
            row.style.display = (row.dataset.platform === platform) ? '' : 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview();
    selectAudienceMode('individual');
    setDeliveryScheduleMode('schedule');
});
</script>
@endpush
