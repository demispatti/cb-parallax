<?php

namespace CbParallax\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the help sidebar.
 *
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_help_sidebar_display {
	
	/**
	 * Returns a string containing the content of the 'Help Sidebar'.
	 *
	 * @param string $domain
	 * @param \WP_Screen $current_screen
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function help_sidebar_display( $domain, $current_screen ) {
		
		$html = $current_screen->get_help_sidebar();
		
		ob_start();
		?>

        <p><?php /*echo __( 'More Info', $domain )*/ ?></p>
		
		<?php
		$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
