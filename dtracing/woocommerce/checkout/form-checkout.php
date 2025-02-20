<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
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

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<!-- Two Column Layout Wrapper -->
		<div class="checkout-columns">

			<!-- Left Column: Billing & Shipping -->
			<div class="checkout-left">
				<div id="customer_details">
					<div class="col-1">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="col-2">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>
			</div>

			<!-- Right Column: Order Summary -->
			<div class="checkout-right">
			<h3 id="order-heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
				<div id="order_review" class="woocommerce-checkout-review-order">
					

					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>
			</div>

		</div>
		<!-- End Two Column Layout -->
		<div class="customer-details">
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		</div>
	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
