<?php
/**
 * The meta box class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Admin\Includes;

use WP_Post;
use CbParallax\Admin\Partials as Partials;
use CbParallax\Admin\Menu\Includes as MenuIncludes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('CbParallax\Admin\Partials\cb_parallax_settings_display')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/partials/class-settings-display.php';
}

/**
 * Defines and displays the meta box.
 *
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_meta_box
{
    /**
     * Whether the theme has a custom backround callback for 'wp_head' output.
     *
     * @since  0.1.0
     * @access public
     * @var    bool
     */
    public bool $theme_has_callback = false;
    
    /**
     * Maintains the allowed option values for the image.
     *
     * @since  0.1.0
     * @access public
     * @var    array $allowed_image_options
     */
    public array $allowed_image_options;
    
    /**
     * Maintains the default image image_options.
     *
     * @since  0.1.0
     * @access public
     * @var    array $default_image_options
     */
    public array $default_image_options;
    
    /**
     * The domain of the plugin.
     *
     * @var string $domain
     * @since    0.1.0
     * @access   private
     */
    private string $domain;
    
    /**
     * The reference to the options class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_options $options
     */
    private MenuIncludes\cb_parallax_options $options;
    
    /**
     * The array holding the names of the supported post types.
     *
     * @since    0.7.4
     * @access   private
     * @var      array $screen_ids
     */
    private array $screen_ids;
    
    /**
     * cb_parallax_meta_box constructor.
     *
     * @param string                           $domain
     * @param array                            $screen_ids
     * @param MenuIncludes\cb_parallax_options $options
     */
    public function __construct(string $domain, array $screen_ids, MenuIncludes\cb_parallax_options $options)
    {
        $this->domain = $domain;
        $this->screen_ids = $screen_ids;
        $this->options = $options;
        
        /* If the current user can't edit custom backgrounds, bail early. */
        if (!current_user_can('cb_parallax_edit') && !current_user_can('edit_theme_options')) {
            return;
        }
        
        $this->add_hooks();
        $this->retrieve_options();
    }
    
    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function add_hooks() : void
    {
        //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        
        /* Only load on the edit post screen. */
        add_action('load-post.php', array(
            $this,
            'load_post'
        ));
        add_action('load-post-new.php', array(
            $this,
            'load_post'
        ));
        
        add_action('add_meta_boxes', array(
            $this,
            'add_meta_box'
        ));
        // Save meta data.
        add_action('save_post', array(
            $this,
            'save_post'
        ), 10, 2);
    }
    
    /**
     * Retrieves and sets the white-listed image options and the default options.
     *
     * @return void
     */
    private function retrieve_options() : void
    {
        $this->allowed_image_options = $this->options->get_image_options_whitelist();
        $this->default_image_options = $this->options->get_default_image_options();
    }
    
    /**
     * Adds the actions that add the meta box and add the callback for when the user saves a page or post.
     */
    public function load_post() : void
    {
        $screen = get_current_screen();
        
        /* If the current theme doesn't support custom backgrounds, bail. */
        if (!current_theme_supports('custom-background') || !post_type_supports($screen->post_type, 'custom-background')) {
            return;
        }
        
        /* Get the 'wp_head' callback. */
        $wp_head_callback = get_theme_support('custom-background', 'wp-head-callback');
        
        /* Checks if the theme has set up a custom callback. */
        $this->theme_has_callback = !(empty($wp_head_callback) || '_custom_background_cb' === $wp_head_callback);
        
        // Add the meta box
        add_action('add_meta_boxes', array(
            $this,
            'add_meta_box'
        ), 5);
        
        // Save meta data.
        add_action('save_post', array(
            $this,
            'save_post'
        ), 10, 2);
    }
    
    /**
     * Calls the WordPress function to add the meta box,
     * if we're on a white-listed screen.
     *
     * @param string $post_type
     *
     * @return void
     */
    public function add_meta_box(string $post_type) : void
    {
        $screen = get_current_screen();
        
        if (in_array($screen->id, $this->screen_ids)) {
            add_meta_box('cb-parallax-meta-box', __('cb Parallax', $this->domain), array(
                $this,
                'meta_box_display'
            ), $post_type, 'side', 'core');
        }
    }
    
    /**
     * Retrieves the user-defined optuons and orchestrates the functions that display the form and the settings fields.
     */
    public function meta_box_display() : void
    {
        global $post;
        
        $display = new Partials\cb_parallax_settings_display($this->domain, $this->options, $this->allowed_image_options);
        
        if (is_array(get_post_meta($post->ID, 'cb_parallax', true))) {
            $options = array_merge($this->options->get_default_image_options(), get_post_meta($post->ID, 'cb_parallax', true));
        } else {
            $options = $this->options->get_default_image_options();
        }
        $attachment_id = $options['cb_parallax_attachment_id'] ?? false;
        $canParallax = $options['cb_parallax_can_parallax'] ?? false;
        $parallax_enabled = $options['cb_parallax_can_parallax'] ?? false;
        
        // Get image meta
        $image = null;
        if (false !== $attachment_id) {
            $image = wp_get_attachment_image_src(absint($attachment_id), 'full');
        }
        // Get the image URL.
        $url = $image[0] ?? '';
        
        $nonce = wp_create_nonce('cb_parallax_manage_options_nonce');
        echo '<div id="cb_parallax_settings_form" data-nonce="'.esc_attr($nonce).'" data-form="image" data-postid="'.esc_attr($post->ID).'">';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
        echo $display->get_hidden_fields_display($attachment_id, $url, $canParallax, $parallax_enabled);
        $settings = $this->options->get_options_arguments('image');
        foreach ($settings as $option_key => $args) {
            $value = isset($options[$option_key]) ? $options[$option_key] : '';
            
            switch ($args['input_type']) {
                case 'checkbox':
                    if ('cb_parallax_can_parallax' === $option_key || 'cb_parallax_parallax_enabled' === $option_key) {
                        break;
                    }
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                    echo $display->get_checkbox_display($value, $args);
                    break;
                
                case 'color':
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                    echo $display->get_color_picker_field($value, $args);
                    break;
                
                case 'select':
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                    echo $display->get_select_field($value, $args);
                    break;
                
                case 'media':
                    if ('cb_parallax_background_image_url' === $option_key) {
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                        echo $display->get_settings_title('metabox');
                    }
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                    echo $display->get_media_button_display();
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the method
                    echo $display->get_background_image_display($url, 'image');
                    break;

                default:
                    break;
            }
        }
        echo '<input id="cb_parallax_form_submit" type="submit" value="'.esc_attr__('Save', 'cb-parallax').'" class="button button-primary button-large" />';
        echo '<input id="cb_parallax_form_reset" type="submit" value="'.esc_attr__('Reset', 'cb-parallax').'" class="button button-secondary button-large" />';
        echo '</div>';
    }
    
    /**
     * Whenever a supported post type gets saved:
     * Retrieves the plugin-related input, preprocesses it and uses the reference
     * of the options class to process and finally store the plugin-related data.
     *
     * @param int      $post_id The post ID.
     * @param \WP_Post $post    The post object.
     *
     * @return void
     *@since 0.1.0
     */
    public function save_post($post_id, WP_Post $post) : void {
        // Verify the nonce.
        if (!isset($_POST['cb_parallax_nonce'] ) ) {
            return;
        }
        
        $nonce = sanitize_text_field(wp_unslash($_POST['cb_parallax_nonce']));
        if (!wp_verify_nonce($nonce, 'cb_parallax_nonce_field')) {
            return;
        }
        
        // Get the post type object.
        $post_type = get_post_type_object($post->post_type );

        // Check if the current user has permission to edit the post.
        if (!current_user_can($post_type->cap->edit_post, $post_id ) ) {
            return;
        }

        // Don't save if the post is only a revision.
        if ('revision' === $post->post_type ) {
            return;
        }
        
        // Check if our options data is present.
        if (!isset($_POST['cb_parallax_options']) || !is_array($_POST['cb_parallax_options'])) {
            return;
        }
        
        // Sanitize the posted data.
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $posted_data = wp_unslash($_POST['cb_parallax_options']);
        
        // Map the hidden URL field.
        if (isset($posted_data['cb_parallax_background_image_url_hidden'])) {
            $posted_data['cb_parallax_background_image_url'] = esc_url_raw($posted_data['cb_parallax_background_image_url_hidden']);
            unset($posted_data['cb_parallax_background_image_url_hidden']);
        }

        $image_options_whitelist = $this->options->get_image_options_whitelist();
        $data = array();
        
        foreach ($image_options_whitelist as $key => $args) {
            if (isset($posted_data[$key])) {
                // Sanitize based on key type.
                if (strpos($key, '_url') !== false) {
                    $data[$key] = esc_url_raw($posted_data[$key]);
                } else if (strpos($key, '_id') !== false) {
                    $data[$key] = absint($posted_data[$key]);
                } else {
                    $data[$key] = sanitize_text_field($posted_data[$key]);
                }
            } else {
                $data[$key] = '';
            }
        }
        
        $this->options->save_options($data, (int) $post_id );
    }
}
