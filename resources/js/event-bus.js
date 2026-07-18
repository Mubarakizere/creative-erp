/**
 * Global Event Bus
 * 
 * Provides a framework-agnostic frontend event system.
 * Can be used by Alpine.js, Livewire, or vanilla JS to communicate across components.
 */

class EventBus {
    constructor() {
        this.listeners = {};
    }

    /**
     * Subscribe to an event.
     * @param {string} event The event name
     * @param {function} callback The callback function
     * @returns {function} Unsubscribe function
     */
    on(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        
        this.listeners[event].push(callback);
        
        // Return unsubscribe function
        return () => {
            this.listeners[event] = this.listeners[event].filter(cb => cb !== callback);
        };
    }

    /**
     * Publish an event with data.
     * @param {string} event The event name
     * @param {*} data The payload
     */
    emit(event, data = null) {
        if (!this.listeners[event]) {
            return;
        }
        
        this.listeners[event].forEach(callback => {
            try {
                callback(data);
            } catch (error) {
                console.error(`Error in event listener for ${event}:`, error);
            }
        });
    }

    /**
     * Subscribe to an event, but only trigger once.
     * @param {string} event The event name
     * @param {function} callback The callback function
     */
    once(event, callback) {
        const unsubscribe = this.on(event, (data) => {
            unsubscribe();
            callback(data);
        });
    }

    /**
     * Remove all listeners for an event.
     * @param {string} event The event name
     */
    off(event) {
        if (this.listeners[event]) {
            delete this.listeners[event];
        }
    }
}

// Make it globally available
window.EventBus = new EventBus();

export default window.EventBus;
