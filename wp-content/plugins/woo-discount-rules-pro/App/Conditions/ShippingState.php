<?php

namespace WDRPro\App\Conditions;
if (!defined('ABSPATH')) {
    exit;
}
use Wdr\App\Conditions\Base;

class ShippingState extends Base
{
    function __construct()
    {
        parent::__construct();
        $this->name = 'shipping_state';
        $this->label = __('State', WDR_PRO_TEXT_DOMAIN);
        $this->group = __('Shipping', WDR_PRO_TEXT_DOMAIN);
        $this->template = WDR_PRO_PLUGIN_PATH . 'App/Views/Admin/Conditions/Shipping/state.php';
    }

    public function check($cart, $options)
    {
        $check_country = $country_based_validation = false;
        if (isset($options->value) && isset($options->operator)) {
            if(isset($options->countries) && !empty(isset($options->countries))){
                $country_based_validation = true;
                $shipping_country = $this->input->post('calc_shipping_country', NULL);
                if (empty($shipping_country) || is_null($shipping_country)) {
                    $shipping_country = self::$woocommerce_helper->getShippingCountry();
                }
                if (!empty($shipping_country)) {
                    $check_country = $this->doCompareInListOperation($options->operator, $shipping_country, $options->countries);
                }
            }
            if(($country_based_validation && $check_country) || (!$country_based_validation && !$check_country)){
                $shipping_state = $this->input->post('calc_shipping_state', NULL);
                if (empty($shipping_state) || is_null($shipping_state)) {
                    $shipping_state = self::$woocommerce_helper->getShippingState();
                }
                if (!empty($shipping_state)) {
                    return $this->doCompareInListOperation($options->operator, $shipping_state, $options->value);
                }
            }
        }
        return false;
    }
}