<?php
/**
 * Core plugin functionality.
 *
 * @package AdminNoticesManager
 * @since 1.0.0
 */

namespace AdminNoticesManager\Core;

use AdminNoticesManager\Notices;
use \WP_Error as WP_Error;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	if ( is_admin() ) {
		new Notices();
	}

	add_action( 'init', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	do_action( 'admin_notices_manager_loaded' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'admin-notices-manager' );
	load_textdomain( 'admin-notices-manager', WP_LANG_DIR . '/admin-notices-manager/admin-notices-manager-' . $locale . '.mo' );
	load_plugin_textdomain( 'admin-notices-manager', false, plugin_basename( ADMIN_NOTICES_MANAGER_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'admin_notices_manager_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}


/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return [ 'admin' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in AdminNoticesManager script loader.' );
	}

	return ADMIN_NOTICES_MANAGER_URL . "assets/dist/js/${script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in AdminNoticesManager stylesheet loader.' );
	}

	return ADMIN_NOTICES_MANAGER_URL . "assets/dist/css/${stylesheet}.css";

}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {

	add_thickbox();

	wp_enqueue_script(
		'admin_notices_manager_admin',
		script_url( 'admin', 'admin' ),
		[ 'thickbox' ],
		ADMIN_NOTICES_MANAGER_VERSION,
		true
	);

}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'admin_notices_manager_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		ADMIN_NOTICES_MANAGER_VERSION
	);

}
