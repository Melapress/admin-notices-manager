<?php


namespace AdminNoticesManager;


class Pointer {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_pointers' ] );
	}

	public function register_pointers() {
		$manager = new PointersManager( 'advanced_notices_manager_' );

		$initial_prompt_pointer_id = 'anm_initial_prompt';
		$manager->add_pointer( [
			'id'      => $initial_prompt_pointer_id,
			'target'  => '#wp-admin-bar-anm_notification_count',
			'options' => array(
				'content'      => sprintf(
					'<h3>%s</h3><p>%s</p>',
					__( 'Admin Notices Manager', 'advanced_notices_manager' ),
					__( 'From now onward, all the admin notices will be displayed here.', 'advanced_notices_manager' )
				),
				'position'     => array(
					'edge'  => 'top',
					'align' => 'center',
				),
				'pointerClass' => 'wp-pointer anm-pointer'
			)
		] );

		$manager->add_pointer( [
			'id'      => 'anm_settings_prompt',
			'target'  => '#menu-settings',
			'options' => array(
				'content'  => sprintf(
					'<h3>%s</h3><p>%s</p>',
					__( 'Configure the Admin Notices Manager', 'advanced_notices_manager' ),
					__( 'Configure how the plugin should handle the different types of admin notices.', 'advanced_notices_manager' )
				),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				)
			)
		] );
	}
}
