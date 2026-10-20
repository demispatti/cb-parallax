<?php
/**
 * The plugin bootstrap file.
 *
 * This is a plugin for custom backgrounds on single posts, products and pages,
 * with a parallax effect. The parallax effect requires an image with a width
 * of at least 1920px and a height of at least 1200px for vertical parallax effect.
 *
 * @link              https://wordpress.org/plugins/cb-parallax/
 * @since             0.1.0
 * @package           CbParallax
 *
 * @wordpress-plugin
 * Plugin Name:       cbParallax
 * Plugin URI:        https://wordpress.org/plugins/cb-parallax/
 * Description:       Lets you add custom background images with or without vertical or horizontal parallax effect for single posts, pages and products.
 * Version:           1.1.1
 * Requires at least: 6.7
 * Tested up to:      7.1
 * Requires PHP:      8.0
 * Author:            Demis Patti
 * Author URI:        https://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cb-parallax
 * Domain Path:       /languages
 */

namespace CbParallax;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
if (!defined('CBPARALLAX_VERSION')) {
    define('CBPARALLAX_VERSION', '1.1.1');
}

/**
 * Define the constants for the plugin paths.
 */
if (!defined('CBPARALLAX_PLUGINS_ROOT_DIR')) {
    if ('src' === basename(__DIR__)) {
        define('CBPARALLAX_PLUGINS_ROOT_DIR', plugin_dir_path(__FILE__).'../../');
    } else {
        define('CBPARALLAX_PLUGINS_ROOT_DIR', plugin_dir_path(__FILE__).'../');
    }
}

if (!defined('CBPARALLAX_ROOT_URL')) {
    define('CBPARALLAX_ROOT_URL', plugin_dir_url(__FILE__));
}

if (!defined('CBPARALLAX_ROOT_DIR')) {
    define('CBPARALLAX_ROOT_DIR', plugin_dir_path(__FILE__));
}

/**
 * Require the initial classes.
 */
if (!class_exists('CbParallax\Includes\cb_parallax_activator')) {
    require_once CBPARALLAX_ROOT_DIR.'includes/class-activator.php';
}

if (!class_exists('CbParallax\Includes\cb_parallax_deactivator')) {
    require_once CBPARALLAX_ROOT_DIR.'includes/class-deactivator.php';
}

if (!class_exists('CbParallax\Includes\cb_parallax')) {
    require_once CBPARALLAX_ROOT_DIR.'includes/class-cb-parallax.php';
}

/**
 * The code that runs during plugin activation.
 *
 * @return void
 * @since 0.1.0
 */
function activate_plugin() : void
{
    Includes\cb_parallax_activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @return void
 * @since 0.1.0
 */
function deactivate_plugin() : void
{
    Includes\cb_parallax_deactivator::deactivate();
}

/**
 * Register the activation and deactivation functionality of the plugin.
 */
$plugin_file = defined('CBPARALLAX_PLUGIN_FILE') ? CBPARALLAX_PLUGIN_FILE : __FILE__;
register_activation_hook($plugin_file, __NAMESPACE__.'\activate_plugin');
register_deactivation_hook($plugin_file, __NAMESPACE__.'\deactivate_plugin');

add_action('after_setup_theme', static function() : void
{
    add_theme_support('custom-background');
}
);

/**
 * Runs the plugin.
 *
 * @return void
 * @since 0.1.0
 */
function run_cb_parallax() : void
{
    $plugin = new Includes\cb_parallax();
    $plugin->run();
}

add_action('init', __NAMESPACE__.'\run_cb_parallax', 5);
