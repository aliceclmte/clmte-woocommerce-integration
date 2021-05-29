<?php
/**
 * Displays a table in the WC Settings page
 *
 * @link        https://paulmiller3000.com
 * @since       1.0.0
 *
 * @package     P3k_Galactica
 * @subpackage  P3k_Galactica/admin
 *
 */

$GLOBALS['hide_save_button'] = true;

global $wpdb;

// Get the purchases
$table_name = $table_name = $wpdb->prefix . 'clmte_offsets_purchased';
$log_data = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY time DESC" );

// Get how many pruchased which have not been synced with the CLMTE database
$not_synced = sizeof($wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'PENDING'" ));

?>

<h1>Purchases</h1>

<?php if ( $not_synced != 0 ) { // Not all purchases are synced ?>

<p><?php echo ($not_synced == 1) ? ('1 offset') : ($not_synced . ' offsets') ?> not synced with the CLMTE servers.</p>
<button id="clmte-sync-offsets">Manual Sync</button>

<?php } ?>

<table class="clmte-table">
    <tr>
        <th>Time</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Carbon Dioxide Offset</th>
        <th>Tracking Id</th>
        <th>Offset Id</th>
    </tr>
    <?php foreach ($log_data as $log) { ?>
    <tr class="<?php echo $log->status; ?>">
        <td><?php echo $log->time; ?></td>
        <td><?php echo $log->amount; ?></td>
        <td><?php echo $log->status; ?></td>
        <td><?php echo $log->carbon_dioxide; ?></td>
        <td><?php echo $log->tracking_id; ?></td>
        <td><?php echo $log->offset_id; ?></td>
    </tr>
    <?php } // End foreach ?>
</table>

