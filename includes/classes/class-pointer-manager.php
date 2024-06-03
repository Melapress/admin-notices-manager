<?php
/**
 * Manager class for WordPress pointers.
 *
 * @package AdminNoticesManager
 */

declare(strict_types=1);

namespace AdminNoticesManager;

use function AdminNoticesManager\Core\script_url;

if ( ! class_exists( '\AdminNoticesManager\Pointers_Manager' ) ) {
	/**
	 * Manages WordPress pointers.
	 *
	 * @since 1.0.0
	 */
	class Pointers_Manager {

		/**
		 * Plugin prefix to allow the pointers manager class to be used in different plugins.
		 *
		 * @var string
		 *
		 * @since 1.0.0
		 */
		private static $plugin_prefix = 'advanced_notices_manager_';

		/**
		 * An array of already added pointer.
		 *
		 * @var array[]
		 *
		 * @since 1.0.0
		 */
		private static $pointers;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_pointers' ), 1000 );
			add_action( 'wp_ajax_wpws_dismiss_wp_pointer', array( __CLASS__, 'dismiss_wp_pointer' ) );
		}

		/**
		 * Ajax request handler to dismiss pointers.
		 *
		 * @since 3.2.4
		 */
		public static function dismiss_wp_pointer() {
			if ( ! isset( $_POST['pointer'] ) ) {
				\wp_die( 0 );
			}

			$pointer = \sanitize_text_field( \wp_unslash( $_POST['pointer'] ) );
			if ( \sanitize_key( $pointer ) !== $pointer ) {
				\wp_die( 0 );
			}

			$dismissed = array_filter( explode( ',', (string) \get_option( 'wpws-dismissed-pointers', '' ) ) );
			if ( self::is_dismissed( $pointer ) ) {
				\wp_die( 0 );
			}

			$dismissed[] = $pointer;

			\update_option( 'wpws-dismissed-pointers', implode( ',', $dismissed ), false );
			\wp_die( 1 );
		}

		/**
		 * Checks if a pointer is already dismissed.
		 *
		 * @param string $pointer_id Pointer identifier.
		 *
		 * @return bool True if the pointer is already dismissed. False otherwise.
		 * @since 1.1.0
		 */
		public static function is_dismissed( $pointer_id ) {
			$dismissed = array_filter( explode( ',', (string) \get_option( 'wpws-dismissed-pointers', '' ) ) );

			return in_array( $pointer_id, $dismissed, true );
		}

		/**
		 * Add a pointer to the list.
		 *
		 * @param array $pointer Pointer data.
		 *
		 * @return false|void
		 *
		 * @since 1.0.0
		 */
		public static function add_pointer( $pointer ) {

			if (
			empty( $pointer )
			|| empty( $pointer['id'] )
			|| empty( $pointer['target'] )
			|| empty( $pointer['options'] )
			) {
				return false;
			}

			if ( ! self::$pointers || ! is_array( self::$pointers ) ) {
				self::$pointers = array();
			}

			self::$pointers[ $pointer['id'] ] = $pointer;
		}

		/**
		 * Load Pointers.
		 *
		 * @param string $hook_suffix - Current hook suffix.
		 *
		 * @since 1.0.0
		 */
		public static function load_pointers( $hook_suffix ) {
			// Don't run on WP < 3.3.
			if ( \get_bloginfo( 'version' ) < '3.3' ) {
				return;
			}

			$valid_pointers = array();

			// Check that current user should see the pointers.
			$eligible_user_id = intval( \get_option( 'anm-plugin-installed-by-user-id', 1 ) );
			if ( 0 === $eligible_user_id ) {
				$eligible_user_id = 1;
			}

			$current_user_id = \get_current_user_id();
			if ( 0 === $current_user_id || $current_user_id !== $eligible_user_id ) {
				return;
			}

			// Check pointers and remove dismissed ones.
			if ( empty( self::$pointers ) ) {
				return;
			}

			foreach ( self::$pointers as $pointer_id => $pointer ) {

				// Don't display if already dismissed.
				if ( self::is_dismissed( $pointer_id ) ) {
					return;
				}

				$pointer['pointer_id'] = $pointer['id'];
				unset( $pointer['id'] );

				// Add the pointer to $valid_pointers array.
				$valid_pointers[] = $pointer;
			}

			// No valid pointers? Stop here.
			if ( empty( $valid_pointers ) ) {
				return;
			}

			// Add pointers style to queue.
			\wp_enqueue_style( 'wp-pointer' );

			// Add pointers script to queue. Add custom script.
			$script_handle = self::$plugin_prefix . '_pointer';
			\wp_enqueue_script(
				$script_handle,
				script_url( 'pointer', 'admin' ),
				array( 'wp-pointer' ),
				ADMIN_NOTICES_MANAGER_VERSION,
				true
			);

			// Add pointer options to script.
			\wp_localize_script( $script_handle, 'wpws_pointers', $valid_pointers );
		}
	}
}
