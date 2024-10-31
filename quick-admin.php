<?php
/**
 * Quick Admin.
 *
 * @package   Quick_Admin
 * @author    Nilambar Sharma <nilambar@outlook.com>
 * @license   GPL-2.0+
 * @link      http://nilambar.net
 * @copyright 2014 Nilambar Sharma
 *
 * @wordpress-plugin
 * Plugin Name:       Quick Admin
 * Plugin URI:        http://wordpress.org/plugins/quick-admin/
 * Description:       Add Quick Links in your admin dashboard and admin bar
 * Version:           1.2
 * Author:            Nilambar Sharma
 * Author URI:        http://nilambar.net
 * Text Domain:       quick-admin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'QUICK_ADMIN_BASENAME', basename( dirname( __FILE__ ) ) );
define( 'QUICK_ADMIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'QUICK_ADMIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );


/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-quick-admin.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Quick_Admin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quick_Admin', 'deactivate' ) );

/*
 * Create instance
 */
add_action( 'plugins_loaded', array( 'Quick_Admin', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * Include admin class
 */
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-quick-admin-admin.php' );
	add_action( 'plugins_loaded', array( 'Quick_Admin_Admin', 'get_instance' ) );

}
