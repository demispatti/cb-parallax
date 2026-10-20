/*jshint esversion: 6 */
(function($) {
    "use strict";

    let $instance = null;
    let viewport = $(window);

    //const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    function CbParallaxLayout()
    {
        this.cbParallaxLayout = Cb_Parallax_Layout;
        this.defaultOptions = {
            background_image_url: '',
            background_color: '',
            position_x: 'center',
            position_y: 'center',
            background_attachment: 'fixed',

            can_parallax: '0',
            parallax_enabled: '0',
            cb_parallax_boxed_layout: '0',
            cb_parallax_boxed_layout_margin: '200',
            cb_parallax_boxed_layout_content_container_background_color: '',
            direction: 'vertical',
            vertical_scroll_direction: 'top',
            horizontal_scroll_direction: 'left',
            horizontal_alignment: 'center',
            vertical_alignment: 'center',
            overlay_image: '',
            image_width: viewport.innerWidth(),
            image_height: viewport.innerHeight(),
            overlay_path: '',
            overlay_opacity: '0.3',
            overlay_color: '',
            uses_assumed_background_color: '0'
        };
        this.allowOverride = undefined !== this.cbParallaxLayout.plugin_options.allow_override ? this.cbParallaxLayout.plugin_options.allow_override : '0';
        //this.disableOnMobile = undefined !== this.cbParallaxLayout.plugin_options.disable_on_mobile ? this.cbParallaxLayout.plugin_options.disable_on_mobile : '0';
        this.canParallax = undefined !== this.cbParallaxLayout.image_options.can_parallax ? this.cbParallaxLayout.image_options.can_parallax : '0';
        this.usesAssumedBackgroundColor = undefined !== this.cbParallaxLayout.plugin_options.uses_assumed_background_color ? this.cbParallaxLayout.plugin_options.uses_assumed_background_color : '0';
        this.image = {
            src: undefined !== this.cbParallaxLayout.image_options.background_image_url ? this.cbParallaxLayout.image_options.background_image_url : this.defaultOptions.background_image_url,
            backgroundColor: undefined !== this.cbParallaxLayout.image_options.background_color ? this.cbParallaxLayout.image_options.background_color : this.defaultOptions.background_color,
            positionX: undefined !== this.cbParallaxLayout.image_options.position_x ? this.cbParallaxLayout.image_options.position_x : this.defaultOptions.position_x,
            positionY: undefined !== this.cbParallaxLayout.image_options.position_y ? this.cbParallaxLayout.image_options.position_y : this.defaultOptions.position_y,
            backgroundAttachment: undefined !== this.cbParallaxLayout.image_options.background_attachment ? this.cbParallaxLayout.image_options.background_attachment : this.defaultOptions.background_attachment,
            width: undefined !== this.cbParallaxLayout.image_data.image_width ? this.cbParallaxLayout.image_data.image_width : this.defaultOptions.image_width,
            height: undefined !== this.cbParallaxLayout.image_data.image_height ? this.cbParallaxLayout.image_data.image_height : this.defaultOptions.image_height
        };
        this.context = null;
        this.img = null;
        $instance = this;
    }

    CbParallaxLayout.prototype = {

        /// Init
        init: function() {
            if ('' !== this.image.src && 'none' !== this.image.src && '1' === this.canParallax) {
                this.initBoxedLayout();
            }
        },

        initBoxedLayout: function() {
            $('html').attr('class', 'cbpHtml');
            $('body').attr('class', 'cbpBody');

            if (('1' === this.cbParallaxLayout.plugin_options.boxed_layout && false === this.isNativeBoxedLayout())) {
                this.setupBodyContainer();
                this.addBoxedLayout();
                this.assignBackgroundColor();
            }
        },

        /// Boxed Layout
        isNativeBoxedLayout: function() {
            let $body = $('body');
            let bodyStyles = $body.css(['margin-left', 'margin-right']);
            let bodyWidth = $body[0].getBoundingClientRect().width;
            let viewportWidth = $(window).width();

            let marginLeft = parseFloat(bodyStyles['margin-left']);
            let marginRight = parseFloat(bodyStyles['margin-right']);

            let isCentered = marginLeft === marginRight && marginLeft > 0;
            let isNotFullWidth = bodyWidth < viewportWidth;

            return isCentered && isNotFullWidth;
        },

        setupBodyContainer: function() {
            if (0 === $('#cb_parallax_body_wrap').length) {
                $('body').wrapInner('<div id="cb_parallax_body_wrap"></div>');
            }
        },

        addBoxedLayout: function() {
            const margin = this.cbParallaxLayout.plugin_options.boxed_layout_margin;
            const marginHorizontal = this.extractMargin(0, margin);
            const marginVertical = this.extractMargin(1, margin);
            const bodyWrap = document.getElementById('cb_parallax_body_wrap');
            bodyWrap.style.marginLeft = marginVertical + 'px';
            bodyWrap.style.marginRight = marginVertical + 'px';
            bodyWrap.style.marginTop = marginHorizontal + 'px';
            bodyWrap.style.marginBottom = marginHorizontal + 'px';
            bodyWrap.style.width = "calc(100% - " + (2 * marginVertical) + "px)";
            bodyWrap.style.height = "calc(100% - " + (2 * marginHorizontal) + "px)";
        },

        /// Background Color
        assignBackgroundColor: function() {
            const storedBackgroundColor = this.cbParallaxLayout.plugin_options.boxed_layout_content_container_background_color;
            const bodyWrap = document.getElementById('cb_parallax_body_wrap');

            // Use stored background color
            if (("" !== storedBackgroundColor && '1' === this.cbParallaxLayout.plugin_options.boxed_layout)) {
                bodyWrap.style.setProperty('background-color', storedBackgroundColor, 'important');
                return;
            }

            // Use the assumed background color
            if ('1' === this.usesAssumedBackgroundColor && '1' === this.cbParallaxLayout.plugin_options.boxed_layout) {
                const backgroundColor = this.getAssumedBackgroundColor();
                bodyWrap.style.setProperty('background-color', backgroundColor, 'important');
            }
        },

        getAssumedBackgroundColor: function() {

            let elements = [$('.cbpBody'), $('#cb_parallax_body_wrap')];
            let colors = [];
            $.each(elements, function(index) {
                let elColor = $(this).css('color');
                if ('' !== elColor) {
                    colors[index] = elColor;
                }
            });

            function findFirstColor(colors)
            {
                let color = colors.find(str => /^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+(,\s*(0|1|0?\.\d+))?\s*\)$/.test(str));
                if ('transparent' !== color && '' !== color) {
                    return color;
                }
            }

            const color = findFirstColor(colors);
            if ('undefined' !== color) {
                let tone = this.guessBackgroundToneFromFont(color);
                if ('dark-background' === tone) {

                    return 'rgba(40, 40, 40, 0.94)';
                }
                return 'rgba(255, 255, 255, 0.94)';
            }

            const assumedBackgroundColor = this.getHighestZIndexElementWithBackground().backgroundColor;
            if (null !== assumedBackgroundColor) {
                if (assumedBackgroundColor.indexOf('a') === -1) {
                    return assumedBackgroundColor.replace(')', ', 0.94)').replace('rgb', 'rgba');
                }
                return assumedBackgroundColor;
            }
        },

        getFontColorBrightnessWithAlpha: function(color, background = [255, 255, 255, 0.94]) {
            let match = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d*\.?\d+))?\)/i);
            if (!match) {
                return null;
            }

            let r = parseInt(match[1], 10);
            let g = parseInt(match[2], 10);
            let b = parseInt(match[3], 10);
            let a = match[4] !== undefined ? parseFloat(match[4]) : 1;

            // Composite over white by default
            let finalR = Math.round((1 - a) * background[0] + a * r);
            let finalG = Math.round((1 - a) * background[1] + a * g);
            let finalB = Math.round((1 - a) * background[2] + a * b);

            return (0.299 * finalR + 0.587 * finalG + 0.114 * finalB).toFixed(0);
        },

        guessBackgroundToneFromFont: function(color) {
            let brightness = this.getFontColorBrightnessWithAlpha(color); // Defaults to white background
            if (brightness === null) {
                return 'unknown';
            }
            return 128 < brightness ? 'dark-background' : 'light-background';
        },

        getHighestZIndexElementWithBackground: function($root = $('html'), maxDepth = 200) {
            let maxZIndex = -Infinity;
            let resultElement = null;
            let backgroundColor = null;

            function traverse($element, depth)
            {
                if (depth > maxDepth) {
                    return;
                }

                let style = window.getComputedStyle($element[0]);

                if (
                    'none' === style.display ||
                    'hidden' === style.visibility ||
                    '0' === style.opacity
                ) {
                    return;
                }

                let bgColor = style.backgroundColor;
                let hasBackgroundColor = bgColor && bgColor !== 'rgba(0, 0, 0, 0)' && bgColor !== 'transparent';

                if (hasBackgroundColor) {
                    let zIndex = 'auto' === style.zIndex ? 0 : parseInt(style.zIndex, 10);
                    if (isNaN(zIndex)) {
                        zIndex = 0;
                    }

                    if (zIndex > maxZIndex) {
                        maxZIndex = zIndex;
                        resultElement = $element;
                        backgroundColor = bgColor;
                    }
                }

                $element.children().each(function() {
                    traverse($(this), depth + 1);
                });
            }

            // Usage: make sure $root and maxDepth are defined before this
            traverse($root, 0);

            return {
                element: resultElement,
                backgroundColor: backgroundColor,
                zIndex: maxZIndex
            };

        },

        extractMargin: function(index, string) {
            let result = string.match(/\d+/g);
            return result[index];
        }

    };

    $(document).one('ready', function() {
        let cbParallaxLayout = new CbParallaxLayout();
        cbParallaxLayout.init();
    });

})(jQuery);
