import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Chart from 'chart.js/auto';
window.Chart = Chart;

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
window.Swal = Swal;

import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
window.flatpickr = flatpickr;

import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
window.Cropper = (typeof Cropper === 'function') ? Cropper : (Cropper?.default || Cropper);

// Leaflet & Heatmap (Bundled locally via npm leaflet & leaflet.heat)
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.heat';
window.L = L;

// Inter Font (Bundled locally via npm @fontsource/inter)
import '@fontsource/inter/300.css';
import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import '@fontsource/inter/800.css';

// FontAwesome Icons (Bundled locally via npm @fortawesome/fontawesome-free)
import '@fortawesome/fontawesome-free/css/all.min.css';

// Import common application UI scripts (sidebar collapse, eye toggles, modal helpers, SweetAlert toasts)
import './common';

// Global dropdown and UI helpers
document.addEventListener('DOMContentLoaded', () => {
    if (window.flatpickr) {
        flatpickr('.filter-date-input', {
            mode: 'range',
            dateFormat: 'M d, Y',
            allowInput: true,
        });
    }
});
