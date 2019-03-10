<?php

/**
 * Adds the checkbox on the general settings page to toggle nicescroll behaviour.
 *
 * @link              https://github.com/demispatti/cb-parallax/
 * @since             0.1.0
 * @package           cb_paraallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_parallax_general_settings {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.2.1
	 * @access   private
	 * @var      string $plugin_domain
	 */
	private $plugin_domain;

	/**
	 * The name of the meta key for accessing post meta data.
	 *
	 * @since    0.2.1
	 * @access   private
	 * @var      string $meta_key
	 */
	private $meta_key;

	/**
	 * Adds the option to the general settings page.
	 *
	 * @since    0.2.1
	 * @access   public
	 *
	 * @param    string $plugin_domain
	 * @param    string $meta_key
	 */
	public function __construct($plugin_domain, $meta_key) {

		$this->plugin_domain = $plugin_domain;
		$this->meta_key = $meta_key;
	}

	/**
	 *  Hooks the filter to "add" the option to the general settings page.
	 *
	 * @hooked_action
	 *
	 * @since  0.2.1
	 * @access public
	 * @return void
	 */
	public function add_general_options() {

		add_filter('admin_init', array(&$this, 'register_fields'));
	}

	/**
	 *  Registers the options with WordPress.
	 *
	 * @callback
	 *
	 * @since  0.2.1
	 * @access public
	 * @return void
	 */
	public function register_fields() {

		register_setting('general', $this->meta_key);

		add_settings_section(
			'cbp_settings_section',
			__('cbParallax Settings', $this->plugin_domain),
			null,
			'general'
		);

		add_settings_field(
			'preserve_scrolling',
			__('Preserve scrolling behaviour', $this->plugin_domain),
			array(&$this, 'preserve_scrolling_render'),
			'general',
			'cbp_settings_section'
		);

		add_settings_field(
			'disable_on_mobile',
			__('Disable on mobile', $this->plugin_domain),
			array(&$this, 'disable_on_mobile_render'),
			'general',
			'cbp_settings_section'
		);
	}

	/**
	 *  Renders the checkbox.
	 *
	 * @callback
	 *
	 * @since  0.2.1
	 * @access public
	 * @return void
	 */
	public function preserve_scrolling_render() {

		$value = get_option($this->meta_key);

		$html = '<p class="cbp_general_setting_container cbp-parallax-enabled-container">';
		$html .= '<p class="label_for_' . $this->meta_key . '[preserve_scrolling]"></p>';
		$html .= '<label class="cbp-switch"><input type="checkbox" id="' . $this->meta_key . '[preserve_scrolling]" class="cbp-switch-input" name="' . $this->meta_key . '[preserve_scrolling]" value="1" ' . checked(1, isset($value["preserve_scrolling"]) ? $value["preserve_scrolling"] : false, false) . '/>';
		$html .= '<span class="cbp-switch-label" data-on="On" data-off="Off"></span>';
		$html .= '<span class="cbp-switch-handle"></span>';
		$html .= '</label>';
		$html .= '</p>';

		echo $html;
	}

	/**
	 *  Renders the checkbox.
	 *
	 * @callback
	 *
	 * @since  0.2.1
	 * @access public
	 * @return void
	 */
	public function disable_on_mobile_render() {

			$value = get_option($this->meta_key);

		$html = '<p class="cbp_general_setting_container cbp-parallax-enabled-container">';
		$html .= '<p class="label_for_' . $this->meta_key . '[disable_on_mobile]"></p>';
		$html .= '<label class="cbp-switch"><input type="checkbox" id="' . $this->meta_key . '[disable_on_mobile]" class="cbp-switch-input" name="' . $this->meta_key . '[disable_on_mobile]" value="1" ' . checked(1, isset($value["disable_on_mobile"]) ? $value["disable_on_mobile"] : false, false) . '/>';
		$html .= '<span class="cbp-switch-label" data-on="On" data-off="Off"></span>';
		$html .= '<span class="cbp-switch-handle"></span>';
		$html .= '</label>';
		$html .= '</p>';

		echo $html;
	}
}
