<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/FluffyKod
 * @since      1.0.0
 *
 * @package    Clmte
 * @subpackage Clmte/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Clmte
 * @subpackage Clmte/includes
 * @author     CLMTE <info@clmte.com>
 */
class Clmte_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
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
		delete_option( 'clmte_compensation_price' );
		delete_option( 'clmte_img_id' );
		delete_option( 'clmte_custom_placement' );
	}

}
