import './bootstrap';
import './event-bus';
import TomSelect from 'tom-select';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

window.TomSelect = TomSelect;

// Alpine.js is loaded via CDN in the layout
// This file is for custom JavaScript initialization

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Notyf
    window.notyf = new Notyf({
        duration: 5000,
        position: {
            x: 'right',
            y: window.innerWidth < 768 ? 'bottom' : 'top', // Bottom Center mobile, Top Right desktop
        },
        dismissible: true,
        types: [
            {
                type: 'success',
                background: 'green',
                duration: 3000,
                icon: false
            },
            {
                type: 'info',
                background: 'blue',
                duration: 5000,
                icon: false
            },
            {
                type: 'warning',
                background: 'orange',
                duration: 6000,
                icon: false
            },
            {
                type: 'error',
                background: 'red',
                duration: 8000,
                icon: false
            }
        ]
    });
    
    // Make position responsive on resize
    window.addEventListener('resize', () => {
        const isMobile = window.innerWidth < 768;
        window.notyf.options.position.y = isMobile ? 'bottom' : 'top';
        window.notyf.options.position.x = isMobile ? 'center' : 'right';
    });
});
