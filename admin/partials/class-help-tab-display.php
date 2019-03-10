<?php

namespace CbParallax\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the help tab.
 *
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_help_tab_display {
	
	/**
	 * Returns a string containing the 'Help Tab' content.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function help_tab_display( $domain ) {
		
		ob_start();
		?>

        <div id="cb-parallax-help-tabs">
            <ul class="nav">
                <li><a href="#tabs-1"><?php echo __( 'General Settings', $domain ) ?></a></li>
                <li><a href="#tabs-2"><?php echo __( 'How It Works', $domain ) ?></a></li>
                <li><a href="#tabs-3"><?php echo __( 'Plugin Information and Privacy Notices', $domain ) ?></a></li>
            </ul>
            <div id="tabs-1"><?php echo self::tab_content_general_settings( $domain ) ?></div>
            <div id="tabs-2"><?php echo self::tab_content_how_it_works( $domain ) ?></div>
            <div id="tabs-3"><?php echo self::tab_content_plugin_information_and_privacy_notices( $domain ) ?></div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function tab_content_general_settings( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            Soon
        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <!--<img src="<?php /*echo CBPARALLAX_ROOT_URL . 'admin/images/contextual-help/ch-naming-conventions-small.jpg' */ ?>);" alt="Contextual Help Image"/>-->
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function tab_content_how_it_works( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            Soon
        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <!--<img src="<?php /*echo CBPARALLAX_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-default-web.jpg' */ ?>);" alt="Contextual Help Image"/>-->
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <!--<img src="<?php /*echo CBPARALLAX_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-gmail-web.jpg' */ ?>);" alt="Contextual Help Image"/>-->
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function tab_content_plugin_information_and_privacy_notices( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            Soon
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
