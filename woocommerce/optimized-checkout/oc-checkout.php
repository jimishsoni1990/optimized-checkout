<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       jimishsoni1990@gmail.com
 * @since      1.0.0
 *
 * @package    Owc
 * @subpackage Owc/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Owc
 * @subpackage Owc/public
 * @author     Jimish Soni <jimishsoni1990@gmail.com>
 */
class Owc_Public {

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
	private $user_id;
	private $user_obj;
	private $billing_data;
	private $shipping_data;
	private $virtual_product_checkout;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    	1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		// do not run on admin side
		if( is_admin() ){ return; }

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->virtual_product_checkout = true;

		add_action( 'init', array( $this, 'owc_hooks_n_filters' ), 99 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	function set_user(){

		if(is_user_logged_in()){

			$this->user_id 			= get_current_user_id();
			$this->user_obj 		= new WC_Customer($this->user_id);
			$this->billing_data 	= $this->user_obj->get_billing();
			$this->shipping_data 	= $this->user_obj->get_shipping();

		}

	}

	function owc_virtual_product_checkout_product_in_cart(){
		
		// perform this check only on checkout page
		if( !is_checkout() ){ return; }

		// if cart object is not available, set to false for backup
		if ( !isset(WC()->cart) ){ $this->virtual_product_checkout = false; }

	    foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	      if ( ! $cart_item['data']->is_virtual() ) $this->virtual_product_checkout = false;
	   }

	}


	function owc_hooks_n_filters(){

		add_action( 'wp', function(){
			if( is_checkout() ){
				add_filter('woocommerce_enqueue_styles', '__return_empty_array' );
			}
		}, 10);

		add_action(
			'wp', 
			array( $this, 'owc_virtual_product_checkout_product_in_cart' ), 10);

		add_action( 
			'woocommerce_login_form_start', 
			array( $this, 'woocommerce_login_popup_close_btn' ), 10 );

		remove_action( 
			'woocommerce_before_checkout_form', 
			'woocommerce_checkout_coupon_form', 10 );

		remove_action( 'woocommerce_checkout_order_review', 
			'woocommerce_checkout_payment', 20 );

		remove_action( 'woocommerce_thankyou', 
			'woocommerce_order_details_table', 10 );

		add_filter( 
			'body_class', 
			array( $this, 'owc_add_logged_in_body_class' ));
		
		add_action( 
			'owc_logged_in_user_screen', 
			array( $this, 'owc_logged_in_user_checkout_step' ),
		10, 1 );

		add_filter( 
			'woocommerce_cart_calculate_fees', 
			array( $this, 'remove_recurring_postage_fees' ),
			10, 1 );

		add_action( 
			'owc_header', 
			array( $this, 'owc_header_logo' ), 
			10 );

		add_action( 
			'owc_header', 
			array( $this, 'owc_checkout_steps' ), 
			20 );

		if( wp_is_mobile() ){
			add_action( 
				'owc_header', 
				array( $this, 'owc_mobile_order_summary' ), 
				20 );
		} else {
			add_action( 
				'owc_checkout_right_col_content', 
				array( $this, 'owc_order_summary' ), 
				10 );
		}

		add_action( 
			'woocommerce_before_checkout_shipping_form', 
			array( $this, 'owc_shipping_fields_heading' ), 
			10 );

		add_filter( 
			'woocommerce_checkout_fields' , 
			array( $this, 'owc_override_checkout_fields' ), 
			11 );

		// add inofmation box above shipping fields
		add_action( 
			'owc_information_box', 
			array( $this, 'owc_infomation_box_content' ),
			10 );

		// add inofmation box above shipping fields
		add_action( 
			'owc_shipping_method', 
			array( $this, 'owc_shipping_method_box' ),
			10 );

		add_action( 
			'owc_step_3', 
			array( $this, 'owc_billing_address_box' ),
			10 );

		add_action( 
			'owc_step_3', 
			array( $this, 'owc_payment_method_box' ),
			20 );

		// add placeholder to all fields 
		add_filter( 
			'woocommerce_form_field_args', 
			array( $this, 'owc_add_placeholder' ),
			10, 3 );

		/**
		 * Add to cart fragment
		 */
		add_filter( 'woocommerce_update_order_review_fragments', 
			array( $this, 'owc_add_shipping_method_to_fragment' )
		);

		/**
		 * Add to cart fragment
		 */
		add_filter( 'woocommerce_update_order_review_fragments', 
			array( $this, 'owc_loggin_user_infobox_shipping' )
		);

		/**
 		* Process the checkout
 		*/
		add_action( 'woocommerce_checkout_process', 
			array( $this, 'owc_checkout_process' )
		);

		/**
 		* Add coupons box
 		*/
		add_action( 'woocommerce_review_order_after_cart_contents', 
			array( $this, 'owc_add_coupon_box' )
		);

		/**
 		* Thank you page order review table
 		*/
		add_action( 'owc_thankyou_order_summary', 
			'woocommerce_order_details_table'
		);

		/**
 		* 	Add logo to thank you page
 		*/
		add_action( 'woocommerce_before_thankyou', 
			array( $this, 'owc_header_logo' )
		);

		/**
 		* 	Add prev/next button for step 1
 		*/
		add_action( 'owc_step_one_bottom', 
			array( $this, 'owc_step_one_bottom_navigation' )
		);

		/**
 		* 	Add prev/next button for step 2
 		*/
		add_action( 'owc_step_two_bottom', 
			array( $this, 'owc_step_two_bottom_navigation' )
		);

		/**
 		* 	Information reivew box for step 2
 		*/
		add_action( 'owc_step_two_top', 
			array( $this, 'owc_step_two_info_review_box' )
		);

		/**
 		* 	Information reivew box for step 3
 		*/
		add_action( 'owc_step_three_top', 
			array( $this, 'owc_step_three_info_review_box' )
		);

	}

