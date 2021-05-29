<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/FluffyKod
 * @since      1.0.0
 *
 * @package    Clmte
 */

global $wpdb;

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove compensation product
$product_id = get_option('clmte_compensation_product_id');
if ( $product_id && $product_id != '' ) {
	wp_delete_post( $product_id );
}

// Remove img
$img_id = get_option('clmte_img_id');
if ( $img_id && $img_id != '' ) {
	wp_delete_attachment( $img_id );
}

// Reset options
delete_option( 'clmte_compensation_product_id' );
delete_option( 'clmte_offset_price' );
delete_option( 'clmte_img_id' );

delete_option( 'clmte_api_key' );
delete_option( 'clmte_organisation_id' );
delete_option( 'clmte_production_mode' );
delete_option( 'reload_cart_on_update' );
delete_option( 'clmte_custom_offset_placement' );
delete_option( 'clmte_custom_receipt_placement' );

delete_option( 'clmte-offset-error');
delete_option( 'clmte-tracking-url');
delete_option( 'clmte-offsets-amount');
delete_option( 'clmte-offsets-carbon');

// Remove log table
$table_name = $wpdb->prefix . 'clmte_log';

$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query( $sql );
