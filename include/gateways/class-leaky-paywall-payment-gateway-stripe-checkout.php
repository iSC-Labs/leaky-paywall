<?php
/**
 * Stripe Checkout Payment Gateway Class
 *
 * @package     Leaky Paywall
 * @subpackage  Classes/Roles
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.0.0
*/

class Leaky_Paywall_Payment_Gateway_Stripe_Checkout extends Leaky_Paywall_Payment_Gateway_Stripe {

	/**
	 * Process registration
	 *
	 * @since 4.0.0
	 */
	public function process_confirmation() {

		if ( empty( $_GET['leaky-paywall-confirm'] ) && $_GET['leaky-paywall-confirm'] != 'stripe_checkout' ) {
			return false;
		}

		$settings = get_leaky_paywall_settings();

		$this->email = $_POST['stripeEmail'];
		$this->level_id = $_POST['custom'];

		$level = get_leaky_paywall_subscription_level( $this->level_id );

		$this->level_name  = $level['label'];
		$this->recurring   = !empty( $level['recurring'] ) ? $level['recurring'] : false;
		$this->plan_id     = !empty( $level['plan_id'] ) ? $level['plan_id'] : false;
		$this->level_price = $level['price'];

		// @todo: Fix: this will ignore coupons
		$this->amount      = $level['price'];
		$this->currency    = leaky_paywall_get_currency();
		$this->length_unit = $level['interval'];
		$this->length      = $level['interval_count'];

		if ( ! class_exists( 'Stripe' ) ) {
			require_once LEAKY_PAYWALL_PATH . 'include/stripe/lib/Stripe.php';
		}

		$subscriber_data = parent::process_signup();

		do_action( 'leaky_paywall_after_process_stripe_checkout', $subscriber_data );

		leaky_paywall_subscriber_registration( $subscriber_data );

	}

	/**
	 * Add credit card fields
	 *
	 * @since 4.0.0
	 */
	public function fields() {

	}

}