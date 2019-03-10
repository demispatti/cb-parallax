/**
 * The script for the admin menu.

 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb-parallax
 * @subpackage        cb-parallax/admin/menu/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
(function ( $ )
{
	"use strict";

	var backgroundImage = null;
	var attachmentId = null;
	var parallaxOnOffCheckBox = null;
	var parallaxDirectionSelectBox = null;
	var overlayOptionsContainer = null;
	var overlayImageSelectBox = null;
	var overlayImageContainer = null;

	var attachmentWidth = cbParallax.attachmentWidth != 'undefined' ? cbParallax.attachmentWidth : 0;
	var attachmentHeight = cbParallax.attachmentHeight != 'undefined' ? cbParallax.attachmentHeight : 0;

	var parallaxGlobalContainer = null;

	var addMediaButton = null;
	var removeMediaButton = null;

	var backgroundImageContainer = null;
	var parallaxEnabledContainer = null;
	var imageOptionsContainer = null;
	var parallaxOptionsContainer = null;
	//var backgroundColorContainer = null;
	var backgroundAttachmentContainer = null;

	var verticalScrollDirectionContainer = null;
	var horizontalScrollDirectionContainer = null;
	var verticalAlignmentContainer = null;
	var horizontalAlignmentContainer = null;
	var overlayColorContainer = null;
	var overlayOpacityContainer = null;
	var directionContainer = null;

	var cb_parallax_frame;


	function setImageUrl ()
	{
		backgroundImage.attr( "src", (cbParallax.backgroundImageUrl) != 'undefined' ? cbParallax.backgroundImageUrl : '' );
	}
	function wrapOptionFields ()
	{
		var ids = cbParallax.defaults;

		$( ids ).each( function ( i )
		{
			var tr = $( '.form-table tr' ).eq( i );
			var id = $( this ).selector;

			tr.wrap( '<div class="cb-parallax-table-row" id="' + id + '_container"></div>' );
		} );

		$( '.wp-picker-container:nth-of-type(1)' ).wrap( '<div class="cb-parallax-table-row" id="cb_parallax_background_color_container"></div>' );
		$( '.wp-picker-container:nth-of-type(2)' ).wrap( '<div class="cb-parallax-table-row" id="cb_parallax_overlay_color_container"></div>' );

	}
	function wrapImage ()
	{
		backgroundImage.wrap( '<a class="cb-parallax-image-container" href="#"></a>' );
	}
	function wrapImageOptionsHeading ()
	{
		$( 'h2:nth-of-type(3)' ).wrap( '<a class="cb-parallax-image-options-heading-container"></a>' );
	}
	function setObjects ()
	{
		// Background image
		backgroundImage = $( '#cb_parallax_background_image_url' );
		// Attachment id
		attachmentId = $( "#cb_parallax_attachment_id" );
		// On/Off switch
		parallaxOnOffCheckBox = $( '#cb_parallax_parallax_enabled' );
		// Direction switch
		parallaxDirectionSelectBox = $( '#cb_parallax_direction' );
		// Overlay options select
		overlayImageSelectBox = $( '#cb_parallax_overlay_image' );
		// Add media button
		addMediaButton = $( ".cb-parallax-add-media-button" );
		// Remove media button
		removeMediaButton = $( ".cb-parallax-remove-media-button" );
		// Set this height to keep things in place
	}
	function assembleContainers ()
	{
		backgroundAttachmentContainer = $( '#cb_parallax_background_attachment_container' );
		//backgroundColorContainer = $( '#cb_parallax_background_color_container' );
		backgroundImageContainer = $( '#cb_parallax_background_image_container' );

		parallaxEnabledContainer = $( '#cb_parallax_parallax_enabled_container' );
		directionContainer = $( '#cb_parallax_direction_container' );

		verticalScrollDirectionContainer = $( '#cb_parallax_vertical_scroll_direction_container' );
		horizontalScrollDirectionContainer = $( '#cb_parallax_horizontal_scroll_direction_container' );
		verticalAlignmentContainer = $( '#cb_parallax_vertical_alignment_container' );
		horizontalAlignmentContainer = $( '#cb_parallax_horizontal_alignment_container' );

		overlayImageContainer = $( '#cb_parallax_overlay_image_container' );
		overlayOpacityContainer = $( '#cb_parallax_overlay_opacity_container' );
		overlayColorContainer = $( '#cb_parallax_overlay_color_container' );

		parallaxGlobalContainer = $( '#cb_parallax_global_container' );

		imageOptionsContainer = $( '#cb_parallax_background_repeat_container, #cb_parallax_position_x_container, #cb_parallax_position_y_container, #cb_parallax_background_attachment_container' ).wrapAll( '<div class="cb-parallax-image-options-container"></div>' );
		parallaxOptionsContainer = $( '#cb_parallax_vertical_scroll_direction_container, #cb_parallax_horizontal_scroll_direction_container, #cb_parallax_horizontal_alignment_container, #cb_parallax_vertical_alignment_container, #cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container' ).wrapAll( '<div class="cb-parallax-parallax-options-container"></div>' );
		overlayOptionsContainer = $( '#cb_parallax_overlay_image_container, #cb_parallax_overlay_opacity_container, #cb_parallax_overlay_color_container' ).wrapAll( '<div class="cb-parallax-overlay-options-container"></div>' );

	}
	function insertPluginSectionHeading ()
	{
		parallaxGlobalContainer.before( '<div id="cb_parallax_plugin_options_heading_container"><h2>' + cbParallax.pluginSectionTitleText + '</h2></div>' );
	}
	function insertImageSectionHeading ()
	{
		$( '.cb-parallax-image-options-container' ).before( '<div id="cb_parallax_image_options_heading_container"><h2>' + cbParallax.imageSectionTitleText + '</h2></div>' );
	}
	function initColorpicker ()
	{
		$( '#cb_parallax_background_color, #cb_parallax_overlay_color' ).wpColorPicker();
	}
	function initFancySelect ()
	{
		$( '.cb-parallax-fancy-select' ).fancySelect();
	}
	function localizeScript ()
	{
		if ( cbParallax.locale == 'de_DE' )
		{
			$( '<style>.cb-parallax-switch-input ~ .cb-parallax-switch-label:before{content:"' + cbParallax.switchesText.Off + '";}</style>' ).appendTo( 'head' );
			$( '<style>.cb-parallax-switch-input:checked ~ .cb-parallax-switch-label:after{content:"' + cbParallax.switchesText.On + '";}</style>' ).appendTo( 'head' );
		}
	}
	function fixView()
	{
		if(backgroundAttachmentContainer.css('display') == 'none')
		{
			verticalAlignmentContainer.css('height', directionContainer.height() * 2);
			horizontalAlignmentContainer.css( 'height', directionContainer.height() * 2 );
		} else
		{
			verticalAlignmentContainer.css( 'height', 'auto' );
			horizontalAlignmentContainer.css( 'height', 'auto' );
		}
	}


	function toggleParallaxOnOffSwitch ()
	{
		setView();
	}
	function toggleParallaxDirection ()
	{
		if ( parallaxOnOffCheckBox.prop( 'checked' ) )
		{
			if ( backgroundImage.attr( 'src' ) != '' )
			{
				if ( parallaxDirectionSelectBox.val() === cbParallax.verticalString )
				{
					horizontalScrollDirectionContainer.hide();
					verticalAlignmentContainer.hide();

					verticalScrollDirectionContainer.show();
					horizontalAlignmentContainer.show();
				}
				else
				{
					verticalScrollDirectionContainer.hide();
					horizontalAlignmentContainer.hide();

					horizontalScrollDirectionContainer.show();
					verticalAlignmentContainer.show();
				}
			}
			else
			{
				verticalScrollDirectionContainer.hide();
				horizontalAlignmentContainer.hide();

				horizontalScrollDirectionContainer.hide();
				verticalAlignmentContainer.hide();
			}

		}
		else
		{
			verticalScrollDirectionContainer.hide();
			horizontalAlignmentContainer.hide();

			horizontalScrollDirectionContainer.hide();
			verticalAlignmentContainer.hide();
		}

	}
	function toggleOverlayOpacityAndColorOptions ()
	{
		if ( backgroundImage.attr( 'src' ) != '' )
		{
			overlayOptionsContainer.show();

			if ( $( "#cb_parallax_overlay_image" ).val() != cbParallax.noneString )
			{
				overlayOpacityContainer.show();
				overlayColorContainer.show();
			}
			else
			{
				overlayOpacityContainer.hide();
				overlayColorContainer.hide();
			}

		}
		else
		{
			overlayOpacityContainer.hide();
			overlayColorContainer.hide();
		}

	}


	function setView ()
	{
		// If there is an attachment...
		if ( backgroundImage.attr( 'src' ) != '' )
		{
			// If parallax is not possible with this attachment...
			if ( attachmentWidth < 1920 || attachmentHeight < 1200 )
			{
				parallaxOptionsContainer.hide();
				parallaxEnabledContainer.hide();
				directionContainer.hide();
				imageOptionsContainer.show();

				addMediaButton.hide();
				backgroundImage.show();
				removeMediaButton.show();
			}
			// else if parallax is possible AND "off"
			else if ( ( attachmentWidth >= 1920 && attachmentHeight >= 1200 ) && false == parallaxOnOffCheckBox.prop( 'checked' ) )
			{
				directionContainer.hide();
				parallaxOptionsContainer.hide();
				overlayOptionsContainer.hide();
				imageOptionsContainer.show();
				parallaxEnabledContainer.show();

				addMediaButton.hide();
				backgroundImage.show();
				removeMediaButton.show();
			}
			// else if parallax is possible AND "on"
			else if ( true == attachmentWidth >= 1920 && attachmentHeight >= 1200 && parallaxOnOffCheckBox.prop( 'checked' ) )
			{
				parallaxOptionsContainer.show();
				parallaxEnabledContainer.show();
				directionContainer.show();
				imageOptionsContainer.hide();

				addMediaButton.hide();
				backgroundImage.show();
				removeMediaButton.show();
			}
			// Else parallax is possible AND "on"
			else
			{
				directionContainer.hide();
				parallaxOptionsContainer.hide();
				overlayOptionsContainer.hide();
				imageOptionsContainer.show();
				parallaxEnabledContainer.show();

				addMediaButton.hide();
				backgroundImage.show();
				removeMediaButton.show();
			}
		}
		// ...else there is no attachment...
		else
		{
			parallaxEnabledContainer.hide();
			imageOptionsContainer.hide();
			parallaxOptionsContainer.hide();
			directionContainer.hide();
			$( '#cb_parallax_image_options_heading_container' ).hide();

			removeMediaButton.hide();
			backgroundImage.hide();
			addMediaButton.show();
		}

		toggleOverlayOpacityAndColorOptions();
		toggleParallaxDirection();
		fixView();
	}


	function listen ()
	{
		parallaxOnOffCheckBox.bind( 'click', function ()
		{
			toggleParallaxOnOffSwitch();
		} );

		parallaxDirectionSelectBox.bind( 'change.fs', function ()
		{
			$( this ).trigger( 'change.$' );
			toggleParallaxDirection();
		} );

		overlayOptionsContainer.bind( 'change.fs', function ()
		{
			$( this ).trigger( 'change.$' );
			toggleOverlayOpacityAndColorOptions();
		} );

		removeMediaButton.bind( 'click', function ()
		{
			backgroundImage.attr( 'src', '' );
			attachmentId.val( '' );

			attachmentHeight = 0;
			attachmentWidth = 0;

			setView();

		} );

		$( '.cb-parallax-add-media-button, .cb-parallax-media-url, .cb-parallax-image-container' ).bind( 'click', function ()
		{

			if ( cb_parallax_frame )
			{
				cb_parallax_frame.open();
				return;
			}
			cb_parallax_frame = wp.media.frames.cb_parallax_frame = wp.media( {

				className: "media-frame cb-parallax-frame",
				frame    : "select",
				multiple : false,
				title    : cbParallaxMediaFrame.title,
				library  : { type: "image" },
				button   : { text: cbParallaxMediaFrame.button }
			} );

			cb_parallax_frame.on( "select", function ()
			{
				var media_attachment = cb_parallax_frame.state().get( "selection" ).first().toJSON();

				attachmentId.val( media_attachment.id );
				backgroundImage.attr( 'src', media_attachment.url );

				attachmentHeight = media_attachment.height;
				attachmentWidth = media_attachment.width;

				setView();
			} );

			// Opens the media frame.
			cb_parallax_frame.open();
		} );
	}


	function dismissNotice ()
	{
		var notice = $( '.cb-parallax-admin-menu .notice' );

		if ( notice.length )
		{
			notice.slideUp( 400 );
		}
	}


	$( document ).one( 'ready', function ()
	{
		setTimeout( dismissNotice, 3600 );
	} );


	$( document ).ready( function ()
	{
		localizeScript();
		wrapOptionFields();
		setObjects();
		wrapImage();
		setImageUrl();
		assembleContainers();
		insertImageSectionHeading();
		wrapImageOptionsHeading();
		insertPluginSectionHeading();
		initColorpicker();
		initFancySelect();
		setView();

		listen();
	} );


})( jQuery );
