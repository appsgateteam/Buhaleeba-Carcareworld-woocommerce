<?php

//////////////////////////////////////////////////////
////////////////////////////
//////////////////////////////////////////////////////
/////////////////////////////////////RESPONSE OBJECT
/**
 * Add order meta to the REST API
 * WC 2.6+
 *
 * @param \WP_REST_Response $response The response object.
 * @param \WP_Post $post Post object.
 * @param \WP_REST_Request $request Request object.
 * @return object updated response object
 */
add_filter(
    'woocommerce_rest_prepare_shop_order_object',
    function ($response, $object, $request) {
        $order_data = $response->get_data();

        $totals = ysgGetTotalItemsInCart($order_data);
        $points = ysgGetTotalPointsUsed($order_data);
        $coupon  = ysgGetCouponUsed($order_data);

        $billing_skynet = array();

        $ostatus = $order_data['status'];
        if ($ostatus == "processing") {
            $billing_skynet = getSkynetAddressDetails();
        }

        if ($order_data['payment_method'] == "cod" && $ostatus == "processing") {
            $order_data['status'] = "pending processing";
        }

        if ($ostatus == "ysg-pickuppaid") {
            $order_data['status'] = "pickup-paid";
        }

        if ($ostatus == "ysg-pickupcod") {
            $order_data['status'] = "pickup-cod";
        }

        if ($ostatus == "wc-ysg-shipped") {
            $order_data['status'] = "out for delivery";
        }

        if ($ostatus == "ywraq-new") {
            $order_data['status'] = "quote request";
        }

        if ($ostatus == "ywraq-pending") {
            $order_data['status'] = "quote request";
        }

        $order_data['skynet_billing_address'] = $billing_skynet;

        $order_data['shipping_full'] = 15;
        $order_data['shipping_full_details'] = array();

        /*
        if (isset($order_data['shipping_lines']) and !empty($order_data['shipping_lines'])) {
            foreach ($order_data['shipping_lines'] as $si) {
                if ($si['method_id'] == "wc_pickup_store") {
                    if ($order_data['payment_method'] == "cod" && $order_data['status'] == "processing") {
                        $order_data['status'] = "pickup - cod";
                    } elseif ($order_data['payment_method'] != "cod" && $order_data['status'] == "processing") {
                        $order_data['status'] = "pickup - paid";
                    }
                }
            }
        }
        */

        //add payment status -- when not paid set to cod
        $order_data['ysg_payment_status'] = "paid";
        if ($order_data['payment_method'] == "cod") {
            $order_data['ysg_payment_status'] = "cod";
        }

        //set all statuses to lowercase
        $order_data['status'] = strtolower($order_data['status']);

        foreach ($order_data['line_items'] as $key => $item) {
            //print_r($item);
            $order_item_discount = ysgGetOrderItemDiscount($item);
            $discount_type = ysgSetDiscountType($item, $points, $coupon, $order_item_discount, $totals);

            $order_data['line_items'][$key]['discount_type'] = $discount_type['discount_types'];
            $order_data['line_items'][$key]['total_discount_amount'] = $discount_type['discount_total_amount'];

            $order_data['line_items'][$key]['item_unit_price'] = $order_data['line_items'][$key]['subtotal'] / $order_data['line_items'][$key]['quantity'];

            //save old total and 
            $order_data['line_items'][$key]['subtotal_old'] = $order_data['line_items'][$key]['subtotal'];
            $order_data['line_items'][$key]['total_old'] = $order_data['line_items'][$key]['total'];

            //subtotals set line items total and subtotal
            $order_data['line_items'][$key]['item_unit_price'] = ($order_data['line_items'][$key]['subtotal'] + $order_data['line_items'][$key]['subtotal_tax']) / $order_data['line_items'][$key]['quantity'];
            //$order_data['line_items'][$key]['total'] = $order_data['line_items'][$key]['total'] + $order_data['line_items'][$key]['total_tax'];
        }

        //add cod fee to line_items
        $cod_price = getCashOnDeliveryPriceFromOrder($order_data['fee_lines']);
        if (!empty($cod_price)) {
            $cod_price['amount'] = $cod_price['amount'] + $cod_price['total_tax'];
            $order_data['ysg_cod_additional_cost'] = $cod_price;
        }

        $shipping_tax = 0;
        //update shipping lines
        if (isset($order_data['shipping_lines']) && !empty($order_data['shipping_lines'])) {
            foreach ($order_data['shipping_lines'] as $key => $item) {
                if ($item['method_id'] == "ysg_skynet") {
                    $shipping_tax = number_format(((float)$item['total']) * (5 / 105), 2);
                    $order_data['shipping_lines'][$key]["total_tax"] = $shipping_tax;
                }
            }
        }

        $order_data["shipping_tax"] = $shipping_tax;
        $order_data["total_tax"] = number_format($order_data["total_tax"] + $shipping_tax, 2);


        //set new earned points //
        $order_data['ysg_order_earned_points'] =  0;
        $order_data['ysg_pickup_store_details'] = "";

        if (isset($order_data['meta_data']) && !empty($order_data['meta_data'])) {
            foreach ($order_data['meta_data'] as $key => $item) {
                $o = $item->get_data();
                if ($o['key'] == "ysg_points_value") {
                    $order_data['ysg_order_earned_points'] = $o['value'];
                }

                if ($o['key'] == "ysg_shipping_pickupstore_id") {
                    $store_post = get_post($o['value']);
                    $pstore_data = array();
                    if (!empty($store_post)) {
                        $a = get_post_meta($o['value']);

                        $pstore_data['name'] = $store_post->post_title;
                        $pstore_data['address'] = $a['address'];
                        $pstore_data['city'] = $a['city'];
                        $pstore_data['phone'] = $a['phone'];

                        $pstore_data['store_country'] = $a['store_country'];
                        $pstore_data['store_order_email'] = $a['store_order_email'];
                        $pstore_data['waze'] = $a['waze'];
                        $pstore_data['description'] = $a['description'];

                        $pstore_data['map'] = $a['map'];
                        $pstore_data['enable_order_email'] = $a['enable_order_email'];
                    }
                    $order_data['ysg_pickup_store_details'] = $pstore_data;
                }
            }
        }

        //
        //
        $response->data = $order_data;
        return $response;
    },
    10,
    3
);


