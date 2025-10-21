/**
 * Form Validator
 *
 * Validates form inputs before submission
 *
 * @class FormValidator
 */

export class FormValidator {
    constructor(form) {
        this.form = form;
        this.errors = [];

        this.init();
    }

    init() {
        this.form.addEventListener('submit', this.validate.bind(this));
    }

    validate(e) {
        this.errors = [];

        // Perform validation
        this.validateImageUrl();
        this.validateColorFields();
        this.validateNumericFields();

        if (this.errors.length > 0) {
            e.preventDefault();
            this.displayErrors();
            return false;
        }

        return true;
    }

    validateImageUrl() {
        const imageUrl = this.form.querySelector('#background-image-url');
        if (imageUrl && imageUrl.value && !this.isValidUrl(imageUrl.value)) {
            this.errors.push('Invalid image URL');
        }
    }

    validateColorFields() {
        const colorFields = this.form.querySelectorAll('input[type="color"]');
        colorFields.forEach(field => {
            if (field.value && !this.isValidColor(field.value)) {
                this.errors.push(`Invalid color value: ${field.name}`);
            }
        });
    }

    validateNumericFields() {
        const numericFields = this.form.querySelectorAll('input[type="number"]');
        numericFields.forEach(field => {
            if (field.value && isNaN(field.value)) {
                this.errors.push(`Invalid numeric value: ${field.name}`);
            }
        });
    }

    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    isValidColor(color) {
        return /^#([0-9A-F]{3}){1,2}$/i.test(color) ||
               /^rgba?\([\d\s,]+\)$/i.test(color);
    }

    displayErrors() {
        // Display errors to user
        const errorContainer = document.querySelector('.cb-parallax-errors');
        if (errorContainer) {
            errorContainer.innerHTML = this.errors.map(error =>
                `<div class="error"><p>${error}</p></div>`
            ).join('');
        } else {
            alert(this.errors.join('\n'));
        }
    }
}

export default FormValidator;
