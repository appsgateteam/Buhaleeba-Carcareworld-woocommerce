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
    $fields['billing']['billing_state']['priority'] = $fields['shipping']['shipping_state']['priority'] = 37;
    $fields['billing']['billing_state']['label'] = $fields['shipping']['shipping_state']['label'] = "Emirates / State";

    //city
    $fields['billing']['billing_city']['priority'] = $fields['shipping']['shipping_city']['priority'] = 36;
    $fields['billing']['billing_city']['label'] = $fields['shipping']['shipping_city']['label'] = "Area / City";

    //address
    $fields['billing']['billing_address_1']['priority'] = $fields['shipping']['shipping_address_1']['priority'] = 38;
    $fields['billing']['billing_address_2']['priority'] = $fields['shipping']['shipping_address_2']['priority'] = 39;

    return $fields;
}


add_filter('woocommerce_default_address_fields', 'ysg_custom_override_default_locale_fields');
function ysg_custom_override_default_locale_fields($fields)
{
    $fields['state']['priority'] = 37;
    $fields['state']['label'] = "State";
    //unset($fields['state']);

    //
    $fields['city']['priority'] = 36;
    $fields['city']['priority'] = "Area / City";
    //unset($field['city']);
    //
    $fields['address_1']['priority'] = 38;
    $fields['address_2']['priority'] = 39;
    return $fields;
}

add_filter('woocommerce_checkout_fields', 'checkout_select_field_with_optgroup', 10, 1);
function checkout_select_field_with_optgroup($fields)
{
    $scontry = WC()->customer->get_shipping_country();
    if (!empty($scontry)) {
        $sccites = ysgMainGetCCities($scontry);
    }
    $options = array(
        '' => __("Select City / Area"),
    );

    if (!empty($sccites)) {
        foreach ($sccites as $item) {
            $a = array();
            if (!empty($item->areas)) {
                foreach ($item->areas as $k) {
                    $a[$k->term_id] = $k->name;
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


    wc_enqueue_js("
		jQuery( ':input.wc-enhanced-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = { minimumResultsForSearch: 5 };
			jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
        });");


    return $fields;
}

/* Custom JavaScripts */
function checkout_page_jsscript()
{
    wp_enqueue_script('checkout_page_jsscript', get_template_directory_uri() . '/ysg/scripts/js_checkout.js');
}
add_action('wp_enqueue_scripts', 'checkout_page_jsscript');

/////////////////////////////////////////////////////

add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

    $dorder = new WC_Order($order_id);
    $order = $dorder->get_data();
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
        //unset($available_gateways['cod']);
    }
    return $available_gateways;
});


/**
 *  if shipping rate is 0, concatenate ": $0.00" to the label
 
add_filter('woocommerce_cart_shipping_method_full_label', function ($label, $method) {
    if ($method->cost == 0) {
        $label = 'Free shipping'; //
    }
    return $label;
}, 10, 2);
 */
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
        $locations = get_terms(array(
            'taxonomy' => 'ysg_wc_shipping_cities',
            'hide_empty' => false,
            'parent' => $item->term_id
        ));

        $the_item = (array) $item;
        $the_item['areas'] = $locations;
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
