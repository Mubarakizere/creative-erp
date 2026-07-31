import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Global Axios Interceptors for Loading States
let activeRequests = 0;

window.axios.interceptors.request.use(function (config) {
    activeRequests++;
    if (window.Alpine && window.Alpine.store('loading')) {
        window.Alpine.store('loading').start();
    }
    return config;
}, function (error) {
    activeRequests--;
    if (activeRequests <= 0 && window.Alpine && window.Alpine.store('loading')) {
        window.Alpine.store('loading').stop();
    }
    return Promise.reject(error);
});

window.axios.interceptors.response.use(function (response) {
    activeRequests--;
    if (activeRequests <= 0 && window.Alpine && window.Alpine.store('loading')) {
        window.Alpine.store('loading').stop();
    }
    return response;
}, function (error) {
    activeRequests--;
    if (activeRequests <= 0 && window.Alpine && window.Alpine.store('loading')) {
        window.Alpine.store('loading').stop();
    }
    return Promise.reject(error);
});
