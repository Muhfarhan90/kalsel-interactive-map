import { createPopper } from '@popperjs/core';
import './bootstrap';
import Alpine from 'alpinejs';
import Plyr from 'plyr';
import 'plyr/dist/plyr.css';
// leaflet
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import './components/rich-text-editor';
import './components/category-sort';
import './components/geo-coordinate-picker';

window.Alpine = Alpine;
window.createPopper = createPopper;
window.flatpickr = flatpickr;
window.Plyr = Plyr;
// leaflet
window.L = L;

document.addEventListener('contextmenu', (event) => {
    event.preventDefault();
});

Alpine.start();
