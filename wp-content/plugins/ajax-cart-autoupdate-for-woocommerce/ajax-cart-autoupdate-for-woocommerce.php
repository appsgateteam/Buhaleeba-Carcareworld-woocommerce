<?php
/*
 * Plugin Name: Ajax Cart AutoUpdate for WooCommerce
 * Description: Automatically updates cart page and mini cart when quantity is changed. Optionally turns off cart page notices.
 * Version: 1.5.4
 * Author: taisho
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

register_activation_hook( __FILE__, 'acau_activate' );
function acau_activate() { 
	// Prevent plugin activation if the minimum PHP version requirement is not met.
	if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		$msg = '<p><strong>Ajax Cart AutoUpdate for WooCommerce</strong> requires PHP version 5.4 or greater. Your server runs ' . PHP_VERSION . '.</p>';
		wp_die( $msg, 'Plugin Activation Error',  array( 'response' => 200, 'back_link' => TRUE ) );
	}
	// Store time of first plugin activation (add_option does nothing if the option already exists).
	add_option( 'acau_first_activate', time());
}

// Create settings class.
class acau_settings {
	
	private $options;	
	private $settings_page_name;
	private $settings_menu_name;

	public function __construct() {
		
		if ( is_admin() ) {
			
			$this->settings_page_name = 'ajax-cart-autoupdate';
			$this->settings_menu_name = 'Ajax Cart AutoUpdate';
			
			// Initialize and register settings. 
			add_action( 'admin_init', [ $this, 'display_options' ] );
			// Add settings page.
			add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
			// Add settings link to plugins page.
			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_settings_link' ] );
			// Scripts for settings page - color picker & dynamically show / hide based on checked options.
			add_action( 'admin_enqueue_scripts',  [ $this, 'admin_enqueue_scripts' ] );
			// Display a notice encouraging to rate the plugin if not dismissed.
			include_once( 'includes/acau-feedback-notice.php' );
			
		}		
	
		if ( isset( get_option( 'acau_settings' )['acau_custom_spinning_wheel'] )) {
			$wheel_color_arg = ( get_option( 'acau_settings' )['acau_custom_spinning_wheel'] == 1 ) ? null : [ 'class' => 'hidden' ];
		} else {
			$wheel_color_arg = [ 'class' => 'hidden' ];
		}
		
		$this->settings_list = [
			
			'acau_update_delay' 		=> [ 'val' => 1000, 	'title' => __( 'Update delay', 'ajax-cart-autoupdate-for-woocommerce' ), 			'type' => 'number', 	'tab' => 'configuration',
											 'descr' => __( 'Time in miliseconds between the last quantity change and sending a request to update quantity on server (a spinning wheel is displayed when this happens). Allows changing the quantity multiple times with only one server request done in the end. Low values will result in the spinning wheel showing more than once when the changes are slow. High values will make the customers wait and wonder why the cart doesn\'t recalculate. How long the spinning wheel is displayed, is based entirely on the server response time.', 'ajax-cart-autoupdate-for-woocommerce' ) ],
			'acau_positive_qty' 		=> [ 'val' => true, 	'title' => __( 'Cart minimum quantity', 'ajax-cart-autoupdate-for-woocommerce' ), 	'type' => 'checkbox', 	'tab' => 'configuration',
											 'descr' => __( 'Change minimum product quantity on cart page from 0 to 1. If unchecked, 0 quantity is valid and such products are removed on cart update.', 'ajax-cart-autoupdate-for-woocommerce' ) ],
			'acau_cart_notices_off' 	=> [ 'val' => true, 	'title' => __( 'Cart page notices', 'ajax-cart-autoupdate-for-woocommerce' ), 		'type' => 'checkbox', 	'tab' => 'configuration',
											 'descr' => __( 'Turn off notices on cart page, including "Cart updated." and "&lt;Product&gt; removed. Undo?". Empty cart message will still be displayed.', 'ajax-cart-autoupdate-for-woocommerce' ) ],
			'acau_custom_spinning_wheel'=> [ 'val' => false, 	'title' => __( 'Custom spinning wheels', 'ajax-cart-autoupdate-for-woocommerce' ), 	'type' => 'checkbox',	'tab' => 'configuration',
											 'descr' => __( 'Change how the spinning wheels look like. Won\'t work if your theme overrides the default spinning wheels.', 'ajax-cart-autoupdate-for-woocommerce' ) ],
			'acau_spinning_wheel_color' => [ 'val' => '#000000','title' => __( 'Spinning wheel color', 'ajax-cart-autoupdate-for-woocommerce' ), 	'type' => 'color', 		'tab' => 'configuration',
											 'descr' => __( 'You can modify the spinning wheels further with custom CSS for .woocommerce .blockUI.blockOverlay:before,.woocommerce .loader:before', 'ajax-cart-autoupdate-for-woocommerce' ),
											 'arg' => $wheel_color_arg ],											 
		];
	}
		   
	public function display_options() {
		
		$active_tab = 'configuration';
		
		// Option group (section ID), option name (one row in database with an array for all settings), args (sanitized)
		register_setting( 'acau_settings', 'acau_settings', [ $this, 'sanitize' ] );	
		// ID / title / cb / page
        add_settings_section( 'configuration_section', null, [ $this, 'print_section_info' ], $this->settings_page_name );
		
		// Add setting fields from 'settings_list' array based on an active tab.
		$arr = $this->settings_list;
		foreach($arr as $key => $item) {
			if ( $active_tab == $arr[$key]['tab']  ) {
				$args = [ 'name' => $key ];
				if ( isset ( $arr[$key]['arg'] ) ) $args+= $arr[$key]['arg'];
				add_settings_field (
					$key, // ID
					$arr[$key]['title'], // Title
					[ $this, 'acau_print_field' ], // Callback
					$this->settings_page_name, // Page
					$active_tab . '_section', // Section ID
					$args // Optional args	
				);
			}
		}
	}

	public function add_settings_page() {
        // This page will be under "Settings"
        $this->plugin_hook_suffix = add_options_page(
            'Settings Admin', $this->settings_menu_name, 'manage_options', $this->settings_page_name, [ $this, 'create_settings_page' ]
        );
    }
	
	public function add_settings_link( $links ) {
		$links = array_merge( [
			'<a href="' . esc_url( admin_url( '/options-general.php?page=' . $this->settings_page_name ) ) . '">' . __( 'Settings' ) . '</a>'
		], $links );
		return $links;
	}
	
	public function admin_enqueue_scripts ( $page ) {
		if ( $page !== $this->plugin_hook_suffix )
			return;
		$plugin_slug = 'ajax-cart-autoupdate-for-woocommerce';
		$plugin_short_slug = 'ajax-cart-autoupdate';
		wp_enqueue_style( 'wp-color-picker' );	
		wp_enqueue_script( $plugin_short_slug . '-admin', plugins_url() . '/' . $plugin_slug . '/js/' . $plugin_slug . '-admin' . '.js', [ 'wp-color-picker', 'jquery' ], '', true );	
	}
	
	/**
	 * Get the option that is saved or the default.
	 *
	 * @param string $index. The option we want to get.
	 */
	
	public function acau_get_settings( $index = false ) {
		
		$arr = $this->settings_list;
		foreach($arr as $key => $item) {		
			$defaults[$key] = $arr[$key]['val'];
		}
			
		$settings = get_option( 'acau_settings', $defaults );

		if ( $index && isset( $settings[ $index ] ) ) {
			return $settings[ $index ];
		}

		return $settings;
	}

    public function create_settings_page() {
		
		$this->options = $this->acau_get_settings();		
	
        ?>
        <div class="wrap">            		
			<div class="acau_admin_links">
				<a href="https://wordpress.org/support/plugin/ajax-cart-autoupdate-for-woocommerce/" target="_blank"><?php esc_html_e( 'Support & suggestions', 'ajax-cart-autoupdate-for-woocommerce' );?></a>			
				|
				<a href="https://wordpress.org/support/plugin/ajax-cart-autoupdate-for-woocommerce/reviews/?rate=5#new-post" target="_blank"><?php esc_html_e( 'Rate this plugin', 'ajax-cart-autoupdate-for-woocommerce' );?></a>
			</div>
			<h1>Ajax Cart AutoUpdate for WooCommerce</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields 
                settings_fields( 'acau_settings' );
                do_settings_sections( $this->settings_page_name );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
	public function sanitize( $input ) {
		
		$new_input = $this->acau_get_settings();
		$arr = $this->settings_list;	
		
		foreach($arr as $key => $item) {		
			switch ( $arr[$key]['type'] ) {
				case 'checkbox' :
					if( isset( $input[$key] ) ) {
						$new_input[$key] = ( $input[$key] == 1 ? 1 : 0 );
					} else {
						$new_input[$key] = 0;
					}
					break;
				case 'text' :
				case 'color' :
					if( isset( $input[$key] ) )
						$new_input[$key] = sanitize_text_field( $input[$key] );
					break;
				case 'number' :
					if( isset( $input[$key] ) )
						$new_input[$key] = absint( $input[$key] );
					break;					 
			}		
		}
	
        return $new_input;
    }

    /** 
     * Print the Section text
     */
	
    public function print_section_info() {
		return;
        // print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
	 
		public function acau_print_field(array $args) {
		 
		$field = $args['name'];
	
		switch ( $this->settings_list[$field]['type'] ) {
			
			case 'checkbox' :
			
				$fieldset = 
					'<fieldset>
						<label><input id="%1$s" type="checkbox" name="acau_settings[%1$s]" value="1" %2$s />%3$s</label>
					</fieldset>';					
				$fieldset .= isset ( $this->settings_list[$field]['info'] ) ? '<p class="description">%4$s</p>' : '';
				
				$info = isset ( $this->settings_list[$field]['info'] ) ? $this->settings_list[$field]['info'] : '';

				printf (
					$fieldset,
					esc_attr( $field ),
					isset( $this->options[$field] ) && ( 1 == $this->options[$field] )  ? 'checked="checked" ':'',
					$this->settings_list[$field]['descr'],
					$info
				);			
				break;
		
			case 'text' :
			case 'number' :
			
				$fieldset = '<input type="text" id="%1$s" name="acau_settings[%1$s]" value="%2$s" />';
				$fieldset .= isset ( $this->settings_list[$field]['descr'] ) ? '<p class="description">%3$s</p>' : '';				
				$descr = isset ( $this->settings_list[$field]['descr'] ) ? $this->settings_list[$field]['descr'] : '';
			
				printf (
					$fieldset,
					esc_attr( $field ),
					isset( $this->options[$field] ) ? esc_attr( $this->options[$field]) : '',
					$descr
				);			
				break;
				
			case 'color' :
			
				$fieldset = '<input type="text" id="%1$s" name="acau_settings[%1$s]" value="%2$s" class="acau-color-field" data-default-color="#000000"/> ';
				$fieldset .= isset ( $this->settings_list[$field]['descr'] ) ? '<p class="description">%3$s</p>' : '';	
				$descr = isset ( $this->settings_list[$field]['descr'] ) ? $this->settings_list[$field]['descr'] : '';
				
				printf(
					$fieldset,
					esc_attr( $field ),
					isset( $this->options[$field] ) ? esc_attr( $this->options[$field]) : '',
					$descr
				);
		}	
	
	}	
}

$acau_settings_page = new acau_settings();

if( is_admin() ) {
	
	// Change plugin settings page CSS.
	add_action( 'admin_head-settings_page_ajax-cart-autoupdate', 'acau_settings_style' );	
	function acau_settings_style() {
		
		$my_style = "
		input[type=checkbox], input[type=radio] {
			margin: -4px 8px 0 0;
		}
		input, select {
			margin: 1px 1px 1px 0;
		}	
		.form-table th {
			padding: 10px 10px 10px 0;
			width: 170px;
		}
		.form-table td {
			padding: 5px 10px;
		}
		.form-table td p {
			margin-bottom: 6px;
		}
		.wrap h1 {		
			padding: 9px 0;		
		}
		.acau_admin_links {
			float: right;
			margin: 15px 50px 15px 0;
			vertical-align: middle;
		}		
		";
		
		echo '<style>' . acau_minify($my_style) . '</style>';
		
	}

}

// Only if WooCommerce is active (doesn't work for Github installations which have version number in folder name).
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || ( get_site_option('active_sitewide_plugins') && array_key_exists( 'woocommerce/woocommerce.php', get_site_option('active_sitewide_plugins') ) ) ) {
	
	// Remove plugin prefix and dash "_" from argument keys.
	function acau_replaceArrayKeys( $array ) {
		$replacedKeys = str_replace('acau_', null, array_keys( $array ));
		return array_combine( $replacedKeys, $array );
	}
	$args = acau_replaceArrayKeys ( $acau_settings_page->acau_get_settings() );
	
	// Run plugin.
	add_action( 'template_redirect', function() use ( $args ) { ajax_cart_autoupdate( $args ); });
	function ajax_cart_autoupdate( $args ) {
		
		if (! is_cart() ) return; // Only if it's a cart page.
		
		// Enqueue js script inline using wc_enqueue_js.
		add_action( 'template_redirect', function() use ( $args ) { acau_enqueue_script ( $args ); }, 20);
		
		/*don't display default "Update cart" button, its behavior can still be triggered automatically,
		in WooCommerce 3.4.0 input element type was changed to button,
		class .button is present in all versions (at least from 2.6 when AJAX cart appeared in WooCommerce core) 
		change spinning wheel color*/
		add_action( 'wp_head', function() use ( $args ) { acau_apply_styles ( $args ); }, 20);
		
		// Cart page minimum qty = 1 instead of 0.
		if (1 == $args['positive_qty'] ) {				
			add_filter( 'woocommerce_quantity_input_args', 'acau_cart_min_qty', 10, 2 );				
		}
		
		/* Don't display any notices, it includes:
		- "Cart updated."
		- removed cart item notice
		- the one when checkout session expires and cart items disappear (it duplicates no items in cart standard message).
		*/		
		if (1 == $args['cart_notices_off'] ) {
			WC()->session->set( 'wc_notices', null );
		}
		
	}
	
	function acau_apply_styles( $args ) {
		$my_style = "
		[name='update_cart'] {
			display: none!important;
		}" .		
		(( 1 == $args['custom_spinning_wheel'] ) ?
		".woocommerce .blockUI.blockOverlay:before,.woocommerce .loader:before {	
			color: " . $args['spinning_wheel_color'] . ";
		}" : "");
		
		echo '<style>' . acau_minify($my_style) . '</style>';
	}
	
	function acau_enqueue_script( $args ) {
		
		wc_enqueue_js( '
		
			var timeout;

			jQuery("div.woocommerce").on("change keyup mouseup", "input.qty, select.qty", function(){ // keyup and mouseup for Firefox support
				if (timeout != undefined) clearTimeout(timeout); //cancel previously scheduled event
				if (jQuery(this).val() == "") return; //qty empty, instead of removing item from cart, do nothing
				timeout = setTimeout(function() {
					jQuery("[name=\"update_cart\"]").trigger("click");
				}, ' . $args["update_delay"] . ' ); // schedule update cart event with delay in miliseconds specified in plugin settings
			});
			
		' );
		
	}
		
	function acau_cart_min_qty( $args, $product ) {
		$args['min_value'] = 1;
		return $args;
	}
}

function acau_minify( $input ) { 
	$output = $input;
	// Remove whitespace
	$output = preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
	// Remove trailing whitespace at the start
	$output = preg_replace('/\s\s+(.*)/', '$1', $output);
	// Remove comments
	// $output = preg_replace('#/\*.*?\*/#s', '', $output);
	$output = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/', '', $output);
	return $output;
}