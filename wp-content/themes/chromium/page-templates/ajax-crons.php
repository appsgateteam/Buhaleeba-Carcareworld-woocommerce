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


if (isset($_GET['action']) && $_GET['action'] == "get_cities_country") {
    $code = $_GET['country_code'];
    $cstype = $_GET['cstype'];
    $all_cities = ysgMainGetCCities($code);

    if (empty($all_cities)) {
        echo  json_encode(array(
            "success" => 1,
            "cities" => ""
        ));
        return;
    }

    $text = "<option>Select City / Area</option>";
    $all_areas = array();
    foreach ($all_cities as $a) {
        $text = $text . "<optgroup label='" . $a->name . "'>";
        if (!empty($a->areas)) {
            foreach ($a->areas as $r) {
                $text = $text . '<option value="' . $r->term_id . '">';
                $text = $text . $r->name . ", " . $a->name;
                $text = $text . '</option>';

                $all_areas[] = $r;
            }
        }
        $text = $text . "</optgroup>";
    }

    echo json_encode(array(
        "success" => 1,
        "cstype" => $cstype,
        "cities" => $all_cities,
        "all_areas" => $all_areas,
        "html" => $text
    ));
    exit;
}