<?php

/**
 * Template Name: User Cancel Order Page
 */

use FontLib\Table\Type\head;

if (is_user_logged_in()) {
    $current_user_id = get_current_user_id();
} else {
    header("location:" . site_url());
    exit;
}

//chcek if user has the order
$order_id = $_GET['order_id'];
$order = wc_get_order($order_id);
$order_cancelled = false;

$confirm = $_GET['confirmed'];
if (!empty($confirm) and $confirm === "1") {
    $order->update_status("cancelled");
    $order_cancelled = true;

    //send cancellation email
}


get_header(); ?>
<main class="site-content" role="main" itemscope="itemscope" itemprop="mainContentOfPage">
    <!-- Main content -->
    <section class="ucancel-order-section">
        <div class="content">
            <?php if ($order_cancelled == false) : ?>
                <h3>Are you sure you want to cancel the order : #<?php echo $order_id ?>?</h3>

                <div class="hold_cancel_btns">
                    <a class="cancel_btn" href="<?php echo site_url() . '/user-cancel-order/?confirmed=1&order_id=' . $order_id; ?>">
                        Yes
                    </a>

                    <a class="cancel_btn back_btn_cancel" href="<?php echo site_url() . '/my-account-2/orders/'; ?>">
                        Cancel
                    </a>
                </div>
            <?php else : ?>

                <div class="woocommerce-message" role="alert">
                    <a href="<?php echo site_url() . '/my-account-2/orders/'; ?>" tabindex="1" class="button wc-forward">
                        Back to orders
                    </a> Order #<?php echo $order_id ?> has been cancelled.
                </div>

            <?php endif; ?>

        </div>
    </section>
</main>


<?php get_footer(); ?>