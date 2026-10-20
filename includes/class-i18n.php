<?php
/**
 * Define the internationalization functionality.
 *
 * @link       https://wordpress.org/plugins/cb-parallax/
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */

namespace CbParallax\Includes;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_i18n
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
     * cb_parallax_i18n constructor.
     *
     * @param string $domain
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }
    
    /**
     * Registers the methods that need to be hooked with WordPress.
     *
     * @return void
     * @since 0.9.0
     */
    public function add_hooks() : void
    {
        add_action('init', array(
            $this,
            'load_plugin_textdomain'
        ));
    }
    
    /**
     * Calls the WordPress function that loads this plugin's translated strings.
     */
    public function load_plugin_textdomain() : void
    {
        load_plugin_textdomain($this->domain, false, dirname(dirname(plugin_basename(__FILE__))).'/languages/');
    }
}
