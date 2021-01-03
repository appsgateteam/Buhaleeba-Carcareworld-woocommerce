<?php
/**
 * Plain Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @version 1.0.0
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

echo $email_heading . "\n\n";

echo $email_description . "\n\n";

//Include table

wc_get_template( 'emails/plain/request-quote-table.php', array(
	'raq_data' => $raq_data
), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );


if ( ! empty( $raq_data['user_message'] ) ) {

	echo __( 'Customer\'s message', 'yith-woocommerce-request-a-quote' ) . "\n";

	echo $raq_data['user_message'] . "\n\n";
}

echo __( 'Customer\'s details', 'yith-woocommerce-request-a-quote' ) . "\n";

echo __( 'Name:', 'yith-woocommerce-request-a-quote' );
echo $raq_data['user_name'] . "\n";
echo __( 'Email:', 'yith-woocommerce-request-a-quote' );
echo $raq_data['user_email'] . "\n";


$country_code = isset( $raq_data['user_country'] ) ? $raq_data['user_country'] : '';

foreach ( $raq_data as $key => $field ) {

	$avoid_key = array( 'customer_id', 'raq_content', 'user_country', 'user_message', 'user_email', 'user_name', 'order_id', 'lang', 'message', 'user_additional_field', 'user_additional_field_2', 'user_additional_field_3' );

	if ( in_array( $key, $avoid_key ) ) {
		continue;
	}

	switch ( $field['type'] ) {

		case 'ywraq_heading':
			echo '-- ' . $field['label'] . ' --';
			break;

		case 'country':
			$countries = WC()->countries->get_countries();
			echo $field['label'] . ': ' . $countries[ $country_code ] . "\n";
			break;

		case 'state':
			$states = WC()->countries->get_states( $country_code );
			$state  = $states[ $field['value'] ];
			echo $field['label'] . ': ' . ( $state == '' ? $field['value'] : $state ) . "\n";
			break;

		case 'checkbox':
			echo $field['label'] . ': ' . ( $field['value'] == 1 ? __( 'Yes', 'yith-woocommerce-request-a-quote' ) : __( 'No', 'yith-woocommerce-request-a-quote' ) ) . "\n";
			break;

		case 'ywraq_multiselect':
			echo $field['label'] . ': ' . implode( ', ', $field['value'] ) . "\n";
			break;

		case 'ywraq_acceptance':
			$value = ( $field['value'] == 'on' ? __( 'Accepted', 'yith-woocommerce-request-a-quote' ) : __( 'Not Accepted', 'yith-woocommerce-request-a-quote' ) );
			echo $field['label'] . ': ' . $value . "\n";
			break;

		default:
			echo $field['label'] . ': ' . $field['value'] . "\n";

	}

}

if ( ! empty( $raq_data['user_additional_field'] ) || ! empty( $raq_data['user_additional_field_2'] ) || ! empty( $raq_data['user_additional_field_3'] ) ) {
	echo __( 'Customer\'s additional fields', 'yith-woocommerce-request-a-quote' );

	if ( ! empty( $raq_data['user_additional_field'] ) ) {
		echo get_option( 'ywraq_additional_text_field_label' ) . ': ' . $raq_data['user_additional_field'] . "\n";
	}

	if ( ! empty( $raq_data['user_additional_field_2'] ) ) {
		echo get_option( 'ywraq_additional_text_field_label_2' ) . ': ' . $raq_data['user_additional_field_2'] . "\n";
	}

	if ( ! empty( $raq_data['user_additional_field_3'] ) ) {
		echo get_option( 'ywraq_additional_text_field_label_3' ) . ': ' . $raq_data['user_additional_field_3'] . "\n";
	}

}


echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );