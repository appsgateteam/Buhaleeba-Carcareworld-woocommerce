<?php
/**
 * Plain Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @version 2.0.8
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo $email_heading . "\n\n";

$quote_number = apply_filters( 'ywraq_quote_number', $raq_data['order-number'] );
echo sprintf( __( '%s n. %d', 'yith-woocommerce-request-a-quote' ), apply_filters( 'wpml_translate_single_string', $email_title, 'admin_texts_woocommerce_ywraq_send_quote_settings', '[woocommerce_ywraq_send_quote_settings]email-title', $raq_data['lang'] ), $quote_number ) . "\n\n";

echo apply_filters( 'wpml_translate_single_string', $email_description, 'admin_texts_woocommerce_ywraq_send_quote_settings', '[woocommerce_ywraq_send_quote_settings]email-description', $raq_data['lang'] ) . "\n\n";

echo sprintf( __( 'Request date: %s', 'yith-woocommerce-request-a-quote' ), $raq_data['order-date'] ) . "\n\n";

if ( $raq_data['expiration_data'] != '' ) {
	echo sprintf( __( 'Expiration date: %s', 'yith-woocommerce-request-a-quote' ), $raq_data['expiration_data-date'] ) . "\n\n";
}

if ( ! empty( $raq_data['admin_message'] ) ) {
	echo $raq_data['admin_message'] . "\n\n";
}

//Include table
wc_get_template( 'emails/plain/quote-table.php', array( 'order' => $order ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );

if ( get_option( 'ywraq_show_accept_link' ) != 'no' ) {
	echo ywraq_get_label( 'accept' ) . "\n";
	echo esc_url(
		add_query_arg(
			array(
				'request_quote' => $raq_data['order-number'],
				'status'        => 'accepted',
				'lang'          => yit_get_prop( $order, 'wpml_language', true ),
				'raq_nonce'     => ywraq_get_token( 'accept-request-quote', $raq_data['order-number'], $raq_data['user_email'] )
			), YITH_Request_Quote()->get_raq_page_url()
		)
	);
	echo "\n\n";
}

if ( get_option( 'ywraq_show_reject_link' ) != 'no' ) {
	echo ywraq_get_label( 'reject' ) . "\n";
	echo esc_url( ywraq_get_rejected_quote_page( $order ) );
	echo "\n\n";
}

if ( ( $after_list = yit_get_prop( $order, '_ywraq_request_response_after', true ) ) != '' ) {
	echo apply_filters( 'ywraq_quote_after_list', $after_list, $raq_data['order-id'] ) . "\n\n";
}

$user_name       = yit_get_prop( $order, 'ywraq_customer_name', true );
$user_email      = yit_get_prop( $order, 'ywraq_customer_email', true );
$billing_company = yit_get_prop( $order, '_billing_company', true );

$billing_address_1 = yit_get_prop( $order, '_billing_address_1', true );
$billing_address_2 = yit_get_prop( $order, '_billing_address_2', true );

$billing_address = $billing_address_1 . ( ( $billing_address_1 != '' ) ? ' ' : '' ) . $billing_address_2;
if ( $billing_address == '' ) {
	$billing_address = yit_get_prop( $order, 'ywraq_billing_address', true );
}

$billing_city     = yit_get_prop( $order, '_billing_city', true );
$billing_postcode = yit_get_prop( $order, '_billing_postcode', true );
$billing_country  = yit_get_prop( $order, '_billing_country', true );
$billing_state    = yit_get_prop( $order, '_billing_state', true );
$billing_phone    = yit_get_prop( $order, 'ywraq_billing_phone', true );
$billing_phone    = empty( $billing_phone ) ? yit_get_prop( $order, '_billing_phone', true ) : $billing_phone;
$billing_vat      = yit_get_prop( $order, 'ywraq_billing_vat', true );
$billing_vat      = empty( $billing_vat ) ? yit_get_prop( $order, '_billing_vat', true ) : $billing_vat;


echo __( 'Customer\'s details', 'yith-woocommerce-request-a-quote' ) . "\n";

echo __( 'Name:', 'yith-woocommerce-request-a-quote' );
echo $user_name . "\n";

if ( $billing_company != '' ) {
	echo __( 'Company:', 'yith-woocommerce-request-a-quote' );
	echo $billing_company . "\n";
}
if ( $billing_address != '' ) {
	echo __( 'Address:', 'yith-woocommerce-request-a-quote' );
	echo $billing_address . "\n";
}
if ( $billing_city != '' ) {
	echo __( 'City:', 'yith-woocommerce-request-a-quote' );
	echo $billing_city . "\n";
}
if ( $billing_postcode != '' ) {
	echo __( 'Postcode:', 'yith-woocommerce-request-a-quote' );
	echo $billing_postcode . "\n";
}
if ( $billing_state != '' ) {

	if ( $billing_country != '' ) {
		$states        = WC()->countries->get_states( $billing_country );
		$billing_state = ( $states[ $billing_state ] != '' ? $states[ $billing_state ] : $billing_state );
	}

	echo __( 'State:', 'yith-woocommerce-request-a-quote' );
	echo $billing_state . "\n";
}
if ( $billing_country != '' ) {

	$countries = WC()->countries->get_countries();

	echo __( 'Country:', 'yith-woocommerce-request-a-quote' );
	echo $countries[ $billing_country ] . "\n";

}
echo __( 'Email:', 'yith-woocommerce-request-a-quote' );
echo $user_email . "\n";

if ( $billing_phone != '' ) {
	echo __( 'Billing Phone:', 'yith-woocommerce-request-a-quote' );
	echo $billing_phone . "\n";
}

if ( $billing_vat != '' ) {
	echo __( 'Billing VAT:', 'yith-woocommerce-request-a-quote' );
	echo $billing_vat . "\n";
}

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

