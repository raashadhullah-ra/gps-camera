@extends('layout')

@section('title', 'Create Ad - GeoCam Admin')
@section('page_title', 'Create Ad')

@section('breadcrumbs')
    <span>App Control</span>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <a href="{{ route('admin.ads.index') }}" class="text-decoration-none text-gray-800">Ads Management</a>
    <i class="fa-solid fa-chevron-right fs-10 mx-1"></i>
    <span class="active-crumb">Create Ad</span>
@endsection

@section('content')
<div class="create-ad-page">
    <form id="createAdForm" action="{{ route('admin.ads.custom.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf

        <!-- Page Header Row -->
        <div class="page-header-row mb-4">
            <div class="header-title-group">
                <h1 class="page-main-title">Create Ad</h1>
                <p class="page-main-subtitle">Configure custom campaign creative, target audience, format and scheduling</p>
            </div>
            <div class="header-actions-group">
                <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Ads</span>
                </a>
            </div>
        </div>

        <!-- Wizard Stepper -->
        <div class="wizard-steps">
            <div class="step-indicator active" id="step1-indicator">
                <span class="step-circle bg-primary text-white">1</span>
                <span>Source & Format</span>
            </div>
            <div class="step-divider"></div>
            <div class="step-indicator text-muted" id="step2-indicator">
                <span class="step-circle bg-light border text-muted">2</span>
                <span>Creative & Placement</span>
            </div>
            <div class="step-divider"></div>
            <div class="step-indicator text-muted" id="step3-indicator">
                <span class="step-circle bg-light border text-muted">3</span>
                <span>Audience & Schedule</span>
            </div>
            <div class="step-divider"></div>
            <div class="step-indicator text-muted" id="step4-indicator">
                <span class="step-circle bg-light border text-muted">4</span>
                <span>Review</span>
            </div>
        </div>

        <!-- Step 1: Source & Format -->
        <div id="step-1" class="wizard-step">
            <div class="row g-4">
                <!-- Main Form Column -->
                <div class="col-lg-8">
                    <div class="wizard-card">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Ad Source & Basic Information</h5>
                                <p class="card-subtitle">Choose the advertising provider, format and basic campaign identifiers</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- 1. Source Selection -->
                            <label class="form-label fw-semibold text-dark fs-13 mb-2">Ad Source Provider <span class="text-danger">*</span></label>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="ad-source-card disabled">
                                        <input class="form-check-input source-radio" type="radio" name="source" id="sourceAdmob" value="Google AdMob" disabled>
                                        <div class="source-icon-wrap">
                                            <i class="fa-brands fa-google text-danger"></i>
                                        </div>
                                        <div class="source-info">
                                            <div class="source-title">Google AdMob</div>
                                            <p class="source-desc">Use a connected AdMob ad unit</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="ad-source-card active" onclick="document.getElementById('sourceCustom').checked = true">
                                        <input class="form-check-input source-radio" type="radio" name="source" id="sourceCustom" value="Custom Campaign" checked>
                                        <div class="source-icon-wrap">
                                            <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
                                        </div>
                                        <div class="source-info">
                                            <div class="source-title">Custom Campaign</div>
                                            <p class="source-desc">Upload and manage your own creative</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Campaign Identifiers -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark fs-13 mb-1" for="inputAdName">Ad Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="inputAdName" class="form-control" placeholder="Premium Upgrade Campaign" value="Premium Upgrade Campaign" required>
                                    <div class="invalid-feedback">Please enter a campaign name.</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold text-dark fs-13 mb-0" for="inputCampaignId">Campaign ID</label>
                                        <button type="button" class="btn btn-link p-0 fs-11 text-decoration-none text-primary" onclick="regenerateCampaignId()" title="Generate new random ID">
                                            <i class="fa-solid fa-arrows-rotate me-1"></i> Auto Generate
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" name="campaign_id" id="inputCampaignId" class="form-control font-monospace" placeholder="CUSTOM-PRM-021" value="{{ $generatedCampaignId ?? 'CUSTOM-PRM-021' }}">
                                        <button type="button" class="btn btn-outline-secondary" onclick="regenerateCampaignId()" title="Generate new ID">
                                            <i class="fa-solid fa-dice"></i>
                                        </button>
                                    </div>
                                    <div class="form-text fs-11 text-muted">Format: CUSTOM-ABC-123</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark fs-13 mb-1" for="selectObjective">Objective <span class="text-danger">*</span></label>
                                    <select name="objective" id="selectObjective" class="form-select" required>
                                        <option value="App Subscription" selected>App Subscription</option>
                                        <option value="Website Traffic">Website Traffic</option>
                                        <option value="Brand Awareness">Brand Awareness</option>
                                    </select>
                                    <div class="invalid-feedback">Please choose an objective.</div>
                                </div>
                            </div>

                            <!-- 3. Format Selection -->
                            <label class="form-label fw-semibold text-dark fs-13 mb-2">Select Format <span class="text-danger">*</span></label>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="format-card select-format" data-value="Banner">
                                        <div class="format-icon-wrap">
                                            <i class="fa-solid fa-rectangle-ad"></i>
                                        </div>
                                        <div class="format-title">Banner</div>
                                        <p class="format-desc">Display a banner ad</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="format-card select-format active" data-value="Native">
                                        <div class="format-icon-wrap">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                        <div class="format-title">Native</div>
                                        <p class="format-desc">Blend ads into content</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="format-card select-format" data-value="Interstitial">
                                        <div class="format-icon-wrap">
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        </div>
                                        <div class="format-title">Interstitial</div>
                                        <p class="format-desc">Full screen ad experience</p>
                                    </div>
                                </div>
                                <input type="hidden" name="format" id="adFormatInput" value="Native" required>
                            </div>

                            <!-- 4. Publishing Switch -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border mb-4 bg-light">
                                <div>
                                    <div class="fw-semibold text-dark fs-13">Enable ad immediately after publishing</div>
                                    <div class="text-muted fs-11">Campaign will start delivering impressions once validated and published</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked style="width: 2.4em; height: 1.25em;">
                                </div>
                            </div>

                            <!-- 5. Campaign Goal & Destination -->
                            <div class="sub-panel-box">
                                <div class="sub-panel-title">Campaign Goal & Destination</div>
                                <div class="row g-3">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label fs-12 fw-semibold text-dark mb-1">Conversion Goal</label>
                                        <select name="conversion_goal" class="form-select">
                                            <option value="Premium subscription" selected>Premium subscription</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label fs-12 fw-semibold text-dark mb-1">Destination Type</label>
                                        <select name="destination_type" class="form-select">
                                            <option value="In-app screen" selected>In-app screen</option>
                                            <option value="External URL">External URL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label fs-12 fw-semibold text-dark mb-1">Destination</label>
                                        <select name="destination" class="form-select">
                                            <option value="Premium Upgrade" selected>Premium Upgrade</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label fs-12 fw-semibold text-dark mb-1">Tracking Event</label>
                                        <select name="tracking_event" class="form-select">
                                            <option value="premium_upgrade_opened" selected>premium_upgrade_opened</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary Column -->
                <div class="col-lg-4">
                    <div class="summary-sidebar-card">
                        <div class="card-header">
                            <h6 class="card-title">Configuration Summary</h6>
                        </div>
                        <div class="card-body">
                            <ul class="summary-list">
                                <li>
                                    <span class="summary-label"><i class="fa-solid fa-cloud-arrow-up text-primary"></i> Source</span>
                                    <span class="summary-value" id="summarySource">Custom Campaign</span>
                                </li>
                                <li>
                                    <span class="summary-label"><i class="fa-solid fa-newspaper text-primary"></i> Format</span>
                                    <span class="summary-value" id="summaryFormat">Native</span>
                                </li>
                                <li>
                                    <span class="summary-label"><i class="fa-solid fa-bullseye text-primary"></i> Objective</span>
                                    <span class="summary-value" id="summaryObjective">App Subscription</span>
                                </li>
                                <li>
                                    <span class="summary-label"><i class="fa-solid fa-file-lines text-warning"></i> Status</span>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-11">Draft</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="summary-sidebar-card">
                        <div class="card-header">
                            <h6 class="card-title">Setup Checklist</h6>
                        </div>
                        <div class="card-body">
                            <ul class="checklist-list">
                                <li><i class="fa-solid fa-circle-check text-success"></i> <span>Source selected</span></li>
                                <li><i class="fa-solid fa-circle-check text-success" id="chkAdName"></i> <span>Ad name added</span></li>
                                <li><i class="fa-solid fa-circle-check text-success"></i> <span>Format selected</span></li>
                                <li><i class="fa-solid fa-circle-check text-success"></i> <span>Objective added</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="wizard-action-bar">
                <a href="{{ route('admin.ads.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="button" class="btn btn-primary next-step" data-target="2">Continue to Creative</button>
                </div>
            </div>
        </div>

        <!-- Step 2: Creative & Placement -->
        <div id="step-2" class="wizard-step d-none">
            <div class="row g-4">
                <!-- Creative Content Form -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Creative Content</h5>
                                <p class="card-subtitle">Media assets and copywriting</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="creativeImageInput">Creative Image <span class="text-danger">*</span></label>
                                <input type="file" name="creative_image" id="creativeImageInput" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                                <input type="hidden" name="cropped_image_data" id="croppedImageData">
                                <div class="invalid-feedback" id="imageInvalidFeedback">Please upload a valid creative image (JPG, PNG, WebP up to 5MB).</div>
                                <div class="d-flex justify-content-between text-muted fs-11 mt-1">
                                    <span>JPG, PNG, WebP • Max 5MB</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-link p-0 text-primary fs-11 d-none" id="reCropImageBtn">
                                            <i class="fa-solid fa-crop-simple me-1"></i>Re-Crop
                                        </button>
                                        <button type="button" class="btn btn-link p-0 text-danger fs-11 d-none" id="removeImageBtn">
                                            <i class="fa-solid fa-trash-can me-1"></i>Clear Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-dark fs-13 mb-0" for="liveHeadline">Headline <span class="text-danger">*</span></label>
                                    <span class="text-muted fs-11" id="headlineCharCount">29 / 60</span>
                                </div>
                                <input type="text" name="headline" class="form-control" placeholder="Enjoy GPS Camera Without Ads" id="liveHeadline" value="Enjoy GPS Camera Without Ads" maxlength="60" required>
                                <div class="invalid-feedback">Please enter a headline (max 60 characters).</div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-dark fs-13 mb-0" for="liveDesc">Description</label>
                                    <span class="text-muted fs-11" id="descCharCount">53 / 120</span>
                                </div>
                                <textarea name="description" class="form-control" rows="2" placeholder="Upgrade once for an uninterrupted camera experience." id="liveDesc" maxlength="120">Upgrade once for an uninterrupted camera experience.</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="liveCTA">Call to Action <span class="text-danger">*</span></label>
                                <select name="call_to_action" class="form-select" id="liveCTA" required>
                                    <option value="Upgrade Now" selected>Upgrade Now</option>
                                    <option value="Learn More">Learn More</option>
                                    <option value="Download">Download</option>
                                    <option value="Get Started">Get Started</option>
                                    <option value="Shop Now">Shop Now</option>
                                </select>
                                <div class="invalid-feedback">Please select a call to action.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="liveDestUrl">Destination URL <span class="text-danger">*</span></label>
                                <input type="text" name="destination_url" class="form-control" id="liveDestUrl" placeholder="app://subscription/upgrade" value="app://subscription/upgrade" required>
                                <div class="invalid-feedback">Please enter a destination screen link or URL.</div>
                            </div>
                            <div>
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="inputAltText">Alt Text (Accessibility)</label>
                                <input type="text" name="alt_text" id="inputAltText" class="form-control" placeholder="Premium upgrade ad description" value="Premium upgrade ad to remove ads from GPS Camera app">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Preview -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Live Preview</h5>
                                <p class="card-subtitle">Real-time mobile ad preview</p>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                            <div class="card shadow-sm mx-auto" style="width: 260px; border-radius: 28px; border: 8px solid #1e293b; background: #fff; overflow: hidden;">
                                <!-- Mobile Phone Top Notch -->
                                <div style="background: #1e293b; height: 16px; display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 36px; height: 4px; background: #475569; border-radius: 4px;"></div>
                                </div>
                                
                                <div class="p-3">
                                    <!-- Sponsored badge -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-10 fw-semibold rounded-pill">Sponsored · Ad</span>
                                        <i class="fa-solid fa-circle-info text-muted fs-11" title="Ad Info"></i>
                                    </div>

                                    <!-- Media Container with Live Image Preview -->
                                    <div class="position-relative rounded-3 mb-2 overflow-hidden border" style="height: 140px; background: linear-gradient(135deg, #1e40af, #3b82f6);">
                                        <div id="previewImagePlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-2 text-center">
                                            <i class="fa-regular fa-image fs-1 mb-1 text-white-50"></i>
                                            <span class="fs-11 text-white-50">Image preview will appear here</span>
                                        </div>
                                        <img id="previewImage" src="" alt="Creative Preview" class="w-100 h-100 d-none" style="object-fit: cover;">
                                    </div>

                                    <!-- Headline -->
                                    <h6 class="mb-1 fw-bold fs-13 text-dark text-truncate" id="previewHeadline">Enjoy GPS Camera Without Ads</h6>

                                    <!-- Description -->
                                    <p class="text-secondary mb-3 fs-11" id="previewDesc" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.35; min-height: 30px;">Upgrade once for an uninterrupted camera experience.</p>

                                    <!-- CTA Button -->
                                    <button type="button" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" id="previewCTA">Upgrade Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placement -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Ad Placement</h5>
                                <p class="card-subtitle">Screens where ad is displayed</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input placement-checkbox" type="checkbox" name="placements[]" value="Gallery Screen" id="placeGallery" checked>
                                <label class="form-check-label fs-13 text-dark" for="placeGallery">Gallery Screen</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input placement-checkbox" type="checkbox" name="placements[]" value="Camera Settings" id="placeSettings" checked>
                                <label class="form-check-label fs-13 text-dark" for="placeSettings">Camera Settings</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input placement-checkbox" type="checkbox" name="placements[]" value="Photo Preview" id="placePreview">
                                <label class="form-check-label fs-13 text-dark" for="placePreview">Photo Preview</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input placement-checkbox" type="checkbox" name="placements[]" value="Save Success" id="placeSuccess">
                                <label class="form-check-label fs-13 text-dark" for="placeSuccess">Save Success</label>
                            </div>

                            <div class="text-danger fs-11 d-none mb-3" id="placementFeedback">
                                <i class="fa-solid fa-circle-exclamation me-1"></i> Please select at least one ad placement.
                            </div>

                            <div class="sub-panel-box mt-3">
                                <div class="sub-panel-title">Placement Rules</div>
                                <ul class="list-unstyled mb-0 small">
                                    <li class="d-flex justify-content-between mb-2">
                                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> Position</span>
                                        <span class="text-muted">Inline content</span>
                                    </li>
                                    <li class="d-flex justify-content-between mb-2">
                                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> Safe Area</span>
                                        <span class="text-muted">Passed</span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span><i class="fa-solid fa-circle-check text-success me-1"></i> Camera controls</span>
                                        <span class="text-muted">No overlap</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="wizard-action-bar">
                <button type="button" class="btn btn-outline-secondary prev-step" data-target="1">Back</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="button" class="btn btn-primary next-step" data-target="3">Continue to Audience</button>
                </div>
            </div>
        </div>

        <!-- Step 3: Audience & Schedule -->
        <div id="step-3" class="wizard-step d-none">
            <div class="row g-4">
                <!-- Audience Targeting -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Audience Targeting</h5>
                                <p class="card-subtitle">Select eligible user groups</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="selectAudienceSegment">Audience Segment <span class="text-danger">*</span></label>
                                <select name="audience_segment_id" id="selectAudienceSegment" class="form-select" required>
                                    <option value="0" selected>All Users</option>
                                    @if(isset($segments) && count($segments) > 0)
                                        @foreach($segments as $segment)
                                            <option value="{{ $segment->id }}">{{ $segment->name }} ({{ number_format($segment->estimated_reach ?? $segment->audience_count ?? 0) }} users)</option>
                                        @endforeach
                                    @else
                                        <option value="1">Free Users</option>
                                        <option value="2">High-Frequency Users</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">Please choose an audience segment.</div>
                            </div>
                            <div class="mb-3 position-relative">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-dark fs-13 mb-0" id="labelCountries">Target Countries</label>
                                    <span class="text-muted fs-11" id="selectedCountriesCount">All Selected</span>
                                </div>

                                <div class="dropdown custom-multi-select-dropdown" id="countriesDropdownWrap">
                                    <button class="btn form-select text-start d-flex align-items-center justify-content-between w-100 bg-white" type="button" id="countriesDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <span class="text-truncate d-block pe-2" id="countriesSelectedText">All Available Countries ({{ count($countries ?? []) }})</span>
                                    </button>

                                    <div class="dropdown-menu shadow-lg border rounded-3 p-2 w-100" style="max-height: 280px; overflow-y: auto;" aria-labelledby="countriesDropdownBtn">
                                        <!-- Search & Quick actions -->
                                        <div class="px-1 pb-2 mb-2 border-bottom">
                                            <input type="text" class="form-control form-control-sm mb-2 fs-12" id="countrySearchInput" placeholder="Search countries..." oninput="filterCountryOptions(this.value)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <button type="button" class="btn btn-link p-0 fs-11 text-decoration-none text-primary fw-semibold" onclick="toggleAllCountries(true)">Select All</button>
                                                <button type="button" class="btn btn-link p-0 fs-11 text-decoration-none text-muted" onclick="toggleAllCountries(false)">Clear</button>
                                            </div>
                                        </div>

                                        <!-- Country list with checkboxes -->
                                        <div class="country-options-list" id="countryOptionsList">
                                            @php
                                                $countryFlags = [
                                                    'India' => '🇮🇳',
                                                    'United States' => '🇺🇸',
                                                    'Brazil' => '🇧🇷',
                                                    'Indonesia' => '🇮🇩',
                                                    'Japan' => '🇯🇵',
                                                    'UAE' => '🇦🇪',
                                                    'United Kingdom' => '🇬🇧',
                                                ];
                                            @endphp
                                            @if(isset($countries) && count($countries) > 0)
                                                @foreach($countries as $country)
                                                    <label class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded-2 fs-12 country-option-item" style="cursor: pointer;">
                                                        <input class="form-check-input mt-0 country-checkbox" type="checkbox" name="countries[]" value="{{ $country }}" checked onchange="updateCountrySelectUI()">
                                                        <span class="fs-14">{{ $countryFlags[$country] ?? '🌐' }}</span>
                                                        <span class="country-name-text text-dark flex-grow-1">{{ $country }}</span>
                                                    </label>
                                                @endforeach
                                            @else
                                                <label class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded-2 fs-12 country-option-item" style="cursor: pointer;">
                                                    <input class="form-check-input mt-0 country-checkbox" type="checkbox" name="countries[]" value="India" checked onchange="updateCountrySelectUI()">
                                                    <span class="fs-14">🇮🇳</span>
                                                    <span class="country-name-text text-dark flex-grow-1">India</span>
                                                </label>
                                                <label class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded-2 fs-12 country-option-item" style="cursor: pointer;">
                                                    <input class="form-check-input mt-0 country-checkbox" type="checkbox" name="countries[]" value="United States" checked onchange="updateCountrySelectUI()">
                                                    <span class="fs-14">🇺🇸</span>
                                                    <span class="country-name-text text-dark flex-grow-1">United States</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text fs-11 text-muted">Select one or more target countries from locations.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1 d-block">Platforms <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input platform-checkbox" type="checkbox" name="platforms[]" value="Android" id="platAndroid" checked>
                                    <label class="form-check-label fs-13 text-dark" for="platAndroid">Android</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input platform-checkbox" type="checkbox" name="platforms[]" value="iOS" id="platiOS" checked>
                                    <label class="form-check-label fs-13 text-dark" for="platiOS">iOS</label>
                                </div>
                                <div class="text-danger fs-11 d-none mt-1" id="platformFeedback">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> Please select at least one target platform.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Minimum App Version</label>
                                <select name="min_app_version" class="form-select">
                                    <option value="2.3.9+" selected>2.3.9+</option>
                                </select>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="exclude_subscribed" id="excSub" value="1" checked>
                                <label class="form-check-label fs-13 text-dark" for="excSub">Exclude subscribed users</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frequency & Limits -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Frequency & Limits</h5>
                                <p class="card-subtitle">Impression caps and budget</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Frequency Cap</label>
                                <input type="number" name="frequency_cap" class="form-control" value="3" min="1" max="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Per User</label>
                                <select name="frequency_per_user" class="form-select">
                                    <option value="Per day" selected>Per day</option>
                                    <option value="Per week">Per week</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Maximum Impressions</label>
                                <input type="number" name="max_impressions" class="form-control" value="500000" min="1000">
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="stop_at_limit" id="stopLimit" value="1" checked>
                                <label class="form-check-label fs-13 text-dark" for="stopLimit">Stop when limit is reached</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Daily Budget (optional)</label>
                                <input type="number" name="daily_budget" class="form-control" placeholder="5000" min="0">
                            </div>
                            <div>
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Pacing</label>
                                <select name="pacing" class="form-select">
                                    <option value="Even delivery" selected>Even delivery</option>
                                    <option value="As fast as possible">As fast as possible</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Schedule -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Campaign Schedule</h5>
                                <p class="card-subtitle">Active dates and timing</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="startDateInput">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="startDateInput" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <div class="invalid-feedback">Start date is required.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Start Time</label>
                                <input type="time" name="start_time" class="form-control" value="09:00">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1" for="endDateInput">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="endDateInput" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                                <div class="invalid-feedback" id="dateFeedback">End date is required and cannot be before start date.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">End Time</label>
                                <input type="time" name="end_time" class="form-control" value="23:59">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-13 mb-1">Time Zone</label>
                                <select name="timezone" class="form-select">
                                    <option value="Asia/Kolkata" selected>Asia/Kolkata</option>
                                </select>
                            </div>
                            <div class="alert alert-primary py-2 px-3 fs-11 mb-0">
                                <i class="fa-solid fa-shield-halved me-1"></i> Consent-aware delivery applied.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="wizard-action-bar">
                <button type="button" class="btn btn-outline-secondary prev-step" data-target="2">Back</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="button" class="btn btn-primary next-step" data-target="4">Continue to Review</button>
                </div>
            </div>
        </div>

        <!-- Step 4: Review -->
        <div id="step-4" class="wizard-step d-none">
            <div class="row g-4">
                <!-- Review Details -->
                <div class="col-lg-8">
                    <div class="wizard-card">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Review Campaign Settings</h5>
                                <p class="card-subtitle">Confirm campaign, creative, placement and delivery options</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- 4 Summary Metric Cards -->
                            <div class="row g-3 text-center mb-4">
                                <div class="col-md-3">
                                    <div class="p-3 rounded-3 border bg-light h-100">
                                        <i class="fa-solid fa-cloud-arrow-up text-primary fs-3 mb-2"></i>
                                        <h6 class="mb-1 fs-12 text-secondary">Campaign</h6>
                                        <p class="fw-bold text-dark fs-13 mb-0" id="reviewCampaignName">Premium Upgrade</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-3 border bg-light h-100">
                                        <i class="fa-solid fa-newspaper text-primary fs-3 mb-2"></i>
                                        <h6 class="mb-1 fs-12 text-secondary">Format</h6>
                                        <p class="fw-bold text-dark fs-13 mb-0" id="reviewFormat">Native</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-3 border bg-light h-100">
                                        <i class="fa-solid fa-location-crosshairs text-primary fs-3 mb-2"></i>
                                        <h6 class="mb-1 fs-12 text-secondary">Placement</h6>
                                        <p class="fw-bold text-dark fs-13 mb-0" id="reviewPlacements">Gallery, Settings</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded-3 border bg-light h-100">
                                        <i class="fa-solid fa-users text-primary fs-3 mb-2"></i>
                                        <h6 class="mb-1 fs-12 text-secondary">Audience</h6>
                                        <p class="fw-bold text-dark fs-13 mb-0" id="reviewAudience">Free Users</p>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success d-flex align-items-center gap-2 mb-4 py-2 px-3">
                                <i class="fa-solid fa-circle-check fs-5"></i>
                                <span class="fs-13">Campaign parameters are ready. Review creative copy and submit to publish.</span>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="confirmRules" required>
                                <label class="form-check-label fw-semibold text-dark fs-13" for="confirmRules">
                                    I confirm that the creative media, targeting rules, and schedule are accurate. <span class="text-danger">*</span>
                                </label>
                                <div class="invalid-feedback">You must confirm the settings before publishing.</div>
                            </div>

                            <div class="sub-panel-box">
                                <div class="sub-panel-title">Publishing Options</div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="publish_option" id="pubNow" value="Publish now" checked>
                                    <label class="form-check-label fs-13" for="pubNow">
                                        <strong>Publish now</strong> — Start delivering immediately after submission.
                                    </label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="publish_option" id="pubSched" value="Schedule activation">
                                    <label class="form-check-label fs-13" for="pubSched">
                                        <strong>Schedule activation</strong> — Deliver according to configured campaign start date and time.
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Preview Column -->
                <div class="col-lg-4">
                    <div class="wizard-card h-100 mb-0">
                        <div class="card-header">
                            <div>
                                <h5 class="card-title">Final Preview</h5>
                                <p class="card-subtitle">How users will see this ad</p>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                            <div class="card shadow-sm mx-auto" style="width: 260px; border-radius: 28px; border: 8px solid #1e293b; background: #fff; overflow: hidden;">
                                <div style="background: #1e293b; height: 16px; display: flex; align-items: center; justify-content: center;">
                                    <div style="width: 36px; height: 4px; background: #475569; border-radius: 4px;"></div>
                                </div>
                                <div class="p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fs-10 fw-semibold rounded-pill">Sponsored · Ad</span>
                                        <i class="fa-solid fa-circle-info text-muted fs-11"></i>
                                    </div>
                                    <div class="position-relative rounded-3 mb-2 overflow-hidden border" style="height: 140px; background: linear-gradient(135deg, #1e40af, #3b82f6);">
                                        <div id="finalPreviewImagePlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white p-2 text-center">
                                            <i class="fa-regular fa-image fs-1 mb-1 text-white-50"></i>
                                            <span class="fs-11 text-white-50">Creative Media</span>
                                        </div>
                                        <img id="finalPreviewImage" src="" alt="Creative Preview" class="w-100 h-100 d-none" style="object-fit: cover;">
                                    </div>
                                    <h6 class="mb-1 fw-bold fs-13 text-dark text-truncate" id="finalPreviewHeadline">Enjoy GPS Camera Without Ads</h6>
                                    <p class="text-secondary mb-3 fs-11" id="finalPreviewDesc" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.35; min-height: 30px;">Upgrade once for an uninterrupted camera experience.</p>
                                    <button type="button" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" id="finalPreviewCTA">Upgrade Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="wizard-action-bar">
                <button type="button" class="btn btn-outline-secondary prev-step" data-target="3">Back</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary">Save as Draft</button>
                    <button type="submit" class="btn btn-primary px-4">Publish Ad</button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Crop Creative Image Modal -->
