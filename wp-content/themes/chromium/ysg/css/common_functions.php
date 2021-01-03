<?php

/**
 * @action wp_head
 * sets script top title
 */
add_action('wp_head', function () {
    echo '<script type="text/javascript">var ysg_base_url = "' . site_url() . '";var ysg_tem_url ="' . get_template_directory_uri() . '";</script>';
});

add_action('woocommerce_before_main_content', function () {
    if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
        echo "<div class=\"yg_hold_main_search\">";
    }
}, 7);
add_action('get_footer', function () {
    if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
        echo "</div>";
    }
}, 7);

/**Custom Price HTML */
add_filter('woocommerce_get_price_html', 'custom_price_message');
function custom_price_message($price)
{
    if (is_product()) {
        $new_price = $price . ' <span class="custom-price-prefix">' . __('(VAT Included)') . '</span>';
        return $new_price;
    }
    return $price;
}

/**
 * prints for display, testing & troubleshooting
 * @function ysgPrintParams
 * @access public
 * @return void
 */
function ysgPrintParams($params, $exit = false)
{
    echo "<pre>";
    print_r($params);
    echo "</pre>";

    if ($exit == true) {
        exit();
    }
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

/**
 * Log FIle
 */
function ysgLogInFile($message)
{
    // path of the log file where errors need to be logged 
    $log_file = get_template_directory() . "/ysg/logs.txt";
    $log = "\r\n\r\n[User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a", strtotime(gmdate("Y-m-d H:i:s")) + 4 * 3600) . "]" . PHP_EOL .
        "\r\nMessage: " . $message . PHP_EOL . PHP_EOL .
        "-------------------------" . PHP_EOL . PHP_EOL;

    file_put_contents($log_file, $log, FILE_APPEND);
}

/**
 * Clear cart after logout
 * @action wp_logout
 * occurs after logout
 */
add_action(
    'wp_logout',
    function () {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
    }
);


////////////////////////////////////////////////

if (file_exists(get_template_directory() . '/ysg/functions/functions_general.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_general.php';
}


if (file_exists(get_template_directory() . '/ysg/functions/functions_api.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_api.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/checkout_functions.php')) {
    include_once get_template_directory() . '/ysg/functions/checkout_functions.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/order_functions.php')) {
    include_once get_template_directory() . '/ysg/functions/order_functions.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/functions_users.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_users.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/functions_admin.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_admin.php';
}

if (file_exists(get_template_directory() . '/ysg/functions/functions_forms.php')) {
    include_once get_template_directory() . '/ysg/functions/functions_forms.php';
}
