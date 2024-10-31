<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Quick_Admin
 * @author    Nilambar Sharma <nilambar@outlook.com>
 * @license   GPL-2.0+
 * @link      http://nilambar.net
 * @copyright 2014 Nilambar Sharma
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

if ( is_multisite() ) {

	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );

	delete_option('qa_plugin_options');

	if ( $blogs ) {

	 	foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			delete_option('qa_plugin_options');
			restore_current_blog();
		}
	}

} else {

	delete_option('qa_plugin_options');

}
