<?php
/**
 * The validation class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.6.0
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
 * The class responsible for sanitizing and validating the user inputs.
 *
 * @since      0.6.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/menu/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_validation
{

    /**
     * Maintains the whitelist for the image options.
     *
     * @since  0.6.0
     * @access public
     * @var    array $image_options_whitelist
     */
    public array $image_options_whitelist;

    /**
     * Maintains the whitelist for the plugin options.
     *
     * @since  0.6.0
     * @access public
     * @var    array $plugin_options_whitelist
     */
    public array $plugin_options_whitelist;

    /**
     * Maintains the default image options.
     *
     * @since  0.1.0
     * @access public
     * @var    array $default_image_options
     */
    public array $default_image_options;

    /**
     * The domain of the plugin.
     *
     * @since  0.1.0
     * @access private
     * @var    string $domain
     */
    private string $domain;

    /**
     * The reference to the options class.
     *
     * @since  0.6.0
     * @access private
     * @var    cb_parallax_options $options
     */
    private cb_parallax_options $options;

    /**
     * Constructor.
     *
     * @param string              $domain  The plugin domain.
     * @param cb_parallax_options $options The options instance.
     *
     * @since 0.6.0
     */
    public function __construct(string $domain, cb_parallax_options $options)
    {
        $this->domain = $domain;
        $this->options = $options;

        $this->retrieve_options();
    }

    /**
     * Retrieves and sets the whitelists for the plugin and image options
     * and the default options, too.
     *
     * @return void
     * @since 0.6.0
     */
    private function retrieve_options() : void
    {
        $this->image_options_whitelist = $this->options->get_image_options_whitelist();
        $this->plugin_options_whitelist = $this->options->get_plugin_options_whitelist();
        $this->default_image_options = $this->options->get_default_image_options();
    }

    /**
     * Calls the functions to sanitize and validate the user input.
     *
     * @param string $post_id The post ID or empty string for plugin options.
     * @param array  $input   The input data to validate.
     *
     * @return array The validated and sanitized data.
     * @since 0.6.0
     */
    public function run($post_id, array $input) : array
    {
        $input = $this->sanitize($input);
        
        return $this->validate_input($post_id, $input );
    }
    
    /**
     * Sanitizes the input values using WordPress sanitization functions.
     *
     * @param array $input The input data to sanitize.
     *
     * @return array The sanitized output.
     * @since 0.6.0
     */
    private function sanitize(array $input ): array {
        $output = array();
        
        foreach ($input as $key => $value) {
            if (isset($input[$key])) {
                // Use appropriate sanitization based on the key.
                if (strpos($key, '_url') !== false) {
                    $output[$key] = esc_url_raw($value);
                } else if (strpos($key, '_color') !== false) {
                    $output[$key] = $this->sanitize_color($value);
                } else if (strpos($key, '_id') !== false) {
                    $output[$key] = absint($value);
                } else {
                    $output[$key] = sanitize_text_field($value);
                }
            }
        }
        
        return apply_filters('cb_parallax_sanitize', $output, $input );
    }
    
    /**
     * Sanitizes a color value (hex or rgba).
     *
     * @since 0.9.8
     * @param string $value The color value to sanitize.
     * @return string The sanitized color value or empty string.
     */
    private function sanitize_color(string $value) : string
    {
        // Allow empty values.
        if (empty($value)) {
            return '';
        }
        
        // Hex color pattern.
        if (preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $value)) {
            return $value;
        }
        
        // RGBA color pattern.
        if (preg_match('/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+))?\s*\)$/i', $value)) {
            return $value;
        }
        
        // HSLA color pattern.
        if (preg_match('/^hsla?\(\s*\d{1,3}\s*,\s*\d{1,3}%?\s*,\s*\d{1,3}%?\s*(,\s*(0|1|0?\.\d+))?\s*\)$/i', $value)) {
            return $value;
        }
        
        return '';
    }
    
    /**
     * Validates the user input against whitelists and defaults.
     *
     * @param string $post_id The post ID or empty string for plugin options.
     * @param array  $input   The input data to validate.
     *
     * @return array The validated output.
     * @since 0.6.0
     */
    public function validate_input($post_id, array $input) : array
    {
        if ( '' === $post_id ) {
            $defaults = $this->options->get_default_options();
        } else {
            $defaults = $this->options->get_default_image_options();
        }
        
        $options_meta = $this->options->get_options_arguments('image');
        $data = array();
        
        foreach ($input as $option_key => $value) {
            switch ($option_key) {
                case 'cb_parallax_background_color':
                case 'cb_parallax_overlay_color':
                case 'cb_parallax_boxed_layout_content_container_background_color':
                    // Already sanitized in sanitize() method.
                    $data[$option_key ] = $value;
                    break;
                
                case 'cb_parallax_background_image_url':
                    // Validate URL format.
                    if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                        $data[$option_key] = $value;
                    } else {
                        $data[$option_key] = isset($defaults[$option_key]) ? $defaults[ $option_key ] : '';
                    }
                    break;
                
                default:
                    $data[$option_key] = $value;
                    break;
            }
        }
        
        $output = array();
        foreach ($data as $option_key => $value) {
            if (isset($options_meta[$option_key]['input_type']) && 'checkbox' === $options_meta[$option_key]['input_type']) {
                $output[$option_key] = '1' === $value ? '1' : '0';
            } else if (isset($options_meta[$option_key]['input_type']) && 'select' === $options_meta[$option_key]['input_type']) {
                if (!in_array($value, $options_meta[$option_key]['select_values'], true)) {
                    $output[$option_key] = $options_meta[$option_key]['default_value'];
                } else {
                    $output[$option_key] = $value;
                }
            } else {
                $output[$option_key] = $value;
            }
        }
        
        return apply_filters('cb_parallax_validate', $output, $input );
    }
}
