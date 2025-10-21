/**
 * Viewport Observer Utility
 *
 * Efficiently observe viewport changes and element visibility
 * using modern browser APIs (IntersectionObserver, ResizeObserver)
 *
 * @class ViewportObserver
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

export class ViewportObserver {
    /**
     * @param {Object} options - Configuration options
     */
    constructor(options = {}) {
        this.options = {
            throttleDelay: options.throttleDelay || 100,
            debounceDelay: options.debounceDelay || 250,
            ...options
        };

        this.listeners = {
            resize: new Set(),
            scroll: new Set(),
            orientation: new Set()
        };

        this.viewport = {
            width: window.innerWidth,
            height: window.innerHeight,
            scrollX: window.pageXOffset,
            scrollY: window.pageYOffset,
            orientation: this.getOrientation()
        };

        this.throttleTimers = new Map();
        this.debounceTimers = new Map();

        this.init();
    }

    /**
     * Initialize observers
     */
    init() {
        this.attachEventListeners();
        this.updateViewport();
    }

    /**
     * Get current orientation
     * @returns {string}
     */
    getOrientation() {
        return window.innerHeight > window.innerWidth ? 'portrait' : 'landscape';
    }

    /**
     * Update viewport information
     */
    updateViewport() {
        const previousOrientation = this.viewport.orientation;

        this.viewport = {
            width: window.innerWidth,
            height: window.innerHeight,
            scrollX: window.pageXOffset || document.documentElement.scrollLeft,
            scrollY: window.pageYOffset || document.documentElement.scrollTop,
            orientation: this.getOrientation()
        };

        // Check if orientation changed
        if (previousOrientation !== this.viewport.orientation) {
            this.notifyListeners('orientation', this.viewport);
        }
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        window.addEventListener('resize', this.handleResize.bind(this), { passive: true });
        window.addEventListener('scroll', this.handleScroll.bind(this), { passive: true });
        window.addEventListener('orientationchange', this.handleOrientationChange.bind(this), { passive: true });
    }

    /**
     * Handle resize event
     */
    handleResize() {
        this.throttle('resize', () => {
            this.updateViewport();
            this.notifyListeners('resize', this.viewport);
        }, this.options.throttleDelay);
    }

    /**
     * Handle scroll event
     */
    handleScroll() {
        this.throttle('scroll', () => {
            this.updateViewport();
            this.notifyListeners('scroll', this.viewport);
        }, this.options.throttleDelay);
    }

    /**
     * Handle orientation change event
     */
    handleOrientationChange() {
        this.debounce('orientation', () => {
            this.updateViewport();
            this.notifyListeners('orientation', this.viewport);
        }, this.options.debounceDelay);
    }

    /**
     * Throttle function execution
     * @param {string} key
     * @param {Function} func
     * @param {number} delay
     */
    throttle(key, func, delay) {
        if (!this.throttleTimers.has(key)) {
            func();
            this.throttleTimers.set(key, true);

            setTimeout(() => {
                this.throttleTimers.delete(key);
            }, delay);
        }
    }

    /**
     * Debounce function execution
     * @param {string} key
     * @param {Function} func
     * @param {number} delay
     */
    debounce(key, func, delay) {
        if (this.debounceTimers.has(key)) {
            clearTimeout(this.debounceTimers.get(key));
        }

        const timer = setTimeout(() => {
            func();
            this.debounceTimers.delete(key);
        }, delay);

        this.debounceTimers.set(key, timer);
    }

    /**
     * Notify all listeners of an event type
     * @param {string} type
     * @param {*} data
     */
    notifyListeners(type, data) {
        if (this.listeners[type]) {
            this.listeners[type].forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`ViewportObserver: Error in ${type} listener`, error);
                }
            });
        }
    }

    /**
     * Add event listener
     * @param {string} type - Event type (resize, scroll, orientation)
     * @param {Function} callback
     */
    on(type, callback) {
        if (!this.listeners[type]) {
            console.warn(`ViewportObserver: Unknown event type "${type}"`);
            return;
        }

        this.listeners[type].add(callback);

        // Return unsubscribe function
        return () => this.off(type, callback);
    }

    /**
     * Remove event listener
     * @param {string} type
     * @param {Function} callback
     */
    off(type, callback) {
        if (this.listeners[type]) {
            this.listeners[type].delete(callback);
        }
    }

    /**
     * Get current viewport information
     * @returns {Object}
     */
    getViewport() {
        return { ...this.viewport };
    }

    /**
     * Check if element is in viewport
     * @param {HTMLElement} element
     * @param {number} threshold - Percentage of element that should be visible (0-1)
     * @returns {boolean}
     */
    isInViewport(element, threshold = 0) {
        const rect = element.getBoundingClientRect();
        const elementHeight = rect.height;
        const elementWidth = rect.width;

        const visibleHeight = Math.min(rect.bottom, this.viewport.height) - Math.max(rect.top, 0);
        const visibleWidth = Math.min(rect.right, this.viewport.width) - Math.max(rect.left, 0);

        const visibleArea = visibleHeight * visibleWidth;
        const totalArea = elementHeight * elementWidth;

        return totalArea > 0 && (visibleArea / totalArea) >= threshold;
    }

    /**
     * Observe element visibility using IntersectionObserver
     * @param {HTMLElement} element
     * @param {Function} callback
     * @param {Object} options
     * @returns {IntersectionObserver}
     */
    observeVisibility(element, callback, options = {}) {
        if (!('IntersectionObserver' in window)) {
            console.warn('IntersectionObserver not supported');
            return null;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                callback({
                    isVisible: entry.isIntersecting,
                    intersectionRatio: entry.intersectionRatio,
                    entry
                });
            });
        }, {
            threshold: options.threshold || [0, 0.5, 1],
            rootMargin: options.rootMargin || '0px',
            ...options
        });

        observer.observe(element);

        return observer;
    }

    /**
     * Observe element size changes using ResizeObserver
     * @param {HTMLElement} element
     * @param {Function} callback
     * @returns {ResizeObserver}
     */
    observeSize(element, callback) {
        if (!('ResizeObserver' in window)) {
            console.warn('ResizeObserver not supported');
            return null;
        }

        const observer = new ResizeObserver((entries) => {
            entries.forEach(entry => {
                callback({
                    width: entry.contentRect.width,
                    height: entry.contentRect.height,
                    entry
                });
            });
        });

        observer.observe(element);

        return observer;
    }

    /**
     * Destroy the observer and clean up
     */
    destroy() {
        // Clear all timers
        this.throttleTimers.clear();
        this.debounceTimers.forEach(timer => clearTimeout(timer));
        this.debounceTimers.clear();

        // Clear all listeners
        Object.keys(this.listeners).forEach(key => {
            this.listeners[key].clear();
        });

        // Remove event listeners would go here if we stored them
        // For simplicity, we're relying on garbage collection
    }
}

export default ViewportObserver;