/**
 * Set the discount types used on this order
 * @funciton ysgSetDiscountType
 * @access public
 * @return array
 */

function ysgSetDiscountType($order_item, $points, $coupon, $order_item_discount, $totals)
{
    $all_discount_details = array();
    $total_discount = 0;
    if ($points != false) {
        $all_discount_details[] = $k = ysgGetPointsDiscount($points, $order_item, $totals);
        $total_discount = $total_discount + $k['order_item_value'];
    }

    if ($coupon != false) {
        $all_discount_details[] = $l =  ysgGetCouponDiscount($coupon, $order_item, $totals);
        $total_discount = $total_discount + $l['order_item_value'];
    }

    if ($order_item_discount != false) {
        $all_discount_details[] = $p = ysgGetNormalDiscount($order_item_discount, $order_item, $totals);
        $total_discount = $total_discount + $p['order_item_value'];
    }

    $m = array();
    $m['discount_types'] = $all_discount_details;
    $m['discount_total_amount'] = $total_discount;

    return ($m);
}


/**
 * Get the order points item discount details
 * @funciton ysgGetPointsDiscount
 * @access public
 * @return array
 */

function ysgGetPointsDiscount($points, $order_item, $totals)
{
    $order_item_value = - ($order_item['subtotal'] / $totals['amount']) * $points['amount'];
    $single_order_item_value = - (($order_item['subtotal'] / $totals['amount']) / $order_item['quantity']) * $points['amount'];
    $a = array();

    $a['order_item_value'] = $order_item_value;
    $a['order_item_value_single'] = $single_order_item_value;
    $a['name'] = 'Points Discount';
    $a['id'] = $points['id'];
    $a['type'] = "Points";
    $a['quantity'] = $order_item['quantity'];

    return $a;
}

