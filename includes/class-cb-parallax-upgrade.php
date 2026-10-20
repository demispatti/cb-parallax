<?php

namespace CbParallax\Includes;

use WP_Query;
use CbParallax\Admin\Menu\Includes as MenuIncludes;
use CbParallax\Admin\Menu\Includes\cb_parallax_options;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Require dependencies.
 */
if (!class_exists('MenuIncludes\cb_parallax_options')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/menu/includes/class-options.php';
}
if (!class_exists('AdminIncludes\cb_parallax_post_type_support')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/includes/class-post-type-support.php';
}

/**
 * Executes function such as migrating image_options and refactoring existing meta_keys.
 *
 * @link
 * @since             0.6.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_upgrade
{
    /**
     * Maintains the allowed option values.
     *
     * @since  0.6.0
     * @access public
     * @var    array $image_options_whitelist
     */
    public array $image_options_whitelist;
    
    /**
     * Maintains the default image image_options
     *
     * @since  0.6.0
     * @access public
     * @var    array $default_image_options
     */
    public array $default_image_options;
    
    /**
     * Maintains the default plugin image_options
     *
     * @since  0.6.0
     * @access public
     * @var    array $default_plugin_options
     */
    public array $default_plugin_options;
    
    /**
     * The domain of the plugin.
     *
     * @since    0.6.0
     * @access   private
     * @var      string $domain
     */
    private string $domain;
    
    /**
     * The version number of the plugin.
     *
     * @since    0.6.0
     * @access   private
     * @var      string $version
     */
    private string $version;
    
    /**
     * The reference to the image_options class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_options $options
     */
    private cb_parallax_options $options;
    
    /**
     * cb_parallax_upgrade constructor.
     *
     * @param $domain
     * @param $version
     */
    public function __construct($domain, $version)
    {
        $this->domain = $domain;
        $this->version = $version;
        $this->retrieve_options();
    }
    
    /**
     * Instantiates the class responsible for handling the options
     * and sets the required option values.
     */
    private function retrieve_options() : void
    {
        $this->options = new MenuIncludes\cb_parallax_options($this->domain);
        $this->image_options_whitelist = $this->options->get_image_options_whitelist();
        $this->default_image_options = $this->options->get_default_image_options();
        $this->default_plugin_options = $this->options->get_default_plugin_options();
    }
    
    /**
     * Kicks off the options upgrader.
     */
    public function run() : void
    {
        // Upgrade options and post meta and Add a "flag" on success
        $has_versioned_settings = false !== get_option('cb_parallax_plugin') ? get_option('cb_parallax_plugin')['settings_version'] : false;
        if (!$has_versioned_settings) {
            if ($this->upgrade_plugin_options() && $this->upgrade_post_meta_options()) {
                $this->add_upgrade_flag();
            }
        }
    }
    /**
     * @return bool
     */
    private function upgrade_plugin_options() : bool
    {
        // Upgrade post meta of all posts and pages
        $has_versioned_settings = get_option('cb_parallax_plugin');
        $options = get_option('cb_parallax_options');
        if (!$has_versioned_settings) {
            $cb_parallax_options = new cb_parallax_options('cb-parallax');
            $default_plugin_options = array_merge($cb_parallax_options->get_default_options(), $cb_parallax_options->get_default_image_options());
            foreach ($default_plugin_options as $key => $value) {
                if (true === $value) {
                    $default_plugin_options[$key] = '1';
                }
                if (false === $value) {
                    $default_plugin_options[$key] = '0';
                }
            }
            foreach ($default_plugin_options as $key => $value) {
                if (!isset($options[$key])) {
                    $options[$key] = $value;
                }
            }
            
            // Upgrade "can_parallax" value
            $image_meta = wp_get_attachment_metadata($options['cb_parallax_attachment_id']);
            if (false !== $image_meta && 1080 <= $image_meta['width'] && 1080 <= $image_meta['height']) {
                $options['cb_parallax_can_parallax'] = '1';
            } else {
                $options['cb_parallax_can_parallax'] = '0';
            }
            
            // Remove deprecated option keys
            unset($options['cb_parallax_global']);
            unset($options['cb_parallax_background_color']);
            unset($options['cb_parallax_background_repeat']);
            
            delete_option('cb_parallax_options');
            return add_option('cb_parallax_options', $options, '', true);
        }
        
        return false;
    }
    /**
     * @return bool|void
     */
    /**
     * @return bool|void
     */
    private function upgrade_post_meta_options()
    {
        $has_versioned_settings = get_option('cb_parallax_plugin');
        if (!$has_versioned_settings) {
            $cb_parallax_options = new cb_parallax_options('cb-parallax');
            $default_plugin_options = $cb_parallax_options->get_default_options();
            $default_plugin_options_args = $cb_parallax_options->get_options_arguments();
            
            // Get the post types that are supported
            $excluded_posttypes = array(
                'attachment'          => 'attachment',
                'revision'            => 'revision',
                'nav_menu_item'       => 'nav_menu_item',
                'custom_css'          => 'custom_css',
                'customize_changeset' => 'customize_changeset',
                'oembed_cache'        => 'oembed_cache',
                'user_request'        => 'user_request',
                'wp_block'            => 'wp_block',
                'wp_template'         => 'wp_template',
                'wp_template_part'    => 'wp_template_part',
                'wp_global_styles'    => 'wp_global_styles',
                'wp_navigation'       => 'wp_navigation',
                'wp_font_family'      => 'wp_font_family',
                'wp_font_face'        => 'wp_font_face',
            );
            $post_types = get_post_types();
            $post_types = array_unique(array_merge($post_types, $excluded_posttypes));
            $args = array(
                'posts_per_page' => -1,
                'post_type'      => $post_types,
                'meta_key'       => 'cb_parallax'
            );
            $posts = new WP_Query($args);
            
            // Upgrade "can_parallax" value
            foreach ($posts->posts as $post) {
                $post_meta = get_post_meta($post->ID, 'cb_parallax', true);
                if (!isset($post_meta['cb_parallax_can_parallax'])) {
                    $image_meta = wp_get_attachment_metadata($post_meta['cb_parallax_attachment_id']);
                    if (1080 <= $image_meta['width'] && 1080 <= $image_meta['height']) {
                        $post_meta['cb_parallax_can_parallax'] = true;
                    } else {
                        $post_meta['cb_parallax_can_parallax'] = false;
                    }
                }
            }
            
            foreach ($posts->posts as $post) {
                $post_meta = get_post_meta($post->ID, 'cb_parallax', true);
                // Fill missing options
                $option_keys = $this->options->get_all_option_keys('image');
                foreach ($option_keys as $option_key) {
                    if (!in_array($post_meta[$option_key], $post_meta)) {
                        $post_meta[$option_key] = $default_plugin_options[$option_key];
                    }
                }
                // Upgrade select values
                foreach ($default_plugin_options_args as $args) {
                    if (isset($args['option_key']) && 'select' === $args['input_type']) {
                        $needle = $post_meta[$args['option_key']];
                        $haystack = $args['select_values'];
                        if (!in_array($needle, $haystack)) {
                            $post_meta[$args['option_key']] = $default_plugin_options[$args['option_key']];
                            if (true === $post_meta[$args['option_key']]) {
                                $post_meta[$args['option_key']] = '1';
                            }
                            if (false === $post_meta[$args['option_key']]) {
                                $post_meta[$args['option_key']] = '0';
                            }
                        }
                    }
                }
                // Update post meta
                delete_post_meta($post->ID, 'cb_parallax');
                if (false === add_post_meta($post->ID, 'cb_parallax', $post_meta)) {
                    return false;
                }
            }
            
            // post meta is up to date
            return true;
        }
    }
    /**
     * @return void
     */
    private function add_upgrade_flag() : void
    {
        update_option('cb_parallax_plugin', [
            'settings_version' => CBPARALLAX_VERSION
        ], true);
    }
}
