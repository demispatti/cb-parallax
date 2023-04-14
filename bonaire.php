<?php
namespace Bonaire;

use Bonaire\Includes as Includes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * @package:          Bonaire
 * @since             0.1.0
 * @package           bonaire
 * @author            Demis Patti
 * @link:             https://wordpress.org/plugins/bonaire/
 * Plugin URI:        https://wordpress.org/plugins/bonaire/
 * Plugin Name:       Bonaire
 * Description:       A very simple email client for Contact Form 7 in combination with Flamingo.
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bonaire
 * Domain Path:       /languages
 * Version:           0.1.1
 * Stable tag:        0.1.1
 * Requires at least: 5.5
 * Tested up to:      6.1.1
 * Requires PHP:      5.6
 * Max. PHP version:  7.4.21
 */

/**
 * Define plugin constants.
 */
if ( ! defined( 'BONAIRE_ROOT_DIR' ) ) {
	define( 'BONAIRE_ROOT_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'BONAIRE_ROOT_URL' ) ) {
	define( 'BONAIRE_ROOT_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BONAIRE_PLUGINS_ROOT_DIR' ) ) {
	if('src' === basename( __DIR__ )){
		define( 'BONAIRE_PLUGINS_ROOT_DIR', plugin_dir_path( __FILE__ ) . '../../' );
		define( 'BONAIRE_SCRIPT_DEBUG', true );
	} else {
		define( 'BONAIRE_PLUGINS_ROOT_DIR', plugin_dir_path( __FILE__ ) . '../' );
	}
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'Includes\Bonaire' ) ) {
	require_once BONAIRE_ROOT_DIR . 'includes/class-bonaire.php';
}
if ( ! class_exists( 'Includes\Bonaire_Activator' ) ) {
	require_once BONAIRE_ROOT_DIR . 'includes/class-activator.php';
}
if ( ! class_exists( 'Includes\Bonaire_Deactivator' ) ) {
	require_once BONAIRE_ROOT_DIR . 'includes/class-deactivator.php';
}

/**
 * The class that launches the plugin.
 *
 * @since     0.9.6
 * @package    Bonaire
 * @subpackage
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Launcher {
	
	/**
	 * The name of the plugin.
	 *
	 * @var      string $name
	 * @since   0.9.6
	 * @access   public
	 */
	public $name = 'bonaire';
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   public
	 */
	public $domain = 'bonaire';
	
	/**
	 * The version of the plugin.
	 *
	 * @var      string $version
	 * @since   0.9.6
	 * @access   public
	 */
	public $version = '1.0.0';
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function add_hooks() {
		
		register_activation_hook( __FILE__, array( $this, 'activate_bonaire' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_bonaire' ) );
	}
	
	/**
	 * Runs during plugin activation.
	 * This action is documented in includes/class-activator.php
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function activate_bonaire() {
		
		$Bonaire_Activator = new Includes\Bonaire_Activator();
		$Bonaire_Activator->activate();
	}
	
	/**
	 * Runs during plugin deactivation.
	 * This action is documented in includes/class-deactivator.php
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function deactivate_bonaire() {
		
		$Bonaire_Deactivator = new  Includes\Bonaire_Deactivator();
		$Bonaire_Deactivator->deactivate();
	}
	
	/**
	 * Begins execution of the plugin.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function run_bonaire() {
		
		$Bonaire = new Includes\Bonaire( $this->name, $this->domain, $this->version );
		$Bonaire->init();
	}
	
}

/**
 * Add activation and deactivation hooks.
 */
$Bonaire_Launcher = new Bonaire_Launcher();
$Bonaire_Launcher->add_hooks();

/**
 * Run plugin.
 */
add_action( 'plugins_loaded', array( $Bonaire_Launcher, 'run_bonaire' ), 10 );