/**
 * Get the order item coupon discount details
 * @funciton ysgGetCouponDiscount
 * @access public
 * @return array
 */

function ysgGetCouponDiscount($coupon, $order_item, $totals)
{
    $order_item_value = ($order_item['subtotal'] / $totals['amount']) * $coupon['discount'];
    $single_order_item_value = (($order_item['subtotal'] / $totals['amount']) / $order_item['quantity']) * $coupon['discount'];
    $a = array();

    $a['order_item_value'] = $order_item_value;
    $a['order_item_value_single'] = $single_order_item_value;
    $a['name'] = $coupon['code'];
    $a['id'] = $coupon['id'];
    $a['type'] = "Coupon";
    $a['quantity'] = $order_item['quantity'];

    return $a;
}

/**
 * Get the order normal discount details -- single order item
 * @funciton ysgGetNormalDiscount
 * @access public
 * @return array
 */

function ysgGetNormalDiscount($order_item_discount, $order_item, $totals)
{
    if (isset($order_item_discount['saved_amount_with_tax'])) {
        $order_item_value = $order_item_discount['saved_amount_with_tax'];
        $single_order_item_value = $order_item_discount['saved_amount_with_tax'] / $order_item_discount['cart_quantity'];
    }

    if (isset($order_item_discount['cart_discount_details']) and !empty($order_item_discount['cart_discount_details'])) {

        $amount_discount = $amount_discount_single = 0;
        foreach ($order_item_discount['cart_discount_details'] as $item) {
            $amount_discount = $item['cart_discount_price'];
            $amount_discount_single = $item['cart_discount_product_price'];
        }

        $order_item_value = ($order_item['subtotal'] / $totals['amount']) * $amount_discount;
        $single_order_item_value = (($order_item['subtotal'] / $totals['amount'])) * $amount_discount_single;
    }


    $a = array();

    $a['order_item_value'] = $order_item_value;
    $a['order_item_value_single'] = $single_order_item_value;
    $a['name'] = "";
    $a['id'] = $order_item_discount['product_id'];
    $a['type'] = "Discount";
    $a['quantity'] = $order_item_discount['cart_quantity'];

    return $a;
}
/**
 * Get the order item discount details
 * @funciton ysgGetOrderItemDiscount
 * @access public
 * @return array
 */

function ysgGetOrderItemDiscount($order_item)
{
    $discount_details = false;
    if (!isset($order_item['meta_data']) or empty($order_item['meta_data'])) {
        return $discount_details;
    }

    foreach ($order_item['meta_data'] as $item) {
        $data = @$item->get_data();
        if (isset($data['key']) && $data['key'] === "_advanced_woo_discount_item_total_discount") {
            $discount_details = $data['value'];
        }
    }
    return $discount_details;
}



/**
 * Get  point in the cart
 * @funciton ysgGetTotalItemsInCart
 * @access public
 * @return array
 */
function ysgGetTotalPointsUsed($order)
{
    $point_discount = FALSE;
    foreach ($order['fee_lines'] as $key => $item) {
        if ($order['fee_lines'][$key]['name'] === "Points Discount") {
            $point_discount = $order['fee_lines'][$key];
            break;
        }
    }

    return $point_discount;
}


/**
 * Get  coupon in the cart
 * @funciton ysgGetCouponUsed
 * @access public
 * @return array
 */
function ysgGetCouponUsed($order)
{
    $coupon = FALSE;
    foreach ($order['coupon_lines'] as $key => $item) {
        $coupon = $order['coupon_lines'][$key];
        break;
    }

    return $coupon;
}
/**
 * Get total items in the cart
 * @funciton ysgGetTotalItemsInCart
 * @access public
 * @return array
 */
function ysgGetTotalItemsInCart($order)
{
    $q = 0;
    $total_amount = 0;
    $totals = array();
    foreach ($order['line_items'] as $key => $item) {
        $a = array();
        $total_amount = $order['line_items'][$key]['subtotal'] + $total_amount;
        $q =   $order['line_items'][$key]['quantity'] + $q;
    }

    $totals['amount'] = $total_amount;
    $totals['quantity'] = $q;
    return $totals;
}

