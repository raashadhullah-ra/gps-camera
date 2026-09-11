@extends('layout')

@section('title', '500 - Server Error | GeoCam Admin')
@section('page_title', 'Server Error')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-75 py-5">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5" style="max-width: 580px; width: 100%; background: #ffffff;">
        {{-- Icon Graphic --}}
        <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle mx-auto" 
             style="width: 88px; height: 88px; background: rgba(245, 158, 11, 0.08); color: #f59e0b;">
            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
        </div>

        {{-- Error Code & Title --}}
        <span class="badge bg-warning-subtle text-warning fs-12 fw-semibold px-3 py-1.5 rounded-pill mb-3 d-inline-block mx-auto">
            HTTP 500 · Internal Server Error
        </span>
        
        <h1 class="fs-3 fw-bold text-dark mb-2">Something went wrong</h1>
        <p class="fs-14 text-secondary mb-4" style="line-height: 1.6;">
            An unexpected error occurred on our server while processing your request. Please try refreshing or return to the dashboard.
        </p>

        {{-- Action Buttons --}}
        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="window.location.reload()">
                <i class="fa-solid fa-rotate me-1.5"></i> Refresh Page
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary px-4 py-2">
                <i class="fa-solid fa-table-cells-large me-1.5"></i> Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