<div class="modal fade" id="cropCreativeModal" tabindex="-1" aria-labelledby="cropCreativeModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold fs-15 text-dark" id="cropCreativeModalLabel">
                    <i class="fa-solid fa-crop-simple text-primary me-2"></i>Crop Creative Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="cropper-container-wrapper mb-3 rounded-3 overflow-hidden shadow-inner" style="max-height: 420px; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 280px;">
                    <img id="creativeImageToCrop" src="" alt="Source Image" style="max-width: 100%; max-height: 400px; display: block;">
                </div>
                
                <!-- Toolbar Controls -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2">
                    <div class="btn-group btn-group-sm" id="aspectRatioButtons">
                        <button type="button" class="btn btn-outline-secondary active" id="cropRatioBanner" title="Banner (1.91:1 / 1200x628)">
                            <i class="fa-solid fa-rectangle-ad me-1"></i> 1.91:1 (Banner)
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="cropRatio169" title="16:9 Landscape">
                            16:9
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="cropRatio11" title="1:1 Square">
                            1:1
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="cropRatioFree" title="Free Crop">
                            Free
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" id="cropZoomInBtn" title="Zoom In">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="cropZoomOutBtn" title="Zoom Out">
                                <i class="fa-solid fa-magnifying-glass-minus"></i>
                            </button>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" id="cropRotateLeftBtn" title="Rotate Left">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="cropRotateRightBtn" title="Rotate Right">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="cropResetBtn" title="Reset">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="fs-12 text-muted text-center mt-3">
                    Drag box corners to crop. Supports JPG, PNG, and WebP formats. Minimum 1200 × 628 px recommended.
                </div>
            </div>
            <div class="modal-footer bg-light border-top px-4 py-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" id="cancelCropBtn">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="applyCropBtn">
                    <i class="fa-solid fa-check me-1"></i> Crop & Apply to Live Preview
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Global Campaign ID Generator function
    function regenerateCampaignId() {
        const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let randLetters = '';
        for (let i = 0; i < 3; i++) {
            randLetters += letters.charAt(Math.floor(Math.random() * letters.length));
        }
        const randDigits = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
        const newId = `CUSTOM-${randLetters}-${randDigits}`;
        const input = document.getElementById('inputCampaignId');
        if (input) {
            input.value = newId;
            input.classList.remove('is-invalid');
        }
        if (window.showToast) {
            window.showToast('info', 'Campaign ID Generated', `Assigned: ${newId}`);
        }
    }

    // Target Countries Multi-Select Handlers
    function updateCountrySelectUI() {
        const checkboxes = document.querySelectorAll('.country-checkbox');
        const checked = document.querySelectorAll('.country-checkbox:checked');
        const textEl = document.getElementById('countriesSelectedText');
        const countEl = document.getElementById('selectedCountriesCount');

        if (!textEl) return;

        if (checked.length === 0) {
            textEl.innerText = 'No countries selected';
            textEl.className = 'text-truncate d-block pe-2 text-muted';
            if (countEl) countEl.innerText = '0 selected';
        } else if (checked.length === checkboxes.length) {
            textEl.innerText = `All Available Countries (${checkboxes.length})`;
            textEl.className = 'text-truncate d-block pe-2 text-dark fw-semibold';
            if (countEl) countEl.innerText = 'All Selected';
        } else if (checked.length <= 2) {
            const names = Array.from(checked).map(c => c.value).join(', ');
            textEl.innerText = names;
            textEl.className = 'text-truncate d-block pe-2 text-dark';
            if (countEl) countEl.innerText = `${checked.length} selected`;
        } else {
            const names = Array.from(checked).slice(0, 2).map(c => c.value).join(', ');
            textEl.innerText = `${names} +${checked.length - 2} more`;
            textEl.className = 'text-truncate d-block pe-2 text-dark';
            if (countEl) countEl.innerText = `${checked.length} selected`;
        }
    }

    function toggleAllCountries(select) {
        document.querySelectorAll('.country-checkbox').forEach(cb => {
            cb.checked = select;
        });
        updateCountrySelectUI();
    }

    function filterCountryOptions(query) {
        const q = (query || '').toLowerCase().trim();
        document.querySelectorAll('.country-option-item').forEach(item => {
            const text = item.querySelector('.country-name-text')?.innerText.toLowerCase() || '';
            item.style.display = text.includes(q) ? 'flex' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const form = document.getElementById('createAdForm');
        const nextBtns = document.querySelectorAll('.next-step');
        const prevBtns = document.querySelectorAll('.prev-step');
        const steps = document.querySelectorAll('.wizard-step');

        // Helper: Validate current step fields before navigating
        function validateStep(currentStepNum) {
            let isValid = true;
            const stepEl = document.getElementById('step-' + currentStepNum);
            if (!stepEl) return true;

            // Check standard HTML5 required fields in this step
            const requiredFields = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
            requiredFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (!field.checked) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                } else if (field.type === 'file') {
                    // Check if file is uploaded OR if cropped base64 data / loaded preview image exists
                    const previewImg = document.getElementById('previewImage');
                    const croppedData = document.getElementById('croppedImageData')?.value;
                    const hasLoadedImage = (previewImg && previewImg.src && !previewImg.classList.contains('d-none')) || !!croppedData;
                    if ((!field.files || field.files.length === 0) && !hasLoadedImage) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                } else {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                }
            });

            // Special validation for Step 2: Placement checkboxes & Creative Image
            if (currentStepNum == 2) {
                const checkedPlacements = stepEl.querySelectorAll('input[name="placements[]"]:checked');
                const placementFeedback = document.getElementById('placementFeedback');
                if (checkedPlacements.length === 0) {
                    if (placementFeedback) placementFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    if (placementFeedback) placementFeedback.classList.add('d-none');
                }

                const creativeInput = document.getElementById('creativeImageInput');
                const croppedData = document.getElementById('croppedImageData')?.value;
                const previewImg = document.getElementById('previewImage');
                const hasImg = (previewImg && previewImg.src && !previewImg.classList.contains('d-none')) || !!croppedData;
                if ((!creativeInput.files || creativeInput.files.length === 0) && !hasImg) {
                    creativeInput.classList.add('is-invalid');
                    isValid = false;
                }
            }

            // Special validation for Step 3: Platforms and Date Range
            if (currentStepNum == 3) {
                const checkedPlatforms = stepEl.querySelectorAll('input[name="platforms[]"]:checked');
                const platformFeedback = document.getElementById('platformFeedback');
                if (checkedPlatforms.length === 0) {
                    if (platformFeedback) platformFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    if (platformFeedback) platformFeedback.classList.add('d-none');
                }

                const startDate = document.getElementById('startDateInput');
                const endDate = document.getElementById('endDateInput');
                if (startDate && endDate && startDate.value && endDate.value) {
                    if (endDate.value < startDate.value) {
                        endDate.classList.add('is-invalid');
                        const dateFeedback = document.getElementById('dateFeedback');
                        if (dateFeedback) dateFeedback.innerText = 'End date cannot be earlier than start date.';
                        isValid = false;
                    }
                }
            }

            if (!isValid) {
                const firstInvalid = stepEl.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return isValid;
        }

        // Real-time invalid feedback removal on user interaction
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
            input.addEventListener('change', function() {
                this.classList.remove('is-invalid');
            });
        });

        // Navigation between wizard steps
        function showStep(stepNum) {
            steps.forEach(s => s.classList.add('d-none'));
            const targetStep = document.getElementById('step-' + stepNum);
            if (targetStep) targetStep.classList.remove('d-none');

            // Update indicators
            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById('step' + i + '-indicator');
                if (!indicator) continue;
                const circle = indicator.querySelector('.step-circle');
                if (!circle) continue;

                if (i < stepNum) {
                    indicator.classList.remove('text-muted');
                    circle.className = 'step-circle bg-success text-white';
                    circle.innerHTML = '<i class="fa-solid fa-check"></i>';
                } else if (i == stepNum) {
                    indicator.classList.remove('text-muted');
                    circle.className = 'step-circle bg-primary text-white';
                    circle.innerHTML = i;
                } else {
                    indicator.classList.add('text-muted');
                    circle.className = 'step-circle bg-light border text-muted';
                    circle.innerHTML = i;
                }
            }

            // Sync Step 4 Review summary values
            if (stepNum == 4) {
                const adNameInput = document.getElementById('inputAdName');
                const revCamp = document.getElementById('reviewCampaignName');
                if (adNameInput && revCamp) revCamp.innerText = adNameInput.value.trim() || 'Custom Campaign';

                const formatInput = document.getElementById('adFormatInput');
                const revFormat = document.getElementById('reviewFormat');
                if (formatInput && revFormat) revFormat.innerText = formatInput.value;

                const audienceSelect = document.getElementById('selectAudienceSegment');
                const revAudience = document.getElementById('reviewAudience');
                if (audienceSelect && revAudience) {
                    revAudience.innerText = audienceSelect.options[audienceSelect.selectedIndex]?.text || 'All Users';
                }

                const placementBoxes = document.querySelectorAll('input[name="placements[]"]:checked');
                const revPlacements = document.getElementById('reviewPlacements');
                if (revPlacements) {
                    const names = Array.from(placementBoxes).map(cb => cb.value.replace(' Screen', '').replace(' Settings', ''));
                    revPlacements.innerText = names.length > 0 ? names.join(', ') : 'None';
                }
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        nextBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const currentStep = this.closest('.wizard-step');
                const currentStepNum = currentStep.id.replace('step-', '');
                if (validateStep(currentStepNum)) {
                    showStep(this.dataset.target);
                }
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                showStep(this.dataset.target);
            });
        });

        // Form Submit Validation
        form.addEventListener('submit', function(e) {
            if (!validateStep(4) || !form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        // ----------------------------------------------------
        // 1. Creative Image Upload, Cropper.js & Live Preview
        // ----------------------------------------------------
        const creativeImageInput = document.getElementById('creativeImageInput');
        const croppedImageData = document.getElementById('croppedImageData');
        const removeImageBtn = document.getElementById('removeImageBtn');
        const reCropImageBtn = document.getElementById('reCropImageBtn');
        const imageToCrop = document.getElementById('creativeImageToCrop');
        const cropModalEl = document.getElementById('cropCreativeModal');
        const applyCropBtn = document.getElementById('applyCropBtn');

        let cropperInstance = null;
        let originalImageSource = null;

        function updateImagePreview(src) {
            const previewImg = document.getElementById('previewImage');
            const previewPlaceholder = document.getElementById('previewImagePlaceholder');
            const finalImg = document.getElementById('finalPreviewImage');
            const finalPlaceholder = document.getElementById('finalPreviewImagePlaceholder');

            if (src) {
                if (previewImg) { previewImg.src = src; previewImg.classList.remove('d-none'); }
                if (previewPlaceholder) previewPlaceholder.classList.add('d-none');
                if (finalImg) { finalImg.src = src; finalImg.classList.remove('d-none'); }
                if (finalPlaceholder) finalPlaceholder.classList.add('d-none');
                if (removeImageBtn) removeImageBtn.classList.remove('d-none');
                if (reCropImageBtn) reCropImageBtn.classList.remove('d-none');
            } else {
                if (previewImg) { previewImg.src = ''; previewImg.classList.add('d-none'); }
                if (previewPlaceholder) previewPlaceholder.classList.remove('d-none');
                if (finalImg) { finalImg.src = ''; finalImg.classList.add('d-none'); }
                if (finalPlaceholder) finalPlaceholder.classList.remove('d-none');
                if (removeImageBtn) removeImageBtn.classList.add('d-none');
                if (reCropImageBtn) reCropImageBtn.classList.add('d-none');
            }
        }

        function openCropper(imageSrc) {
            if (!imageSrc) return;
            imageToCrop.src = imageSrc;

            const modal = bootstrap.Modal.getOrCreateInstance(cropModalEl);
            modal.show();

            const initCropper = function() {
                cropModalEl.removeEventListener('shown.bs.modal', initCropper);
                if (cropperInstance) {
                    cropperInstance.destroy();
                }

                // Determine aspect ratio according to chosen format
                const activeFormat = document.getElementById('adFormatInput')?.value || 'Banner';
                let initialRatio = 1.91; // Standard banner 1200x628
                if (activeFormat === 'Interstitial') {
                    initialRatio = 9 / 16;
                } else if (activeFormat === 'Native') {
                    initialRatio = 16 / 9;
                }

                // Update active ratio button
                document.querySelectorAll('#aspectRatioButtons .btn').forEach(b => b.classList.remove('active'));
                const defaultBtn = document.getElementById('cropRatioBanner');
                if (defaultBtn) defaultBtn.classList.add('active');

                const CropperClass = window.Cropper || (typeof Cropper !== 'undefined' ? Cropper : null);
                if (CropperClass) {
                    cropperInstance = new CropperClass(imageToCrop, {
                        aspectRatio: initialRatio,
                        viewMode: 2,
                        autoCropArea: 0.95,
                        responsive: true,
                        background: false,
                    });
                }
            };

            cropModalEl.addEventListener('shown.bs.modal', initCropper);
        }

        if (creativeImageInput) {
            creativeImageInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;

                // 1. Validation: Allowed MIME Types & Extensions (JPG, JPEG, PNG, WebP only)
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileExt = file.name.split('.').pop().toLowerCase();
                const isImageMime = file.type && (file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/webp');
                const isAllowedExt = allowedExtensions.includes(fileExt);

                if (!isImageMime && !isAllowedExt) {
                    this.value = '';
                    if (croppedImageData) croppedImageData.value = '';
                    this.classList.add('is-invalid');
                    const feedback = document.getElementById('imageInvalidFeedback');
                    if (feedback) feedback.innerText = 'Invalid file format. Only JPG, PNG, and WebP images are allowed.';
                    if (window.showToast) {
                        window.showToast('error', 'Invalid File Type', 'Only JPG, PNG, and WebP images are supported.');
                    }
                    updateImagePreview(null);
                    return;
                }

                // 2. Validation: Max File Size 5MB
                if (file.size > 5 * 1024 * 1024) {
                    this.value = '';
                    if (croppedImageData) croppedImageData.value = '';
                    this.classList.add('is-invalid');
                    const feedback = document.getElementById('imageInvalidFeedback');
                    if (feedback) feedback.innerText = 'Image size exceeds 5MB limit. Please choose a smaller image.';
                    if (window.showToast) {
                        window.showToast('error', 'File Too Large', 'Image size must be less than 5MB.');
                    }
                    updateImagePreview(null);
                    return;
                }

                this.classList.remove('is-invalid');

                // 3. Read image and launch Cropper
                const reader = new FileReader();
                reader.onload = function(event) {
                    originalImageSource = event.target.result;
                    openCropper(originalImageSource);
                };
                reader.readAsDataURL(file);
            });
        }

        // Apply Cropped Image
        if (applyCropBtn) {
            applyCropBtn.addEventListener('click', function() {
                if (cropperInstance) {
                    const canvas = cropperInstance.getCroppedCanvas({
                        maxWidth: 1920,
                        maxHeight: 1080,
                        fillColor: '#ffffff',
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    if (canvas) {
                        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.92);
                        if (croppedImageData) croppedImageData.value = croppedDataUrl;
                        updateImagePreview(croppedDataUrl);
                        if (creativeImageInput) creativeImageInput.classList.remove('is-invalid');

                        const modal = bootstrap.Modal.getInstance(cropModalEl);
                        if (modal) modal.hide();

                        if (window.showToast) {
                            window.showToast('success', 'Image Cropped', 'Creative image successfully cropped and applied to live preview.');
                        }
                    }
                }
            });
        }

        // Re-crop button
        if (reCropImageBtn) {
            reCropImageBtn.addEventListener('click', function() {
                if (originalImageSource) {
                    openCropper(originalImageSource);
                }
            });
        }

        // Remove image button
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', function() {
                if (creativeImageInput) creativeImageInput.value = '';
                if (croppedImageData) croppedImageData.value = '';
                originalImageSource = null;
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
                updateImagePreview(null);
            });
        }

        // Aspect ratio buttons in Cropper Modal
        const btnBanner = document.getElementById('cropRatioBanner');
        const btn169 = document.getElementById('cropRatio169');
        const btn11 = document.getElementById('cropRatio11');
        const btnFree = document.getElementById('cropRatioFree');

        function setActiveRatioBtn(activeBtn) {
            document.querySelectorAll('#aspectRatioButtons .btn').forEach(b => b.classList.remove('active'));
            if (activeBtn) activeBtn.classList.add('active');
        }

        if (btnBanner) {
            btnBanner.addEventListener('click', () => {
                if (cropperInstance) cropperInstance.setAspectRatio(1.91);
                setActiveRatioBtn(btnBanner);
            });
        }
        if (btn169) {
            btn169.addEventListener('click', () => {
                if (cropperInstance) cropperInstance.setAspectRatio(16 / 9);
                setActiveRatioBtn(btn169);
            });
        }
        if (btn11) {
            btn11.addEventListener('click', () => {
                if (cropperInstance) cropperInstance.setAspectRatio(1);
                setActiveRatioBtn(btn11);
            });
        }
        if (btnFree) {
            btnFree.addEventListener('click', () => {
                if (cropperInstance) cropperInstance.setAspectRatio(NaN);
                setActiveRatioBtn(btnFree);
            });
        }

        // Zoom & Rotate toolbar controls
        const zoomInBtn = document.getElementById('cropZoomInBtn');
        const zoomOutBtn = document.getElementById('cropZoomOutBtn');
        const rotateLeftBtn = document.getElementById('cropRotateLeftBtn');
        const rotateRightBtn = document.getElementById('cropRotateRightBtn');
        const resetCropBtn = document.getElementById('cropResetBtn');

        if (zoomInBtn) zoomInBtn.addEventListener('click', () => cropperInstance && cropperInstance.zoom(0.1));
        if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => cropperInstance && cropperInstance.zoom(-0.1));
        if (rotateLeftBtn) rotateLeftBtn.addEventListener('click', () => cropperInstance && cropperInstance.rotate(-90));
        if (rotateRightBtn) rotateRightBtn.addEventListener('click', () => cropperInstance && cropperInstance.rotate(90));
        if (resetCropBtn) resetCropBtn.addEventListener('click', () => cropperInstance && cropperInstance.reset());

        // ----------------------------------------------------
        // 2. Headline & Description Live Text Binding
        // ----------------------------------------------------
        const headInput = document.getElementById('liveHeadline');
        const descInput = document.getElementById('liveDesc');
        const ctaInput = document.getElementById('liveCTA');
        const headlineCharCount = document.getElementById('headlineCharCount');
        const descCharCount = document.getElementById('descCharCount');

        function updateHeadline(val) {
            const cleanVal = val.trim() || 'Enjoy GPS Camera Without Ads';
            const p1 = document.getElementById('previewHeadline');
            const p2 = document.getElementById('finalPreviewHeadline');
            if (p1) p1.innerText = cleanVal;
            if (p2) p2.innerText = cleanVal;
            if (headlineCharCount) headlineCharCount.innerText = `${val.length} / 60`;
        }

        function updateDescription(val) {
            const cleanVal = val.trim() || 'Upgrade once for an uninterrupted camera experience.';
            const p1 = document.getElementById('previewDesc');
            const p2 = document.getElementById('finalPreviewDesc');
            if (p1) p1.innerText = cleanVal;
            if (p2) p2.innerText = cleanVal;
            if (descCharCount) descCharCount.innerText = `${val.length} / 120`;
        }

        function updateCTA(val) {
            const cleanVal = val || 'Upgrade Now';
            const p1 = document.getElementById('previewCTA');
            const p2 = document.getElementById('finalPreviewCTA');
            if (p1) p1.innerText = cleanVal;
            if (p2) p2.innerText = cleanVal;
        }

        if (headInput) {
            ['input', 'keyup', 'change', 'paste'].forEach(evt => {
                headInput.addEventListener(evt, () => updateHeadline(headInput.value));
            });
        }

        if (descInput) {
            ['input', 'keyup', 'change', 'paste'].forEach(evt => {
                descInput.addEventListener(evt, () => updateDescription(descInput.value));
            });
        }

        if (ctaInput) {
            ctaInput.addEventListener('change', () => updateCTA(ctaInput.value));
        }

        // ----------------------------------------------------
        // 3. Format Selection Cards
        // ----------------------------------------------------
        const formatCards = document.querySelectorAll('.select-format');
        const formatInput = document.getElementById('adFormatInput');
        const summaryFormat = document.getElementById('summaryFormat');

        formatCards.forEach(card => {
            card.addEventListener('click', function() {
                formatCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const val = this.dataset.value;
                formatInput.value = val;
                formatInput.classList.remove('is-invalid');
                if (summaryFormat) summaryFormat.innerText = val;
            });
        });

        // ----------------------------------------------------
        // 4. Objective & Ad Name Sync
        // ----------------------------------------------------
        const inputAdName = document.getElementById('inputAdName');
        const selectObjective = document.getElementById('selectObjective');
        const summaryObjective = document.getElementById('summaryObjective');

        if (inputAdName) {
            inputAdName.addEventListener('input', function() {
                const chk = document.getElementById('chkAdName');
                if (chk) {
                    if (this.value.trim()) {
                        chk.className = 'fa-solid fa-circle-check text-success';
                    } else {
                        chk.className = 'fa-regular fa-circle text-muted';
                    }
                }
            });
        }

        if (selectObjective && summaryObjective) {
            selectObjective.addEventListener('change', function() {
                summaryObjective.innerText = this.value;
            });
        }
    });
</script>
@endpush
@endsection
