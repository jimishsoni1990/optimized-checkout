<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>
<div id="owc-checkout-wrapper">
	<div class="owc-container">
		<form name="checkout" method="post" class="checkout woocommerce-checkout row" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
			<div class="owc-row">
				<div class="owc-col left-container">

					<?php do_action( 'owc_header', $checkout ); ?>	

					<?php if( show_loggged_user_checkout_step() ): ?>

						<div class="step-0 step-content logged-in-user">

							<?php do_action( 'owc_logged_in_user_screen', $checkout ); ?>

						</div>

					<?php endif; ?>

					<div class="step-1 step-content">

						<?php do_action( 'owc_step_one_top', $checkout ); ?>

						<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

						<?php do_action( 'owc_information_box', $checkout ); ?>

						<?php do_action( 'woocommerce_checkout_shipping' ); ?>

						<?php do_action( 'owc_step_one_bottom', $checkout ); ?>

					</div>

					<div class="step-2 step-content">

						<?php do_action( 'owc_step_two_top', $checkout ); ?>

						<?php do_action( 'owc_shipping_method', $checkout ); ?>
						
						<?php do_action( 'owc_step_two_bottom', $checkout ); ?>

					</div>

					<div class="step-3 step-content">

						<?php do_action( 'owc_step_three_top', $checkout ); ?>

						<?php do_action( 'owc_step_3', $checkout ); ?>

						<?php do_action( 'owc_step_three_bottom', $checkout ); ?>

					</div>
					
					<?php do_action( 'owc_left_container_footer', $checkout ); ?>

				</div>

				<div class="owc-col right-container">
					<?php do_action( 'owc_checkout_right_col_content', $checkout ); ?>
				</div>

			</div>
		</form>
	</div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
