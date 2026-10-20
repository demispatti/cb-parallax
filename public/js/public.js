/*jshint esversion: 6 */

// Main Script
(function($) {
    "use strict";

    let $instance = null;
    let viewport = $(window);

    //let $document = $('document');

    function CbParallaxPublic()
    {
        this.cbParallaxPublic = Cb_Parallax_Public;

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
        this.effect = this.cbParallaxPublic.image_options.effect_type;
        this.direction = undefined !== this.cbParallaxPublic.image_options.direction ? this.cbParallaxPublic.image_options.direction : this.defaultOptions.direction;
        this.direction = 'Parallax' !== this.effect ? 'vertical' : this.direction;

        this.verticalScrollDirection = undefined !== this.cbParallaxPublic.image_options.vertical_scroll_direction ? this.cbParallaxPublic.image_options.vertical_scroll_direction : this.defaultOptions.vertical_scroll_direction;
        this.horizontalScrollDirection = undefined !== this.cbParallaxPublic.image_options.horizontal_scroll_direction ? this.cbParallaxPublic.image_options.horizontal_scroll_direction : this.defaultOptions.horizontal_scroll_direction;
        this.horizontalAlignment = undefined !== this.cbParallaxPublic.image_options.horizontal_alignment ? this.cbParallaxPublic.image_options.horizontal_alignment : this.defaultOptions.horizontal_alignment;
        this.verticalAlignment = undefined !== this.cbParallaxPublic.image_options.vertical_alignment ? this.cbParallaxPublic.image_options.vertical_alignment : this.defaultOptions.vertical_alignment;
        //
        this.parallaxEnabled = undefined !== this.cbParallaxPublic.plugin_options.parallax_enabled ? this.cbParallaxPublic.plugin_options.parallax_enabled : '0';
        this.allowOverride = undefined !== this.cbParallaxPublic.plugin_options.allow_override ? this.cbParallaxPublic.plugin_options.allow_override : '0';
        //
        this.canParallax = undefined !== this.cbParallaxPublic.image_options.can_parallax ? this.cbParallaxPublic.image_options.can_parallax : '0';
        this.scrolling = {preserved: undefined !== this.cbParallaxPublic.plugin_options.preserve_scrolling ? this.cbParallaxPublic.plugin_options.preserve_scrolling : '0'};

        this.image = {
            src: undefined !== this.cbParallaxPublic.image_options.background_image_url ? this.cbParallaxPublic.image_options.background_image_url : this.defaultOptions.background_image_url,
            backgroundColor: undefined !== this.cbParallaxPublic.image_options.background_color ? this.cbParallaxPublic.image_options.background_color : this.defaultOptions.background_color,
            positionX: undefined !== this.cbParallaxPublic.image_options.position_x ? this.cbParallaxPublic.image_options.position_x : this.defaultOptions.position_x,
            positionY: undefined !== this.cbParallaxPublic.image_options.position_y ? this.cbParallaxPublic.image_options.position_y : this.defaultOptions.position_y,
            backgroundAttachment: undefined !== this.cbParallaxPublic.image_options.background_attachment ? this.cbParallaxPublic.image_options.background_attachment : this.defaultOptions.background_attachment,

            width: undefined !== this.cbParallaxPublic.image_data.image_width ? this.cbParallaxPublic.image_data.image_width : this.defaultOptions.image_width,
            height: undefined !== this.cbParallaxPublic.image_data.image_height ? this.cbParallaxPublic.image_data.image_height : this.defaultOptions.image_height
        };
        this.overlay = {
            path: undefined !== this.cbParallaxPublic.overlay_options.overlay_path ? this.cbParallaxPublic.overlay_options.overlay_path : this.defaultOptions.overlay_path,
            image: undefined !== this.cbParallaxPublic.overlay_options.overlay_image ? this.cbParallaxPublic.overlay_options.overlay_image : this.defaultOptions.overlay_image,
            opacity: undefined !== this.cbParallaxPublic.overlay_options.overlay_opacity ? this.cbParallaxPublic.overlay_options.overlay_opacity : this.defaultOptions.overlay_opacity,
            color: undefined !== this.cbParallaxPublic.overlay_options.overlay_color ? this.cbParallaxPublic.overlay_options.overlay_color : this.defaultOptions.overlay_color
        };
        //this.canvasDim = [];
        /*this.canvasDim.x = this.getCanvasDimensions()[x];
        this.canvasDim.y = this.getCanvasDimensions()[y];*/
        this.context = null;
        this.img = null;
        this.scaleFactor = undefined !== this.cbParallaxPublic.scale_factor ? this.cbParallaxPublic.scale_factor : 1.5;
        this.imageAspectRatio = null/* = this.getBackgroundImageAspectRatio()*/;
        this.scrollFactor = parseFloat('1.2');
        this.viewport = [];
        this.viewport.x = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        this.viewport.y = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        $instance = this;
    }

    CbParallaxPublic.prototype = {

        /// Events
        addEvents: function() {
            //let viewport = $(window);
            if ('1' === $instance.canParallax && '1' === $instance.parallaxEnabled) {
                viewport.on('scroll', {context: this}, $instance.parallaxOnScroll);
                //viewport.on('scroll', {context: this}, $instance.actionOnScroll);
                viewport.on('resize', {context: this}, $instance.parallaxOnResize);
            }
        },

        actionOnScroll: function(event) {
            let $this = $instance;
            if (undefined !== event) {
                $this = event.data.context;
            }

            const scrollY = window.scrollY;
            const el = document.getElementById('cb_parallax_canvas');
            //const effect = 'Parallax';
            //const effect = 'Spiral Scroll';
            switch ($instance.effect) {
                case 'Parallax':
                    $this.parallaxOnScroll(event);
                    break;
                case 'Spiral Scroll':
                    //let a = $this.get2dTransformRatios(spiralScroll, el, scrollY);
                    $this.spiralScroll(el, scrollY);
                    break;
                case 'Sine Wave':
                    $this.sineWaveFloat(el, scrollY);
                    break;
                case 'Golden Ratio':
                    $this.goldenRatioScale(el, scrollY);
                    break;
                case 'Fibonacci':
                    $this.fibonacciPath(el, scrollY);
                    break;
                case 'Radial Outward':
                    $this.radialOutwardMovement(el, scrollY);
                    break;
                case 'orbital':
                    $this.orbitalLayers(el, scrollY);
                    break;
                case 'tornado':
                    $this.tornadoTwirl(el, scrollY);
                    break;
                case 'diagonal':
                    $this.diagonalParallax(el, scrollY);
                    break;
                case 'ocean-wave':
                    $this.oceanWaveLayers(el, scrollY);
                    break;
                case 'vortex':
                    $this.vortexInOut(el, scrollY);
                    break;
                case 'perspective-flip':
                    $this.perspectiveFlip(el, scrollY);
                    break;
                case 'random-drift':
                    $this.randomDrift(el, scrollY);
                    break;
                case 'depth-fog':
                    $this.depthFog(el, scrollY);
                    break;
                case 'parallax-stars':
                    $this.parallaxStars(el, scrollY);
                    break;
                case 'logarithmic-spiral':
                    $this.logarithmicSpiral(el, scrollY);
                    break;
                case 'grid-morph':
                    $this.gridMorph(el, scrollY);
                    break;
                case 'path-follow':
                    $this.pathFollowing(el, scrollY);
                    break;
                case 'magnetic':
                    $this.magneticEasing(el, scrollY);
                    break;
                case 'infinite-loop':
                    $this.infiniteLoop(el, scrollY);
                    break;
            }
        },

        /*get2dTransformRatios: function(fn, el, scrollY) {
            let imageWidth = this.image.width;
            let imageHeight = this.image.height;
            let scrollratio = this.getScrollRatio();
            let a = fn(el, scrollY);
            let b = 2;
            return a;
        },*/

        /// Init
        init: function() {
            if ('none' !== $instance.image.src && '1' === $instance.canParallax) {
                $instance.imports = {
                    requestAnimationFrame: requestAnimationFrame
                };
                window.__forceSmoothScrollPolyfill__ = true;

                //$instance.scrollFactor = $instance.getScrollRatio();
                $instance.imageAspectRatio = $instance.getBackgroundImageAspectRatio();

                $instance.addEvents();
            }
        },

        initBackground: function() {
            if ('none' !== $instance.image.src) {
                //$instance.doBackground();
                $('html').attr('class', 'cbpHtml');
                $('body').attr('class', 'cbpBody');
                $instance.setupCanvas();
                $instance.setupOverlay();
                $instance.addCss();

                let imageDim = $instance.getImageDimensions();
                //this.canvasDim = this.getCanvasDimensions();
                let canvasDim = $instance.getCanvasDimensions();
                let canvas = document.getElementById('cb_parallax_canvas');
                let context = canvas.getContext('2d');
                $instance.img = new Image();
                let sx = 0;
                let sy = 0;
                let sWidth = imageDim.width;
                let sHeight = imageDim.height;
                let dx = -((imageDim.width - viewport.width()) / 2).toFixed(0);
                let dy = 0;
                let dWidth = viewport.innerWidth()/*imageDim.width*/;
                let dHeight = viewport.innerHeight()/*Math.abs((parseInt(imageDim.height) + parseInt(imageDim.height / $instance.scrollFactor)))*/;

                $instance.img.onload = function() {
                    let image = this;
                    context.drawImage(this, 0, 0, canvasDim.x, canvasDim.y);

                    //context.drawImage(image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
                    //context.drawImage(image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
                    //context.drawImage(this, dx, dy, dWidth, dHeight);
                    //context.drawImage(image, 0, 0);
                    /*context.drawImage(
                        this,
                        /!*(-imageDim.width * 1).toFixed(0)*!/0,
                        /!*(-imageDim.height * 1).toFixed(0)*!/0,
                        (imageDim.width * 2).toFixed(0),
                        (imageDim.height * 2).toFixed(0),
                        0,
                        0,
                        (canvasDim.x * 2).toFixed(0),
                        (canvasDim.y * 2).toFixed(0)
                    );*/
                    $instance.doParallaxBackgroundImageMovement();
                    //$instance.parallaxOnResize();
                };
                $instance.img.src = $instance.image.src + '?' + encodeURIComponent(new Date().toDateString());
            }
        },

        /*doBackground: function() {
            $('html').attr('class', 'cbpHtml');
            $('body').attr('class', 'cbpBody');
            $instance.setupCanvas();
            $instance.setupOverlay();
            $instance.addCss();
            let imageDim = $instance.getImageDimensions();
            //this.canvasDim = this.getCanvasDimensions();
            let canvasDim = $instance.getCanvasDimensions();
            let canvas = document.getElementById('cb_parallax_canvas');
            let context = canvas.getContext('2d');
            $instance.img = new Image();
            //let $this = this;

            $instance.img.onload = function() {
                context.drawImage(this, 0, 0, imageDim.width, imageDim.height);
                /!*context.drawImage(
                    this,
                    /!*(-imageDim.width * 1).toFixed(0)*!/0,
                    /!*(-imageDim.height * 1).toFixed(0)*!/0,
                    (imageDim.width * 2).toFixed(0),
                    (imageDim.height * 2).toFixed(0),
                    0,
                    0,
                    (canvasDim.x * 2).toFixed(0),
                    (canvasDim.y * 2).toFixed(0)
                );*!/
                $instance.doParallaxBackgroundImageMovement();
                $instance.parallaxOnResize();
            };
            $instance.img.src = $instance.image.src + '?' + encodeURIComponent(new Date().toDateString());
        },*/

        setupCanvas: function() {
            if (0 === $('#cb_parallax_canvas').length) {
                let canvasDim = $instance.getCanvasDimensions();
                const text = '<div id="cb_parallax_overlay"></div><canvas id="cb_parallax_canvas" class="custom-background" width="' + canvasDim.x + '" height="' + canvasDim.y + '" style="background-color: ' + $instance.image.backgroundColor + '"></canvas>';
                //const text = '<div id="cb_parallax_overlay"></div><canvas id="cb_parallax_canvas" class="custom-background"></canvas>';
                $(text).insertBefore('.cbpHtml > head');
            }
        },

        setupOverlay: function() {
            if (0 === $('#cb_parallax_overlay').length) {
                $instance.overlayContainer = $('#cb_parallax_overlay');
                let overlayBackgroundColor = $instance.cbParallaxPublic.image_options.overlay_color;
                let overlayOpacity = $instance.extractOpacity($instance.cbParallaxPublic.image_options.overlay_opacity);

                $instance.overlayContainer.css({
                    'background': 'url(' + $instance.cbParallaxPublic.overlay_options.overlay_path + $instance.cbParallaxPublic.overlay_options.overlay_image + ')',
                    'opacity': overlayOpacity,
                    'background-color': overlayBackgroundColor
                });
            }
        },

        /*getBackgroundMode: function() {
            let mode = 'static';

            if ('1' === $instance.parallaxEnabled && '1' === $instance.canParallax) {
                mode = 'Parallax';
            }

            return mode;
        },*/

        /// Image Scaling
        scaleImageForVerticalScrolling: function(imgW, imgH, viewportW, viewportH, scaleFactor) {
            const targetW = viewportW;
            const targetH = viewportH * scaleFactor;

            let newW = targetW;
            let newH = imgH / imgW * targetW;

            // Skalieren der Bildbreite und -höhe auf die Mindestmasse
            if (newH < targetH || newW < viewportW) {
                while (newW < viewportW || newH < targetH) {
                    newW += 1;
                    newH += 1;
                }
            }

            return {
                width: newW.toFixed(0),
                height: newH.toFixed(0)
            };
        },

        scaleImageForHorizontalScrolling: function(imgW, imgH, viewportW, viewportH, scaleFactor) {
            // Zielbreite: 125% der Viewport-Breite
            const targetWidth = viewportW * scaleFactor;

            // Berechne Skalierung, um Breite zu erreichen
            const scale = targetWidth / imgW;

            // Skaliere Höhe entsprechend
            let newWidth = imgW * scale;
            let newHeight = imgH * scale;

            // Wenn neue Höhe kleiner als Viewport-Höhe → anpassen
            if (newHeight < viewportH) {
                const scaleH = viewportH / imgH;
                newWidth = imgW * scaleH;
                newHeight = imgH * scaleH;
            }

            return {
                width: newWidth.toFixed(0),
                height: newHeight.toFixed(0)
            };
        },

        /// Moves
        doParallaxBackgroundImageMovement: function() {
            $instance.updateCanvasAlignment();
            $instance.updateParallaxAxis();
            // We call this one once from here so everything is Radiohead, Everything in its right place :)
            $instance.doParallaxTranslate3DTransform($instance.getParallaxTransform());
        },

        updateCanvasAlignment: function() {
            let element = $('#cb_parallax_canvas');

            if ('vertical' === $instance.direction) {
                element.css({'left': $instance.getBackgroundImageHorizontalAlignment()});
            } else if ('horizontal' === $instance.direction) {
                element.css({'top': $instance.getBackgroundImageVerticalAlignment()});
            }
        },

        updateParallaxAxis: function() {
            let element = $("#cb_parallax_canvas");

            if ('vertical' === $instance.direction) {
                if ('to bottom' === $instance.verticalScrollDirection) {
                    element.css({
                        'position': 'fixed',
                        'top': $instance.getBackgroundImageVerticalPositionInPx()
                    });
                }
            } else if ('horizontal' === $instance.direction) {
                element.css({
                    'position': 'fixed',
                    'left': $instance.getBackgroundImageHorizontalPositionInPx()
                });
            }
        },

        /*considerAdminBar: function() {
            if(0 !== $('#wpadminbar').length) {
                let posTop = $('#wpadminbar').height();
            }
            
        },*/

        /// Image Movement
        doParallaxTranslate3DTransform: function(transform) {
            $("#cb_parallax_canvas").css({
                '-webkit-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
                '-moz-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
                '-ms-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
                '-o-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
                'transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)'
            });
        },

        getParallaxTransform: function() {
            let transform = {
                x: null,
                y: null
            };
            let ratio = $instance.getScrollRatio();
            let scrollingPosition = $(window).scrollTop();

            // Determines the values for the transformation.
            if ('vertical' === $instance.direction) {
                if ('to top' === $instance.verticalScrollDirection) {
                    transform.x = 0;
                    transform.y = -scrollingPosition * ratio;
                } else if ('to bottom' === $instance.verticalScrollDirection) {
                    transform.x = 0;
                    transform.y = scrollingPosition * ratio;
                }
            } else if ('horizontal' === $instance.direction) {
                if ('to the left' === $instance.horizontalScrollDirection) {
                    transform.x = -scrollingPosition * ratio;
                    transform.y = 0;
                } else if ('to the right' === $instance.horizontalScrollDirection) {
                    transform.x = scrollingPosition * ratio;
                    transform.y = 0;
                }
            }
            return transform;
        },

        parallaxOnScroll: function(event) {
            let $this = $instance;

            if (undefined !== event) {
                $this = event.data.context;
            }
            $this.isScrolling = '1';
            requestAnimationFrame($this.scrollParallaxBackgroundImage);
        },

        scrollParallaxBackgroundImage: function() {
            if ('1' === $instance.isScrolling) {
                $instance.doParallaxTranslate3DTransform($instance.getParallaxTransform());
            }
            $instance.isScrolling = '0';
            requestAnimationFrame($instance.scrollParallaxBackgroundImage);
        },

        parallaxOnResize: function(event) {
            let $this = $instance;
            $this.canvasDim = $this.getCanvasDimensions();
            if (undefined !== event) {
                $this = event.data.context;
            }
            $this.isResizing = '1';
            requestAnimationFrame($this.resizeParallaxBackgroundImage);
        },

        resizeParallaxBackgroundImage: function() {
            if ('1' === $instance.isResizing) {
                $instance.updateCanvasDimensions();
                $instance.updateCanvasAlignment();
                $instance.doParallaxTranslate3DTransform($instance.getParallaxTransform());
                $instance.updateParallaxAxis();
            }
            $instance.isResizing = '0';
            requestAnimationFrame($instance.resizeParallaxBackgroundImage);
        },

        /// Image Parallax Hepler Functions
        getBackgroundImageHorizontalAlignment: function() {
            let posX = null;
            let canvasDim = $instance.getCanvasDimensions();
            let aspectRatio = $instance.getViewportAspectRatio();
            let landscape = aspectRatio >= $instance.imageAspectRatio ? '1' : '0';
            let portrait = aspectRatio < $instance.imageAspectRatio ? '1' : '0';
            //let viewport = $(window);

            if ('vertical' === $instance.direction) {
                if ('to top' === $instance.verticalScrollDirection) {
                    if ('1' === landscape) {
                        switch (this.horizontalAlignment) {
                            case 'left':
                                posX = '0';
                                break;
                            case 'center':
                                posX = (viewport.innerWidth() / 2) - (canvasDim.x / 2);
                                break;
                            case 'right':
                                posX = viewport.innerWidth() - canvasDim.x;
                                break;
                        }
                        return parseInt(posX) + 'px';
                    } else if ('1' === portrait) {
                        switch ($instance.horizontalAlignment) {
                            case 'left':
                                posX = '0';
                                break;
                            case 'center':
                                posX = (viewport.innerWidth() / 2) - (canvasDim.x / 2);
                                break;
                            case 'right':
                                posX = viewport.innerWidth() - canvasDim.x;
                                break;
                        }
                        return parseInt(posX) + 'px';
                    }
                } else if ('to bottom' === $instance.verticalScrollDirection) {
                    if ('1' === landscape) {
                        switch ($instance.horizontalAlignment) {
                            case 'left':
                                posX = '0';
                                break;
                            case 'center':
                                posX = (viewport.innerWidth() / 2) - (canvasDim.x / 2);
                                break;
                            case 'right':
                                posX = viewport.innerWidth() - canvasDim.x;
                                break;
                        }
                        return parseInt(posX) + 'px';
                    } else if ('1' === portrait) {
                        switch ($instance.horizontalAlignment) {
                            case 'left':
                                posX = '0';
                                break;
                            case 'center':
                                posX = (viewport.innerWidth() / 2) - (canvasDim.x / 2);
                                break;
                            case 'right':
                                posX = viewport.innerWidth() - canvasDim.x;
                                break;
                        }
                        return parseInt(posX) + 'px';
                    }
                }
            }
        },

        getBackgroundImageVerticalAlignment: function() {
            let posY = null;
            let canvasDim = $instance.getCanvasDimensions();
            let aspectRatio = $instance.getViewportAspectRatio();
            let landscape = aspectRatio >= $instance.imageAspectRatio ? '1' : '0';
            let portrait = aspectRatio < $instance.imageAspectRatio ? '1' : '0';
            //let viewport = $(window);

            if ('horizontal' === $instance.direction) {
                if ('to the left' === $instance.horizontalScrollDirection) {
                    if ('1' === landscape) {
                        switch ($instance.verticalAlignment) {
                            case 'top':
                                posY = '0';
                                break;
                            case 'center':
                                //posY = -( canvasDim.y / 2 ) - ($( window ).innerHeight() / 2 );
                                posY = (viewport.innerHeight() / 2) - (canvasDim.y / 2);
                                break;
                            case 'bottom':
                                posY = viewport.innerHeight() - canvasDim.y;
                                break;
                        }
                        return parseInt(posY) + 'px';
                    } else if ('1' === portrait) {
                        switch ($instance.verticalAlignment) {
                            case 'top':
                                posY = '0';
                                break;
                            case 'center':
                                posY = (viewport.innerHeight() / 2) - (canvasDim.y / 2);
                                //posY = - ( ( canvasDim.y / 2 ) - $( window ).innerHeight() / 2 ) / 2;
                                break;
                            case 'bottom':
                                posY = -viewport.innerHeight() + canvasDim.y;
                                break;
                        }
                        return parseInt(posY) + 'px';
                    }
                } else if ('to the right' === $instance.horizontalScrollDirection) {
                    if ('1' === landscape) {
                        switch ($instance.verticalAlignment) {
                            case 'top':
                                posY = '0';
                                break;
                            case 'center':
                                //posY = ( $( window ).innerHeight() / 2 ) - ( canvasDim.y / 2 );
                                posY = (viewport.innerHeight() / 2) - (canvasDim.y / 2);
                                break;
                            case 'bottom':
                                posY = viewport.innerHeight() - canvasDim.y;
                                break;
                        }
                        return parseInt(posY) + 'px';
                    } else if ('1' === portrait) {
                        switch ($instance.verticalAlignment) {
                            case 'top':
                                posY = '0';
                                break;
                            case 'center':
                                //posY = - ( canvasDim.y / 2 ) + ($( window ).innerHeight() / 2 );
                                posY = (viewport.innerHeight() / 2) - (canvasDim.y / 2);
                                break;
                            case 'bottom':
                                posY = viewport.innerHeight() - canvasDim.y;
                                break;
                        }
                        return parseInt(posY) + 'px';
                    }
                }
            }
        },

        getScrollRatio: function() {
            let canvasDim = $instance.getCanvasDimensions();
            let documentOffsetX = null;
            let imageOffsetY = null;
            let imageOffsetX = null;
            let ratio = null;
            //let viewport = $(window);

            if ('vertical' === $instance.direction) {
                documentOffsetX = $(document).innerHeight() - viewport.innerHeight();
                imageOffsetY = canvasDim.y - viewport.height();
                ratio = (imageOffsetY / documentOffsetX);
            } else if ('horizontal' === $instance.direction) {
                documentOffsetX = $(document).innerHeight() - viewport.innerHeight();
                imageOffsetX = canvasDim.x - viewport.innerWidth();
                ratio = (imageOffsetX / documentOffsetX);
            }
            return ratio;
        },

        getCanvasDimensions: function() {
            let displayRatio = viewport.innerWidth() / viewport.innerHeight();
            let imageRatio = $instance.image.width / $instance.image.height;
            let imageHeight = 0;
            let imageWidth = 0;

            if ('vertical' === $instance.direction) {

                if (imageRatio < displayRatio) {
                    imageWidth = viewport.innerWidth();
                    imageHeight = imageWidth / $instance.image.width * $instance.image.height;
                } else {
                    imageHeight = viewport.innerHeight() * $instance.scrollFactor;
                    imageWidth = imageHeight / $instance.image.height * $instance.image.width;
                }
                return {x: imageWidth.toFixed(0), y: imageHeight.toFixed(0)};
            }
            if ('horizontal' === $instance.direction) {

                if (imageRatio < displayRatio) {
                    imageWidth = viewport.innerWidth() * $instance.scrollFactor;
                    imageHeight = imageWidth / $instance.image.width * $instance.image.height;
                } else {
                    imageHeight = viewport.innerHeight();
                    imageWidth = imageHeight / $instance.image.height * $instance.image.width;
                }
                return {x: imageWidth.toFixed(0), y: imageHeight.toFixed(0)};
            }
        },

        getImageDimensions: function() {
            const viewportW = window.innerWidth;
            const viewportH = window.innerHeight;
            const imgW = parseInt($instance.image.width);
            const imgH = parseInt($instance.image.height);
            let scaleFactor = {x: 1.25, y: 1.25};
            if ('vertical' === $instance.direction) {
                return $instance.scaleImageForVerticalScrolling(imgW, imgH, viewportW, viewportH, scaleFactor.x);
            }
            if ('horizontal' === $instance.direction) {
                return $instance.scaleImageForHorizontalScrolling(imgW, imgH, viewportW, viewportH, scaleFactor.y);
            }
        },

        updateCanvasDimensions: function() {
            let canvasDim = $instance.getCanvasDimensions();
            let canvas = $("#cb_parallax_canvas");
            let canvasWidth = parseInt(canvasDim.x);
            let canvasHeight = parseInt(canvasDim.y);
            canvas.width(canvasWidth);
            canvas.height(canvasHeight);
        },

        addCss: function() {
            let html = $('html');
            html.css('background-color', 'transparent');
            html.css('background', 'transparent');

            let body = $('body');
            body.removeClass('custom-background');
            body.removeProp('background-image');
            body.css('background-color', 'transparent');
            body.css('background', 'transparent');
            body.find('::before').css('background-color', 'transparent');
            body.find('::before').css('background', 'transparent');

            $(':root').css('background-color', 'transparent');
        },

        getBackgroundImageHorizontalPositionInPx: function() {
            let posX = null;
            let canvasDimensions = $instance.getCanvasDimensions();

            if ('to the left' === $instance.horizontalScrollDirection) {
                posX = 0;
                return posX.toString() + 'px';
            } else if ('to the right' === $instance.horizontalScrollDirection) {
                let number = $(window).innerWidth() - canvasDimensions.x;
                posX = parseInt(number.toFixed());
                return posX.toString() + 'px';
            }
        },

        getBackgroundImageVerticalPositionInPx: function() {
            let posY = null;
            let canvasDimensions = $instance.getCanvasDimensions();

            if ('to top' === $instance.verticalScrollDirection) {
                posY = 0;
                return posY.toString() + 'px';
            } else if ('to bottom' === $instance.verticalScrollDirection) {
                let number = $(window).innerHeight() - canvasDimensions.y;
                posY = parseInt(number.toFixed());
                return posY.toString() + 'px';
            }
        },

        getBackgroundImageAspectRatio: function() {
            let image = $instance.image;
            return (image.width / image.height).toFixed(2);
        },

        getViewportAspectRatio: function() {
            //let viewport = $(window);
            return (viewport.innerWidth() / viewport.innerHeight()).toFixed(2);
        },

        extractOpacity: function(string) {
            let result = string.match(/\d+/g);
            return result[0] + "." + result[1];
        },

        //////////////////////////////////////////

        // Effects
        clampMovement: function(x, y, element) {
            const maxX = element.offsetWidth * 0.2;
            const maxY = element.offsetHeight * 0.2;
            return [
                Math.max(-maxX, Math.min(maxX, x)),
                Math.max(-maxY, Math.min(maxY, y))
            ];
        },

        spiralScroll: function(e, s) {
            let r = s * 0.1, a = s * 0.01, x = r * Math.cos(a), y = r * Math.sin(a);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        sineWaveFloat: function(e, s) {
            let a = e.offsetWidth * 0.2, x = a * Math.sin(s * 0.01), y = s * 0.5;
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        goldenRatioScale: function(e, s) {
            let scale = Math.min(1.2, 1 + s * 0.0001 * 1.618);
            e.style.transform = `scale(${scale})`;
        },

        fibonacciPath: function(e, s) {
            let f = s / 100, a = f * (Math.PI / 3), r = f * 5, x = r * Math.cos(a), y = r * Math.sin(a);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        radialOutwardMovement: function(e, s) {
            let a = Math.PI / 4, d = s * 0.3, x = d * Math.cos(a), y = d * Math.sin(a);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        orbitalLayers: function(e, s) {
            let r = e.offsetWidth * 0.2, a = s * 0.01, x = r * Math.cos(a), y = r * Math.sin(a);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        tornadoTwirl: function(e, s) {
            let y = s * 0.5, a = s * 0.1;
            [, y] = $instance.clampMovement(0, y, e);
            e.style.transform = `translate3d(0,${y}px,0) rotate(${a}deg)`;
        },

        diagonalParallax: function(e, s) {
            let x = s * 0.3, y = s * 0.7;
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        oceanWaveLayers: function(e, s) {
            let a = e.offsetWidth * 0.2, x = a * Math.sin(s * 0.02), y = s * 0.4;
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        vortexInOut: function(e, s) {
            let sc = Math.min(1.2, 1 + s * 0.0002), a = s * 0.2;
            e.style.transform = `rotate(${a}deg) scale(${sc})`;
        },

        perspectiveFlip: function(e, s) {
            let a = s * 0.1;
            e.style.transform = `rotateX(${a}deg) scale(1.2)`;
            e.style.transformStyle = 'preserve-3d';
        },

        randomDrift: function(e, s) {
            let x = Math.sin(s * 0.01) * e.offsetWidth * 0.2, y = Math.cos(s * 0.01) * e.offsetHeight * 0.2;
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        depthFog: function(e, s) {
            let y = s * 0.5, b = Math.min(s * 0.01, 5);
            [, y] = $instance.clampMovement(0, y, e);
            e.style.transform = `translateY(${y}px)`;
            e.style.filter = `blur(${b}px)`;
        },

        parallaxStars: function(e, s) {
            let y = s * 0.2;
            [, y] = $instance.clampMovement(0, y, e);
            e.style.transform = `translateY(${y}px)`;
        },

        logarithmicSpiral: function(e, s) {
            let b = 0.1, t = s * 0.01, r = Math.exp(b * t) * 5, x = r * Math.cos(t), y = r * Math.sin(t);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        gridMorph: function(e, s) {
            let sk = s * 0.05;
            e.style.transform = `skew(${sk}deg,${sk}deg)`;
        },

        pathFollowing: function(e, s) {
            let t = (s % 1000) / 1000, x = 500 * Math.cos(2 * Math.PI * t), y = 500 * Math.sin(2 * Math.PI * t);
            [x, y] = $instance.clampMovement(x, y, e);
            e.style.transform = `translate3d(${x}px,${y}px,0)`;
        },

        magneticEasing: function(e, s) {
            let x = Math.sin(s * 0.01) * e.offsetWidth * 0.2;
            [x] = $instance.clampMovement(x, 0, e);
            e.style.transform = `translateX(${x}px)`;
        },

        infiniteLoop: function(e, s) {
            let y = s % e.offsetHeight;
            [, y] = $instance.clampMovement(0, y, e);
            e.style.transform = `translateY(${y}px)`;
        }

    };

    let cbParallaxPublic = new CbParallaxPublic();
    $(document).one('ready', function() {
        cbParallaxPublic.init();
    });
    $(window).on('load', function() {
        cbParallaxPublic.initBackground();
    });

})(jQuery);
