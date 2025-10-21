/**
 * Device Detection Utility
 *
 * Modern device detection using feature detection and viewport queries
 * instead of fragile user-agent string parsing.
 *
 * @class DeviceDetector
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

export class DeviceDetector {
    /**
     * Detect device characteristics
     * @returns {Object}
     */
    static detect() {
        const detector = new DeviceDetector();
        return detector.getDeviceInfo();
    }

    /**
     * Get comprehensive device information
     * @returns {Object}
     */
    getDeviceInfo() {
        return {
            isMobile: this.isMobile(),
            isTablet: this.isTablet(),
            isDesktop: this.isDesktop(),
            isTouchDevice: this.isTouchDevice(),
            hasHover: this.hasHover(),
            screenSize: this.getScreenSize(),
            orientation: this.getOrientation(),
            pixelRatio: this.getPixelRatio(),
            prefersReducedMotion: this.prefersReducedMotion(),
            os: this.getOS(),
            browser: this.getBrowser()
        };
    }

    /**
     * Check if device is mobile
     * Uses viewport width and touch capability
     * @returns {boolean}
     */
    isMobile() {
        // Primary: Check viewport width
        if (window.innerWidth <= 768) {
            return true;
        }

        // Secondary: Check touch and specific mobile patterns
        if (this.isTouchDevice() && window.innerWidth <= 1024) {
            // Check for mobile-specific patterns in user agent as fallback
            const mobilePattern = /android|webos|iphone|ipod|blackberry|iemobile|opera mini/i;
            return mobilePattern.test(navigator.userAgent.toLowerCase());
        }

        return false;
    }

    /**
     * Check if device is tablet
     * @returns {boolean}
     */
    isTablet() {
        const width = window.innerWidth;

        // Tablets typically between 768px and 1024px with touch
        if (width > 768 && width <= 1024 && this.isTouchDevice()) {
            return true;
        }

        // iPad specific detection
        const isIPad = /ipad/i.test(navigator.userAgent.toLowerCase()) ||
                      (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        return isIPad;
    }

    /**
     * Check if device is desktop
     * @returns {boolean}
     */
    isDesktop() {
        return !this.isMobile() && !this.isTablet();
    }

    /**
     * Check if device supports touch
     * @returns {boolean}
     */
    isTouchDevice() {
        return (
            'ontouchstart' in window ||
            navigator.maxTouchPoints > 0 ||
            navigator.msMaxTouchPoints > 0
        );
    }

    /**
     * Check if device supports hover
     * @returns {boolean}
     */
    hasHover() {
        if (window.matchMedia) {
            return window.matchMedia('(hover: hover)').matches;
        }

        // Fallback: assume non-touch devices have hover
        return !this.isTouchDevice();
    }

    /**
     * Get screen size category
     * @returns {string}
     */
    getScreenSize() {
        const width = window.innerWidth;

        if (width < 576) return 'xs'; // Extra small
        if (width < 768) return 'sm'; // Small
        if (width < 992) return 'md'; // Medium
        if (width < 1200) return 'lg'; // Large
        if (width < 1400) return 'xl'; // Extra large
        return 'xxl'; // Extra extra large
    }

    /**
     * Get device orientation
     * @returns {string}
     */
    getOrientation() {
        if (window.matchMedia) {
            if (window.matchMedia('(orientation: portrait)').matches) {
                return 'portrait';
            }
            if (window.matchMedia('(orientation: landscape)').matches) {
                return 'landscape';
            }
        }

        // Fallback
        return window.innerHeight > window.innerWidth ? 'portrait' : 'landscape';
    }

    /**
     * Get device pixel ratio
     * @returns {number}
     */
    getPixelRatio() {
        return window.devicePixelRatio || 1;
    }

    /**
     * Check if user prefers reduced motion
     * @returns {boolean}
     */
    prefersReducedMotion() {
        if (window.matchMedia) {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        }
        return false;
    }

    /**
     * Detect operating system
     * @returns {string}
     */
    getOS() {
        const userAgent = navigator.userAgent.toLowerCase();
        const platform = navigator.platform.toLowerCase();

        if (/iphone|ipad|ipod/.test(userAgent)) return 'ios';
        if (/android/.test(userAgent)) return 'android';
        if (/win/.test(platform)) return 'windows';
        if (/mac/.test(platform)) return 'macos';
        if (/linux/.test(platform)) return 'linux';

        return 'unknown';
    }

    /**
     * Detect browser
     * @returns {string}
     */
    getBrowser() {
        const userAgent = navigator.userAgent.toLowerCase();

        if (userAgent.indexOf('edg') > -1) return 'edge';
        if (userAgent.indexOf('chrome') > -1) return 'chrome';
        if (userAgent.indexOf('safari') > -1) return 'safari';
        if (userAgent.indexOf('firefox') > -1) return 'firefox';
        if (userAgent.indexOf('msie') > -1 || userAgent.indexOf('trident') > -1) return 'ie';

        return 'unknown';
    }

    /**
     * Check if device is low-end based on various factors
     * @returns {boolean}
     */
    isLowEndDevice() {
        // Check for hardware concurrency (CPU cores)
        const cores = navigator.hardwareConcurrency || 0;

        // Check for device memory (if available)
        const memory = navigator.deviceMemory || 0;

        // Low-end indicators
        if (cores > 0 && cores <= 2) return true;
        if (memory > 0 && memory <= 2) return true;

        return false;
    }

    /**
     * Get connection information
     * @returns {Object}
     */
    getConnectionInfo() {
        const connection = navigator.connection ||
                          navigator.mozConnection ||
                          navigator.webkitConnection;

        if (!connection) {
            return { type: 'unknown', effectiveType: 'unknown' };
        }

        return {
            type: connection.type || 'unknown',
            effectiveType: connection.effectiveType || 'unknown',
            downlink: connection.downlink || 0,
            rtt: connection.rtt || 0,
            saveData: connection.saveData || false
        };
    }

    /**
     * Check if performance optimizations should be enabled
     * @returns {boolean}
     */
    shouldOptimizePerformance() {
        return this.isLowEndDevice() ||
               this.prefersReducedMotion() ||
               this.getConnectionInfo().saveData;
    }
}

export default DeviceDetector;
