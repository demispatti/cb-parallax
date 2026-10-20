<?php
/**
 * The settings display class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.6.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/partials
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Admin\Partials;

use CbParallax\Admin\Menu\Includes as MenuIncludes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('CbParallax\Admin\Menu\Includes\cb_parallax_validation')) {
    require_once CBPARALLAX_ROOT_DIR.'admin/menu/includes/class-validation.php';
}

/**
 * The class that deals with the settings display.
 *
 * @since      0.6.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/partials
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_settings_display
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
     * The reference to the options class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_options $options
     */
    private MenuIncludes\cb_parallax_options $options;
    
    /**
     * The array containing the allowed image_options.
     *
     * @since  0.6.0
     * @access private
     * @var    array $allowed_image_options
     */
    private array $allowed_image_options;
    
    /**
     * The array containing the default image_options.
     *
     * @since  0.6.0
     * @access private
     * @var    array $default_image_options
     */
    private array $default_image_options;
    
    /**
     * The array containing the default plugin image_options.
     *
     * @since  0.6.0
     * @access private
     * @var    array $default_plugin_options
     */
    private array $default_plugin_options;
    
    /**
     * The reference to the validation class.
     *
     * @since  0.6.0
     * @access private
     * @var    MenuIncludes\cb_parallax_validation $validation
     */
    private MenuIncludes\cb_parallax_validation $validation;
    
    /**
     * cb_parallax_admin constructor.
     *
     * @param string $domain
     * @param $options
     * @param array  $allowed_image_options
     */
    public function __construct(string $domain, $options, array $allowed_image_options)
    {
        $this->domain = $domain;
        $this->options = $options;
        $this->allowed_image_options = $allowed_image_options;
        $this->set_validation_instance();
        $this->set_default_plugin_options();
        $this->set_default_image_options();
    }
    
    /**
     * Holds the reference to the class that's responsible for validation operations.
     */
    private function set_validation_instance() : void
    {
        $this->validation = new MenuIncludes\cb_parallax_validation($this->domain, $this->options);
    }
    
    /**
     * Holds the array containing the default plugin options.
     */
    private function set_default_plugin_options() : void
    {
        $this->default_plugin_options = $this->options->get_default_plugin_options();
    }
    
    /**
     * Holds the array containing the default image options.
     */
    private function set_default_image_options() : void
    {
        $this->default_image_options = $this->options->get_default_image_options();
    }
    /**
     * Creates and returns a string containing the hidden fields:
     * - The nonce for the form
     * - The image id
     * - The image url
     *
     * @param string $attachment_id
     * @param string $url
     * @param        $canParallax
     * @param        $parallax_enabled
     *
     * @return string $html
     */
    public function get_hidden_fields_display(string $attachment_id, string $url, $canParallax, $parallax_enabled) : string
    {
        ob_start();
        wp_nonce_field('cb_parallax_nonce_field', 'cb_parallax_nonce');
        ?>
        <input type="hidden" name="cb_parallax_options[cb_parallax_parallax_enabled]"
               id="cb_parallax_options[cb_parallax_parallax_enabled]"
               class="cb_parallax_parallax_enabled"
               value="<?php echo esc_attr($parallax_enabled); ?>" data-key="cb_parallax_parallax_enabled"
               data-input="cb-parallax"/>
        <input type="hidden" name="cb_parallax_options[cb_parallax_attachment_id]"
               id="cb_parallax_options[cb_parallax_attachment_id]"
               class="cb_parallax_attachment_id"
               value="<?php echo esc_attr($attachment_id); ?>" data-key="cb_parallax_attachment_id"
               data-input="cb-parallax"/>
        <input type="hidden" name="cb_parallax_options[cb_parallax_background_image_url_hidden]"
               id="cb_parallax_options[cb_parallax_background_image_url_hidden]"
               class="cb_parallax_background_image_url_hidden"
               value="<?php echo esc_url($url); ?>" data-key="cb_parallax_background_image_url"
               data-input="cb-parallax"/>
        <input type="hidden" name="cb_parallax_options[cb_parallax_can_parallax]"
               id="cb_parallax_options[cb_parallax_can_parallax]"
               class="cb_parallax_can_parallax"
               value="<?php echo esc_attr($canParallax); ?>" data-key="cb_parallax_can_parallax"
               data-input="cb-parallax"/>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Creates and returns a string containing a button.
     *
     * @return string $html
     */
    public function get_media_button_display() : string
    {
        ob_start();
        ?>
        <div id="cb_parallax_remove_media_button" class="cb-parallax-remove-media-button-container">
            <a class="cb-parallax-remove-media" href="#"><i class="dashicons dashicons-no-alt"
                                                            aria-hidden="true"></i></a>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        
        return $html;
    }
    
    /**
     * Creates and returns a settings field containing the image input field and the image placeholder.
     *
     * @param string $url
     * @param string $section
     *
     * @return string $html
     */
    public function get_background_image_display(string $url, string $section = 'image') : string
    {
        $placeholder_image_class = '' === $url ? '' : 'hidden';
        $postfix = 'plugin' === $section ? '' : '-small';
        $img_postfix = 'de_DE' === get_locale() ? 'de' : 'default';
        $placeholder_image_url = CBPARALLAX_ROOT_URL.'admin/menu/images/placeholder'.$postfix.'-'.$img_postfix.'.png';

        ob_start();
        ?>
        <div class="cb-parallax-image-container">
            <a href="#" class="cb-parallax-media-url">
                <img id="cb_parallax_options[cb_parallax_background_image_url]" class="cb_parallax_background_image"
                     alt=""
                     src="<?php echo esc_url($url); ?>" data-key="cb_parallax_background_image_url"
                     data-input="cb-parallax"/>
                <img id="cb_parallax_placeholder_image"
                     class="cb_parallax_placeholder_image <?php echo esc_attr($placeholder_image_class); ?>" alt=""
                     src="<?php echo esc_url($placeholder_image_url); ?>" data-key="cb_parallax_placeholder_image" data-input=""
                    <?php if ('' !== $url) : ?> hidden<?php endif; ?>/>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Creates and returns a settings field containing a checkbox and it's label.
     *
     * @param string $value
     * @param array  $args
     *
     * @return string $html
     */
    public function get_checkbox_display($value, array $args) : string
    {
        $option_key = $args['option_key'];
        $name = $args['name'];
        $description = $args['description'];
        $value = isset($value) && '1' === $value ? '1' : '0';

        ob_start();
        ?>
        <div class="cb-parallax-single-option-container" id="<?php echo esc_attr($option_key); ?>_container"
             title="<?php echo esc_attr($description); ?>">
            <div>
                <label for="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"
                       class="label-for-cb-parallax-switch"><?php echo esc_html($name); ?></label>

                <label class="cb-parallax-switch">
                    <input type="checkbox" id="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"
                           class="cb-parallax-switch-input cb-parallax-input-checkbox <?php echo esc_attr($option_key); ?>"
                           name="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"
                           value="<?php echo esc_attr($value); ?>"
                            <?php checked(1, $value, true); ?> data-key="<?php echo esc_attr($option_key); ?>"
                           data-input="cb-parallax"/>
                    <span class="cb-parallax-switch-label <?php echo esc_attr($option_key); ?>" data-on="On" data-off="Off"></span>
                    <span class="cb-parallax-switch-handle"></span>
                </label>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Creates and returns a settings field containing a color picker and it's label.
     *
     * @param string $value
     * @param array  $args
     *
     * @return string $html
     */
    public function get_color_picker_field($value, array $args) : string
    {
        $option_key = $args['option_key'];
        $description = $args['description'];
        $placeholder = $this->default_image_options[$option_key] ?? ($this->default_plugin_options[$option_key] ?? '');

        ob_start();
        ?>
        <div class="cb-parallax-single-option-container" id="<?php echo esc_attr($option_key); ?>_container"
             title="<?php echo esc_attr($description); ?>">
            <p class="cb-parallax-input-container">
                <label for="<?php echo esc_attr($option_key); ?>"><?php echo esc_html($args['name']); ?></label>
                <input type="text" id="<?php echo esc_attr($option_key); ?>" title="<?php echo esc_attr($description); ?>"
                       name="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"
                       placeholder="<?php echo esc_attr($placeholder); ?>"
                       value="<?php echo esc_attr($value); ?>"
                       class="<?php echo esc_attr($option_key); ?> cb-parallax-color-picker cb-parallax-input-color-picker"
                       data-alpha="true" data-key="<?php echo esc_attr($option_key); ?>"
                       data-input="color-picker"/>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Creates and returns a settings field containing a drop down box and it's label.
     *
     * @param string $value
     * @param array  $args
     *
     * @return string $html
     */
    public function get_select_field($value, array $args) : string
    {
        $option_key = $args['option_key'];
        $description = $args['description'];
        $select_values = $args['select_values'];

        ob_start();
        ?>
        <div class="cb-parallax-single-option-container" id="<?php echo esc_attr($option_key); ?>_container">
            <p class="cb-parallax-input-container" title="<?php echo esc_attr($description); ?>">
                <label for="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"><?php echo esc_html($args['name']); ?></label>
                <select name="cb_parallax_options[<?php echo esc_attr($option_key); ?>]"
                        class="floating-element fancy-select cb-parallax-fancy-select cb-parallax-input-select"
                        data-key="<?php echo esc_attr($option_key); ?>"
                        data-input="cb-parallax" id="cb_parallax_options[<?php echo esc_attr($option_key); ?>]">
                    <?php foreach ($select_values as $select_value) : ?>
                        <option value="<?php echo esc_attr($select_value); ?>" <?php selected($value, $select_value); ?>><?php echo esc_html(translate($select_value, 'cb-parallax')); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Returns a string containing the form title.
     *
     * @return string
     */
    public function get_form_title() : string
    {
        ob_start();
        ?>
        <header class="cb-parallax-page-heading">
            <span class="cb-parallax-page-mark" aria-hidden="true"><span></span></span>
            <div>
                <p class="cb-parallax-page-eyebrow"><?php esc_html_e('Background atmosphere', 'cb-parallax'); ?></p>
                <h1><?php esc_html_e('cbParallax Settings', 'cb-parallax'); ?></h1>
                <p class="cb-parallax-page-description"><?php esc_html_e('Create depth and gentle movement without losing focus on your content.', 'cb-parallax'); ?></p>
            </div>
        </header>
        <?php
        return ob_get_clean();
    }

    /**
     * Configures the visibility of the titles and returns them.
     *
     * @param string $section The section identifier.
     *
     * @return string The HTML output for titles.
     */
    public function get_settings_title(string $section) : string
    {
        if ('meta-box' === $section || 'metabox' === $section ) {
            $parallax_class = 'hidden';
            $static_class = '';
        } else {
            $parallax_class = '';
            $static_class = 'hidden';
        }

        $title_parallax = '<h3 class="parallax '.esc_attr($parallax_class).'">'.esc_html__('Parallax Image Settings', 'cb-parallax').'</h3>';
        $title_static = '<h3 class="static '.esc_attr($static_class).'">'.esc_html__('Static Image Settings', 'cb-parallax').'</h3>';

        return $title_parallax.$title_static;
    }
}
