<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/FluffyKod
 * @since             1.0.0
 * @package           Clmte
 *
 * @wordpress-plugin
 * Plugin Name:       CLMTE - WooCommerce Integration
 * Plugin URI:        https://github.com/FluffyKod/CLMTE-Woo
 * Description:       Easily allow your customers to carbon offset their purchases in your WooCommerce shop. Read more at clmte.com.
 * Version:           1.0.0
 * Author:            CLMTE
 * Author URI:        https://github.com/FluffyKod
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
* Display a message advising WooCommerce is required
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
    $compensation_price = get_compensation_price();
		
    if ($compensation_price) {
        ?>
        <div id="clmte-compensation">
            <div class="info">
                <i id="clmte-info" class="fa fa-info-circle"></i>
                <p>Vill du klimatkompensera ditt köp för <b> <?php echo $compensation_price; ?> SEK</b>?</p> 
            </div>
            <button id="clmte-compensate">Lägg till klimatkompensation</button>
        </div>

        <div id="clmte-panel">
            <p>CLMTEs klimatkompensation gör ditt köp klimatneutralt genom att finansiera initiativ runt om i världen som minskar koldioxidutsläpp. Kostnaden är beräknad enligt bolagets data- och forskningsbaserade algoritm, och alla finansierade initiativ är FN-certifierade. Läs mer på 
            <a href="https://clmte.com/faq" target="_blank" rel="nofollow">clmte.com/faq</a>!
            <p>	
        </div>
        <?php
    }
        
    return;
}

/**
* Display QR-code if CLMTE offset was purchased.
*/
function clmte_create_receipt(){

    // Get saved options
    $clmte_error = get_option( 'clmte-offset-error');
    $clmte_tracking_url = get_option( 'clmte-tracking-url');
    $clmte_offsets_amount = get_option( 'clmte-offsets-amount');
    $clmte_offsets_carbon = get_option( 'clmte-offsets-carbon');

    // Check if CLMTE carbon offset purchased
    if ( !$clmte_offsets_amount ) {
        return;
    }

    ?>

    <div id="clmte-order">
        <h2>Your Carbon Offset</h2>
    
        <div class="clmte-order-content">

            <p>Thank you for carbon offsetting your purchase with CLMTE!</p>
            <p><span><?php echo $clmte_offsets_carbon; ?>kg carbon dioxide </span> will be compensated due to your offset.</p>

            <?php
            // Display QR code and thank you message
            if ( isset($clmte_tracking_url) ) {
            ?>

            <p class="clmte-order-title"><b>CLMTE Carbon Tracking</b></p>

            <div id="clmte-qr-code">
                <p>Scan the QR-code below or visit <a rel="nofollow" target="_blank" href="<?php echo $clmte_tracking_url; ?>">your tracking page</a> to follow the impact of your offset in real time and see which offset initiative it contributes towards.</p>

                <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo $clmte_tracking_url; ?>&choe=UTF-8" title="Scan to track your CLMTE carbon offset!" alt="CLMTE offset tracking QR-code"/>

                <p>For more information, visit <a href="https://clmte.com/faq" target="_blank" rel="nofollow">clmte.com/faq</a>.</p>
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
* Fetches the compensation price from the tundra api
*
* @return float
*/
function get_compensation_price() { 

    $compensation_price = get_option('clmte_compensation_price');

    if ($compensation_price == null or $compensation_price == '') {

        // API
        $api_key = get_option('clmte_api_key');

        $organisation_id = get_option('clmte_organisation_id');

        $api_url = get_clmte_url( 
            'https://api.tundra.clmte.com/organisation/',
            'https://api-sandbox.tundra.clmte.com/organisation/'
        );

        $url = $api_url . $organisation_id .'/cost';

        $data = make_json_request( $url );
        $compensation_price = $data->price; 

    }

    // Format compensation price to two decimals
    $compensation_price = number_format((float)$compensation_price, 2, ',', '');
    
    return $compensation_price;
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
