<?php
/**
** Add shipping method to WC
**/
function wps_store_shipping_method( $methods ) {
	$methods['wc_pickup_store'] = 'WC_PICKUP_STORE';

	return $methods;
}
add_filter('woocommerce_shipping_methods', 'wps_store_shipping_method');

/**
** Declare Shipping Method
**/
function wps_store_shipping_method_init() {
	if (class_exists('WC_Shipping_Method')) {
		class WC_PICKUP_STORE extends WC_Shipping_Method {
			/**
			 * Constructor for your shipping class
			 *
			 * @access public
			 * @return void
			 */
			public function __construct() {
				$this->id = 'wc_pickup_store';
				$this->method_title = __('WC Pickup Store');
				$this->method_description = __('Lets users to choose a store to pick up their products', 'wc-pickup-store');
	
				$this->init();
				// $this->includes();
			}
	
			// public function includes() {}
	
			/**
			 * Init your settings
			 *
			 * @access public
			 * @return void
			 */
			function init() {
				// Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
	
				// Turn these settings into variables we can use
				foreach ( $this->settings as $setting_key => $value ) {
					$this->$setting_key = apply_filters('wps_settings_data', $value, $setting_key, $this->settings);
				}
	
				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				add_filter('woocommerce_get_order_item_totals', array($this, 'wc_reordering_order_item_totals'), 10, 3);
			}
	
			public function init_form_fields() {
				$this->form_fields = array(
					'enabled' => array(
						'title' => __( 'Enable/Disable', 'woocommerce' ),
						'type' => 'checkbox',
						'label' => __( 'Enable', 'woocommerce' ),
						'default'  => 'yes',
						'description' => __( 'Enable/Disable shipping method', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'enable_store_select' => array(
						'title' => __( 'Enable stores in checkout', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Enable', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'Shows select field to pick a store in checkout', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'title' => array(
						'title' => __( 'Shipping Method Title', 'wc-pickup-store' ),
						'type' => 'text',
						'description' => __( 'Label that appears in checkout options', 'wc-pickup-store' ),
						'default' => __( 'Pickup Store', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'costs_type' => array(
						'title' => __( 'Shipping Costs Type', 'wc-pickup-store' ),
						'type' => 'select',
						'class' => 'wc-enhanced-select',
						'description' => __( 'Choose a shipping costs type to calculate Pick up store costs. Use None to deactivate shipping store costs', 'wc-pickup-store' ),
						'default' => 'flat',
						'options' => array(
							'none' => __('None', 'wc-pickup-store'),
							'flat' => __('Flat Rate', 'wc-pickup-store'),
							'percentage' => __('Percentage', 'wc-pickup-store')
						),
						'desc_tip'    => true
					),
					'costs' => array(
						'title' => __( 'Shipping Costs', 'wc-pickup-store' ),
						'type' => 'text',
						'description' => __( 'Adds main shipping cost to store pickup', 'wc-pickup-store' ),
						'default' => 0,
						'placeholder' => '0',					
						'desc_tip'    => true
					),
					'costs_per_store' => array(
						'title' => __( 'Enable costs per store', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Enable', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'Allows to add shipping costs by store that will override the main shipping cost.', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'stores_order_by' => array(
						'title' => __( 'Order Stores by', 'wc-pickup-store' ),
						'type' => 'select',
						'class' => 'wc-enhanced-select',
						'description' => __( 'Choose what order the stores will be shown', 'wc-pickup-store' ),
						'default' => 'title',
						'options' => array(
							'title' => 'Title',
							'date' => 'Date',
							'ID' => 'ID',
							'rand' => 'Random'
						),
						'desc_tip'    => true
					),
					'stores_order' => array(
						'title' => __( 'Order', 'wc-pickup-store' ),
						'type' => 'select',
						'class' => 'wc-enhanced-select',
						'description' => __( 'Choose what order the stores will be shown', 'wc-pickup-store' ),
						'default' => 'DESC',
						'options' => array(
							'DESC' => 'DESC',
							'ASC' => 'ASC'
						),
						'desc_tip'    => true
					),
					'store_default' => array(
						'type' => 'store_default',
						'description' => __( 'Choose a default store to Checkout', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'checkout_notification' => array(
						'title' => __( 'Checkout notification', 'wc-pickup-store' ),
						'type' => 'textarea',
						'description' => __( 'Message that appears next to shipping options on the Checkout page', 'wc-pickup-store' ),
						'default' => __( '', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'hide_store_details' => array(
						'title' => __( 'Hide store details on Checkout', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Hide', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'Hide selected store details on the Checkout page.', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'country_filtering' => array(
						'title' => __( 'Disable store filtering by Country', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Disable', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'By default, stores will be filtered by country on the Checkout.', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'external_bootstrap' => array(
						'title' => __( 'Disable Bootstrap', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Disable', 'woocommerce' ),
						'default'  => 'no',
						'description' => sprintf(__( 'Disable external Bootstrap library.  Current version %s', 'wc-pickup-store' ), '3.3.7.'),
						'desc_tip'    => true
					),
					'external_font_awesome' => array(
						'title' => __( 'Disable Font Awesome', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Disable', 'woocommerce' ),
						'default'  => 'no',
						'description' => sprintf(__( 'Disable external Font Awesome library.  Current version %s', 'wc-pickup-store' ), '4.7.0.'),
						'desc_tip'    => true
					),
					'local_css' => array(
						'title' => __( 'Disable local css', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Disable', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'Disable WC Pickup Store css library.', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'disable_select2' => array(
						'title' => __( 'Disable select2 on Checkout', 'wc-pickup-store' ),
						'type' => 'checkbox',
						'label' => __( 'Disable', 'woocommerce' ),
						'default'  => 'no',
						'description' => __( 'Disable select2 library for stores dropdown on Checkout page.', 'wc-pickup-store' ),
						'desc_tip'    => true
					),
					'plugin_version' => array(
						'type' => 'plugin_version',
					),
				);
			}
	
			public function is_available( $package ) {
				$is_available = ($this->enabled == 'yes') ? true : false;
	
				return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
			}
	
			/**
			 * calculate_shipping function.
			 *
			 * @access public
			 * @param mixed $package
			 * @return void
			 */
			public function calculate_shipping( $package = array() ) {
				$formatted_title = (!empty($this->costs) && $this->costs_per_store != 'yes') ? $this->title . ': ' . wc_price($this->wps_get_calculated_costs($this->costs, true)) : $this->title;
				$rate = array(
					'id' => $this->id,
					'label' => apply_filters('wps_formatted_shipping_title', $formatted_title, $this->title),
					'cost' => apply_filters('wps_shipping_costs', $this->wps_get_calculated_costs($this->costs, true)),
					'package' => $package,
					'calc_tax' => 'per_order' // 'per_item'
				);
	
				// Register the rate
				$this->add_rate( $rate );
			}
	
			public function generate_store_default_html() {
				ob_start();
				?>
				<tr valign="top">
					<th scope="row" class="titledesc"><?php _e('Default store', 'wc-pickup-store'); ?>:</th>
					<td class="forminp">
						<p><?php
							echo sprintf(__('Find this option in <a href="%s" target="_blank">the Customizer</a>', 'wc-pickup-store'), admin_url('/customize.php?autofocus[section]=wps_store_customize_section'));
						?></p>
					</td>
				</tr>
				<?php
				return ob_get_clean();
			}
	
			public function generate_plugin_version_html() {
				ob_start();
				?>
				<tr valign="top">
					<td colspan="2" align="right">
						<p><em><?php echo sprintf(__('Version %s', $this->id), WPS_PLUGIN_VERSION); ?></em></p>
					</td>
				</tr>
				<?php
				return ob_get_clean();
			}
	
			public function wc_reordering_order_item_totals($total_rows, $order, $tax_display) {
				/* Update 1.5.9 */
				$order_id = $order->get_id();
				$store = wps_get_post_meta($order_id, '_shipping_pickup_stores');
				$formatted_title = (!empty($this->costs) && $this->costs_per_store != 'yes') ? $this->title . ': ' . wc_price($this->wps_get_calculated_costs($this->costs, true, $order)) : $this->title;
				$item_label[] = __('Pickup Store', 'wc-pickup-store');
				if (!empty($this->checkout_notification))
					$item_label[] = $this->checkout_notification;
	
				if($order->has_shipping_method($this->id) && !empty($store)) {
					foreach ($total_rows as $key => $row) {
						$new_rows[$key] = $row;
						if($key == 'shipping') {
							$new_rows['shipping']['value'] = $formatted_title; // Shipping title
							$new_rows[$this->id] = array(
								'label' => apply_filters('wps_order_shipping_item_label', implode(': ', $item_label), $this->checkout_notification),
								'value' => $store
							);
						}
					}
					$total_rows = $new_rows;
				}
	
				return $total_rows;
			}
	
			/**
			** Get calculated costs based on flat/percentage cost type
			**/
			public function wps_get_calculated_costs($shipping_costs, $costs_on_method = false, $order = null) {
				$store_shipping_cost = (double) (!empty($shipping_costs) && $this->costs_per_store == 'yes') ? $shipping_costs : $this->costs;
				switch ($this->costs_type) {
					case 'flat':
						$costs = (($this->costs_per_store == 'yes' && !$costs_on_method) || ($this->costs_per_store == 'no' && $costs_on_method)) ? $store_shipping_cost : 0;
					break;
					case 'percentage':
						$subtotal = !is_null($order) ? $order->get_subtotal() : WC()->cart->get_subtotal();
						$subtotal = (double) apply_filters('wps_subtotal_for_store_cost', $subtotal);
						$costs = (($this->costs_per_store == 'yes' && !$costs_on_method) || ($this->costs_per_store == 'no' && $costs_on_method)) ? ($subtotal * $store_shipping_cost) / 100 : 0;
					break;
					default:
						$costs = 0;
					break;
				}
	
				return apply_filters('wps_store_calculated_costs', $costs, $this->costs_type);
			}
		}
		new WC_PICKUP_STORE();
	}
}
add_action('init', 'wps_store_shipping_method_init');

/**
** Returns the main instance for WC_PICKUP_STORE class
**/
function wps() {
	return new WC_PICKUP_STORE();
}