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

/**********************************
* HELPER FUNCTIONS
**********************************/

/**
* Makes a curl-request and returns an array with the json data
*
* @param string $url the url to be fetched and parsed as json
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

        $url = 'https://api-sandbox.tundra.clmte.com/organisation/'. $organisation_id .'/cost';

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
