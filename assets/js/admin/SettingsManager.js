/**
 * Settings Manager
 *
 * Manages the settings page UI and interactions
 *
 * @class SettingsManager
 */

export class SettingsManager {
    constructor(config) {
        this.config = config;
        this.form = document.querySelector('.cb-parallax-settings-form');

        if (this.form) {
            this.init();
        }
    }

    init() {
        this.attachEventListeners();
        this.initializeUI();
    }

    attachEventListeners() {
        // Event listeners for form interactions
        const parallaxSwitch = document.querySelector('#parallax-enabled');
        if (parallaxSwitch) {
            parallaxSwitch.addEventListener('change', this.handleParallaxToggle.bind(this));
        }
    }

    initializeUI() {
        // Initialize UI state based on current settings
        this.updateUIState();
    }

    handleParallaxToggle(e) {
        const isEnabled = e.target.checked;
        this.toggleParallaxOptions(isEnabled);
    }

    toggleParallaxOptions(enabled) {
        const parallaxOptions = document.querySelectorAll('.parallax-option');
        parallaxOptions.forEach(option => {
            option.style.display = enabled ? 'block' : 'none';
        });
    }

    updateBackgroundImage(attachment) {
        // Update background image fields
        const imageField = document.querySelector('#background-image-url');
        const idField = document.querySelector('#background-image-id');

        if (imageField) imageField.value = attachment.url;
        if (idField) idField.value = attachment.id;
    }

    updateUIState() {
        // Update UI based on current state
        const parallaxEnabled = document.querySelector('#parallax-enabled');
        if (parallaxEnabled) {
            this.toggleParallaxOptions(parallaxEnabled.checked);
        }
    }
}

export default SettingsManager;
