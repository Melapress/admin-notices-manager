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
		add_action( 'wp_ajax_anm_hide_notice_forever', [ $this, 'hide_notice_forever' ] );
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

		// If we have a nonce posted, check it.
		if ( wp_doing_ajax() && isset( $_POST['_nonce'] ) ) {
			$nonce_check = wp_verify_nonce( sanitize_text_field( $_POST['_nonce'] ), 'anm-ajax-nonce' );
			if ( ! $nonce_check ) {
				return false;
				exit();
			}
		}

		if ( isset( $_POST[ 'notices' ] ) ) {
			$notices_to_process     = $_POST[ 'notices' ];
			$currently_held_options = get_option( 'anm-notices', [] );
			$hidden_forever         = get_option( 'anm-hidden-notices', [] );
			$hashed_notices         = [];
			$details                = [];	
			$format                 = get_option('date_format') . ' ' . get_option('time_format');

			foreach ( $_POST[ 'notices' ] as $index => $notice ) {
				$hash = wp_hash( $notice );

				$hashed_notices[ $hash ] = current_time( 'timestamp' );
				$details[ $index ]       = [ $hash, date_i18n( $format, current_time( 'timestamp' ) ) ];

				// Do we already know about this notice?
				if ( isset( $currently_held_options[ $hash ] ) ) {
					$hashed_notices[ $hash ] = $currently_held_options[ $hash ];
					$details[ $index ]       = [ $hash, date_i18n( $format, $currently_held_options[ $hash ] ) ];
				}

				if ( in_array( $hash, $hidden_forever ) ) {
					$details[ $index ] = 'do-not-display';
				}
			}

			update_option( 'anm-notices', $hashed_notices );

			wp_send_json_success( $details );
		}
		
	}

	public function hide_notice_forever() {

		// If we have a nonce posted, check it.
		if ( wp_doing_ajax() && isset( $_POST['_nonce'] ) ) {
			$nonce_check = wp_verify_nonce( sanitize_text_field( $_POST['_nonce'] ), 'anm-ajax-nonce' );
			if ( ! $nonce_check ) {
				return false;
				exit();
			}
		}

				
		if ( isset( $_POST[ 'notice_hash' ] ) ) {
			$currently_held_options = get_option( 'anm-hidden-notices', [] );
			array_push( $currently_held_options, $_POST[ 'notice_hash' ] ); 
			update_option( 'anm-hidden-notices', $currently_held_options );
			wp_send_json_success();
		}
	}
}
