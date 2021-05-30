<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/aliceclmte/clmte-woocommerce-integration
 * @since             1.0.0
 * @package           Clmte
 *
 * @wordpress-plugin
 * Plugin Name:       CLMTE - WooCommerce Integration
 * Plugin URI:        https://github.com/aliceclmte/clmte-woocommerce-integration
 * Description:       Easily allow your customers to carbon offset their purchases in your WooCommerce shop. Read more at clmte.com.
 * Version:           1.0.0
 * Author:            CLMTE
 * Author URI:        https://github.com/aliceclmte/clmte-woocommerce-integration
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       clmte
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CLMTE_VERSION', '1.0.0' );

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
* Check for the existence of WooCommerce and any other requirements
*/
function clmte_check_requirements() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        return true;
    } else {
        add_action( 'admin_notices', 'clmte_missing_wc_notice' );
        return false;
    }
}


/**
* Display a message showing that WooCommerce is required
*/
function clmte_missing_wc_notice() { 
    $class = 'notice notice-error';
    $message = __( 'CLMTE requires WooCommerce to be installed and active.', 'clmte' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

/**
* Display clmte offset box
*/
function clmte_create_offset_box(){

    // Check if API key and organisation ID are set and correct
    if (get_option('clmte_has_correct_credentials') == FALSE) {
        return;
    }

    // Get stored price of offset
    clmte_align_offset_price();
    $offset_price = get_offset_price();
		
    if ( isset($offset_price) ) {
        ?>
        <div id="clmte-compensation">
            <div class="info">
                <i id="clmte-info" class="fa fa-info-circle"></i>
                <p><?php _e('Vill du klimatkompensera ditt köp för', 'clmte'); ?> <b><?php echo esc_html( $offset_price . ' ' . get_woocommerce_currency() ); ?></b>?</p> 
            </div>
            <button id="clmte-compensate"><?php _e('Lägg till klimatkompensation', 'clmte'); ?></button>
        </div>

        <div id="clmte-panel">
            <p><?php _e('CLMTEs klimatkompensation gör ditt köp klimatneutralt genom att finansiera initiativ runt om i världen som minskar koldioxidutsläpp. Kostnaden är beräknad enligt bolagets data- och forskningsbaserade algoritm, och alla finansierade initiativ är FN-certifierade. Läs mer på', 'clmte'); ?> 
            <a href="https://clmte.com/faq" target="_blank" rel="nofollow">clmte.com/faq</a>!
            <p>	
        </div>
        <?php
    }
        
    return;
}

/**
* Display CLMTE receipt with carbon offset order information and a QR-code to track the offset if a CLMTE offset was purchased.
*/
function clmte_create_receipt(){

    // Get saved options
    $clmte_purchase = get_option( 'clmte-purchase' );

    // Check if CLMTE carbon offset purchased
    if ( !array_key_exists('clmte-offsets-amount', $clmte_purchase) ) {
        return;
    }

    ?>

    <div id="clmte-order">
        <h2><?php _e('Din Klimatkompensation', 'clmte'); ?></h2>
    
        <div class="clmte-order-content">

            <p><?php _e('Tack för du klimatkompenserade ditt köp med CLMTE!', 'clmte'); ?></p>

            <?php
            // Display carbon dioxide
            if ( array_key_exists('clmte-offsets-carbon', $clmte_purchase) ) {
            ?>
            <p><span><?php echo esc_html( $clmte_purchase['clmte-offsets-carbon'] ); ?><?php _e('kg koldioxid', 'clmte'); ?></span> <?php _e('kommer att klimatkompenseras tack vare din insats.', 'clmte'); ?></p>
            <?php } // End isset clmte carbon dioxide ?>

            <?php
            // Display QR code and thank you message
            if ( array_key_exists('clmte-tracking-url', $clmte_purchase) ) {
            ?>

            <p class="clmte-order-title"><b><?php _e('CLMTE Klimatspårning', 'clmte'); ?></b></p>

            <div id="clmte-qr-code">
                <p><?php _e('Skanna QR-koden nedan eller besök', 'clmte'); ?> <a rel="nofollow" target="_blank" href="<?php echo esc_url( $clmte_purchase['clmte-tracking-url'] ); ?>"><?php _e('din spårningssida', 'clmte'); ?></a> <?php _e('för att följa din klimatkompensations påverkan i realtid och se vilket klimatinitiativ det bidrar till.', 'clmte'); ?></p>

                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo esc_url( $clmte_purchase['clmte-tracking-url'] ); ?>&choe=UTF-8" title="<?php _e('Skanna för att följa din klimatkompensation!', 'clmte'); ?>" alt="<?php _e('QR-kod för CLMTE klimatkompensation', 'clmte'); ?>"/>

                <p><?php _e('För mer information, besök', 'clmte'); ?> <a href="https://clmte.com/faq" target="_blank" rel="nofollow">clmte.com/faq</a>.</p>
            </div>

            <?php } // End isset clmte tracking url ?>

        </div>
        
    </div>
    <?php
}

/**********************************
* HELPER FUNCTIONS
**********************************/

/**
* Makes sure that the API and Organisation IDs are correct.
* If correct and working: set clmte_has_correct_credentials to true
* Otherwise, set it to false
*/
function clmte_check_credentials() {

    $option_name = 'clmte_has_correct_credentials';

    // Check if API key and Organisation_id even exists
    if ( empty(get_option('clmte_api_key')) or empty(get_option('clmte_organisation_id')) ) {
        update_option( $option_name, FALSE );
        return;
    }

    // API key and Org ID set, attempt to get and set offset price
    if (get_offset_price(TRUE) == NULL) {
        update_option( $option_name, FALSE );
        return;
    }

    // It works
    update_option( $option_name, TRUE );
}

/**
* Creates a log and inserts it into the clmte_log table
*
* @param string $log - the log statement
* @param string $type - the type of log (error, activity)
*/
function clmte_create_log( $log, $type ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'clmte_log';

    $wpdb->insert(
        $table_name,
        array(
            'description' => $log,
            'type' => $type
        )
    );
}

/**
* Sends an API Post request to the CLMTE servers
*
* @param string $url     - the url to the CLMTE api
* @param string $api_key - the clients api key
* @param int $amount  - how many offsets to buy
*
* @return array
*/
function clmte_send_offset_request( $url, $api_key, $amount ) {

    // Save how many offsets pruchased
	$clmte_purchase = array();
	$clmte_purchase['clmte-offsets-amount'] = $amount;
    
    // Create request header
    $header = array();
    $header[] = 'Content-type: application/json'; 
    $header[] = 'Authorization: APIKey ' . $api_key;

    // Build the data query
    $data = array( 'amount' => $amount );
    $data_string = json_encode($data);

    // Create curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute request and catch errors
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);

        // Add log
        clmte_create_log( "CURL request failed: $error_msg", 'error' );
    }
    curl_close($ch);
    
    // Get the response
    $body = json_decode($response);

    // If errors, get the error message
    if (array_key_exists('errors', $body) && $body->errors[0]->message != '') { // Purchase failed

        // Get server error message
        $error_msg = $body->errors[0]->message;

        // Update option with error
        $clmte_purchase['clmte-offset-error'] = $error_msg;

    } else { // Purchase succeeded

        $clmte_purchase['clmte-offset-id'] = $body->id;
        $clmte_purchase['clmte-offsets-carbon'] = $body->carbonDioxide;

        // If tracking ID exists, create a tracking url
        if (array_key_exists('trackingID', $body)) {
            $tracking_id = $body->trackingID;

            // Compose a tracking url
            $tracking_url = "https://clmte.com/track?trackingId=$tracking_id&amount=$product_quantity";

            // Save tracking URL and ID
            $clmte_purchase['clmte-tracking-id'] = $tracking_id;
            $clmte_purchase['clmte-tracking-url'] = $tracking_url;
        }

    }

    return $clmte_purchase;
}

