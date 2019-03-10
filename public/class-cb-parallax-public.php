<?php

/**
 * The public facing part of the plugin.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/public
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_public {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_name
	 */
	private $plugin_name;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string $plugin_version
	 */
	private $plugin_version;

	/**
	 * The loader that's responsible for maintaining
	 * and registering all hooks that power the plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      object $loader
	 */
	private $loader;

	/**
	 * Kicks off the public part of the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_name
	 * @param string $plugin_domain
	 * @param string $plugin_version
	 * @param object $loader
	 * @param string $meta_key
	 */
	public function __construct( $plugin_name, $plugin_domain, $plugin_version, $loader ) {

		$this->plugin_name    = $plugin_name;
		$this->plugin_domain  = $plugin_domain;
		$this->plugin_version = $plugin_version;
		$this->loader         = $loader;

		$this->load_dependencies();
	}

	/**
	 * Checks if it should "superseed" a possibly installed "Nicescrollr" plugin on the frontend.
	 *
	 * @hooked_action
	 *
	 * @since    0.2.4
	 * @access   public
	 * @return   void
	 */
	public function check_for_nicescrollr_plugin() {

		// Retrieves the "parallax enabled" option set within the meta box.
		$post_meta = get_post_meta( get_the_ID(), 'cb_parallax', true );
		// Checks for the "scrolling preserved" option.
		$scrolling_preserved = ( '1' == get_option( 'cb_parallax', true ) ? get_option( 'cb_parallax', true ) : false );

		$parallax_enabled = ( isset( $post_meta['parallax_enabled'] ) && '1' == $post_meta['parallax_enabled'] ? $post_meta['parallax_enabled'] : false );

		if ( $parallax_enabled || $scrolling_preserved ) {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'nicescrollr/nsr.php' ) ) {

				set_transient( 'cb_parallax_superseeds_nicescrollr_plugin_on_frontend', true, 60 );
			}
		}
	}

	/**
	 * Loads the class responsible for localizing the script that manages the parallax related stuff on the frontend.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "public/includes/class-cb-parallax-public-localisation.php";
	}

	/**
	 * Registers the stylesheet for the public-facing side of the site.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name . '-public-css',
			plugin_dir_url( __FILE__ ) . 'css/public.css',
			array(),
			$this->plugin_version,
			'all'
		);
	}

	/**
	 * Registers the scripts for the public-facing side of the site.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function enqueue_scripts() {

		// Nicescroll, modified version.
		wp_enqueue_script(
			$this->plugin_name . '-cb-parallax-nicescroll-min-js',
			plugin_dir_url( __FILE__ ) . '../vendor/nicescroll/jquery.cbp.nicescroll.min.js',
			array( 'jquery' ),
			$this->plugin_version,
			true
		);

		if( $this->has_background_image() ) {
			// Parallax script.
			wp_enqueue_script(
				$this->plugin_name . '-public-js',
				plugin_dir_url( __FILE__ ) . 'js/public.js',
				array(
					'jquery',
					$this->plugin_name . '-cb-parallax-nicescroll-min-js',
				),
				$this->plugin_version,
				true
			);
		}

	}

	/**
	 * Initiates the localisation for the public part of the plugin.
	 *
	 * @hooked_action
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function define_public_localisation() {

		$public_localisation = new cb_parallax_public_localisation( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_localisation, 'retrieve_image_data', 12 );
		$this->loader->add_action( 'template_redirect', $public_localisation, 'retrieve_image_options' );
		$this->loader->add_action( 'wp_enqueue_scripts', $public_localisation, 'localize_frontend', 1000 );
	}

	/**
	 * Determines if there is a background image related to the requested post (type).
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   bool
	 */
	private function has_background_image() {

		global $post;

		$image_options              = get_option( 'cb_parallax_options' );
		$post_meta                  = isset( $post ) ? get_post_meta( $post->ID, 'cb_parallax', true ) : false;
		$menu_options_attachment_id = isset( $image_options['cb_parallax_attachment_id'] ) ? $image_options['cb_parallax_attachment_id'] : false;
		$post_meta_attachment_id    = isset( $post_meta['cb_parallax_attachment_id'] ) ? $post_meta['cb_parallax_attachment_id'] : false;

		$image_source = $this->determine_image_source();

		if ( 'global' == $image_source ) {

			if( false !== $menu_options_attachment_id ) {

				return true;
			} else {

				return false;
			}
		} else {

			if ( false !== $post_meta_attachment_id ) {

				return true;
			} else {

				return false;
			}
		}
	}

	/**
	 * Determines the image source.
	 *
	 * @since    0.6.0
	 * @access   private
	 * @return   string $source_type
	 */
	private function determine_image_source() {

		global $post;

		$post_meta      = isset( $post ) ? get_post_meta( $post->ID, 'cb_parallax', true ) : false;
		$post_has_image = isset( $post_meta['cb_parallax_attachment_id'] ) ? $post_meta['cb_parallax_attachment_id'] : false;

		$options        = get_option( 'cb_parallax_options' );
		$is_global      = isset( $options['cb_parallax_global'] ) ? $options['cb_parallax_global'] : false;
		$allow_override = isset( $options['cb_parallax_allow_override'] ) ? $options['cb_parallax_allow_override'] : false;
		$attachment_id  = isset( $options['cb_parallax_attachment_id'] ) ? $options['cb_parallax_attachment_id'] : '';

		$source_type = null;

		if ( ! $is_global || $is_global && $attachment_id == '' ) {
			$source_type = 'per_post';

		} else if ( $is_global && $allow_override && $post_has_image ) {
			$source_type = 'per_post';
		} else {
			$source_type = 'global';
		}

		return $source_type;

	}

	/**
	 * Retrieves the name of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * Retrieves the domain of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The domain of the plugin.
	 */
	public function get_plugin_domain() {

		return $this->plugin_domain;
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}

}
