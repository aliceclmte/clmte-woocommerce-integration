<?php
/**
 * Displays a table in the WC Settings page
 *
 * @link        https://paulmiller3000.com
 * @since       1.0.0
 *
 * @package     P3k_Galactica
 * @subpackage  P3k_Galactica/admin
 */

$GLOBALS['hide_save_button'] = true;

global $wpdb;

// Get the purchases.
$table_name = $table_name = $wpdb->prefix . 'clmte_offsets_purchased';
$log_data   = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY time DESC" );

// Get how many pruchased which have not been synced with the CLMTE database.
$not_synced = count( $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'PENDING'" ) );

?>

<h1>Purchases</h1>

<?php if ( 0 !== $not_synced ) { // Not all purchases are synced. ?>

<p><?php echo esc_html( ( 1 === $not_synced ) ? ( __( '1 kompensation', 'clmte' ) ) : ( $not_synced . __( ' kompensationer', 'clmte' ) ) ); ?> <?php _esc_html_e( 'ej synkroniserade med CLMTE’s servrar.', 'clmte' ); ?></p>
<p><i><?php _esc_html_e( 'Obs: Högst 2 stycken kompensationer med statusen PENDING kommer att synkroniseras automatiskt vid varje nytt köp av klimatkompensationer.', 'clmte' ); ?></i></p>
<button id="clmte-sync-offsets"><?php _esc_html_e( 'Manuell Synkronisering', 'clmte' ); ?></button>

<?php } ?>

<table class="clmte-table">
    <tr>
        <th><?php esc_html_e( 'Tid', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Antal', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Status', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Kg koldioxid kompenserad', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Spårnings-id', 'clmte' ); ?></th>
        <th><?php esc_html_e( 'Kompensations-id', 'clmte' ); ?></th>
    </tr>
    <?php foreach ( $log_data as $log ) { ?>
    <tr class="<?php echo esc_attr( $log->status ); ?>">
        <td><?php echo esc_html( $log->time ); ?></td>
        <td><?php echo esc_html( $log->amount ); ?></td>
        <td><?php echo esc_html( $log->status ); ?></td>
        <td><?php echo esc_html( $log->carbon_dioxide ); ?></td>
        <td><?php echo esc_html( $log->tracking_id ); ?></td>
        <td><?php echo esc_html( $log->offset_id ); ?></td>
    </tr>
    <?php } // End foreach. ?>
</table>

