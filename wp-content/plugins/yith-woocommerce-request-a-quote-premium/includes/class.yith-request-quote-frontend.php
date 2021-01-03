<?php

/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!defined('ABSPATH') || !defined('YITH_YWRAQ_VERSION')) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Frontend class.
 *
 * @class   YITH_YWRAQ_Frontend
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if (!class_exists('YITH_YWRAQ_Frontend')) {

	/**
	 * Class YITH_YWRAQ_Frontend
	 */
	class YITH_YWRAQ_Frontend
	{

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWRAQ_Frontend
		 */
		protected static $instance;

		public $shortcodes;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_YWRAQ_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance()
		{
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		public function __construct()
		{

			add_action('wp_loaded', array($this, 'update_raq_list'), 35);

			//custom styles and javascripts
			add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_scripts'), 20);

			//show button in single page
			add_action('woocommerce_before_single_product', array($this, 'show_button_single_page'));

			//show request a quote button.
			add_filter('yith_ywraq-show_btn_single_page', 'yith_ywraq_show_button_in_single_page');
			add_filter('yith_ywraq-btn_other_pages', 'yith_ywraq_show_button_in_other_pages', 10);

			add_filter('woocommerce_loop_add_to_cart_link', array($this, 'hide_add_to_cart_loop'), 99, 2);

			$this->shortcodes = new YITH_YWRAQ_Shortcodes();

			//quote button to checkout
			if ('yes' == get_option('ywraq_show_button_on_checkout_page', 'no')) {
				add_action('woocommerce_review_order_before_submit', array($this, 'show_button_on_checkout'));
				//add the gateway
				require_once(YITH_YWRAQ_INC . 'class.yith-request-quote-gateway.php');
				add_filter('woocommerce_payment_gateways', array($this, 'add_ywraq_gateway'));
			}
		}

		/**
		 * Show the Request a quote button on checkout page.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function show_button_on_checkout()
		{

			$order_payment = WC()->session->get('order_awaiting_payment');

			if ($order_payment ||  !YITH_Request_Quote_Premium()->check_user_type()) {
				return;
			}

			echo '<input type="hidden" id="ywraq_checkout_quote" name="ywraq_checkout_quote" value="" />';
			$label_button = get_option('ywraq_checkout_quote_button_label', __('Request a Quote', 'yith-woocommerce-request-a-quote'));
			echo apply_filters('ywraq_quote_button_checkout_html', '<button type="submit" class="button alt" id="ywraq_checkout_quote" value="' . esc_attr($label_button) . '" data-value="' . esc_attr($label_button) . '">' . esc_html($label_button) . '</button>');
		}


		/**
		 * @author Emanuela Castorina
		 */
		public function show_button_single_page()
		{
			global $product;

			if (!$product) {
				global $post;
				if (!$post || !is_object($post) || !is_singular()) {
					return;
				}
				$product = wc_get_product($post->ID);

				if (!$product) {
					return;
				}
			}

			$show_button_near_add_to_cart = get_option('ywraq_show_button_near_add_to_cart', 'no');

			if (yith_plugin_fw_is_true($show_button_near_add_to_cart) && $product->is_in_stock() && $product->get_price() !== '') {
				if ($product->is_type('variable')) {
					add_action('woocommerce_after_single_variation', array($this, 'add_button_single_page'), 15);
				} else {
					add_action('woocommerce_after_add_to_cart_button', array($this, 'add_button_single_page'), 15);
				}
			} else {
				add_action('woocommerce_single_product_summary', array($this, 'add_button_single_page'), 35);
				add_action('yith_wcqv_product_summary', array($this, 'add_button_single_page'), 27);
			}
		}

		/**
		 * Hide add to cart in single page
		 *
		 * Hide the button add to cart in the single product page
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		public function hide_add_to_cart_single()
		{

			if (catalog_mode_plugin_enabled()) {
				return;
			}

			global $post;

			if (!$post || !is_object($post) || !is_singular()) {
				return;
			}

			$product = wc_get_product($post->ID);
			if (!$product || apply_filters('ywraq_hide_add_to_cart_single', false, $product)) {
				return;
			}
			if (get_option('ywraq_hide_add_to_cart') == 'yes' || ($product->get_price() == '' && $product->get_type() != 'external')) {
				if (isset($product) && $product && $product->is_type('variable')) {
					$css = ".single_variation_wrap .variations_button button{
	                 display:none!important;
	                }";
				} elseif (!$product->is_type('gift-card')) {
					$css = ".cart button.single_add_to_cart_button, .cart a.single_add_to_cart_button{
	                 display:none!important;
	                }";
				}
				wp_add_inline_style('yith_ywraq_frontend', apply_filters('yith_ywrad_hide_cart_single_css', $css));
			}
		}

		/**
		 * Hide add to cart in loop
		 *
		 * Hide the button add to cart in the shop page
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 *
		 * @param            $link
		 * @param WC_Product $product
		 *
		 * @return string
		 */
		public function hide_add_to_cart_loop($link, $product)
		{

			if (!catalog_mode_plugin_enabled() && get_option('ywraq_hide_add_to_cart') == 'yes') {

				if (!$product->is_type(array('external', 'grouped', 'variable'))) {
					if (apply_filters('hide_add_to_cart_loop', true, $link, $product)) {
						$link = '';
					}
				}
			}

			return $link;
		}

		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_styles_scripts()
		{

			global $post;

			$raq_page_id = YITH_Request_Quote()->get_raq_page_id();
			$suffix      = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

			//Styles and scripts in request a quote page
			if ($post && $post->ID == $raq_page_id) {
				//if the registration user is requested
				if (get_option('ywraq_add_user_registration_check') == 'yes' && get_option('woocommerce_registration_generate_password') != 'yes') {
					wp_enqueue_script('ywraq-password-strength', YITH_YWRAQ_ASSETS_URL . '/js/frontend-password' . $suffix . '.js', array('jquery', 'password-strength-meter'), YITH_YWRAQ_VERSION, true);
					wp_localize_script('ywraq-password-strength', 'ywraq_pwd', array(
						'min_password_strength' => apply_filters('woocommerce_min_password_strength', 3),
						'i18n_password_error'   => esc_attr__('Please enter a stronger password.', 'woocommerce'),
						'i18n_password_hint'    => esc_attr(wp_get_password_hint()),
					));
				}
			}

			wp_register_script('yith_ywraq_frontend', YITH_YWRAQ_ASSETS_URL . '/js/frontend' . $suffix . '.js', array('jquery'), YITH_YWRAQ_VERSION, true);

			$localize_script_args = array(
				'ajaxurl'                 => WC_AJAX::get_endpoint("%%endpoint%%"),
				'current_lang'            => ywraq_get_current_language(),
				'no_product_in_list'      => ywraq_get_list_empty_message(),
				'block_loader'            => YITH_YWRAQ_ASSETS_URL . '/images/ajax-loader.gif',
				'go_to_the_list'          => (get_option('ywraq_after_click_action') == 'yes') ? 'yes' : 'no',
				'rqa_url'                 => YITH_Request_Quote()->get_redirect_page_url(),
				'current_user_id'         => is_user_logged_in() ? get_current_user_id() : '',
				'hide_price'              => get_option('ywraq_hide_price') == 'yes' ? 1 : 0,
				'allow_out_of_stock'      => get_option('ywraq_allow_raq_out_of_stock'),
				'allow_only_on_out_of_stock' => get_option('ywraq_show_btn_only_out_of_stock'),
				'select_quantity'         => apply_filters('yith_ywraq_select_quantity_grouped_label', __('Set at least the quantity for a product', 'yith-woocommerce-request-a-quote')),
				'i18n_choose_a_variation' => esc_attr__('Please select some product options before adding this product to your quote list.', 'yith-woocommerce-request-a-quote'),
				'i18n_out_of_stock'       => apply_filters('yith_ywraq_variation_outofstock_label', esc_attr__('This Variation is Out of Stock, please select another one.', 'yith-woocommerce-request-a-quote')),
				'raq_table_refresh_check' => apply_filters('yith_ywraq_table_refresh_check', true),
				'auto_update_cart_on_quantity_change' => apply_filters('yith_ywraq_auto_update_cart_on_quantity_change', true)

			);

			wp_localize_script('yith_ywraq_frontend', 'ywraq_frontend', apply_filters('yith_ywraq_frontend_localize', $localize_script_args));
			wp_enqueue_style('yith_ywraq_frontend', YITH_YWRAQ_ASSETS_URL . '/css/ywraq-frontend.css');
			wp_enqueue_script('yith_ywraq_frontend');

			if (defined('YITH_YWRAQ_PREMIUM')) {
				$custom_css = require_once(YITH_YWRAQ_TEMPLATE_PATH . '/layout/css.php');
				wp_add_inline_style('yith_ywraq_frontend', $custom_css);
			}

			if (function_exists('Woo_Bulk_Discount_Plugin_t4m')) {
				remove_filter('woocommerce_cart_product_subtotal', array(Woo_Bulk_Discount_Plugin_t4m(), 'filter_cart_product_subtotal'), 10, 3);
			}

			$this->hide_add_to_cart_single();
		}

		/**
		 * Check if the button can be showed in single page
		 *
		 * @return void
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function add_button_single_page()
		{

			$show_button = apply_filters('yith_ywraq-show_btn_single_page', true);
			if (yith_plugin_fw_is_true($show_button)) {
				$this->print_button();
			}
		}

		/**
		 * @param bool $product_id
		 *
		 * @internal param bool $product
		 */
		public function print_button($product_id = false)
		{

			if (!$product_id) {
				global $product;
			} else {
				$product = wc_get_product($product_id);
			}

			if (!apply_filters('yith_ywraq_before_print_button', true, $product)) {
				return;
			}

			$style_button = (get_option('ywraq_show_btn_link') == 'button') ? 'button' : 'ywraq-link';
			$product_id   = yit_get_prop($product, 'id', true);

			$args = array(
				'class'         => 'add-request-quote-button ' . $style_button,
				'wpnonce'       => wp_create_nonce('add-request-quote-' . $product_id),
				'product_id'    => $product_id,
				'label'         => ywraq_get_label('btn_link_text'),
				'label_browse'  => ywraq_get_label('browse_list'),
				'template_part' => 'button',
				'rqa_url'       => YITH_Request_Quote()->get_raq_page_url(),
				'exists'        => ($product->is_type('variable')) ? false : YITH_Request_Quote()->exists($product_id),
			);

			if ($product->is_type('variable')) {
				$args['variations'] = implode(',', YITH_Request_Quote()->raq_variations);
			}

			$args['args'] = $args;

			$template_button = 'add-to-quote.php';

			if (class_exists('YITH_WAPO_Type') && !is_product()) {

				$has_addons = YITH_WAPO_Type::getAllowedGroupTypes($product_id);

				if (!empty($has_addons)) {
					$template_button = 'add-to-quote-addons.php';
				}
			}

			if ($product->is_type('yith-composite') && !is_product()) {
				$template_button = 'add-to-quote-addons.php';
			}

			wc_get_template($template_button, apply_filters('ywraq_add_to_quote_args', $args), '', YITH_YWRAQ_TEMPLATE_PATH . '/');
		}

		/**
		 * Update the Request Quote List
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function update_raq_list()
		{

			if (isset($_POST['update_raq_wpnonce']) && isset($_POST['raq']) && wp_verify_nonce($_POST['update_raq_wpnonce'], 'update-request-quote-quantity')) {

				foreach ($_POST['raq'] as $key => $value) {

					if ($value['qty'] != 0) {

						YITH_Request_Quote()->update_item($key, 'quantity', $value['qty']);
					} else {
						YITH_Request_Quote()->remove_item($key);
					}
				}
			}
		}

		/**
		 * Add the gateway to WC Available Gateways
		 *
		 * @param array $gateways all available WC gateways
		 *
		 * @return array $gateways all WC gateways + offline gateway
		 * @since 2.1.9
		 */
		public function add_ywraq_gateway($gateways)
		{
			$gateways[] = 'YITH_YWRAQ_Gateway';

			return $gateways;
		}
	}

	/**
	 * Unique access to instance of YITH_YWRAQ_Frontend class
	 *
	 * @return \YITH_YWRAQ_Frontend
	 */
	function YITH_YWRAQ_Frontend()
	{
		return YITH_YWRAQ_Frontend::get_instance();
	}
}
