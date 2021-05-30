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

$table_name = $table_name = $wpdb->prefix . 'clmte_log';
$log_data = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY time DESC" );

?>

<h1><?php _e('CLMTE Plugin-loggar', 'clmte'); ?></h1>

<table class="clmte-table">
    <tr>
        <th><?php _e('Typ', 'clmte'); ?></th>
        <th><?php _e('Beskrivning', 'clmte'); ?></th>
        <th><?php _e('Tid', 'clmte'); ?></th>
    </tr>
    <?php foreach ($log_data as $log) { ?>
    <tr class="<?php echo $log->type; ?>">
        <td><?php echo $log->type; ?></td>
        <td><?php echo $log->description; ?></td>
        <td><?php echo $log->time; ?></td>
    </tr>
    <?php } // End foreach ?>
</table>

