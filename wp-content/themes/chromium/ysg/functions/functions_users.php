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
            'url'  => $invoice_link,
            'name' => __('Invoice')
        );
    }

    /*
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
*/

    return $actions;
}, 10, 2);


/**
 * @snippet       Send Formatted Email @ WooCommerce Custom Order Status
 * @how-to        Get CustomizeWoo.com FREE
 * @sourcecode    https://businessbloomer.com/?p=91907
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 3.5.7
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

// Targets custom order status "refused"
// Uses 'woocommerce_order_status_' hook

add_action(
    'woocommerce_order_status_changed',
    function ($order_id, $from_status, $to_status) {

        global $woocommerce;
        $heading = 'Cancellation Request';
        $subject = 'Order Cancellation Request';

        // Get WooCommerce email objects
        $mailer = WC()->mailer()->get_emails();

        if ($to_status == "cancel-request") {
            //send email to admin about  order change
            $order = new WC_Order($order_id);
            // Create a mailer
            $mailer = $woocommerce->mailer();
            $message_body = __('Order Cancelation Request');
            $message = $mailer->wrap_message(
                // Message head and message body.
                sprintf(__('Order %s :  Cancellation Request'), $order->get_order_number()),
                $message_body
            );
            $admin_email = get_option("admin_email");
            // Cliente email, email subject and message.
            $mailer->send($admin_email, sprintf(__('Order %s : Cancel Request'), $order->get_order_number()), $message);
        }
    },
    20,
    3
);
