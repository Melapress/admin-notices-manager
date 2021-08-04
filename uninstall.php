<?php

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
    $network_id = get_current_network_id();
    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "
            DELETE FROM $wpdb->sitemeta
            WHERE meta_key LIKE %s
            AND site_id = %d
            ",
            [
                'anm%',
                $network_id
            ]
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
            [
                'anm%',
            ]
        )
    );
}