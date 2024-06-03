<?php
/**
 * Class adding plugin's pointers.
 *
 * @package AdminNoticesManager
 */

declare(strict_types=1);

namespace AdminNoticesManager;

if ( ! class_exists( '\AdminNoticesManager\Pointer' ) ) {
	/**
	 * Class handles pointer registration.
	 *
	 * @since 1.0.0
	 */
	class Pointer {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			\add_action( 'admin_init', array( __CLASS__, 'register_pointers' ) );
			Pointers_Manager::init();
		}

		/**
		 * Registers pointers.
		 *
		 * @since 1.0.0
		 */
		public static function register_pointers() {

			if ( ! Settings::notice_hiding_allowed_for_current_user() ) {
				return;
			}

			$initial_prompt_pointer_id = 'anm_initial_prompt';
			Pointers_Manager::add_pointer(
				array(
					'id'      => $initial_prompt_pointer_id,
					'target'  => '#wp-admin-bar-anm_notification_count',
					'options' => array(
						'content'      => sprintf(
							'<h3>%s</h3><p>%s</p>',
							esc_html__( 'Admin Notices Manager', 'admin-notices-manager' ),
							esc_html__( 'From now onward, all the admin notices will be displayed here.', 'admin-notices-manager' )
						),
						'position'     => array(
							'edge'  => 'top',
							'align' => 'center',
						),
						'pointerClass' => 'wp-pointer anm-pointer',
					),
				)
			);

			Pointers_Manager::add_pointer(
				array(
					'id'      => 'anm_settings_prompt',
					'target'  => '#menu-settings',
					'options' => array(
						'content'  => sprintf(
							'<h3>%s</h3><p>%s</p>',
							esc_html__( 'Configure the Admin Notices Manager', 'admin-notices-manager' ),
							esc_html__( 'Configure how the plugin handles different types of admin notices from the Settings > Admin Notices menu item.', 'admin-notices-manager' )
						),
						'position' => array(
							'edge'  => 'left',
							'align' => 'center',
						),
					),
				)
			);
		}
	}
}
