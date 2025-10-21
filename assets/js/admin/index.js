/**
 * CB Parallax Admin JavaScript Entry Point
 *
 * Modern ES6+ module entry point for admin functionality
 *
 * @package CbParallax
 * @subpackage JavaScript/Admin
 * @author CB Parallax Team
 * @version 1.0.0
 * @license GPL-2.0-or-later
 */

import { SettingsManager } from './SettingsManager.js';
import { MediaUploader } from './MediaUploader.js';
import { FormValidator } from './FormValidator.js';

/**
 * Main admin application class
 */
class CbParallaxAdmin {
    constructor() {
        this.settingsManager = null;
        this.mediaUploader = null;
        this.formValidator = null;
        this.config = null;

        this.init();
    }

    /**
     * Initialize the admin application
     */
    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    /**
     * Setup the admin application
     */
    setup() {
        // Get configuration from WordPress
        this.config = window.Cb_Parallax_Admin || {};

        // Initialize components only on settings page
        if (this.isSettingsPage()) {
            this.initSettingsManager();
            this.initMediaUploader();
            this.initFormValidator();
        }

        // Initialize metabox functionality on post edit screens
        if (this.isPostEditScreen()) {
            this.initMetaBox();
        }
    }

    /**
     * Check if we're on the settings page
     * @returns {boolean}
     */
    isSettingsPage() {
        return document.body.classList.contains('cb-parallax-settings-page') ||
               document.querySelector('.cb-parallax-settings-form') !== null;
    }

    /**
     * Check if we're on a post edit screen
     * @returns {boolean}
     */
    isPostEditScreen() {
        return document.body.classList.contains('post-php') ||
               document.body.classList.contains('post-new-php');
    }

    /**
     * Initialize settings manager
     */
    initSettingsManager() {
        try {
            this.settingsManager = new SettingsManager(this.config);
        } catch (error) {
            console.error('CbParallax Admin: Failed to initialize settings manager', error);
        }
    }

    /**
     * Initialize media uploader
     */
    initMediaUploader() {
        try {
            this.mediaUploader = new MediaUploader({
                onSelect: (attachment) => {
                    if (this.settingsManager) {
                        this.settingsManager.updateBackgroundImage(attachment);
                    }
                }
            });
        } catch (error) {
            console.error('CbParallax Admin: Failed to initialize media uploader', error);
        }
    }

    /**
     * Initialize form validator
     */
    initFormValidator() {
        try {
            const form = document.querySelector('.cb-parallax-settings-form');
            if (form) {
                this.formValidator = new FormValidator(form);
            }
        } catch (error) {
            console.error('CbParallax Admin: Failed to initialize form validator', error);
        }
    }

    /**
     * Initialize metabox functionality
     */
    initMetaBox() {
        // Metabox functionality will be handled by existing code for now
        // Can be modernized in future iteration
        console.log('CbParallax Admin: Metabox initialized');
    }
}

// Initialize the admin application
const cbParallaxAdmin = new CbParallaxAdmin();

// Export for module usage
export { CbParallaxAdmin, cbParallaxAdmin };
export default cbParallaxAdmin;
