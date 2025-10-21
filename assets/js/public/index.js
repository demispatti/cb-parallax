/**
 * CB Parallax Public JavaScript Entry Point
 *
 * Modern ES6+ module entry point for frontend functionality
 *
 * @package CbParallax
 * @subpackage JavaScript/Public
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

import { ParallaxEngine } from './ParallaxEngine.js';
import { SmoothScroll } from '../lib/smoothscroll/SmoothScroll.js';
import { DeviceDetector } from '../utils/DeviceDetector.js';

/**
 * Main application class
 */
class CbParallaxPublic {
    constructor() {
        this.parallaxEngine = null;
        this.smoothScroll = null;
        this.config = null;
        this.device = null;

        this.init();
    }

    /**
     * Initialize the application
     */
    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * Setup the application
     */
    setup() {
        // Get configuration from WordPress localized script
        this.config = window.Cb_Parallax_Public || {};

        // Detect device
        this.device = DeviceDetector.detect();

        // Initialize smooth scrolling if enabled
        if (this.shouldEnableSmoothScroll()) {
            this.initSmoothScroll();
        }

        // Initialize parallax if we have image options
        if (this.hasParallaxConfig()) {
            this.initParallax();
        }

        // Expose API to window for debugging
        if (this.config.debug) {
            this.exposeDebugAPI();
        }
    }

    /**
     * Check if smooth scroll should be enabled
     * @returns {boolean}
     */
    shouldEnableSmoothScroll() {
        const pluginOptions = this.config.plugin_options || {};
        return pluginOptions.preserve_scrolling === '1';
    }

    /**
     * Check if we have parallax configuration
     * @returns {boolean}
     */
    hasParallaxConfig() {
        return this.config.image_options && this.config.image_options.background_image_url;
    }

    /**
     * Initialize smooth scrolling
     */
    initSmoothScroll() {
        try {
            this.smoothScroll = new SmoothScroll({
                duration: 468,
                easing: 'easeInOutCubic',
                cancelOnUserAction: true
            });

            // Apply polyfill for older browsers
            SmoothScroll.polyfill();
        } catch (error) {
            console.error('CbParallax: Failed to initialize smooth scroll', error);
        }
    }

    /**
     * Initialize parallax engine
     */
    initParallax() {
        try {
            const imageOptions = this.config.image_options || {};
            const pluginOptions = this.config.plugin_options || {};
            const overlayOptions = this.config.overlay_options || {};

            const parallaxConfig = {
                // Image settings
                backgroundImageUrl: imageOptions.background_image_url,
                backgroundColor: imageOptions.background_color || '',
                positionX: imageOptions.position_x || 'center',
                positionY: imageOptions.position_y || 'center',
                backgroundAttachment: imageOptions.background_attachment || 'fixed',
                backgroundRepeat: imageOptions.background_repeat || 'no-repeat',

                // Parallax settings
                parallaxEnabled: pluginOptions.parallax_enabled === '1',
                canParallax: imageOptions.can_parallax === '1',
                disableOnMobile: pluginOptions.disable_on_mobile === '1',
                direction: imageOptions.direction || 'vertical',
                verticalScrollDirection: imageOptions.vertical_scroll_direction || 'top',
                horizontalScrollDirection: imageOptions.horizontal_scroll_direction || 'left',
                horizontalAlignment: imageOptions.horizontal_alignment || 'center',
                verticalAlignment: imageOptions.vertical_alignment || 'center',
                parallaxFactor: 1.2,

                // Overlay settings
                overlay: {
                    enabled: overlayOptions.overlay_image !== 'none',
                    path: overlayOptions.overlay_path || '',
                    image: overlayOptions.overlay_image || 'none',
                    opacity: overlayOptions.overlay_opacity || '0.3',
                    color: overlayOptions.overlay_color || ''
                },

                // Performance settings
                performance: {
                    useWillChange: !this.device.isLowEndDevice,
                    useGPU: true,
                    throttleResize: 150
                }
            };

            this.parallaxEngine = new ParallaxEngine(document.body, parallaxConfig);
        } catch (error) {
            console.error('CbParallax: Failed to initialize parallax', error);
        }
    }

    /**
     * Expose debug API to window
     */
    exposeDebugAPI() {
        window.CbParallaxDebug = {
            getConfig: () => this.config,
            getDevice: () => this.device,
            getParallaxEngine: () => this.parallaxEngine,
            getSmoothScroll: () => this.smoothScroll,
            reload: () => {
                if (this.parallaxEngine) {
                    this.parallaxEngine.destroy();
                }
                this.setup();
            }
        };

        console.log('CbParallax: Debug API available at window.CbParallaxDebug');
    }

    /**
     * Destroy the application
     */
    destroy() {
        if (this.parallaxEngine) {
            this.parallaxEngine.destroy();
            this.parallaxEngine = null;
        }

        if (this.smoothScroll) {
            this.smoothScroll.destroy();
            this.smoothScroll = null;
        }
    }
}

// Initialize the application
const cbParallaxApp = new CbParallaxPublic();

// Export for module usage
export { CbParallaxPublic, cbParallaxApp };
export default cbParallaxApp;
