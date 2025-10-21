<?php
/**
 * Main Plugin Class
 *
 * The core plugin class that orchestrates all plugin functionality.
 * Uses dependency injection and follows modern WordPress plugin architecture.
 *
 * @package    CbParallax
 * @subpackage Core
 * @author     Demis Patti <demis@demispatti.ch>
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax;

use CbParallax\Core\Container;
use CbParallax\Core\Hooks\HookManager;
use CbParallax\Services\OptionsService;
use CbParallax\Admin\AdminController;
use CbParallax\Public\PublicController;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Main Plugin Class
 */
final class Plugin
{
    /**
     * Plugin version
     */
    public const VERSION = '0.9.7';

    /**
     * Plugin slug
     */
    public const SLUG = 'cb-parallax';

    /**
     * Text domain
     */
    public const TEXT_DOMAIN = 'cb-parallax';

    /**
     * Minimum PHP version
     */
    private const MIN_PHP_VERSION = '7.4.0';

    /**
     * Minimum WordPress version
     */
    private const MIN_WP_VERSION = '5.5.0';

    /**
     * Dependency injection container
     *
     * @var Container
     */
    private Container $container;

    /**
     * Hook manager instance
     *
     * @var HookManager
     */
    private HookManager $hookManager;

    /**
     * Singleton instance
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct()
    {
        $this->container = new Container();
        $this->hookManager = new HookManager();

        $this->checkRequirements();
        $this->registerServices();
        $this->loadTextDomain();
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }

    /**
     * Check if system requirements are met
     *
     * @return void
     * @throws \RuntimeException If requirements are not met
     */
    private function checkRequirements(): void
    {
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            throw new \RuntimeException(
                sprintf(
                    'CB Parallax requires PHP %s or higher. You are running PHP %s.',
                    self::MIN_PHP_VERSION,
                    PHP_VERSION
                )
            );
        }

        global $wp_version;
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            throw new \RuntimeException(
                sprintf(
                    'CB Parallax requires WordPress %s or higher. You are running WordPress %s.',
                    self::MIN_WP_VERSION,
                    $wp_version
                )
            );
        }
    }

    /**
     * Register services in the dependency injection container
     *
     * @return void
     */
    private function registerServices(): void
    {
        // Register core services
        $this->container->singleton(OptionsService::class, function ($container) {
            return new OptionsService(self::TEXT_DOMAIN);
        });

        // Register controllers
        $this->container->singleton(AdminController::class, function ($container) {
            return new AdminController(
                self::TEXT_DOMAIN,
                self::VERSION,
                $container->get(OptionsService::class)
            );
        });

        $this->container->singleton(PublicController::class, function ($container) {
            return new PublicController(
                self::TEXT_DOMAIN,
                $container->get(OptionsService::class)
            );
        });
    }

    /**
     * Run the plugin
     *
     * @return void
     */
    public function run(): void
    {
        try {
            $this->registerHooks();
            $this->initializeControllers();
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * Register WordPress hooks
     *
     * @return void
     */
    private function registerHooks(): void
    {
        // Plugin lifecycle hooks
        $this->hookManager->addAction(
            'plugins_loaded',
            [$this, 'onPluginsLoaded'],
            10
        );

        $this->hookManager->addAction(
            'init',
            [$this, 'onInit'],
            0
        );
    }

    /**
     * Initialize controllers based on context
     *
     * @return void
     */
    private function initializeControllers(): void
    {
        if (is_admin()) {
            $this->initializeAdmin();
        } else {
            $this->initializePublic();
        }
    }

    /**
     * Initialize admin controller
     *
     * @return void
     */
    private function initializeAdmin(): void
    {
        $adminController = $this->container->get(AdminController::class);
        $adminController->register($this->hookManager);
    }

    /**
     * Initialize public controller
     *
     * @return void
     */
    private function initializePublic(): void
    {
        $publicController = $this->container->get(PublicController::class);
        $publicController->register($this->hookManager);
    }

    /**
     * Load plugin text domain for internationalization
     *
     * @return void
     */
    private function loadTextDomain(): void
    {
        $this->hookManager->addAction(
            'plugins_loaded',
            function () {
                load_plugin_textdomain(
                    self::TEXT_DOMAIN,
                    false,
                    dirname(plugin_basename(CBPARALLAX_PLUGIN_FILE)) . '/languages'
                );
            }
        );
    }

    /**
     * Callback for plugins_loaded hook
     *
     * @return void
     */
    public function onPluginsLoaded(): void
    {
        // Allow other plugins to hook into CB Parallax
        do_action('cb_parallax_loaded', $this);
    }

    /**
     * Callback for init hook
     *
     * @return void
     */
    public function onInit(): void
    {
        // Allow other plugins to hook into CB Parallax initialization
        do_action('cb_parallax_init', $this);
    }

    /**
     * Get the dependency injection container
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get a service from the container
     *
     * @param string $id Service identifier
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    /**
     * Get plugin version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get plugin slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return self::SLUG;
    }

    /**
     * Get text domain
     *
     * @return string
     */
    public function getTextDomain(): string
    {
        return self::TEXT_DOMAIN;
    }

    /**
     * Handle errors gracefully
     *
     * @param \Throwable $error
     * @return void
     */
    private function handleError(\Throwable $error): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                'CB Parallax Error: %s in %s on line %d',
                $error->getMessage(),
                $error->getFile(),
                $error->getLine()
            ));

            if (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
                wp_die(
                    sprintf(
                        '<h1>CB Parallax Error</h1><p>%s</p><pre>%s</pre>',
                        esc_html($error->getMessage()),
                        esc_html($error->getTraceAsString())
                    )
                );
            }
        }
    }

    /**
     * Plugin activation hook
     *
     * @return void
     */
    public static function activate(): void
    {
        // Add capability to administrators
        $role = get_role('administrator');
        if ($role) {
            $role->add_cap('cb_parallax_edit', true);
        }

        // Set default options
        $optionsService = new OptionsService(self::TEXT_DOMAIN);
        $optionsService->setDefaults();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation hook
     *
     * @return void
     */
    public static function deactivate(): void
    {
        // Remove capability from administrators
        $role = get_role('administrator');
        if ($role) {
            $role->remove_cap('cb_parallax_edit');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin uninstall hook
     *
     * @return void
     */
    public static function uninstall(): void
    {
        // Remove all plugin options
        $optionsService = new OptionsService(self::TEXT_DOMAIN);
        $optionsService->deleteAll();

        // Remove all post meta
        delete_post_meta_by_key('_cb_parallax_background_image_id');
        delete_post_meta_by_key('_cb_parallax_background_image_url');
        delete_post_meta_by_key('_cb_parallax_background_color');
        delete_post_meta_by_key('_cb_parallax_can_parallax');
        delete_post_meta_by_key('_cb_parallax_direction');
        delete_post_meta_by_key('_cb_parallax_overlay_image');
    }
}
