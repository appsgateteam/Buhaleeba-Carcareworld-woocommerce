<?php

/**
 * Plugin Name: YSG Skynet Shipping
 * Plugin URI: 
 * Description: Does skynet shipping 
 * Author: YSG
 * Version: 0.0.1
 * Author URI: https://oneandorange.com
 *
 * YSG Skynet Shipping  is released under the GNU General Public License (GPL)
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package ysg-skynet-shipping
 */

/**
 * The main YSG-Skynet Shipping Class.
 * uses woocommerce
 */
if (!defined('WPINC')) {
    die;
}

define('YSG_SKYNET_SHIPPING', plugin_dir_path(__FILE__));

//includes
require_once YSG_SKYNET_SHIPPING . 'includes/Skynet_Shipping.php';

global $ysg_sfull_price;
/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function ysg_skynet_shipping_method()
    {
        if (!class_exists('YSG_Skynet_Shipping_Method')) {
            class YSG_Skynet_Shipping_Method extends WC_Shipping_Method
            {
                var $ysg_skynet_main_shipping_class;
                public $full_cost = 0;
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct()
                {
                    $this->ysg_skynet_main_shipping_class = new Skynet_Shipping();

                    $this->id                 = 'ysg_skynet';
                    $this->method_title       = __('Skynet Shipping', 'ysg_skynet');
                    $this->method_description = __('Skynet Shipping Method', 'ysg_skynet');
                    $this->tax_status = 'taxable';

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array(
                        'KW', // Kuwait
                        'SA', // Saudi Arabia
                        'OM',   // Oman
                        'BH', // Bahrain
                        'AE'  // United Arab Emirates
                    );

                    $this->init();

                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Skynet Shipping', 'ysg_skynet');
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                /**
                 * Define settings field for this shipping
                 * @return void 
                 */
                function init_form_fields()
                {

                    $this->form_fields = array(

                        'enabled' => array(
                            'title' => __('Enable', 'ysg_skynet'),
                            'type' => 'checkbox',
                            'description' => __('Enable this shipping.', 'ysg_skynet'),
                            'default' => 'yes'
                        ),

                        'title' => array(
                            'title' => __('Title', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('Custom Skynet Shipping', 'ysg_skynet'),
                            'default' => __('Custom Skynet Shipping', 'ysg_skynet')
                        ),

                        'weight' => array(
                            'title' => __('Weight (kg)', 'ysg_skynet'),
                            'type' => 'number',
                            'description' => __('Maximum allowed weight', 'ysg_skynet'),
                            'default' => 100
                        ),

                        'company_name' => array(
                            'title' => __('Company Name', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('Company Name', 'ysg_skynet'),
                            'default' => "Skynet Shipping"
                        ),

                        'address' => array(
                            'title' => __('Address', 'ysg_skynet'),
                            'type' => 'textarea',
                            'description' => __('Address', 'ysg_skynet'),
                            'default' => 100
                        ),

                        'city' => array(
                            'title' => __('City', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('City', 'ysg_skynet'),
                            'default' => 100
                        ),

                        'country' => array(
                            'title' => __('Country', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('Country', 'ysg_skynet'),
                            'default' => "United Arab Emirates"
                        ),

                        'email' => array(
                            'title' => __('Email', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('Email', 'ysg_skynet'),
                            'default' => ""
                        ),

                        'phone' => array(
                            'title' => __('Phone', 'ysg_skynet'),
                            'type' => 'text',
                            'description' => __('Phone Number', 'ysg_skynet'),
                            'default' => ""
                        ),
                    );
                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping($package = array())
                {
                    global $ysg_sfull_price;
                    $cost = $this->ysg_skynet_main_shipping_class->getAEShippingCost($package);
                    $cost_without = $cost * (100 / 105);
                    $cost_tax = $cost - $cost_without;

                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost_without,
                        'taxes'     => array($cost_tax),
                        'calc_tax' => 'per_order'
                    );
                    //
                    $this->full_cost = $this->ysg_skynet_main_shipping_class->getFullShippingCost();
                    $this->setFullPrice();

                    $this->add_rate($rate);
                }
                /**
                 * @function setFullPrice
                 * @access private
                 * @return void
                 */
                private function setFullPrice()
                {
                    $_SESSION['ysg_full_price'] = $this->full_cost;
                }

                /**
                 * @function ysgGetSettings
                 * @access public
                 * @return array
                 */
                public function ysgGetSettings()
                {
                    return $this->settings;
                }
            }
        }
    }

    add_action('woocommerce_shipping_init', 'ysg_skynet_shipping_method');

    function add_ysg_skynet_shipping_method($methods)
    {
        $methods[] = 'YSG_Skynet_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_ysg_skynet_shipping_method');

    /////
    function ysg_skynet_shipping_validate_order($posted)
    {
        $packages = WC()->shipping->get_packages();

        $chosen_methods = WC()->session->get('chosen_shipping_methods');

        if (is_array($chosen_methods) && in_array('ysg_skynet', $chosen_methods)) {

            foreach ($packages as $i => $package) {

                if ($chosen_methods[$i] != "ysg_skynet") {
                    continue;
                }

                $YSG_Skynet_Shipping_Method = new YSG_Skynet_Shipping_Method();
                $weightLimit = (int) $YSG_Skynet_Shipping_Method->settings['weight'];
                $weight = 0;

                foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];
                }

                $weight = wc_get_weight($weight, 'kg');

                if ($weight > $weightLimit) {

                    $message = sprintf(__('Sorry, %d kg exceeds the maximum weight of %d kg for %s', 'ysg_skynet'), $weight, $weightLimit, $YSG_Skynet_Shipping_Method->title);

                    $messageType = "error";

                    if (!wc_has_notice($message, $messageType)) {
                        wc_add_notice($message, $messageType);
                    }
                }
            }
        }
    }

    //add_action('woocommerce_review_order_before_cart_contents', 'ysg_skynet_shipping_validate_order', 10);
    //add_action('woocommerce_before_cart_table', 'ysg_skynet_shipping_validate_order', 10);
    add_action('woocommerce_after_checkout_validation', 'ysg_skynet_shipping_validate_order', 10);
}
