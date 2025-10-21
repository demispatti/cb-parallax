/**
 * Professional SmoothScroll Implementation
 *
 * A modern, high-performance smooth scrolling library using requestAnimationFrame
 * and modern JavaScript patterns. Provides smooth, customizable scrolling behavior
 * with extensive browser support.
 *
 * @class SmoothScroll
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

export class SmoothScroll {
    /**
     * Default configuration options
     * @type {Object}
     */
    static DEFAULT_OPTIONS = {
        duration: 468,           // Scroll duration in milliseconds
        easing: 'easeInOutCubic', // Easing function name
        offset: 0,                // Vertical offset in pixels
        updateURL: false,         // Update URL hash on scroll
        callback: null,           // Callback after scroll completes
        cancelOnUserAction: true  // Cancel scroll on user input
    };

    /**
     * Available easing functions
     * @type {Object}
     */
    static EASING_FUNCTIONS = {
        linear: t => t,
        easeInQuad: t => t * t,
        easeOutQuad: t => t * (2 - t),
        easeInOutQuad: t => t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t,
        easeInCubic: t => t * t * t,
        easeOutCubic: t => (--t) * t * t + 1,
        easeInOutCubic: t => t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1,
        easeInQuart: t => t * t * t * t,
        easeOutQuart: t => 1 - (--t) * t * t * t,
        easeInOutQuart: t => t < 0.5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t,
        easeInSine: t => 1 - Math.cos(t * Math.PI / 2),
        easeOutSine: t => Math.sin(t * Math.PI / 2),
        easeInOutSine: t => -(Math.cos(Math.PI * t) - 1) / 2
    };

    /**
     * @param {Object} options - Configuration options
     */
    constructor(options = {}) {
        this.options = { ...SmoothScroll.DEFAULT_OPTIONS, ...options };
        this.isScrolling = false;
        this.animationId = null;
        this.startTime = null;
        this.startPosition = null;
        this.targetPosition = null;

        this.init();
    }

    /**
     * Initialize the smooth scroll instance
     * @private
     */
    init() {
        if (this.options.cancelOnUserAction) {
            this.setupCancelListeners();
        }
    }

    /**
     * Setup event listeners to cancel scrolling on user interaction
     * @private
     */
    setupCancelListeners() {
        const events = ['wheel', 'touchstart', 'keydown'];

        this.cancelHandler = (e) => {
            if (this.isScrolling) {
                // Don't cancel on certain keys
                if (e.type === 'keydown') {
                    const allowedKeys = [33, 34, 35, 36, 38, 40]; // Page Up, Page Down, End, Home, Up, Down
                    if (!allowedKeys.includes(e.keyCode)) {
                        return;
                    }
                }
                this.cancel();
            }
        };

        events.forEach(event => {
            window.addEventListener(event, this.cancelHandler, { passive: true });
        });
    }

    /**
     * Get the current scroll position
     * @returns {number}
     * @private
     */
    getCurrentPosition() {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    }

    /**
     * Get the maximum scroll position
     * @returns {number}
     * @private
     */
    getMaxPosition() {
        return Math.max(
            document.body.scrollHeight,
            document.body.offsetHeight,
            document.documentElement.clientHeight,
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight
        ) - window.innerHeight;
    }

    /**
     * Clamp a value between min and max
     * @param {number} value
     * @param {number} min
     * @param {number} max
     * @returns {number}
     * @private
     */
    clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    /**
     * Get easing function
     * @param {string|function} easing
     * @returns {function}
     * @private
     */
    getEasingFunction(easing) {
        if (typeof easing === 'function') {
            return easing;
        }

        return SmoothScroll.EASING_FUNCTIONS[easing] || SmoothScroll.EASING_FUNCTIONS.easeInOutCubic;
    }

    /**
     * Animation step function
     * @param {number} timestamp
     * @private
     */
    step(timestamp) {
        if (!this.startTime) {
            this.startTime = timestamp;
        }

        const elapsed = timestamp - this.startTime;
        const progress = Math.min(elapsed / this.options.duration, 1);
        const easingFunction = this.getEasingFunction(this.options.easing);
        const easedProgress = easingFunction(progress);

        const currentPosition = this.startPosition + (this.targetPosition - this.startPosition) * easedProgress;

        window.scrollTo(0, currentPosition);

        if (progress < 1) {
            this.animationId = requestAnimationFrame(this.step.bind(this));
        } else {
            this.complete();
        }
    }

    /**
     * Complete the scroll animation
     * @private
     */
    complete() {
        this.isScrolling = false;
        this.animationId = null;
        this.startTime = null;

        if (this.options.callback && typeof this.options.callback === 'function') {
            this.options.callback();
        }

        if (this.options.updateURL && this.targetHash) {
            history.pushState(null, null, this.targetHash);
        }
    }

    /**
     * Cancel the current scroll animation
     */
    cancel() {
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.isScrolling = false;
            this.animationId = null;
            this.startTime = null;
        }
    }

    /**
     * Scroll to a specific position
     * @param {number} position - Target scroll position in pixels
     * @param {Object} options - Override default options for this scroll
     * @returns {Promise} - Resolves when scroll completes
     */
    scrollTo(position, options = {}) {
        return new Promise((resolve) => {
            // Cancel any ongoing scroll
            this.cancel();

            // Merge options
            const scrollOptions = { ...this.options, ...options, callback: resolve };
            this.options = scrollOptions;

            // Get positions
            this.startPosition = this.getCurrentPosition();
            this.targetPosition = this.clamp(
                position + scrollOptions.offset,
                0,
                this.getMaxPosition()
            );

            // If already at target, resolve immediately
            if (Math.abs(this.targetPosition - this.startPosition) < 1) {
                resolve();
                return;
            }

            // Start animation
            this.isScrolling = true;
            this.animationId = requestAnimationFrame(this.step.bind(this));
        });
    }

    /**
     * Scroll to a DOM element
     * @param {HTMLElement|string} element - Element or selector
     * @param {Object} options - Override default options for this scroll
     * @returns {Promise} - Resolves when scroll completes
     */
    scrollToElement(element, options = {}) {
        const target = typeof element === 'string'
            ? document.querySelector(element)
            : element;

        if (!target) {
            console.warn(`SmoothScroll: Element not found:`, element);
            return Promise.reject(new Error('Element not found'));
        }

        const targetPosition = target.getBoundingClientRect().top + this.getCurrentPosition();

        // Store hash if element has ID
        if (target.id) {
            this.targetHash = `#${target.id}`;
        }

        return this.scrollTo(targetPosition, options);
    }

    /**
     * Scroll to top of the page
     * @param {Object} options - Override default options for this scroll
     * @returns {Promise} - Resolves when scroll completes
     */
    scrollToTop(options = {}) {
        return this.scrollTo(0, options);
    }

    /**
     * Scroll to bottom of the page
     * @param {Object} options - Override default options for this scroll
     * @returns {Promise} - Resolves when scroll completes
     */
    scrollToBottom(options = {}) {
        return this.scrollTo(this.getMaxPosition(), options);
    }

    /**
     * Scroll by a relative amount
     * @param {number} delta - Amount to scroll in pixels (positive = down, negative = up)
     * @param {Object} options - Override default options for this scroll
     * @returns {Promise} - Resolves when scroll completes
     */
    scrollBy(delta, options = {}) {
        const currentPosition = this.getCurrentPosition();
        return this.scrollTo(currentPosition + delta, options);
    }

    /**
     * Destroy the smooth scroll instance
     */
    destroy() {
        this.cancel();

        if (this.options.cancelOnUserAction && this.cancelHandler) {
            const events = ['wheel', 'touchstart', 'keydown'];
            events.forEach(event => {
                window.removeEventListener(event, this.cancelHandler);
            });
        }
    }

    /**
     * Check if smooth scroll is supported
     * @returns {boolean}
     */
    static isSupported() {
        return 'requestAnimationFrame' in window &&
               'cancelAnimationFrame' in window &&
               'pageYOffset' in window;
    }

    /**
     * Create a polyfill for CSS scroll-behavior
     * This makes scroll-behavior: smooth work in all browsers
     */
    static polyfill() {
        if (!SmoothScroll.isSupported()) {
            console.warn('SmoothScroll: Browser does not support required features');
            return;
        }

        // Check if native smooth scrolling is already supported
        if ('scrollBehavior' in document.documentElement.style) {
            return; // Native support exists, no polyfill needed
        }

        // Create global instance
        const globalScroller = new SmoothScroll();

        // Override window.scrollTo
        const originalScrollTo = window.scrollTo;
        window.scrollTo = function(x, y) {
            if (typeof x === 'object' && x.behavior === 'smooth') {
                globalScroller.scrollTo(x.top || y || 0);
            } else {
                originalScrollTo.call(window, x, y);
            }
        };

        // Override Element.scrollIntoView
        const originalScrollIntoView = Element.prototype.scrollIntoView;
        Element.prototype.scrollIntoView = function(arg) {
            if (typeof arg === 'object' && arg.behavior === 'smooth') {
                globalScroller.scrollToElement(this);
            } else {
                originalScrollIntoView.call(this, arg);
            }
        };
    }
}

export default SmoothScroll;
