<?php

/**
 * @add_action wp_enqueue_scripts
 * add custom js scripts
 * @return void
 */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('checkout_page_jsscript', get_template_directory_uri() . '/ysg/scripts/js_checkout.js');
});

/**
 * manage check out fields - add, remove, reorder fields
 */
add_filter(
    'woocommerce_checkout_fields',
    function ($fields) {
        $fields['billing']['billing_email']['priority'] = 25;
        $fields['billing']['billing_email']['class'] = array('form-row-first');

        //email
        $fields['billing']['billing_phone']['priority'] = 26;
        $fields['billing']['billing_phone']['class'] = array('form-row-last', 'float-right-important');


        $fields['billing']['billing_country']['priority'] = $fields['shipping']['shipping_country']['priority'] = 35;
        $fields['billing']['billing_country']['label'] = $fields['shipping']['shipping_country']['label'] = 'Country';
        //state
        //$fields['billing']['billing_state']['priority'] = $fields['shipping']['shipping_state']['priority'] = 37;
        //$fields['billing']['billing_state']['label'] = $fields['shipping']['shipping_state']['label'] = "Emirates / State";
        unset($fields['billing']['billing_state']);
        unset($fields['shipping']['shipping_state']);

        //city
        $fields['billing']['billing_city']['priority'] = $fields['shipping']['shipping_city']['priority'] = 36;
        $fields['billing']['billing_city']['required'] = $fields['shipping']['shipping_city']['required'] = false;
        $fields['billing']['billing_city']['label'] = $fields['shipping']['shipping_city']['label'] = " ";
        $fields['billing']['billing_city']['custom_attributes'] = array('data-type' => 'billing');
        $fields['shipping']['shipping_city']['custom_attributes'] = array('data-type' => 'shipping');

        $fields['billing']['billing_city']['default'] = $fields['shipping']['shipping_city']['default'] = " ";

        //address
        $fields['billing']['billing_address_1']['priority'] = $fields['shipping']['shipping_address_1']['priority'] = 38;
        $fields['billing']['billing_address_2']['priority'] = $fields['shipping']['shipping_address_2']['priority'] = 39;

        //adding new fields for states
        $fields['billing']['billing_city2'] = array(
            'required' => false,
            'placeholder'   => _x('City', 'placeholder', 'woocommerce'),
            'label'  => "City / Area",
            'default'  => "",
            'priority'  => 36,
            'class'     => array('form-row-wide ysg_hidden js_billing_city2'),
            'clear'     => true
        );

        $fields['shipping']['shipping_city2'] = array(
            'placeholder'   => _x('City', 'placeholder', 'woocommerce'),
            'priority'  => 36,
            'default'  => "",
            'label'  => "City / Area",
            'class'     => array('form-row-wide ysg_hidden js_shipping_city2'),
            'clear'     => true
        );

        //adding new field for pickup store id
        $fields['shipping']['shipping_pickupstore_id'] = array(
            'required' => false,
            'placeholder'   => "",
            'type' => 'hidden',
            'class'     => array('js_pickupstore_id'),
        );

        return $fields;
    }
);


/**
 * @action woocommerce_default_address_fields 
 * manage default checkout address fields
 * add, remove, reorder
 */
add_filter(
    'woocommerce_default_address_fields',
    function ($fields) {
        //$fields['state']['priority'] = 37;
        //$fields['state']['label'] = "State";
        unset($fields['state']);

        //
        $fields['city']['priority'] = 36;
        $fields['city']['required'] = false;
        $fields['city']['label'] = "Emirates / Area";
        $fields['city']['default'] = "";
        //unset($field['city']);
        //
        $fields['address_1']['priority'] = 38;
        $fields['address_2']['priority'] = 39;
        return $fields;
    }
);

