<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password - GPS Camera Admin</title>

    <!-- Vite Assets (Includes Bootstrap 5, FontAwesome, Inter Font, SweetAlert2) -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>

<div class="auth-split-layout">
    
    <!-- Left Hero Section -->
    <div class="auth-hero-panel">
        <!-- Background Vector Route & Pins Overlay -->
        <div class="hero-graphic-overlay">
            <svg viewBox="0 0 500 700" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Curved GPS Map Route -->
                <path d="M 370 120 C 370 180 320 210 260 250 C 200 290 190 360 210 430 C 230 500 370 520 370 600" stroke="#0ea5e9" stroke-width="2.5" stroke-dasharray="8 8" stroke-linecap="round" opacity="0.45"/>
                
                <!-- Pin 1: Top Right -->
                <g transform="translate(370, 120)" opacity="0.75">
                    <circle cx="0" cy="0" r="14" fill="#0ea5e9" fill-opacity="0.2"/>
                    <circle cx="0" cy="0" r="7" fill="#0ea5e9"/>
                    <path d="M-8 -18 C-14 -18 -18 -14 -18 -8 C-18 2 0 16 0 16 C0 16 18 2 18 -8 C18 -14 14 -18 8 -18 Z" fill="#0284c7" transform="scale(0.55) translate(-0, -10)" opacity="0.9"/>
                </g>

                <!-- Waypoint 2: Mid Path -->
                <g transform="translate(200, 360)" opacity="0.6">
                    <circle cx="0" cy="0" r="10" fill="#0284c7" fill-opacity="0.15"/>
                    <circle cx="0" cy="0" r="4" fill="#38bdf8"/>
                </g>

                <!-- Pin 3: Bottom Right -->
                <g transform="translate(370, 600)" opacity="0.75">
                    <circle cx="0" cy="0" r="16" fill="#0284c7" fill-opacity="0.2"/>
                    <circle cx="0" cy="0" r="8" fill="#0284c7"/>
                    <path d="M-8 -18 C-14 -18 -18 -14 -18 -8 C-18 2 0 16 0 16 C0 16 18 2 18 -8 C18 -14 14 -18 8 -18 Z" fill="#0ea5e9" transform="scale(0.55) translate(-0, -10)" opacity="0.9"/>
                </g>
            </svg>
        </div>

        <div class="hero-header">
            <img src="{{ asset('assets/Logos/Geo icon.png') }}" alt="GeoCam Logo" class="hero-logo-img">
            <span class="hero-brand-name">GPS Camera Admin</span>
        </div>

        <div class="hero-body">
            <div class="hero-subtitle-badge">ENTERPRISE CONTROL CENTER</div>
            <h1 class="hero-heading">
                Control every installation.
                <span class="highlight-blue">Protect every location.</span>
            </h1>
            <p class="hero-description">
                Monitor devices, manage app behavior, review analytics and respond to issues from one secure administration portal.
            </p>

            <div class="hero-feature-list">
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <span class="feature-text">Installation Intelligence</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <span class="feature-text">Firebase Analytics & Crash Monitoring</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-box">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <span class="feature-text">Secure Remote App Control</span>
                </div>
            </div>
        </div>

        <div class="hero-footer">
            <div class="footer-shield-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="footer-text-group">
                <div class="footer-title">Enterprise Administration Console</div>
                <div class="footer-meta">Encrypted access · Audited sessions · Role-based permissions</div>
            </div>
        </div>
    </div>

    <!-- Right Form Section -->
    <div class="auth-form-panel">
        <!-- Top Right Help / Contact Owner Header -->
        <div class="form-panel-header">
            <span class="need-help-text">Need help?</span>
            <a href="#" class="contact-owner-btn">
                <i class="fa-regular fa-envelope"></i>
                <span>Contact System Owner</span>
            </a>
        </div>

        <!-- Center Recovery Card -->
        <div class="form-card-container">
            <div class="text-center">
                <div class="auth-portal-badge">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>ACCOUNT RECOVERY</span>
                </div>
            </div>

            <h2 class="auth-card-title">Forgot your password?</h2>
            <p class="auth-card-subtitle">Enter your Super Admin email and we'll send a secure recovery code.</p>

            <form action="{{ url('/auth/login') }}" method="GET" class="needs-validation" novalidate>
                <div class="form-group-item">
                    <label class="form-label">Admin Email</label>
                    <div class="input-icon-group">
                        <i class="fa-regular fa-envelope input-prefix-icon"></i>
                        <input type="email" class="form-control" value="admin@gpscamera.app" placeholder="admin@gpscamera.app" required>
                    </div>
                    <div class="invalid-feedback">Please enter your registered admin email.</div>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Send Recovery Code</span>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <div class="text-center mt-3 mb-2">
                <a href="{{ url('/auth/login') }}" class="forgot-password-link" style="font-size: 13.5px;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Super Admin Login
                </a>
            </div>

            <div class="recovery-notice-box">
                <i class="fa-solid fa-triangle-exclamation notice-icon"></i>
                <div>
                    Recovery codes expire after 10 minutes and can be used only once.
                </div>
            </div>

            <div class="card-footer-support">
                <div>No longer have access to this email?</div>
                <a href="#">Contact System Owner</a>
            </div>

            <div class="security-note">
                <i class="fa-solid fa-lock"></i>
                <span>Password recovery attempts are logged for account protection.</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="form-panel-footer">
            <div>© 2026 GPS Camera Admin</div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <span>|</span>
                <a href="#">Security</a>
                <span>|</span>
                <a href="#">Terms</a>
            </div>
            <div class="system-status">
                <span class="status-dot"></span>
                <span>All systems operational</span>
            </div>
        </div>
    </div>

</div>

<!-- Bootstrap Client-side Validation Script (Prevents page reload on empty/invalid input) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // SweetAlert2 Flash Toasts (Top Right)
        @if (session('status'))
            window.showToast ? window.showToast('success', @json(session('status'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('status')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
        @endif

        @if (session('success'))
            window.showToast ? window.showToast('success', @json(session('success'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
        @endif

        @if (session('error'))
            window.showToast ? window.showToast('error', @json(session('error'))) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
        @endif

        @if ($errors->any())
            window.showToast ? window.showToast('error', @json($errors->first())) : (window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json($errors->first()), showConfirmButton: false, timer: 4000, timerProgressBar: true }));
        @endif
    });
</script>

</body>
</html>
