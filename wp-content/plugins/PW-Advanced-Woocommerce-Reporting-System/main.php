<?php
/*
Plugin Name: PW Advanced Woo Reporting
Plugin URI: http://proword.net/Plugins/Advanced_Reporting/
Description: WooCommerce Advance Reporting plugin is a comprehensive and the most complete reporting system.
Version: 5.7
Author: Proword
Author URI: http://proword.net/
Text Domain: pw_report_wcreport_textdomain
Domain Path: /languages/
*/


/*
v5.7
    Fixed: Compatible with WooCommerce 4.6.x
    Fixed: Delivery Date Value
    Fixed: Some Functionality issues

v5.6
    Fixed: Compatible with Wordpress 5.5.x
    Fixed: Duplicate the address in Order Billing Report
    Fixed: Some Functionality issues

v5.5
    Fixed: Some Functionality issues

v5.4
	Fixed : Purchased Product Qty Export issue
	Fixed : Cross Tab Reports Qty Sql
	Fixed : Unformated Email
	Added : Email Optimization
    Added : Translate file for FR by Mr.C.Ferrandon

v5.3
	Fixed : Purchase Product Qty Sql
	Fixed : Ajax error in "All Orders" report
	Fixed : Fixed some functionality issues

v5.2
	Fixed : Fixed some functionality issues
	Fixed : Fixed some illogical issues

v5.1
	Fixed : Some of Accounting value
	Fixed : Fixed some functionality issues

v5.0
	Fixed : SQL Error in Email
*/

/*
V4.9
	Added : All Orders per Billing
	Added : Compatible with Order Delivery Date
	Added : Product Qty
	Added : Variation Qty
	Added : Add Custom Taxonomy in Variation Reports
	Added : Add Shipping Column in Basic Mode for All Orders
	Fixed : Analysis Simple Products Issue
	Fixed : Analysis Variation Products Issue
	Fixed : Tax Reports Total Number Issue
	Fixed : Customer Analysis

V4.8
	Fixed : Charts not Working in Dashboard

V4.7
	Fixed : Datepicker in Safari
	Fixed : Business Intelligence Reports Add-on issues

V4.6
	Compatibile with WooCommerce 3.3.X and WordPress 4.9.5

V4.5
	Update : Compatible with Business Intelligence Reports Add-on
	Update : Add Brands Column to Variation Report

V4.4
	Fixed : Price Column in Excel Export
	Fixed : Didplay SKU for some products
	Update : Compatible with new version of CrossTavs Add-on

V4.3
	Adde  : Footer text inn invoice Pdf
	Fixed : Grodd Amount in All Orders report
	Fixed : Profit report issue
	Fixed : Display Products in Invoice Pdf
	Fixed : Compatible with PHP 7.1
	Fixed : Compatible with PHP 5.3

V4.2
	Fixed : Some issue with Custom Taxonomy and Fields Add On
	Fixed : Gross amount and Partial Refund in "All Orders" report

V4.1
	Fixed : Visible on dashboard menu
	Fixed : SKU column in All Orders
	Fixed : http error for amchart


V4.0
	Added : Add Favorite
	Upgrade : Compatible with Cost of Goods Plugins : WooCommerce Cost of Goods by Woothemes (https://woocommerce.com/products/woocommerce-cost-of-goods/)  and WooCommerce Profit of Sales Report by IndoWebKreasi (http://indowebkreasi.com/posr)
	Upgrade : Compatible with WooCommerce Extra Product Options Plugin (https://codecanyon.net/item/woocommerce-extra-product-options/7908619)
	Upgrade : Compatible with popular Brands Plugins : WooCommerce Brands by Proword (https://codecanyon.net/item/woocommerce-brands/8039481), WooCommerce Brands By Woothemes(https://woocommerce.com/products/brands/) , Ultimate WooCommerce Brands Plugin (https://codecanyon.net/item/ultimate-woocommerce-brands-plugin/9433984), YITH WOOCOMMERCE BRANDS ADD-ON (http://yithemes.com/themes/plugins/yith-woocommerce-brands-add-on/)
	Upgrade : Compatible with popular Invoice Plugins : WooCommerce PDF Invoices (https://codecanyon.net/item/woocommerce-pdf-invoice), WooCommerce PDF Invoices (https://docs.woocommerce.com/document/woocommerce-pdf-invoice-setup-and-customization/)
	Added : Invoice in "All Orders" report
	Added : "All Orders" per Country
	Added : Send reports via Email in schedule time
	Upgrade : UI
	Added : New reports : Analysis Products, Stock Reports, Customer Analysis and ...
	Added : Cost of Good Reports
	Added : RTL Support
	Upgrade : Ajax structure
	Fixed : Tax Reports Issue
V3.1
	fixed : Chart icons broken in dashboard
	fixed : Not appear some columns in chart
	fixed : Stock List report for 0 stock
	fixed : Currency Columns Order
*/

