<?php
/**
 * Admin Controller
 *
 * Handles all admin functionality including settings pages,
 * metaboxes, and admin scripts/styles.
 *
 * @package    CbParallax
 * @subpackage Admin
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax\Admin;

use CbParallax\Core\Hooks\HookManager;
use CbParallax\Services\OptionsService;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Admin Controller Class
 */
class AdminController
{
    /**
     * Text domain
     *
     * @var string
     */
    private string $textDomain;

    /**
     * Plugin version
     *
     * @var string
     */
    private string $version;

    /**
     * Options service
     *
     * @var OptionsService
     */
    private OptionsService $optionsService;

    /**
     * Constructor
     *
     * @param string         $textDomain     Text domain
     * @param string         $version        Plugin version
     * @param OptionsService $optionsService Options service
     */
    public function __construct(
        string $textDomain,
        string $version,
        OptionsService $optionsService
    ) {
        $this->textDomain = $textDomain;
        $this->version = $version;
        $this->optionsService = $optionsService;
    }

    /**
     * Register hooks with WordPress
     *
     * @param HookManager $hookManager Hook manager instance
     * @return void
     */
    public function register(HookManager $hookManager): void
    {
        // Admin menu
        $hookManager->addAction('admin_menu', [$this, 'registerAdminMenu'], 10);

        // Admin scripts and styles
        $hookManager->addAction('admin_enqueue_scripts', [$this, 'enqueueStyles'], 10);
        $hookManager->addAction('admin_enqueue_scripts', [$this, 'enqueueScripts'], 10);

        // Settings registration
        $hookManager->addAction('admin_init', [$this, 'registerSettings'], 10);

        // Add meta boxes for post types
        $hookManager->addAction('add_meta_boxes', [$this, 'registerMetaBoxes'], 10);
        $hookManager->addAction('save_post', [$this, 'savePostMeta'], 10, 2);

        // Add theme support
        $hookManager->addAction('after_setup_theme', [$this, 'addThemeSupport'], 10);

        // Add contextual help
        $hookManager->addAction('load-settings_page_cb-parallax', [$this, 'addContextualHelp'], 10);
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function registerAdminMenu(): void
    {
        add_options_page(
            __('CB Parallax Settings', $this->textDomain),
            __('CB Parallax', $this->textDomain),
            'manage_options',
            'cb-parallax',
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Render settings page
     *
     * @return void
     */
    public function renderSettingsPage(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // For now, include the legacy settings page
        // This can be modernized in a future iteration
        $legacyFile = plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . 'admin/menu/partials/settings-display.php';

        if (file_exists($legacyFile)) {
            include $legacyFile;
        } else {
            echo '<div class="wrap">';
            echo '<h1>' . esc_html__('CB Parallax Settings', $this->textDomain) . '</h1>';
            echo '<p>' . esc_html__('Settings page is being modernized.', $this->textDomain) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Register settings
     *
     * @return void
     */
    public function registerSettings(): void
    {
        $imageSchema = $this->optionsService->getImageOptionsSchema();
        $pluginSchema = $this->optionsService->getPluginOptionsSchema();
        $overlaySchema = $this->optionsService->getOverlayOptionsSchema();

        // Register image options
        foreach ($imageSchema as $key => $schema) {
            register_setting(
                'cb_parallax_image_options',
                'cb_parallax_' . $key,
                [
                    'type' => $schema['type'],
                    'default' => $schema['default'],
                    'sanitize_callback' => $schema['sanitize'] ?? null,
                ]
            );
        }

        // Register plugin options
        foreach ($pluginSchema as $key => $schema) {
            register_setting(
                'cb_parallax_plugin_options',
                'cb_parallax_' . $key,
                [
                    'type' => $schema['type'],
                    'default' => $schema['default'],
                    'sanitize_callback' => $schema['sanitize'] ?? null,
                ]
            );
        }

        // Register overlay options
        foreach ($overlaySchema as $key => $schema) {
            register_setting(
                'cb_parallax_overlay_options',
                'cb_parallax_' . $key,
                [
                    'type' => $schema['type'],
                    'default' => $schema['default'],
                    'sanitize_callback' => $schema['sanitize'] ?? null,
                ]
            );
        }
    }

    /**
     * Enqueue admin styles
     *
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueueStyles(string $hook): void
    {
        // Only load on our settings page and post edit screens
        if (!$this->shouldLoadAssets($hook)) {
            return;
        }

        wp_enqueue_style(
            'cb-parallax-admin',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . 'admin/css/admin.min.css',
            [],
            $this->version,
            'all'
        );

        // Load color picker alpha
        wp_enqueue_style(
            'wp-color-picker-alpha',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . 'vendor/color-picker-alpha/wp-color-picker-alpha.min.css',
            ['wp-color-picker'],
            $this->version
        );
    }

    /**
     * Enqueue admin scripts
     *
     * @param string $hook Current admin page hook
     * @return void
     */
    public function enqueueScripts(string $hook): void
    {
        if (!$this->shouldLoadAssets($hook)) {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue color picker
        wp_enqueue_script('wp-color-picker');

        // Enqueue color picker alpha
        wp_enqueue_script(
            'wp-color-picker-alpha',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . 'vendor/color-picker-alpha/wp-color-picker-alpha.min.js',
            ['wp-color-picker'],
            $this->version,
            true
        );

        $jsFile = $this->useModernAssets()
            ? 'dist/js/admin.min.js'
            : 'admin/js/settings-display.min.js';

        wp_enqueue_script(
            'cb-parallax-admin',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . $jsFile,
            ['jquery', 'wp-color-picker', 'wp-color-picker-alpha'],
            $this->version,
            true
        );

        // Localize script
        wp_localize_script(
            'cb-parallax-admin',
            'Cb_Parallax_Admin',
            [
                'image_options' => $this->optionsService->getImageOptions(),
                'plugin_options' => $this->optionsService->getPluginOptions(),
                'overlay_options' => $this->optionsService->getOverlayOptions(),
                'nonce' => wp_create_nonce('cb_parallax_admin'),
            ]
        );
    }

    /**
     * Check if we should load assets
     *
     * @param string $hook Current admin page hook
     * @return bool
     */
    private function shouldLoadAssets(string $hook): bool
    {
        // Load on settings page
        if ($hook === 'settings_page_cb-parallax') {
            return true;
        }

        // Load on post edit screens
        $screen = get_current_screen();
        if ($screen && in_array($screen->post_type, $this->getSupportedPostTypes(), true)) {
            return true;
        }

        return false;
    }

    /**
     * Check if modern assets should be used
     *
     * @return bool
     */
    private function useModernAssets(): bool
    {
        $distPath = plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . 'dist/';
        return file_exists($distPath . 'js/admin.min.js');
    }

    /**
     * Register meta boxes
     *
     * @return void
     */
    public function registerMetaBoxes(): void
    {
        foreach ($this->getSupportedPostTypes() as $postType) {
            add_meta_box(
                'cb_parallax_metabox',
                __('CB Parallax Background', $this->textDomain),
                [$this, 'renderMetaBox'],
                $postType,
                'normal',
                'high'
            );
        }
    }

    /**
     * Render meta box
     *
     * @param \WP_Post $post Current post object
     * @return void
     */
    public function renderMetaBox(\WP_Post $post): void
    {
        // For now, include legacy metabox
        $legacyFile = plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . 'admin/partials/metabox-display.php';

        if (file_exists($legacyFile)) {
            include $legacyFile;
        }
    }

    /**
     * Save post meta
     *
     * @param int      $postId Post ID
     * @param \WP_Post $post   Post object
     * @return void
     */
    public function savePostMeta(int $postId, \WP_Post $post): void
    {
        // Verify nonce
        if (!isset($_POST['cb_parallax_metabox_nonce']) ||
            !wp_verify_nonce($_POST['cb_parallax_metabox_nonce'], 'cb_parallax_metabox')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $postId)) {
            return;
        }

        // Save meta fields (simplified version)
        $metaFields = [
            '_cb_parallax_background_image_id',
            '_cb_parallax_background_image_url',
            '_cb_parallax_background_color',
            '_cb_parallax_can_parallax',
        ];

        foreach ($metaFields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($postId, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    /**
     * Add theme support
     *
     * @return void
     */
    public function addThemeSupport(): void
    {
        add_theme_support('custom-background');
    }

    /**
     * Add contextual help
     *
     * @return void
     */
    public function addContextualHelp(): void
    {
        $screen = get_current_screen();

        if (!$screen) {
            return;
        }

        $screen->add_help_tab([
            'id' => 'cb_parallax_overview',
            'title' => __('Overview', $this->textDomain),
            'content' => '<p>' . __('Configure your parallax background settings here.', $this->textDomain) . '</p>',
        ]);
    }

    /**
     * Get supported post types
     *
     * @return array
     */
    private function getSupportedPostTypes(): array
    {
        return apply_filters('cb_parallax_supported_post_types', [
            'post',
            'page',
            'product',
        ]);
    }
}
