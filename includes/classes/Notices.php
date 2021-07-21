<?php
/**
 * Contains class Notices.
 *
 * @package AdminNoticesManager
 */

namespace AdminNoticesManager;

/**
 * Takes care of the admin notices content capture.
 *
 * @package AdminNoticesManager
 * @since 1.0.0
 */
class Notices {

	/**
	 * Notices constructor.
	 */
	public function __construct() {

		// priority of 0 to render before any notices
		add_action( 'network_admin_notices', array( $this, 'start_output_capturing' ), 0 );
		add_action( 'user_admin_notices', array( $this, 'start_output_capturing' ), 0 );
		add_action( 'admin_notices', array( $this, 'start_output_capturing' ), 0 );

		//  priority of 999999 to render after all notices
		add_action( 'all_admin_notices', array( $this, 'finish_output_capturing' ), 999999 );

		add_action( 'admin_bar_menu', [ $this, 'add_item_in_admin_bar' ], 100 );
		add_action( 'wp_ajax_anm_log_notices', [ $this, 'log_notices' ] );
	}

	/**
	 * Prints the beginning of wrapper element before all notices.
	 */
	public function start_output_capturing() {
		// hidden by default to prevent a flash of unstyled content on page load
		echo '<div class="anm-notices-wrapper" style="display: none;">';
	}

	/**
	 * Prints the beginning of wrapper element after all notices.
	 */
	public function finish_output_capturing() {
		echo '</div><!-- /.anm-notices-wrapper -->';
	}

	/**
	 * Adds menu item showing number of notifications.
	 *
	 * @param \WP_Admin_Bar $admin_bar WordPress admin bar.
	 */
	public function add_item_in_admin_bar( $admin_bar ) {
		$admin_bar->add_menu(
			[
				'id'     => 'anm_notification_count',
				'title'  => __('No admin notices', 'admin-notices-manager'),
				'href'   => '#',
				'parent' => 'top-secondary',
			]
		);
	}

	public function log_notices() {
		error_log(  print_r( $_POST, true ) );

		if ( isset( $_POST[ 'notices' ] ) ) {
			update_option( 'anm-stuff', $_POST[ 'notices' ] );
		}
		
	}
}
