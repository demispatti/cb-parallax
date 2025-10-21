<?php
/**
 * Plugin Name: CB Parallax
 * Plugin URI: https://wordpress.org/plugins/cb-parallax/
 * Description: Premium parallax background plugin with custom backgrounds for posts, pages, and products. Features vertical and horizontal parallax effects with modern architecture.
 * Version: 0.9.7
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Author: Demis Patti
 * Author URI: http://demispatti.ch
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cb-parallax
 * Domain Path: /languages
 *
 * @package CbParallax
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

// Define plugin constants
define('CBPARALLAX_VERSION', '0.9.7');
define('CBPARALLAX_PLUGIN_FILE', __FILE__);
define('CBPARALLAX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CBPARALLAX_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CBPARALLAX_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Legacy constants for backward compatibility
if (!defined('CBPARALLAX_ROOT_DIR')) {
    define('CBPARALLAX_ROOT_DIR', CBPARALLAX_PLUGIN_DIR);
}
if (!defined('CBPARALLAX_ROOT_URL')) {
    define('CBPARALLAX_ROOT_URL', CBPARALLAX_PLUGIN_URL);
}

/**
 * Autoloader
 *
 * Load Composer autoloader if available, otherwise use fallback
 */
if (file_exists(CBPARALLAX_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once CBPARALLAX_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Fallback autoloader for PSR-4
    spl_autoload_register(function ($class) {
        $prefix = 'CbParallax\\';
        $base_dir = CBPARALLAX_PLUGIN_DIR . 'src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    });
}

// Load helper functions
if (file_exists(CBPARALLAX_PLUGIN_DIR . 'src/helpers.php')) {
    require_once CBPARALLAX_PLUGIN_DIR . 'src/helpers.php';
}

/**
 * Plugin activation hook
 *
 * @return void
 */
function cb_parallax_activate(): void
{
    try {
        \CbParallax\Plugin::activate();
        flush_rewrite_rules();
    } catch (\Throwable $e) {
        wp_die(
            sprintf(
                '<h1>CB Parallax Activation Error</h1><p>%s</p>',
                esc_html($e->getMessage())
            )
        );
    }
}

/**
 * Plugin deactivation hook
 *
 * @return void
 */
function cb_parallax_deactivate(): void
{
    try {
        \CbParallax\Plugin::deactivate();
        flush_rewrite_rules();
    } catch (\Throwable $e) {
        error_log('CB Parallax deactivation error: ' . $e->getMessage());
    }
}

/**
 * Plugin uninstall hook
 *
 * @return void
 */
function cb_parallax_uninstall(): void
{
    \CbParallax\Plugin::uninstall();
}

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'cb_parallax_activate');
register_deactivation_hook(__FILE__, 'cb_parallax_deactivate');

/**
 * Initialize and run the plugin
 *
 * @return void
 */
function cb_parallax_init(): void
{
    try {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            add_action('admin_notices', function () {
                echo '<div class="error"><p>';
                printf(
                    esc_html__('CB Parallax requires PHP 7.4 or higher. You are running PHP %s.', 'cb-parallax'),
                    esc_html(PHP_VERSION)
                );
                echo '</p></div>';
            });
            return;
        }

        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '5.5.0', '<')) {
            add_action('admin_notices', function () use ($wp_version) {
                echo '<div class="error"><p>';
                printf(
                    esc_html__('CB Parallax requires WordPress 5.5 or higher. You are running WordPress %s.', 'cb-parallax'),
                    esc_html($wp_version)
                );
                echo '</p></div>';
            });
            return;
        }

        // Initialize the plugin
        $plugin = \CbParallax\Plugin::getInstance();
        $plugin->run();

        // Provide access to plugin instance
        $GLOBALS['cb_parallax'] = $plugin;

    } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                'CB Parallax initialization error: %s in %s on line %d',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
        }

        add_action('admin_notices', function () use ($e) {
            echo '<div class="error"><p>';
            echo '<strong>' . esc_html__('CB Parallax Error:', 'cb-parallax') . '</strong> ';
            echo esc_html($e->getMessage());
            echo '</p></div>';
        });
    }
}

// Hook into plugins_loaded with high priority
add_action('plugins_loaded', 'cb_parallax_init', 5);

/**
 * Load plugin text domain for internationalization
 */
add_action('init', function () {
    load_plugin_textdomain(
        'cb-parallax',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}, 0);
