<?php
// Register new status
function ysg_register_custom_order_statuses()
{
    register_post_status('wc-ysg-pickuppaid', array(
        'label'                     => 'Pickup - Paid',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Pickup - Paid (%s)', 'Pickup - Paid (%s)')
    ));

    register_post_status('wc-ysg-pickupcod', array(
        'label'                     => 'Pickup - COD',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Pickup - COD (%s)', 'Pickup - COD (%s)')
    ));

    register_post_status('wc-ysg-shipped', array(
        'label'                     => 'Out for Delivery',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Out for delivery (%s)', 'Pickup - COD (%s)')
    ));
}
add_action('init', 'ysg_register_custom_order_statuses');


// Add to list of WC Order statuses
function ysg_add_custom_order_statuses($order_statuses)
{
    $new_order_statuses = array();

    // add new order status after processing
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-ysg-pickuppaid'] = 'Pickup - Paid';
            $new_order_statuses['wc-ysg-pickupcod'] = 'Pickup - COD';
            $new_order_statuses['wc-ysg-shipped'] = 'Out for delivery';
        }
    }

    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'ysg_add_custom_order_statuses');


add_action(
    'woocommerce_after_add_to_cart_button',
    function () {
        $show_button = apply_filters('yith_ywraq-show_btn_single_page', true);
        if (!yith_plugin_fw_is_true($show_button)) {
            return;
        }

        $pid = get_the_ID();
        $min_qty = get_post_meta($pid, "ysg_quote_min_q", true);
        if (empty($min_qty)) {
            $min_qty = 5;
        }

        echo '<div class="yg_all_quote_info">';
        echo '<div class="info_yqtye">The minimum quantity for quotation is : ' . $min_qty . '</div>';
        echo '<div class="enmquots_ysg">';
        echo '<input type="number" class="quote_qty js_ysg_quote_qty" value="" min="' . $min_qty . '" placeholder="Quotation Quantity" />';
    },
    9
);
add_action(
    'woocommerce_after_add_to_cart_button',
    function () {
        $show_button = apply_filters('yith_ywraq-show_btn_single_page', true);
        if (!yith_plugin_fw_is_true($show_button)) {
            return;
        }
        echo "</div></div>";
    },
    16
);
