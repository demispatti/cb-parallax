/**
 * The script for the admin menu.
 * @link              https://de.wordpress.org/plugins/cb-parallax
 * @since             0.1.0
 * @package           cb-parallax
 * @subpackage        cb-parallax/admin/menu/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
/*jshint esversion: 6 */
(function($) {
    "use strict";

    function CbParallaxSettingsDisplay()
    {
        // localized values
        this.cb_Parallax = Cb_Parallax_Admin;
        this.canParallax = undefined !== this.cb_Parallax.image_options.cb_parallax_can_parallax ? this.cb_Parallax.image_options.cb_parallax_can_parallax : '0';
        this.cb_parallax_boxed_layout_margin = '200px';
        this.cb_parallax_boxed_layout_content_container_background_color = '';
        this.parallaxEnabled = undefined !== this.cb_Parallax.image_options.cb_parallax_parallax_enabled ? this.cb_Parallax.image_options.cb_parallax_parallax_enabled : '0';
        // 'movable parts' :)
        this.parallaxEnabledSwitch = null;
        this.placeholderImage = null;
        this.backgroundImage = null;
        this.attachmentId = null;
        this.CheckBoxes = null;
        this.parallaxDirectionSelectBox = null;
        this.addMediaButton = null;
        this.removeMediaButton = null;
        // form and buttons
        this.settingsForm = null;
        this.submitButton = null;
        this.resetButton = null;
        // Containers for different settings display (static / parallax options)
        this.verticalScrollDirectionContainer = null;
        this.horizontalScrollDirectionContainer = null;
        this.verticalAlignmentContainer = null;
        this.horizontalAlignmentContainer = null;

        this.direction = undefined !== this.cb_Parallax.image_options.cb_parallax_direction ? this.cb_Parallax.image_options.cb_parallax_direction : this.cb_Parallax.default_options.cb_parallax_direction;
    }

    CbParallaxSettingsDisplay.prototype = {

        constructor: function() {
            // localized values
            this.cb_Parallax = Cb_Parallax_Admin;
            this.canParallax = undefined !== this.cb_Parallax.image_options.can_parallax ? this.cb_Parallax.image_options.can_parallax : '0';
            this.cb_parallax_boxed_layout_margin = '200px';
            this.cb_parallax_boxed_layout_content_container_background_color = '';
            // 'movable parts' :)
            this.parallaxEnabledSwitch = null;
            this.placeholderImage = null;
            this.backgroundImage = null;
            this.attachmentId = null;
            this.CheckBoxes = null;
            this.parallaxDirectionSelectBox = null;
            this.addMediaButton = null;
            this.removeMediaButton = null;
            // form and buttons
            this.settingsForm = null;
            this.submitButton = null;
            this.resetButton = null;
            // Containers for different settings display (static / parallax options)
            this.verticalScrollDirectionContainer = null;
            this.horizontalScrollDirectionContainer = null;
            this.verticalAlignmentContainer = null;
            this.horizontalAlignmentContainer = null;

            this.direction = undefined !== this.cb_Parallax.image_options.cb_parallax_direction ? this.cb_Parallax.image_options.cb_parallax_direction : this.cb_Parallax.default_options.cb_parallax_direction;
        },

        init: function() {
            this.initColorpicker();
            this.initFancySelect();
            this.assembleSettingsContainers();
            this.localizeScript();
            this.initSettingsDisplay();
            this.addEvents();
            this.initContextualHelp();
        },

        assembleSettingsContainers: function() {

            this.allImageOptions = $('#cb_parallax_parallax_enabled_container, #cb_parallax_background_color_container, #cb_parallax_direction_container, #cb_parallax_vertical_scroll_direction_container, #cb_parallax_horizontal_scroll_direction_container, #cb_parallax_horizontal_alignment_container, #cb_parallax_vertical_alignment_container, #cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container, #cb_parallax_position_x_container, #cb_parallax_position_y_container, #cb_parallax_background_attachment_container, #cb_parallax_boxed_layout_settings_title, #cb_parallax_boxed_layout_container, #cb_parallax_boxed_layout_margin_container, #cb_parallax_boxed_layout_content_container_background_color_container, #cb_parallax_uses_assumed_background_color_container, #cb_parallax_allow_override_settings_title, #cb_parallax_allow_override_container, #cb_parallax_disable_on_mobile_settings_title, #cb_parallax_disable_on_mobile_container');

            this.parallaxOptionsContainers = $('#cb_parallax_direction_container, #cb_parallax_vertical_scroll_direction_container, #cb_parallax_horizontal_scroll_direction_container, #cb_parallax_horizontal_alignment_container, #cb_parallax_vertical_alignment_container, #cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container');

            this.pluginOptionsContainers = $('#cb_parallax_parallax_enabled_container, #cb_parallax_boxed_layout_settings_title, #cb_parallax_boxed_layout_container, #cb_parallax_boxed_layout_margin_container, #cb_parallax_boxed_layout_content_container_background_color_container, #cb_parallax_uses_assumed_background_color_container, #cb_parallax_allow_override_settings_title, #cb_parallax_allow_override_container, #cb_parallax_disable_on_mobile_settings_title, #cb_parallax_disable_on_mobile_container, #cb_parallax_remove_media_button');
            

            this.verticalScrollDirectionContainer = $('#cb_parallax_vertical_scroll_direction_container');
            this.horizontalScrollDirectionContainer = $('#cb_parallax_horizontal_scroll_direction_container');
            this.verticalAlignmentContainer = $('#cb_parallax_vertical_alignment_container');
            this.horizontalAlignmentContainer = $('#cb_parallax_horizontal_alignment_container');

            // objects
            this.parallaxEnabledSwitch = $('.cb_parallax_parallax_enabled');
            this.placeholderImage = $('.cb_parallax_placeholder_image');
            this.backgroundImage = $('.cb_parallax_background_image');
            this.attachmentId = $(".cb_parallax_attachment_id");
            this.CheckBoxes = $('.cb-parallax-input-checkbox');
            this.parallaxDirectionSelectBox = $("[data-key='cb_parallax_direction']");
            this.parallaxDirectionSelectBoxTrigger = $("#cb_parallax_direction_container").find('div.trigger');
            this.parallaxSettingsTitle = $('h3.parallax');
            this.staticSettingsTitle = $('h3.static');
            this.addMediaButton = $('.cb-parallax-media-url');
            this.removeMediaButton = $(".cb-parallax-remove-media");
            this.settingsForm = $('#cb_parallax_settings_form');
            this.submitButton = $('#cb_parallax_form_submit');
            this.resetButton = $('#cb_parallax_form_reset');
        },

        initColorpicker: function() {
            $('.cb-parallax-color-picker').wpColorPicker();
        },

        initFancySelect: function() {
            $('.cb-parallax-fancy-select').fancySelect();
        },

        localizeScript: function() {
            /*const off = this.cb_Parallax.strings.switches_text.Off;
            const on = this.cb_Parallax.strings.switches_text.On;*/
            const off = 'off';
            const on = 'on';
            $('<style>.cb-parallax-switch-input ~ .cb-parallax-switch-label:before{content:"' + off + '";}</style>').appendTo('head');
            $('<style>.cb-parallax-switch-input:checked ~ .cb-parallax-switch-label:after{content:"' + on + '";}</style>').appendTo('head');
        },

        // Events
        addEvents: function() {
            this.CheckBoxes.on('click', this.toggleCheckbox);
            this.parallaxDirectionSelectBoxTrigger.on('change', {context: this}, this.toggleParallaxEnabledCheckbox);
            this.parallaxEnabledSwitch.on('change', {context: this}, this.initSettingsDisplay);
            this.parallaxDirectionSelectBox.on('change.fs', {context: this}, this.toggleParallaxEnabledCheckbox);
            this.removeMediaButton.on('click', {context: this}, this.removeMedia);
            this.addMediaButton.on('click', {context: this}, this.addMedia);
            this.submitButton.on('click', {context: this}, this.cbparallaxSaveOptions);
            this.resetButton.on('click', {context: this}, this.cbparallaxResetOptions);
        },

        // Actions
        initSettingsDisplay: function(event) {
            let $this = this;
            if (undefined !== event) {
                $this = event.data.context;
            }
            
            // Hide all options at first
            let parallaxEnabled = undefined !== $("[data-key='cb_parallax_parallax_enabled']") ? $("[data-key='cb_parallax_parallax_enabled']").val() : $this.parallaxEnabled;

            $this.allImageOptions.hide();
            $this.pluginOptionsContainers.show();
            if ('1' === parallaxEnabled) {
                $this.parallaxOptionsContainers.show();
                $this.removeMediaButton.show();
            }

            $this.toggleParallaxEnabledCheckbox();
        },

        toggleParallaxEnabledCheckbox: function(event) {

            let $this = this;
            if (undefined !== event) {
                event.preventDefault();
                $this = event.data.context;
            }

            $this.parallaxSettingsTitle.addClass('hidden');
            $this.staticSettingsTitle.addClass('hidden');
            $($this.parallaxDirectionSelectBox).trigger('change.$');

            // If there is an image
            $this.verticalAlignmentContainer.hide();
            $this.horizontalAlignmentContainer.hide();
            $this.parallaxSettingsTitle.removeClass('hidden');
            if ('1' === $("[data-key='cb_parallax_parallax_enabled']").val()) {
                if ('horizontal' === $("[data-key='cb_parallax_direction']").val()) {
                    $this.horizontalScrollDirectionContainer.show();
                    $this.verticalScrollDirectionContainer.hide();
                } else {
                    $this.verticalScrollDirectionContainer.show();
                    $this.horizontalScrollDirectionContainer.hide();
                }
            } else {
                $this.parallaxOptionsContainers.hide();
                $this.horizontalScrollDirectionContainer.hide();
            }
        },

        toggleCheckbox: function() {
            if ('0' === $(this).val()) {
                $(this).val('1');
            } else {
                $(this).val('0');
            }
        },

        // Image handling
        setPlaceholderVisibility: function(visible) {
            this.placeholderImage
                .prop('hidden', !visible)
                .toggleClass('hidden', !visible);
        },

        addMedia: function(event) {
            event.preventDefault();
            let $this = event.data.context;

            let cb_parallax_frame = wp.media({
                className: "media-frame cb-parallax-frame",
                frame: "select",
                multiple: false,
                title: cbParallaxMediaFrame.title,
                library: {type: "image"},
                button: {text: cbParallaxMediaFrame.button}
            });

            cb_parallax_frame.on("select", function() {
                let media_attachment = cb_parallax_frame.state().get("selection").first().toJSON();

                $this.attachmentId.val(media_attachment.id);
                $this.backgroundImage.attr('src', media_attachment.url);
                // Hide placeholder image
                $this.setPlaceholderVisibility(false);
                // Update the values that are held in hidden fields
                $("[data-key='cb_parallax_background_image_url']").val(media_attachment.url);
                $("[data-key='cb_parallax_background_image_url_hidden']").val(media_attachment.url);
                $("[data-key='cb_parallax_attachment_id']").val(media_attachment.id);
                $("[data-key='cb_parallax_can_parallax']").val('1');
                $("[data-key='cb_parallax_parallax_enabled']").val('1');
                $this.addMediaButton.addClass('disabled');
                // Set image dimensions
                $this.attachmentHeight = media_attachment.height;
                $this.attachmentWidth = media_attachment.width;
                if (1080 <= $this.attachmentHeight && 1080 <= $this.attachmentWidth) {
                    $("[data-key='cb_parallax_can_parallax']").val('1');
                } else {
                    $("[data-key='cb_parallax_can_parallax']").val('0');
                }
                // Update the user interface
                $this.initSettingsDisplay();
            });
            // Opens the media frame.
            cb_parallax_frame.open();
        },

        removeMedia: function(event) {
            event.preventDefault();
            let $this = event.data.context;

            $this.backgroundImage.attr('src', '');
            // Show placeholder image
            $this.setPlaceholderVisibility(true);
            $this.attachmentId.val('');
            $this.attachmentHeight = 0;
            $this.attachmentWidth = 0;
            $this.canParallax = '0';
            $this.parallaxEnabled = '0';
            // Update the values that are held in hidden fields
            $("[data-key='cb_parallax_background_image_url_hidden']").val('');
            $("[data-key='cb_parallax_background_image_url']").val('');
            $("[data-key='cb_parallax_attachment_id']").val('');
            $("[data-key='cb_parallax_can_parallax']").val('0');
            $("[data-key='cb_parallax_parallax_enabled']").val('0');
            $this.addMediaButton.removeClass('disabled');

            $this.initSettingsDisplay();
            return false;
        },

        // Options handling
        cbparallaxSaveOptions: function(event) {
            event.preventDefault();
            let $this = event.data.context;

            let input = {};
            // Retrieve values
            let dataForm = $this.settingsForm.data('form');
            let optionKeys = 'image' === dataForm ? $this.cb_Parallax.option_keys.image : $this.cb_Parallax.option_keys.all;
            $.each(optionKeys, function() {
                input[this] = $("[data-key='" + this + "']").val();
            });

            let data = {
                action: 'cb_parallax_save_options',
                nonce: $this.settingsForm.data('nonce'),
                input: input,
                post_id: $this.settingsForm.data('postid')
            };

            $.post(ajaxurl, data, function(response) {
                if (true === response.success) {
                    Swal.fire({
                        title: 'done!',
                        text: 'Settings saved.',
                        icon: 'success',
                        timer: 2000,
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        title: 'oops!',
                        text: 'Something went wrong. Please reload the page and try again.',
                        icon: 'error',
                        timer: 2000,
                        confirmButtonText: 'Ok'
                    });
                }
            });

            $this.initSettingsDisplay();
            return false;
        },

        cbparallaxResetOptions: function(event) {
            event.preventDefault();
            let $this = event.data.context;

            let data = {
                action: 'cb_parallax_reset_options',
                nonce: $this.settingsForm.data('nonce'),
                post_id: $this.settingsForm.data('postid')
            };

            if (!window.confirm($this.cb_Parallax.strings.reset_settings_confirmation)) {
                return false;
            }
            $.post(ajaxurl, data, function(response) {
                if (true === response.success) {
                    $this.resetOptionsPageInputFields();
                    $('');
                    Swal.fire({
                        title: 'done!',
                        text: 'Settings saved.',
                        icon: 'success',
                        timer: 2000,
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        title: 'oops!',
                        text: 'Something went wrong. Please reload the page and try again.',
                        icon: 'error',
                        timer: 2000,
                        confirmButtonText: 'Ok'
                    });
                }
            });
        },

        resetOptionsPageInputFields: function() {
            let dataForm = this.settingsForm.data('form');
            let optionKeys = 'image' === dataForm ? this.cb_Parallax.option_keys.image : this.cb_Parallax.option_keys.all;

            let $this = this;
            $.each(optionKeys, function() {
                let element = $("[data-key=" + this + "]");
                let value = $this.cb_Parallax.default_options[this];
                if ('checkbox' === element.attr('type')) {
                    $(element).val('0').prop('checked', '');
                } else if ('text' === element.attr('type')) {
                    $(element).wpColorPicker('color', 'transparent');
                } else if ('select' === element.attr('type')) {
                    // Set the actual dropdown value
                    element.val(value);
                    // Retrieve Fancy Select 'dropdown'
                    let list = $(element).parent().find('ul.options');
                    let listElements = $(list).find('li');
                    $(listElements).removeAttr('class');
                    $(list).find($("li[data-raw-value=" + this + "]")).addClass('selected');
                    // Set the trigger content (the part that is shown inside fancy select select box
                    $(element).parent().find('.trigger').html(value);
                } else {
                    $(element).val(value);
                }
            });
            // Reset the preview image
            this.backgroundImage.attr('src', '');
            // Show placeholder image
            this.setPlaceholderVisibility(true);
            // Reset the values that are held in hidden fields
            $("[data-key='cb_parallax_background_image_url_hidden']").val('');
            $("[data-key='cb_parallax_attachment_id']").val('');
        },

        // Help Tab
        initContextualHelp: function() {
            $("#cb-parallax-help-tabs").tabs();
        }
    };

    $(document).one('ready', function() {
        let cbParallaxSettingsDisplay = new CbParallaxSettingsDisplay();
        cbParallaxSettingsDisplay.init();
    });

})(jQuery);
