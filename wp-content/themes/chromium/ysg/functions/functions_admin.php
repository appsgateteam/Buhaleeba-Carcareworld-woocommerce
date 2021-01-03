<?php

/**
 * add tracking status column to the admin order table
 */
add_filter('manage_edit-shop_order_columns', function ($columns) {
    $columns['ysg_tracking_status'] = 'Tracking Status';
    return $columns;
});

/**
 * get and show tracking status value of each order in admin order table list
 */
add_action('manage_shop_order_posts_custom_column', function ($column) {
    global $post;
    if ('ysg_tracking_status' === $column) {
        $order = wc_get_order($post->ID);
        echo get_post_meta($order->id, 'ysg_tracking_status', true);;
    }
});

/**
 * Remove certain columns from products list page table
 */
add_filter('manage_edit-product_columns', function ($columns) {
    unset($columns['total_subscribers']);
    unset($columns['taxonomy-ysg_product_type']);
    unset($columns['product_tag']);
    return $columns;
}, 10, 1);


/**
 * add cities link to the admin menu
 */
add_action(
    'admin_menu',
    function () {
        add_menu_page('linked_url', 'Shipping Cities / Areas', 'read', '/edit-tags.php?taxonomy=ysg_wc_shipping_cities', '', 'dashicons-text', 58);
    }
);


/**
 * show payment status & tracking status in order information 
 */
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


add_action('woocommerce_product_options_general_product_data', function () {
    global $woocommerce, $post;
    echo '<div class=" product_custom_field ">';
    woocommerce_wp_text_input(
        array(
            'id'          => 'ysg_quote_min_q',
            'label'       => __('Minimum Quotation Quantity', 'woocommerce'),
            'placeholder' => 'Minimum Quote Quantity',
            'type'    => 'number'
        )
    );

    echo '</div>';
});


add_action('woocommerce_process_product_meta', function ($post_id) {
    //wc_get_product($post_id);
    $quantity = $_POST['ysg_quote_min_q'];
    update_post_meta($post_id, 'ysg_quote_min_q', esc_html($quantity));
});