	function owc_step_two_info_review_box(){
		if( $this->virtual_product_checkout ){ return; }
		?>
			<div class="information-review-box">
				<table>
					<tr class="contact">
						<td>Contact</td>
						<td width="100%" class="contact-email"></td>
						<td><a class="owc-information" href="#owc-information">Change</a></td>
					</tr>
					<tr class="ship-to">
						<td>Ship to</td>
						<td class="ship-to-address"></td>
						<td><a class="owc-information" href="#owc-information">Change</a></td>
					</tr>
				</table>
			</div>
		<?php
	}

	function owc_step_three_info_review_box(){
		if( $this->virtual_product_checkout ){ return; }
		?>
			<div class="information-review-box">
				<table>
					<tr class="contact">
						<td>Contact</td>
						<td width="100%" class="contact-email"></td>
						<td><a class="owc-information" href="#owc-information">Change</a></td>
					</tr>
					<tr class="ship-to">
						<td>Ship to</td>
						<td class="ship-to-address"></td>
						<td><a class="owc-information" href="#owc-information">Change</a></td>
					</tr>
					<tr class="ship-method">
						<td>Method</td>
						<td class="shipping-method"></td>
						<td><a class="owc-shipping" href="#owc-shipping">Change</a></td>
					</tr>
				</table>
			</div>
		<?php
	}

	function owc_step_one_bottom_navigation(){
		if( $this->virtual_product_checkout ){ return; }
		?>
			<div class="bottom-navigation">
				<a class="go-back-btn owc-back-to-shop" href="<?php echo wc_get_page_permalink( 'shop' ); ?>">< Return to shop</a>
				<button class="owc-shipping" type="button">Continue to shipping</button>
			</div>
		<?php
	}

	function owc_step_two_bottom_navigation(){
		if( $this->virtual_product_checkout ){ return; }
		?>
			<div class="bottom-navigation">
				<a class="go-back-btn owc-information" href="#owc-information">< Return to information</a>
				<button class="owc-payment">Continue to payment</button>
			</div>
		<?php
	}

