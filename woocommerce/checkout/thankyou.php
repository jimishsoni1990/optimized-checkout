<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order">
	<div class="owc-container">	
		<div class="owc-row">
			<div class="owc-col left-container">
				<?php
				if ( $order ) :

					do_action( 'woocommerce_before_thankyou', $order->get_id() );
					?>

					<?php if ( $order->has_status( 'failed' ) ) : ?>

						<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
							<span>Order #<?php echo $order->get_order_number(); ?></span>
							<span>
							<?php 
							$first_name = $order->get_billing_first_name();
							esc_html_e( "Oops! your order can not be processed., $first_name!", 'woocommerce' ); ?></p>
							</span>

						<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
							<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
							<?php if ( is_user_logged_in() ) : ?>
								<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
							<?php endif; ?>
						</p>

					<?php else : ?>

						<div class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
							<svg width="60px" height="60px" viewBox="0 0 24 24" id="check-mark-circle" xmlns="http://www.w3.org/2000/svg" class="icon line"><path id="primary" d="M12,21h0a9,9,0,0,1-9-9H3a9,9,0,0,1,9-9h0a9,9,0,0,1,9,9h0A9,9,0,0,1,12,21ZM8,11.5l3,3,5-5" style="fill: none; stroke: var(--oc-thank-you-check-color); stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5;"></path></svg>
							<div class="order-info">
								<span>Order #<?php echo $order->get_order_number(); ?></span>
								<span>
								<?php 
									$first_name = $order->get_billing_first_name();
									echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( "Thank you $first_name!", 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</div>
						</div>

						<div class="owc-box">
							<h3>Your order is confirmed</h3>
							<p>You'll receive a confirmation email with your order number shortly.</p>
						</div>

						<div class="owc-box">
							<h3>Order Updates</h3>
							<p>You'll get shipping and delivery updates by email.</p>
						</div>

						<div class="owc-box">
							<h3>Customer Information</h3>
							<div class="owc-row">
								<div class="owc-col">

									<h4>Contant information</h4>
									<p class="order-info-value"><?php echo $order->get_billing_email(); ?></p>

									<h4>Shipping address</h4>
									<p class="order-info-value"><?php echo $order->get_formatted_shipping_address(); ?></p>

									<h4>Shipping Method</h4>
									<p class="order-info-value"><?php echo $order->get_shipping_to_display(); ?></p>

								</div>
								<div class="owc-col">

									<h4>Payment method</h4>
									<p class="order-info-value">
										<?php
											echo ($order->get_payment_method_title()) ? $order->get_payment_method_title() : 'Free';
											echo ' - '.$order->get_formatted_order_total();
										?>
									</p>

									<h4>Billing address</h4>
									<p class="order-info-value"><?php echo $order->get_formatted_billing_address(); ?></p>
								</div>
							</div>
						</div>

					<?php endif; ?>

					<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
					<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

					<div class="bottom-navigation">
						<a class="" href="#"></a>
						<a class="owc-contimue-shopping button" href="<?php echo get_site_url(); ?>">Continue Shopping</a>
					</div>

				<?php else : ?>

					<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

				<?php endif; ?>
			</div>
			<div class="owc-col right-container">
				<div class="content-wrapper owc-order-summary">
					<?php do_action('owc_thankyou_order_summary', $order); ?>
				</div>
			</div>
		</div>
	</div>
</div>