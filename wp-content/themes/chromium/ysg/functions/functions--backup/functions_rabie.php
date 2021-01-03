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




















// HERE are is the array of cities for United Arab Emirates (AE)
function get_cities_options(){
    $domain = 'woocommerce'; // The domain text slug

    return array(
        ''          => __('Select a city', $domain),
        'Abu-Dhabi'      => 'Abu Dhabi',      'Dubai'    => 'Dubai',
        'Sharjah'  => 'Sharjah',  'Ajman' => 'Ajman',
        'Umm-Al-Quwain'  => 'Umm Al Quwain',  'Ras-Al-Khaimah'   => 'Ras Al Khaimah', 'Fujairah'   => 'Fujairah',

    );
}

// add an additional field
add_filter( 'woocommerce_checkout_fields' , 'additional_checkout_city_field' );
function additional_checkout_city_field( $fields ) {
    // Inline CSS To hide the fields on start
    ?><style> #billing_city2_field.hidden, #shipping_city2_field.hidden {display:none;}</style><?php

    $fields['billing']['billing_city2'] = array(
        'placeholder'   => _x('Other city', 'placeholder', 'woocommerce'),
        'required'  => false,
        'priority'  => 75,
        'class'     => array('form-row-wide hidden'),
        'clear'     => true
    );

    $fields['shipping']['shipping_city2'] = array(
        'placeholder'   => _x('Other city', 'placeholder', 'woocommerce'),
        'required'  => false,
        'priority'  => 75,
        'class'     => array('form-row-wide hidden'),
        'clear'     => true
    );

    return $fields;
}

// Add checkout custom select fields
add_action( 'wp_footer', 'custom_checkout_city_field', 20, 1 );
function custom_checkout_city_field() {
    // Only checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ):

    $country = 'AE'; //  <=== <=== The country code

    $b_city  = 'billing_city';
    $s_city  = 'shipping_city';
    $billing_city_compo    = 'name="'.$b_city.'" id="'.$b_city.'"';
    $shipping_city_compo   = 'name="'.$s_city.'" id="'.$s_city.'"';
    $end_of_field          = ' autocomplete="address-level2" value="">';
    $billing_text_field    = '<input type="text" class="input-text" ' . $billing_city_compo  . $end_of_field;
    $shipping_text_field   = '<input type="text" class="input-text" ' . $shipping_city_compo . $end_of_field;
    $billing_select_field  = '<select ' . $billing_city_compo  . $end_of_field;
    $shipping_select_field = '<select ' . $shipping_city_compo . $end_of_field;

    ?>
    <script type="text/javascript">
    jQuery(function($){
        var a   = <?php echo json_encode( get_cities_options() ); ?>,           fc = 'form.checkout',
            b   = 'billing',                s   = 'shipping',               ci = '_city2',
            bc  = '<?php echo $b_city; ?>', sc = '<?php echo $s_city; ?>',  co = '_country',
            bci = '#'+bc,                   sci = '#'+sc,                   fi = '_field',
            btf = '<?php echo $billing_text_field; ?>',     stf = '<?php echo $shipping_text_field; ?>',
            bsf = '<?php echo $billing_select_field; ?>',   ssf = '<?php echo $shipping_select_field; ?>',
            cc  = '<?php echo $country; ?>';

        // Utility function that fill dynamically the select field options
        function dynamicSelectOptions( type ){
            var select = (type == b) ? bsf : ssf,
                fvalue = (type == b) ? $(bci).val() : $(sci).val();


            $.each( a, function( key, value ){
                selected = ( fvalue == key ) ? ' selected' : '';
                selected = ( ( fvalue == '' || fvalue == undefined ) && key == '' ) ? ' selected' : selected;
                select += '<option value="'+key+'"'+selected+'>'+value+'</option>';
            });
            select += '</select>';

            if ( type == b ) 
                $(bci).replaceWith(select);
            else 
                $(sci).replaceWith(select);
        }

        // Utility function that will show / hide the "country2" additional text field
        function showHideCity2( type, city ){
            var field   = (type == b) ? bci : sci,
                country = $('#'+type+co).val();

            if( country == cc && city == 'Other' && $('#'+type+ci+fi).hasClass('hidden') ){
                $('#'+type+ci+fi).removeClass('hidden');
            } else if( country != cc || ( city != 'Other' && ! $('#'+type+ci+fi).hasClass('hidden') ) ) {
                $('#'+type+ci+fi).addClass('hidden');
                if( country != cc && city == 'Other' ){
                    $(field).val('');
                }
            }
        }

        // On billing country change
        $(fc).on('change', '#'+b+co, function(){
            var bcv = $(bci).val();
            if($(this).val() == cc){
                if( $(bci).attr('type') == 'text' ){
                    dynamicSelectOptions(b);
                    showHideCity2( b, $(bci).val() );
                }
            } else {
                if( $(bci).attr('type') != 'text' ){
                    $(bci).replaceWith(btf);
                    $(bci).val(bcv);
                    showHideCity2( b, $(bci).val() );
                }
            }
        });

        // On shipping country change
        $(fc).on('change', '#'+s+co, function(){
            var scv = $(sc).val();
            if($(this).val() == cc){
                if( $(sci).attr('type') == 'text' ){
                    dynamicSelectOptions(s);
                    showHideCity2( s, $(sci).val() );
                }
            } else {
                if( $(sci).attr('type') != 'text' ){
                    $(sci).replaceWith(stf);
                    $(sci).val(scv);
                    showHideCity2( s, $(sci).val() );
                }
            }
        });

        // On billing city change
        $(fc).on('change', bci, function(){
            showHideCity2( b, $(this).val() );
        });

        // On shipping city change
        $(fc).on('change', sci, function(){
            showHideCity2( s, $(this).val() );
        });
    });
    </script>
    <?php
    endif;
}

// Check  for city 2 fields if billing or/and shipping city fields is "Other"
add_action('woocommerce_checkout_process', 'cbi_cf_process');
function cbi_cf_process() {
    // Check billing city 2 field
    if( isset($_POST['billing_city2']) && empty($_POST['billing_city2']) && $_POST['billing_city'] == 'Other' ){
        wc_add_notice( __( "Please fill in billing city field" ), "error" );
    }

    // Updating shipping city 2 field
    if( isset($_POST['shipping_city2']) && empty($_POST['shipping_city2']) && $_POST['shipping_city'] == 'Other' ){
        wc_add_notice( __( "Please fill in shipping city field" ), "error" );
    }
}

// Updating billing and shipping city fields when using "Other"
add_action( 'woocommerce_checkout_create_order', 'update_order_city_field', 30, 2 );
function update_order_city_field( $order, $posted_data ) {
    // Updating billing city from 'billing_city2'
    if( isset($_POST['billing_city2']) && ! empty($_POST['billing_city2']) && $_POST['billing_city'] == 'Other' ){
        $order->set_billing_city(sanitize_text_field( $_POST['billing_city2'] ) );
    }

    // Updating shipping city
    if( isset($_POST['shipping_city2']) && ! empty($_POST['shipping_city2']) && $_POST['shipping_city'] == 'Other' ){
        $order->set_shipping_city(sanitize_text_field( $_POST['shipping_city'] ) );
    }
}

