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

	/**
	 * @var string Name of the option storing the plugin settings.
	 */
	private static $option_name = 'anm_settings';

	/**
	 * Settings constructor.
	 */
	public function __construct() {

		$options = self::get_settings();

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

		$popup_style_options = array(
			'slide-in' => __( 'Slide in from the right', 'sample-domain' ),
			'popup'    => __( 'Popup', 'sample-domain' ),
		);

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
							],
							'exceptions' => array(
								'title' => __( 'CSS selector', 'admin-notices-manager' ),
								'type'  => 'text',
								'value' => array_key_exists( 'exceptions-css-selector', $options ) ? $options['exceptions-css-selector'] : '',
								'text'  => __( 'Plugin will ignore all notices matching this CSS selector. Use jQuery compatible CSS selector.', 'admin-notices-manager' ),
							),
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
					'styling' => [
						'title'  => __( 'Admin notices popup styling', 'sample-domain' ),
						'text'   => __( 'How do you want ANM to look?', 'sample-domain' ),
						'fields' => [
							'popup-style' => [
								'title'   => __( 'Popup style', 'sample-domain' ),
								'type'    => 'radio',
								'value'   => array_key_exists( 'popup-style', $options ) ? $options['popup-style'] : 'slide-in',
								'choices' => $popup_style_options
							],
							'slide_in_background' => [
								'title'   => __( 'Slide in background colour', 'sample-domain' ),
								'type'    => 'color',
								'value'   => array_key_exists( 'popup-style', $options ) ? $options['popup-style'] : '#1d2327',
							],
						]
					],
				),
			)
		);

		new \RationalOptionPages( $pages );
	}

	public static function get_settings() {
		return wp_parse_args( get_option( self::$option_name, array() ), [
			"success_level_notices"          => "popup-only",
			"error_level_notices"            => "popup-only",
			"warning_level_notices"          => "popup-only",
			"information_level_notices"      => "popup-only",
			"no_level_notices"               => "popup-only",
			"wordpress_system_admin_notices" => "leave",
			"popup_style"                    => "slide-in",
			"slide_in_background"            => "#1d2327",
			"exceptions_css_selector"        => "",
		] );
	}
}
