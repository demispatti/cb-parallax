<?php
/**
 * Helper Functions
 *
 * Global helper functions for the CB Parallax plugin
 *
 * @package    CbParallax
 * @subpackage Core
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

if (!function_exists('cb_parallax')) {
    /**
     * Get the main plugin instance
     *
     * @return \CbParallax\Plugin
     */
    function cb_parallax(): \CbParallax\Plugin
    {
        return \CbParallax\Plugin::getInstance();
    }
}

if (!function_exists('cb_parallax_get_option')) {
    /**
     * Get a plugin option value
     *
     * @param string $key     Option key
     * @param mixed  $default Default value
     * @return mixed
     */
    function cb_parallax_get_option(string $key, $default = null)
    {
        $optionsService = cb_parallax()->get(\CbParallax\Services\OptionsService::class);
        return $optionsService->get($key, $default);
    }
}

if (!function_exists('cb_parallax_set_option')) {
    /**
     * Set a plugin option value
     *
     * @param string $key   Option key
     * @param mixed  $value Option value
     * @return bool
     */
    function cb_parallax_set_option(string $key, $value): bool
    {
        $optionsService = cb_parallax()->get(\CbParallax\Services\OptionsService::class);
        return $optionsService->set($key, $value);
    }
}

if (!function_exists('cb_parallax_is_enabled')) {
    /**
     * Check if parallax is enabled
     *
     * @return bool
     */
    function cb_parallax_is_enabled(): bool
    {
        return (bool) cb_parallax_get_option('parallax_enabled', false);
    }
}

if (!function_exists('cb_parallax_asset_url')) {
    /**
     * Get asset URL
     *
     * @param string $path Relative path to asset
     * @return string
     */
    function cb_parallax_asset_url(string $path): string
    {
        return plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . ltrim($path, '/');
    }
}

if (!function_exists('cb_parallax_asset_path')) {
    /**
     * Get asset path
     *
     * @param string $path Relative path to asset
     * @return string
     */
    function cb_parallax_asset_path(string $path): string
    {
        return plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . ltrim($path, '/');
    }
}

if (!function_exists('cb_parallax_view')) {
    /**
     * Render a view file
     *
     * @param string $view View name
     * @param array  $data Data to pass to view
     * @return void
     */
    function cb_parallax_view(string $view, array $data = []): void
    {
        $viewFile = cb_parallax_asset_path("views/{$view}.php");

        if (!file_exists($viewFile)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_die(sprintf('View file not found: %s', esc_html($viewFile)));
            }
            return;
        }

        extract($data, EXTR_SKIP);
        include $viewFile;
    }
}

if (!function_exists('cb_parallax_sanitize_rgba')) {
    /**
     * Sanitize RGBA color value
     *
     * @param string $color RGBA color string
     * @return string
     */
    function cb_parallax_sanitize_rgba(string $color): string
    {
        // Check for rgba format
        if (preg_match('/rgba?\([\d\s,\.]+\)/i', $color)) {
            return $color;
        }

        // Fall back to hex sanitization
        return sanitize_hex_color($color);
    }
}
