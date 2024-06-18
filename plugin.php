<?php

/**
 * Plugin Name: ACE/Netflix Aspera Package Transfer
 */

add_action( 'admin_menu', function () {
    add_menu_page(
        'Package Transfers',
        'Package Transfers',
        'manage_options',
        'aspera/package-transfers.php',
        function () {
            include 'admin.html.php';
        }
    );
} );

add_action( 'admin_post_start_transfer', function() {

    $recipientEmail = sanitize_text_field( $_REQUEST['recipientEmail'] );

    global $wpdb;
    $result = $wpdb->insert(
        $wpdb->prefix . 'aspera_package_transfers',
        [
            'recipient' => $recipientEmail,
            'status' => 'pending',
            'log' => 'Transfer has been initiated.',
        ]
    );

    include dirname(__FILE__) . '/demo.php';

    $redirect = sanitize_text_field( $_REQUEST['redirect'] );
    wp_redirect( add_query_arg( 'page', $redirect, admin_url() ));
} );

register_activation_hook( __FILE__, function() {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$wpdb->prefix}aspera_package_transfers (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime NOT NULL DEFAULT NOW(),
        recipient tinytext NOT NULL,
        status tinytext NOT NULL,
        log text NOT NULL,
        PRIMARY KEY  (id)
    ) {$wpdb->get_charset_collate()};";

    $results = dbDelta( $sql );
    ray($results);
} );
