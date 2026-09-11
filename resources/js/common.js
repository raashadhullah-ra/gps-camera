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
    // 2. Universal Password Eye Toggle (Supports login.blade.php & custom inputs)
    // ----------------------------------------------------
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.password-toggle-btn, .toggle-password, [data-toggle-password], .btn-toggle-password, .password-toggle-icon');
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
            const container = toggleBtn.closest('.input-icon-group, .input-group, .position-relative, .form-group, .mb-3');
            if (container) {
                inputEl = container.querySelector('input[type="password"], input[type="text"]');
            }
        }

        if (!inputEl) return;

        const isPassword = inputEl.getAttribute('type') === 'password';
        inputEl.setAttribute('type', isPassword ? 'text' : 'password');

        // Toggle icon classes
        const icon = toggleBtn.tagName.toLowerCase() === 'i' ? toggleBtn : toggleBtn.querySelector('i, svg');
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

    /**
     * Setup avatar image picker with validation (2MB, JPG/PNG) and Cropper.js modal
     */
    window.setupAvatarCropper = function (options = {}) {
        const fileInput = document.getElementById(options.fileInputId || 'adminPhotoInput');
        const previewImg = document.getElementById(options.previewImgId || 'avatarImagePreview');
        const previewIcon = document.getElementById(options.previewIconId || 'avatarUploadIcon');
        const summaryCircle = document.getElementById(options.summaryCircleId || 'summaryAvatarCircle');
        const hiddenCroppedInput = document.getElementById(options.hiddenCroppedInputId || 'croppedPhotoInput');

        if (!fileInput) return;

        let cropperInstance = null;
        const modalEl = document.getElementById('cropPhotoModal');
        const imageToCrop = document.getElementById('imageToCrop');
        const applyCropBtn = document.getElementById('applyCropBtn');

        fileInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;

            // 1. Validation: Allowed MIME Types (JPG, PNG, WEBP)
            const isImage = (file.type && file.type.startsWith('image/')) || /\.(jpe?g|png|webp)$/i.test(file.name);
            if (!isImage) {
                window.showToast('error', 'Invalid File Type', 'Please select a JPG or PNG image file.');
                this.value = '';
                return;
            }

            // 2. Validation: Max 2MB File Size (2 * 1024 * 1024 bytes)
            const maxSizeBytes = 2 * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                window.showToast('error', 'File Too Large', `Image size is ${sizeMB}MB. Maximum allowed file size is 2MB.`);
                this.value = '';
                return;
            }

            // 3. Read image for Cropper Modal
            const reader = new FileReader();
            reader.onload = function (evt) {
                const resultData = evt.target.result;

                // Immediate preview fallback
                if (previewImg) {
                    previewImg.src = resultData;
                    previewImg.style.display = 'block';
                }
                if (previewIcon) {
                    previewIcon.style.display = 'none';
                }
                const avatarInitials = document.getElementById('avatarTextInitials');
                if (avatarInitials) {
                    avatarInitials.style.display = 'none';
                }
                if (summaryCircle) {
                    summaryCircle.innerHTML = `<img src="${resultData}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
                }
                if (hiddenCroppedInput) {
                    hiddenCroppedInput.value = resultData;
                }

                if (imageToCrop) {
                    imageToCrop.src = resultData;
                }

                // Show Cropper Modal
                if (modalEl) {
                    try {
                        const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
                        if (bs && bs.Modal) {
                            const modal = bs.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else {
                            $(modalEl).modal('show');
                        }
                    } catch (e) {
                        console.error('Error showing cropper modal:', e);
                    }
                }
            };
            reader.readAsDataURL(file);
        });

        if (modalEl && imageToCrop) {
            modalEl.addEventListener('shown.bs.modal', function () {
                if (cropperInstance) {
                    cropperInstance.destroy();
                }
                const CropperClass = window.Cropper || (typeof Cropper !== 'undefined' ? (Cropper.default || Cropper) : null);
                if (CropperClass) {
                    cropperInstance = new CropperClass(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        responsive: true,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                }
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                if (cropperInstance) {
                    cropperInstance.destroy();
                    cropperInstance = null;
                }
            });

            // Zoom In / Out / Rotate / Reset controls
            document.getElementById('cropZoomInBtn')?.addEventListener('click', () => cropperInstance?.zoom(0.1));
            document.getElementById('cropZoomOutBtn')?.addEventListener('click', () => cropperInstance?.zoom(-0.1));
            document.getElementById('cropRotateLeftBtn')?.addEventListener('click', () => cropperInstance?.rotate(-90));
            document.getElementById('cropRotateRightBtn')?.addEventListener('click', () => cropperInstance?.rotate(90));
            document.getElementById('cropResetBtn')?.addEventListener('click', () => cropperInstance?.reset());

            // Apply Crop
            if (applyCropBtn) {
                applyCropBtn.addEventListener('click', function () {
                    if (cropperInstance) {
                        const canvas = cropperInstance.getCroppedCanvas({
                            width: 500,
                            height: 500,
                            imageSmoothingEnabled: true,
                            imageSmoothingQuality: 'high',
                        });

                        if (canvas) {
                            const croppedBase64 = canvas.toDataURL('image/jpeg', 0.92);

                            if (previewImg) {
                                previewImg.src = croppedBase64;
                                previewImg.style.display = 'block';
                            }
                            if (previewIcon) {
                                previewIcon.style.display = 'none';
                            }
                            const avatarInitials = document.getElementById('avatarTextInitials');
                            if (avatarInitials) {
                                avatarInitials.style.display = 'none';
                            }

                            if (summaryCircle) {
                                summaryCircle.innerHTML = `<img src="${croppedBase64}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
                            }

                            if (hiddenCroppedInput) {
                                hiddenCroppedInput.value = croppedBase64;
                            }
                        }
                    }

                    try {
                        const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
                        if (bs && bs.Modal) {
                            const modal = bs.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                    } catch (e) {}

                    window.showToast('success', 'Photo Applied', 'Photo cropped & ready for save (500×500 px).');
                });
            }
        }
    };

    // Auto initialize if admin photo input is found in the current page
    function autoInitCropper() {
        if (document.getElementById('adminPhotoInput')) {
            window.setupAvatarCropper({
                fileInputId: 'adminPhotoInput',
                previewImgId: 'avatarImagePreview',
                previewIconId: 'avatarUploadIcon',
                summaryCircleId: 'summaryAvatarCircle',
                hiddenCroppedInputId: 'croppedPhotoInput'
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInitCropper);
    } else {
        autoInitCropper();
    }

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

