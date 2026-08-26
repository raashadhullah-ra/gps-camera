/**
 * Common UI Scripts & Interactive Handlers for GeoCam Admin
 */

// ----------------------------------------------------
// 0. Global Universal Custom Toast & Alert Setup
// ----------------------------------------------------
if (typeof window !== 'undefined') {
    // Ensure toast container exists
    const getOrCreateToastContainer = () => {
        let container = document.getElementById('geocam-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'geocam-toast-container';
            container.className = 'geocam-toast-container';
            document.body.appendChild(container);
        }
        return container;
    };

    /**
     * Display a custom notification toast matching screenshot design
     * @param {'success'|'error'|'danger'|'warning'|'info'} type 
     * @param {string} titleOrMessage 
     * @param {string} [optionalMessage] 
     * @param {number} [duration=4000]
     */
    window.showToast = function (type = 'success', titleOrMessage = '', optionalMessage = '', duration = 4000) {
        // Normalize arguments
        let iconType = 'success';
        let title = '';
        let message = '';

        const knownTypes = ['success', 'error', 'danger', 'warning', 'info'];

        if (knownTypes.includes(type?.toLowerCase())) {
            iconType = type.toLowerCase() === 'danger' ? 'error' : type.toLowerCase();
            if (optionalMessage) {
                title = titleOrMessage;
                message = optionalMessage;
            } else {
                // If only 1 string passed after type:
                // Determine if string is short (title) or long message
                if (titleOrMessage.toLowerCase().includes('copied') || titleOrMessage.toLowerCase().includes('copy')) {
                    title = 'Location name copied';
                    message = titleOrMessage;
                } else if (iconType === 'success') {
                    title = 'Success';
                    message = titleOrMessage;
                } else if (iconType === 'error') {
                    title = 'Error';
                    message = titleOrMessage;
                } else if (iconType === 'warning') {
                    title = 'Warning';
                    message = titleOrMessage;
                } else {
                    title = 'Information';
                    message = titleOrMessage;
                }
            }
        } else if (knownTypes.includes(titleOrMessage?.toLowerCase())) {
            // Called like showToast('message', 'success')
            iconType = titleOrMessage.toLowerCase() === 'danger' ? 'error' : titleOrMessage.toLowerCase();
            message = type;
            if (message.toLowerCase().includes('copied')) {
                title = 'Location name copied';
            } else {
                title = iconType.charAt(0).toUpperCase() + iconType.slice(1);
            }
        } else {
            // Default success
            iconType = 'success';
            title = 'Notification';
            message = type || titleOrMessage;
        }

        const iconMap = {
            success: 'fa-solid fa-check',
            error: 'fa-solid fa-xmark',
            warning: 'fa-solid fa-exclamation',
            info: 'fa-solid fa-info'
        };

        const container = getOrCreateToastContainer();
        const toastEl = document.createElement('div');
        toastEl.className = `geocam-toast toast-${iconType}`;
        toastEl.setAttribute('role', 'alert');

        toastEl.innerHTML = `
            <div class="toast-icon-circle">
                <i class="${iconMap[iconType] || iconMap.info}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-message">${message}</div>` : ''}
            </div>
            <button type="button" class="toast-close-btn" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        container.appendChild(toastEl);

        // Trigger slide-in animation
        requestAnimationFrame(() => {
            toastEl.classList.add('geocam-toast-show');
        });

        // Close logic
        let removeTimeout;
        const removeToast = () => {
            clearTimeout(removeTimeout);
            toastEl.classList.remove('geocam-toast-show');
            toastEl.classList.add('geocam-toast-hide');
            setTimeout(() => {
                toastEl.remove();
            }, 300);
        };

        const startTimer = () => {
            if (duration > 0) {
                removeTimeout = setTimeout(removeToast, duration);
            }
        };

        startTimer();

        // Pause timer on hover
        toastEl.addEventListener('mouseenter', () => clearTimeout(removeTimeout));
        toastEl.addEventListener('mouseleave', startTimer);

        // Click close button
        const closeBtn = toastEl.querySelector('.toast-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removeToast();
            });
        }

        return toastEl;
    };

    /**
     * Helper for informational toasts
     * @param {string} message 
     * @param {string} [title='Information'] 
     */
    window.showInfoToast = function (message, title = 'Information') {
        return window.showToast('info', title, message);
    };

    /**
     * Display a modal SweetAlert confirmation / alert
     * @param {Object} options 
     */
    window.showAlert = function (options = {}) {
        if (!window.Swal) return;
        return window.Swal.fire({
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b',
            ...options
        });
    };
}

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------
    // 1. Universal Sidebar Toggle (Desktop Collapse + Mobile Drawer)
    // ----------------------------------------------------
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn') || document.querySelector('.sidebar-toggle');
    const adminWrapper = document.getElementById('adminWrapper') || document.querySelector('.admin-wrapper');
    const sidebarPanel = document.querySelector('.sidebar-panel');

    // Restore desktop collapsed state from localStorage
    if (adminWrapper && localStorage.getItem('geocam_sidebar_collapsed') === 'true' && window.innerWidth > 992) {
        adminWrapper.classList.add('sidebar-collapsed');
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.innerWidth <= 992) {
                // Mobile: toggle show drawer
                if (sidebarPanel) {
                    sidebarPanel.classList.toggle('show');
                }
            } else {
                // Desktop: toggle compact icon-only mode
                if (adminWrapper) {
                    adminWrapper.classList.toggle('sidebar-collapsed');
                    const isCollapsed = adminWrapper.classList.contains('sidebar-collapsed');
                    localStorage.setItem('geocam_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                }
            }
        });
    }

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 992 && sidebarPanel && sidebarPanel.classList.contains('show')) {
            if (!sidebarPanel.contains(e.target) && sidebarToggleBtn && !sidebarToggleBtn.contains(e.target)) {
                sidebarPanel.classList.remove('show');
            }
        }
    });

    // ----------------------------------------------------
    // 2. Universal Password Eye Toggle
    // ----------------------------------------------------
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.password-toggle-btn, .toggle-password, [data-toggle-password], .btn-toggle-password');
        if (!toggleBtn) return;

        e.preventDefault();

        // 1. Look for explicit target selector via data attribute
        const targetSelector = toggleBtn.getAttribute('data-target') || toggleBtn.getAttribute('data-toggle-password');
        let inputEl = null;

        if (targetSelector && targetSelector !== 'true') {
            inputEl = document.querySelector(targetSelector);
        }

        // 2. Look in closest input group or parent form container
        if (!inputEl) {
            const container = toggleBtn.closest('.input-group, .position-relative, .form-group, .mb-3');
            if (container) {
                inputEl = container.querySelector('input[type="password"], input[type="text"]');
            }
        }

        if (!inputEl) return;

        const isPassword = inputEl.getAttribute('type') === 'password';
        inputEl.setAttribute('type', isPassword ? 'text' : 'password');

        // Toggle icon classes
        const icon = toggleBtn.querySelector('i, svg');
        if (icon) {
            if (isPassword) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });

    // ----------------------------------------------------
    // 3. Universal Logout Modal Trigger
    // ----------------------------------------------------
    const openLogoutModalBtn = document.getElementById('openLogoutModalBtn');
    const logoutModalEl = document.getElementById('logoutConfirmModal');
    if (openLogoutModalBtn && logoutModalEl) {
        openLogoutModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.bootstrap && window.bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(logoutModalEl);
                modal.show();
            }
        });
    }

    // ----------------------------------------------------
    // 4. Initialize Bootstrap Tooltips & Popovers
    // ----------------------------------------------------
    if (window.bootstrap) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverTriggerList.forEach(el => new bootstrap.Popover(el));
    }
});

