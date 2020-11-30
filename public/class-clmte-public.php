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
			'compensation_product_id' => get_option('clmte_compensation_product_id')
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
		if ( get_option( 'clmte_custom_placement' ) == false or get_option( 'clmte_custom_placement' ) == 'no') {

			// Create the clmte offset box
			clmte_create_offset_box();

		}
	}

	/**
	 * Check if compensation has been purchased, send API tundra post request
	 *
	 * @since    1.0.0
	 */
	public function send_tundra_request( $order_id ) {
		if ( ! $order_id )
			return;
	
		// Allow code execution only once 
		if( ! get_post_meta( $order_id, '_thankyou_action_done', true ) ) {
	
			// Get an instance of the WC_Order object
			$order = wc_get_order( $order_id );
	
			// Get the order key
			$order_key = $order->get_order_key();
	
			// Get the order number
			$order_key = $order->get_order_number();
	
			if($order->is_paid()) {

				// Loop through order items
				foreach ( $order->get_items() as $item_id => $item ) {
		
					// Get the product object
					$product = $item->get_product();
		
					// Get the product Id
					$product_id = $product->get_id();

					if ($product_id == get_option('clmte_compensation_product_id')) {

						// API

						// Get the product quantity
						$product_quantity = $item->get_quantity();

						// Use correct grammar, 1 offset / multiple offsets
						$offset_string = $product_quantity > 1 ? 'offsets' : 'offset';	

						$offset_error = False;

						// Send request to CLMTE tundra API
						$data = array( 'amount' => $product_quantity );
						$data_string = http_build_query($data);

						$url = "https://api-sandbox.tundra.clmte.com/compensation";

						$api_key = get_option('clmte_api_key');
			
						$header = array();
						$header[] = 'Content-length: 0';
						$header[] = 'Content-type: application/json'; 
						$header[] = 'Authorization: APIKey ' . $api_key;

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_POST, true);
    					curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

						$response = curl_exec($ch);
						if (curl_errno($ch)) {
							$error_msg = curl_error($ch);
							$offset_error = True;
						}
						curl_close($ch);
		
						$data = json_decode($response);

						if (array_key_exists('errors', $data)) {
							$offset_error = True;
						}

						// Show error message if error occured
						if ($offset_error) {
							echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
								<li class="woocommerce-order-overview__order order">
								CARBON OFFSETS PURCHASED
								<strong>' . $product_quantity . ' '. $offset_string .'</strong>
								<strong style="font-size: 14px">
								Something went wrong when adding your carbon '. $offset_string .' to the CLMTE database. Please email support@clmte.com to get your '. $offset_string .' added manually.
								</strong>
								</td>
								</ul>';

							// Do not show other messages
							break;
						}

						// Extract tracking id
						if (array_key_exists('trackingID', $data)) {
							$tracking_id = $data->trackingID;

							// Compose a tracking url
							$tracking_url = "https://clmte.com/track-compensation/?trackingID=$tracking_id&amount=$product_quantity";
						}
		
						// Create section to display CLMTE information
						echo '<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
								<li class="woocommerce-order-overview__order order">
								CARBON OFFSETS PURCHASED
								<strong>' . $product_quantity . ' '. $offset_string .', equivalent to '. $data->carbonDioxide .'kg carbon dioxide</strong>
								</li>';

						// Only display tracking section if trackingId exists
						if (isset($tracking_url)) {
							echo '<li class="woocommerce-order-overview__order 	order">
							TRACK YOUR CARBON OFFSET

							<img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl='. $tracking_url .'&choe=UTF-8" title="Link to clmte.com" />
							<strong style="font-size: 14px;">
							Scan or go to <a rel="nofollow" target="_blank" href="'. $tracking_url .'">the tracking page for your offset</a> to track your carbon offset.
							</strong>
							</li>';
						}

						// Close of the CLMTE information
						echo '
							<li class="woocommerce-order-overview__order 	order">
							
							READ MORE AT <a rel="nofollow" href="https://clmte.com" target="_blank">CLMTE.COM</a>
								
								</li>
							</ul>';

						// Prevent code from running multiple times
						break;
					}

				}

			}
	
			// Flag the action as done (to avoid repetitions on reload for example)
			$order->update_meta_data( '_thankyou_action_done', true );
			$order->save();
		}
	}
	

}
