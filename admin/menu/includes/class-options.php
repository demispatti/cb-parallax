<?php
/**
 * The options class.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.6.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/menu/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Admin\Menu\Includes;

use WP_Post;
use WP_Error;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The class that maintains the default options and the related meta data.
 *
 * @since      0.6.0
 * @package    CbParallax
 * @subpackage CbParallax/admin/menu/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_options
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
    public mixed $plugin_options_whitelist;
    
    /**
     * The domain of the plugin.
     *
     * @var string $domain
     * @since    0.1.0
     * @access   private
     */
    private string $domain;
    
    /**
     * cb_parallax_admin constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
        $this->image_options_whitelist = $this->image_options_whitelist();
        $this->plugin_options_whitelist = $this->plugin_options_whitelist();
    }
    
    /**
     * Holds all whitelisted plugin options.
     *
     * @return mixed
     */
    private function plugin_options_whitelist() : mixed
    {
        $plugin_options['cb_parallax_allow_override'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        $plugin_options['cb_parallax_boxed_layout'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        $plugin_options['cb_parallax_uses_assumed_background_color'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        $plugin_options['cb_parallax_boxed_layout_margin'] = array(
            '50 50'   => 'horizontal 50px vertical 50px',
            '100 50'  => 'horizontal 100px vertical 50px',
            '100 100' => 'horizontal 100px vertical 100px',
            '150 100' => 'horizontal 150px vertical 100px',
            '150 150' => 'horizontal 150px vertical 150px',
            '200 150' => 'horizontal 200px vertical 150px',
            '200 200' => 'horizontal 200px vertical 200px',
            '250 200' => 'horizontal 250px vertical 200px',
            '250 250' => 'horizontal 250px vertical 250px',
            '300 250' => 'horizontal 300px vertical 250px',
            '300 300' => 'horizontal 300px vertical 300px',
            '350 300' => 'horizontal 350px vertical 300px',
            '350 250' => 'horizontal 350px vertical 350px',
            '400 350' => 'horizontal 400px vertical 350px',
            '400 400' => 'horizontal 400px vertical 400px'
        );
        
        $plugin_options['cb_parallax_boxed_layout_content_container_background_color'] = array();
        
        $plugin_options['cb_parallax_preserve_scrolling'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        $plugin_options['cb_parallax_disable_on_mobile'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        return $plugin_options;
    }
    /**
     * @return array
     */
    private function image_options_whitelist() : array
    {
        $image_options_whitelist['cb_parallax_attachment_id'] = array();
        
        $image_options_whitelist['cb_parallax_background_image_url'] = array();
        
        $image_options_whitelist['cb_parallax_parallax_enabled'] = array(
            'off' => '0',
            'on'  => '1',
        );
        
        // Image image_options for a static background image.
        $image_options_whitelist['cb_parallax_position_x'] = array(
            'left'   => __('left', "cb-parallax"),
            'center' => __('center', "cb-parallax"),
            'right'  => __('right', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_position_y'] = array(
            'top'    => __('top', "cb-parallax"),
            'center' => __('center', "cb-parallax"),
            'bottom' => __('bottom', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_background_attachment'] = array(
            'fixed'  => __('fixed', "cb-parallax"),
            'scroll' => __('scroll', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_direction'] = array(
            'vertical'   => __('vertical', "cb-parallax"),
            'horizontal' => __('horizontal', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_vertical_scroll_direction'] = array(
            'top'    => __('to top', "cb-parallax"),
            'bottom' => __('to bottom', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_horizontal_scroll_direction'] = array(
            'left'  => __('to the left', "cb-parallax"),
            'right' => __('to the right', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_horizontal_alignment'] = array(
            'left'   => __('left', "cb-parallax"),
            'center' => __('center', "cb-parallax"),
            'right'  => __('right', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_vertical_alignment'] = array(
            'top'    => __('top', "cb-parallax"),
            'center' => __('center', "cb-parallax"),
            'bottom' => __('bottom', "cb-parallax"),
        );
        
        $image_options_whitelist['cb_parallax_overlay_image'] = array_merge($this->convert_overlay_image_names_for_display($this->retrieve_overlay_image_names()), array('none' => __('none', "cb-parallax")));
        
        $image_options_whitelist['cb_parallax_overlay_opacity'] = array(
            '0.1' => '0.1',
            '0.2' => '0.2',
            '0.3' => '0.3',
            '0.4' => '0.4',
            '0.5' => '0.5 (default)',
            '0.6' => '0.6',
            '0.7' => '0.7',
            '0.8' => '0.8',
            '0.9' => '0.9',
            '0.0' => __('none', "cb-parallax")
        );
        
        $image_options_whitelist['cb_parallax_overlay_color'] = '';
        
        return $image_options_whitelist;
    }
    
    /**
     * Converts the overlay image file names into 'human readable' ones.
     *
     * @param array $input
     *
     * @return array $output
     */
    private function convert_overlay_image_names_for_display(array $input) : array
    {
        $output = array();
        
        foreach ($input as $value) {
            // Remove the file extension
            $name = preg_replace(array(
                '/.png/',
                '/-/'
            ), array(
                '',
                ' '
            ), $value);
            if (preg_match('/\s/', $name)) {
                // Remove whitespace and capitalize.
                $name = implode(' ', array_map('ucfirst', explode(' ', $name)));
                $output[$value] = $name;
            } else {
                $output[$value] = ucfirst($name);
            }
        }
        
        return $output;
    }
    
    /**
     * Retrieves and puts the file names of the overlay images into an array and returns it.
     *
     * @return mixed | bool false | array $names_list
     */
    private function retrieve_overlay_image_names() : mixed
    {
        $path = CBPARALLAX_ROOT_DIR.'public/images/overlays/';
        $excluded = array(
            '.',
            '..',
            '.DS_Store'
        );
        $names_list = array();
        
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, $excluded)) {
                    $names_list[] = wp_strip_all_tags($entry);
                }
            }
            closedir($handle);
            
            return $names_list;
        } else {
            return false;
        }
    }
    
    /**
     * Returns an array containing the whitelisted image options.
     *
     * @return array|mixed
     */
    public function get_image_options_whitelist() : mixed
    {
        return $this->image_options_whitelist;
    }
    
    /**
     * Returns an array containing the whitelisted plugin options.
     *
     * @return array|mixed
     */
    public function get_plugin_options_whitelist() : mixed
    {
        return $this->plugin_options_whitelist;
    }
    
    /**
     * Returns an array containing the option arguments per section or all of them.
     *
     * @param string $section
     *
     * @return array $args
     */
    public function get_options_arguments(string $section = '') : array
    {
        if ('plugin' === $section) {
            $options = $this->plugin_options();
        } else {
            if ('image' === $section) {
                $options = $this->image_options();
            } else {
                $options = array_merge($this->image_options(), $this->plugin_options());
            }
        }
        
        $args = array_map(function($arguments)
        {
            return array(
                'option_key'     => $arguments['option_key'],
                'name'           => $arguments['name'],
                'settings_group' => $arguments['settings_group'],
                'description'    => $arguments['description'],
                'input_type'     => $arguments['input_type'],
                'default_value'  => $arguments['default_value'],
                'select_values'  => $arguments['select_values'],
            );
        }, $options);
        
        return $args;
    }
    
    /**
     * Holds the plugin options and their arguments.
     *
     * @return array
     */
    private function plugin_options() : array
    {
        return array_merge(array(
            'cb_parallax_boxed_layout'                                    => array(
                'option_key'     => 'cb_parallax_boxed_layout',
                'name'           => __('Boxed Layout', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Sets a boxed layout with the following margin around the body html tag.', "cb-parallax"),
                'default_value'  => '1',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_boxed_layout_margin'                             => array(
                'option_key'     => 'cb_parallax_boxed_layout_margin',
                'name'           => __('Margin In Pixels', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'plugin',
                'description'    => __('The margin for the body tag.', "cb-parallax"),
                'default_value' => $this->plugin_options_whitelist['cb_parallax_boxed_layout_margin']['200 150'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->plugin_options_whitelist['cb_parallax_boxed_layout_margin'],
            ),
            'cb_parallax_boxed_layout_content_container_background_color' => array(
                'option_key'     => 'cb_parallax_boxed_layout_content_container_background_color',
                'name'           => __('Boxed Layout Background Color', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'plugin',
                'description'    => __('Set a background color for the boxed Layout.', "cb-parallax"),
                'default_value' => '#ffffff',
                'input_type'     => 'color',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_uses_assumed_background_color'                   => array(
                'option_key'     => 'cb_parallax_uses_assumed_background_color',
                'name'           => __('Assume Background Color', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'plugin',
                'description'    => __('Assumes a background image for the content container and applies it.', "cb-parallax"),
                'default_value'  => '1',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_allow_override'                                  => array(
                'option_key'     => 'cb_parallax_allow_override',
                'name'           => __('Enable Individual Background Images For Pages And Posts', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'plugin',
                'description'    => __('Individual settings on a per post -/ per page basis will be used instead of this image and it\'s settings. On pages where no background image is defined, the main background image will be displayed, if one is defined.', "cb-parallax"),
                'default_value'  => '0',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_disable_on_mobile'                               => array(
                'option_key'     => 'cb_parallax_disable_on_mobile',
                'name'           => __('Disable Parallax Effect On Mobile Devices', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'plugin',
                'description'    => __('Disable parallax-effect on mobile devices. This may be useful <i>if</i> you encounter performance issues.', "cb-parallax"),
                'default_value'  => '1',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            )
        ), $this->image_options());
    }
    
    /**
     * Holds the image options and their arguments.
     *
     * @return array
     */
    private function image_options() : array
    {
        return array(
            'cb_parallax_parallax_enabled'            => array(
                'option_key'     => 'cb_parallax_parallax_enabled',
                'name'           => __('Enable Parallax Features', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Enable parallax', "cb-parallax"),
                'default_value'  => '0',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_attachment_id'               => array(
                'option_key'     => 'cb_parallax_attachment_id',
                'name'           => __('Attachment ID', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => '',
                'default_value'  => '',
                'input_type'     => 'none',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_background_image_url'        => array(
                'option_key'     => 'cb_parallax_background_image_url',
                'name'           => __('Background Image', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => '',
                'default_value'  => '',
                'input_type'     => 'media',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
            'cb_parallax_overlay_color'               => array(
                'option_key'     => 'cb_parallax_overlay_color',
                'name'           => __('Overlay Color', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Select overlay color.', "cb-parallax"),
                'default_value'  => '',
                'input_type'     => 'color',
                'notice_level'   => 'none',
                'select_values'  => 'none'
            ),
            'cb_parallax_position_x'                  => array(
                'option_key'     => 'cb_parallax_position_x',
                'name'           => __('Horizontal Position', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Set the horizontal position of the background image.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_position_x']['center'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_position_x'],
            ),
            'cb_parallax_position_y'                  => array(
                'option_key'     => 'cb_parallax_position_y',
                'name'           => __('Vertical Position', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Set the vertical position of the background image.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_position_y']['center'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_position_y'],
            ),
            'cb_parallax_background_attachment'       => array(
                'option_key'     => 'cb_parallax_background_attachment',
                'name'           => __('Background Attachment', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Set the attachment.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_background_attachment']['fixed'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_background_attachment'],
            ),
            'cb_parallax_direction'                   => array(
                'option_key'     => 'cb_parallax_direction',
                'name'           => __('Image Moving Direction', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Choose horizontal or vertical parallax direction.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_direction']['vertical'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_direction'],
            ),
            'cb_parallax_vertical_scroll_direction'   => array(
                'option_key'     => 'cb_parallax_vertical_scroll_direction',
                'name'           => __('While Scrolling Down The Background Image Moves', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Choose whether the image should move up or downwards on page scroll.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_vertical_scroll_direction']['top'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_vertical_scroll_direction'],
            ),
            'cb_parallax_horizontal_scroll_direction' => array(
                'option_key'     => 'cb_parallax_horizontal_scroll_direction',
                'name'           => __('While Scrolling Down The Background Image Moves', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Choose whether the image should move to the left or to the right on page scroll.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_horizontal_scroll_direction']['left'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_horizontal_scroll_direction']
            ),
            'cb_parallax_horizontal_alignment'        => array(
                'option_key'     => 'cb_parallax_horizontal_alignment',
                'name'           => __('Horizontal Alignment', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Align the image horizontally.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_horizontal_alignment']['center'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_horizontal_alignment'],
            ),
            'cb_parallax_vertical_alignment'          => array(
                'option_key'     => 'cb_parallax_vertical_alignment',
                'name'           => __('Vertical Alignment', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Align the image vertically.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_vertical_alignment']['center'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_vertical_alignment']
            ),
            'cb_parallax_overlay_image'               => array(
                'option_key'     => 'cb_parallax_overlay_image',
                'name'           => __('Overlay Image', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Select an overlay image if you like.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_overlay_image']['none'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_overlay_image']
            ),
            'cb_parallax_overlay_opacity'             => array(
                'option_key'     => 'cb_parallax_overlay_opacity',
                'name'           => __('Overlay Opacity', "cb-parallax"),
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => __('Set overlay opacity.', "cb-parallax"),
                'default_value'  => $this->image_options_whitelist['cb_parallax_overlay_opacity']['0.4'],
                'input_type'     => 'select',
                'notice_level'   => 'none',
                'select_values'  => $this->image_options_whitelist['cb_parallax_overlay_opacity']
            ),
            'cb_parallax_can_parallax'                => array(
                'option_key'     => 'cb_parallax_can_parallax',
                'name'           => '',
                'callback'       => 'render_settings_field_callback',
                'settings_group' => 'background-image',
                'description'    => '',
                'default_value'  => '0',
                'input_type'     => 'checkbox',
                'notice_level'   => 'none',
                'select_values'  => 'none',
            ),
        );
    }
    
    /**
     * Returns the section heading that will be displayed in the settings form.
     *
     * @param string $section
     *
     * @return array|bool
     */
    public function get_section_heading(string $section) : bool|array
    {
        $background_image_heading = array(
            'option_key'     => 'cb_parallax_image_options',
            'name'           => __('Background Image', "cb-parallax"),
            'settings_group' => 'background-image',
            'description'    => __('Customize the background image image_options.', "cb-parallax"),
            'callback'       => 'background_image_settings_section_callback',
            'class'          => 'icon icon-equalizer',
        );
        
        $plugin_heading = array(
            'option_key'     => 'cb_parallax_parallax_options',
            'name'           => __('General Settings', "cb-parallax"),
            'settings_group' => 'plugin',
            'description'    => __('General settings.', "cb-parallax"),
            'callback'       => 'plugin_settings_section_callback',
            'class'          => 'icon icon-equalizer',
        );
        
        switch ($section) {
            case 'background-image':
                $heading = $background_image_heading;
                break;
            
            case 'plugin':
                $heading = $plugin_heading;
                break;
            default:
                return false;
        }
        
        return $heading;
    }
    
    /**
     * Retrieves the user-defined options.
     *
     * @return array|bool|mixed
     */
    /*public function fetch_options()
    {
        global $post;
        $options = false;
        
        // Determines if we get the image data from the post meta or from the image_options array.
        $source_type = $this->determine_image_source();
        
        if ($source_type === 'per_post') {
            $page_for_posts = get_option('page_for_posts');
            
            if (false == (!is_front_page() && is_home())) {
                $options = get_post_meta($post->ID, 'cb_parallax', true);
            } else {
                if (false != $page_for_posts) {
                    $options = get_post_meta($page_for_posts, 'cb_parallax', true);
                } else {
                    if (isset($post)) {
                        $options = get_post_meta($post->ID, 'cb_parallax', true);
                    }
                }
            }
            
            if (is_array($options)) {
                $options = array_merge($this->get_default_image_options(), $options);
            }
            
            return $options;
        }
        
        return get_option('cb_parallax_options');
    }*/
    
    /**
     * Determines where to fetch the image from.
     *
     * - First we need to check if the settings will be applied 'global'.
     * - Then, since there is a plugin-related option to override the settings on a per post-basis,
     *   we need to check that setting too.
     * - At last, we check if the requested image exists and can be served
     *
     * @return string $result
     */
    public function determine_image_source() : string
    {
        global $post;
        
        $options = get_option('cb_parallax_options');
        $parallax_enabled = '1' === $options['cb_parallax_parallax_enabled'];
        $allow_override = '1' === $options['cb_parallax_allow_override'];
        $is_post = is_a($post, 'WP_Post');
        $post_meta = $is_post ? get_post_meta($post->ID, 'cb_parallax', true) : false;
        
        $source_type = 'none';
        if ($parallax_enabled) {
            $source_type = 'global';
        }
        if($allow_override && $is_post && '' !== $post_meta['cb_parallax_attachment_id']) {
            $source_type = 'per_post';
        }

        return $source_type;
    }
    
    /**
     * Returns an array containing the default image options as key/value pairs.
     *
     * @return array $default_image_options
     */
    public function get_default_image_options() : array
    {
        $default_image_options = array();
        
        $image_options = $this->image_options();
        
        foreach ($image_options as $option_key => $args) {
            $default_image_options[$option_key] = $args['default_value'];
        }
        
        return $default_image_options;
    }
    
    /**
     * Returns the plugin options based on the plugin settings or the post meta data respectively.
     *
     * @return array
     */
    public function get_plugin_options() : array
    {
        global $post;
        
        $plugin_options = array();
        $stored_options = get_option('cb_parallax_options');
        $plugin_options_keys = $this->get_all_option_keys('');
        $plugin_options_keys[] = 'cb_parallax_parallax_enabled';
        
        // If a page or post is requested AND the options are fetched on a per post basis
        if (is_a($post, '\WP_Post') && 'per_page' === $this->determine_options_source($post)) {
            $post_meta = get_post_meta($post->ID, 'cb_parallax', true);
            // Maybe overwrite the value with the one from the post meta data
            $stored_options['cb_parallax_parallax_enabled'] = $post_meta['cb_parallax_parallax_enabled'] ?? $stored_options['cb_parallax_parallax_enabled'];
        }
        
        foreach ($plugin_options_keys as $key) {
            $plugin_options[$key] = $stored_options[$key] ?? '0';
        }
        
        return $plugin_options;
    }
    
    /**
     * Returns an array containing the option keys per section or all of them.
     *
     * @param string $section
     *
     * @return array $keys
     */
    public function get_all_option_keys(string $section = '') : array
    {
        if ('plugin' === $section) {
            $option_keys = $this->get_default_plugin_options();
        } else {
            if ('image' === $section) {
                $option_keys = $this->get_default_image_options();
            } else {
                $option_keys = array_merge($this->get_default_image_options(), $this->get_default_plugin_options());
            }
        }
        
        $keys = array();
        
        foreach ($option_keys as $name => $option_key) {
            $keys[] = $name;
        }
        
        return $keys;
    }
    
    /**
     * Returns an array containing the default plugin options as key/value pairs.
     *
     * @return array $default_plugin_options
     */
    public function get_default_plugin_options() : array
    {
        $default_plugin_options = array();
        
        $options = $this->plugin_options();
        
        foreach ($options as $option_key => $args) {
            $default_plugin_options[$option_key] = $args['default_value'];
        }
        
        return $default_plugin_options;
    }
    
    /**
     * Determines where to fetch the options from.
     *
     * - First we need to check if the settings will be applied 'global'.
     * - Then, since there is a plugin-related option to override the settings on a per post-basis,
     *   we need to check that setting too.
     * - At last, we check if the requested image exists and can be served
     *
     * @param \WP_Post $post
     *
     * @return string $result
     */
    public function determine_options_source(WP_Post $post) : string
    {
        $stored_options = get_option('cb_parallax_options');
        $is_sidewide = '1' === $stored_options['cb_parallax_parallax_enabled'] && '1' === $stored_options['cb_parallax_can_parallax'];
        $allow_override = '1' === $stored_options['cb_parallax_allow_override'];
        $is_post = is_a($post, 'WP_Post');
        $has_image_url_in_post_meta = false;
        $post_meta = $is_post ? get_post_meta($post->ID, 'cb_parallax', true) : false;
        if ($is_post && $allow_override && false !== $post_meta && '' !== $post_meta['cb_parallax_background_image_url']) {
            $has_image_url_in_post_meta = true;
        }
        $has_image_url_in_options = '' !== $stored_options['cb_parallax_background_image_url'];
        $can_post_parallax = isset($post_meta['cb_parallax_can_parallax']) && '0' !== $post_meta['cb_parallax_can_parallax'];
        // Determine image source
        $result = 'none';
        if(true === $is_sidewide) {
            $result = 'global';
        }
        if ($allow_override && $has_image_url_in_post_meta && $can_post_parallax) {
            $result = 'per_page';
        }
        if ($is_sidewide && $allow_override && !$has_image_url_in_post_meta && $has_image_url_in_options) {
            $result = 'global';
        }
        
        return $result;
    }
    
    /**
     * Saves the user-defined options to the database.
     *
     * @param string $post_id
     * @param array  $input
     *
     * @return bool|\WP_Error
     */
    public function save_options(array $input, $post_id = '') : WP_Error|bool
    {
        $validation = new cb_parallax_validation($this->domain, $this);

        $data = $validation->run($post_id, $input);
        
        // Checks if the options are on a per post basis or plugin-related.
        if ('' === $post_id) {
            $result = update_option('cb_parallax_options', $data, true);
        } else {
            delete_post_meta((int) $post_id, 'cb_parallax');
            $result = add_post_meta((int) $post_id, 'cb_parallax', $data, false);
        }
        
        /**
         * If the result is false,
         * the cause could be that we tried to store identical, already stored options.
         * So we check if that is the case and maybe turn the 'false' into a more detailed answer.
         */
        if (false === $result) {
            if ('' === $post_id) {
                $stored_options = get_option('cb_parallax_options');
            } else {
                $stored_options = get_post_meta((int) $post_id, 'cb_parallax', true);
            }
            $diff = array_diff($data, $stored_options);
            if (empty($diff)) {
                return new WP_Error(-1, __('There\'s nothing new to save.', "cb-parallax"));
            }
            
            return new WP_Error(-2, __('Failed to save settings.', "cb-parallax"));
        }
        
        return true;
    }
    
    /**
     * Restores the plugin settings to it's default values.
     *
     * @param $post_id
     *
     * @return  \WP_Error | bool true
     */
    public function reset_options($post_id) : WP_Error|bool
    {
        delete_option('cb_parallax_options');
        
        // Checks if the options are post-related or plugin-related.
        if ('' === $post_id) {
            $result = add_option('cb_parallax_options', $this->get_default_options());
        } else {
            delete_post_meta((int) $post_id, 'cb_parallax');
            $result = add_post_meta((int) $post_id, 'cb_parallax', $this->get_default_image_options(), false);
        }
        
        if (false === $result) {
            return new WP_Error(-3, __('Failed to reset settings. Please refresh the page and try again.', "cb-parallax"));
        }
        
        return true;
    }
    
    /**
     * Returns an array containing the default options per section or all of them.
     *
     * @param string $section
     *
     * @return array
     */
    public function get_default_options(string $section = '') : array
    {
        if ('image' == $section) {
            return $this->get_default_image_options();
        }
        
        return array_merge($this->get_default_plugin_options(), $this->get_default_image_options());
    }
    
    /**
     * @toBeImplemented
     *
     * Resets all plugin-related meta data on posts and pages.
     *
     * @return bool
     */
    /*public function reset_all_post_meta()
    {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => array(
                'page',
                'post',
                'product'
            ),
            'meta_key' => 'cb_parallax'
        );
        $posts = new WP_Query($args);
        
        foreach ($posts as $i => $post) {
            delete_post_meta($post->ID, 'cb_parallax');
        }
        
        return true;
    }*/
    
    /**
     * Looks for the referred media file and compares it by file name aditionally,
     * to make sure we are dealing with the correct file.
     * Since the ids for attachments will most likely be assigned in a different order after website migrations,
     * we check the file by it's file name, too. We rather serve no file instead of a wrong one.
     *
     * @return bool
     */
    public function is_image_in_media_library() : bool
    {
        $stored_image_options = $this->get_image_options();
        $attachment_id = $stored_image_options['cb_parallax_attachment_id'];
        $image_attributes = wp_get_attachment_image_src($attachment_id, 'full');
        $attachment_meta = wp_get_attachment_metadata($attachment_id, false);
        
        // If no image was found...
        // The user will be informed about that in the admin area. // @todo
        if (false === $attachment_meta) {
            set_transient('cb_parallax_background_image_missing', array(
                'attachment_id' => $attachment_id,
                'url'           => $stored_image_options['cb_parallax_background_image_url']
            ));
            
            return false;
        } else {
            $stored_file = $attachment_meta['file'] ?? 'false';
            $found_file = str_replace(site_url().'/wp-content/uploads/', '', $image_attributes[0]);
        }
        
        /**
         * If the stored file does not match the given file that is related to the stored 'attachment id'...
         * The user will be informed about that in the admin area.
         */
        if ($stored_file !== $found_file) {
            set_transient('cb_parallax_background_image_missmatch', array(
                'stored_file' => $stored_file,
                'found_file'  => $found_file
            ));
            
            return false;
        }
        
        return true;
    }
    /**
     * Returns the image options based on the plugin settings or the post meta data respectively.
     *
     * @return array $image_options
     */
    public function get_image_options() : array
    {
        global $post;
        
        $options = get_option('cb_parallax_options');
        $parallax_enabled = '1' === $options['cb_parallax_parallax_enabled'];
        $allow_override = '1' === $options['cb_parallax_allow_override'];
        $is_post = is_a($post, 'WP_Post');
        $post_meta = $is_post ? get_post_meta($post->ID, 'cb_parallax', true) : false;
        
        $source_type = 'none';
        if ($parallax_enabled) {
            $source_type = 'global';
        }
        if ($allow_override && $is_post && '' !== $post_meta['cb_parallax_attachment_id']) {
            $source_type = 'per_post';
        }
        
        if ('per_post' === $source_type && false !== $post_meta) {
            $options = array_merge($options, $post_meta);
        }
        
        return $options;
    }
}
