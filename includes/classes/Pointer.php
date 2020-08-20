<?php


namespace AdminNoticesManager;


class Pointer {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_pointers' ] );
	}

	public function register_pointers() {
		$manager = new PointersManager( 'advanced_notices_manager_' );
		$manager->add_pointer( [
			'id'      => 'anm_initial_prompt',
			'target'  => '#wp-admin-bar-anm_notification_count',
			'options' => array(
				'content'  => sprintf(
					'<h3>%s</h3><p>%s</p>',
					__( 'Admin Notices Manager', 'advanced_notices_manager' ),
					__( 'From now onward, all the admin notices will be displayed here.', 'advanced_notices_manager' )
				),
				'position' => array(
					'edge'  => 'top',
					'align' => 'center',
				)
			)
		] );
	}
}
