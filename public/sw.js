const CACHE_NAME = 'creative-erp-cache-v1';
const urlsToCache = [
    '/',
    '/manifest.json',
    '/images/logo.png',
    // We're omitting the generated JS/CSS since Vite hashes them, 
    // network-first strategy handles that below.
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    // Basic network-first strategy with cache fallback
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
