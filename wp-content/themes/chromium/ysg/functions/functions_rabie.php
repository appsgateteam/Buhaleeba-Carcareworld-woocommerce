<?php

add_filter( 'woocommerce_cart_item_name', 'add_sku_in_cart', 20, 3);

function add_sku_in_cart( $title, $values, $cart_item_key ) {
    $sku = $values['data']->get_sku();
    return $sku ? $title . sprintf(" (SKU: %s)", $sku) : $title;
}




function kmchild_wps_get_store_custom_fields($fields) {
	$fields[] = 'description';
	return $fields;
}
add_filter('wps_get_store_custom_fields', 'kmchild_wps_get_store_custom_fields');


add_filter('woocommerce_package_rates', 'pax_remove_shipping_options_for_particular_country', 10, 2);

function pax_remove_shipping_options_for_particular_country($available_shipping_methods){

	if ( is_admin() ) return $available_shipping_methods;

	if ( isset( $available_shipping_methods['wc_pickup_store'] ) && WC()->customer->get_shipping_country() <> 'AE' ) {
        unset( $available_shipping_methods['wc_pickup_store'] );
    }
    return $available_shipping_methods;
}




function autocoupon_function() {
$code_value = wp_generate_password( 15, false );
$coupon_code   = $code_value;  // Code created using the random string snippet.
$amount = '10'; // Amount
$discount_type = 'percent'; // Type: fixed_cart, percent, fixed_product, percent_product

$coupon = array(
'post_title' => $coupon_code,
'post_content' => '',
'post_status' => 'publish',
'post_author' => 1,
'post_type' => 'shop_coupon'
);

$new_coupon_id = wp_insert_post( $coupon );
	$today=date('d-m-Y');
$expiry_date= date('d-m-Y', strtotime($today. ' + 60 days'));


// Add meta
update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
update_post_meta( $new_coupon_id, 'individual_use', 'no' );
update_post_meta( $new_coupon_id, 'product_ids', '' );
update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
update_post_meta( $new_coupon_id, 'usage_limit', '1' );
update_post_meta( $new_coupon_id, 'expiry_date', $expiry_date );
update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

return $coupon_code;
}

add_shortcode('autocoupon', 'autocoupon_function');





/**
 * Run shortcodes in all the places where smart tags are processed.
 * @link   https://wpforms.com/developers/how-to-display-shortcodes-inside-the-confirmation-message/
 *
 */
function wpf_smart_tags_shortcodes( $content ) {
     
    return do_shortcode( $content );
}
add_filter( 'wpforms_process_smart_tags', 'wpf_smart_tags_shortcodes', 12, 1 );






add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){

  wp_redirect( home_url() );
  exit();


}

// Billing and Shipping fields on my account edit-addresses and checkout
add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );
function custom_override_default_address_fields( $fields ) {
    $fields['first_name']['label'] = 'First name';
    $fields['last_name']['label'] = 'Last name';
    $fields['company']['label'] = 'Company name';
    $fields['address_1']['label'] = 'Street address';
    $fields['address_2']['label'] = 'Apartment, unit, etc.';
    $fields['city']['label'] = 'City';
    $fields['country']['label'] = 'Country';
    $fields['state']['label'] = 'County/State';
    $fields['postcode']['label'] = 'Postcode';

    return $fields;
}