add_filter('woocommerce_checkout_fields', 'checkout_select_field_with_optgroup', 10, 1);
function checkout_select_field_with_optgroup($fields)
{
    $scontry = WC()->customer->get_shipping_country();
    if (!empty($scontry)) {
        $sccites = ysgMainGetCCities($scontry);
    }
    $options = array(
        '' => __("Select Emirates / Area"),
    );

    if (!empty($sccites)) {
        foreach ($sccites as $item) {
            $a = array();
            if (!empty($item->areas)) {
                foreach ($item->areas as $k) {
                    $a[$k->term_id] = $k->name . ", " . $item->name;
                }
            }
            $options[$item->name] = $a;
        }
    }

    $fields['billing']['billing_city']['type'] = 'select_og';
    $fields['billing']['billing_city']['options'] = $options;
    $fields['billing']['billing_city']['input_class'] = array('wc-enhanced-select', 'js-ysg_selected_city');
    $fields['billing']['billing_city']['class'] = array('form-row-wide');
    $fields['billing']['billing_city']['clear'] = true;

    $fields['shipping']['shipping_city']['type'] = 'select_og';
    $fields['shipping']['shipping_city']['options'] = $options;
    $fields['shipping']['shipping_city']['input_class'] = array('wc-enhanced-select', 'js-ysg_selected_scity');
    $fields['shipping']['shipping_city']['class'] = array('form-row-wide');
    $fields['shipping']['shipping_city']['clear'] = true;

    $fields['shipping']['shipping_city']['label'] = $fields['billing']['billing_city']['label'] = "Emirates / Area";


    wc_enqueue_js("
		jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = { minimumResultsForSearch: 5 };
			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
        });");


    return $fields;
}

// Check  for city 2 fields if billing or/and shipping city fields is "Other"
add_action('woocommerce_checkout_process', function () {

    //get the country 
    $ship_tda = esc_html($_POST['ship_to_different_address']);
    $billing_country = $_POST['billing_country'];
    //if its dropdown -- use billing_city2 -- else use billing_city

    $cities = ysgMainGetCCities($billing_country);
    if (empty($cities)) {
        if (!isset($_POST['billing_city2']) or empty($_POST['billing_city2'])) {
            wc_add_notice(__("Please fill in billing city field"), "error");
        }
    } else {
        if (!isset($_POST['billing_city']) or empty($_POST['billing_city'])) {
            wc_add_notice(__("Please fill in billing city field"), "error");
        }
    }

    if (!empty($ship_tda) && $ship_tda === "1") {
        $shipping_country = $_POST['shipping_country'];
        $cities = ysgMainGetCCities($shipping_country);
        if (empty($cities)) {
            if (!isset($_POST['shipping_city2']) or empty($_POST['shipping_city2'])) {
                wc_add_notice(__("Please fill in shipping city field"), "error");
            }
        } else {
            if (!isset($_POST['shipping_city']) or empty($_POST['shipping_city'])) {
                wc_add_notice(__("Please fill in shipping city field"), "error");
            }
        }
    }
});

// Updating billing and shipping city fields when using "Other"
add_action('woocommerce_checkout_create_order', function ($order, $posted_data) {
    $ship_tda = $posted_data['ship_to_different_address'];
    $bcity = "";

    // Updating billing city from 'billing_city2'
    if (isset($_POST['billing_city2']) && !empty($_POST['billing_city2']) && $_POST['billing_city'] == '') {
        $bcity = sanitize_text_field($_POST['billing_city2']);
        $order->set_billing_city($bcity);
    }

    // Updating biling city
    if (isset($_POST['billing_city']) && !empty($_POST['billing_city'])) {
        $city = get_term(sanitize_text_field($_POST['billing_city']), 'ysg_wc_shipping_cities');
        if (!empty($city)) {
            $bcity = $city->name;
            $order->set_billing_city($bcity);
        }
    }
    //sace shipping
    if (empty($ship_tda) || $ship_tda !== "1") {
        $order->set_shipping_city($bcity);
    } else {
        // Updating shipping city
        if (isset($_POST['shipping_city2']) && !empty($_POST['shipping_city2']) && $_POST['shipping_city'] == '') {
            $order->set_shipping_city(sanitize_text_field($_POST['shipping_city2']));
        }

        // Updating shipping city
        if (isset($_POST['shipping_city']) && !empty($_POST['shipping_city'])) {
            $city = get_term(sanitize_text_field($_POST['shipping_city']), 'ysg_wc_shipping_cities');
            if (!empty($city)) {
                $n_city = $city->name;
                $order->set_shipping_city($n_city);
            } else {
                $n_city = "";
            }
        }
    }
}, 30, 2);

///////////////////////////////////////////////////// 
/**
 * @action woocommerce_checkout_update_order_meta
 * UPDATE META DATA AFTER CHECKOUT
 */


