<?php
/**
 * Public Controller
 *
 * Handles all frontend functionality including script/style enqueuing
 * and parallax rendering.
 *
 * @package    CbParallax
 * @subpackage Public
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax\Public;

use CbParallax\Core\Hooks\HookManager;
use CbParallax\Services\OptionsService;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Public Controller Class
 */
class PublicController
{
    /**
     * Text domain
     *
     * @var string
     */
    private string $textDomain;

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
     * @param OptionsService $optionsService Options service
     */
    public function __construct(string $textDomain, OptionsService $optionsService)
    {
        $this->textDomain = $textDomain;
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
        $hookManager->addAction('wp_enqueue_scripts', [$this, 'enqueueStyles'], 10);
        $hookManager->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts'], 10);
    }

    /**
     * Enqueue public stylesheets
     *
     * @return void
     */
    public function enqueueStyles(): void
    {
        $cssFile = $this->useModernAssets()
            ? 'dist/css/public.min.css'
            : 'public/css/public.min.css';

        wp_enqueue_style(
            'cb-parallax-public',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . $cssFile,
            [],
            $this->getAssetVersion($cssFile),
            'all'
        );
    }

    /**
     * Enqueue public scripts
     *
     * @return void
     */
    public function enqueueScripts(): void
    {
        // Enqueue jQuery (required for legacy compatibility)
        wp_enqueue_script('jquery');

        $jsFile = $this->useModernAssets()
            ? 'dist/js/public.min.js'
            : 'public/js/public.min.js';

        wp_enqueue_script(
            'cb-parallax-public',
            plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . $jsFile,
            ['jquery'],
            $this->getAssetVersion($jsFile),
            true
        );

        // Localize script with options
        wp_localize_script(
            'cb-parallax-public',
            'Cb_Parallax_Public',
            $this->getLocalizedData()
        );
    }

    /**
     * Get localized data for JavaScript
     *
     * @return array
     */
    private function getLocalizedData(): array
    {
        $imageOptions = $this->optionsService->getImageOptions();
        $pluginOptions = $this->optionsService->getPluginOptions();
        $overlayOptions = $this->optionsService->getOverlayOptions();

        // Get image dimensions if image exists
        $imageData = [];
        if (!empty($imageOptions['attachment_id'])) {
            $imageMeta = wp_get_attachment_metadata($imageOptions['attachment_id']);
            if ($imageMeta) {
                $imageData = [
                    'image_width' => $imageMeta['width'] ?? 0,
                    'image_height' => $imageMeta['height'] ?? 0,
                ];
            }
        }

        return [
            'image_options' => $this->formatOptionsForJs($imageOptions),
            'plugin_options' => $this->formatOptionsForJs($pluginOptions),
            'overlay_options' => $this->formatOverlayOptions($overlayOptions),
            'image_data' => $imageData,
            'debug' => defined('WP_DEBUG') && WP_DEBUG,
        ];
    }

    /**
     * Format options for JavaScript (convert booleans to '0'/'1' strings)
     *
     * @param array $options Options array
     * @return array
     */
    private function formatOptionsForJs(array $options): array
    {
        $formatted = [];

        foreach ($options as $key => $value) {
            if (is_bool($value)) {
                $formatted[$key] = $value ? '1' : '0';
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }

    /**
     * Format overlay options with full paths
     *
     * @param array $overlayOptions Overlay options
     * @return array
     */
    private function formatOverlayOptions(array $overlayOptions): array
    {
        if (empty($overlayOptions['overlay_path'])) {
            $overlayOptions['overlay_path'] = plugin_dir_url(CBPARALLAX_PLUGIN_FILE) . 'public/images/overlays/';
        }

        return $this->formatOptionsForJs($overlayOptions);
    }

    /**
     * Check if modern assets should be used
     *
     * @return bool
     */
    private function useModernAssets(): bool
    {
        $distPath = plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . 'dist/';
        return file_exists($distPath . 'js/public.min.js');
    }

    /**
     * Get asset version for cache busting
     *
     * @param string $file Relative file path
     * @return string
     */
    private function getAssetVersion(string $file): string
    {
        $filePath = plugin_dir_path(CBPARALLAX_PLUGIN_FILE) . $file;

        if (file_exists($filePath)) {
            return (string) filemtime($filePath);
        }

        return \CbParallax\Plugin::VERSION;
    }
}
