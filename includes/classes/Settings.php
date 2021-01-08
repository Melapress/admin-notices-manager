<?php
/**
 * Contains class Settings.
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
class Settings {

	public static $option_name = 'anm_settings';

	/**
	 * Settings constructor.
	 */
	public function __construct() {

		$options = get_option( self::$option_name, array() );

		if ( ! class_exists( 'RationalOptionPages' ) ) {
			require_once( ADMIN_NOTICES_MANAGER_INC . 'vendor' . DIRECTORY_SEPARATOR . 'jeremyHixon-RationalOptionPages' . DIRECTORY_SEPARATOR . 'RationalOptionPages.php' );
		}

		$notice_handling_options = array(
			'popup-only' => __( 'hide from the WordPress dashboard and show them in the plugin\'s popup', 'sample-domain' ),
			'hide'       => __( 'hide them completely (do not show in the WordPress dashboard or in the plugin\'s popup)', 'sample-domain' ),
			'leave'      => __( 'do not do anything (they will appear on the WordPress dashboard as per usual)', 'sample-domain' ),
		);

		$system_notices_options = $notice_handling_options;
		unset( $system_notices_options['hide'] );

		$standard_notices = [
			'success' => __( 'Success level notices', 'sample-domain' ),
			'error'   => __( 'Error level notices', 'sample-domain' ),
			'warning' => __( 'Warning level notices', 'sample-domain' ),
			'info'    => __( 'Information level notices', 'sample-domain' )
		];

		$standard_notices_section_fields = [];
		foreach ( $standard_notices as $notice_type => $notice_field_title ) {
			$field_name                                     = $notice_type . '-notices';
			$standard_notices_section_fields[ $field_name ] = [
				'title'   => $notice_field_title,
				'type'    => 'radio',
				'value'   => array_key_exists( $field_name, $options ) ? $options[ $field_name ] : 'popup-only',
				'choices' => $notice_handling_options
			];
		}

		$pages = array(
			self::$option_name => array(
				'menu_title'  => __( 'Admin Notices', 'sample-domain' ),
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'Admin notices settings', 'sample-domain' ),
				'text'        => 'Use the settings in this page to configure how the plugin should handle different types of admin notices. Refer to the introduction to admin notices for a detailed explanation about the different types of admin notices available in WordPress.',
				'sections'    => array(
					'standard-notices'     => array(
						'title'  => __( 'Standard admin notices', 'sample-domain' ),
						'fields' => $standard_notices_section_fields
					),
					'non-standard-notices' => array(
						'title'  => __( 'Non-Standard admin notices', 'sample-domain' ),
						'text'   => __( 'These type of admin notices are typically created by third party plugins and themes and do not have any severity level. Use the below settings to configure how the plugin should handle these type of admin notices.', 'sample-domain' ),
						'fields' => [
							'no-level-notices' => [
								'title'   => __( 'No level notices', 'sample-domain' ),
								'type'    => 'radio',
								'value'   => array_key_exists( 'no-level-notices', $options ) ? $options['no-level-notices'] : 'popup-only',
								'choices' => $notice_handling_options
							]
						]
					),
					'system-notices'       => array(
						'title'  => __( 'WordPress system admin notices', 'sample-domain' ),
						'text'   => __( 'These type of admin notices are used by WordPress to advise you about the status of specific actions, for example to confirm that the changed settings were saved, or that a plugin was successfully installed. It is recommended to let these admin notices appear in the WordPress dashboard.', 'sample-domain' ),
						'fields' => [
							'system-level-notices' => [
								'title'   => __( 'WordPress system admin notices', 'sample-domain' ),
								'type'    => 'radio',
								'value'   => array_key_exists( 'system-level-notices', $options ) ? $options['system-level-notices'] : 'leave',
								'choices' => $system_notices_options
							]
						]
					),
				),
			)
		);

		new \RationalOptionPages( $pages );
	}
}
