@extends('layout')

@section('title', '404 - Page Not Found | GeoCam Admin')
@section('page_title', 'Page Not Found')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-75 py-5">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5" style="max-width: 580px; width: 100%; background: #ffffff;">
        {{-- Icon Graphic --}}
        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle mx-auto" 
             style="width: 88px; height: 88px; background: rgba(59, 130, 246, 0.08); color: #2563eb;">
            <i class="fa-solid fa-compass-drafting fa-3x"></i>
        </div>

        {{-- Error Code & Title --}}
        <span class="badge bg-primary-subtle text-primary fs-12 fw-semibold px-3 py-1.5 rounded-pill mb-3 d-inline-block mx-auto">
            HTTP 404 · Page Not Found
        </span>
        
        <h1 class="fs-3 fw-bold text-dark mb-2">Looking for something?</h1>
        <p class="fs-14 text-secondary mb-4" style="line-height: 1.6;">
            {{ $exception->getMessage() ?: "The page or resource you are trying to access does not exist, has been removed, or has had its URL modified." }}
        </p>

        {{-- Action Buttons --}}
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('admin.dashboard') }}'">
                <i class="fa-solid fa-arrow-left me-1.5"></i> Go Back
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-table-cells-large me-1.5"></i> Return to Dashboard
            </a>
        </div>

        {{-- Quick Navigation Links --}}
        <div class="mt-4 pt-3 border-top text-muted fs-12">
            <span>Quick Links:</span>
            <a href="{{ route('admin.devices.index') }}" class="text-decoration-none text-primary ms-2 me-2">Installed Devices</a>·
            <a href="{{ route('admin.locations.index') }}" class="text-decoration-none text-primary ms-2 me-2">Locations</a>·
            <a href="{{ route('admin.notifications.index') }}" class="text-decoration-none text-primary ms-2">Notifications</a>
        </div>
    </div>
</div>
@endsection
