<?php
/**
Plugin Name: WC Pickup Store
Plugin URI: https://www.keylormendoza.com/plugins/wc-pickup-store/
Description: Lets you to set up a custom post type for stores available to use it as shipping method Local pickup in WooCommerce. It allows your clients to choose an store on the Checkout page and also adds the store fields to the order details and email.
Version: 1.6.0
Requires at least: 4.7
Tested up to: 5.5.3
WC requires at least: 3.0
WC tested up to: 4.7.1
Author: Keylor Mendoza A.
Author URI: https://www.keylormendoza.com
License: GPLv2
Text Domain: wc-pickup-store
*/

if (!defined('ABSPATH')) { exit; }

if (!defined('WPS_PLUGIN_FILE')) {
	define('WPS_PLUGIN_FILE', plugin_basename(__FILE__));
}

if (!defined('WPS_PLUGIN_VERSION')) {
	define('WPS_PLUGIN_VERSION', '1.6.0');
}

/**
** Check if WooCommerce is active
**/
if ( !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
	add_action('admin_notices', 'wps_store_inactive_notice');
	return;
}

function wps_store_inactive_notice() {
	if ( current_user_can( 'activate_plugins' ) ) :
		if ( !class_exists( 'WooCommerce' ) ) :
			?>
			<div id="message" class="error">
				<p>
					<?php
					printf(
						__('%1$s requires %2$sWooCommerce%3$s to be active.', 'wc-pickup-store'),
						'<strong>WC Pickup Store</strong>',
						'<a href="http://wordpress.org/plugins/woocommerce/" target="_blank" >',
						'</a>'
					);
					?>
				</p>
			</div>		
			<?php
		endif;
	endif;
}

/**
** Update stores Country
**/
function wps_store_update_default_country() {
	if (version_compare(WPS_PLUGIN_VERSION, '1.5.24') >= 0) {
		if (!get_option('wps_countries_updated')) {
			?>
			<div id="message" class="notice notice-error">
				<p><?php
					$id = "wc_pickup_store";
					$update_url = sprintf(admin_url('admin.php?page=wc-settings&tab=shipping&section=%s&update_country=1'), $id);
					printf(
						__('Since version %1$s, a new Country validation was added to %2$s. Please, update stores without country to the default %3$s %4$shere%5$s.', 'wc-pickup-store'),
						'<strong>1.5.24</strong>',
						'<strong>WC Pickup Store</strong>',
						'<strong>' . wps_get_wc_default_country() . '</strong>',
						'<a href="' . $update_url . '" >',
						'</a>'
					);
					?></p>
			</div>
			<?php
		} 

		if (get_option('wps_countries_updated') && isset($_GET['post_type']) && $_GET['post_type'] == 'store') {
			?>
			<div id="message" class="notice notice-info is-dismissible">
				<p><?php
					printf(
						__('Since version %1$s, a new Country validation was added to %2$s and all stores have been updated.', 'wc-pickup-store'),
						'<strong>1.5.24</strong>',
						'<strong>WC Pickup Store</strong>'
					);
				?></p>
			</div>
			<?php
		}
	}
}
add_action('admin_notices', 'wps_store_update_default_country');

/**
** Plugin files
**/
include plugin_dir_path(__FILE__) . '/includes/wps-init.php';
include plugin_dir_path(__FILE__) . '/includes/wps-admin.php';
include plugin_dir_path(__FILE__) . '/includes/wps-functions.php';
include plugin_dir_path(__FILE__) . '/includes/widget-stores.php';
include plugin_dir_path(__FILE__) . '/includes/post_type-store.php';
include plugin_dir_path(__FILE__) . '/includes/vc_stores.php';

