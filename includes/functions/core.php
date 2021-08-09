<?php
/**
 * Core plugin functionality.
 *
 * @package AdminNoticesManager
 * @since 1.0.0
 */

namespace AdminNoticesManager\Core;

use AdminNoticesManager\Notices;
use AdminNoticesManager\Pointer;
use AdminNoticesManager\Settings;
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
		new Pointer();
		new Settings();
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
	update_option( 'anm-plugin-installed-by-user-id', get_current_user_id(), false );
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

	$system_messages = [
		//  pages and posts
		__( 'Post draft updated.' ),
		__( 'Post updated.' ),
		__( 'Page draft updated.' ),
		__( 'Page updated.' ),
		__( '1 post not updated, somebody is editing it.' ),
		__( '1 page not updated, somebody is editing it.' ),

		//  comments
		__( 'Invalid comment ID.' ),
		__( 'Sorry, you are not allowed to edit comments on this post.' ),
		__( 'This comment is already approved.' ),
		__( 'This comment is already in the Trash.' ),
		__( 'This comment is already marked as spam.' ),

		//  users
		__( 'New user created.' ),
		__( 'User updated.' ),
		__( 'User deleted.' ),
		__( 'Changed roles.' ),
		__( 'The current user&#8217;s role must have user editing capabilities.' ),
		__( 'Other user roles have been changed.' ),
		__( 'You can&#8217;t delete the current user.' ),
		__( 'Other users have been deleted.' ),
		__( 'User removed from this site.' ),
		__( "You can't remove the current user." ),
		__( 'Other users have been removed.' ),

		//  themes
		__( 'The active theme is broken. Reverting to the default theme.' ),
		__( 'Settings saved and theme activated.' ),
		__( 'New theme activated.' ),
		__( 'Theme deleted.' ),
		__( 'You cannot delete a theme while it has an active child theme.' ),
		__( 'Theme resumed.' ),
		__( 'Theme could not be resumed because it triggered a <strong>fatal error</strong>.' ),
		__( 'Theme will be auto-updated.' ),
		__( 'Theme will no longer be auto-updated.' ),

		//  plugins
		__( 'Plugin activated.' ),
		__( 'Plugin deactivated.' ),
		__( 'Plugin downgraded successfully.' ),
		__( 'Plugin updated successfully.' ),

		//  settings
		__( 'Settings saved.' ),
		__( 'Permalink structure updated.' ),
		__( 'You should update your %s file now.' ),
		__( 'Permalink structure updated. Remove write access on %s file now!' ),
		__( 'Privacy Policy page updated successfully.' ),
		__( 'The currently selected Privacy Policy page does not exist. Please create or select a new page.' ),
		__( 'The currently selected Privacy Policy page is in the Trash. Please create or select a new Privacy Policy page or <a href="%s">restore the current page</a>.' ),

		//  multisite
		__( 'Sites removed from spam.' ),
		__( 'Sites marked as spam.' ),
		__( 'Sites deleted.' ),
		__( 'Site deleted.' ),
		__( 'Sorry, you are not allowed to delete that site.' ),
		__( 'Site archived.' ),
		__( 'Site unarchived.' ),
		__( 'Site activated.' ),
		__( 'Site deactivated.' ),
		__( 'Site removed from spam.' ),
		__( 'Site marked as spam.' ),

		//  personal data export
		__( 'Unable to initiate confirmation request.' ),
		__( 'Unable to initiate user privacy confirmation request.' ),
		__( 'Unable to add this request. A valid email address or username must be supplied.' ),
		__( 'Invalid user privacy action.' ),
		__( 'Confirmation request sent again successfully.' ),
		__( 'Confirmation request initiated successfully.' )
	];

	$plural_system_messages = [
		//  posts and pages
		[ '%s post permanently deleted.', '%s posts permanently deleted.' ],
		[ '%s post moved to the Trash.', '%s posts moved to the Trash.' ],
		[ '%s post restored from the Trash.', '%s posts restored from the Trash.' ],
		[ '%s page permanently deleted.', '%s pages permanently deleted.' ],
		[ '%s page moved to the Trash.', '%s pages moved to the Trash.' ],
		[ '%s page restored from the Trash.', '%s pages restored from the Trash.' ],
		[ '%s post updated.', '%s posts updated.' ],
		[ '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.' ],
		[ '%s page updated.', '%s pages updated.' ],
		[ '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.' ],

		//  comments
		[ '%s comment approved.', '%s comments approved.' ],
		[ '%s comment marked as spam.', '%s comments marked as spam.' ],
		[ '%s comment restored from the spam.', '%s comments restored from the spam.' ],
		[ '%s comment moved to the Trash.', '%s comments moved to the Trash.' ],
		[ '%s comment restored from the Trash.', '%s comments restored from the Trash.' ],
		[ '%s comment permanently deleted.', '%s comments permanently deleted.' ],

		//  users
		[ '%s user deleted.', '%s users deleted.' ],

		//  personal data export
		[ '%d confirmation request failed to resend.', '%d confirmation requests failed to resend.' ],
		[ '%d confirmation request re-sent successfully.', '%d confirmation requests re-sent successfully.' ],
		[ '%d request marked as complete.', '%d requests marked as complete.' ],
		[ '%d request failed to delete.', '%d requests failed to delete.' ],
		[ '%d request deleted successfully.', '%d requests deleted successfully.' ]
	];

	foreach ( $plural_system_messages as $message ) {
		array_push( $system_messages, _n( $message[0], $message[1], 0 ) );
		array_push( $system_messages, _n( $message[0], $message[1], 1 ) );
		array_push( $system_messages, _n( $message[0], $message[1], 2 ) );
		array_push( $system_messages, _n( $message[0], $message[1], 5 ) );
	}

	wp_localize_script( 'admin_notices_manager_admin', 'anm_i18n', [
		'title'              => esc_html__( 'Admin notices', 'admin-notices-manager' ),
		'title_empty'        => esc_html__( 'No admin notices', 'admin-notices-manager' ),
		'date_time_preamble' => esc_html__( 'First logged: ', 'admin-notices-manager' ),
		'system_messages'    => $system_messages,
		'settings'           => Settings::get_settings(),
		'ajaxurl'            => admin_url( 'admin-ajax.php' ),
		'nonce'              => wp_create_nonce( 'anm-ajax-nonce' )
	] );

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
