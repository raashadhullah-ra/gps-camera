@extends('layout')

@section('title', '403 - Unauthorized Access | GeoCam Admin')
@section('page_title', 'Access Denied')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-75 py-5">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5" style="max-width: 580px; width: 100%; background: #ffffff;">
        {{-- Icon Graphic --}}
        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle mx-auto" 
             style="width: 88px; height: 88px; background: rgba(239, 68, 68, 0.08); color: #ef4444;">
            <i class="fa-solid fa-shield-halved fa-3x"></i>
        </div>

        {{-- Error Code & Title --}}
        <span class="badge bg-danger-subtle text-danger fs-12 fw-semibold px-3 py-1.5 rounded-pill mb-3 d-inline-block mx-auto">
            HTTP 403 · Access Denied
        </span>
        
        <h1 class="fs-3 fw-bold text-dark mb-2">Unauthorized Access</h1>
        <p class="fs-14 text-secondary mb-4" style="line-height: 1.6;">
            {{ $exception->getMessage() ?: "You don't have the required administrative permissions to access this page or perform this action. If you believe this is an error, please contact your Super Administrator." }}
        </p>

        {{-- Action Buttons --}}
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('admin.dashboard') }}'">
                <i class="fa-solid fa-arrow-left me-1.5"></i> Go Back
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-table-cells-large me-1.5"></i> Dashboard
            </a>
        </div>

        {{-- Footer Details --}}
        <div class="mt-4 pt-3 border-top text-muted fs-12">
            Logged in as: <strong class="text-dark">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</strong> 
            @if(auth()->check())
                · Role: <span class="badge bg-light text-secondary border">{{ auth()->user()->role }}</span>
            @endif
        </div>
    </div>
</div>
@endsection