	function woocommerce_login_popup_close_btn(){
		if( !is_checkout() ){ return; }
		echo '<a href="#" id="close-login-popup">Continue to checkout</a>';
	}

	function owc_add_logged_in_body_class( $classes ) {

		if( show_loggged_user_checkout_step() ){
			$classes[] = 'show-step-0';
		}

		if( $this->virtual_product_checkout ){
			$classes[] = 'owc-only-virtual-products-checkout';
		}

	    return $classes;
	}

	function owc_add_coupon_box(){
		$security_nonce = wp_create_nonce( "apply-coupon" );
		?>
			<p class="form-row form-row-wide" id="coupon_field">
				<label for="coupon_code" class="">
					Coupon code 
				</label>
				<span class="woocommerce-input-wrapper">
					<input type="text" class="input-text" name="coupon_code" id="coupon_code" placeholder="Enter coupon code " value="">
					<input type="hidden" class="coupon_nonce" name="coupon_nonce" value="<?php echo $security_nonce; ?>">
					<button type="button" class="apply_coupon_code">Apply</button>
				</span>
			</p>
			<div class="coupon_applied_msg"></div>
		<?php
	}

	/**
	 * Add shipping methods to fragments
	 *
	 * @since    1.0.0
	 */
	function owc_add_shipping_method_to_fragment( $fragments ) {
		global $woocommerce;

		if( is_checkout() ){

			ob_start();

			$this->owc_shipping_method_box();

			$fragments['.owc-shipping-methods-box'] = ob_get_clean();

		}
		
		return $fragments;
	}

