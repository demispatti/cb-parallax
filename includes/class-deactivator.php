<?php
/**
 * Fired during plugin deactivation.
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
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.1.0
 * @package    CbParallax
 * @subpackage CbParallax/includes
 * @author     Demis Patti <demis@demispatti.ch>
 */
class cb_parallax_deactivator
{
    /**
     * The variable that holds the name of the capability which is necessary
     * to interact with this plugin.
     *
     * @since    0.1.0
     * @access   static
     * @var      string $capability
     */
    public static string $capability = 'cb_parallax_edit';
    
    /**
     * Removes the capability necessary to interact with this plugin to the user, if the user has the role of an administrator.
     *
     * @return void
     */
    public static function deactivate() : void
    {
        // Gets the administrator role.
        $role = get_role('administrator');
        
        // If the acting user has admin rights, the capability gets removed.
        if (!empty($role)) {
            $role->remove_cap(self::$capability);
        }
    }
}