/**
 * add gets the cash on delivery price from order
 * @function getCashOnDeliveryPriceFromOrder
 * @return array
 */
function getCashOnDeliveryPriceFromOrder($fee_lines)
{
    $cod_price = array();
    if (empty($fee_lines)) {
        return $cod_price;
    }
    foreach ($fee_lines as $item) {
        if ($item['name'] === "Cash on delivery") {
            $cod_price = $item;
            break;
        }
    }

    return $cod_price;
}
/**
 * @function getSkynetAddressDetails
 * address of skynet from settings and add to order request
 * @return array
 */
function getSkynetAddressDetails()
{
    $skynet_settings = get_option("woocommerce_ysg_skynet_settings", TRUE);
    $address = array(
        "first_name" => "",
        "last_name" => "",
        "company" => $skynet_settings['company_name'],
        "address" => $skynet_settings['address'],
        "city" => $skynet_settings['city'],
        "state" => "",
        "postcode" => "",
        "country" => $skynet_settings['country'],
        "email" => $skynet_settings['email'],
        "phone" => $skynet_settings['phone']
    );

    return $address;
}

//////////////////////////////////////////////////////////////////
///////////////////////////////////ReST API request Save // Insert 

/**
 * Add product types to product product on rest insert
 * 
 * @access public
 * @return array
 */

add_action(
    'woocommerce_rest_insert_product_object',
    function ($post, $request, $true) {
        $product = $post->get_data();
        $post_type =  get_post_type($product['id']);
        if ($post_type == 'product') {
            $params = $request->get_params();
            $to_add = array();

            if (array_key_exists("ysg_product_type", $params)) {
                foreach ($params["ysg_product_type"] as $term) {
                    //check if id of post_type exists
                    $name = (isset($term['name']) and !empty($term['name'])) ? $term['name'] : "";
                    $id = (isset($term['id']) and !empty($term['id'])) ? $term['id'] : "";
                    $parent_id = (isset($term['parent_id']) and !empty($term['parent_id'])) ? $term['parent_id'] : 0;

                    $can_add_to_product = false;
                    //if no term id, and no term name do not create anything, create new term
                    if (empty($id) and empty($name)) {
                        error_log("Taxonomy with no name and id was not created for the product with id " . $product['id']);
                        continue;
                    }

                    //if just name exists -- create new term
                    if (empty($id) && !empty($name)) {
                        $id = ysgCreateProductType($name, $parent_id);
                        if ($id == false) {
                            error_log("Taxonomy term was not successfully created for the product with id " . $product['id']);
                            continue;
                        }
                        $can_add_to_product = true;
                    }

                    //term id exists
                    if (!empty($id) and $can_add_to_product == false) {
                        $nterm = get_term($id, 'ysg_product_type');
                        if ($nterm == null or empty($nterm) or is_wp_error($nterm)) {
                            error_log("Taxonomy term does not exists and was not added to the product with id " . $product['id']);
                            continue;
                        }
                        $can_add_to_product = true;
                    }

                    if ($can_add_to_product) {
                        $to_add[] = $id;
                        //add new product type to product
                        //print_r($params["ysg_product_type"]);
                    }
                }

                if (!empty($to_add)) {
                    wp_set_post_terms($product['id'], $to_add, 'ysg_product_type');
                }
            }
        }
        //print_r($request->get_params());
        //print_r($request);
    },
    10,
    3
);
/**
 * create new product type term
 * @funciton ysgCreateProductType
 * @return array
 */

function ysgCreateProductType($name, $parent_id)
{
    $args = array('name' => $name, 'parent' => $parent_id);
    $terms = get_terms($args);

    if (!is_wp_error($terms) and !empty($terms)) {
        foreach ($terms as $a) {
            $term = $a;
        }
        return $term;
    }

    $term = wp_insert_term($name, 'ysg_product_type', array('parent' => $parent_id));
    if (is_wp_error($term)) {
        return false;
    }

    return $term['term_id'];
}