add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

    $dorder = new WC_Order($order_id);
    $order = $dorder->get_data();

    $oim = $dorder->get_order_item_totals();

    //ysgPrintParams($order, true);
    if ($_POST['payment_method'] == "cod") {
        $payment_status = "cod";
    } else {
        $payment_status = "paid";
    }

    update_post_meta($order_id, 'ysg_payment_status', $payment_status);

    //UPDATE order store if exists
    $shipping = $dorder->get_items('shipping');
    foreach ($shipping as $shipping_item_obj) {
        $mid = $shipping_item_obj->get_method_id();
        //save store details
        if ($mid == 'wc_pickup_store') {
            $storeid = $_POST['shipping_pickupstore_id'];
            update_post_meta($order_id, 'ysg_shipping_pickupstore_id', 4618);
        }
    }


    //UPDATE order points save the value in
    if (class_exists('Woo_Pr_Model')) {
        global $woo_pr_model;
        $total = $dorder->get_subtotal();
        $coupons = $dorder->get_coupons();
        $fees = $dorder->get_fees();
        //remove Coupons
        $discount_value  = 0;
        foreach ($coupons as $cpitem) {
            $cpdata = $cpitem->get_data();
            $discount_value = $cpdata['discount'] + $discount_value;
        }

        //remove fees - only points
        $fee_value = 0;
        $plurallable = !empty(get_option('woo_pr_lables_points_monetary_value')) ? get_option('woo_pr_lables_points_monetary_value') : esc_html__('Points', 'woopoints');
        $woo_pr_fee_name = $plurallable . esc_html__(' Discount', 'woopoints');

        foreach ($fees as $feeitem) {
            $feedata = $feeitem->get_data();
            if ($feedata['name'] == $woo_pr_fee_name) {
                $fee_value = $feedata['total'];
            }
        }
        $total = $total - $discount_value - abs($fee_value);
        $points = $woo_pr_model->woo_pr_calculate_earn_points_from_price($total);
        $val = $woo_pr_model->woo_pr_calculate_discount_amount($points);
        update_post_meta($order_id, 'ysg_points_value', $val);
        update_post_meta($order_id, 'ysg_actual_points_earned', $points);
    }

    //set type of coupon used
    //ysgPrintParams($dorder->get_coupon_codes());
    $ccodes = $dorder->get_coupon_codes();
    if (!empty($ccodes)) {
        //you can only use one coupon code per order -- loops once
        foreach ($ccodes as $item) {
            //check if code is a coupon
            $c = get_page_by_title($item, OBJECT, 'shop_coupon');

            //we have only 2 plugins for coupon -- wc coupon & woo discount rules
            if (!empty($c)) {
                update_post_meta($order_id, 'ysg_coupon_type', "Coupon");
                update_post_meta($order_id, 'ysg_coupon_type_name', $item);
            } else {
                //--woo discount rules
                update_post_meta($order_id, 'ysg_coupon_type', "Discount");
                update_post_meta($order_id, 'ysg_coupon_type_name', $item);
            }
        }
    }
});

add_action('woocommerce_thankyou', 'woocommerce_thankyou_change_order_status', 10, 1);
function woocommerce_thankyou_change_order_status($order_id)
{
    if (!$order_id) return;
    $dorder = new WC_Order($order_id);
    $order = $dorder->get_data();

    $shipping = $dorder->get_items('shipping');
    //ysgPrintParams($order);
    //var_dump($order['shipping']);
    foreach ($shipping as $item_id => $shipping_item_obj) {
        $mid = $shipping_item_obj->get_method_id();
        //save store details
        if ($mid == 'wc_pickup_store' && $dorder->get_status() !== "completed") {
            if ($order['payment_method'] == "cod") {
                $dorder->update_status('wc-ysg-pickupcod');
            } else {
                $dorder->update_status('wc-ysg-pickuppaid');
            }
        }
    }
}

/**
 * Remove COD for guest
 */

add_filter('woocommerce_available_payment_gateways', function ($available_gateways) {
    if (isset($available_gateways['cod']) && !is_user_logged_in()) {
        //unset($available_gateways['cod']);
    }
    return $available_gateways;
});


/**
 *  if shipping rate is 0, concatenate ": $0.00" to the label
 */
add_filter('woocommerce_cart_shipping_method_full_label', function ($label, $method) {
    if ($method->cost == 0 && $method->id === "ysg_skynet") {
        $label = 'Free shipping'; //
    }
    return $label;
}, 10, 2);

/** 
 * Type: Code snippet for woocommerce. Can be added to function.php file of the active child theme (or active theme) or in any plugin file.
 * Description: Add Select field with option group new form field type "select_og" to WooCommerce available form field types.
 * Author: LoicTheAztec
 *
 * Field type: select_og
 */
