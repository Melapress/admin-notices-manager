<?php
/**
 * Manager class for WordPress pointers.
 *
 * @package AdminNoticesManager
 */

namespace AdminNoticesManager;

use function AdminNoticesManager\Core\script_url;

/**
 * Manages WordPress pointers.
 */
class PointersManager {

	/**
	 * Plugin prefix to allow the pointers manager class to be used in different plugins.
	 *
	 * @var string
	 */
	private $plugin_prefix;

	/**
	 * An array of already added pointer.
	 *
	 * @var array[]
	 */
	private $pointers;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_prefix Plugin prefix.
	 */
	public function __construct( $plugin_prefix ) {
		$this->plugin_prefix = $plugin_prefix;
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pointers' ), 1000 );
		add_action( 'wp_ajax_wpws_dismiss_wp_pointer', array( $this, 'dismiss_wp_pointer' ) );
	}

	/**
	 * Ajax request handler to dismiss pointers.
	 *
	 * @since 3.2.4
	 */
	public function dismiss_wp_pointer() {
		$pointer = sanitize_text_field( wp_unslash( $_POST['pointer'] ) ); // phpcs:ignore
		if ( sanitize_key( $pointer ) !== $pointer ) {
			wp_die( 0 );
		}

		$dismissed = array_filter( explode( ',', (string) get_option( 'wpws-dismissed-pointers', '' ) ) );
		if ( self::is_dismissed( $pointer ) ) {
			wp_die( 0 );
		}

		$dismissed[] = $pointer;

		update_option( 'wpws-dismissed-pointers', implode( ',', $dismissed ), false );
		wp_die( 1 );
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
		$dismissed = array_filter( explode( ',', (string) get_option( 'wpws-dismissed-pointers', '' ) ) );

		return in_array( $pointer_id, $dismissed, true );
	}

	/**
	 * Add a pointer to the list.
	 *
	 * @param array $pointer Pointer data.
	 *
	 * @return false|void
	 */
	public function add_pointer( $pointer ) {

		if (
			empty( $pointer )
			|| empty( $pointer['id'] )
			|| empty( $pointer['target'] )
			|| empty( $pointer['options'] )
		) {
			return false;
		}

		if ( ! $this->pointers || ! is_array( $this->pointers ) ) {
			$this->pointers = array();
		}

		$this->pointers[ $pointer['id'] ] = $pointer;
	}

	/**
	 * Load Pointers.
	 *
	 * @param string $hook_suffix - Current hook suffix.
	 *
	 * @since 1.0.0
	 */
	public function load_pointers( $hook_suffix ) {
		// Don't run on WP < 3.3.
		if ( get_bloginfo( 'version' ) < '3.3' ) {
			return;
		}

		$valid_pointers = array();

		// Check that current user should see the pointers.
		$eligible_user_id = intval( get_option( 'anm-plugin-installed-by-user-id', 1 ) );
		if ( 0 === $eligible_user_id ) {
			$eligible_user_id = 1;
		}

		$current_user_id = get_current_user_id();
		if ( 0 === $current_user_id || $current_user_id !== $eligible_user_id ) {
			return;
		}

		// Check pointers and remove dismissed ones.
		if ( empty( $this->pointers ) ) {
			return;
		}

		foreach ( $this->pointers as $pointer_id => $pointer ) {

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
		wp_enqueue_style( 'wp-pointer' );

		// Add pointers script to queue. Add custom script.
		$script_handle = $this->plugin_prefix . '_pointer';
		wp_enqueue_script(
			$script_handle,
			script_url( 'pointer', 'admin' ),
			array( 'wp-pointer' ),
			ADMIN_NOTICES_MANAGER_VERSION,
			true
		);

		// Add pointer options to script.
		wp_localize_script( $script_handle, 'wpws_pointers', $valid_pointers );
	}
}
