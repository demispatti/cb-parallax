/**
 * Media Uploader
 *
 * WordPress media uploader integration
 *
 * @class MediaUploader
 */

export class MediaUploader {
    constructor(options = {}) {
        this.options = options;
        this.frame = null;

        this.init();
    }

    init() {
        this.attachEventListeners();
    }

    attachEventListeners() {
        const uploadButtons = document.querySelectorAll('.cb-parallax-upload-button');
        uploadButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.openMediaFrame();
            });
        });
    }

    openMediaFrame() {
        if (this.frame) {
            this.frame.open();
            return;
        }

        // Create WordPress media frame
        if (typeof wp !== 'undefined' && wp.media) {
            this.frame = wp.media({
                title: 'Select Background Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            this.frame.on('select', () => {
                const attachment = this.frame.state().get('selection').first().toJSON();
                if (this.options.onSelect) {
                    this.options.onSelect(attachment);
                }
            });

            this.frame.open();
        }
    }
}

export default MediaUploader;
