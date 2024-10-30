<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Mobigate
 * @author    Don Nguyen <don.nguyen@hazuu.com>
 * @link      http://hazuu.com
 * @copyright 2014 Don Nguyen
 *
 * @mobigate
 * Plugin Name:       Mobigate
 * Plugin URI:        @TODO
 * Description:       Help getting game from Mobigate.vn
 * Version:           1.0.3
 * Author:            Don Nguyen
 * Author URI:        don.nguyen@hazuu.com
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/dondonnguyen/mobigate-wordpress-plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-mobigate.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'Mobigate', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Mobigate', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Mobigate_Admin with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'Mobigate', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-mobigate-admin.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/game-list-table.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/api-connector.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/wp-flash-message.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'admin/includes/mobigate-helper.php' );

	add_action( 'plugins_loaded', array( 'Mobigate_Admin', 'get_instance' ) );
}
