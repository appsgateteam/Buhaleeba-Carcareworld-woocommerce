<?php

/**
 * Template Name: Ajax Cron
 */
if (isset($_GET['action']) && $_GET['action'] == "check_shipping_status") {

    if (file_exists(get_template_directory() . '/ysg/functions/classes/ShippingClass.php')) {
        include_once get_template_directory() . '/ysg/functions/classes/ShippingClass.php';

        $ysg_skynet_shipping = new ShippingClass();
        $ysg_skynet_shipping->updateShippingStatus();
    }
}