add_filter('woocommerce_form_field_select_og', 'add_form_field_type_select_with_option_group', 10, 4);
function add_form_field_type_select_with_option_group($field, $key, $args, $value)
{
    if ($args['required']) {
        $args['class'][] = 'validate-required';
        $required        = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
    } else {
        $required = '';
    }

    if (is_string($args['label_class'])) {
        $args['label_class'] = array($args['label_class']);
    }

    if (is_null($value)) {
        $value = $args['default'];
    }

    // Custom attribute handling.
    $custom_attributes         = array();
    $args['custom_attributes'] = array_filter((array) $args['custom_attributes'], 'strlen');

    if ($args['maxlength']) {
        $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
    }

    if (!empty($args['autocomplete'])) {
        $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
    }

    if (true === $args['autofocus']) {
        $args['custom_attributes']['autofocus'] = 'autofocus';
    }

    if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
        foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
            $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
        }
    }

    if (!empty($args['validate'])) {
        foreach ($args['validate'] as $validate) {
            $args['class'][] = 'validate-' . $validate;
        }
    }

    $field           = '';
    $label_id        = $args['id'];
    $sort            = $args['priority'] ? $args['priority'] : '';
    $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';
    $options         = '';

    if (!empty($args['options'])) {
        // First loop: Options group
        foreach ($args['options'] as $option_group => $option_values) {
            if ('' === $option_group) {
                // If we have a blank option, select2 needs a placeholder.
                if (empty($args['placeholder'])) {
                    $args['placeholder'] = $option_values ? $option_values : __('Choose an option', 'woocommerce');
                }
                $custom_attributes[] = 'data-allow_clear="true"';

                $options .= '<option value="' . esc_attr($option_group) . '">' . esc_attr($option_values) . '</option>';
            } else {
                $options .= '<optgroup label="' . esc_attr($option_group) . '">';

                // Second loop: Options in an otion group
                foreach ($option_values as $option_key => $option_text) {
                    $options .= '<option value="' . esc_attr($option_key) . '" ' . selected($value, $option_key, false) . '>' . esc_attr($option_text) . '</option>';
                }

                $options .= '</optgroup>';
            }
        }

        $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' data-placeholder="' . esc_attr($args['placeholder']) . '">
                ' . $options . '
            </select>';
    }

    if (!empty($field)) {
        $field_html = '';

        if ($args['label'] && 'checkbox' !== $args['type']) {
            $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
        }

        $field_html .= $field;

        if ($args['description']) {
            $field_html .= '<span class="description">' . esc_html($args['description']) . '</span>';
        }

        $container_class = esc_attr(implode(' ', $args['class']));
        $container_id    = esc_attr($args['id']) . '_field';
        $field           = sprintf($field_container, $container_class, $container_id, $field_html);
    }

    return $field;
}


/**
 * 
 */

function ysgMainGetCCities($code)
{
    $country = agGetCountry($code);

    if (empty($country)) {
        return FALSE;
    }

    $cities = get_terms(array(
        'taxonomy' => 'ysg_wc_shipping_cities',
        'hide_empty' => false,
        'meta_query'        => array(
            'relation'        => 'AND',
            array(
                'key'            => 'ysg_wcs_country_parent',
                'value'            => $country->ID,
                'compare'        => '='
            )
        )
    ));

    if (empty($cities)) {
        return FALSE;
    }

    $all_cities = array();
    foreach ($cities as $item) {
        $the_locations = array();
        $locations = get_terms(array(
            'taxonomy' => 'ysg_wc_shipping_cities',
            'hide_empty' => false,
            'parent' => $item->term_id
        ));

        $the_item = (array) $item;
        foreach ($locations as $lk) {
            $pd = (array)$lk;
            $del_date = get_field('ysg_wcs_is_delivery_day', 'ysg_wc_shipping_cities_' . $lk->term_id);
            $pd['delivery_day'] = !empty($del_date) ? $del_date : "";
            $the_locations[] = (object) $pd;
        }
        $the_item['areas'] = $the_locations;
        $all_cities[] = (object)$the_item;
    }

    return $all_cities;
}

function agGetCountry($code)
{
    $args = array(
        'numberposts'    => 1,
        'post_type'        => 'ysg_wcs_country',
        'meta_key'        => 'ysg_wcs_c_code',
        'meta_value'    => $code
    );
    $countries = get_posts($args);

    if (empty($countries)) {
        return FALSE;
    }

    foreach ($countries as $item) {
        $country = $item;
    }

    return $country;
}

add_action('woocommerce_order_status_changed', function ($order_id, $old_status, $new_status) {
}, 120, 3);
/**
 * substract points from order that have not been completed yet
 * @function ysgSubtractPendingOrderPoints
 * @return points
 */
