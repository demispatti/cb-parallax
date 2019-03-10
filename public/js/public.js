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

(function ($)
{

	"use strict";


	/**
	 * requestAnimationFrame polyfill by Erik Möller. fixes from Paul Irish and Tino Zijdel
	 http://paulirish.com/2011/requestanimationframe-for-smart-animating/
	 http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating
	 MIT license
	 */
	(function ()
	{
		var lastTime = 0;
		var vendors = ['ms', 'moz', 'webkit', 'o'];
		for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x)
		{
			window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
			window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
				|| window[vendors[x] + 'CancelRequestAnimationFrame'];
		}

		if (!window.requestAnimationFrame)
		{
			window.requestAnimationFrame = function (callback, element)
			{
				var currTime = new Date().getTime();
				var timeToCall = Math.max(0, 16 - (currTime - lastTime));
				var id = window.setTimeout(function ()
					{
						callback(currTime + timeToCall);
					},
					timeToCall);
				lastTime = currTime + timeToCall;
				return id;
			};
		}

		if (!window.cancelAnimationFrame)
		{
			window.cancelAnimationFrame = function (id)
			{
				clearTimeout(id);
			};
		}
	}());


	var requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame;
	var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);


	function Plugin()
	{

		this.defaultOptions = {
			backgroundImageUrl  : '',
			backgroundColor     : '',
			positionX           : 'center',
			positionY           : 'center',
			backgroundAttachment: 'fixed',

			canParallax         	 : false,
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
		};
		this.parallax = {
			canParallax                 : (cbParallax.canParallax != 'undefined' ? cbParallax.canParallax : false/*this.defaultOptions.canParallax*/),
			parallaxEnabled          : (cbParallax.parallaxEnabled != 'undefined' ? cbParallax.parallaxEnabled : /*this.defaultOptions.parallaxEnabled*/false),
			direction                : (cbParallax.direction != 'undefined' ? cbParallax.direction : 'vertical'/*this.defaultOptions.direction*/),
			verticalScrollDirection  : (cbParallax.verticalScrollDirection != 'undefined' ? cbParallax.verticalScrollDirection : this.defaultOptions.verticalScrollDirection),
			horizontalScrollDirection: (cbParallax.horizontalScrollDirection != 'undefined' ? cbParallax.horizontalScrollDirection : this.defaultOptions.horizontalScrollDirection),
			horizontalAlignment      : (cbParallax.horizontalAlignment != 'undefined' ? cbParallax.horizontalAlignment : this.defaultOptions.horizontalAlignment),
			verticalAlignment        : (cbParallax.verticalAlignment != 'undefined' ? cbParallax.verticalAlignment : this.defaultOptions.verticalAlignment),
			disableOnMobile			 : cbParallax.disableOnMobile != 'undefined' ? cbParallax.disableOnMobile : false
		};
		this.scrolling = {
			preserved: (cbParallax.preserveScrolling != 'undefined' ? cbParallax.preserveScrolling : false)
		};
		this.image = {
			src                 : (cbParallax.backgroundImageUrl != 'undefined' ? cbParallax.backgroundImageUrl : this.defaultOptions.backgroundImageUrl),
			backgroundColor     : (cbParallax.backgroundColor != 'undefined' ? cbParallax.backgroundColor : this.defaultOptions.backgroundColor),
			positionX           : (cbParallax.positionX != 'undefined' ? cbParallax.positionX : this.defaultOptions.positionX),
			positionY           : (cbParallax.positionY != 'undefined' ? cbParallax.positionY : this.defaultOptions.positionX),
			backgroundAttachment: (cbParallax.backgroundAttachment != 'undefined' ? cbParallax.backgroundAttachment : this.defaultOptions.backgroundAttachment),

			width : (cbParallax.imageWidth != 'undefined' ? cbParallax.imageWidth : this.defaultOptions.imageWidth),
			height: (cbParallax.imageHeight != 'undefined' ? cbParallax.imageHeight : this.defaultOptions.imageHeight)
		};
		this.overlay = {
			path   : (cbParallax.overlayPath != 'undefined' ? cbParallax.overlayPath : this.defaultOptions.overlayPath),
			image  : (cbParallax.overlayImage != 'undefined' ? cbParallax.overlayImage : this.defaultOptions.overlayImage),
			opacity: (cbParallax.overlayOpacity != 'undefined' ? cbParallax.overlayOpacity : this.defaultOptions.overlayOpacity),
			color  : (cbParallax.overlayColor != 'undefined' ? cbParallax.overlayColor : this.defaultOptions.overlayColor)
		};
		this.overlayContainer = document.getElementById('cb_parallax_overlay_container');
		this.body = document.getElementsByTagName('body');
		this.html = document.getElementsByTagName('html');
		this.requestAnimationFrame = window.mozRequestAnimationFrame || window.msRequestAnimationFrame || window.requestAnimationFrame;
		this.isScrolling = false;
		this.isResizing = false;
		this.niceScrollConfig = {
			zindex                 : '-9999',
			scrollspeed            : '120',
			mousescrollstep        : '24',
			preservenativescrolling: true,
			horizrailenabled       : false,
			cursordragspeed        : '1.2'
		};
	}


	Plugin.prototype = {

		constructor                    : Plugin,

		setOverlay                     : function ()
		{

			if (cbParallax.overlayImage != cbParallax.noneString)
			{
				$('body').prepend('<div id="cb_parallax_overlay"></div>');
				this.overlayContainer = $('#cb_parallax_overlay');
				this.overlayContainer.css({
					'background'      : 'url(' + cbParallax.overlayPath + cbParallax.overlayImage + ')',
					'background-color': Plugin.prototype.hexToRgbA(cbParallax.overlayColor),
					'opacity'         : cbParallax.overlayOpacity
				});
			}
		},
		setupImageContainer            : function ()
		{

			$('#cb_parallax_image_container').css({
				'background-size' : cbParallax.imageWidth + 'px' + ' ' + cbParallax.imageHeight + 'px',
			});

		},
		hexToRgbA                      : function (hex)
		{
			var c;
			if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex))
			{
				c = hex.substring(1).split('');
				if (c.length == 3)
				{
					c = [c[0], c[0], c[1], c[1], c[2], c[2]];
				}
				c = '0x' + c.join('');
				return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ', ' + '0.5'/*cbParallax.overlayOpacity*/ + ')';
			}
			//throw new Error('Bad Hex');
		},
		revertBodyStyling              : function ()
		{
			var body = $('body');
			body.removeClass('custom-background');
			body.removeProp('background-image');
		},
		getHorizontalAlignInPx         : function ()
		{

			var posX = null;
			switch (cbParallax.positionX)
			{

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
		getVerticalAlignInPx           : function ()
		{

			var posY = null;
			switch (cbParallax.positionY)
			{

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


		parallaxGetScrollRatio         : function ()
		{

			var documentOffsetX = null;
			var imageOffsetY = null;
			var imageOffsetX = null;
			var ratio = null;
			if (cbParallax.direction == 'vertical')
			{
				documentOffsetX = $(document).height() - $(window).height();
				imageOffsetY = cbParallax.imageHeight - $(window).height();

				ratio = (imageOffsetY / documentOffsetX);

			}
			else if (cbParallax.direction == 'horizontal')
			{
				documentOffsetX = $(document).height() - $(window).height();
				imageOffsetX = cbParallax.imageWidth - $(window).width();

				ratio = (imageOffsetX / documentOffsetX);
			}
			return ratio;
		},
		parallaxGetTransform           : function ()
		{

			var transform = {
				x: null,
				y: null
			};
			var ratio = Plugin.prototype.parallaxGetScrollRatio();
			var scrollingPosition = $(window).scrollTop();
			// Determines the values for the transformation.
			if (cbParallax.direction == 'vertical')
			{

				if (cbParallax.verticalScrollDirection == 'to top')
				{
					transform.x = 0;
					transform.y = -scrollingPosition * ratio;

				}
				else if (cbParallax.verticalScrollDirection == 'to bottom')
				{
					transform.x = 0;
					transform.y = scrollingPosition * ratio;
				}
			}
			else if (cbParallax.direction == 'horizontal')
			{

				if (cbParallax.horizontalScrollDirection == 'to the left')
				{
					transform.x = -scrollingPosition * ratio;
					transform.y = 0;

				}
				else if (cbParallax.horizontalScrollDirection == 'to the right')
				{
					transform.x = scrollingPosition * ratio;
					transform.y = 0;
				}
			}
			return transform;
		},
		parallaxSetInitialPosition     : function ()
		{

			var imageContainer = $('#cb_parallax_image_container');

			if (cbParallax.direction == 'vertical')
			{
				imageContainer.css({
					'left': Plugin.prototype.getHorizontalAlignInPx()
				});

				if (cbParallax.verticalScrollDirection == 'to top')
				{

					imageContainer.css({
						'top': 0
					});

				}
				else if (cbParallax.verticalScrollDirection == 'to bottom')
				{

					imageContainer.css({
						'bottom': 0
					});
				}

			}
			else if (cbParallax.direction == 'horizontal')
			{

				imageContainer.css({
					'top': Plugin.prototype.getVerticalAlignInPx()
				});

				if (cbParallax.horizontalScrollDirection == 'to the left')
				{

					imageContainer.css({
						'left': 0
					});

				}
				else if (cbParallax.horizontalScrollDirection == 'to the right')
				{

					imageContainer.css({
						'right': 0
					});
				}
			}
		},
		parallaxAnimationLoop          : function ()
		{

			if (Plugin.prototype.isScrolling)
			{

				var transform = Plugin.prototype.parallaxGetTransform();
				Plugin.prototype.parallaxSetTranslate3DTransform(transform);
			}
			Plugin.prototype.isScrolling = false;
			requestAnimationFrame(Plugin.prototype.parallaxAnimationLoop);
		},
		parallaxKeepImageCentered      : function ()
		{

			if ( Plugin.prototype.isResizing)
			{

				var posX = Plugin.prototype.getHorizontalAlignInPx();
				var posY = Plugin.prototype.getVerticalAlignInPx();

				if (cbParallax.direction == 'vertical' && cbParallax.horizontalAlignment == 'center' || cbParallax.horizontalAlignment == 'right')
				{

					$('#cb_parallax_image_container').css({
						'left': posX
					});
				}
				else if (cbParallax.direction == 'horizontal' && cbParallax.verticalAlignment == 'center' || cbParallax.verticalAlignment == 'bottom')
				{

					$('#cb_parallax_image_container').css({
						'top': posY
					});
				}
			}
		},
		parallaxAnimationLoopOnResize  : function ()
		{

			if (Plugin.prototype.isResizing)
			{
				Plugin.prototype.parallaxKeepImageCentered();
				var transform = Plugin.prototype.parallaxGetTransform();
				Plugin.prototype.parallaxSetTranslate3DTransform(transform);
			}
			Plugin.prototype.isResizing = false;
			requestAnimationFrame(Plugin.prototype.parallaxAnimationLoopOnResize);
		},
		parallaxSetTranslate3DTransform: function (transform)
		{

			$('#cb_parallax_image_container').css({
				'-webkit-transform': 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-moz-transform'   : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-ms-transform'    : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'-o-transform'     : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)',
				'transform'        : 'translate3d(' + transform.x + 'px, ' + transform.y + 'px, 0px)'
			});
		},
		parallaxPreserveScrolling      : function ()
		{

			var nice = $('html').niceScroll( Plugin.prototype.niceScrollConfig);
			nice.hide();
			$('body').css('-ms-overflow-style', 'scrollbar');
		},
		parallaxBootstrap              : function ()
		{

			if ($('#ascrail2000').length == 0)
			{
				Plugin.prototype.parallaxPreserveScrolling();
			}

			Plugin.prototype.setOverlay();
			Plugin.prototype.revertBodyStyling();
			Plugin.prototype.parallaxSetInitialPosition();

			// We call this one once from here so everything is Radiohead, Everything in its right place.
			var transform = Plugin.prototype.parallaxGetTransform();
			Plugin.prototype.parallaxSetTranslate3DTransform(transform);
		},


		setupStaticImageContainer: function ()
		{
			$( 'body' ).css( {
				'background-size': cbParallax.imageWidth + 'px' + ' ' + cbParallax.imageHeight + 'px',
				'background'     : 'url(' + cbParallax.backgroundImageUrl + ')',
				'background-position': cbParallax.positionX + ' ' + cbParallax.positionY,
				'background-attachment': cbParallax.backgroundAttachment,
				'background-repeat'    : cbParallax.backgroundRepeat,
				//'zIndex': '-2'
			} );
		},
		staticKeepImageAligned         : function ()
		{

			Plugin.prototype.isResizing = false;
			requestAnimationFrame(Plugin.prototype.staticKeepImageAligned);
		},
		staticBootstrap                : function ()
		{

			if ($('#ascrail2000').length == 0)
			{
				Plugin.prototype.parallaxPreserveScrolling();
			}
			Plugin.prototype.setOverlay();
			Plugin.prototype.revertBodyStyling();
			Plugin.prototype.setupStaticImageContainer();
		},


		prependCanvas                  : function ()
		{
			var canvas = '<canvas id="cb_parallax_image_container" class="custom-background" width="' + cbParallax.imageWidth + '" height="' + cbParallax.imageHeight + '"></canvas>';
			$('body').prepend( canvas );
		},


		init                           : function ()
		{

			if ( '1' == this.parallax.canParallax && '1' == this.parallax.parallaxEnabled)
			{

				window.onload = function ()
				{

					Plugin.prototype.prependCanvas();
					var canvas = document.getElementById('cb_parallax_image_container');
					var context = canvas.getContext('2d');
					var img = new Image();

					img.onload = function ()
					{
						context.drawImage(img, 0, 0, cbParallax.imageWidth, cbParallax.imageHeight);
						Plugin.prototype.parallaxBootstrap();
					};

					img.src = cbParallax.backgroundImageUrl;
				};
			}
			else if ( '1' == this.parallax.canParallax && '0' == this.parallax.parallaxEnabled || '0' == this.parallax.canParallax || isMobile && '1' == this.parallax.disableOnMobile )
			{

				Plugin.prototype.staticBootstrap();
			}

		},


		observeScrollEvent             : function ()
		{

			$(document).bind('scroll', function ()
			{

				if ('1' == cbParallax.canParallax && '1' == cbParallax.parallaxEnabled)
				{

					if (false == isMobile || false == isMobile && '1' != cbParallax.disableOnMobile)
					{
						Plugin.prototype.isScrolling = true;
						requestAnimationFrame(Plugin.prototype.parallaxAnimationLoop);
					}
				}
			});
		},
		observeResizeEvent             : function ()
		{

			$(window).bind('resize', function ()
			{

				if ('1' == cbParallax.canParallax && '1' == cbParallax.parallaxEnabled)
				{

					if (false == isMobile && '0' == cbParallax.disableOnMobile)
					{
						Plugin.prototype.isResizing = true;
						requestAnimationFrame(Plugin.prototype.parallaxAnimationLoopOnResize);
					}
				}
				else if ('1' == cbParallax.canParallax && '0' == cbParallax.parallaxEnabled || false == isMobile && '1' == cbParallax.disableOnMobile)
				{
					Plugin.prototype.isResizing = true;
					requestAnimationFrame(Plugin.prototype.staticKeepImageAligned);
				}
			});
		}

	};


	$(document).one('ready', function ()
	{

		var plugin = new Plugin();
		plugin.init();
		plugin.observeScrollEvent();
		plugin.observeResizeEvent();
	});


})(jQuery);
