<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/FluffyKod
 * @since      1.0.0
 *
 * @package    Clmte
 * @subpackage Clmte/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Clmte
 * @subpackage Clmte/public
 * @author     CLMTE <info@clmte.com>
 */

global $woocommerce;

class Clmte_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'wp_ajax_add_compensation_to_cart', 'add_compensation_to_cart');
		add_action( 'wp_ajax_remove_compensation_from_cart', 'remove_compensation_from_cart');

		

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clmte_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clmte_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/clmte-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Clmte_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Clmte_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/clmte-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'clmte', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'reload_cart' => get_option('clmte_reload_cart_on_update')
		));

	}

	/**
	 * Change cart data compensation product price based on settings
	 *
	 * @since    1.0.0
	 */
	public function before_calculate_totals( $cart_obj ) {

		$compensation_id = get_option('clmte_compensation_product_id');

		foreach( $cart_obj->get_cart() as $key => $item ) {
			if( $item['product_id'] == $compensation_id ) {
				// Get product price
				$compensation_price = get_compensation_price();
				
				// Update the price of compensation product
				$item['data']->set_price( ( $compensation_price ) );
			}
		}
	}

	/**
	 * Add compensation product to cart
	 *
	 * @since    1.0.0
	 */
	public function add_compensation_to_cart() {
		// Add compensation product to cart
		WC()->cart->add_to_cart( get_option('clmte_compensation_product_id') );

		wp_die();
	}

	/**
	 * Remove compensation products from cart
	 *
	 * @since    1.0.0
	 */
	public function remove_compensation_from_cart() {
		// Remove all compensation products
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( $cart_item['product_id'] == get_option('clmte_compensation_product_id') ) {
				 WC()->cart->remove_cart_item( $cart_item_key );
			}
	   }

		wp_die();
	}

	/**
	 * Add checkbox with CLMTE compensation to cart
	 *
	 * @since    1.0.0
	 */
	public function clmte_add_offset_box() {

		// If custom placement is no, automatically add clmte offset box in cart
		if ( get_option( 'clmte_custom_offset_placement' ) == false or get_option( 'clmte_custom_offset_placement' ) == 'no') {

			// Create the clmte offset box
			clmte_create_offset_box();

		}
	}

    /**
	 * Add clmte receipt with tracking QR-Code automatically if custom placement has not been specified.
	 *
	 * @since    1.0.0
	 */
    public function clmte_thank_you() {

        // If custom placement is no, automatically add clmte receipt in the order details section
		if ( get_option( 'clmte_custom_receipt_placement' ) == false or get_option( 'clmte_custom_receipt_placement' ) == 'no') {

			// Create CLMTE offset receipt with QR-code
            clmte_create_receipt();

		}

        
    }

    /**
	 * Check if clmte carbon offset is purchased and send API post request to Tundra API. 
	 *
	 * @since    1.0.0
	 */
    public function clmte_purchase_carbon_offset( $order_id ) {
        // If not order_id, return
        if ( ! $order_id )
			return;

        // Allow code execution only once 
		if( ! get_post_meta( $order_id, '_clmte_offset_purchased', true ) ) {
            
            // Reset CLMTE options
            update_option( 'clmte-offset-purchased', false );
            update_option( 'clmte-offset-error', false );
            update_option( 'clmte-tracking-url', false );
            update_option( 'clmte-offsets-amount', false );
            update_option( 'clmte-offsets-carbon', false );

            // Get an instance of the WC_Order object
			$order = wc_get_order( $order_id );

            // Check if order is payed fully
            if($order->is_paid()) {
                
                // Loop through all order items to check if offset was purchased
				foreach ( $order->get_items() as $item_id => $item ) {
                    
                    // Get the product object
					$product = $item->get_product();
		
					// Get the product Id
					$product_id = $product->get_id();

                    // Check if product is carbon offset
                    if ($product_id == get_option('clmte_compensation_product_id')) {

                        // Get the product quantity
						$product_quantity = $item->get_quantity();

                        // Add log
                        clmte_create_log( "$product_quantity CLMTE carbon $offset_string purchased!", 'activity' );

                        // Catch errors
                        $offset_error = False;

                        ////////////////////////////////////
                        // Send request to CLMTE tundra API
                        ////////////////////////////////////

                        // Build the data query
						$data = array( 'amount' => $product_quantity );
						$data_string = json_encode($data);
                        
                        // Get correct api url (production or sandbox)
						$url = get_clmte_url( 
							"https://api.tundra.clmte.com/compensation",
							"https://api-sandbox.tundra.clmte.com/compensation"
						);

                        // Get organisations api_key
						$api_key = get_option( 'clmte_api_key' );
                        
                        // TODO: If not api_key?

                        // Create request header
						$header = array();
						$header[] = 'Content-type: application/json'; 
						$header[] = 'Authorization: APIKey ' . $api_key;

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
						if (array_key_exists('errors', $body) && $body->errors[0]->message != '') {

                            // Get server error message
							$error_msg = $body->errors[0]->message;

                            // Update option with error
                            update_option( 'clmte-offset-error', $error_msg );

                            // Add log
							clmte_create_log( "API POST request error: $error_msg", 'error' );

                            // Add purchase log
                            clmte_add_purchase( $product_quantity );

                            // Do not continue
                            return;
						}

                        ////////////////////////
                        // Purchase success
                        ////////////////////////

                        // Save how many offsets pruchased and the carbon dioxide equivalent
                        update_option( 'clmte-offsets-amount', $product_quantity );

                        update_option( 'clmte-offsets-carbon', $body->carbonDioxide );

                        // If tracking ID exists, create a tracking url
                        if (array_key_exists('trackingID', $body)) {
							$tracking_id = $body->trackingID;

							// Compose a tracking url
							$tracking_url = "https://clmte.com/track?trackingId=$tracking_id&amount=$product_quantity";

                            // Save tracking URL
                            update_option('clmte-tracking-url', $tracking_url );
						}

                        // Add purchase log
                        clmte_add_purchase( $product_quantity, 'CREATED', $body->id, $body->trackingID, $body->carbonDioxide );

                        // Flag the action as done (to avoid repetitions on reload for example)
                        $order->update_meta_data( '_clmte_offset_purchased', true );
                        $order->save();

                        // Break!
                        break;

                    }

                }

            }
        }    
    }
	
}
