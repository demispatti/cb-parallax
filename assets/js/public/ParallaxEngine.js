/**
 * Professional Parallax Engine
 *
 * A modern, high-performance parallax scrolling engine using HTML5 Canvas,
 * requestAnimationFrame, and modern JavaScript patterns.
 *
 * @class ParallaxEngine
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

import { DeviceDetector } from '../utils/DeviceDetector.js';
import { ViewportObserver } from '../utils/ViewportObserver.js';

export class ParallaxEngine {
    /**
     * Default configuration
     * @type {Object}
     */
    static DEFAULT_CONFIG = {
        parallaxFactor: 1.2,
        direction: 'vertical',
        verticalScrollDirection: 'top',
        horizontalScrollDirection: 'left',
        horizontalAlignment: 'center',
        verticalAlignment: 'center',
        backgroundColor: '',
        backgroundRepeat: 'no-repeat',
        overlay: {
            enabled: false,
            image: 'none',
            opacity: 0.3,
            color: ''
        },
        performance: {
            useWillChange: true,
            useGPU: true,
            throttleResize: 150
        }
    };

    /**
     * @param {HTMLElement} container - Container element for parallax
     * @param {Object} options - Configuration options
     */
    constructor(container, options = {}) {
        this.container = container;
        this.config = { ...ParallaxEngine.DEFAULT_CONFIG, ...options };

        // Core elements
        this.canvas = null;
        this.context = null;
        this.overlay = null;

        // Image properties
        this.image = null;
        this.imageLoaded = false;
        this.imageAspectRatio = 0;

        // State
        this.isScrolling = false;
        this.isResizing = false;
        this.rafId = null;
        this.resizeTimeout = null;

        // Dimensions
        this.viewport = {
            width: 0,
            height: 0,
            aspectRatio: 0
        };

        this.canvasDimensions = {
            width: 0,
            height: 0
        };

        // Device detection
        this.device = DeviceDetector.detect();

        // Bind methods
        this.handleScroll = this.handleScroll.bind(this);
        this.handleResize = this.handleResize.bind(this);
        this.animate = this.animate.bind(this);

        this.init();
    }

    /**
     * Initialize the parallax engine
     */
    async init() {
        try {
            this.updateViewportDimensions();

            if (this.shouldEnableParallax()) {
                await this.loadImage();
                this.setupCanvas();
                this.setupOverlay();
                this.attachEventListeners();
                this.startAnimation();
            } else {
                this.setupStaticBackground();
            }
        } catch (error) {
            console.error('ParallaxEngine: Initialization failed', error);
            this.fallbackToStatic();
        }
    }

    /**
     * Check if parallax should be enabled
     * @returns {boolean}
     */
    shouldEnableParallax() {
        if (!this.config.parallaxEnabled) {
            return false;
        }

        if (this.config.disableOnMobile && this.device.isMobile) {
            return false;
        }

        if (!this.config.canParallax) {
            return false;
        }

        return true;
    }

    /**
     * Load the background image
     * @returns {Promise}
     */
    loadImage() {
        return new Promise((resolve, reject) => {
            if (!this.config.backgroundImageUrl) {
                reject(new Error('No background image URL provided'));
                return;
            }

            this.image = new Image();

            this.image.onload = () => {
                this.imageLoaded = true;
                this.imageAspectRatio = this.image.width / this.image.height;
                resolve();
            };

            this.image.onerror = () => {
                reject(new Error('Failed to load background image'));
            };

            this.image.src = this.config.backgroundImageUrl;
        });
    }

    /**
     * Setup canvas element
     */
    setupCanvas() {
        this.calculateCanvasDimensions();

        this.canvas = document.createElement('canvas');
        this.canvas.id = 'cb-parallax-canvas';
        this.canvas.className = 'cb-parallax-canvas';
        this.canvas.width = this.canvasDimensions.width;
        this.canvas.height = this.canvasDimensions.height;

        if (this.config.backgroundColor) {
            this.canvas.style.backgroundColor = this.config.backgroundColor;
        }

        // Performance optimizations
        if (this.config.performance.useWillChange) {
            this.canvas.style.willChange = 'transform';
        }

        this.context = this.canvas.getContext('2d', {
            alpha: false,
            desynchronized: true
        });

        // Insert canvas at the beginning of body
        document.body.insertBefore(this.canvas, document.body.firstChild);

        // Draw initial image
        this.drawImage();
        this.updateCanvasPosition();
    }

    /**
     * Setup overlay element
     */
    setupOverlay() {
        if (!this.config.overlay.enabled &&
            this.config.overlay.image === 'none' &&
            !this.config.overlay.color) {
            return;
        }

        this.overlay = document.createElement('div');
        this.overlay.id = 'cb-parallax-overlay';
        this.overlay.className = 'cb-parallax-overlay';

        if (this.config.overlay.opacity && this.config.overlay.opacity !== '0.0') {
            this.overlay.style.opacity = this.config.overlay.opacity;
        }

        if (this.config.overlay.color) {
            this.overlay.style.backgroundColor = this.config.overlay.color;
        }

        if (this.config.overlay.image && this.config.overlay.image !== 'none') {
            this.overlay.style.backgroundImage = `url(${this.config.overlay.path}${this.config.overlay.image})`;
        }

        document.body.insertBefore(this.overlay, document.body.firstChild);
    }

    /**
     * Calculate canvas dimensions based on parallax factor
     */
    calculateCanvasDimensions() {
        const isLandscape = this.viewport.aspectRatio >= this.imageAspectRatio;

        if (this.config.direction === 'vertical') {
            if (isLandscape) {
                this.canvasDimensions.width = this.viewport.width;
                this.canvasDimensions.height = Math.ceil(
                    this.viewport.width / this.imageAspectRatio * this.config.parallaxFactor
                );
            } else {
                this.canvasDimensions.width = Math.ceil(
                    this.viewport.height * this.imageAspectRatio
                );
                this.canvasDimensions.height = Math.ceil(
                    this.viewport.height * this.config.parallaxFactor
                );
            }
        } else { // horizontal
            if (isLandscape) {
                this.canvasDimensions.width = Math.ceil(
                    this.viewport.width * this.config.parallaxFactor
                );
                this.canvasDimensions.height = Math.ceil(
                    this.viewport.width / this.imageAspectRatio
                );
            } else {
                this.canvasDimensions.width = Math.ceil(
                    this.viewport.height * this.imageAspectRatio * this.config.parallaxFactor
                );
                this.canvasDimensions.height = this.viewport.height;
            }
        }
    }

    /**
     * Draw image on canvas
     */
    drawImage() {
        if (!this.imageLoaded || !this.context) {
            return;
        }

        this.context.drawImage(
            this.image,
            0,
            0,
            this.canvasDimensions.width,
            this.canvasDimensions.height
        );
    }

    /**
     * Update viewport dimensions
     */
    updateViewportDimensions() {
        this.viewport.width = window.innerWidth;
        this.viewport.height = window.innerHeight;
        this.viewport.aspectRatio = this.viewport.width / this.viewport.height;
    }

    /**
     * Calculate parallax transform
     * @returns {Object}
     */
    calculateParallaxTransform() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        const maxScroll = document.documentElement.scrollHeight - this.viewport.height;
        const scrollProgress = scrollTop / maxScroll;

        let translateX = 0;
        let translateY = 0;

        if (this.config.direction === 'vertical') {
            const maxTranslate = this.canvasDimensions.height - this.viewport.height;

            if (this.config.verticalScrollDirection === 'top') {
                translateY = -scrollProgress * maxTranslate;
            } else {
                translateY = scrollProgress * maxTranslate;
            }

            // Horizontal alignment
            const isLandscape = this.viewport.aspectRatio >= this.imageAspectRatio;
            if (!isLandscape) {
                const maxHorizontalTranslate = this.canvasDimensions.width - this.viewport.width;

                switch (this.config.horizontalAlignment) {
                    case 'left':
                        translateX = 0;
                        break;
                    case 'center':
                        translateX = -maxHorizontalTranslate / 2;
                        break;
                    case 'right':
                        translateX = -maxHorizontalTranslate;
                        break;
                }
            }
        } else { // horizontal
            const maxTranslate = this.canvasDimensions.width - this.viewport.width;

            if (this.config.horizontalScrollDirection === 'left') {
                translateX = -scrollProgress * maxTranslate;
            } else {
                translateX = scrollProgress * maxTranslate;
            }

            // Vertical alignment
            const isLandscape = this.viewport.aspectRatio >= this.imageAspectRatio;
            if (isLandscape) {
                const maxVerticalTranslate = this.canvasDimensions.height - this.viewport.height;

                switch (this.config.verticalAlignment) {
                    case 'top':
                        translateY = 0;
                        break;
                    case 'center':
                        translateY = -maxVerticalTranslate / 2;
                        break;
                    case 'bottom':
                        translateY = -maxVerticalTranslate;
                        break;
                }
            }
        }

        return { translateX, translateY };
    }

    /**
     * Update canvas position with transform
     */
    updateCanvasPosition() {
        if (!this.canvas) {
            return;
        }

        const transform = this.calculateParallaxTransform();
        const translate = this.config.performance.useGPU
            ? `translate3d(${transform.translateX}px, ${transform.translateY}px, 0)`
            : `translate(${transform.translateX}px, ${transform.translateY}px)`;

        this.canvas.style.transform = translate;
    }

    /**
     * Handle scroll event
     */
    handleScroll() {
        if (!this.isScrolling) {
            this.isScrolling = true;
        }
    }

    /**
     * Handle resize event
     */
    handleResize() {
        clearTimeout(this.resizeTimeout);

        this.resizeTimeout = setTimeout(() => {
            this.updateViewportDimensions();
            this.calculateCanvasDimensions();

            if (this.canvas) {
                this.canvas.width = this.canvasDimensions.width;
                this.canvas.height = this.canvasDimensions.height;
                this.drawImage();
                this.updateCanvasPosition();
            }
        }, this.config.performance.throttleResize);
    }

    /**
     * Animation loop
     */
    animate() {
        if (this.isScrolling) {
            this.updateCanvasPosition();
            this.isScrolling = false;
        }

        this.rafId = requestAnimationFrame(this.animate);
    }

    /**
     * Start animation loop
     */
    startAnimation() {
        if (!this.rafId) {
            this.rafId = requestAnimationFrame(this.animate);
        }
    }

    /**
     * Stop animation loop
     */
    stopAnimation() {
        if (this.rafId) {
            cancelAnimationFrame(this.rafId);
            this.rafId = null;
        }
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        window.addEventListener('scroll', this.handleScroll, { passive: true });
        window.addEventListener('resize', this.handleResize, { passive: true });
    }

    /**
     * Detach event listeners
     */
    detachEventListeners() {
        window.removeEventListener('scroll', this.handleScroll);
        window.removeEventListener('resize', this.handleResize);
    }

    /**
     * Setup static background (no parallax)
     */
    setupStaticBackground() {
        if (!this.config.backgroundImageUrl) {
            return;
        }

        document.body.style.backgroundImage = `url(${this.config.backgroundImageUrl})`;
        document.body.style.backgroundSize = 'cover';
        document.body.style.backgroundPosition = `${this.config.positionX} ${this.config.positionY}`;
        document.body.style.backgroundAttachment = this.config.backgroundAttachment || 'fixed';
        document.body.style.backgroundRepeat = this.config.backgroundRepeat || 'no-repeat';

        if (this.config.backgroundColor) {
            document.body.style.backgroundColor = this.config.backgroundColor;
        }
    }

    /**
     * Fallback to static background on error
     */
    fallbackToStatic() {
        console.warn('ParallaxEngine: Falling back to static background');
        this.destroy();
        this.setupStaticBackground();
    }

    /**
     * Destroy the parallax engine
     */
    destroy() {
        this.stopAnimation();
        this.detachEventListeners();

        if (this.canvas && this.canvas.parentNode) {
            this.canvas.parentNode.removeChild(this.canvas);
        }

        if (this.overlay && this.overlay.parentNode) {
            this.overlay.parentNode.removeChild(this.overlay);
        }

        this.canvas = null;
        this.context = null;
        this.overlay = null;
        this.image = null;
    }

    /**
     * Update configuration
     * @param {Object} newConfig
     */
    updateConfig(newConfig) {
        this.config = { ...this.config, ...newConfig };
        this.destroy();
        this.init();
    }
}

export default ParallaxEngine;