	/**
	 * Add shipping methods to fragments
	 *
	 * @since    1.0.0
	 */
	function owc_loggin_user_infobox_shipping( $fragments ) {
		global $woocommerce;

		if( is_checkout() ){

			$fragments['.logged-in-user .information-review-box .shipping-method'] = '<td class="shipping-method">'.$this->get_selected_shipping_method().'</td>';

		}
		
		return $fragments;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, get_theme_file_uri( '/woocommerce/optimized-checkout/oc-style.css' ), array(), false, 'all' );

		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, get_theme_file_uri( '/woocommerce/optimized-checkout/oc-jquery.js' ), array( 'jquery' ), false, true );

	}

	/**
	 * 	add placeholder to woo fileds 
	 *	
	 * 	@since    1.0.0
	 */
	function owc_add_placeholder($args, $key, $value){
		if( $args['placeholder'] == '' ){
			$args['placeholder'] = $args['label'];
		}
		return $args;
	}

	/**
	 * 	Header -Logo
	 *	
	 * 	@since    1.0.0
	 */
	function owc_header_logo(){
		if ( function_exists( 'the_custom_logo' ) ) {
			echo '<div class="oc-logo">';
		 		the_custom_logo();
		 	echo '</div>';
		}	
	}

	/**
	 * 	Header - checkout steps
	 *	
	 * 	@since    1.0.0
	 */
	function owc_checkout_steps(){

		$cart_page_url = wc_get_cart_url();

		if( show_loggged_user_checkout_step() || $this->virtual_product_checkout ){

			$steps = array(
				'show-cart' => 'Cart',
				'owc-payment' => 'Payment'
			);

		} else {

			$steps = array(
				'show-cart' => 'Cart',
				'owc-information' => 'Information',
				'owc-shipping' => 'Shipping',
				'owc-payment' => 'Payment'
			);

		}

		$i = 0;
		echo '<ul class="owc-checkout-steps-wrapper">';
		foreach ($steps as $url => $name) {
			$class = ( $i == 1 ) ? ' active' : '';
			echo "<li class='owc-checkout-step'>
					<a class='$url $class' href='#$url'>$name</a>
				  </li>";
			$i++;
		}
		echo '</ul>';
	}

	function owc_shipping_fields_heading(){
		echo "<h3>Shipping address</h3>";
	}

	function owc_override_checkout_fields( $fields ){

		// Address fields
		$fields['shipping']['shipping_first_name']['placeholder'] = 'First name';
		$fields['billing']['billing_first_name']['placeholder'] = 'First name';
		
		$fields['shipping']['shipping_last_name']['placeholder'] = 'Last name';
		$fields['billing']['billing_last_name']['placeholder'] = 'Last name';
		
		$fields['shipping']['shipping_company']['placeholder'] = 'Company';
		$fields['billing']['billing_company']['placeholder'] = 'Company';
		
		$fields['shipping']['shipping_address_1']['placeholder'] = 'Street address';
		$fields['billing']['billing_address_1']['placeholder'] = 'Street address';
		
		$fields['shipping']['shipping_address_2']['label'] = 'Apartment, suite, unit, etc.';
		$fields['shipping']['shipping_address_2']['priority'] = 50;
		$fields['billing']['billing_address_2']['label'] = 'Apartment, suite, unit, etc.';
		$fields['billing']['billing_address_2']['priority'] = 50;
		
		$fields['shipping']['shipping_city']['placeholder'] = 'Town / City';
		$fields['billing']['billing_city']['placeholder'] = 'Town / City';
		
		$fields['shipping']['shipping_postcode']['placeholder'] = 'Postcode / Zip';
		$fields['billing']['billing_postcode']['placeholder'] = 'Postcode / Zip';
		
		$fields['shipping']['shipping_country']['placeholder'] = 'Country';
		$fields['billing']['billing_country']['placeholder'] = 'Country';
		
		$fields['shipping']['shipping_state']['placeholder'] = 'State / County';
		$fields['billing']['billing_state']['placeholder'] = 'State / County';

		$fields['billing']['billing_email']['placeholder'] = 'Email address';
		$fields['billing']['billing_phone']['placeholder'] = 'Phone';
		$fields['billing']['billing_phone']['required'] = false;

		$fields['order']['order_comments']['placeholder'] = 'Any delivery notes about your order. e.g. Deliver to a neighbour if Im out';

		if( $this->virtual_product_checkout ){
			unset($fields['billing']['billing_company']);
			unset($fields['billing']['billing_address_1']);
			unset($fields['billing']['billing_address_2']);
			unset($fields['billing']['billing_city']);
			unset($fields['billing']['billing_postcode']);
			unset($fields['billing']['billing_country']);
			unset($fields['billing']['billing_state']);
			unset($fields['billing']['billing_phone']);
			add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
		}

     	return $fields;

	}

	function remove_recurring_postage_fees( $cart ) {
	    if ( ! empty( $cart->recurring_cart_key ) ) {
	        remove_action( 'woocommerce_cart_totals_after_order_total', array( 'WC_Subscriptions_Cart', 'display_recurring_totals' ), 10 );
	        remove_action( 'woocommerce_review_order_after_order_total', array( 'WC_Subscriptions_Cart', 'display_recurring_totals' ), 10 );
	    }
	}

	function owc_infomation_box_content($checkout){
		$fields = $checkout->get_checkout_fields( 'billing' );
		$field = $fields['billing_email'];
		$key = "billing_email";
		?>
			<div class="inofmation-box">
				<div class="inofmation-box-header">
					<h3>
						<?php
							if( $this->virtual_product_checkout ){
								echo 'Billing Information';
							} else {
								echo 'Information';
							}
						?>
					</h3>
					<?php if( !is_user_logged_in() ): ?>
						<p class="login-msg">Already have an account? <a href="#show-account">Log in</a></p>
					<?php endif; ?>
				</div>
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php do_action('owc_information_box_end'); ?>
			</div>
		<?php
		
	}

	function owc_shipping_method_box(){

		if( $this->virtual_product_checkout ){ return; }

		$count = 0;

		if( isset( WC()->shipping->packages[0] ) ){
			$count = count(WC()->shipping->packages[0]['rates']);
		}
		?>
			<div class="owc-shipping-methods-box <?php echo ($count == 1 ) ? 'one-method-only' : ''; ?>">
				<h3>Shipping Methods</h3>
				<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

					<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

			</div>
		<?php
	}

	function owc_billing_address_box($checkout){

		if( $this->virtual_product_checkout ){
			$this->owc_virtual_checkout_billing_address_box($checkout);
		} else {
		
			do_action( 'woocommerce_before_checkout_billing_form', $checkout ); 
		?>

			<div class="owc-billing-address-box">
				<h3>Billing address</h3>

				<ul id="billing_address_choice" class="woocommerce-shipping-methods">
					<li>
						<input 
							type="radio" 
							name="billing_address_choice" 
							id="billing_address_choice_same" 
							value="same_billing" 
							class="billing_address_choice"
							checked="checked" 
						>
						<label for="billing_address_choice_same">Same as shipping address</label>					
					</li>
					<li>
						<input 
							type="radio" 
							name="billing_address_choice" 
							id="billing_address_choice_different" 
							value="different_billing" 
							class="billing_address_choice"
						>
						<label for="billing_address_choice_different">Use a different billing address</label>	

						<div class="billing-fields">
							<div class="woocommerce-billing-fields__field-wrapper">
								<?php
								$fields = $checkout->get_checkout_fields( 'billing' );

								foreach ( $fields as $key => $field ) {
									if($key == 'billing_email'){
										continue;
									}
									woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
								}
								?>
							</div>	
						</div>
					</li>
				</ul>

			</div>

		<?php 
			do_action( 'woocommerce_after_checkout_billing_form', $checkout );
		}
	}

	function owc_virtual_checkout_billing_address_box($checkout){
		?>

		<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

			<div class="owc-billing-address-box">
				<div class="woocommerce-billing-fields__field-wrapper">
					<?php
					$fields = $checkout->get_checkout_fields( 'billing' );

					foreach ( $fields as $key => $field ) {
						if($key == 'billing_email'){
							continue;
						}
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					}
					?>
				</div>	
			</div>

			<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>

		<?php
	}

	function owc_payment_method_box(){
		?>
			<div class="owc-payment-methods-box">
				<h3>Payment</h3>
				<?php woocommerce_checkout_payment(); ?>
			</div>
		<?php
	}

	/*
	*	Since billing fields are manadatory in woo, 
	*	lets copy ship field data to billing is user
	*	select to have the same adderess.
	*/
	function owc_checkout_process() {
	    
	    if( $_POST['billing_address_choice'] == 'same_billing' ){
	    	$_POST['billing_first_name'] 	= $_POST['shipping_first_name'];
	    	$_POST['billing_last_name'] 	= $_POST['shipping_last_name'];
	    	$_POST['billing_company'] 		= $_POST['shipping_company'];
	    	$_POST['billing_country'] 		= $_POST['shipping_country'];
	    	$_POST['billing_address_1'] 	= $_POST['shipping_address_1'];
	    	$_POST['billing_address_2'] 	= $_POST['shipping_address_2'];
	    	$_POST['billing_city'] 			= $_POST['shipping_city'];
	    	$_POST['billing_state'] 		= $_POST['shipping_state'];
	    	$_POST['billing_postcode'] 		= $_POST['shipping_postcode'];
	    }

	}

	function owc_order_summary(){
		?>
			<div class="content-wrapper owc-order-summary">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
				<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
				
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>
		<?php
	}

	function owc_mobile_order_summary(){
		?>
			<a href="#" class="open-summary-box">Have coupon code or gift card?</a>
			<div class="owc-mobile-order-summary">
				<div class="summary-header">
					<div>Show order summary</div>
					<div><?php wc_cart_totals_order_total_html(); ?></div>
				</div>
				<?php echo $this->owc_order_summary(); ?>
			</div>
		<?php
	}

	function formatted_billing_address(){
		$address = 'Same as shipping address';
		echo $address;
	}

	function formatted_shipping_address(){
		$address = '';
		
		if( $this->shipping_data['address_1'] != '' ){
			$address_data = $this->shipping_data;	
		} else {
			$address_data = $this->billing_data;	
		}

		$address .= $address_data['first_name']. ' ' .$address_data['last_name'].', ';
		

		if( $address_data['company'] != '' ){
			$address .= $address_data['company']. ', ';
		}

		$address .= $address_data['address_1']. ', ';

		if( $address_data['address_2'] != '' ){
			$address .= $address_data['address_2']. ', ';
		}

		$address .= $address_data['city']. ', ' 
					.$address_data['postcode']. ', '
					.$address_data['country'];

		echo $address;
	}

	function get_selected_shipping_method(){

		if( isset(WC()->session->get('shipping_for_package_0')['rates']) ){
			foreach( WC()->session->get('shipping_for_package_0')['rates'] as $method_id => $rate ){
			    if( WC()->session->get('chosen_shipping_methods')[0] == $method_id ){
			        $rate_label = $rate->label; // The shipping method label name
			        $rate_cost_excl_tax = floatval($rate->cost); // The cost excluding tax
			        // The taxes cost
			        $rate_taxes = 0;
			        foreach ($rate->taxes as $rate_tax)
			            $rate_taxes += floatval($rate_tax);
			        // The cost including tax
			        $rate_cost_incl_tax = $rate_cost_excl_tax + $rate_taxes;

			        $method = $rate_label.': '.WC()->cart->get_cart_shipping_total();
			        break;
			    }
			}
		} else {
			$method = 'No shipping method selected.';
		}

		return $method;
	}

	function owc_logged_in_user_checkout_step(){
		$this->set_user();
		$style = 1;
		if( $style == 1){
		?>

			<div class="information-review-box">
				<table>
					<tr class="contact">
						<th>Contact</th>
						<td width="100%" class="contact-email"><?php echo $this->user_obj->get_billing_email(); ?></td>
						<td><a class="owc-information" href="#">Change</a></td>
					</tr>
					<tr class="ship-to">
						<th>Ship to</th>
						<td class="ship-to-address"><?php $this->formatted_shipping_address(); ?></td>
						<td><a class="owc-information" href="#">Change</a></td>
					</tr>
					<tr class="bill-to">
						<th>Bill to</th>
						<td class="bill-to-address"><?php $this->formatted_billing_address(); ?></td>
						<td><a class="owc-payment" href="#">Change</a></td>
					</tr>
					<tr class="ship-method">
						<th>Method</th>
						<td class="shipping-method"><?php echo $this->get_selected_shipping_method(); ?></td>
						<td><a class="owc-shipping" href="#">Change</a></td>
					</tr>
				</table>
			</div>

		<?php }else { ?>

			<div class="contact">
				<h3>Contact Information</h3>
				<div class="saved-info">
					<p><?php echo $this->user_obj->get_billing_email(); ?></p>
					<a href="#">Change</a>
				</div>
			</div>

			<div class="shipping-address">
				<h3>Billing & Shipping address</h3>
				<div class="saved-info">
					<p><?php echo $this->formatted_shipping_address(); ?></p>
					<a href="#">Change</a>
				</div>
			</div>

		<?php } ?>

			<div class="payment">
				<h3>Payment</h3>
				<?php woocommerce_checkout_payment(); ?>
			</div>
		<?php
	}
}

new Owc_Public( 'optimized-checkout', '1.0' );

function show_loggged_user_checkout_step(){

	if( is_user_logged_in() ){

		// return true only if user has saved shipping / billing address
		// shipping address has more priority
		$user_id 		= get_current_user_id();
		$user_obj 		= new WC_Customer($user_id);
		$billing_data 	= $user_obj->get_billing();
		$shipping_data 	= $user_obj->get_shipping();

		if( $shipping_data['address_1'] != '' || $billing_data['address_1'] != '' ){
			return true;
		}
	}

	return false;
}

add_filter( 'template_include', 'owc_checkout_page_template', 99 );
function owc_checkout_page_template( $template ) {

	if ( is_checkout()  ) {

        $new_template = locate_template( array( '/woocommerce/optimized-checkout/checkout-template.php' ) );

		if ( '' != $new_template ) {
		    return $new_template ;
		}
    }
    return $template;
}
