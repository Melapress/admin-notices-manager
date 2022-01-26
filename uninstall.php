<?php

/**
 * Uninstall script.
 */
// If uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"
            DELETE FROM $wpdb->sitemeta
            WHERE meta_key LIKE %s
            ",
			array(
				'anm%',
			)
		)
	);
} else {
	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"
            DELETE FROM $wpdb->options
            WHERE option_name LIKE %s
            ",
			array(
				'anm%',
			)
		)
	);
}
