<?php

add_filter('woocommerce_checkout_fields', 'ysg_reorder_checkout_fields');

function ysg_reorder_checkout_fields($fields)
{
    $fields['billing']['billing_email']['priority'] = 25;
    $fields['billing']['billing_email']['class'] = array('form-row-first');

    //email
    $fields['billing']['billing_phone']['priority'] = 26;
    $fields['billing']['billing_phone']['class'] = array('form-row-last', 'float-right-important');


    $fields['billing']['billing_country']['priority'] = $fields['shipping']['shipping_country']['priority'] = 35;
    $fields['billing']['billing_country']['label'] = $fields['shipping']['shipping_country']['label'] = 'Country';
    //state
    $fields['billing']['billing_state']['priority'] = $fields['shipping']['shipping_state']['priority'] = 36;
    $fields['billing']['billing_state']['label'] = $fields['shipping']['shipping_state']['label'] = "Emirates / State";

    //city
    $fields['billing']['billing_city']['priority'] = $fields['shipping']['shipping_city']['priority'] = 37;
    $fields['billing']['billing_city']['label'] = $fields['shipping']['shipping_city']['label'] = "Area / City";

    //address
    $fields['billing']['billing_address_1']['priority'] = $fields['shipping']['shipping_address_1']['priority'] = 38;
    $fields['billing']['billing_address_2']['priority'] = $fields['shipping']['shipping_address_2']['priority'] = 39;

    /*echo "<pre>";
    print_r($fields);
    echo "</pre>";
 
    wc_enqueue_js("
		jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = { minimumResultsForSearch: 5 };
			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
		});");
    exit;*/


    return $fields;
}


add_filter('woocommerce_default_address_fields', 'ysg_custom_override_default_locale_fields');
function ysg_custom_override_default_locale_fields($fields)
{
    $fields['state']['priority'] = 36;
    $fields['state']['label'] = "Emirates / State";
    $fields['state']['required'] = true;

    //
    $fields['city']['priority'] = 37;
    $fields['city']['priority'] = "Area / City";
    $fields['city']['required'] = 1;

    //
    $fields['address_1']['priority'] = 38;
    $fields['address_2']['priority'] = 39;
    return $fields;
}

add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
    if ($_POST['payment_method'] == "cod") {
        $payment_status = "cod";
    } else {
        $payment_status = "paid";
    }
    update_post_meta($order_id, 'ysg_payment_status', $payment_status);
});

add_action('woocommerce_thankyou', 'woocommerce_thankyou_change_order_status', 10, 1);
function woocommerce_thankyou_change_order_status($order_id)
{
    if (!$order_id) return;
    $dorder = new WC_Order($order_id);
    $order = $dorder->get_data();

    $shipping = $dorder->get_items('shipping');
    //var_dump($shipping);
    //var_dump($order['shipping']);
    foreach ($shipping as $item_id => $shipping_item_obj) {
        $mid = $shipping_item_obj->get_method_id();
        if ($mid == 'wc_pickup_store' && $dorder->get_status() !== "completed") {
            if ($order['payment_method'] == "cod") {
                $dorder->update_status('wc-ysg-pickupcod');
            } else {
                $dorder->update_status('wc-ysg-pickuppaid');
            }
        }
    }
}


add_action('woocommerce_admin_order_data_after_order_details', function ($order) {

    $payment_status = get_post_meta($order->id, 'ysg_payment_status', true);
    $text = '<div class="clear"></div><br/><h3>Information</h3>';
    if (!empty($payment_status)) {
        $text = $text . '<p style="font-size: 0.9rem; color: #000" class="form-field form-field-wide">'
            . '<strong class="earned-label">' . __('Payment Status', 'textdomain') . ':</strong> '
            . '<span>' . strtoupper($payment_status) . '</span>'
            . '</p>';
    }

    $ds = get_post_meta($order->id, 'ysg_tracking_status', true);
    if (!empty($ds)) {
        $text = $text . '<p class="form-field form-field-wide">'
            . '<strong class="earned-label">' . __('Delivery Status', 'textdomain') . ':</strong> '
            . '<span>' . strtoupper($ds) . '</span>'
            . '</p>';
    }
    echo $text . "<br/><br/><br/>";
});


/**
 * Remove COD for guest
 */

add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
    if (isset($available_gateways['cod']) && !is_user_logged_in()) {
        unset($available_gateways['cod']);
    }
    return $available_gateways;
});
