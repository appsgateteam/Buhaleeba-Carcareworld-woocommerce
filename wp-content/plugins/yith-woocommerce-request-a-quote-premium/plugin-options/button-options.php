<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$section1 = array(
	'button_settings' => array(
		'name' => __( 'Request a Quote Button Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_button_settings'
	),

	'show_btn_link' => array(
		'name'      => __( 'Button type', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose whether to show a button or a link for the "Add to quote" button.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'id'        => 'ywraq_show_btn_link',
		'options'   => array(
			'link'   => __( 'Link', 'yith-woocommerce-request-a-quote' ),
			'button' => __( 'Button', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'button',
	),

	'after_click_action' => array(
		'name'      => __( 'Add to quote action after click', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Select one of the options to define the "Add to quote" button or link behavior.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'radio',
		'id'        => 'ywraq_after_click_action',
		'options'   => array(
			'no'  => __( 'after clicking on "add to quote button", the user will view a link on the single product page to go to the request list.', 'yith-woocommerce-request-a-quote' ),
			'yes' => __( 'after clicking on "add to quote button", the user will be automatically redirected to the request list.', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'no',
	),

	'show_btn_single_page' => array(
		'name'      => __( 'Choose where to show the "add to quote" button.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Show button in single product page', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'id'        => 'ywraq_show_btn_single_page',
		'default'   => 'yes'
	),

	'show_btn_other_pages'   => array(
		'name'      => '',
		'desc'      => __( 'Show button in other pages', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_btn_other_pages',
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'default'   => 'no'
	),


	'show_btn_woocommerce_blocks'   => array(
		'name'      => '',
		'desc'      => __( 'Show button in WooCommerce Blocks', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_btn_woocommerce_blocks',
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'default'   => 'no'
	),

	'allow_raq_out_of_stock' => array(
		'name'      => '',
		'desc'      => __( 'Show button even if the product is out of stock', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_allow_raq_out_of_stock',
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'default'   => 'no'
	),

	'show_btn_only_out_of_stock' => array(
		'name'      => '',
		'desc'      => __( 'Show button only on out of stock products. ', 'yith-woocommerce-request-a-quote' ),
		'id'        => 'ywraq_show_btn_only_out_of_stock',
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'default'   => 'no'
	),

	'show_button_near_add_to_cart' => array(
		'name'      => __( 'Show button next to "Add to cart" in single product page', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'By enabling this option, the user will see the Request a Quote link or button next to the Add to cart button.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'checkbox',
		'id'        => 'ywraq_show_button_near_add_to_cart',
		'default'   => 'no'
	),

	'show_button_on_checkout_page' => array(
		'name'        => __( 'Show button on checkout page', 'yith-woocommerce-request-a-quote' ),
		'desc-inline' => __( 'Show a button in checkout to let your users convert the cart content into a quote request. This will immediately create a quote request. <br>(We suggest enabling this option if you want that your users on checkout are not directed to the Quote page before submitting a request.)', 'yith-woocommerce-request-a-quote' ),
		'type'        => 'yith-field',
		'yith-type'   => 'checkbox',
		'id'          => 'ywraq_show_button_on_checkout_page',
		'default'     => 'no'
	),

	'checkout_quote_button_label' => array(
		'name'      => __( 'Checkout Button Label', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose the text you wish to show within the "add to quote" button or link on the checkout page.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'id'        => 'ywraq_checkout_quote_button_label',
		'default'   => __( 'Request a Quote', 'yith-woocommerce-request-a-quote' ),
	),

	'button_settings_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_button_settings_end'
	),

	/**
	 * BUTTON LABELS
	 */
	'button_labels'       => array(
		'name' => __( 'Button Labels', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_button_labels'
	),

	'show_btn_link_text' => array(
		'name'      => __( 'Add to Quote', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose the text you wish to show within the "add to quote" button or link on the single product page.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'id'        => 'ywraq_show_btn_link_text',
		'default'   => __( 'Add to quote', 'yith-woocommerce-request-a-quote' ),
	),

	'show_already_in_quote' => array(
		'name'      => __( 'Product already in the list.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose the text you wish to display in the link that shows users the product is already on the request list within the loop (product list) or in the single product page.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'id'        => 'ywraq_show_already_in_quote',
		'default'   => __( 'This product is already in quote request list', 'yith-woocommerce-request-a-quote' ),
	),

	'show_browse_list' => array(
		'name'      => __( 'Browse the list', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose the text you wish to show in the link that redirects users to the request list within the loop (product list) or on the single product page.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'id'        => 'ywraq_show_browse_list',
		'default'   => __( 'Browse the list', 'yith-woocommerce-request-a-quote' ),
	),

	'show_product_added' => array(
		'name'      => __( 'Product added to the list!', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose the text you wish to show soon after adding a product to the request list.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'text',
		'id'        => 'ywraq_show_product_added',
		'default'   => __( 'Product added', 'yith-woocommerce-request-a-quote' ),
	),

	'button_labels_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_button_labels_end'
	),
	/**
	 * BUTTON COLORS
	 */
	'button_colors'     => array(
		'name' => __( 'Request Quote Button Color Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_button_colors'
	),

	'layout_settings_button_bg_color' => array(
		'name'      => __( 'Button background color', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'desc'      => '',
		'id'        => 'ywraq_layout_button_bg_color',
		'default'   => '#0066b4'
	),

	'layout_settings_button_bg_color_hover' => array(
		'name'      => __( 'Button background on hover', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'desc'      => '',
		'id'        => 'ywraq_layout_button_bg_color_hover',
		'default'   => '#044a80'
	),

	'layout_settings_button_color' => array(
		'name'      => __( 'Button/Link text color', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'desc'      => '',
		'id'        => 'ywraq_layout_button_color',
		'default'   => '#fff'
	),

	'layout_settings_button_color_hover' => array(
		'name'      => __( 'Button/Link text color hover', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'colorpicker',
		'desc'      => '',
		'id'        => 'ywraq_layout_button_color_hover',
		'default'   => '#fff'
	),

	'button_colors_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_button_colors_end'
	),

	/**
	 * USER SETTINGS
	 */
	'user_settings'     => array(
		'name' => __( 'User Settings', 'yith-woocommerce-request-a-quote' ),
		'type' => 'title',
		'id'   => 'ywraq_user_settings'
	),

	'user_type' => array(
		'name'      => __( 'Choose who you want to show the "Add to quote" button.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose whether to show the "Add to quote" button only to logged or guest users or both.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'id'        => 'ywraq_user_type',
		'options'   => array(
			'all'       => __( 'Guests & logged-in users', 'yith-woocommerce-request-a-quote' ),
			'guests'    => __( 'Guests', 'yith-woocommerce-request-a-quote' ),
			'customers' => __( 'Logged-in users', 'yith-woocommerce-request-a-quote' ),
		),
		'default'   => 'all'
	),
	'user_role' => array(
		'name'      => __( 'Choose who you want to show the "Add to quote" button.', 'yith-woocommerce-request-a-quote' ),
		'desc'      => __( 'Choose whether to show the "Add to quote" button only to specific roles.', 'yith-woocommerce-request-a-quote' ),
		'type'      => 'yith-field',
		'yith-type' => 'select',
		'class'     => 'wc-enhanced-select',
		'css'       => 'min-width:300px',
		'multiple'  => true,
		'id'        => 'ywraq_user_role',
		'options'   => yith_ywraq_get_roles(),
		'default'   => array( 'all' )
	),

	'user_settings_end' => array(
		'type' => 'sectionend',
		'id'   => 'ywraq_user_settings_end'
	),

);


return array( 'button' => apply_filters( 'ywraq_button_settings_options', $section1 ) );
