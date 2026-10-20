<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/public
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Pub;

use CbParallax\Pub\Includes as PublicIncludes;
use CbParallax\Admin\Menu\Includes as MenuIncludes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Require dependencies.
 */
if (!class_exists('CbParallax\Pub\Includes\cb_parallax_public_localisation')) {
    require_once CBPARALLAX_ROOT_DIR.'public/includes/class-public-localisation.php';
}

/**
 * The class responsible for the public facing part of the plugin.
 *
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/public
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_public
{
    /**
     * The domain of the plugin.
     *
     * @var string $domain
     * @since    0.1.0
     * @access   private
     */
    private string $domain;
    
    /**
     * Holds the reference to the options class.
     *
     * @var    MenuIncludes\cb_parallax_options $options
     * @since  0.6.0
     * @access private
     */
    private MenuIncludes\cb_parallax_options $options;
    
    /**
     * Holds the user-defined plugin settings.
     *
     * @var array $plugin_options
     */
    private array $plugin_options;
    private string $version;
    
    /**
     * cb_parallax_public constructor.
     *
     * @param string                           $domain
     * @param MenuIncludes\cb_parallax_options $options
     */
    public function __construct(string $domain, MenuIncludes\cb_parallax_options $options)
    {
        $this->domain = $domain;
        $this->options = $options;
        $this->version = CBPARALLAX_VERSION;
        
        $this->set_plugin_options();
        $this->include_public_localisation();
    }
    
    /**
     * Retrieves the user-defined plugin settings.
     */
    private function set_plugin_options() : void
    {
        $this->plugin_options = $this->options->get_plugin_options();
    }
    
    /**
     * Includes the class responsible for assembling and sending data to the respective javascript file.
     *
     * @return void
     * @since 0.9.0
     */
    private function include_public_localisation() : void
    {
        $localisation = new PublicIncludes\cb_parallax_public_localisation($this->domain, $this->options);
        $localisation->add_hooks();
    }
    
    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function add_hooks() : void
    {
        add_action('wp_enqueue_scripts', [
            $this,
            'enqueue_styles'
        ], 14);
        add_action('wp_enqueue_scripts', [
            $this,
            'enqueue_parallax_scripts'
        ], 15);
        add_action('wp_enqueue_scripts', [
            $this,
            'enqueue_layout_scripts'
        ], 16);
    }
    
    /**
     * Registers the stylesheets with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function enqueue_styles() : void
    {
        wp_enqueue_style('cb-parallax-public-css', CBPARALLAX_ROOT_URL.'public/css/public.css', [], $this->version);
    }

    /**
     * @return void
     */
    public function enqueue_layout_scripts() : void
    {
        /**
         * @var \WP_Post $post
         */ global $post;
        
        // If there is no image at all, we stop here
        /*if (false === (new MenuIncludes\cb_parallax_options($this->domain))->determine_image_source()) {
            return;
        }*/
        
        /**
         * If the image was not found, we bail early.
         */
        if (true !== $this->options->is_image_in_media_library($post)) {
            return;
        }
        
        wp_enqueue_script('jquery');
        
        //$this->set_plugin_options($post);
        // Layout script
        $options = get_option('cb_parallax_options');
        if ('1' === ($options['cb_parallax_boxed_layout'] ?? '0')) {
            wp_enqueue_script('cb-parallax-layout-js', CBPARALLAX_ROOT_URL.'public/js/layout.js', ['jquery'], $this->version, true);
        }
    }
    /**
     * @return void
     */
    public function enqueue_parallax_scripts() : void
    {
        /**
         * @var \WP_Post $post
         */ global $post;
        
        // If there is no image at all, we stop here
        /*if (false === (new MenuIncludes\cb_parallax_options($this->domain))->determine_image_source()) {
            return;
        }*/
        
        /**
         * If the image was not found, we bail early.
         */
        if (true !== $this->options->is_image_in_media_library($post)) {
            return;
        }
        
        wp_enqueue_script('jquery');
        
        //$this->set_plugin_options($post);
        
        wp_enqueue_script('cb-parallax-inc-raf-js', CBPARALLAX_ROOT_URL.'vendor/raf/raf.js', ['jquery'], $this->version, true);
        
        wp_enqueue_script('cb-parallax-inc-smoothscroll-js', CBPARALLAX_ROOT_URL.'vendor/smoothscroll/smoothscroll.js', ['jquery'], $this->version, true);
        
        // Parallax script
        wp_enqueue_script('cb-parallax-public-js', CBPARALLAX_ROOT_URL.'public/js/public.js', [
            'jquery',
            'cb-parallax-inc-raf-js',
            'cb-parallax-inc-smoothscroll-js'
        ], $this->version, true);
    }
}
