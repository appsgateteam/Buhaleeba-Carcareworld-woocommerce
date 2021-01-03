<?php

/**
 * Adds a new column to the "My Orders" table in the account.
 * * @param string[] $columns the columns in the orders table
 * @return string[] updated columns
 */
/*add_filter('woocommerce_my_account_my_orders_columns', function ($columns) {
    $new_columns = array();
    foreach ($columns as $key => $name) {
        $new_columns[$key] = $name;
        // add ship-to after order status column
        if ('custom' === $key) {
            $new_columns['order-ship-to'] = __('Ship to', 'textdomain');
        }
    }

    return $new_columns;
});*/


add_filter('woocommerce_my_account_my_orders_actions', function ($actions, $order) {
    $status = $order->get_status();
    $order_id =    $order->get_id();

    //add invoice link
    $invoice_link = $order->get_transaction_id();

    if (!empty($invoice_link)) {
        $actions['invoice'] = array(
            // adjust URL as needed
            'url'  => $invoice_link,
            'name' => __('Invoice')
        );
    }


    $payment_method = $order->payment_method;
    $can_cancel = false;

    if ($status === "processing") {
        $can_cancel = true;
    }

    $sts = array('processing', 'wc-ysg-pickupcod', 'wc-ysg-pickuppaid', 'wc-ysg-shipped');
    if ($payment_method == "cod") {
        $can_cancel = true;
    } else if ($payment_method == "cod" && in_array($status, $sts)) {
        $can_cancel = true;
    }

    if ($can_cancel == true) {
        $actions['cancel'] = array(
            // adjust URL as needed
            'url'  => site_url() . '/user-cancel-order/?order_id=' . $order_id,
            'name' => __('Cancel')
        );
    }


    return $actions;
}, 10, 2);