/**
* Creates an offset log and inserts it into the clmte_offsets_purchased table
*
* @param int $amount - how many offsets purchased
* @param string $status - [CREATED or PENDING], if the purchase has been logged
* @param string $offset_id - id of purchased carbon offset
* @param string $tracking_id - tracking id of purchased carbon offset
* @param int $carbon_dioxide - CO2 compensated by the purchased carbon offset
*/

function clmte_create_purchase_log( $amount, $status = 'PENDING', $offset_id = NULL, $tracking_id = NULL, $carbon_dioxide = NULL ) {
    
    global $wpdb;

    $table_name = $wpdb->prefix . 'clmte_offsets_purchased';

    $wpdb->insert(
        $table_name,
        array(
            'offset_id' => $offset_id,
            'tracking_id' => $tracking_id,
            'carbon_dioxide' => $carbon_dioxide,
            'amount' => $amount,
            'status' => $status
        )
    );
}


/**
* Decides if the sandbox or real api url should be used
*
* @param string $production - the production api url
* @param string $sandbox - the sandbox api url
* @return string 
*/
function get_clmte_url( $production, $sandbox ) { 
    
    $in_production = get_option('clmte_production_mode');

    if ( $in_production == 'yes' ) {
        // Use real api
        return $production;
    } else {
        // Use sandbox api
        return $sandbox;
    }
}

