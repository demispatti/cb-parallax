<?php
/**
 * Options Service
 *
 * Manages plugin options and settings with a clean, modern API.
 * Replaces the old 991-line monolithic class with focused, testable code.
 *
 * @package    CbParallax
 * @subpackage Services
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax\Services;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Options Service Class
 */
class OptionsService
{
    /**
     * Option key prefix
     */
    private const OPTION_PREFIX = 'cb_parallax_';

    /**
     * Text domain
     *
     * @var string
     */
    private string $textDomain;

    /**
     * Image options schema
     *
     * @var array
     */
    private array $imageOptionsSchema;

    /**
     * Plugin options schema
     *
     * @var array
     */
    private array $pluginOptionsSchema;

    /**
     * Overlay options schema
     *
     * @var array
     */
    private array $overlayOptionsSchema;

    /**
     * Constructor
     *
     * @param string $textDomain Text domain for translations
     */
    public function __construct(string $textDomain)
    {
        $this->textDomain = $textDomain;
        $this->initializeSchemas();
    }

    /**
     * Initialize option schemas
     *
     * @return void
     */
    private function initializeSchemas(): void
    {
        $this->imageOptionsSchema = [
            'attachment_id' => [
                'type' => 'integer',
                'default' => 0,
                'sanitize' => 'absint',
            ],
            'background_image_url' => [
                'type' => 'string',
                'default' => '',
                'sanitize' => 'esc_url_raw',
            ],
            'background_color' => [
                'type' => 'string',
                'default' => '',
                'sanitize' => 'sanitize_hex_color',
            ],
            'position_x' => [
                'type' => 'string',
                'default' => 'center',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['left', 'center', 'right'],
            ],
            'position_y' => [
                'type' => 'string',
                'default' => 'center',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['top', 'center', 'bottom'],
            ],
            'background_attachment' => [
                'type' => 'string',
                'default' => 'fixed',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['fixed', 'scroll'],
            ],
            'background_repeat' => [
                'type' => 'string',
                'default' => 'no-repeat',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'],
            ],
            'can_parallax' => [
                'type' => 'boolean',
                'default' => false,
                'sanitize' => 'rest_sanitize_boolean',
            ],
            'direction' => [
                'type' => 'string',
                'default' => 'vertical',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['vertical', 'horizontal'],
            ],
            'vertical_scroll_direction' => [
                'type' => 'string',
                'default' => 'top',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['top', 'bottom'],
            ],
            'horizontal_scroll_direction' => [
                'type' => 'string',
                'default' => 'left',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['left', 'right'],
            ],
            'horizontal_alignment' => [
                'type' => 'string',
                'default' => 'center',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['left', 'center', 'right'],
            ],
            'vertical_alignment' => [
                'type' => 'string',
                'default' => 'center',
                'sanitize' => 'sanitize_text_field',
                'allowed_values' => ['top', 'center', 'bottom'],
            ],
        ];

        $this->pluginOptionsSchema = [
            'parallax_enabled' => [
                'type' => 'boolean',
                'default' => false,
                'sanitize' => 'rest_sanitize_boolean',
            ],
            'disable_on_mobile' => [
                'type' => 'boolean',
                'default' => true,
                'sanitize' => 'rest_sanitize_boolean',
            ],
            'preserve_scrolling' => [
                'type' => 'boolean',
                'default' => true,
                'sanitize' => 'rest_sanitize_boolean',
            ],
            'allow_override' => [
                'type' => 'boolean',
                'default' => true,
                'sanitize' => 'rest_sanitize_boolean',
            ],
        ];

        $this->overlayOptionsSchema = [
            'overlay_image' => [
                'type' => 'string',
                'default' => 'none',
                'sanitize' => 'sanitize_text_field',
            ],
            'overlay_path' => [
                'type' => 'string',
                'default' => '',
                'sanitize' => 'esc_url_raw',
            ],
            'overlay_opacity' => [
                'type' => 'number',
                'default' => 0.3,
                'sanitize' => function ($value) {
                    return max(0, min(1, (float) $value));
                },
            ],
            'overlay_color' => [
                'type' => 'string',
                'default' => '',
                'sanitize' => 'sanitize_hex_color',
            ],
        ];
    }

    /**
     * Get an option value
     *
     * @param string $key     Option key (without prefix)
     * @param mixed  $default Default value if option doesn't exist
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $optionKey = self::OPTION_PREFIX . $key;
        $value = get_option($optionKey, $default);

        // Get schema for this option
        $schema = $this->getOptionSchema($key);

        if ($schema && isset($schema['default']) && $value === $default) {
            return $schema['default'];
        }

        return $value;
    }

    /**
     * Set an option value
     *
     * @param string $key   Option key (without prefix)
     * @param mixed  $value Option value
     * @param bool   $autoload Whether to autoload the option
     * @return bool True if successful, false otherwise
     */
    public function set(string $key, $value, bool $autoload = true): bool
    {
        $optionKey = self::OPTION_PREFIX . $key;

        // Sanitize the value based on schema
        $value = $this->sanitizeValue($key, $value);

        return update_option($optionKey, $value, $autoload);
    }

