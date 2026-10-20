<?php

namespace CbParallax\Admin\Menu;

use CbParallax\Admin\Partials as Partials;
use CbParallax\Admin\Includes as AdminIncludes;
use CbParallax\Admin\Menu\Includes as MenuIncludes;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

if (!class_exists('Partials\cb_parallax_settings_page_display')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/partials/class-settings-display.php';
}
if (!class_exists('AdminIncludes\cb_parallax_localisation')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/includes/class-localisation.php';
}

/**
 * The class responsible for the admin menu.
 *
 * @link
 * @since             0.6.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/menu
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_settings_page
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
     * Holds this page's screen id.
     *
     * @var string $screen_id
     */
    private string $screen_id = 'settings_page_cb-parallax';
    
    /**
     * Holds a list of supported post types.
     *
     * @var array $screen_ids
     */
    private array $screen_ids;
    
    /**
     * The reference to the options class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_options $options
     */
    private Includes\cb_parallax_options $options;
    
    /**
     * cb_parallax_admin constructor.
     *
     * @param string                           $domain
     * @param array                            $screen_ids
     * @param MenuIncludes\cb_parallax_options $options
     */
    public function __construct(string $domain, array $screen_ids, Includes\cb_parallax_options $options)
    {
        $this->domain = $domain;
        $this->screen_ids = $screen_ids;
        $this->options = $options;
        $this->add_hooks();
        $this->init();
    }
    
    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function add_hooks() : void
    {
        add_action('admin_enqueue_scripts', array(
            $this,
            'enqueue_styles'
        ), 10);
        add_action('admin_enqueue_scripts', array(
            $this,
            'enqueue_scripts'
        ), 10);
        add_action('admin_enqueue_scripts', array(
            $this,
            'include_menu_localisation'
        ));
        add_action('admin_menu', array(
            $this,
            'add_options_page'
        ), 20);
        add_filter('admin_body_class', array(
            $this,
            'add_body_class'
        ), 10, 1);
    }
    
    /**
     * Adds the actions for registering the stylesheets and javascript files with WordPress,
     * if we're on a white-listed screen.
     */
    private function init() : void
    {
        if (in_array($this->screen_id, $this->screen_ids, true)) {
            add_action('admin_enqueue_scripts', array(
                $this,
                'enqueue_styles'
            ), 10);
            add_action('admin_enqueue_scripts', array(
                $this,
                'enqueue_scripts'
            ), 10);
        }
    }
    
    /**
     * Registers the stylesheets with WordPress.
     *
     * @param string $hook_suffix
     *
     * @return void
     * @since 0.9.0
     */
    public function enqueue_styles(string $hook_suffix) : void
    {
        if ('' !== $hook_suffix && $hook_suffix === $this->screen_id) {
            $warm_stylesheet = CBPARALLAX_ROOT_DIR.'admin/css/zenchime-ui.css';
            $warm_stylesheet_version = file_exists($warm_stylesheet) ? (string) filemtime($warm_stylesheet) : CBPARALLAX_VERSION;

            // Color picker.
            wp_enqueue_style('wp-color-picker');
            
            // Fancy Select
            wp_enqueue_style('cb-parallax-inc-fancy-select-css', CBPARALLAX_ROOT_URL.'vendor/fancy-select/fancySelect.css', array(), CBPARALLAX_VERSION, 'all');
            
            // Menu
            wp_enqueue_style('cb-parallax-settings-display-css', CBPARALLAX_ROOT_URL.'admin/menu/css/settings-display.css', array(), CBPARALLAX_VERSION, 'all');

            // Warm visual layer. Kept separate so the legacy layout remains easy to maintain.
            wp_enqueue_style(
                'cb-parallax-zenchime-ui-css',
                CBPARALLAX_ROOT_URL.'admin/css/zenchime-ui.css',
                array('cb-parallax-settings-display-css'),
                $warm_stylesheet_version
            );
        }
    }
    
    /**
     * Registers the javascript files with WordPress.
     *
     * @param string $hook_suffix
     *
     * @return void
     * @since 0.9.0
     */
    public function enqueue_scripts(string $hook_suffix) : void
    {
        if ('' !== $hook_suffix && $hook_suffix === $this->screen_id) {
            $settings_script = CBPARALLAX_ROOT_DIR.'admin/js/settings-display.js';
            $settings_script_version = file_exists($settings_script) ? (string) filemtime($settings_script) : CBPARALLAX_VERSION;

            wp_enqueue_script('jquery');
            // Dashicons
            wp_enqueue_style('dashicons');
            
            // Color picker.
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('wp-color-picker-alpha', CBPARALLAX_ROOT_URL.'vendor/color-picker-alpha/wp-color-picker-alpha.min.js', array(
                'jquery',
                'wp-color-picker'
            ), 'all', true);
            
            // Media Frame.
            wp_enqueue_script('media-views');
            
            // Media upload engine.
            wp_enqueue_media();
            
            // Fancy Select.
            wp_enqueue_script('cb-parallax-inc-fancy-select-js', CBPARALLAX_ROOT_URL.'vendor/fancy-select/fancySelect.js', array('jquery'), 'all', true);
            
            // Admin part.
            wp_enqueue_script('cb-parallax-settings-display-js', CBPARALLAX_ROOT_URL.'admin/js/settings-display.js', array(
                'jquery',
                'wp-color-picker',
                'media-views',
                'cb-parallax-inc-fancy-select-js',
            ), $settings_script_version, true);
        }
    }
    
    /**
     * Adds a css class to the body tag.
     *
     * @param string $classes
     *
     * @return string $classes
     */
    public function add_body_class(string $classes) : string
    {
        $classes .= 'cb-parallax-settings-page ';
        
        return $classes;
    }
    
    /**
     * Includes the class responsible for localizing the javascript file for the menu.
     *
     * @param string $hook_suffix
     */
    public function include_menu_localisation(string $hook_suffix) : void
    {
        if ('' !== $hook_suffix && $hook_suffix === $this->screen_id) {
            $script_localisation = new AdminIncludes\cb_parallax_localisation($this->domain, $this->screen_ids, $this->options);
            $script_localisation->add_hooks();
        }
    }
    
    /**
     * Calls the function that registers the options page with WordPress.
     *
     * @return void
     */
    public function add_options_page() : void
    {
        add_options_page(__('cbParallax Settings Page', $this->domain), 'cbParallax', 'manage_options', 'cb-parallax.php', array(
                $this,
                'settings_display'
            ));
    }
    
    /**
     * Retrieves the user-defined optuons and orchestrates the functions that display the form and the settings fields.
     *
     * @echo
     */
    public function settings_display() : void
    {
        /**
         * @var \WP_Post $post
         */ global $post;
        
        $allowed_image_options = $this->options->get_image_options_whitelist();
        $display = new Partials\cb_parallax_settings_display($this->domain, $this->options, $allowed_image_options);
        
        $stored_options = get_option('cb_parallax_options');
        $attachment_id = $stored_options['cb_parallax_attachment_id'] ?? null;
        $canParallax = $stored_options['cb_parallax_can_parallax'] ?? false;
        $parallax_enabled = $stored_options['cb_parallax_can_parallax'] ?? false;
        
        // Get image meta data
        $image = null;
        if (isset($attachment_id)) {
            $image = wp_get_attachment_image_src(absint($attachment_id), 'full');
        }
        // Get the image url
        $url = $image[0] ?? '';
        
        /**
         * If the image was not found, we bail early.
         */
        if (false === $this->options->is_image_in_media_library($post)) {
            $attachment_id = '';
            $url = '';
        }
        
        // The settings form
        $nonce = wp_create_nonce('cb_parallax_manage_options_nonce');
        echo '<div class="wrap cb-parallax-settings-shell">';
        echo '<form id="cb_parallax_settings_form" data-nonce="'.esc_attr($nonce).'" method="post" data-form="" data-postid="">';
        echo $display->get_hidden_fields_display($attachment_id, $url, $canParallax, $parallax_enabled);
        echo $display->get_form_title();
        $settings = $this->options->get_options_arguments();
        foreach ($settings as $option_key => $args) {
            $value = $stored_options[$option_key] ?? '';
            
            switch ($args['input_type']) {
                case 'checkbox':
                    if ('cb_parallax_can_parallax' === $option_key || 'cb_parallax_parallax_enabled' === $option_key) {
                        break;
                    }
                    if ('cb_parallax_boxed_layout' === $option_key) {
                        echo '<h3 id="cb_parallax_boxed_layout_settings_title" class="parallax">'.__('Boxed Layout', $this->domain).'</h3>';
                    }
                    if ('cb_parallax_allow_override' === $option_key) {
                        echo '<h3 id="cb_parallax_allow_override_settings_title" class="parallax">'.__('Individual Background Images', $this->domain).'</h3>';
                    }
                    if ('cb_parallax_disable_on_mobile' === $option_key) {
                        echo '<h3 id="cb_parallax_disable_on_mobile_settings_title" class="parallax">'.__('Disable On Mobile Devices', $this->domain).'</h3>';
                    }
                    if ('cb_parallax_parallax_enabled' === $option_key) {
                        echo $display->get_settings_title('plugin');
                    }
                    echo $display->get_checkbox_display($value, $args);
                    break;
                
                case 'color':
                    
                    echo $display->get_color_picker_field($value, $args);
                    break;
                
                case 'select':
                    
                    echo $display->get_select_field($value, $args);
                    break;
                
                case 'media':
                    
                    echo $display->get_media_button_display();
                    echo $display->get_background_image_display($url, 'plugin');
                    break;
            }
        }
        echo submit_button(esc_html__('Save', 'cb-parallax'), 'primary', 'submit', false, array(
            'id'    => 'cb_parallax_form_submit',
            'class' => 'button button-primary button-large',
        ));
        echo '<input id="cb_parallax_form_reset" type="submit" value="'.esc_attr__('Reset', 'cb-parallax').'" class="button button-secondary button-large" />';
        echo '</form>';
        echo '</div>';
    }
}
