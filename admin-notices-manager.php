<?php
/**
 * Plugin Name: Admin Notices Manager
 * Plugin URI: https://www.wpwhitesecurity.com/
 * Description: Better manage admin notices & never miss an important WordPress and developer message.
 * Author: WP White Security
 * Author URI: https://www.wpwhitesecurity.com/
 * Version: 1.3.0
 * Text Domain: admin-notices-manager
 * Domain Path: /languages
 * License: GPL2
 *
 * @package AdminNoticesManager
 *
 */

/*
	Admin Notices Manager
	Copyright(c) 2021  WP White Security  (email : info@wpwhitesecurity.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Useful global constants.
if ( ! defined( 'ADMIN_NOTICES_MANAGER_VERSION' ) ) {
	define( 'ADMIN_NOTICES_MANAGER_VERSION', '1.3.0' );
	define( 'ADMIN_NOTICES_MANAGER_URL', plugin_dir_url( __FILE__ ) );
	define( 'ADMIN_NOTICES_MANAGER_PATH', plugin_dir_path( __FILE__ ) );
	define( 'ADMIN_NOTICES_MANAGER_INC', ADMIN_NOTICES_MANAGER_PATH . 'includes/' );
}

// Include files.
require_once ADMIN_NOTICES_MANAGER_INC . 'functions/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\AdminNoticesManager\Core\activate' );
register_deactivation_hook( __FILE__, '\AdminNoticesManager\Core\deactivate' );

// Require Composer autoloader if it exists.
if ( file_exists( ADMIN_NOTICES_MANAGER_PATH . '/vendor/autoload.php' ) ) {
	require_once ADMIN_NOTICES_MANAGER_PATH . 'vendor/autoload.php';
}

// Bootstrap.
AdminNoticesManager\Core\setup();