    /**
     * Delete an option
     *
     * @param string $key Option key (without prefix)
     * @return bool True if successful, false otherwise
     */
    public function delete(string $key): bool
    {
        $optionKey = self::OPTION_PREFIX . $key;
        return delete_option($optionKey);
    }

    /**
     * Get all image options
     *
     * @return array
     */
    public function getImageOptions(): array
    {
        $options = [];

        foreach ($this->imageOptionsSchema as $key => $schema) {
            $options[$key] = $this->get($key, $schema['default']);
        }

        return $options;
    }

    /**
     * Get all plugin options
     *
     * @return array
     */
    public function getPluginOptions(): array
    {
        $options = [];

        foreach ($this->pluginOptionsSchema as $key => $schema) {
            $options[$key] = $this->get($key, $schema['default']);
        }

        return $options;
    }

    /**
     * Get all overlay options
     *
     * @return array
     */
    public function getOverlayOptions(): array
    {
        $options = [];

        foreach ($this->overlayOptionsSchema as $key => $schema) {
            $options[$key] = $this->get($key, $schema['default']);
        }

        return $options;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAll(): array
    {
        return [
            'image_options' => $this->getImageOptions(),
            'plugin_options' => $this->getPluginOptions(),
            'overlay_options' => $this->getOverlayOptions(),
        ];
    }

    /**
     * Set default options
     *
     * @return void
     */
    public function setDefaults(): void
    {
        foreach ($this->imageOptionsSchema as $key => $schema) {
            if (!$this->exists($key)) {
                $this->set($key, $schema['default']);
            }
        }

        foreach ($this->pluginOptionsSchema as $key => $schema) {
            if (!$this->exists($key)) {
                $this->set($key, $schema['default']);
            }
        }

        foreach ($this->overlayOptionsSchema as $key => $schema) {
            if (!$this->exists($key)) {
                $this->set($key, $schema['default']);
            }
        }
    }

    /**
     * Check if an option exists
     *
     * @param string $key Option key (without prefix)
     * @return bool
     */
    public function exists(string $key): bool
    {
        $optionKey = self::OPTION_PREFIX . $key;
        return get_option($optionKey) !== false;
    }

    /**
     * Delete all plugin options
     *
     * @return void
     */
    public function deleteAll(): void
    {
        foreach (array_keys($this->imageOptionsSchema) as $key) {
            $this->delete($key);
        }

        foreach (array_keys($this->pluginOptionsSchema) as $key) {
            $this->delete($key);
        }

        foreach (array_keys($this->overlayOptionsSchema) as $key) {
            $this->delete($key);
        }
    }

    /**
     * Sanitize a value based on its schema
     *
     * @param string $key   Option key
     * @param mixed  $value Value to sanitize
     * @return mixed Sanitized value
     */
    private function sanitizeValue(string $key, $value)
    {
        $schema = $this->getOptionSchema($key);

        if (!$schema || !isset($schema['sanitize'])) {
            return $value;
        }

        $sanitizeCallback = $schema['sanitize'];

        // If it's a callable, call it
        if (is_callable($sanitizeCallback)) {
            $value = call_user_func($sanitizeCallback, $value);
        }

        // Validate against allowed values if specified
        if (isset($schema['allowed_values']) && !in_array($value, $schema['allowed_values'], true)) {
            return $schema['default'];
        }

        return $value;
    }

    /**
     * Get option schema
     *
     * @param string $key Option key
     * @return array|null
     */
    private function getOptionSchema(string $key): ?array
    {
        if (isset($this->imageOptionsSchema[$key])) {
            return $this->imageOptionsSchema[$key];
        }

        if (isset($this->pluginOptionsSchema[$key])) {
            return $this->pluginOptionsSchema[$key];
        }

        if (isset($this->overlayOptionsSchema[$key])) {
            return $this->overlayOptionsSchema[$key];
        }

        return null;
    }

    /**
     * Get image options schema
     *
     * @return array
     */
    public function getImageOptionsSchema(): array
    {
        return $this->imageOptionsSchema;
    }

    /**
     * Get plugin options schema
     *
     * @return array
     */
    public function getPluginOptionsSchema(): array
    {
        return $this->pluginOptionsSchema;
    }

    /**
     * Get overlay options schema
     *
     * @return array
     */
    public function getOverlayOptionsSchema(): array
    {
        return $this->overlayOptionsSchema;
    }
}
