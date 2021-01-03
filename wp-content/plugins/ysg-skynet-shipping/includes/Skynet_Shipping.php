<?php

if (!function_exists('add_action')) {
    echo 'You cannot access directly.';
    exit;
}

class Skynet_Shipping
{
    private $full_shipping_cost = 0;
    public function __construct()
    {
    }
    /**
     * calculates AE shipping cost
     * getAEShippingCost
     */
    public function getAEShippingCost($package)
    {
        $weight = $full_cost = $cost = 0;
        $country = $package["destination"]["country"];
        $state_area = $package["destination"]["city"];

        $the_country = $this->getCountryDetails($country);

        $the_city = $this->getCityDetails($state_area);

        foreach ($package['contents'] as $item_id => $values) {
            $_product = $values['data'];
            $weight = $weight + $_product->get_weight() * $values['quantity'];
        }

        $weight = wc_get_weight($weight, 'kg');

        if (isset($the_city) && !empty($the_city->is_remote) && $the_city->is_remote === true) {
            //remote city cost
            if ($weight <= $the_country->standard_shipping_weight) {
                //normal remote shipping calculation applies
                $cost = $the_country->standard_cost_remote;
                $full_cost = $the_country->standard_cost_odoo_remote;
            } else {
                $cost = $the_country->standard_cost_remote + (($weight - $the_country->standard_shipping_weight) * $the_country->additional_cost_per_kg_remote);

                $full_cost = $the_country->standard_cost_odoo_remote + (($weight - $the_country->standard_shipping_weight) * $the_country->additional_cost_per_kg_remote);
            }
        } else {
            if ($weight <= $the_country->standard_shipping_weight) {
                //normal shipping calculation applies
                $cost = $the_country->standard_cost;
                $full_cost = $the_country->standard_cost_odoo;
            } else {
                $cost = $the_country->standard_cost + (($weight - $the_country->standard_shipping_weight) * $the_country->additional_cost_per_kg);

                $full_cost = $the_country->standard_cost_odoo + (($weight - $the_country->standard_shipping_weight) * $the_country->additional_cost_per_kg);
            }
        }   //get city and country settings

        $this->full_shipping_cost = $full_cost;
        if (!empty($the_country->free_shipping_amount) && $package['cart_subtotal'] >= $the_country->free_shipping_amount) {
            return 0;
        }

        return $cost;
    }

    /**
     * @function getFullShippingCost
     * @access public
     * @return float $ccost
     */
    public function getFullShippingCost()
    {
        return $this->full_shipping_cost;
    }

    /**
     * get the shipping details of the selected country based on coutry code
     * @function getCountryDetails
     * @access private
     * @return object
     */

    private function getCountryDetails($country_code)
    {
        $args = array(
            'numberposts'    => 1,
            'post_type'        => 'ysg_wcs_country',
            'meta_key'        => 'ysg_wcs_c_code',
            'meta_value'    => $country_code
        );
        $countries = get_posts($args);

        if (empty($countries)) {
            return FALSE;
        }

        foreach ($countries as $item) {
            $country = $item;
        }

        //get values
        $the_country = (array) $country;

        $the_country['free_shipping_amount'] = get_field("ysg_wc_free_shipping", $country->ID);
        $the_country['standard_cost'] = get_field("ysg_wc_ssc", $country->ID);
        $the_country['standard_cost_odoo'] = get_field("ysg_wc_ssc_odoo", $country->ID);
        $the_country['standard_shipping_weight'] = get_field("ysg_wcs_mssw", $country->ID);

        $the_country['additional_cost_per_kg'] = get_field("ysg_wc_acpkg", $country->ID);
        $the_country['additional_cost_per_kg_remote'] = get_field("ysg_wc_acpkg_remote", $country->ID);
        $the_country['standard_cost_remote'] = get_field("ysg_wcs_sscc_remote", $country->ID);
        $the_country['standard_cost_odoo_remote'] = get_field("ysg_wcs_ssc_full_remote", $country->ID);

        return (object) $the_country;
    }

    /**
     * gets the city details based on the provided city/area
     * @function getCityDetails
     * @access private
     */
    private function getCityDetails($city_area)
    {
        $city = get_term($city_area, 'ysg_wc_shipping_cities');

        if (empty($city)) {
            return FALSE;
        }

        $the_city = (array) $city;
        $the_city['is_remote'] = get_field("ysg_wcs_is_remote", "ysg_wc_shipping_cities_" . $city->term_id);

        return (object) $the_city;
    }
    /** 
     * @function sendCurlRequest
     */
    private function sendCurlRequest()
    {
    }
}
