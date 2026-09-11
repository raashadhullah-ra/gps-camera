@extends('layout')

@section('title', 'Firebase Settings - GPS Camera Admin')
@section('page_title', 'Firebase Settings')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Settings</a>
    <span class="breadcrumb-separator">/</span>
    <span class="active-crumb">Firebase</span>
@endsection

@section('content')
<div class="container-fluid p-4">
    <!-- 1. Top Header Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="fs-4 fw-bold text-dark mb-1">Firebase Configuration</h1>
            <p class="fs-13 text-secondary mb-0">Configure and manage Firebase Cloud Messaging (FCM) credentials for instant push notifications.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($settings->isNotEmpty())
                @if(auth()->user()->hasPermissionTo('firebase_settings.edit'))
                    <a href="{{ route('admin.settings.firebase.edit', $settings->first()->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Project</span>
                    </a>
                @endif
            @else
                @if(auth()->user()->hasPermissionTo('firebase_settings.create'))
                    <a href="{{ route('admin.settings.firebase.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Project</span>
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- 2. Active Project Hero Card -->
    @if($activeSetting)
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-11 fw-semibold">
                                <i class="fa-solid fa-bolt me-1"></i> Active Routing Project
                            </span>
                            @if($activeSetting->connection_status === 'connected')
                                <span class="status-indicator active">
                                    <span class="status-dot"></span> Connected
                                </span>
                            @elseif($activeSetting->connection_status === 'failed')
                                <span class="badge-status badge-danger">
                                    <span class="status-dot"></span> Connection Error
                                </span>
                            @else
                                <span class="badge-status badge-secondary">
                                    <span class="status-dot"></span> Untested
                                </span>
                            @endif
                        </div>
                        <h2 class="fs-4 fw-bold text-dark mb-0">{{ $activeSetting->label }}</h2>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('admin.settings.firebase.test', $activeSetting->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" title="Test Google Cloud OAuth2 / FCM Connection">
                                <i class="fa-solid fa-satellite-dish"></i>
                                <span>Test Connection</span>
                            </button>
                        </form>
                        <a href="{{ route('admin.settings.firebase.edit', $activeSetting->id) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit Config</span>
                        </a>
                    </div>
                </div>

                @if($activeSetting->last_error_message)
                    <div class="alert alert-danger border-0 rounded-3 p-3 mb-3 fs-13 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation fs-5"></i>
                        <div><strong>Connection Error:</strong> {{ $activeSetting->last_error_message }}</div>
                    </div>
                @endif

                <div class="row g-3 pt-2 border-top">
                    <div class="col-sm-6 col-md-3">
                        <div class="fs-11 text-secondary text-uppercase fw-semibold mb-1">Project ID</div>
                        <div class="fs-13 fw-bold text-dark font-monospace">{{ $activeSetting->project_id ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="fs-11 text-secondary text-uppercase fw-semibold mb-1">App ID</div>
                        <div class="fs-13 fw-bold text-dark font-monospace text-truncate">{{ $activeSetting->app_id ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="fs-11 text-secondary text-uppercase fw-semibold mb-1">Messaging Sender ID</div>
                        <div class="fs-13 fw-bold text-dark font-monospace">{{ $activeSetting->messaging_sender_id ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="fs-11 text-secondary text-uppercase fw-semibold mb-1">Last Connection Test</div>
                        <div class="fs-13 fw-bold text-dark">
                            {{ $activeSetting->last_tested_at ? $activeSetting->last_tested_at->diffForHumans() : 'Never tested' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Active Project State -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-4 text-center">
            <div class="py-4">
                <h3 class="fs-5 fw-bold text-dark">No Active Firebase Project</h3>
                <p class="text-secondary fs-13 mb-3">Upload your Firebase Service Account JSON or fill the credentials form to begin sending notifications.</p>
                <a href="{{ route('admin.settings.firebase.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add Firebase Project</span>
                </a>
            </div>
        </div>
    @endif

    <!-- 3. All Saved Projects List Card (Matching Table Card UI) -->
    <div class="devices-table-card mb-4">
        <div class="table-card-top-bar">
            <h2 class="table-title">Firebase Project Configuration</h2>
            <div class="table-header-tools">
                @if($settings->isNotEmpty())
                    <a href="{{ route('admin.settings.firebase.edit', $settings->first()->id) }}" class="btn-tool">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Project</span>
                    </a>
                @else
                    <a href="{{ route('admin.settings.firebase.create') }}" class="btn-tool">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Project</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="devices-data-table">
                <thead>
                    <tr>
                        <th>Project / Label</th>
                        <th>Project ID</th>
                        <th>App ID</th>
                        <th>Messaging Sender ID</th>
                        <th>Connection Status</th>
                        <th>Status</th>
                        <th>Last Verified</th>
                        <th class="th-action">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                        <tr>
                            <!-- 1. Label -->
                            <td>
                                <div>
                                    <div class="fw-bold text-dark">{{ $setting->label }}</div>
                                    @if(!empty($setting->service_account_json))
                                        <div class="fs-11 text-success">
                                            <i class="fa-solid fa-key me-1"></i>Service Account Loaded
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Project ID -->
                            <td>
                                <span class="installation-id-link">{{ $setting->project_id ?: '—' }}</span>
                            </td>

                            <!-- 3. App ID -->
                            <td class="font-monospace text-muted text-truncate">{{ $setting->app_id ?: '—' }}</td>

                            <!-- 4. Messaging Sender ID -->
                            <td class="font-monospace text-dark">{{ $setting->messaging_sender_id ?: '—' }}</td>

                            <!-- 5. Connection Status -->
                            <td>
                                @if($setting->connection_status === 'connected')
                                    <span class="status-indicator active">
                                        <span class="status-dot"></span> Connected
                                    </span>
                                @elseif($setting->connection_status === 'failed')
                                    <span class="badge-status badge-danger" title="{{ $setting->last_error_message }}">
                                        <span class="status-dot"></span> Failed
                                    </span>
                                @else
                                    <span class="badge-status badge-secondary">
                                        <span class="status-dot"></span> Untested
                                    </span>
                                @endif
                            </td>

                            <!-- 6. Active Toggle Pill -->
                            <td>
                                @if($setting->is_active)
                                    <span class="status-indicator active">
                                        <span class="status-dot"></span> Active
                                    </span>
                                @else
                                    <form action="{{ route('admin.settings.firebase.activate', $setting->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary py-1 px-2 fs-11" title="Make this project active">
                                            Make Active
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- 7. Last Verified -->
                            <td>
                                <span class="date-text">
                                    {{ $setting->last_tested_at ? $setting->last_tested_at->diffForHumans() : 'Never' }}
                                </span>
                            </td>

                            <!-- 8. Actions -->
                            <td class="td-action">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Edit Link -->
                                    <a href="{{ route('admin.settings.firebase.edit', $setting->id) }}" class="btn-action-icon" title="Edit Configuration">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete Form with SweetAlert Confirmation -->
                                    <form action="{{ route('admin.settings.firebase.destroy', $setting->id) }}" method="POST" class="d-inline delete-project-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action-icon text-danger" title="Delete Project" onclick="confirmDeleteProject(event, '{{ addslashes($setting->label) }}')">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">
                                No Firebase configurations found. Click <strong>+ Add Project</strong> to set up your first project.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. Non-Coder Guide Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-circle-question text-primary fs-5"></i>
            <h3 class="fs-6 fw-bold text-dark mb-0">Non-Coder Guide: How to Get Firebase Credentials</h3>
        </div>
        <div class="row g-3 fs-13 text-secondary">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1">
                        <span class="badge bg-primary rounded-circle me-1" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">1</span>
                        Get Service Account JSON (Best)
                    </div>
                    <ol class="ps-3 mb-0 small">
                        <li>Open <a href="https://console.firebase.google.com/" target="_blank" class="text-primary text-decoration-none fw-semibold">Firebase Console &nearr;</a></li>
                        <li>Click ⚙️ <strong>Project Settings</strong> &rarr; <strong>Service Accounts</strong> tab</li>
                        <li>Click <strong>Generate new private key</strong> button</li>
                        <li>Upload the downloaded <code>.json</code> file on the <strong>Add/Edit Project</strong> page.</li>
                    </ol>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1">
                        <span class="badge bg-primary rounded-circle me-1" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">2</span>
                        Get Web / Mobile App Credentials
                    </div>
                    <ol class="ps-3 mb-0 small">
                        <li>In Firebase Console &rarr; ⚙️ <strong>Project Settings</strong> &rarr; <strong>General</strong></li>
                        <li>Scroll to <strong>Your apps</strong> section</li>
                        <li>Copy <strong>apiKey</strong>, <strong>appId</strong>, <strong>messagingSenderId</strong>, and <strong>projectId</strong></li>
                    </ol>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1">
                        <span class="badge bg-primary rounded-circle me-1" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">3</span>
                        Get Web Push VAPID Key
                    </div>
                    <ol class="ps-3 mb-0 small">
                        <li>In Firebase Console &rarr; ⚙️ <strong>Project Settings</strong></li>
                        <li>Open the <strong>Cloud Messaging</strong> tab</li>
                        <li>Scroll to <strong>Web configuration</strong> &rarr; <strong>Web Push certificates</strong></li>
                        <li>Click <strong>Generate key pair</strong> and copy the public key</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDeleteProject(event, label) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Firebase Project?',
            text: `Are you sure you want to delete "${label}"? Active push notifications routing through this project will stop.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete project',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(`Are you sure you want to delete "${label}"?`)) {
            form.submit();
        }
    }
}
</script>
@endpush