function ysgSubtractPendingOrderPoints($old_points, $userid)
{
    $prefix = WOO_PR_META_PREFIX;
    global $woo_pr_model;
    $added_points = 0;

    //get all user orders - not completed, not refunded, not cancelled
    //get all order points from those orders -- summ
    //substract from old points

    $statuses = wc_get_order_statuses();
    $astutuses = array();

    foreach ($statuses as $k => $v) {
        //completed is already added, refunded and cancelled are already removed
        if ($v != "Cancelled" && $v != "Refunded" && $v != "Completed") {
            $astutuses[] = $k;
        }
    }

    $orderids = wc_get_orders(array(
        'customer_id' => $userid,
        'status' => $astutuses,
        'return' => 'ids',
        'limit' => -1
    ));

    foreach ($orderids as $item) {
        //get all points
        $order_points_logs_args = array(
            'post_parent'   => $item,
            'meta_query'    => array(
                array(
                    'key'     => '_woo_log_events',
                    'value'   => 'earned_purchase',
                ),
            )
        );
        //get order logs data
        $order_points_logs = $woo_pr_model->woo_pr_get_points($order_points_logs_args);
        foreach ($order_points_logs as $item) {
            $logspointid = $item['ID'];
            $a = get_post_meta($logspointid, '_woo_log_userpoint', true);
            $added_points = $added_points + intval($a);
        }
    }

    $new_points = $old_points - $added_points;
    if ($new_points < 0) {
        return 0;
    }

    return $new_points;
}

//rearrang shipping methods
add_filter(
    'woocommerce_package_rates',
    function ($available_shipping_methods, $package) {
        // Arrange shipping methods as per your requirement
        $sort_order    = array(
            'ysg_skynet'    =>    array(),
            'wc_pickup_store'    =>    array(),
        );

        // unsetting all methods that needs to be sorted
        foreach ($available_shipping_methods as $carrier_id => $carrier) {
            $carrier_name    =    current(explode(":", $carrier_id));
            if (array_key_exists($carrier_name, $sort_order)) {
                $sort_order[$carrier_name][$carrier_id]    =        $available_shipping_methods[$carrier_id];
                unset($available_shipping_methods[$carrier_id]);
            }
        }

        // adding methods again according to sort order array
        foreach ($sort_order as $carriers) {
            $available_shipping_methods    =    array_merge($available_shipping_methods, $carriers);
        }
        return $available_shipping_methods;
    },
    10,
    2
);


// define the woocommerce_cart_totals_order_total_html callback 
add_filter('woocommerce_cart_totals_order_total_html', function ($value) {
    $value = '<strong>' . WC()->cart->get_total() . '</strong> ';
    $shipping_cost = WC()->cart->get_shipping_total();
    $shipping_inclusive_tax = WC()->cart->get_shipping_tax();

    //ysgPrintParams(WC()->cart());
    //calculate shipping ad

    // If prices are tax inclusive, show taxes here.
    if (wc_tax_enabled() && WC()->cart->display_prices_including_tax()) {
        $tax_string_array = array();
        $cart_tax_totals  = WC()->cart->get_tax_totals();

        if (get_option('woocommerce_tax_total_display') == 'itemized') {
            foreach ($cart_tax_totals as $code => $tax) {
                $shnewins = $shipping_inclusive_tax + $tax->amount;
                $ntext = str_replace($tax->amount, $shnewins, $tax->formatted_amount);
                $tax_string_array[] = sprintf('%s %s', $ntext, $tax->label);
            }
        } elseif (!empty($cart_tax_totals)) {
            $tax_string_array[] = sprintf('%s %s', wc_price(WC()->cart->get_taxes_total(true, true)), WC()->countries->tax_or_vat());
        }

        if (!empty($tax_string_array)) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()
                ? sprintf(' ' . __('estimated for %s', 'woocommerce'), WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]])
                : '';
            $value .= '<small class="includes_tax">' . sprintf(__('(includes %s)', 'woocommerce'), implode(', ', $tax_string_array) . $estimated_text) . '</small>';
        }
    }

    return $value;
}, 10, 1);


//filter update order tax content
add_filter('woocommerce_get_order_item_totals', function ($total_rows, $order) {
    $order_total = $order->get_total();
    $shipping_cost = $order->get_shipping_total();
    $shipping_inclusive_tax = $order->get_shipping_tax();;
    $order_tax = $order->get_cart_tax();

    //$shipping_tax = wc_price(($order_tax));
    $shipping_tax = wc_price(($order_tax + $shipping_inclusive_tax));
    //
    $total_rows['order_total'] = array(
        'label' => __('Total:', 'woocommerce'),
        'value'   => wc_price($order_total) . '(includes ' . $shipping_tax . 'VAT)'
    );

    return $total_rows;
}, 10, 2);
