/**
 * The Public script.
 *
 * @link              https://github.com/demispatti/cb-parallax
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

(function ($) {
	"use strict";

	/**
	 * requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
	 http://paulirish.com/2011/requestanimationframe-for-smart-animating/
	 http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
	 MIT license
	 */
	(function () {
		var lastTime = 0;
		var vendors = ['ms', 'moz', 'webkit', 'o'];
		for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
			window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
			window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
				|| window[vendors[x] + 'CancelRequestAnimationFrame'];
		}

		if (!window.requestAnimationFrame)
			window.requestAnimationFrame = function (callback, element) {
				var currTime = new Date().getTime();
				var timeToCall = Math.max(0, 16 - (currTime - lastTime));
				var id = window.setTimeout(function () {
						callback(currTime + timeToCall);
					},
					timeToCall);
				lastTime = currTime + timeToCall;
				return id;
			};

		if (!window.cancelAnimationFrame)
			window.cancelAnimationFrame = function (id) {
				clearTimeout(id);
			};
	}());

	var requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame;
	var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

	function Plugin() {

		this.defaultOptions = {
			imageSrc            : '',
			backgroundColor     : '',
			positionX           : 'center',
			positionY           : 'center',
			backgroundAttachment: 'fixed',

			parallaxPossible         : false,
			parallaxEnabled          : false,
			direction                : 'vertical',
			verticalScrollDirection  : 'top',
			horizontalScrollDirection: 'left',
			horizontalAlignment      : 'center',
			verticalAlignment        : 'center',
			overlayImage             : 'none',
			imageWidth               : $(window).width(),
			imageHeight              : $(window).height(),
			overlayPath              : '',
			overlayOpacity           : '0.3',
			overlayColor             : ''
		},
			this.parallax = {
				possible                 : cbParallax.parallaxPossible != 'undefined' ? cbParallax.parallaxPossible : this.defaultOptions.parallaxPossible,
				enabled                  : cbParallax.parallaxEnabled != 'undefined' ? cbParallax.parallaxEnabled : this.defaultOptions.parallaxEnabled,
				direction                : cbParallax.direction != 'undefined' ? cbParallax.direction : this.defaultOptions.direction,
				verticalScrollDirection  : cbParallax.verticalScrollDirection != 'undefined' ? cbParallax.verticalScrollDirection : this.defaultOptions.verticalScrollDirection,
				horizontalScrollDirection: cbParallax.horizontalScrollDirection != 'undefined' ? cbParallax.horizontalScrollDirection : this.defaultOptions.horizontalScrollDirection,
				horizontalAlignment      : cbParallax.horizontalAlignment != 'undefined' ? cbParallax.horizontalAlignment : this.defaultOptions.horizontalAlignment,
				verticalAlignment        : cbParallax.verticalAlignment != 'undefined' ? cbParallax.verticalAlignment : this.defaultOptions.verticalAlignment
			},
			this.scrolling = {
				preserved: cbParallax.scrollingPreserved != 'undefined' ? cbParallax.scrollingPreserved : false
			},
			this.image = {
				src                 : cbParallax.imageSrc != 'undefined' ? cbParallax.imageSrc : this.defaultOptions.imageSrc,
				backgroundColor     : cbParallax.backgroundColor != 'undefined' ? cbParallax.backgroundColor : this.defaultOptions.backgroundColor,
				positionX           : cbParallax.positionX != 'undefined' ? cbParallax.positionX : this.defaultOptions.positionX,
				positionY           : cbParallax.positionY != 'undefined' ? cbParallax.positionY : this.defaultOptions.positionX,
				backgroundAttachment: cbParallax.backgroundAttachment != 'undefined' ? cbParallax.backgroundAttachment : this.defaultOptions.backgroundAttachment,

				width : cbParallax.imageWidth != 'undefined' ? cbParallax.imageWidth : this.defaultOptions.imageWidth,
				height: cbParallax.imageHeight != 'undefined' ? cbParallax.imageHeight : this.defaultOptions.imageHeight
			},
			this.overlay = {
				path   : cbParallax.overlayPath != 'undefined' ? cbParallax.overlayPath : this.defaultOptions.overlayPath,
				image  : cbParallax.overlayImage != 'undefined' ? cbParallax.overlayImage : this.defaultOptions.overlayImage,
				opacity: cbParallax.overlayOpacity != 'undefined' ? cbParallax.overlayOpacity : this.defaultOptions.overlayOpacity,
				color  : cbParallax.overlayColor != 'undefined' ? cbParallax.overlayColor : this.defaultOptions.overlayColor
			},
			this.overlayContainer = document.getElementById('cbp_overlay_container'),
			this.body = document.getElementsByTagName('body'),
			this.html = document.getElementsByTagName('html'),
			this.requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame,
			this.isScrolling = false,
			this.isResizing = false,
			this.niceScrollConfig = {
				zindex                 : '-9999',
				scrollspeed            : '120',
				mousescrollstep        : '24',
				preservenativescrolling: true,
				horizrailenabled       : false,
				cursordragspeed        : '1.2'
			}
	}

	Plugin.prototype = {

		constructor                    : Plugin,
		setOverlay                     : function () {

			if (cbParallax.overlayImage != "none") {
				$('body').prepend('<div id="cbp_overlay"></div>');
				this.overlayContainer = $('#cbp_overlay');
				this.overlayContainer.css({
					'background'      : 'url(' + cbParallax.overlayPath + cbParallax.overlayImage + ')',
					'background-color': '#' + cbParallax.overlayColor,
					'opacity'         : cbParallax.overlayOpacity
				});
			}
		},
		setupImageContainer            : function () {

			$('#cbp_image_container').css({
				'background-size' : cbParallax.imageWidth + 'px' + ' ' + cbParallax.imageHeight + 'px',
				'background-color': '#' + cbParallax.backgroundColor
			});
		},
		revertBodyStyling              : function () {

			var body = $('body');
			body.removeClass('custom-background');
			body.removeProp('background-image');
		},
		getHorizontalAlignInPx         : function () {

			var posX = null;
			switch (cbParallax.positionX) {

				case 'left':
					posX = '0';
					break;

				case 'center':
					posX = ($(window).width() / 2) - (cbParallax.imageWidth / 2) + 'px';
					break;

				case 'right':
					posX = $(window).width() - cbParallax.imageWidth + 'px';
					break;
			}
			return posX;
		},
		getVerticalAlignInPx           : function () {

			var posY = null;
			switch (cbParallax.positionY) {

				case 'top':
					posY = '0';
					break;

				case 'center':
					posY = ($(window).height() / 2) - (cbParallax.imageHeight / 2) + 'px';
					break;

				case 'bottom':
					posY = $(window).height() - cbParallax.imageHeight + 'px';
					break;
			}
			return posY;
		},
		parallaxGetScrollRatio         : function () {

			var documentOffsetX = null;
			var imageOffsetY = null;
			var imageOffsetX = null;
			var ratio = null;
			if (cbParallax.direction == 'vertical') {
				documentOffsetX = $(document).height() - $(window).height();
				imageOffsetY = cbParallax.imageHeight - $(window).height();

				ratio = (imageOffsetY / documentOffsetX);

			} else if (cbParallax.direction == 'horizontal') {
				documentOffsetX = $(document).height() - $(window).height();
				imageOffsetX = cbParallax.imageWidth - $(window).width();

				ratio = (imageOffsetX / documentOffsetX);
			}
			return ratio;
		},
		parallaxGetTransform           : function () {

			var transform = {
				x: null,
				y: null
			};
			var ratio = this.parallaxGetScrollRatio();
			var scrollingPosition = $(window).scrollTop();
			// Determines the values for the transformation.
			if (cbParallax.direction == 'vertical') {

				if (cbParallax.verticalScrollDirection == 'to top') {
					transform.x = 0;
					transform.y = -scrollingPosition * ratio;

				} else if (cbParallax.verticalScrollDirection == 'to bottom') {
					transform.x = 0;
					transform.y = scrollingPosition * ratio;
				}
			} else if (cbParallax.direction == 'horizontal') {

				if (cbParallax.horizontalScrollDirection == 'to the left') {
					transform.x = -scrollingPosition * ratio;
					transform.y = 0;

				} else if (cbParallax.horizontalScrollDirection == 'to the right') {
					transform.x = scrollingPosition * ratio;
					transform.y = 0;
				}
			}
			return transform;
		},
		parallaxSetInitialPosition     : function () {

			var imageContainer = $('#cbp_image_container');

			if (cbParallax.direction == 'vertical') {
				imageContainer.css({
					'left': this.getHorizontalAlignInPx()
				});

				if (cbParallax.verticalScrollDirection == 'to top') {

					imageContainer.css({
						'top': 0
					});

				} else if (cbParallax.verticalScrollDirection == 'to bottom') {

					imageContainer.css({
						'bottom': 0
					});
				}

			} else if (cbParallax.direction == 'horizontal') {

				imageContainer.css({
					'top': this.getVerticalAlignInPx()
				});

				if (cbParallax.horizontalScrollDirection == 'to the left') {

					imageContainer.css({
						'left': 0
					});

				} else if (cbParallax.horizontalScrollDirection == 'to the right') {

					imageContainer.css({
						'right': 0
					});
				}
			}
		},
		parallaxAnimationLoop          : function () {

			if (Plugin.prototype.isScrolling) {

				var transform = Plugin.prototype.parallaxGetTransform();
				Plugin.prototype.parallaxSetTranslate3DTransform(transform);
			}
			Plugin.prototype.isScrolling = false;
			requestAnimationFrame(Plugin.prototype.parallaxAnimationLoop);
		},
		parallaxKeepImageCentered      : function () {

			if (this.isResizing) {

				var posX = this.getHorizontalAlignInPx();
				var posY = this.getVerticalAlignInPx();

				if (cbParallax.direction == 'vertical' && cbParallax.horizontalAlignment == 'center' || cbParallax.horizontalAlignment == 'right') {

					$('#cbp_image_container').css({
						'left': posX
					});
				} else if (cbParallax.direction == 'horizontal' && cbParallax.verticalAlignment == 'center' || cbParallax.verticalAlignment == 'bottom') {

					$('#cbp_image_container').css({
						'top': posY
					});
				}
			}
		},
		parallaxAnimationLoopOnResize  : function () {

			if (Plugin.prototype.isResizing) {
				Plugin.prototype.parallaxKeepImageCentered();
				var transform = Plugin.prototype.parallaxGetTransform();
				Plugin.prototype.parallaxSetTranslate3DTransform(transform);
			}
			Plugin.prototype.isResizing = false;
			requestAnimationFrame(Plugin.prototype.parallaxAnimationLoopOnResize);
		},
		parallaxSetTranslate3DTransform: function (transform) {

			$('#cbp_image_container').css({
				'-webkit-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-moz-transform'   : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-ms-transform'    : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-o-transform'     : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'transform'        : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)'
			});
		},
		parallaxPreserveScrolling      : function () {

			var nice = $('html').niceScroll(this.niceScrollConfig);
			nice.hide();
			$('body').css('-ms-overflow-style', 'scrollbar');
		},
		parallaxBootstrap              : function () {

			if ($('#ascrail2000').length == 0) {
				this.parallaxPreserveScrolling();
			}

			this.setOverlay();
			this.revertBodyStyling();
			this.parallaxSetInitialPosition();

			// We call this one once from here so everything is Radiohead, Everything in its right place.
			var transform = this.parallaxGetTransform();
			this.parallaxSetTranslate3DTransform(transform);
		},
		staticSetImagePosition         : function () {

			$('#cbp_image_container').css({
				'left': Plugin.prototype.getHorizontalAlignInPx(),
				'top' : Plugin.prototype.getVerticalAlignInPx()
			});
		},
		staticKeepImageAligned         : function () {

			if (Plugin.prototype.isResizing) {
				Plugin.prototype.staticSetImagePosition();
			}
			Plugin.prototype.isResizing = false;
			requestAnimationFrame(Plugin.prototype.staticKeepImageAligned);
		},
		staticBootstrap                : function () {

			if ($('#ascrail2000').length == 0) {
				this.parallaxPreserveScrolling();
			}
			this.setOverlay();
			this.revertBodyStyling();
			this.setupImageContainer();
			this.staticSetImagePosition();
		},
		prependCanvas                  : function () {

			var element = '<canvas id="cbp_image_container" class="custom-background" width="' + cbParallax.imageWidth + '" height="' + cbParallax.imageHeight + '"></canvas>';
			$('body').prepend(element);
		},
		init                           : function () {

			if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled) {

				window.onload = function () {

					var canvas = document.getElementById('cbp_image_container');
					var context = canvas.getContext('2d');
					var img = new Image();

					img.onload = function () {
						context.drawImage(img, 0, 0, cbParallax.imageWidth, cbParallax.imageHeight);
						Plugin.prototype.parallaxBootstrap();
					};

					img.src = cbParallax.imageSrc;
				};
			} else if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled == false || isMobile && cbParallax.isDisabledOnMobile) {

				window.onload = function () {

					var canvas = document.getElementById('cbp_image_container');
					var context = canvas.getContext('2d');
					var img = new Image();

					img.onload = function () {
						context.drawImage(img, 0, 0, cbParallax.imageWidth, cbParallax.imageHeight);
						Plugin.prototype.staticBootstrap();
					};

					img.src = cbParallax.imageSrc;
				};
			} else if (this.scrolling.preserved) {
				this.parallaxPreserveScrolling();
			}
		},
		observeScrollEvent             : function () {

			$(document).bind('scroll', function () {

				if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled) {

					if(false == isMobile || false == isMobile && '1' != cbParallax.isDisabledOnMobile){
						Plugin.prototype.isScrolling = true;
						requestAnimationFrame(Plugin.prototype.parallaxAnimationLoop);
					}
				}
			});
		},
		observeResizeEvent             : function () {

			$(window).bind('resize', function () {

				if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled) {

					if (!isMobile && !cbParallax.isDisabledOnMobile){
						Plugin.prototype.isResizing = true;
						requestAnimationFrame(Plugin.prototype.parallaxAnimationLoopOnResize);
					}
				} else if (cbParallax.parallaxPossible && cbParallax.parallaxEnabled == false || isMobile && cbParallax.isDisabledOnMobile) {
					Plugin.prototype.isResizing = true;
					requestAnimationFrame(Plugin.prototype.staticKeepImageAligned);
				}
			});
		}
	};

	$(document).ready(function () {

		var plugin = new Plugin();
		plugin.prependCanvas();
		plugin.init();
		plugin.observeScrollEvent();
		plugin.observeResizeEvent();
	});

})(jQuery);
