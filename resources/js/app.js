import './bootstrap';

// Alpine.js is loaded via CDN in the layout
// This file is for custom JavaScript initialization

document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss alerts after 5 seconds
    document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
        const delay = parseInt(el.dataset.autoDismiss) || 5000;
        setTimeout(() => {
            el.style.transition = 'opacity 300ms ease-out';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, delay);
    });
});
