<?php
/**
 * The AJAX functionality class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.9.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/menu/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Admin\Menu\Includes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The class responsible for the ajax functionality.
 *
 * @since      0.9.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_ajax
{

    /**
     * The domain of the plugin.
     *
     * @since  0.9.0
     * @access protected
     * @var    string $domain
     */
    protected string $domain;

    /**
     * Holds the instance responsible for handling the user options.
     *
     * @since  0.9.0
     * @access protected
     * @var    cb_parallax_options $options
     */
    protected cb_parallax_options $options;

    /**
     * Holds the error text for failed nonce checks.
     *
     * @since  0.9.0
     * @access protected
     * @var    string $nonce_error_text
     */
    protected string $nonce_error_text;

    /**
     * Constructor.
     *
     * @param string              $domain  The plugin domain.
     * @param cb_parallax_options $options The options instance.
     *
     * @since 0.9.0
     */
    public function __construct(string $domain, cb_parallax_options $options)
    {
        $this->domain = $domain;
        $this->options = $options;
        $this->nonce_error_text = esc_html__('Security check failed.', 'cb-parallax' );
    }

    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @since 0.9.0
     * @return void
     */
    public function add_hooks() : void
    {
        add_action('wp_ajax_cb_parallax_save_options', array(
            $this,
            'cb_parallax_save_options'
        ));
        add_action('wp_ajax_cb_parallax_reset_options', array(
            $this,
            'cb_parallax_reset_options'
        ));
    }
    
    /**
     * Initiates the routine to save the user-defined options.
     *
     * Verifies the nonce, sanitizes input, sends the data to the responsible class,
     * receives the result and returns the answer.
     *
     * @return void
     * @since 0.9.0
     */
    public function cb_parallax_save_options() : void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                    'success' => false,
                    'message' => esc_html__('You do not have permission to perform this action.', 'cb-parallax'),
                ), 403);
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        if (!wp_verify_nonce($nonce, 'cb_parallax_manage_options_nonce')) {
            wp_send_json_error(array(
                    'success' => false,
                    'message' => $this->nonce_error_text,
                ), 403);
        }
        
        // Retrieve and sanitize the post ID.
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        
        // Retrieve and sanitize the options.
        $data = array();
        $options_defaults = 0 === $post_id ? $this->options->get_default_options() : $this->options->get_default_options('image');
        
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $input = isset($_POST['input']) && is_array($_POST['input']) ? wp_unslash($_POST['input']) : array();
        
        foreach ($options_defaults as $key => $list) {
            if (isset($input[$key])) {
                $data[$key] = sanitize_text_field($input[$key ] );
            }
        }
        
        // Save options.
        $result = $this->options->save_options($data, $post_id);
        
        if (is_wp_error($result) ) {
            $code = $result->get_error_code();
            $msg = $result->get_error_message();
            
            // Code -1 means nothing new to save, which is still a success.
            if (-1 === $code) {
                wp_send_json_success(array(
                        'success' => true,
                        'message' => $msg,
                    )
                );
            }
            
            wp_send_json_error(array(
                    'success' => false,
                    'message' => $msg.' '.esc_html__('Please try again later.', 'cb-parallax').' ('.$code.')',
                ));
        }
        
        wp_send_json_success(array(
                'success' => true,
                'message' => esc_html__('Settings saved.', 'cb-parallax'),
            ));
    }
    
    /**
     * Initiates the routine to reset the user-defined options.
     *
     * Verifies the nonce, sends the data to the responsible class,
     * receives the result and returns the answer.
     *
     * @return void
     * @since 0.9.0
     */
    public function cb_parallax_reset_options() : void
    {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                    'success' => false,
                    'message' => esc_html__('You do not have permission to perform this action.', 'cb-parallax'),
                ), 403);
        }
        
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        
        if (!wp_verify_nonce($nonce, 'cb_parallax_manage_options_nonce')) {
            wp_send_json_error(array(
                    'success' => false,
                    'message' => $this->nonce_error_text,
                ), 403);
        }
        
        // Get the post id to determine if we're resetting plugin options or per-post options.
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        
        $result = $this->options->reset_options($post_id);
        
        if (is_wp_error($result) ) {
            $code = $result->get_error_code();
            $msg = $result->get_error_message();
            
            wp_send_json_error(array(
                    'success' => false,
                    'message' => $msg.' '.esc_html__('Please try again later.', 'cb-parallax').' ('.$code.')',
                ));
        }
        
        wp_send_json_success(array(
                'success' => true,
                'message' => esc_html__('Settings restored to default.', 'cb-parallax'),
            ));
    }
}
