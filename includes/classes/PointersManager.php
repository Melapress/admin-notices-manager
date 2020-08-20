<?php


namespace AdminNoticesManager;


use function AdminNoticesManager\Core\script_url;

class PointersManager {

	private $plugin_prefix;

	private $pointers;

	public function __construct( $plugin_prefix ) {

		$this->plugin_prefix = $plugin_prefix;
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pointers' ), 1000 );
	}

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
			$this->pointers = [];
		}

		$pointer['options']['pointerClass'] .= 'wp-pointer anm-pointer';
		$this->pointers[$pointer['id']] = $pointer;
	}

	/**
	 * Method: Load WSAL Notice Pointer.
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

		// Check pointers and remove dismissed ones.
		foreach ( $this->pointers as $pointer_id => $pointer ) {

			// don't display if already dismissed
			$already_dismissed = false;
			if ( $already_dismissed ) {
				return;
			}

			// don't display if already dismissed
			$already_dismissed = false;
			if ( $already_dismissed ) {
				return;
			}

			$pointer['pointer_id'] = $pointer['id'];

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
			[ 'wp-pointer' ],
			ADMIN_NOTICES_MANAGER_VERSION,
			true
		);

		// Add pointer options to script.
		wp_localize_script( $script_handle, 'wpws_pointers', $valid_pointers );
	}
}