/**
* Makes a curl-request and returns an array with the json data
*
* @param string $url - the url to be fetched and parsed as json
* @return array
*/
function make_json_request( $url ) { 
    
    // Initialize curl request
    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    
    // Execute curl request and save response
    $response = curl_exec($ch);
    curl_close($ch);

    // Save the json formatted data as an array
    $data = json_decode($response);

    return $data;
}

/**
* Fetches the offset price from the tundra api
*
* @return float
*/
function get_offset_price( $new_request = FALSE ) { 

    // Check for saved price of option
    $offset_price = get_option('clmte_offset_price', NULL);

    if ($offset_price == '0,00' or $offset_price == '0.00') {
        $offset_price = NULL;
    } 

    // Make api request if no previously saved price
    if ($offset_price == NULL or $new_request) {

        // Get API key and organisation id
        $api_key = get_option('clmte_api_key');
        $organisation_id = get_option('clmte_organisation_id');

        // Use the correct api endpoint
        $api_url = get_clmte_url( 
            'https://api.tundra.clmte.com/organisation/',
            'https://api-sandbox.tundra.clmte.com/organisation/'
        );

        // Build url
        $url = $api_url . $organisation_id .'/cost';

        // Get price of offset
        $data = make_json_request( $url );
        $offset_price = $data->price; 

        // Format compensation price to two decimals
        $offset_price = number_format((float)$offset_price, wc_get_price_decimals(), wc_get_price_decimal_separator(), '');

        if ($offset_price == '0,00' or $offset_price == '0.00') {
            $offset_price = NULL;
        } 
        
        // Update offset price
        update_option( 'clmte_offset_price', $offset_price );
    }

    // Return the offset price
    return $offset_price;
}

/**
* Makes sure the offset has an equal amount of decimal places as the site configuration.
*/
function clmte_align_offset_price() {
    // Check if offset has correct amount of decimals
    if (get_option('clmte_offset_price') != NULL) {
        if (wc_get_price_decimals() != strlen(substr(strrchr(get_option('clmte_offset_price'), "."), 1))) {
            // Reformat the price with correct number of decimals
            get_offset_price( TRUE );
        }
    }
}

function clmte_sync_offsets( $limit = FALSE ) { 

    global $wpdb;

    // Get all pending offsets
    $table_name = $wpdb->prefix . 'clmte_offsets_purchased';
    $pending = $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'PENDING' ORDER BY time ASC" );

    // Get correct api url (production or sandbox)
    $url = get_clmte_url( 
        "https://api.tundra.clmte.com/compensation",
        "https://api-sandbox.tundra.clmte.com/compensation"
    );

    // Get organisations api_key
    $api_key = get_option( 'clmte_api_key' );
    
    // Go through every pending offset

    $num_synced = 0;
    foreach( $pending as $p ) {

        // If limit reached, do not continue
        if ( $limit && $num_synced >= $limit ) { break; }

        // Call API
        $clmte_purchase = clmte_send_offset_request( $url, $api_key, $p->amount );

        // Check for no errors
        if ( !array_key_exists('clmte-offset-error', $clmte_purchase) ) {

            // Update the row for the clmte purchase
            $wpdb->update(
                $table_name, 
                array(
                    'offset_id'      => $clmte_purchase['clmte-offset-id'] ?? NULL,
                    'tracking_id'    => $clmte_purchase['clmte-tracking-id'] ?? NULL, 
                    'carbon_dioxide' => $clmte_purchase['clmte-offsets-carbon'] ?? NULL,
                    'status'         => 'CREATED'
                ),
                array(
                    'id' => $p->id
                )
            );

            // Keep track of offsets synced
            $num_synced++;

        }

    }
}

/**********************************
* END OF HELPER FUNCTIONS
**********************************/

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clmte-activator.php
 */
function activate_clmte() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clmte-activator.php';
	Clmte_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-clmte-deactivator.php
 */
function deactivate_clmte() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clmte-deactivator.php';
	Clmte_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_clmte' );
register_deactivation_hook( __FILE__, 'deactivate_clmte' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-clmte.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_clmte() {
	if (clmte_check_requirements()) {
		$plugin = new Clmte();
		$plugin->run();
	}
}
run_clmte();