if ( ! class_exists('pw_report_wcreport_class')) {

    //USE IN INCLUDE
    define('__PW_REPORT_WCREPORT_ROOT_DIR__', dirname(__FILE__));

    //USE IN ENQUEUE AND IMAGE
    define('__PW_REPORT_WCREPORT_CSS_URL__', plugins_url('assets/css/', __FILE__));
    define('__PW_REPORT_WCREPORT_JS_URL__', plugins_url('assets/js/', __FILE__));
    define('__PW_REPORT_WCREPORT_URL__', plugins_url('', __FILE__));

    //PERFIX
    define('__PW_REPORT_WCREPORT_FIELDS_PERFIX__', 'custom_report_');

    //TEXT DOMAIN FOR MULTI LANGUAGE
    define('__PW_REPORT_WCREPORT_TEXTDOMAIN__', 'pw_report_wcreport_textdomain');

    //COST OF GOOF PRICE
    //define ('__PW_COG__','_PW_COST_GOOD_FIELD');

    include('includes/datatable_generator.php');
    include('class/mail_class.php');
    load_plugin_textdomain(__PW_REPORT_WCREPORT_TEXTDOMAIN__, false,
        dirname(plugin_basename(__FILE__)) . '/languages/');


    //new pw_report_wcreport_crosstab_addon_class;

    //CUSTOM WORK ID
    $customwork_id = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'customwork_id', 0);
    if ($customwork_id != 0) {
        $customwork_id = explode(",", $customwork_id);
    }
    define('__CUSTOMWORK_ID__', $customwork_id);

    //MAIN CLASS
    class pw_report_wcreport_class extends pw_rpt_datatable_generate
    {

        public $pw_plugin_status = '';

        public $pw_plugin_main_url = '';

        public $pw_core_status = '';

        public $pw_shop_status = '';

        public $otder_status_hide = '';

        public $today = '';

        public $datetime = null;

        public $pw_firstorderdate = '';

        public $our_menu = '';
        public $our_menu_fav = '';

        ////ADDED IN VER4.0
        //CHECK LICENSE & UPDATE
        public $plugin_slug = '';
        public $username = '';
        public $email = '';
        public $api_key = '';
        public $item_valid_id = '';
        public $domain = '';
        public $license_key = '';
        public $api_url = '';

        //public $menu_fields='';

        function __construct()
        {

            //Included Variation and CrossTab
            //register_activation_hook( __FILE__, array( $this,'pw_activate_hook') );
            //add_action( 'upgrader_process_complete', array( $this,'pw_activate_hook') );

            include('includes/actions.php');
            //include('class/customefields.php');

            ////ADDED IN VER4.0
            //SET DEAFULT VALUES
            $this->username      = 'proword';
            $this->api_key       = 't0kbg3ez6pl5yo1ojhhoja9d64swh6wi';
            $this->item_valid_id = '12042129'; //8218941
            $url                 = $_SERVER['SERVER_NAME'];
            $this->domain        = $this->getHost($url);
            $this->license_key   = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_purchase_code');
            $this->email         = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_email', '');
            $this->api_url       = 'https://proword.net/Update_Plugins/';


            add_action('admin_init', array($this, 'pw_standalone_report'));
            add_filter('login_redirect', array($this, 'my_login_redirect'), 10, 3);
            add_filter('woocommerce_login_redirect', array($this, 'my_login_redirect_woo'), 10, 2);
            //add_action('current_screen', array($this,'my_login_redirect'));


            add_action('admin_head', array($this, 'pw_report_backend_enqueue'));
            add_action('plugins_loaded', array($this, 'loadTextDomain'));
            add_action('admin_menu', array($this, 'pw_report_setup_menus'));

            $field                  = __PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'activate_purchase_code';
            $this->pw_plugin_status = get_option($field);

            $this->pw_core_status = false;

            if (get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'dashboard_status') == 'false' && get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'dashboard_alt') != 'dashboard') {
                $pw_plugin_main_url       = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'dashboard_alt');
                $pw_plugin_main_url       = explode("admin.php?page=", $pw_plugin_main_url);
                $this->pw_plugin_main_url = $pw_plugin_main_url[1];
            } else {
                $this->pw_plugin_main_url = 'wcx_wcreport_plugin_dashboard&parent=dashboard&smenu=dashboard';
            }


            $this->today = date_i18n("Y-m-d");

            //DEFAULT ORDER STATUS AND HIDE STATUS
            $pw_shop_status = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'order_status');
            if ($pw_shop_status != '') {
                $this->pw_shop_status = implode(",", $pw_shop_status);
            } else {
                $this->pw_shop_status = 'wc-completed,wc-on-hold,wc-processing';
            }

            $otder_status_hide = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'otder_status_hide');
            if ($otder_status_hide == 'on') {
                $this->otder_status_hide = 'trash';
            }

            //////////ADD COST OF GOOD CUSTOM FIELD//////////
            ///add_action( 'woocommerce_product_options_general_product_data', array($this,'pw_add_custom_price_box') );
            ///add_action( 'woocommerce_process_product_meta',  array($this,'pw_custom_woocommerce_process_product_meta'), 2 );
            ///add_action( 'woocommerce_process_product_meta_variable',  array($this,'pw_custom_woocommerce_process_product_meta'), 2 );


            // Add Variation Settings
            ///add_action( 'woocommerce_product_after_variable_attributes', array($this,'variation_settings_fields'), 10, 3 );
            // Save Variation Settings

            ///add_action( 'woocommerce_save_product_variation', array($this,'save_variation_settings_fields'), 10, 2 );


            //add_filter( 'woocommerce_get_price_html', array($this,'pw_add_custom_price_front'), 10, 2 );
            //add_filter( 'woocommerce_get_variation_price_html', array($this,'add_custom_price_front'), 10, 2 );

            //
            //add_action( 'woocommerce_before_calculate_totals', array($this,'woo_add_donation'));


            //SET THE COST OF GOOD CUSTOM FIELD
            $enable_cog = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'enable_cog', "no");
            if ($enable_cog == 'yes_another') {
                $cog_plugin    = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cog_plugin', "woo_profit");
                $profit_fields = array(
                    'woo_profit'  => array(
                        'field'     => '_wc_cog_cost', // FOR EACH PRODUCT -> postmeta
                        'total'     => '_wc_cog_item_total_cost', // FOR EACH ITEM of ORDER -> order_itemmeta
                        'order_cog' => 'wc_cog_order_total_cost', // FOR EACH ORDER -> postmeta
                    ),
                    'indo_profit' => array(
                        'field'     => '_posr_cost_of_good', // FOR EACH PRODUCT -> postmeta
                        'total'     => '_posr_line_cog_total', // FOR EACH ITEM of ORDER -> order_itemmeta
                        'order_cog' => '_posr_line_cog_total', // FOR EACH PRODUCT -> postmeta
                    ),

                );

                if ($cog_plugin == 'other') {
                    $cog_field = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cog_field', "_PW_COST_GOOD_FIELD");
                    define('__PW_COG__', $cog_field);

                    $cog_field = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cog_field_total',
                        "_PW_COST_GOOD_FIELD");
                    define('__PW_COG_TOTAL__', $cog_field);
                } else {
                    $cog_field = $profit_fields[$cog_plugin]['field'];
                    define('__PW_COG__', $cog_field);

                    $cog_total = $profit_fields[$cog_plugin]['total'];
                    define('__PW_COG_TOTAL__', $cog_total);

                    $order_cog = $profit_fields[$cog_plugin]['order_cog'];
                    define('__PW_COG_ORDER_TOTAL__', $order_cog);
                }

            } elseif ($enable_cog == 'yes_this') {

                //include('add-ons/woocommerce-cost-of-goods-Proword/main.php');


                define('__PW_COG__', '_PW_COST_GOOD_FIELD');
                define('__PW_COG_TOTAL__', '_PW_COST_GOOD_ITEM_TOTAL_COST');
            } else {
                define('__PW_COG__', '');
                define('__PW_COG_TOTAL__', '');
            }


            ////ADDED IN VER4.0
            ///////////////////////////BRANDS ADD-ONS///////////////////
            $brand_slug = $brand_label = $brand_thumb = '';
            if (defined("__PW_BRANDS_ADD_ON__")) {

                $enable_brands = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'enable_brand', "no");
                $brand_thumb   = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'brand_thumb');

                if ($brand_thumb == 'on') {
                    $brand_thumb = 1;
                } else {
                    $brand_thumb = '';
                }

                if ($enable_brands == 'yes_another') {
                    $brand_plugin = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'brands_plugin', "product_brand");
                    $brand_label  = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'brand_label',
                        __('Brand', __PW_REPORT_WCREPORT_TEXTDOMAIN__));


                    $brand_slug  = $brand_plugin;
                    $brand_thumb = '';

                    if ($brand_plugin == 'other') {
                        $brand_field = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'brand_slug', "product_brand");
                        $brand_slug  = $brand_field;
                        $brand_thumb = '';
                    }

                    //PROWORD BRAND PLUGIN
                } elseif ($enable_brands == 'yes_this') {
                    $brand_slug  = 'product_brand';
                    $brand_label = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'brand_label',
                        __('Brand', __PW_REPORT_WCREPORT_TEXTDOMAIN__));

                }
            }
            define('__PW_BRAND_SLUG__', $brand_slug);
            define('__PW_BRAND_LABEL__', $brand_label);
            define('__PW_BRAND_THUMB__', $brand_thumb);

            ////ADDED IN VER4.0
            /// AUTO UPDATE
            $this->plugin_slug = basename(dirname(__FILE__));
            add_filter('pre_set_site_transient_update_plugins', array($this, 'pw_report_check_for_plugin_update'));
            // Take over the Plugin info screen
            add_filter('plugins_api', array($this, 'pw_report_plugin_api_call'), 10, 3);

        }

        function pw_date_format($date)
        {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return '%Y-%m-%d';
            }

            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                return '%d-%m-%Y';
            }
        }

        function pw_report_check_for_plugin_update($checked_data)
        {
            global $api_url, $plugin_slug, $wp_version;
            $plugin_slug   = $this->plugin_slug;
            $domain        = $this->domain;
            $license_key   = $this->license_key;
            $email         = $this->email;
            $item_valid_id = $this->item_valid_id; //8218941
            $api_url       = $this->api_url;

            update_option("UPDATE", $license_key . $domain . 'AW' . $plugin_slug);
            //Comment out these two lines during testing.
            if (empty($checked_data->checked)) {
                return $checked_data;
            }

            $args           = array(
                'slug'    => $plugin_slug,
                'version' => $checked_data->checked[$plugin_slug . '/main.php'],
            );
            $request_string = array(
                'body'       => array(
                    'action'      => 'basic_check',
                    'request'     => serialize($args),
                    'api-key'     => md5(get_bloginfo('url')),
                    'license-key' => $license_key,
                    'email'       => $email,
                    'domain'      => $domain,
                    'item-id'     => $item_valid_id,

                ),
                'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
            );

            // Start checking for an update
            $raw_response = wp_remote_post($api_url, $request_string);

            $response = '';
            if ( ! is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) && isset($raw_response['body']) && is_array($raw_response['body'])) {
                $response = unserialize($raw_response['body']);
            }

            if (is_object($response) && ! empty($response)) // Feed the update data into WP updater
            {
                $checked_data->response[$plugin_slug . '/main.php'] = $response;
            }

            return $checked_data;
        }

        function pw_report_plugin_api_call($def, $action, $args)
        {
            global $plugin_slug, $api_url, $wp_version;

            $plugin_slug   = $this->plugin_slug;
            $domain        = $this->domain;
            $license_key   = $this->license_key;
            $email         = $this->email;
            $item_valid_id = $this->item_valid_id; //8218941
            $api_url       = $this->api_url;

            if ( ! isset($args->slug) || ($args->slug != $plugin_slug)) {
                return false;
            }

            // Get the current version
            $plugin_info     = get_site_transient('update_plugins');
            $current_version = $plugin_info->checked[$plugin_slug . '/main.php'];
            $args->version   = $current_version;

            $request_string = array(
                'body'       => array(
                    'action'      => $action,
                    'request'     => serialize($args),
                    'api-key'     => md5(get_bloginfo('url')),
                    'license-key' => $license_key,
                    'email'       => $email,
                    'domain'      => $domain,
                    'item-id'     => $item_valid_id,
                ),
                'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
            );

            $request = wp_remote_post($api_url, $request_string);
            if (is_wp_error($request)) {
                $res = new WP_Error('plugins_api_failed',
                    __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'),
                    $request->get_error_message());
            } else {
                $res = unserialize($request['body']);

                if ($res === false) {
                    $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
                }
            }

            return $res;
        }

        function variation_settings_fields($loop, $variation_data, $variation)
        {
            // NUMBER Field
            woocommerce_wp_text_input(
                array(
                    'id'                => 'pw_cost_of_good[' . $variation->ID . ']',
                    'label'             => __('Cost og Good($)', pw_report_wcreport_textdomain),
                    'desc_tip'          => 'true',
                    'description'       => __('Enter Cost of Good for this product', pw_report_wcreport_textdomain),
                    'value'             => get_post_meta($variation->ID, 'pw_cost_of_good', true),
                    'custom_attributes' => array(
                        'step' => 'any',
                        'min'  => '0'
                    )
                )
            );

        }

        /**
         * Save new fields for variations
         *
         */
        function save_variation_settings_fields($post_id)
        {

            // Number Field
            $number_field = $_POST['pw_cost_of_good'][$post_id];
            if ( ! empty($number_field)) {
                update_post_meta($post_id, 'pw_cost_of_good', esc_attr($number_field));
            }
        }


        function woo_add_donation()
        {
            global $woocommerce;
            global $current_user;
            $current_user = wp_get_current_user();

            $user_info = get_userdata($current_user->ID);

            $role = get_role(strtolower($user_info->roles[0]));

            $role = ($role->name);

            //die(print_r($_REQUEST));

            $cost_of_good = isset($_REQUEST['cost_of_good']) ? $_REQUEST['cost_of_good'] : '';

            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $post_id = $cart_item['data']->id;

                $cost_of_good = get_post_meta($post_id, '_cost_of_good', true);

                $additional_price = '';

                if ($main_price == 'cash_role') {
                    $additional_price = $cash_price;
                }

                if ($additional_price != '') {
                    $cart_item['data']->set_price($additional_price);
                }
            }

            return true;
        }

        function pw_add_custom_price_box()
        {
            woocommerce_wp_text_input(array(
                'id'    => 'pw_cost_of_good',
                'class' => 'wc_input_price short',
                'label' => __('Cost of Good($)', 'woocommerce')
            ));

        }

        function pw_custom_woocommerce_process_product_meta($post_id)
        {
            update_post_meta($post_id, 'pw_cost_of_good', stripslashes($_POST['pw_cost_of_good']));
        }

        function add_custom_price_front($p, $obj)
        {
            global $current_user, $product;
            $post_id          = $obj->post->ID;
            $additional_price = '';


            $current_user = wp_get_current_user();

            $user_info = get_userdata($current_user->ID);

            $role = get_role(strtolower($user_info->roles[0]));

            $role = ($role->name);
            //$role = get_role( strtolower('Administrator'));
            //	echo $role;

            $credit_price    = get_post_meta($post_id, 'pro_credit_price_extra', true);
            $wholesale_price = get_post_meta($post_id, 'pro_wholesale_price_extra', true);

            $credit_prices    = wc_price(floatval($credit_price));
            $wholesale_prices = wc_price(floatval($wholesale_price));

            if (is_admin()) {
                //show in new line
                $tag = 'div';
            } else {
                $tag = 'span';
            }

            if (is_product()) {


                if ( ! empty($credit_price) && ($role == 'credit_role' || $role == 'cash_role' || $role == 'administrator')) {
                    $additional_price .= "$credit_prices";
                }

                if ( ! empty($wholesale_price) && ($role == 'wholesale_role' || $role == 'administrator')) {
                    $additional_price .= "$wholesale_prices";
                }

                /*if ( !empty( $additional_price ) ) {
					return  $additional_price;
				}
				else {
					return  $p ;
				}*/

                $total_price = get_post_meta($post_id, '_price', true);

                $html = "<input value='cash_role' class='pw_prices' type='radio' name='role_price' /><label>$p</label><br />
				<input value='credit_role' class='pw_prices' type='radio' name='role_price' /><label>$credit_prices</label><br />
				<input value='wholesale_role' class='pw_prices' type='radio' name='role_price' /><label>$wholesale_prices</label><br />
				
				<script>
					jQuery(document).ready(function(){
						
						jQuery('.pw_prices').click(function(){
							price=(jQuery(this).val());
							jQuery('.pw_main_price_input').remove();
							jQuery('.cart').append('<input class=\'pw_main_price_input\' name=\'main_price\' value=\''+price+'\' />');
						});
						
					});
				</script>
				
				";

                return $html;

            }

            return $p;
        }


        function array_insert(&$array, $insert, $position)
        {
            settype($array, "array");
            settype($insert, "array");
            settype($position, "int");

            //if pos is start, just merge them
            if ($position == 0) {
                $array = array_merge($insert, $array);
            } else {

                //if pos is end just merge them
                if ($position >= (count($array) - 1)) {
                    $array = array_merge($array, $insert);
                } else {
                    //split into head and tail, then merge head+inserted bit+tail
                    $head  = array_slice($array, 0, $position);
                    $tail  = array_slice($array, $position);
                    $array = array_merge($head, $insert, $tail);
                }
            }
        }

        function menu_fields($index = '')
        {
            $menu_fields = array(
                'all_orders'            => array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),

                ////ADDED IN VER4.0
                /// ORDER PER COUNTRY
                "details_order_country" => array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),

                'product'  => array(
                    'fields' => array(
                        "pw_category_id"   => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_tags_id"       => __('Tags', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"      => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_product_id"    => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),
                'category' => array(
                    'fields' => array(
                        "pw_parent_category_id" => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),

                'tags' => array(
                    'fields' => array(
                        "pw_tags_id" => __('Tags', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),


                'stock_list'  => array(
                    'fields' => array(
                        "pw_category_id" => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"    => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_product_id"  => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),
                /*'variation_stock' => array(
					'fields' => array(
						"pw_category_id" => __('Category',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
						"pw_product_id" => __('Product',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
					),
					'cols' => array(

					),
				),*/
                'tax_reports' => array(
                    'fields' => array(
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),


                'order_product_analysis' => array(
                    'fields' => array(
                        "pw_product_id" => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),

                'order_variation_analysis' => array(
                    'fields' => array(
                        "pw_products" => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                ),


            );

            if (defined("__PW_VARIATION_ADD_ON__")) {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"   => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"      => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),

                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "variation", $new_menu);
            }

            if (__PW_COG__ != '') {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "profit", $new_menu);
            }

            if (defined("__PW_CROSSTABB_ADD_ON__")) {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"   => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"      => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_product_id"    => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "prod_per_month", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_categories"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"      => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_products"      => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "variation_per_month", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "prod_per_country", $new_menu);
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "prod_per_state", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "country_per_month", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "payment_per_month", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_orders_status" => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "order_status_per_month",
                    $new_menu);
            }

            if (defined("__PW_TAX_FIELD_ADD_ON__")) {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "details_tax_field", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_parent_brand_id" => __('Brand', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "brand_tax_field", $new_menu);

                $new_menu    = array(
                    'fields' => array(
                        "pw_customy_taxonomies" => __('Product Taxonimies', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "custom_tax_field", $new_menu);
            }

            if (defined("__PW_BRANDS_ADD_ON__")) {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "details_brands", $new_menu);
            }

            if (defined("__PW_PO_ADD_ON__")) {
                $new_menu    = array(
                    'fields' => array(
                        "pw_category_id"    => __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_brand_id"       => __PW_BRAND_SLUG__ ? __PW_BRAND_LABEL__ : false,
                        "pw_product_id"     => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_countries_code" => __('Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_states_code"    => __('State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                        "pw_orders_status"  => __('Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    ),
                    'cols'   => array(),
                );
                $menu_fields = $this->array_insert_after("all_orders", $menu_fields, "details_product_options",
                    $new_menu);
            }

            ///////////////////////////////////////
            ////GENERATE CUSTOM TAXONOMY FIELDS////
            $visible_custom_taxonomy = array();
            $post_name               = 'product';
            //$all_tax=get_object_taxonomies( $post_name );
            $all_tax = $this->fetch_product_taxonomies($post_name);

            $current_value = array();
            if (is_array($all_tax) && count($all_tax) > 0) {
                //FETCH TAXONOMY
                foreach ($all_tax as $tax) {
                    $tax_status = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'set_default_search_' . $index . '_' . $tax);

                    if ($tax_status == 'on') {
                        $visible_custom_taxonomy[] = $tax;
                    }
                }
            }

            $custom_taxonomy_fields = '';

            if (defined("__PW_TAX_FIELD_ADD_ON__") && is_array($visible_custom_taxonomy) && count($visible_custom_taxonomy) > 0) {

                //FETCH TAXONOMY
                foreach ($visible_custom_taxonomy as $tax) {
                    $taxonomy  = get_taxonomy($tax);
                    $values    = $tax;
                    $label     = $taxonomy->label;
                    $translate = get_option($index . '_' . $tax . "_translate");
                    if ($translate != '') {
                        $label = $translate;
                    }
                    $menu_fields['details_tax_field']['fields'][$tax] = $label;

                    $menu_fields['product']['fields'][$tax]          = $label;
                    $menu_fields['prod_per_month']['fields'][$tax]   = $label;
                    $menu_fields['prod_per_country']['fields'][$tax] = $label;
                    $menu_fields['prod_per_state']['fields'][$tax]   = $label;
                    $menu_fields['stock_list']['fields'][$tax]       = $label;
                }
            }

            //////////////////////////////////////

            return $menu_fields;
        }

        function pw_report_backend_enqueue()
        {

            $array_gift = ['adv_gift', 'wrap_gift', 'license'];

            if ((isset($_GET['parent']) && ! in_array($_GET['parent'],
                        $array_gift)) || (isset($_GET['page']) && $_GET['page'] == 'wcx_wcreport_plugin_mani_settings') || (isset($_GET['page']) && $_GET['page'] == 'permission_report')) {
                include("includes/admin-embed.php");
            }

//		    if(isset($_GET['parent']) || (isset($_GET['page']) && $_GET['page']=='wcx_wcreport_plugin_mani_settings')  || (isset($_GET['page']) && $_GET['page']=='permission_report'))
//			{
//				include ("includes/admin-embed.php");
//			}
        }

        function loadTextDomain()
        {
            load_plugin_textdomain('pw_report_wcreport_textdomain', false,
                dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        function fetch_product_taxonomies($post_name)
        {
            $all_tax    = get_object_taxonomies($post_name);
            $taxonomies = array();
            if (is_array($all_tax) && count($all_tax) > 0) {
                //FETCH TAXONOMY
                $i = 1;
                foreach ($all_tax as $tax) {
                    if ($tax == 'product_cat') {
                        continue;
                    }
                    $taxonomies[] = $tax;
                }
            }

            return $taxonomies;
        }

        function make_custom_taxonomy($args)
        {
            $key                     = $args['page'];
            $visible_custom_taxonomy = array();
            $post_name               = 'product';
            $all_tax                 = $this->fetch_product_taxonomies($post_name);
            $current_value           = array();
            if (is_array($all_tax) && count($all_tax) > 0) {
                //FETCH TAXONOMY
                foreach ($all_tax as $tax) {
                    $tax_status = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'set_default_search_' . $key . '_' . $tax);
                    /*$tax_value=get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__.'set_default_search_'.$key.'_'.$tax);
					pw_elm_value_all_orders_pw_category_id[]*/
                    if ($tax_status == 'on') {
                        $visible_custom_taxonomy[] = $tax;
                    }
                }
            }

            $option_data           = '';
            $param_line            = '';
            $show_custom_tax_block = false;

            $current_value = array();
            if (defined("__PW_TAX_FIELD_ADD_ON__") && is_array($visible_custom_taxonomy) && count($visible_custom_taxonomy) > 0) {

                $post_type_label = get_post_type_object($post_name);
                $label           = $post_type_label->label;

                //FETCH TAXONOMY
                foreach ($visible_custom_taxonomy as $tax) {
                    $taxonomy  = get_taxonomy($tax);
                    $values    = $tax;
                    $label     = $taxonomy->label;
                    $translate = get_option($key . '_' . $tax . "_translate");
                    if ($translate != '') {
                        $label = $translate;
                    }

                    $attribute_taxonomies = wc_get_attribute_taxonomies();

                    ////////////////////////////////////
                    //PERMISSION CHECK
                    $col_style        = '';
                    $permission_value = $this->get_form_element_value_permission($tax);
                    if ( ! $this->get_form_element_permission($tax) && $permission_value == '') {
                        continue;
                    }

                    $permission_value = $this->get_form_element_value_permission($tax);
                    //////////////////////////////////////

                    if ( ! $this->get_form_element_permission($tax) && $permission_value != '') {
                        $col_style = 'display:none';
                    } else {
                        $show_custom_tax_block = true;
                    }

                    $param_line .= ' 
					
					<div class="col-md-6" style="' . $col_style . '">
						<div class="awr-form-title">' . $label . '</div>
						<span class="awr-form-icon"><i class="fa fa-tags"></i></span>
							<div class="full-lbl-cnt more-padding">';

                    $param_line_exclude = $param_line_include = '<select name="pw_custom_taxonomy_in_' . $tax . '[]" class="chosen-select-search" multiple="multiple" style="width: 531px;" data-placeholder="' . __('Choose Inclulde ',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__) . ' ' . $label . ' ..." id="pw_' . $tax . '">';

                    if ($this->get_form_element_permission($tax) && (( ! is_array($permission_value)) || (is_array($permission_value) && in_array('all',
                                    $permission_value)))) {
                        $param_line_include .= '<option value="-1">' . __('Select All',
                                __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</option>';
                    }

                    $param_line_exclude = '<select name="pw_custom_taxonomy_ex_' . $tax . '[]" class="chosen-select-search" multiple="multiple" style="width: 531px;" data-placeholder="' . __('Choose Exclude',
                            __PW_REPORT_WCREPORT_TEXTDOMAIN__) . ' ' . $label . ' ..." id="pw_' . $tax . '">';
                    $args               = array(
                        'orderby'      => 'name',
                        'order'        => 'ASC',
                        'hide_empty'   => 0,
                        'hierarchical' => 1,
                        'exclude'      => '',
                        'include'      => '',
                        'child_of'     => 0,
                        'number'       => '',
                        'pad_counts'   => false
                    );

                    $categories = get_terms($tax, $args);
                    foreach ($categories as $category) {
                        $selected = '';

                        //CHECK IF IS IN PERMISSION
                        if (is_array($permission_value) && ! in_array($category->term_id, $permission_value)) {
                            continue;
                        }

                        if ( ! $this->get_form_element_permission($tax) && $permission_value != '') {
                            $selected = "selected";
                        }

                        $option             = '<option value="' . $category->term_id . '" ' . $selected . '>';
                        $option             .= $category->name;
                        $option             .= ' (' . $category->count . ')';
                        $option             .= '</option>';
                        $param_line_include .= $option;

                    }
                    $param_line_include .= '</select>';

                    $categories = get_terms($tax, $args);
                    foreach ($categories as $category) {

                        $option             = '<option value="' . $category->term_id . '" ' . $selected . '>';
                        $option             .= $category->name;
                        $option             .= ' (' . $category->count . ')';
                        $option             .= '</option>';
                        $param_line_exclude .= $option;
                    }
                    $param_line_exclude .= '</select>';
                    $param_line_exclude = '';
                    $param_line         .= $param_line_include . $param_line_exclude . '
					</div></div> ';
                }
            }

            if ($show_custom_tax_block) {
                $param_line = '
					<div class="col-md-6" style="border:#f2c811 2px solid;width:100%">
						<div class="awr-form-title" style="padding: 7px 5px 10px;text-align: center;background: #f2c811;color: #fff;">
							' . __('Custom Taxonomy', __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '
						</div>' . $param_line . '</div>';
            }

            return $param_line;
        }


        ////ADDED IN VER4.6
        /// GET META DATA OF PRODUCT
        public function pw_get_category_tag($id = 0)
        {
            $term_links       = array();
            $enable_metadata  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'show_category', '');
            $metadata_cat_tax = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cat_tax', '');

            if ($enable_metadata) {
                foreach ($metadata_cat_tax as $cat_tax => $val) {

                    if ($val == 'on') {
                        $terms = get_the_terms($id, $cat_tax);

                        if (is_wp_error($terms)) {
                            return '<div class="pw_intelligence_product_category">
		                            -
		                        </div>';
                        }

                        if (empty($terms)) {
                            return '<div class="pw_intelligence_product_category">
		                            -
		                        </div>';
                        }

                        $counter = 0;
                        foreach ($terms as $term) {
                            $link = get_term_link($term, $cat_tax);
                            if (is_wp_error($link)) {
                                return $link;
                            }
                            $term_links[] = '<span><a href="' . $link . '" rel="tag">' . $term->name . '</a></span>';
                            break;
                        }
                    }
                }
                if (count($term_links) > 0) {
                    return '<div class="pw_intelligence_product_category">
                            ' . implode($term_links, "") . '
                        </div>';
                } else {
                    return '<div class="pw_intelligence_product_category">
                            -
                        </div>';
                }
            } else {
                return true;
            }
        }


        ////ADDED IN VER4.0
        /// PRODUCT OPTIONS CUSTOM FIELDS
        function pw_po_fields_gridheader()
        {
            $po_array_fields = array();
            $po_fields       = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_custom_fields');
            foreach ($po_fields as $fields) {
                foreach ($fields as $po_field) {
                    $input_name  = str_replace(" ", "_", $po_field);
                    $title       = get_option($input_name . '_translate', $po_field);
                    $col_visible = get_option($input_name . '_column', 'off');
                    if ($col_visible == 'on') {
                        $po_array_fields[] = array('lable' => $title, 'status' => 'show');
                    }
                }
            }

            return $po_array_fields;
        }

        function pw_po_checkout_fields_gridheader()
        {
            $po_array_fields = array();
            $po_fields       = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_checkout_custom_fields');
            foreach ($po_fields as $fields) {
                foreach ($fields as $po_field) {

                    //echo $po_field;
                    $exp         = explode('@', $po_field);
                    $po_field    = $exp[0];
                    $title       = $exp[1];
                    $input_name  = str_replace(" ", "_", $po_field);
                    $title       = get_option($input_name . '_translate', $title);
                    $col_visible = get_option($input_name . '_column', 'off');
                    if ($col_visible == 'on') {
                        $po_array_fields[] = array('lable' => $title, 'status' => 'show');
                    }
                }
            }

            return $po_array_fields;
        }

        /*
	     * Compare Saerch Fields & saved data as array
	    */
        function pw_po_fields_apply_search($results)
        {
            $po_flag = array();
            foreach ($results as $items) {
                $order_id      = $items->order_id;
                $order_item_id = $items->order_item_id;

                $po_fields    = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_custom_fields');
                $fields_array = $this->pw_po_fields_fetch_field($order_item_id);

                //echo $order_item_id;
                //print_r($po_fields);

                $po_flag[$order_id] = true;
                foreach ($po_fields as $fields) {
                    foreach ($fields as $po_field) {

                        $input_name     = str_replace(" ", "_", $po_field);
                        $pw_input_value = $this->pw_get_woo_requests($input_name, "", true);

                        if ($pw_input_value != '') {
                            //echo $input_name.'='.$pw_input_value.'#'.implode(",",$fields_array[strtolower($po_field)]['value']).'@';

                            $pw_input_value = explode(",", $pw_input_value);
//print_r($fields_array[strtolower($po_field)]['value']);
                            //if(implode(",",$fields_array[strtolower($po_field)]['value'])!=$pw_input_value)
                            if ((isset($fields_array[strtolower($po_field)]['value']) && count(array_intersect($fields_array[strtolower($po_field)]['value'],
                                        $pw_input_value)) != count($pw_input_value)) || $fields_array[strtolower($po_field)]['value'] == '') {
                                $po_flag[$order_id] = false;
                            }
                        }
                    }
                }
            }

            return $po_flag;
        }

        function pw_po_fields_fetch_field($order_item_id, $field = '')
        {
            global $wpdb;
            $field = str_replace("_", " ", $field);
            //echo $field.'   --   ';
            $types = $wpdb->get_results("SELECT pw_itemmeta.meta_value as meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta as pw_itemmeta where pw_itemmeta.meta_key='_tmcartepo_data' and pw_itemmeta.order_item_id='" . $order_item_id . "'",
                ARRAY_A);
            if (version_compare(THEMECOMPLETE_EPO_VERSION, "4.6", ">=") && $types == null) {
                //echo 'BALATAR'.THEMECOMPLETE_EPO_VERSION;
                $types = $wpdb->get_results("SELECT pw_itemmeta.meta_value as meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta as pw_itemmeta where pw_itemmeta.meta_key LIKE '%$field%' and pw_itemmeta.order_item_id='" . $order_item_id . "'",
                    ARRAY_A);
                //echo "SELECT pw_itemmeta.meta_value as meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta as pw_itemmeta where pw_itemmeta.meta_key LIKE '%$field%' and pw_itemmeta.order_item_id='".$order_item_id."'";
                if ($types != null) {
                    foreach ($types as $v) {
                        return $v['meta_value'];
                    }
                    //print_r($types);
                }

                return '';
            }
            //echo 'BALATAR'.THEMECOMPLETE_EPO_VERSION;


            $po_fields   = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_custom_fields');
            $fields_type = array(
                'textarea',
                'textfield',
                'selectbox',
                'radiobuttons',
                'checkboxes',
                'upload',
                'date',
                'time',
                'range',
                'color'
            );
            $types       = $wpdb->get_results("SELECT pw_itemmeta.meta_value as meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta as pw_itemmeta where pw_itemmeta.meta_key='_tmcartepo_data' and pw_itemmeta.order_item_id='" . $order_item_id . "'",
                ARRAY_A);

            $fields_array = array();
            if ($types != null) {
                foreach ($types as $v) {

                    $data = unserialize($v['meta_value']);
                    $j    = 0;
                    foreach ($data as $fields) {

                        //if(!isset($fields['element']['type'])) continue;

                        $index = $fields['name'];
                        if ( ! isset($fields_array[strtolower($index)])) {
                            $j                                            = 0;
                            $fields_array[strtolower($index)]['value'][0] = $fields['value'];
                            if (isset($fields['is_taxonomy'])) {
                                $fields_array[strtolower($index)]['type'][0] = 'is_taxonomy';
                            } else {
                                $fields_array[strtolower($index)]['type'][0] = $fields['element']['type'];
                            }
                        } else {
                            $j++;
                            $fields_array[strtolower($index)]['value'][$j] = $fields['value'];
                            if (isset($fields['is_taxonomy'])) {
                                $fields_array[strtolower($index)]['type'][$j] = 'is_taxonomy';
                            } else {
                                $fields_array[strtolower($index)]['type'][$j] = $fields['element']['type'];
                            }
                        }
                    }
                }
            }

            return $fields_array;
        }

        function pw_po_checkout_fields_search_fields()
        {
            $po_fields = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_checkout_custom_fields');
            //print_r($po_fields);
            $fields_type = array(
                'textarea',
                'textfield',
                'selectbox',
                'radiobuttons',
                'checkboxes',
                'upload',
                'date',
                'time',
                'range',
                'color'
            );
            $html        = '';

            foreach ($po_fields as $field_type => $value) {
                if ($field_type == 'po_checkout_global_fields_select') {
                    global $wpdb;

                    $types = $wpdb->get_results("SELECT pw_post.post_title,pw_postmeta.meta_value as meta_value FROM {$wpdb->prefix}posts as pw_post 
                                    INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                    where pw_post.post_type='tm_eco_cp' AND pw_post.post_status IN ('publish') 
                                    AND pw_postmeta.meta_key='tm_meta'", ARRAY_A);


                    $val_arr = array();
                    foreach ($value as $val) {
                        $exp       = explode('@', $val);
                        $val_arr[] = $exp[0];
                    }

                    $fields_array = $val_arr;
                    //$fields_array = $value;
                    if ($types != null) {
                        foreach ($types as $v) {
                            if ( ! $v['meta_value']) {
                                continue;
                            }

                            $parent_id = $v['post_title'];
                            $data      = unserialize($v['meta_value']);
                            //print_r($data);
                            foreach ($fields_type as $f_type) {

                                if (isset($data['tmfbuilder'][$f_type . '_header_title'])) {

                                    //print_r($data['tmfbuilder']);
                                    $element_id = $data['tmfbuilder'][$f_type . '_uniqid'][0];
                                    //echo $element_id;

                                    $i = 0;
                                    foreach ($data['tmfbuilder'][$f_type . '_uniqid'] as $fields) {

                                        $fields = str_replace(".", "_", $fields);

                                        if (in_array($fields, $fields_array)) {

                                            $input_name  = str_replace(" ", "_", $fields);
                                            $title       = get_option($input_name . '_translate',
                                                $data['tmfbuilder'][$f_type . '_header_title'][$i]);
                                            $show_filter = get_option($input_name . '_filter');

                                            $input_name_txt = $fields;
                                            if ($show_filter != 'on') {
                                                continue;
                                            }

                                            switch ($f_type) {

                                                case 'selectbox':
                                                    $select_values = $data['tmfbuilder']['multiple_selectbox_options_value'][0];
                                                    $select_titles = $data['tmfbuilder']['multiple_selectbox_options_title'][0];
                                                    $select_option = '<option value="">Choose One</option>';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {
                                                        $select_option .= '<option value="' . $option . '" >' . $select_titles[$j] . '</option>';
                                                        $j++;
                                                    }

                                                    $html .= '<div class="col-md-6">
								                        <div class="awr-form-title">
								                            ' . $title . '
								                        </div>
								                        <span class="awr-form-icon"><i class="fa fa-check"></i></span>';
                                                    $html .= '<select  name="' . $input_name_txt . '" >' . $select_option . '</select></select></div>';
                                                    break;

                                                case 'time':
                                                    $time_format = $data['tmfbuilder'][$f_type . '_time_format'][$i];
                                                    $html        .= '
													<div class="col-md-6">
									                    <div class="awr-form-title">
									                        ' . $title . '
									                    </div>
									                    <span class="awr-form-icon"><i class="fa fa-clock-o"></i></span>
									                    ';
                                                    $html        .= '
												        <div class="input-group date" id="' . $input_name . '">
												            <input type="text"  name="' . $input_name_txt . '" class="form-control"> 
												            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												        </div>

													    <script>
													        jQuery(document).ready( function($) {
													            $("#' . $input_name . '").datetimepicker({
																	format: "' . $time_format . '"
																}); 
													        });
													    </script>	
											        </div>';

                                                    break;

                                                case 'range':
                                                    echo 'sds';
                                                    $range_min  = $data['tmfbuilder'][$f_type . '_min'][$i];
                                                    $range_max  = $data['tmfbuilder'][$f_type . '_max'][$i];
                                                    $range_step = $data['tmfbuilder'][$f_type . '_step'][$i];

                                                    $html .= '<div class="col-md-6">
									                    <div class="awr-form-title">
									                        ' . $title . '
									                    </div><div class="awr-range-slider">
													    <input  name="' . $input_name_txt . '"  class="awr-range-slider__range" type="range" value="' . $range_min . '" min="' . $range_min . '" max="' . $range_max . '" step="' . $range_step . '">
													    <span class="awr-range-slider__value">0</span>
													</div></div>


													<script>
														jQuery(document).ready( function($) {
															var rangeSlider = function(){
																	var slider = $(\'.awr-range-slider\'),
																	range = $(\'.awr-range-slider__range\'),
																	value = $(\'.awr-range-slider__value\');
																	
																	slider.each(function(){
																	
																		value.each(function(){
																			var value = $(this).prev().attr(\'value\');
																			$(this).html(value);
																		});
																
																			range.on(\'input\', function(){
																				$(this).next(value).html(this.value);
																			});
																		});
																	};
															
															rangeSlider();
														    
														});
													
													</script>
';
                                                    /*range_quantity
													range_quantity_min
													range_quantity_max
													range_quantity_step
													range_quantity_default_value
													range_min
													range_max
													range_step
													range_default_value*/
                                                    break;

                                                case 'textfield':
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>
							                        <span class="awr-form-icon"><i class="fa fa-check"></i></span>';
                                                    $html .= '<input type="text" name="' . $input_name_txt . '" /></div>';
                                                    break;

                                                case 'checkboxes':
                                                    $select_values = isset($data['tmfbuilder']['multiple_checkboxes_options_value'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_value'][0] : "";
                                                    $select_titles = isset($data['tmfbuilder']['multiple_checkboxes_options_title'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_title'][0] : "";
                                                    $select_image  = isset($data['tmfbuilder']['multiple_checkboxes_options_imagep'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_imagep'][0] : "";
                                                    $select_option = '';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {
                                                        $img = '';
                                                        if (isset($select_image[$j]) && $select_image[$j] != '') {
                                                            $img = '<img src="' . $select_image[$j] . '" width="30" height="30" />';
                                                        }

                                                        $select_option .= $img . $select_titles[$j] . '<input type="checkbox" name="' . $input_name_txt . '[]" placeholder=""  value="' . $option . '" />';
                                                        $j++;
                                                    }
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>';
                                                    $html .= $select_option . '</div>';
                                                    break;

                                                case 'radiobuttons':
                                                    $select_values = isset($data['tmfbuilder']['multiple_radiobuttons_options_value'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_value'][0] : "";
                                                    $select_titles = isset($data['tmfbuilder']['multiple_radiobuttons_options_title'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_title'][0] : "";
                                                    $select_image  = isset($data['tmfbuilder']['multiple_radiobuttons_options_imagep'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_imagep'][0] : "";

                                                    $select_option = '';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {

                                                        $img = '';
                                                        if (isset($select_image[$j]) && $select_image[$j] != '') {
                                                            $img = '<img src="' . $select_image[$j] . '" width="30" height="30" />';
                                                        }

                                                        $select_option .= $img . $select_titles[$j] . '<input type="radio" name="' . $input_name_txt . '" placeholder="' . $fields . '"  value="' . $option . '" />';
                                                        $j++;
                                                    }
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>';
                                                    $html .= $select_option . '</div>';
                                                    break;

                                                case 'date':
                                                    //date_format
                                                    $date_format = $data['tmfbuilder'][$f_type . '_format'][0];

                                                    switch ($date_format) {
                                                        case "0":
                                                            $date_format = 'dd/mm/yy';
                                                            break;
                                                        case "1":
                                                            $date_format = 'mm/dd/yy';
                                                            break;
                                                        case "2":
                                                            $date_format = 'dd.mm.yy';
                                                            break;
                                                        case "3":
                                                            $date_format = 'mm.dd.yy';
                                                            break;
                                                        case "4":
                                                            $date_format = 'dd-mm-yy';
                                                            break;
                                                        case "5":
                                                            $date_format = 'mm-dd-yy';
                                                            break;
                                                    }

                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>
							                        <span class="awr-form-icon"><i class="fa fa-calendar-o"></i></span>';
                                                    $html .= '<input type="text" name="' . $input_name_txt . '" id="' . $input_name . '" placeholder="" class="datepick"/>
													</div>
                                                    <script>
                                                        jQuery().ready(function($){
                                                            ////ADDED IN VER4.0
                                                            $("#' . $input_name . '").datepicker({ dateFormat: "' . $date_format . '" });
                                                        });
                                                    </script>';
                                                    break;

                                                case 'color':
                                                    $html .= '<div class="col-md-6">
								                        <div class="awr-form-title">
								                            ' . $title . '
								                        </div>
								                        <input type="text" name="' . $input_name_txt . '" placeholder="' . $fields . '" class="wp_ad_picker_color"/>
							                        </div>
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function($) {
                                                            $(".wp_ad_picker_color").wpColorPicker();
                                                        });
                                                    </script>';
                                                    break;
                                            }

                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $html_main = '';
            if ($html != '') {
                $html_main = '<div class="col-md-12">
                        <div class="awr-option-title">' . __('Product Options Checkout Fields',
                        __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</div>
                            <div class="awr-option-fields">';
                $html_main .= $html;

                $html_main .= '
							</div>
						</div>';
            }


            return $html_main;
        }

        function pw_po_fields_search_fields()
        {
            $po_fields   = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'po_custom_fields');
            $fields_type = array(
                'textarea',
                'textfield',
                'selectbox',
                'radiobuttons',
                'checkboxes',
                'upload',
                'date',
                'time',
                'range',
                'color'
            );
            $html        = '';

            foreach ($po_fields as $field_type => $value) {
                if ($field_type == 'po_global_fields_select') {
                    global $wpdb;

                    $types = $wpdb->get_results("SELECT pw_post.post_title,pw_postmeta.meta_value as meta_value FROM {$wpdb->prefix}posts as pw_post 
                                    INNER JOIN {$wpdb->prefix}postmeta as pw_postmeta ON pw_post.ID=pw_postmeta.post_id 
                                    where pw_post.post_type='tm_global_cp' AND pw_post.post_status IN ('publish') 
                                    AND pw_postmeta.meta_key='tm_meta'", ARRAY_A);

                    $fields_array = $value;
                    if ($types != null) {
                        foreach ($types as $v) {
                            if ( ! $v['meta_value']) {
                                continue;
                            }

                            $parent_id = $v['post_title'];
                            $data      = unserialize($v['meta_value']);
                            //print_r($data);
                            foreach ($fields_type as $f_type) {


                                if (isset($data['tmfbuilder'][$f_type . '_header_title'])) {

                                    $i = 0;
                                    foreach ($data['tmfbuilder'][$f_type . '_header_title'] as $fields) {
                                        if (in_array($fields, $fields_array)) {

                                            $input_name  = str_replace(" ", "_", $fields);
                                            $title       = get_option($input_name . '_translate',
                                                $data['tmfbuilder'][$f_type . '_header_title'][$i]);
                                            $show_filter = get_option($input_name . '_filter');
                                            if ($show_filter != 'on') {
                                                continue;
                                            }

                                            switch ($f_type) {

                                                case 'selectbox':
                                                    $select_values = $data['tmfbuilder']['multiple_selectbox_options_value'][0];
                                                    $select_titles = $data['tmfbuilder']['multiple_selectbox_options_title'][0];
                                                    $select_option = '<option value="">Choose One</option>';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {
                                                        $select_option .= '<option value="' . $option . '" >' . $select_titles[$j] . '</option>';
                                                        $j++;
                                                    }

                                                    $html .= '<div class="col-md-6">
								                        <div class="awr-form-title">
								                            ' . $title . '
								                        </div>
								                        <span class="awr-form-icon"><i class="fa fa-check"></i></span>';
                                                    $html .= '<select  name="' . $input_name . '" >' . $select_option . '</select></select></div>';
                                                    break;

                                                case 'time':
                                                    $time_format = $data['tmfbuilder'][$f_type . '_time_format'][$i];
                                                    $html        .= '
													<div class="col-md-6">
									                    <div class="awr-form-title">
									                        ' . $title . '
									                    </div>
									                    <span class="awr-form-icon"><i class="fa fa-clock-o"></i></span>
									                    ';
                                                    $html        .= '
												        <div class="input-group date" id="' . $input_name . '">
												            <input type="text"  name="' . $input_name . '" class="form-control"> 
												            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												        </div>

													    <script>
													        jQuery(document).ready( function($) {
													            $("#' . $input_name . '").datetimepicker({
																	format: "' . $time_format . '"
																}); 
													        });
													    </script>	
											        </div>';

                                                    break;

                                                case 'range':
                                                    $range_min  = $data['tmfbuilder'][$f_type . '_min'][$i];
                                                    $range_max  = $data['tmfbuilder'][$f_type . '_max'][$i];
                                                    $range_step = $data['tmfbuilder'][$f_type . '_step'][$i];

                                                    $html .= '<div class="col-md-6">
									                    <div class="awr-form-title">
									                        ' . $title . '
									                    </div><div class="awr-range-slider">
													    <input  name="' . $input_name . '"  class="awr-range-slider__range" type="range" value="' . $range_min . '" min="' . $range_min . '" max="' . $range_max . '" step="' . $range_step . '">
													    <span class="awr-range-slider__value">0</span>
													</div></div>


													<script>
														jQuery(document).ready( function($) {
															var rangeSlider = function(){
																	var slider = $(\'.awr-range-slider\'),
																	range = $(\'.awr-range-slider__range\'),
																	value = $(\'.awr-range-slider__value\');
																	
																	slider.each(function(){
																	
																		value.each(function(){
																			var value = $(this).prev().attr(\'value\');
																			$(this).html(value);
																		});
																
																			range.on(\'input\', function(){
																				$(this).next(value).html(this.value);
																			});
																		});
																	};
															
															rangeSlider();
														    
														});
													
													</script>
';
                                                    /*range_quantity
													range_quantity_min
													range_quantity_max
													range_quantity_step
													range_quantity_default_value
													range_min
													range_max
													range_step
													range_default_value*/
                                                    break;

                                                case 'textfield':
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>
							                        <span class="awr-form-icon"><i class="fa fa-check"></i></span>';
                                                    $html .= '<input type="text" name="' . $input_name . '" placeholder="' . $fields . '" /></div>';
                                                    break;

                                                case 'checkboxes':
                                                    $select_values = isset($data['tmfbuilder']['multiple_checkboxes_options_value'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_value'][0] : "";
                                                    $select_titles = isset($data['tmfbuilder']['multiple_checkboxes_options_title'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_title'][0] : "";
                                                    $select_image  = isset($data['tmfbuilder']['multiple_checkboxes_options_imagep'][0]) ? $data['tmfbuilder']['multiple_checkboxes_options_imagep'][0] : "";
                                                    $select_option = '';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {
                                                        $img = '';
                                                        if (isset($select_image[$j]) && $select_image[$j] != '') {
                                                            $img = '<img src="' . $select_image[$j] . '" width="30" height="30" />';
                                                        }

                                                        $select_option .= $img . $select_titles[$j] . '<input type="checkbox" name="' . $input_name . '[]" placeholder="' . $fields . '"  value="' . $option . '" />';
                                                        $j++;
                                                    }
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>';
                                                    $html .= $select_option . '</div>';
                                                    break;

                                                case 'radiobuttons':
                                                    $select_values = isset($data['tmfbuilder']['multiple_radiobuttons_options_value'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_value'][0] : "";
                                                    $select_titles = isset($data['tmfbuilder']['multiple_radiobuttons_options_title'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_title'][0] : "";
                                                    $select_image  = isset($data['tmfbuilder']['multiple_radiobuttons_options_imagep'][0]) ? $data['tmfbuilder']['multiple_radiobuttons_options_imagep'][0] : "";

                                                    $select_option = '';
                                                    $j             = 0;
                                                    foreach ($select_values as $option) {

                                                        $img = '';
                                                        if (isset($select_image[$j]) && $select_image[$j] != '') {
                                                            $img = '<img src="' . $select_image[$j] . '" width="30" height="30" />';
                                                        }

                                                        $select_option .= $img . $select_titles[$j] . '<input type="radio" name="' . $input_name . '" placeholder="' . $fields . '"  value="' . $option . '" />';
                                                        $j++;
                                                    }
                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>';
                                                    $html .= $select_option . '</div>';
                                                    break;

                                                case 'date':
                                                    //date_format
                                                    $date_format = $data['tmfbuilder'][$f_type . '_format'][0];

                                                    switch ($date_format) {
                                                        case "0":
                                                            $date_format = 'dd/mm/yy';
                                                            break;
                                                        case "1":
                                                            $date_format = 'mm/dd/yy';
                                                            break;
                                                        case "2":
                                                            $date_format = 'dd.mm.yy';
                                                            break;
                                                        case "3":
                                                            $date_format = 'mm.dd.yy';
                                                            break;
                                                        case "4":
                                                            $date_format = 'dd-mm-yy';
                                                            break;
                                                        case "5":
                                                            $date_format = 'mm-dd-yy';
                                                            break;
                                                    }

                                                    $html .= '<div class="col-md-6">
							                        <div class="awr-form-title">
							                            ' . $title . '
							                        </div>
							                        <span class="awr-form-icon"><i class="fa fa-calendar-o"></i></span>';
                                                    $html .= '<input type="text" name="' . $input_name . '" id="' . $input_name . '" placeholder="' . $fields . '" class="datepick"/>
													</div>
                                                    <script>
                                                        jQuery().ready(function($){
                                                            ////ADDED IN VER4.0
                                                            $("#' . $input_name . '").datepicker({ dateFormat: "' . $date_format . '" });
                                                        });
                                                    </script>';
                                                    break;

                                                case 'color':
                                                    $html .= '<div class="col-md-6">
								                        <div class="awr-form-title">
								                            ' . $title . '
								                        </div>
								                        <input type="text" name="' . $input_name . '" placeholder="' . $fields . '" class="wp_ad_picker_color"/>
							                        </div>
                                                    <script type="text/javascript">
                                                        jQuery(document).ready(function($) {
                                                            $(".wp_ad_picker_color").wpColorPicker();
                                                        });
                                                    </script>';
                                                    break;
                                            }

                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $html_main = '';
            if ($html != '') {
                $html_main = '<div class="col-md-12">
                        <div class="awr-option-title">' . __('Product Options Fields',
                        __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</div>
                            <div class="awr-option-fields">';
                $html_main .= $html;

                $html_main .= '
							</div>
						</div>';
            }


            return $html_main;
        }

        function pw_po_fetch_checkout_fields($order_id, $field)
        {
            global $wpdb;
            $sql = "SELECT pmeta.meta_value as fvalue from {$wpdb->prefix}woocommerce_order_items as pitems left join {$wpdb->prefix}woocommerce_order_itemmeta as pmeta on pitems.order_item_id=pmeta.order_item_id
 left join {$wpdb->prefix}woocommerce_order_itemmeta as pmeta_p on pitems.order_item_id=pmeta_p.order_item_id
where pitems.order_id='$order_id' AND pmeta.meta_key='_wc_eco_fields_value' AND pmeta_p.meta_key='_wc_eco_element_id' AND pmeta_p.meta_value='$field'";

            //echo $sql;
            $order_items = $this->get_results($sql);
            $table_data  = '';
            //print_r($order_items);
            if (isset($order_items[0])) {
                $table_data = $order_items[0]->fvalue;
                $table_data = unserialize($table_data);
                if (is_array($table_data)) {
                    $table_data = implode($table_data, ",");
                } else {
                    $table_data = $order_items[0]->fvalue;
                }
            }

            //print_r($table_data);
            return $table_data;
        }

        ////////////////END PRODUCT OPTIONS CUSTOM FIELDS//////////

        //CUSTOM WORK - 12300
        ///////////////TICKERA CUSTOM FIELDS////////////////
        function pw_tickera_fetch_fields($order_id, $field)
        {
            global $wpdb;
            $sql = "SELECT tmeta.meta_value as fvalue FROM {$wpdb->prefix}posts as tpost LEFT JOIN {$wpdb->prefix}postmeta as tmeta ON tpost.ID=tmeta.post_id Where tpost.post_parent='$order_id' AND tpost.post_type='tc_tickets_instances' AND tmeta.meta_key='$field'";

            //echo $sql;
            $order_items = $this->get_results($sql);
            $table_data  = '-';
            //print_r($order_items);
            if (isset($order_items[0])) {
                $table_data = $order_items[0]->fvalue;
                $table_data = unserialize($table_data);
                if (is_array($table_data)) {
                    $table_data = implode($table_data, ",");
                } else {
                    $table_data = $order_items[0]->fvalue;
                }
            }

            //print_r($table_data);
            return $table_data;
        }

        ////////////////////////////////////////////////

        function pw_standalone_report()
        {

            if (defined("__PW_PERMISSION_ADD_ON__")) {
                global $current_user;
                $current_user = wp_get_current_user();

                $user_info = get_userdata($current_user->ID);

                $role = get_role(strtolower($user_info->roles[0]));

                //$role->has_cap( 'pw_report_appear' );

                if (isset($role->capabilities['pw_report_appear']) && $role->capabilities['pw_report_appear']) {
                    $role_capability = 'pw_report_appear';
                }

                if (strtolower($user_info->roles[0]) == 'woo_report_role') {


                    add_action('admin_head', array($this, 'custom_menu_page_removing'));
                    //echo $_SERVER["PHP_SELF"].' - '.strpos('admin-ajax.php',$_SERVER["PHP_SELF"]);
                    //echo strpos($_SERVER["PHP_SELF"],'admin-ajax.php')=== true;
                    if ( ! isset($_GET['parent']) && strpos($_SERVER["PHP_SELF"], 'admin-ajax.php') === false) {
                        die ('
								<div class="wrap">
									<div class="row">
										<div class="col-xs-12">
											<div class="awr-box awr-acc-box">
												<div class="awr-acc-icon">
												    <i class="fa fa-meh-o"></i>
												</div>
												<h3 class="awr-acc-title">' . __("Access Denied !",
                                __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</h3>
												<div class="awr-acc-desc">' . __("You have no permisson !! Please Contact site Administrator",
                                __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</div>
											</div>
										</div><!--col-xs-12 -->
									</div><!--row -->
								</div><!--wrap -->');
                    }
                }
            }
        }

        function my_login_redirect_woo($redirect, $user)
        {

            if ($user && is_object($user) && is_a($user, 'WP_User')) {
                if ($user->has_cap('woo_report_role')) {
                    $url      = $this->pw_plugin_main_url;
                    $redirect = admin_url("admin.php?page=$url");
                }
            }

            return $redirect;

        }

        function my_login_redirect($redirect_to, $request, $user)
        {

            if ($user && is_object($user) && is_a($user, 'WP_User')) {
                if ($user->has_cap('woo_report_role')) {
                    $url         = $this->pw_plugin_main_url;
                    $redirect_to = admin_url("admin.php?page=$url");
                }
            }

            return $redirect_to;

            //is there a user to check?
            global $user;
            if (isset($user->roles) && is_array($user->roles)) {
                //check for admins

                if (in_array('woo_report_role', $user->roles)) {
                    // redirect them to the default place
                    $url = $this->pw_plugin_main_url;
                    wp_redirect(admin_url("admin.php?page=$url"));

                    $url = $this->pw_plugin_main_url;

                    return admin_url("admin.php?page=$url");
                }

            } else {
                return $redirect_to;
            }

            return $redirect_to;
        }

        function custom_menu_page_removing()
        {
            echo '<style>#adminmenuwrap,#wp-admin-bar-root-default{display:none;}</style>';

            echo '<script >
				jQuery(document).ready(function($){
					jQuery("#adminmenuwrap, #adminmenuback, #wp-admin-bar-root-default").remove();
					jQuery("body").addClass("woo_report_role");
				});
			</script>';
        }

        function get_capability()
        {
            //$role_capability='manage_options';
            $role_capability = 'edit_posts';
            $role_capability = 'edit_pages';
            //$role_capability='read';


            if (defined("__PW_PERMISSION_ADD_ON__")) {
                global $current_user;
                $current_user = wp_get_current_user();

                $user_info = get_userdata($current_user->ID);

                $role = get_role(strtolower($user_info->roles[0]));

                if (strtolower($user_info->roles[0]) == 'administrator') {
                    return 'manage_options';
                }

                //$role->has_cap( 'pw_report_appear' );

                if (isset($role->capabilities['pw_report_appear']) && $role->capabilities['pw_report_appear']) {
                    $role_capability = 'pw_report_appear';
                }
            }

            return $role_capability;
        }

        function get_dashboard_capability($menu_id)
        {
            $permission = true;
            if (defined("__PW_PERMISSION_ADD_ON__")) {

                global $current_user;
                $current_user = wp_get_current_user();
                $user_info    = $current_user->user_login;

                $user_infos = get_userdata($current_user->ID);
                if (strtolower($user_infos->roles[0]) == 'administrator') {
                    return true;
                }

                if (get_option("pw_" . $user_info . "_dashborad_permission") != '') {
                    $menu_permission = get_option("pw_" . $user_info . "_dashborad_permission");
                } else {
                    $user_info       = get_userdata($current_user->ID);
                    $menu_permission = get_option("pw_" . $user_info->roles[0] . "_dashborad_permission");
                    if (strtolower($user_info->roles[0]) == 'administrator') {
                        return true;
                    }
                }

                $fetched_value = json_decode($menu_permission);
                $keys          = "pw_elm_enable_" . $menu_id;
                $current_value = isset($fetched_value->$keys) ? $fetched_value->$keys : "";
                //echo $current_value;
                if ($current_value == 'off' || $current_value == '') {
                    $permission = false;
                }
            }

            return $permission;
        }

        function get_menu_capability($menu_id)
        {
            $permission = true;
            if (defined("__PW_PERMISSION_ADD_ON__")) {

                global $current_user;
                $current_user = wp_get_current_user();
                $user_info    = $current_user->user_login;

                $user_infos = get_userdata($current_user->ID);
                if (strtolower($user_infos->roles[0]) == 'administrator') {
                    return true;
                }

                if (get_option("pw_" . $user_info . "_permission") != '') {
                    $menu_permission = get_option("pw_" . $user_info . "_permission");
                } else {
                    $user_info       = get_userdata($current_user->ID);
                    $menu_permission = get_option("pw_" . $user_info->roles[0] . "_permission");
                    if (strtolower($user_info->roles[0]) == 'administrator') {
                        return true;
                    }
                }


                $fetched_value = json_decode($menu_permission);
                $keys          = "pw_elm_enable_" . $menu_id;
                $current_value = isset($fetched_value->$keys) ? $fetched_value->$keys : "";
                //echo $current_value;
                if ($current_value == 'off' || $current_value == '') {
                    $permission = false;
                }
            }

            return $permission;
        }

        function get_form_element_permission($field_id, $key = '')
        {
            $permission = true;
            if (defined("__PW_PERMISSION_ADD_ON__")) {
                global $current_user;
                $current_user = wp_get_current_user();
                $user_info    = $current_user->user_login;

                $user_infos = get_userdata($current_user->ID);
                if (strtolower($user_infos->roles[0]) == 'administrator') {
                    return true;
                }

                if (get_option("pw_" . $user_info . "_permission") != '') {
                    $menu_permission = get_option("pw_" . $user_info . "_permission");
                } else {
                    $user_info       = get_userdata($current_user->ID);
                    $menu_permission = get_option("pw_" . $user_info->roles[0] . "_permission");
                    if (strtolower($user_info->roles[0]) == 'administrator') {
                        return true;
                    }
                }

                $fetched_value = json_decode($menu_permission);
                $parent        = isset($_GET['smenu']) ? $_GET['smenu'] : $_GET['parent'];
                if ($key != '') {
                    $parent = $key;
                }
                $keys = "pw_elm_checkbox_" . $parent . "_" . $field_id;
                //print_r($fetched_value);
                $current_value = isset($fetched_value->$keys) ? $fetched_value->$keys : "";
                //echo $current_value;
                if ($current_value == '') {
                    $permission = false;
                }
            }

            return $permission;
        }

        function get_form_element_value_permission($field_id, $key = '')
        {
            $permission = true;
            if (defined("__PW_PERMISSION_ADD_ON__")) {
                global $current_user;
                $current_user = wp_get_current_user();
                $user_info    = $current_user->user_login;

                $user_infos = get_userdata($current_user->ID);
                if (strtolower($user_infos->roles[0]) == 'administrator') {
                    return true;
                }

                if (get_option("pw_" . $user_info . "_permission") != '') {
                    $menu_permission = get_option("pw_" . $user_info . "_permission");
                } else {
                    $user_info       = get_userdata($current_user->ID);
                    $menu_permission = get_option("pw_" . $user_info->roles[0] . "_permission");
                    if (strtolower($user_info->roles[0]) == 'administrator') {
                        return true;
                    }

                }

                $fetched_value = json_decode($menu_permission);
                $parent        = isset($_GET['smenu']) ? $_GET['smenu'] : $_GET['parent'];
                if ($key != '') {
                    $parent = $key;
                }
                $keys = "pw_elm_value_" . $parent . "_" . $field_id;
                //print_r($fetched_value->$keys);
                if (isset($fetched_value->$keys) && ! in_array("all", $fetched_value->$keys)) {
                    return $fetched_value->$keys;
                }
            }

            return $permission;
        }

        function pw_get_form_element_permission($field_id, $field_value, $key = '')
        {
            if ( ! defined("__PW_PERMISSION_ADD_ON__")) {
                return $field_value;
            }
            $permission_value  = $this->get_form_element_value_permission($field_id, $key);
            $permission_enable = $this->get_form_element_permission($field_id, $key);
            if ($permission_enable && $field_value == '-1' && $permission_value != 1) {
                return implode(",", $permission_value);
            }

            return $field_value;
        }

        function pw_activate_hook()
        {
            //DEACTIVATE 2 EXTRA ADD-ONS
            $dir = plugin_dir_path(__DIR__);
            // Absolute path to plugins dir
            $my_plugin = $dir . '/PW-Advanced-Woocommerce-Reporting-System-Crosstab-addon/main.php';
            // Check to see if plugin is already active
            deactivate_plugins($my_plugin, true);
            if (is_plugin_active($my_plugin)) {

            }

            $my_plugin = $dir . '/PW-Advanced-Woocommerce-Reporting-System-Variaion-addon/main.php';
            // Check to see if plugin is already active
            deactivate_plugins($my_plugin, true);
            if (is_plugin_active($my_plugin)) {

            }
        }

        function pw_report_setup_menus()
        {
            global $submenu;

            //IF WANT TO SHOW MENU FOR ALL USER USE 'edit_posts'

            $role_capability = $this->get_capability();
            //echo $role_capability;

            add_menu_page(__('Woo Reporting', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Woo Reporting', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, $this->pw_plugin_main_url,
                array($this, 'wcx_plugin_dashboard'), 'dashicons-chart-pie', 65);

            add_submenu_page($this->pw_plugin_main_url, __('Settings', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Settings', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_setting_report&parent=setting&smenu=setting',
                array($this, 'wcx_plugin_mani_settings'));


            add_submenu_page(null, __('Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_dashboard',
                array($this, 'wcx_plugin_dashboard'));

            add_submenu_page(null, __('My Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('My Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_plugin_menu_my_dashboard',
                array($this, 'wcx_plugin_menu_my_dashboard'));

            add_submenu_page(null, __('Details', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Details', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_details',
                array($this, 'wcx_plugin_menu_details'));

            //CUSTOM WORK - 12300
            //add_submenu_page(null, __('Details Tickera',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Details Tickera',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_details_tickera',   array($this,'wcx_plugin_menu_details_tickera' ) );

            //CUSTOM WORK - 4186
            add_submenu_page(null, __('Details Full', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Details Full', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_details_full', array($this, 'wcx_plugin_menu_details_full'));

            //CUSTOM WORK - 53
            add_submenu_page(null, __('Details Full Billing/Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Details Full Billing/Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_details_full_shipping', array($this, 'wcx_plugin_menu_details_full_shipping'));

            //CUSTOM WORK - 16
            add_submenu_page(null, __('Details Full Billing|Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Details Full Billing|Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_details_full_shipping_tax', array($this, 'wcx_plugin_menu_details_full_shipping_tax'));

            //CUSTOM WORK - 4179
            add_submenu_page(null, __('Status Change', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Status Change', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_order_status_change', array($this, 'wcx_plugin_menu_order_status_change'));

            ////ADDED IN VER4.0
            /// ORDER PER COUNTRY
            add_submenu_page(null, __('All Orders (Custom Taxonomy, Field)', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('All Orders (Custom Taxonomy, Field)', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_details_order_country', array($this, 'wcx_plugin_menu_details_order_country'));

            add_submenu_page(null, __('Order/Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Order Per Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_order_per_country', array($this, 'wcx_plugin_menu_order_per_country'));


            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //CUSTOM TAX & FIELDS

            //ALL DETAILS
            add_submenu_page(null, __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_product',
                array($this, 'wcx_plugin_menu_product'));


            add_submenu_page(null, __('Stock List', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Stock List', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_stock_list',
                array($this, 'wcx_plugin_menu_stock_list'));

            ////ADDED IN VER4.5
            //CUSTOM WORK
            add_submenu_page(null, __('Product/Users', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Product/Users', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_product_per_users', array($this, 'wcx_plugin_menu_product_per_users'));


            add_submenu_page(null, __('Profit', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Profit', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_profit',
                array($this, 'wcx_plugin_menu_profit'));
            add_submenu_page(null, __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Category', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_category',
                array($this, 'wcx_plugin_menu_category'));
            ////ADDED IN VER4.0
            add_submenu_page(null, __('Tag', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Tag', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_tags',
                array($this, 'wcx_plugin_menu_tags'));
            add_submenu_page(null, __('Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_customer',
                array($this, 'wcx_plugin_menu_customer'));
            add_submenu_page(null, __('Billing Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Billing Country', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_billingcountry', array($this, 'wcx_plugin_menu_billingcountry'));
            add_submenu_page(null, __('Billing State', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Billing State', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_billingstate', array($this, 'wcx_plugin_menu_billingstate'));
            ////ADDED IN VER4.0
            add_submenu_page(null, __('Billing City', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Billing City', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_billingcity', array($this, 'wcx_plugin_menu_billingcity'));
            add_submenu_page(null, __('Payment Gateway', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Payment Gateway', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_paymentgateway', array($this, 'wcx_plugin_menu_paymentgateway'));
            add_submenu_page(null, __('Order Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Order Status', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_orderstatus', array($this, 'wcx_plugin_menu_orderstatus'));
            add_submenu_page(null, __('Recent Order', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Recent Order', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_recentorder', array($this, 'wcx_plugin_menu_recentorder'));
            add_submenu_page(null, __('Tax Report', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Tax Report', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_taxreport',
                array($this, 'wcx_plugin_menu_taxreport'));
            add_submenu_page(null, __('Purchased Product by Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Purchased Product by Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_customrebuyproducts', array($this, 'wcx_plugin_menu_customrebuyproducts'));


            //CUSTOM WORK - 17427
            if (is_array(__CUSTOMWORK_ID__) && in_array('17427', __CUSTOMWORK_ID__)) {
                add_submenu_page(null, __('Purchased Category by Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    __('Purchased Category by Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                    'wcx_wcreport_plugin_customer_category', array(
                        $this,
                        'wcx_plugin_menu_customer_category'
                    ));
            }


            //CUSTOM WORK - 15092
            if (is_array(__CUSTOMWORK_ID__) && in_array('15092', __CUSTOMWORK_ID__)) {
                add_submenu_page(null, __('Order Per Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    __('Order Per Shipping', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                    'wcx_wcreport_plugin_order_per_custom_shipping', array(
                        $this,
                        'wcx_plugin_menu_order_per_custom_shipping'
                    ));
            }


            add_submenu_page(null, __('Refund Details', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Refund Details', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_refunddetails', array($this, 'wcx_plugin_menu_refunddetails'));
            add_submenu_page(null, __('Coupon', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Coupon', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_coupon',
                array($this, 'wcx_plugin_menu_coupon'));

            //CUSTOM WORK - 12679
            if (is_array(__CUSTOMWORK_ID__) && in_array('12679', __CUSTOMWORK_ID__)) {
                add_submenu_page(null, __('Total Sales per Clinic', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    __('Total Sales per Clinic', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                    'wcx_wcreport_plugin_clinic', array($this, 'wcx_plugin_menu_clinic'));
            }


            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            /////ADDED IN VER4.0
            /// OTHER SUMMARY
            add_submenu_page(null, __('Coupon Discount', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Coupon Discount', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_coupon_discount', array($this, 'wcx_plugin_menu_coupon_discount'));
            add_submenu_page(null, __('Customer Analysis', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Customer Analysis', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_customer_analysis', array($this, 'wcx_plugin_menu_customer_analysis'));
            //add_submenu_page(null, __('Frequently Order Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), __('Frequently Order Customer',__PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability, 'wcx_wcreport_plugin_customer_order_frequently',   array($this,'wcx_plugin_menu_customer_order_frequently' ) );
            add_submenu_page(null, __('Customer in Price Point', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Customer in Price Point', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_customer_min_max', array($this, 'wcx_plugin_menu_customer_min_max'));
            add_submenu_page(null, __('Customer/Non Purchase', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Customer/Non Purchase', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_customer_no_purchased', array($this, 'wcx_plugin_menu_customer_no_purchased'));


            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //STOCK REOPRTS
            /////ADDED IN VER4.0
            add_submenu_page(null, __('Zero Level Stock', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Zero Level Stock', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_stock_zero_level', array($this, 'wcx_plugin_menu_stock_zero_level'));

            add_submenu_page(null, __('Minimum Level Stock', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Minimum Level Stock', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_stock_min_level', array($this, 'wcx_plugin_menu_stock_min_level'));

            add_submenu_page(null, __('Most Stocked', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Most Stocked', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_stock_max_level', array($this, 'wcx_plugin_menu_stock_max_level'));

            add_submenu_page(null, __('Summary Stock Planner', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Summary Stock Planner', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_stock_summary_avg', array($this, 'wcx_plugin_menu_stock_summary_avg'));

            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //ORDER ANALYSIS
            /////ADDED IN VER4.0
            add_submenu_page(null, __('Analysis Simple products', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Analysis Simple products', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_order_product_analysis', array($this, 'wcx_plugin_menu_order_product_analysis'));
            add_submenu_page(null, __('Analysis Variation products', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Analysis Simple products', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_order_variation_analysis',
                array($this, 'wcx_plugin_menu_order_variation_analysis'));

            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //PRODUCT OPTIONS CUSTOM FIELDS
            /////ADDED IN VER4.0
            /// PRODUCT OPTIONS CUSTOM FIELDS


            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //CROSSTAB

            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //VARIATION

            //STOCK VARIATION
            add_submenu_page(null, __('Project vs Actual Sale', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Project vs Actual Sale', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_projected_actual_sale', array($this, 'wcx_plugin_menu_projected_actual_sale'));
            add_submenu_page(null, __('Tax Reports', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Tax Reports', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_tax_reports', array($this, 'wcx_plugin_menu_tax_reports'));


            //CUSTOM WORK - 12412
            add_submenu_page(null, __('Product Qty.', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Product Qty.', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_product_variation_qty', array($this, 'wcx_plugin_menu_product_variation_qty'));


            //////////////////////////////////////////////
            //////////////////////
            //////////////////////////////////////////////
            //ABANDONED CART
            /////ADDED IN VER4.9
            add_submenu_page(null, __('Abandoned Products', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Products', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_abandoned_products', array($this, 'wcx_plugin_menu_abandoned_products'));
            add_submenu_page(null, __('Abandoned Carts', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Abandoned Carts Data', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_abandoned_cart', array($this, 'wcx_plugin_menu_abandoned_cart'));


            /////////////////////////////
            //SETTINGS
            /////////////////////////////////
            add_submenu_page(null, __('Settings', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Report Settings', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_setting_report', array($this, 'wcx_plugin_menu_setting_report'));

            add_submenu_page(null, __('Add-ons', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Report Add-ons', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_addons_report', array($this, 'wcx_plugin_menu_addons_report'));

            add_submenu_page(null, __('Proword', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Other Useful Plugins', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_proword_report', array($this, 'wcx_plugin_menu_proword_report'));

            add_submenu_page(null, __('Activate Plugin', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                __('Active Plugin', __PW_REPORT_WCREPORT_TEXTDOMAIN__), $role_capability,
                'wcx_wcreport_plugin_active_report', array($this, 'wcx_plugin_menu_active_report'));

            //CUSTOMIZE MENUS
            do_action('pw_report_wcreport_admin_menu');

        }

        function wcx_plugin_dashboard($display = "all")
        {
            $this->pages_fetch("dashboard_report.php", $display);
        }

        function wcx_plugin_mani_settings($display = "all")
        {
            include("class/setting_general.php");
        }

        function wcx_plugin_menu_my_dashboard()
        {
            $this->pages_fetch("reports.php");
        }

        //Details
        function wcx_plugin_menu_details()
        {
            $this->pages_fetch("details.php");
        }

        //Details Full
        //CUSTOM WORK - 4186
        function wcx_plugin_menu_details_full()
        {
            $this->pages_fetch("details_full.php");
        }

        //Details Full Billing/Shipping
        //CUSTOM WORK - 53
        function wcx_plugin_menu_details_full_shipping()
        {
            $this->pages_fetch("details_full_shipping.php");
        }

        //Details Full Billing/Shipping with Tax
        //CUSTOM WORK - 16
        function wcx_plugin_menu_details_full_shipping_tax()
        {
            $this->pages_fetch("details_full_shipping_tax.php");
        }


        //CUSTOM WORK - 12300
//		function wcx_plugin_menu_details_tickera(){
//			$this->pages_fetch("details_tickera.php");
//		}


        //ADDED IN VER 4.9
        //ABANDONED
        function wcx_plugin_menu_abandoned_cart()
        {
            $this->pages_fetch("abandoned_cart.php");
        }

        function wcx_plugin_menu_abandoned_products()
        {
            $this->pages_fetch("abandoned_product.php");
        }


        //CUSTOM WORK - 4179
        function wcx_plugin_menu_order_status_change()
        {
            $this->pages_fetch("order_status_change.php");
        }

        /////ADDED IN VER4.0
        /// ORDER PER COUNTRY
        function wcx_plugin_menu_details_order_country()
        {
            $this->pages_fetch("details_order_country.php");
        }

        function wcx_plugin_menu_order_per_country()
        {
            $this->pages_fetch("order_per_country.php");
        }

        //////////////////////ALL DETAILS//////////////////////
        function array_insert_after($key, array &$array, $new_key, $new_value)
        {
            if (array_key_exists($key, $array)) {
                $new = array();
                foreach ($array as $k => $value) {
                    $new[$k] = $value;
                    if ($k === $key) {
                        $new[$new_key] = $new_value;
                    }
                }

                return $new;
            }

            return false;
        }

        function fetch_our_menu_fav($report_name = '')
        {

            $current_user = wp_get_current_user();
            $user_info    = $current_user->user_login;

            $fav_menu           = get_option(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . "fav_menus_" . $user_info);
            $this->our_menu_fav = $fav_menu;
            if (is_array($fav_menu) && count($fav_menu) > 0 && in_array($report_name, $fav_menu)) {
                return true;
            }

            return false;
        }

        function pages_fetch($page, $display = "all")
        {
            $pw_plugin_main_url = '';
            if ($this->pw_plugin_main_url) {
                $pw_plugin_main_url = 'admin.php?page=' . $this->pw_plugin_main_url;
            }

            //NEW MENU
            $this->our_menu = array(
                "logo" => array(
                    "label"     => '',
                    "id"        => "logo",
                    "link"      => '#',
                    "icon"      => __PW_REPORT_WCREPORT_URL__ . "/assets/images/logo.png",
                    "mini_icon" => __PW_REPORT_WCREPORT_URL__ . "/assets/images/mini_logo.png",
                ),

                "dashboard" => array(
                    "label" => __('Dashboard', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "dashboard",
                    "link"  => $pw_plugin_main_url,
                    "icon"  => "fa-bookmark",
                ),


                "all_order_reports" => array(
                    "label"  => __('Order', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"     => "all_order_reports",
                    "link"   => "#",
                    "icon"   => "fa-shopping-cart",
                    "childs" => array(
                        "all_orders"               => array(
                            "label" => __('All Orders', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "all_orders",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_details&parent=all_order_reports&smenu=all_orders",
                            "icon"  => "fa-file-text",
                        ),
                        //CUSTOM WORK - 12300
//						"all_orders_tickera" => array(
//							"label" => __('All Orders Tickera', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
//							"id" => "all_orders_tickera",
//							"link" => "admin.php?page=wcx_wcreport_plugin_details_tickera&parent=all_orders_tickera&smenu=all_orders_tickera",
//							"icon" => "fa-file-text",
//						),
                        "all_orders_full"          => array(
                            "label" => __('All Orders Billing', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "all_orders_full",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_details_full&parent=all_order_reports&smenu=all_orders_full",
                            "icon"  => "fa-file-text",
                        ),
                        "order_per_country"        => array(
                            "label" => __("Order/Country", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "order_per_country",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_order_per_country&parent=all_order_reports&smenu=order_per_country",
                            "icon"  => "fa-eye-slash",
                        ),
                        "order_status"             => array(
                            "label" => __("Order Status", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "order_status",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_orderstatus&parent=all_order_reports&smenu=order_status",
                            "icon"  => "fa-check",
                        ),

                        //CUSTOM WORK - 4179
                        "order_status_change"      => array(
                            "label" => __("Status Change", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "order_status_change",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_order_status_change&parent=all_order_reports&smenu=order_status_change",
                            "icon"  => "fa-check",
                        ),
                        "recent_order"             => array(
                            "label" => __("Recent Order", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "recent_order",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_recentorder&parent=all_order_reports&smenu=recent_order",
                            "icon"  => "fa-shopping-cart",
                        ),
                        "refund_detail"            => array(
                            "label" => __("Refund Detail", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "refund_detail",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_refunddetails&parent=all_order_reports&smenu=refund_detail",
                            "icon"  => "fa-eye-slash",
                        ),
                        ////ADDED IN VER4.0
                        //ORDER ANALYSIS
                        "order_product_analysis"   => array(
                            "label" => __("Analysis Simple Products", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "order_product_analysis",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_order_product_analysis&parent=all_order_reports&smenu=order_product_analysis",
                            "icon"  => "fa-line-chart",
                        ),
                        "order_variation_analysis" => array(
                            "label" => __("Analysis Variation Products", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "order_variation_analysis",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_order_variation_analysis&parent=all_order_reports&smenu=order_variation_analysis",
                            "icon"  => "fa-area-chart",
                        ),


                    )
                ),


                //ADDED IN VER 4.9
//				"abandoned_carts" => array(
//					"label" => __('Abandoned Cart',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//					"id" => "abandoned_carts",
//					"link" => "#",
//					"icon" => "fa-shopping-cart",
//					"childs" => array(
//						"abandoned_products" => array(
//							"label" => __("Products" ,__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//							"id" => "abandoned_products",
//							"link" => "admin.php?page=wcx_wcreport_plugin_abandoned_products&parent=abandoned_carts&smenu=abandoned_products",
//							"icon" => "fa-pie-chart",
//						),
//						"abandoned_cart" => array(
//							"label" => __('Cart Data',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//							"id" => "abandoned_cart",
//							"link" => "admin.php?page=wcx_wcreport_plugin_abandoned_cart&parent=abandoned_carts&smenu=abandoned_cart",
//							"icon" => "fa-pie-chart",
//						),
//					)
//				),

                "product_reports" => array(
                    "label"  => __('Product', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"     => "product_reports",
                    "link"   => "#",
                    "icon"   => "fa-shopping-bag",
                    "childs" => array(
                        "product"               => array(
                            "label" => __("Purchased Product", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "product",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_product&parent=product_reports&smenu=product",
                            "icon"  => "fa-cog",
                        ),
                        //CUSTOM WORK - 12412
                        "product_variation_qty" => array(
                            "label" => __("Purchased Product Qty", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "product_variation_qty",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_product_variation_qty&parent=product_reports&smenu=product_variation_qty",
                            "icon"  => "fa-cog",
                        ),

                        ////ADDED IN VER4.5
                        //CUSTOM WORK
                        "product_per_users"     => array(
                            "label" => __("Product/Users", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "product_per_users",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_product_per_users&parent=product_reports&smenu=product_per_users",
                            "icon"  => "fa-cog",
                        ),

                        "category" => array(
                            "label" => __("Category", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "category",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_category&parent=product_reports&smenu=category",
                            "icon"  => "fa-tags",
                        ),

                        ////ADDED IN VER4.0
                        "tags"     => array(
                            "label" => __("Tag", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "tags",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_tags&parent=product_reports&smenu=tags",
                            "icon"  => "fa-tags",
                        ),

                        "customer_buy_prod" => array(
                            "label" => __("Purchased Product by Customer", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "customer_buy_prod",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_customrebuyproducts&parent=product_reports&smenu=customer_buy_prod",
                            "icon"  => "fa-users",
                        ),

                        //CUSTOM WORK 17427


                        "stock_list"        => array(
                            "label" => __('Product Stock', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "stock_list",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_stock_list&parent=product_reports&smenu=stock_list",
                            "icon"  => "fa-cart-arrow-down",
                        ),

                        /////ADDED IN VER4.0
                        /// STOCK REPORTS
                        "stock_zero_level"  => array(
                            "label" => __("Zero Level Stock", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "stock_zero_level",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_stock_zero_level&parent=product_reports&smenu=stock_zero_level",
                            "icon"  => "fa-exclamation-triangle",
                        ),
                        "stock_min_level"   => array(
                            "label" => __("Minimum Level Stock", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "stock_min_level",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_stock_min_level&parent=product_reports&smenu=stock_min_level",
                            "icon"  => "fa-level-down",
                        ),
                        "stock_max_level"   => array(
                            "label" => __("Most Stocked", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "stock_max_level",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_stock_max_level&parent=product_reports&smenu=stock_max_level",
                            "icon"  => "fa-level-up",
                        ),
                        "stock_summary_avg" => array(
                            "label" => __("Summary Stock Planner", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "stock_summary_avg",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_stock_summary_avg&parent=product_reports&smenu=stock_summary_avg",
                            "icon"  => "fa-newspaper-o",
                        ),
                    )
                ),

                "customer_reports" => array(
                    "label"  => __('Customer', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"     => "customer_reports",
                    "link"   => "#",
                    "icon"   => "fa-user",
                    "childs" => array(

                        "customer"              => array(
                            "label" => __("Customer", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "customer",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_customer&parent=customer_reports&smenu=customer",
                            "icon"  => "fa-user",
                        ),
                        ////ADDED IN VER4.0
                        //OTHER SUMMARY
                        "customer_analysis"     => array(
                            "label" => __("Customer Analysis", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "customer_analysis",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_customer_analysis&parent=customer_reports&smenu=customer_analysis",
                            "icon"  => "fa-bar-chart",
                        ),
                        "customer_min_max"      => array(
                            "label" => __("Customer Min-Max", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "customer_min_max",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_customer_min_max&parent=customer_reports&smenu=customer_min_max",
                            "icon"  => "fa-hand-pointer-o",
                        ),
                        "customer_no_purchased" => array(
                            "label" => __("Customer/Non Purchase", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "customer_no_purchased",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_customer_no_purchased&parent=customer_reports&smenu=customer_no_purchased",
                            "icon"  => "fa-ban",
                        ),

                    )
                ),

                //CUSTOM TAX & FIELD

                "more_reports" => array(
                    "label"  => __('More Reports', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"     => "more_reports",
                    "link"   => "#",
                    "icon"   => "fa-files-o",
                    "childs" => array(

                        "profit" => __PW_COG__ != '' ? array(
                            "label" => __("Profit", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "profit",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_profit&parent=more_reports&smenu=profit",
                            "icon"  => "fa-money",
                        ) : false,

                        "billing_country" => array(
                            "label" => __("Billing Country", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "billing_country",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_billingcountry&parent=more_reports&smenu=billing_country",
                            "icon"  => "fa-globe",
                        ),
                        "billing_state"   => array(
                            "label" => __("Billing State", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "billing_state",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_billingstate&parent=more_reports&smenu=billing_state",
                            "icon"  => "fa-map",
                        ),
                        ////ADDED IN VER4.0
                        "billing_city"    => array(
                            "label" => __("Billing City", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "billing_city",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_billingcity&parent=more_reports&smenu=billing_city",
                            "icon"  => "fa-map-marker",
                        ),
                        "payment_gateway" => array(
                            "label" => __("Payment Gateway", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "payment_gateway",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_paymentgateway&parent=more_reports&smenu=payment_gateway",
                            "icon"  => "fa-credit-card",
                        ),

                        "coupon"           => array(
                            "label" => __("Coupon", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "coupon",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_coupon&parent=more_reports&smenu=coupon",
                            "icon"  => "fa-hashtag",
                        ),
                        "coupon_discount"  => array(
                            "label" => __("Coupon Discount", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "coupon_discount",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_coupon_discount&parent=more_reports&smenu=coupon_discount",
                            "icon"  => "fa-percent",
                        ),
                        "proj_actual_sale" => array(
                            "label" => __('Project vs Actual Sale', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "proj_actual_sale",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_projected_actual_sale&parent=more_reports&smenu=proj_actual_sale",
                            "icon"  => "fa-calendar-check-o",
                        ),

                    )
                ),

                "tax_reports" => array(
                    "label"  => __('Tax', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"     => "tax_reports",
                    "link"   => "#",
                    "icon"   => "fa-percent",
                    "childs" => array(
                        "tax_report"  => array(
                            "label" => __("Tax Report", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "tax_report",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_taxreport&parent=tax_reports&smenu=tax_report",
                            "icon"  => "fa-pie-chart",
                        ),
                        "tax_reports" => array(
                            "label" => __('Tax Reports', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                            "id"    => "tax_reports",
                            "link"  => "admin.php?page=wcx_wcreport_plugin_tax_reports&parent=tax_reports&smenu=tax_reports",
                            "icon"  => "fa-pie-chart",
                        ),
                    )
                ),

                //CROSSTAB
                //VARIATION

                //VARIATION STOCK

                "setting" => array(
                    "label" => __('Settings', __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "setting",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_setting_report&parent=setting&smenu=setting",
                    "icon"  => "fa-cogs",
                ),


//				"proword" => array(
//					"label" => __('Proword',__PW_REPORT_WCREPORT_TEXTDOMAIN__),
//					"id" => "proword",
//					"link" => "admin.php?page=wcx_wcreport_plugin_proword_report&parent=proword&smenu=proword",
//					"icon" => "fa-product-hunt",
//				),
            );


            //CUSTOM WORK - 53
            if (is_array(__CUSTOMWORK_ID__) && in_array('53', __CUSTOMWORK_ID__)) {

                $extra_menu                                    = array(
                    "label" => __("All Orders Billing/Shipping", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "all_orders_full_shipping",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_details_full_shipping&parent=all_order_reports&smenu=all_orders_full_shipping",
                    "icon"  => "fa-area-chart",
                );
                $this->our_menu['all_order_reports']['childs'] = $this->array_insert_after("all_orders_full",
                    $this->our_menu['all_order_reports']['childs'], "all_orders_full_shipping", $extra_menu);

            }

            //CUSTOM WORK - 16
            if (is_array(__CUSTOMWORK_ID__) && in_array('16', __CUSTOMWORK_ID__)) {

                $extra_menu                                    = array(
                    "label" => __("All Orders Billing|Shipping", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "all_orders_full_shipping_tax",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_details_full_shipping_tax&parent=all_order_reports&smenu=all_orders_full_shipping_tax",
                    "icon"  => "fa-area-chart",
                );
                $this->our_menu['all_order_reports']['childs'] = $this->array_insert_after("all_orders_full",
                    $this->our_menu['all_order_reports']['childs'], "all_orders_full_shipping_tax", $extra_menu);

            }

            //CUSTOM WORK - 12679
            if (is_array(__CUSTOMWORK_ID__) && in_array('12679', __CUSTOMWORK_ID__)) {

                $extra_menu                                    = array(
                    "label" => __("Total Sales per Clinic", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "clinic",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_clinic&parent=all_order_reports&smenu=clinic",
                    "icon"  => "fa-area-chart",
                );
                $this->our_menu['all_order_reports']['childs'] = $this->array_insert_after("all_orders",
                    $this->our_menu['all_order_reports']['childs'], "clinic", $extra_menu);

            }

            //CUSTOM WORK - 15092
            if (is_array(__CUSTOMWORK_ID__) && in_array('15092', __CUSTOMWORK_ID__)) {

                $extra_menu                                    = array(
                    "label" => __("Order / Shipping", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "order_per_custom_shipping",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_order_per_custom_shipping&parent=all_order_reports&smenu=order_per_custom_shipping",
                    "icon"  => "fa-users",
                );
                $this->our_menu['all_order_reports']['childs'] = $this->array_insert_after("order_per_country",
                    $this->our_menu['all_order_reports']['childs'], "order_per_custom_shipping", $extra_menu);

            }


            //CUSTOM WORK - 17427
            if (is_array(__CUSTOMWORK_ID__) && in_array('17427', __CUSTOMWORK_ID__)) {

                $extra_menu                                  = array(
                    "label" => __("Purchased Category by Customer", __PW_REPORT_WCREPORT_TEXTDOMAIN__),
                    "id"    => "customer_category",
                    "link"  => "admin.php?page=wcx_wcreport_plugin_customer_category&parent=product_reports&smenu=customer_category",
                    "icon"  => "fa-users",
                );
                $this->our_menu['product_reports']['childs'] = $this->array_insert_after("customer_buy_prod",
                    $this->our_menu['product_reports']['childs'], "customer_category", $extra_menu);

            }


            $visible_menu = array();

            include("class/pages_fetch_dashboards.php");
        }

        ////ADDED IN VER4.0
        /// MENU GENERATOR
        function pw_menu_generator($our_menu, $menu_type = '', $selected_menu)
        {
            $menu_html           = '';
            $menu_html_mini      = '';
            $menu_html_mini_logo = '';
            $menu_html_fav       = '';
            $menu_html_mini_fav  = '';
            $fav_menu_html       = '';
            $fav_menu_html_mini  = '';

            $parent_fav        = array();
            $fav_active_parent = '';
            $fav_active        = '';
            $fav_active_icon   = 'fa-angle-right';


            //$menu_html.= '<ul class="bn-mainmenu-list-ul">';
            $parent = $selected_menu['parent'];
            $smenu  = $selected_menu['smenu'];
            //print_r($selected_menu);

            foreach ($our_menu as $key => $menus) {

                if (defined("__PW_PERMISSION_ADD_ON__")) {
                    if ( ! $this->get_menu_capability($menus['id'])) {
                        continue;
                    }
                }

                if ($key == 'logo') {

                    $menu_html_mini_logo .= '
				     <div class="awr-item">
				      	<a href="javascript:void(0);">
				      		<img src="' . $menus['mini_icon'] . '" class="small image">
				      	</a>	
				     </div>';

                    continue;
                }

                $same_title = array(
                    "all_orders",
                    "details_product_options",
                    "details_brands",
                    "brand_tax_field",
                    "brand_brands"
                );

                $activate         = '';
                $submenu_id       = '';
                $submenu_id_class = '';
                $activate_parent  = '';
                $icon_toggle      = 'fa-angle-right';
                if (isset($menus['childs']) && array_key_exists($smenu, $menus['childs'])) {
                    $icon_toggle     = 'fa-angle-down';
                    $activate        = ' awr-mainmenu-list-active ';
                    $activate_parent = "style='display:block'";
                } elseif ( ! isset($menus['childs']) && $menus['id'] == $parent) {
                    $activate = ' awr-mainmenu-list-active ';
                } else {
                    $submenu_id       = 'id="' . $menus['id'] . '"';
                    $submenu_id_class = $menus['id'];
                }

                $link = $menus['link'];
                if (isset($menus['childs'])) {
                    $link = '#';
                }


                if (isset($menus['childs'])) {

                    $menu_html .= '<li class="awr-mainmenu-list-hassub ' . $activate . ' ' . $menus['id'] . '" data-parent-id="' . $menus['id'] . '"><a href="javascript:void(0);" class="' . $submenu_id_class . '"><i class="fa ' . $menus['icon'] . '"></i><span>' . $menus['label'] . '</span></a>';

                } else {

                    $menu_html .= '<li class=" ' . $activate . ' ' . $activate . $menus['id'] . '" data-parent-id="' . $menus['id'] . '"><a href="' . $link . '" ' . $submenu_id . '><i class="fa ' . $menus['icon'] . '"></i><span>' . $menus['label'] . '</span></a></li>';

                    $menu_html_mini .= '
					<div class="awr-item">
			            <a href="' . $link . '" ' . $submenu_id . '>
			                <i class="fa ' . $menus['icon'] . '"></i>
			            </a>
			            <div class="awr-mini-submenu">
			                <div class="awr-sub-title">' . $menus['label'] . '</div>
			            </div>
			        </div>';

                }

                if (isset($menus['childs'])) {

                    $menu_html .= '<div class="awr-mainmenu-list-toggle"><i class="fa ' . $icon_toggle . '"></i></div>
						<ul class="awr-mainmenu-list-sub" ' . $activate_parent . '>';

                    $menu_html_mini .= '
					<div class="awr-item ' . $activate . '">
			            <span>
			                <i class="fa ' . $menus['icon'] . '"></i>
			            </span>
			            <div class="awr-mini-submenu">
			            	<div class="awr-sub-title">' . $menus['label'] . '</div>
			            	<div class="awr-sub-links-cnt">';

                    foreach ($menus['childs'] as $child) {
                        // IF AEEAT VAUE is NULL or FALSE
                        if ( ! $child) {
                            continue;
                        }

                        if (defined("__PW_PERMISSION_ADD_ON__")) {
                            if ( ! $this->get_menu_capability($child['id'])) {
                                continue;
                            }
                        }

                        if ($child['id'] == $smenu) {
                            $activate = ' awr-mainmenu-list-active ';
                        } else {
                            $activate = '';
                        }

                        $submenu_id = 'id="' . $child['id'] . '"';

                        $menu_html .= '<li><a class="' . $child['id'] . $activate . ' item" data-parent-id="' . $menus['id'] . '"  href="' . $child['link'] . '" ' . $submenu_id . '><span>' . $child['label'] . '</span></a></li>';

                        $menu_html_mini .= '<a class="' . $child['id'] . $activate . ' awr-sub-link awr-sub-link-active" data-parent-id="' . $menus['id'] . '"  href="' . $child['link'] . '" ' . $submenu_id . '>' . $child['label'] . '</a>';

                        if ($this->fetch_our_menu_fav($child['id'])) {
                            $parent_fav[$menus['id']] = $menus['id'];

                            if ($activate != '') {
                                $fav_active_parent = "style='display:block'";
                                $fav_active_icon   = " fa-angle-down ";
                                $fav_active        = " awr-mainmenu-list-active ";
                            }

                            $fav_title = $child['label'];
                            if (in_array($child['id'], $same_title)) {
                                $fav_title = $menus['label'] . '->' . $child['label'];
                            }

                            $fav_menu_html .= '<li><a class="' . $child['id'] . $activate . ' item" data-parent-id="' . $menus['id'] . '"  href="' . $child['link'] . '" ' . $submenu_id . '><span>' . $fav_title . '</span></a></li>';

                            $fav_menu_html_mini .= '<a class="' . $child['id'] . $activate . ' awr-sub-link awr-sub-link-active" data-parent-id="' . $menus['id'] . '"  href="' . $child['link'] . '" ' . $submenu_id . '>' . $fav_title . '</a>';


                        }
                    }

                    $menu_html      .= '</ul></li>';
                    $menu_html_mini .= '
							</div>
						</div>
        			</div>';


                }//IF has childs
                //$menu_html.='</li>';
            }
            //$menu_html.= '</ul>';

            if ($fav_menu_html != '') {
                $menu_html_fav      .= '<li class="awr-mainmenu-list-hassub ' . $fav_active . ' ' . implode(" ",
                        $parent_fav) . '" data-parent-id="fav_menu"><a href="javascript:void(0);" class=""><i class="fa fa-star"></i><span>' . __('Favorite Menus',
                        __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</span></a><div class="awr-mainmenu-list-toggle"><i class="fa ' . $fav_active_icon . '"></i></div>
						<ul class="awr-mainmenu-list-sub" ' . $fav_active_parent . '>' . $fav_menu_html . '</ul></li>';
                $menu_html_mini_fav .= '
					<div class="awr-item ' . $fav_active . '">
			            <span>
			                <i class="fa fa-star"></i>
			            </span>
			            <div class="awr-mini-submenu">
			            	<div class="awr-sub-title">' . __('Favorite Menus', __PW_REPORT_WCREPORT_TEXTDOMAIN__) . '</div>
			            	<div class="awr-sub-links-cnt">' . $fav_menu_html_mini . '</div>
							</div>
	                    </div>';

            }

            if ($menu_type == 'mini') {
                return $menu_html_mini_logo . $menu_html_mini_fav . $menu_html_mini;
            }

            return $menu_html_fav . $menu_html;
        }

        function getHost($url)
        {
            $parseUrl = parse_url(trim($url));
            if (isset($parseUrl['host'])) {
                $host = $parseUrl['host'];
            } else {
                $path = explode('/', $parseUrl['path']);
                $host = $path[0];
            }
            $host = str_ireplace('www.', '', $host);

            return trim($host);
        }


        function dashboard($item_id = '')
        {
            return true;

            return pw_fetch_reports_core();

            //CHECK IF THE CALL FOR THE FUNCTION WAS EMPTY
            if ($item_id != ''):

                $api_url = 'http://marketplace.envato.com/api/edge/' . $this->username . '/' . $this->api_key . '/verify-purchase:' . $item_id . '.json';


                $response = wp_remote_get($api_url);

                /* Check for errors, if there are some errors return false */
                if (is_wp_error($response) or (wp_remote_retrieve_response_code($response) != 200)) {
                    //$html.='There is another way, you can goto Proword and then past the url of proword here';
                    return false;
                }

                /* Transform the JSON string into a PHP array */
                $result = json_decode(wp_remote_retrieve_body($response), true);

                //print_r($result);
                if (isset($result['verify-purchase']['item_id']) && $result['verify-purchase']['item_id'] == $item_valid_id && isset($result['verify-purchase']['item_name']) && $result['verify-purchase']['item_name']) :
                    $this->pw_core_status = true;

                    return $result;
                //
                else:
                    return false;
                endif;
            endif;

        }


        //1-PRODUCTS
        function wcx_plugin_menu_product()
        {
            $this->pages_fetch("product.php");
        }

        ////ADDED IN VER4.5
        //CUSTOM WORK
        function wcx_plugin_menu_product_per_users()
        {
            $this->pages_fetch("product_per_users.php");
        }

        //2-PROFIT
        function wcx_plugin_menu_profit()
        {
            $this->pages_fetch("profit.php");
        }

        //2-CATEGORY
        function wcx_plugin_menu_category()
        {
            $this->pages_fetch("category.php");
        }

        //ADDED IN VER4.0
        //2-1-TAGS
        function wcx_plugin_menu_tags()
        {
            $this->pages_fetch("tags.php");
        }

        //3-CUSTOMER
        function wcx_plugin_menu_customer()
        {
            $this->pages_fetch("customer.php");
        }

        //4-BILLING COUNTRY
        function wcx_plugin_menu_billingcountry()
        {
            $this->pages_fetch("billingcountry.php");
        }

        //5-BILLING STATE
        function wcx_plugin_menu_billingstate()
        {
            $this->pages_fetch("billingstate.php");
        }

        ////ADDED IN VER4.0
        function wcx_plugin_menu_billingcity()
        {
            $this->pages_fetch("billingcity.php");
        }

        //6-PAYMENT GATEWAY
        function wcx_plugin_menu_paymentgateway()
        {
            $this->pages_fetch("paymentgateway.php");
        }

        //7-ORDER STATUS
        function wcx_plugin_menu_orderstatus()
        {
            $this->pages_fetch("orderstatus.php");
        }

        //8-RECENT ORDER
        function wcx_plugin_menu_recentorder()
        {
            $this->pages_fetch("recentorder.php");
        }

        //9-TAX REPORT
        function wcx_plugin_menu_taxreport()
        {
            $this->pages_fetch("taxreport.php");
        }

        //10-CUSTOMER BUY PRODUCT
        function wcx_plugin_menu_customrebuyproducts()
        {
            $this->pages_fetch("customerbuyproducts.php");
        }


        //CUSTOM WORK 15092
        function wcx_plugin_menu_order_per_custom_shipping()
        {
            $this->pages_fetch("order_per_custom_shipping.php");
        }


        //CUSTOM WORK 17427
        function wcx_plugin_menu_customer_category()
        {
            $this->pages_fetch("customer_category.php");
        }


        //11-REFUND DETAILS
        function wcx_plugin_menu_refunddetails()
        {
            $this->pages_fetch("refunddetails.php");
        }

        //12-COUPON
        function wcx_plugin_menu_coupon()
        {
            $this->pages_fetch("coupon.php");
        }

        //CUSTOM WORK - 12679
        function wcx_plugin_menu_clinic()
        {
            $this->pages_fetch("clinic.php");
        }



        ////ADDED IN VER4.0
        /// OTHER SUMMARY
        function wcx_plugin_menu_coupon_discount()
        {
            $this->pages_fetch("coupon_discount.php");
        }

        function wcx_plugin_menu_customer_analysis()
        {
            $this->pages_fetch("customer_analysis.php");
        }

        function wcx_plugin_menu_customer_order_frequently()
        {
            $this->pages_fetch("customer_order_frequently.php");
        }

        function wcx_plugin_menu_customer_min_max()
        {
            $this->pages_fetch("customer_min_max.php");
        }

        function wcx_plugin_menu_customer_no_purchased()
        {
            $this->pages_fetch("customer_no_purchased.php");
        }


        /////ADDED IN VER4.0
        ////////////////////////STOCK REPORTS/////////////////////////
        function wcx_plugin_menu_stock_zero_level()
        {
            $this->pages_fetch("stock_zero_level.php");
        }

        function wcx_plugin_menu_stock_min_level()
        {
            $this->pages_fetch("stock_min_level.php");
        }

        function wcx_plugin_menu_stock_max_level()
        {
            $this->pages_fetch("stock_max_level.php");
        }

        function wcx_plugin_menu_stock_summary_avg()
        {
            $this->pages_fetch("stock_summary_avg.php");
        }

        /////ADDED IN VER4.0
        ////////////////////////ORDER ANALYSIS/////////////////////////
        function wcx_plugin_menu_order_product_analysis()
        {
            $this->pages_fetch("order_product_analysis.php");
        }

        function wcx_plugin_menu_order_variation_analysis()
        {
            $this->pages_fetch("order_variation_analysis.php");
        }


        //////////////////////CROSS TABS//////////////////////

        //VARIATION
        function wcx_plugin_menu_variation()
        {
            $this->pages_fetch("variation.php");
        }

        //STOCK LIST
        function wcx_plugin_menu_stock_list()
        {
            $this->pages_fetch("stock_list.php");
        }

        //VARIATION STOCK
        function wcx_plugin_menu_variation_stock()
        {
            $this->pages_fetch("variation_stock.php");
        }

        //PROJECTED VS ACTUAL SALE
        function wcx_plugin_menu_projected_actual_sale()
        {
            $this->pages_fetch("projected_actual_sale.php");
        }

        //TAX REPORT
        function wcx_plugin_menu_tax_reports()
        {
            $this->pages_fetch("tax_reports.php");
        }


        //SETTING
        function wcx_plugin_menu_setting_report()
        {
            $this->pages_fetch("setting_report.php");
        }

        //ADD-ONS
        function wcx_plugin_menu_addons_report()
        {
            $this->pages_fetch("addons_report.php");
        }

        //ADD-ONS
        function wcx_plugin_menu_proword_report()
        {
            $this->pages_fetch("advertise_other_plugins.php");
        }

        //ACTIVE
        function wcx_plugin_menu_active_report()
        {
            $this->pages_fetch("plugin_active.php");
        }


        //CUSTOM WORK - 12412
        function wcx_plugin_menu_product_variation_qty()
        {
            $this->pages_fetch("product_variation_qty.php");
        }


        ////ADDED IN VER4.0
        //SEND EMAIL SCHEDULE
        public function wcx_send_email_schedule()
        {

            $act_email_reporting = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'active_email', 0);
            $email_schedule      = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'email_schedule', 'daily');

            $email_daily_report      = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'today_email', 0);
            $email_weekly_report     = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cur_week_email', 0);
            $email_monthly_report    = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cur_month_email', 0);
            $email_till_today_report = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'till_today_email', 0);
            $email_yesterday_report  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'yesterday_email', 0);

            $email_last_week_report  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'last_week_email', 0);
            $email_last_month_report = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'last_month_email', 0);
            $email_this_year_report  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'cur_year_email', 0);
            $email_last_year_report  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'last_year_email', 0);
            $email_total_summary     = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'total_summary', 0);
            $email_time_limit        = 300;

            //CUSTOM WORK - 4061
            $email_product_by_customer = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'product_by_customer',
                0);

            set_time_limit($email_time_limit);//set_time_limit — Limits the maximum execution time

            if ($email_daily_report == 1
                || $email_weekly_report == 1
                || $email_monthly_report == 1
                || $email_till_today_report == 1
                || $email_yesterday_report == 1
                || $email_last_week_report == 1
                || $email_last_month_report == 1
                || $email_this_year_report == 1
                || $email_last_year_report == 1
                || $act_email_reporting == 1
                || $email_total_summary == 1
                //CUSTOM WORK - 4061
                || $email_product_by_customer == 1

            ) {
                //Pass
            } else {
                return '';
            }

            add_action('plugins_loaded', array($this, 'loadTextDomain'));

            //$this->check_parent_plugin();
//			$this->define_constant();

            $post_status       = array();
            $shop_order_status = $this->pw_shop_status;
            $otder_status_hide = $pw_hide_os = $this->otder_status_hide;

            $email_data = "";
            //$today_date		= date_i18n("Y-m-d");
            $today_date = $this->today;
            $timestamp  = strtotime($today_date);
            $report     = array();

            if ($email_weekly_report == 1 || $email_last_week_report == 1) {
                $start_of_week = $this->startWeek();
                $current_day   = strtolower(date('l', $timestamp));
                if ($current_day != $start_of_week) {

                    $this_week_strtotime  = strtotime("last {$start_of_week}", $timestamp);
                    $this_week_start_date = date("Y-m-d", $this_week_strtotime);
                    $this_week_end_date   = date('Y-m-d', strtotime("6 day", $this_week_strtotime));

                    $last_week_strtotime  = strtotime("last {$start_of_week} -7 days", $timestamp);
                    $last_week_start_date = date("Y-m-d", $last_week_strtotime);
                    $last_week_end_date   = date("Y-m-d", strtotime("6 day", $last_week_strtotime));
                } else {
                    $this_week_strtotime  = strtotime("this {$start_of_week}", $timestamp);
                    $this_week_start_date = date("Y-m-d", $this_week_strtotime);
                    $this_week_end_date   = date('Y-m-d', strtotime("6 day", $this_week_strtotime));

                    $last_week_strtotime  = strtotime("this {$start_of_week} -7 days", $timestamp);
                    $last_week_start_date = date("Y-m-d", $last_week_strtotime);
                    $last_week_end_date   = date("Y-m-d", strtotime("6 day", $last_week_strtotime));
                }
            }

            if ($email_daily_report == 1):
                $start_date = $today_date;
                $end_date   = $today_date;
                $title      = __("Today", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_yesterday_report == 1):
                $yesterday_date = date("Y-m-d", strtotime("-1 day", $timestamp));
                $start_date     = $yesterday_date;
                $end_date       = $yesterday_date;
                $title          = __("Yesterday", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data     .= "<br>";
                $email_data     .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]       = $title;
            endif;

            if ($email_weekly_report == 1):
                $end_date   = $this_week_end_date;
                $start_date = $this_week_start_date;
                $title      = __("Current Week", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_last_week_report == 1):
                $end_date   = $last_week_end_date;
                $start_date = $last_week_start_date;
                $title      = __("Last Week", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_monthly_report == 1):
                $end_date   = date('Y-m-d', $timestamp);
                $start_date = date('Y-m-01', strtotime('this month', $timestamp));
                $title      = __("Current Month", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_last_month_report == 1):
                $end_date   = date('Y-m-t', strtotime('last month', $timestamp));
                $start_date = date('Y-m-01', strtotime('last month', $timestamp));
                $title      = __("Last Month", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_this_year_report == 1):
                $end_date   = date('Y-m-d', strtotime('this year', $timestamp));
                $start_date = date('Y-01-01', strtotime('this year', $timestamp));
                $title      = __("Current Year", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_last_year_report == 1):
                $end_date   = date('Y-12-31', strtotime('last year', $timestamp));
                $start_date = date('Y-01-01', strtotime('last year', $timestamp));
                $title      = __("Last Year", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_till_today_report == 1):
                $end_date   = date('Y-m-d', $timestamp);
                $start_date = $this->pw_order_first_date();
                $title      = __("Till Date", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_general_email_data($start_date, $end_date, $title, $post_status,
                    $shop_order_status);
                $report[]   = $title;
            endif;

            if ($email_total_summary == 1):

                //echo $pw_total_shop_day;
                $pw_hide_os           = explode(',', $otder_status_hide);
                $pw_shop_order_status = array();
                if (strlen($shop_order_status) > 0 and $shop_order_status != "-1") {
                    $pw_shop_order_status = explode(",", $shop_order_status);
                } else {
                    $pw_shop_order_status = array();
                }

                $end_date   = date('Y-m-d', $timestamp);
                $start_date = $this->pw_order_first_date();
                $title      = __("Till Date", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_special_email_date($start_date, $end_date, $title, $pw_hide_os,
                    $pw_shop_order_status);
                $report[]   = $title;
            endif;

            //CUSTOM WORK - 4061
            if ($email_product_by_customer == 1):

                $end_date   = date('Y-m-t', strtotime('last month', $timestamp));
                $start_date = date('Y-m-01', strtotime('last month', $timestamp));
                $title      = __("Purchased Product by Customer - Last Month", __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                $email_data .= "<br>";
                $email_data .= $this->pw_fetch_special_email_date_purchase_buy_customer($start_date, $end_date, $title,
                    $pw_hide_os, $pw_shop_order_status);
                $report[]   = $title;
            endif;

            if (
                $email_daily_report == 1
                || $email_weekly_report == 1
                || $email_monthly_report == 1
                || $email_till_today_report == 1
                || $email_yesterday_report == 1
                || $email_last_week_report == 1
                || $email_last_month_report == 1
                || $email_this_year_report == 1
                || $email_last_year_report == 1
                || $act_email_reporting == 1
                || $email_total_summary == 1

            ):
                if (strlen($email_data) > 0) {

                    //$this->set_error_log('called funtion ic_woo_schedule_send_email, copleted html data');

                    $new = '<html>';
                    $new .= '<head>';
                    $new .= '<title>';
                    $new .= $title;
                    $new .= '</title>';
                    $new .= '</head>';
                    $new .= '<body>';
                    //$new .= $this->display_logo();
                    $new        .= $email_data;
                    $new        .= '</body>';
                    $new        .= '</html>';
                    $email_data = $new;

                    $email_send_to    = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'sendto_email', '');
                    $email_from_name  = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'from_name', '');
                    $email_from_email = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'sendfrom_email', '');
                    $email_subject    = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'subject_email', '');
                    $email_optimize   = $this->get_options(__PW_REPORT_WCREPORT_FIELDS_PERFIX__ . 'optimize_email', '');

                    $email_send_to    = $this->reformat_email_text($email_send_to);
                    $email_from_email = $this->reformat_email_text($email_from_email);
                    if ($email_send_to || $email_from_email) {

                        //$subject = $email_subject.'-'.implode(", ",$report)." Report";
                        $subject = $email_subject;

                        $headers = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                        if ($email_optimize) {
                            $headers .= 'From: ' . $email_from_name . ' <' . $email_from_email . '>' . "\r\n";
                        } else {
                            $headers .= "From: =?UTF-8?B?" . base64_encode($email_from_name) . "?= <" . $email_from_email . ">" . "\r\n";
                            $headers .= 'Content-Transfer-Encoding: 8bit';
                        }


                        $email_data = str_replace("! ", "", $email_data);
                        $email_data = str_replace("!", "", $email_data);

                        $date_format     = get_option('date_format', "Y-m-d");
                        $time_format     = get_option('time_format', 'g:i a');
                        $reporte_created = date_i18n($date_format . " " . $time_format);

                        $siteurl    = get_option('siteurl');
                        $email_data = $email_data . "<div style=\" padding-bottom:3px; width:520px; margin:auto; text-align:left;\"><strong>" . __("Created Date/Time:",
                                __PW_REPORT_WCREPORT_TEXTDOMAIN__) . " " . "</strong> {$reporte_created}</div>";

                        $message = $email_data;
                        $to      = $email_send_to;


                        update_option("email_message", $message);
                        //return 'OKa';

                        //return $message;

                        //$result = wp_mail( $to, $subject, $message, $headers);
                        $result = wp_mail($to, "=?UTF-8?B?" . base64_encode($subject) . "?=", $message, $headers);

                        return $result;
                    }

                }
            endif;

            return '';
        }

        function pw_email_table_row_html($title, $amount, $count, $price_type = 'price')
        {
            if ($price_type == 'price') {
                $amount = $this->price($amount);
            }

            return '
			<tr>
	            <td style="padding: 10px; background-color: #f2f2f2; color: #696969; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;font-weight: bold;">
	                ' . $title . '
	            </td>
	            <td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">
	            	' . $count . '    
	            </td>
	            <td style="padding: 10px; background-color: #f2f2f2; color: #909090; font-size: 11px;     text-transform: capitalize; border-bottom: 1px solid #ddd;">
	                ' . $amount . '
	            </td>
	        </tr>';
        }

        function pw_fetch_general_email_data($start_date, $end_date, $title = "Daily", $post_status, $shop_order_status)
        {
            $body = '';
            include("includes/fetch_data_dailymail_status.php");

            return $message = $body;
        }

        function pw_fetch_special_email_date(
            $pw_from_date,
            $pw_to_date,
            $title = "Daily",
            $pw_hide_os,
            $pw_shop_order_status
        ) {
            $body = '';
            include("includes/fetch_data_dailymail.php");

            return $body;
        }

        //CUSTOM WORK - 4061
        function pw_fetch_special_email_date_purchase_buy_customer(
            $start_date,
            $end_date,
            $title = "Daily",
            $post_status,
            $shop_order_status
        ) {
            $body = '';
            include("includes/fetch_data_dailymail_purchase_by_customer.php");

            return $body;
        }

        //////////////END SEND EMAIL SCHEDULE////////////


        function get_options($field, $default)
        {
            $value = get_option($field, $default);

            if ($value == 'on') {
                $value = 1;
            }
            if ($value == 'off') {
                $value = 0;
            }

            return $value;
        }

        function validate_email($check)
        {
            $expression = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/";
            if (preg_match($expression, $check)) {
                return true;
            } else {
                return false;
            }
        }

        function reformat_email_text($emails)
        {
            $emails = str_replace("|", ",", $emails);
            $emails = str_replace(";", ",", $emails);
            $emails = explode(",", $emails);

            $newemail = array();
            foreach ($emails as $key => $value):
                $e = trim($value);
                if ($this->validate_email($e)) {
                    $newemail[] = $e;
                }
            endforeach;

            if (count($newemail) > 0) {
                $newemail = array_unique($newemail);

                return implode(",", $newemail);
            } else {
                return false;
            }
        }

        function startWeek()
        {
            $start_of_week = get_option('start_of_week', 0);
            $week_days     = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday");
            $day_name      = isset($week_days[$start_of_week]) ? $week_days[$start_of_week] : "sunday";

            return $day_name;
        }

        function pw_today_total_customer()
        {
            global $wpdb, $sql, $Limit;
            $TodayDate  = $this->today;
            $user_query = new WP_User_Query(array('role' => 'Customer'));
            $users      = $user_query->get_results();
            $user2      = array();
            if ( ! empty($users)) {
                foreach ($users as $user) {
                    $strtotime       = strtotime($user->user_registered);
                    $user_registered = date("Y-m-d", $strtotime);
                    if ($user_registered == $TodayDate) {
                        $user2[] = $user->ID;
                    }
                }

                return count($user2);
            }

            return count($user2);
        }

        function pw_order_first_date($key = null)
        {
            global $wpdb;
            if ($this->pw_firstorderdate) {
                return $this->pw_firstorderdate;
            } else {
                $sql = "SELECT DATE_FORMAT(posts.post_date, '%Y-%m-%d') AS 'OrderDate' FROM {$wpdb->prefix}posts  AS posts	WHERE posts.post_type='shop_order' Order By posts.post_date ASC LIMIT 1";

                return $this->pw_firstorderdate = $wpdb->get_var($sql);
            }
        }

        function pw_intelligence_product_images($title = 'No Title', $id, $url = false)
        {
            $first_letter = strtolower($title[0]);

            //IF PRODUCT NO TITLE or START WITH NO ALPHABETIC CHARACTER
            if ( ! preg_match("/^[a-z]$/", $first_letter)) {
                $first_letter = 'other';
            }

            $image_num = $id % 2;

            //$rand_img=rand(1,5);
            if ($url) {
                $img = __PW_REPORT_WCREPORT_URL__ . '/assets/images/products/' . $first_letter . '/' . $image_num . '.jpg';
            } else {
                $img = '<img src="' . __PW_REPORT_WCREPORT_URL__ . '/assets/images/products/' . $first_letter . '/' . $image_num . '.jpg">';
            }

            return $img;
        }

        function pw_get_ip_address()
        {

            if (isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
            }

            return $ip;
        }

        function pw_cron_event_schedule()
        {
            $this->datetime = date_i18n("Y-m-d H:i:s");
            $args           = array(
                'parent_plugin'  => "WooCommerce",
                'report_plugin'  => __PW_REPORT_WCREPORT_FIELDS_PERFIX__ . '_' . '20150522',
                'site_name'      => get_option('blogname', ''),
                'home_url'       => esc_url(home_url()),
                'site_date'      => $this->datetime,
                'ip_address'     => $this->pw_get_ip_address(),
                'remote_address' => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0'
            );
            $url            = 'h' . 't' . 't' . 'p' . ':' . '/' . '/' . 'p' . 'l' . 'u' . 'g' . 'i' . 'n' . 's.' . 'i' . 'n' . 'f' . 'o' . 's' . 'o' . 'f' . 't' . 't' . 'e' . 'c' . 'h' . '.c' . 'o' . 'm' . '/' . 'w' . 'p' . '-' . 'a' . 'p' . 'i' . '/' . 'p' . 'l' . 'u' . 'g' . 'i' . 'n' . 's' . '.' . 'p' . 'h' . 'p';
            $request        = wp_remote_post($url, array(
                'method'      => 'POST',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => $args,
                'cookies'     => array(),
                'sslverify'   => false
            ));
        }


        //CUSTOMER ID
        public function pw_dropdown_users($args = '')
        {
            $defaults = array(
                'show_option_all'         => '',
                'show_option_none'        => '',
                'hide_if_only_one_author' => '',
                'orderby'                 => 'display_name',
                'order'                   => 'ASC',
                'include'                 => '',
                'exclude'                 => '',
                'multi'                   => 0,
                'show'                    => 'display_name',
                'echo'                    => 1,
                'selected'                => 0,
                'name'                    => 'user',
                'class'                   => '',
                'id'                      => '',
                'blog_id'                 => $GLOBALS['blog_id'],
                'who'                     => '',
                'include_selected'        => false
            );

            $r = wp_parse_args($args, $defaults);
            extract($r, EXTR_SKIP);

            $query_args           = wp_array_slice_assoc($r,
                array('blog_id', 'include', 'exclude', 'orderby', 'order', 'who'));
            $query_args['fields'] = array('ID', 'display_name', 'user_login');
            $users                = get_users($query_args);

            $output = '';

            foreach ((array)$users as $user) {
                $user->ID = (int)$user->ID;

                $author = '';
                if ($user->ID == 0) {
                    $author = __('Guest', __PW_REPORT_WCREPORT_TEXTDOMAIN__);
                } else {
                    $author = get_user_meta($user->ID, 'billing_first_name', true) . ' ' . get_user_meta($user->ID,
                            'billing_last_name', true);
                }

                if ($user->display_name != '') {
                    $full_name = $user->display_name;
                } else {
                    $full_name = $user->user_login;
                }

                if ($full_name != '') {

                    $display = $full_name != ' ' ? $full_name : '(' . $user->user_login . ')';
                    $output  .= "\t<option value='$user->ID'>" . esc_html($author) . "</option>\n";
                }
            }

            $output .= "\t<option value='0'>Guest</option>\n";

            $output .= "</select>";

            $output = apply_filters('wp_dropdown_users', $output);
//
//			if ($echo) {
//				echo $output;
//			}

            return $output;
        }

    }

    $GLOBALS['pw_rpt_main_class'] = new pw_report_wcreport_class;

    //ABANDONED CART
    //include(plugin_dir_path(__FILE__)."/includes/Abandoned/woocommerce-cart-reports.php");


    function pw_add_custom_sku()
    {

        $args = array(

            'label' => __('Custom SKU', 'woocommerce'),

            'placeholder' => __('Enter custom SKU here', 'woocommerce'),

            'id' => 'jk_sku',

            'desc_tip' => true,

            'description' => __('This SKU is for internal use only.', 'woocommerce'),

        );

        woocommerce_wp_text_input($args);

    }

    add_action('woocommerce_product_options_sku', 'pw_add_custom_sku');

    function pw_save_custom_sku($post_id)
    {

// grab the custom SKU from $_POST

        $custom_sku = isset($_POST['jk_sku']) ? sanitize_text_field($_POST['jk_sku']) : ”;

// grab the product

        $product = wc_get_product($post_id);

// save the custom SKU using WooCommerce built-in functions

        $product->update_meta_data('jk_sku', $custom_sku);

        $product->save();

    }

    add_action('woocommerce_process_product_meta', 'pw_save_custom_sku');


    add_action('woocommerce_variation_options_pricing', 'pw_add_custom_field_to_variations', 10, 3);


    function pw_add_custom_field_to_variations($loop, $variation_data, $variation)
    {

        woocommerce_wp_text_input(array(

                'id' => 'custom_field[' . $loop . ']',

                'class' => 'short',

                'label' => __('Custom Field', 'woocommerce'),

                'value' => get_post_meta($variation->ID, 'custom_field', true)

            )

        );

    }

    add_action('woocommerce_save_product_variation', 'pw_save_custom_field_variations', 10, 2);


    function pw_save_custom_field_variations($variation_id, $i)
    {

        $custom_field = $_POST['custom_field'][$i];

        if (isset($custom_field)) {
            update_post_meta($variation_id, 'custom_field', esc_attr($custom_field));
        }

    }

    add_filter('woocommerce_available_variation', 'pw_add_custom_field_variation_data');


    function pw_add_custom_field_variation_data($variations)
    {

        $variations['custom_field'] = '<div class="woocommerce_custom_field">Custom Field: <span>' . get_post_meta($variations['variation_id'],
                'custom_field', true) . '</span></div>';

        return $variations;

    }

}
?>