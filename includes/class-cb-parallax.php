<?php
/**
 * The core plugin class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Includes;

use CbParallax\Pub as Pub;
use CbParallax\Admin as Admin;
use CbParallax\Admin\Menu\Includes as MenuIncludes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Require dependencies.
 */
if (!class_exists('CbParallax\Includes\cb_parallax_i18n')) {
    require_once CBPARALLAX_ROOT_DIR.'includes/class-i18n.php';
}
if (!class_exists('CbParallax\Admin\cb_parallax_admin')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/class-admin.php';
}
if (!class_exists('CbParallax\Pub\cb_parallax_public')) {
    require_once CBPARALLAX_ROOT_DIR.'public/class-public.php';
}
if (!class_exists('CbParallax\Admin\Menu\Includes\cb_parallax_options')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/menu/includes/class-options.php';
}

/**
 * The core class of the plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax
{
    /**
     * The unique identifier of the plugin.
     *
     * @var string $name
     * @since    0.1.0
     * @access   private
     */
    private string $name;
    
    /**
     * The domain of the plugin.
     *
     * @var string $domain
     * @since    0.1.0
     * @access   private
     */
    private string $domain;
    
    /**
     * The current version of the plugin.
     *
     * @var string $version
     * @since    0.1.0
     * @access   private
     */
    private string $version;
    
    /**
     * The reference to the options class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_options $options
     */
    private MenuIncludes\cb_parallax_options $options;
    
    /**
     * cb_parallax constructor.
     */
    public function __construct()
    {
        $this->name = 'cb-parallax';
        $this->domain = $this->name;
        $this->version = CBPARALLAX_VERSION;
        
        $this->set_options_instance();
        $this->include_plugin_text_domain();
    }
    
    /**
     * Holds the object that references to the class responsible for
     * handling the user-defined options.
     *
     * @return void
     */
    private function set_options_instance() : void
    {
        $this->options = new MenuIncludes\cb_parallax_options($this->domain);
    }
    
    /**
     * Instantiates the class responsible for the language localisation features
     * and calls the method that adds the hooks to WordPress.
     *
     * @return void
     */
    private function include_plugin_text_domain() : void
    {
        $i18n = new cb_parallax_i18n($this->domain);
        $i18n->add_hooks();
    }
    
    /**
     * Calls the functions that register the initial hooks with WordPress
     * for both the admin area and the public area.
     *
     * @return void
     */
    public function run() : void
    {
        $this->ensure_options();
        $this->run_admin();
        $this->run_public();
    }
    /**
     * @return void
     */
    private function ensure_options() : void
    {
        $defaults = array_merge($this->options->get_default_options(), $this->options->get_default_image_options());
        $options = get_option('cb_parallax_options');
        
        if (!is_array($options)) {
            add_option('cb_parallax_options', $defaults, '', true);
        } else {
            $upgraded = wp_parse_args($options, $defaults);
            if ($upgraded !== $options) {
                update_option('cb_parallax_options', $upgraded, true);
            }
        }
        
        update_option('cb_parallax_plugin', array('settings_version' => CBPARALLAX_VERSION), true);
    }
    /**
     * @return void
     */
    public function set_initial_plugin_options() : void
    {
        $cb_parallax_options = new MenuIncludes\cb_parallax_options('cb-parallax');
        add_option('cb_parallax_options', array_merge($cb_parallax_options->get_default_options(), $cb_parallax_options->get_default_image_options()), '', true);
        add_option('cb_parallax_plugin', ['settings_version' => CBPARALLAX_VERSION], '', true);
    }
    
    /**
     * Runs the admin part of the plugin,
     * if we're on the admin part of the website.
     *
     * @return void
     */
    private function run_admin() : void
    {
        if (!is_admin()) {
            return;
        }
        
        $admin = new Admin\cb_parallax_admin($this->domain, $this->version, $this->options);
        $admin->add_hooks();
    }
    
    /**
     * Runs the public part of the plugin,
     * if we're on the public part of the website.
     *
     * @return void
     */
    private function run_public() : void
    {
        // If it's admin, which it shouldn't be, we stop here
        if (is_admin()) {
            return;
        }
        
        // If parallax is disabled, we stop here
        $options = get_option('cb_parallax_options');
        if (false === $options) {
            return;
        }
        if ('0' === ($options['cb_parallax_parallax_enabled'] ?? '0') && '0' === ($options['cb_parallax_allow_override'] ?? '0')) {
            return;
        }
        
        // If the plugin is disabled on mobile, we stop here.
        if ('1' === ($options['cb_parallax_disable_on_mobile'] ?? '0') && $this->is_mobile_device()) {
            return;
        }
        
        // Add support for custom backgrounds
        add_theme_support('custom-background');
        
        // We run the public part of the plugin.
        $public = new Pub\cb_parallax_public($this->domain, $this->options);
        $public->add_hooks();
    }
    
    /**
     * Detects if the current request is from a mobile device.
     *
     * Uses WordPress's built-in wp_is_mobile() function as primary detection,
     * with a fallback to user agent detection for edge cases.
     *
     * @return bool True if mobile device detected, false otherwise.
     * @since 0.9.8
     */
    private function is_mobile_device() : bool
    {
        // First check WordPress's built-in mobile detection.
        if (function_exists('wp_is_mobile') && wp_is_mobile()) {
            return true;
        }
        
        // Fallback to user agent detection if available.
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        
        $useragent = sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']));
        
        // Check for common mobile device patterns.
        $mobile_patterns = array(
            'android',
            'mobile',
            'iphone',
            'ipad',
            'ipod',
            'blackberry',
            'windows phone',
            'opera mini',
            'opera mobi',
        );
        
        $useragent_lower = strtolower($useragent);
        foreach ($mobile_patterns as $pattern) {
            if (false !== strpos($useragent_lower, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
}
