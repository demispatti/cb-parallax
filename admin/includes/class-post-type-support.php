<?php

namespace CbParallax\Admin\Includes;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Enables the custom background support for the given post types.
 *
 * @link
 * @since             0.1.0
 * @package           cb_paraallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_post_type_support
{
    /**
     * The available post types.
     *
     * @since    0.1.0
     * @access   private
     * @var      array $post_types
     */
    
    /**
     * The feature we want the post types to work with.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $feature
     */
    private string $feature = 'custom-background';
    
    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function add_hooks() : void
    {
        add_action('init', array(
            $this,
            'add_post_type_support'
        ));
    }
    
    /**
     * Calls the WordPress function that adds support for the given feature applied on the given post type.
     */
    public function add_post_type_support() : void
    {
        foreach ($this->get_supported_post_types() as $post_type) {
            add_post_type_support($post_type, $this->feature);
        }
    }
    
    /**
     * Returns an array containing the supported post types.
     *
     * @return array
     */
    public function get_supported_post_types() : array
    {
        $excluded_posttypes = array(
            'post'                => 'post',
            'page'                => 'page',
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
        
        return array_unique(array_merge($post_types, $excluded_posttypes));
    }
}
