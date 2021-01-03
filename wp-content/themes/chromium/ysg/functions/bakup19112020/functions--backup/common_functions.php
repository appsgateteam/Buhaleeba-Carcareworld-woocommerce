<?php

// Register new status
function register_pickup_shipment_order_status()
{
    register_post_status('wc-pickup-shipment', array(
        'label'                     => 'Processing - Pickup',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Processing Pickup (%s)', 'Processing Pickup (%s)')
    ));
}
add_action('init', 'register_pickup_shipment_order_status');


add_filter('woocommerce_get_price_html', 'custom_price_message');
function custom_price_message($price)
{
    if (is_product()) {
        $new_price = $price . ' <span class="custom-price-prefix">' . __('(VAT Included)') . '</span>';
        return $new_price;
    }
    return $price;
}

// Product Brand List
function doProductBrandsPage()
{
    $product_brands = getProductBrands();
    ob_start();
    include_once get_template_directory() . '/ysg/includes/product_brands.php';

    $text = ob_get_contents();

    ob_end_clean();

    return $text;
}
// product brands shortcode
add_shortcode('ysg_product_brands', 'doProductBrandsPage');


function getProductBrands()
{
    $terms = get_terms(array(
        'taxonomy' => 'pa_product-brand',
        'hide_empty' => true,
    ));
    if (empty($terms)) {
        return array();
    }

    $all_terms = array();
    foreach ($terms as $term) {
        $at = (array) $term;
        $image = get_field('image', $term);
        $at['image'] = $image;

        $all_terms[] = (object) $at;
    }
    return $all_terms;
}

add_filter('posts_search', 'woocommerce_search_product_tag_extended', 999, 2);
function woocommerce_search_product_tag_extended($search, $query)
{
    global $wpdb, $wp;

    $qvars = $wp->query_vars;

    if (is_admin() || empty($search) ||  !(isset($qvars['ptype'])
        && isset($qvars['post_type']) && !empty($qvars['ptype'])
        && $qvars['post_type'] === 'product')) {
        return $search;
    }

    // Here set your custom taxonomy
    $taxonomy = 'ysg_product_type'; // WooCommerce product tag

    // Get the product Ids
    $ids = get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'tax_query'       => array(array(
            'taxonomy' => $taxonomy,
            'field'    => 'name',
            'terms'    => esc_attr($qvars['ptype']),
        )),
    ));

    if (count($ids) > 0) {
        $search = str_replace('AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode(',', $ids) . ")) OR (", $search);
    }
    return $search;
}
add_filter('posts_search', 'woocommerce_search_product_mega_extended', 999, 2);
function woocommerce_search_product_mega_extended($search, $query)
{
    global $wpdb, $wp;

    $qvars = $wp->query_vars;

    if (is_admin() || empty($search) ||  !(isset($qvars['s'])
        && isset($qvars['post_type']) && !empty($qvars['s'])
        && $qvars['post_type'] === 'product')) {
        return $search;
    }

    // SETTINGS:
    $taxonomies = array('product_tag', 'product_cat'); // Here set your custom taxonomies in the array
    $meta_keys  = array('_sku'); // Here set your product meta key(s) in the array

    // Initializing tax query
    $tax_query  = count($taxonomies) > 1 ? array('relation' => 'OR') : array();

    // Loop through taxonomies to set the tax query
    foreach ($taxonomies as $taxonomy) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'name',
            'terms'    => esc_attr($qvars['s']),
        );
    }

    // Get the product Ids from taxonomy(ies)
    $tax_query_ids = (array) get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'tax_query'       => $tax_query,
    ));

    // Initializing meta query
    $meta_query = count($meta_keys) > 1 ? array('relation' => 'OR') : array();

    // Loop through taxonomies to set the tax query
    foreach ($taxonomies as $taxonomy) {
        $meta_query[] = array(
            'key'     => '_sku',
            'value'   => esc_attr($qvars['s']),
        );
    }

    // Get the product Ids from custom field(s)
    $meta_query_ids = (array) get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'meta_query'      => $meta_query,
    ));

    $product_ids = array_unique(array_merge($tax_query_ids, $meta_query_ids)); // Merge Ids in one array  with unique Ids

    if (sizeof($product_ids) > 0) {
        $search = str_replace('AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode(',', $product_ids) . ")) OR (", $search);
    }
    return $search;
}
// Adds image to WooCommerce order emails
function w3p_add_image_to_wc_emails($args)
{
    $args['show_image'] = true;
    $args['image_size'] = array(100, 50);
    $args['show_sku'] = true;
    return $args;
}
add_filter('woocommerce_email_order_items_args', 'w3p_add_image_to_wc_emails');

//////////////////////////////////////////////////////
////////////////////////////
//////////////////////////////////////////////////////
/////////////////////////////////////
/**
 * Add order meta to the REST API
 * WC 2.6+
 *
 * @param \WP_REST_Response $response The response object.
 * @param \WP_Post $post Post object.
 * @param \WP_REST_Request $request Request object.
 * @return object updated response object
 */
add_filter('woocommerce_rest_prepare_shop_order_object', 'my_wc_prepare_shop_order', 10, 3);

function my_wc_prepare_shop_order($response, $object, $request)
{
    $order_data = $response->get_data();

    $totals = ysgGetTotalItemsInCart($order_data);
    $points = ysgGetTotalPointsUsed($order_data);
    $coupon  = ysgGetCouponUsed($order_data);
    if ($order_data['payment_method'] == "cod" && $order_data['status'] == "processing") {
        $order_data['status'] = "pending processing";
    }

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

    $order_data['ysg_payment_status'] = "paid";
    if ($order_data['payment_method'] == "cod") {
        $order_data['ysg_payment_status'] = "cod";
    }

    //print_r($order_data);
    //exit;

    foreach ($order_data['line_items'] as $key => $item) {
        //print_r($item);
        $order_item_discount = ysgGetOrderItemDiscount($item);
        $discount_type = ysgSetDiscountType($item, $points, $coupon, $order_item_discount, $totals);

        $order_data['line_items'][$key]['discount_type'] = $discount_type['discount_types'];
        $order_data['line_items'][$key]['total_discount_amount'] = $discount_type['discount_total_amount'];

        $order_data['line_items'][$key]['item_unit_price'] = $order_data['line_items'][$key]['subtotal'] / $order_data['line_items'][$key]['quantity'];
    }

    $response->data = $order_data;
    return $response;
}


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
 * Add product types to product product
 * @funciton ysgAddProductTypeToProduct
 * @access public
 * @return array
 */

add_action('woocommerce_rest_insert_product_object', 'ysgAddProductTypeToProduct', 10, 3);

function ysgAddProductTypeToProduct($post, $request, $true)
{
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
}
/**
 * create new product type term
 * @funciton ysgCreateProductType
 * @access public
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
/**
 * Redirect to login/register pre-checkout.
 *
 * Redirect guest users to login/register before completing a order.
 */
/*
function ace_redirect_pre_checkout()
{
    if (!function_exists('wc')) return;

    $redirect_page_id = 2555;
    if (!is_user_logged_in() && is_checkout()) {
        wp_redirect(get_permalink('my-account-2'));
        exit;; //die;
    } elseif (is_user_logged_in() && is_page($redirect_page_id)) {
        wp_safe_redirect(get_permalink(wc_get_page_id('checkout')));
        die;
    }
}
add_action('template_redirect', 'ace_redirect_pre_checkout');
*/
////////////////////////////////////////////////

if (file_exists(get_template_directory() . '/ysg/functions/functions_general.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_general.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/functions_admin.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_admin.php';
}
