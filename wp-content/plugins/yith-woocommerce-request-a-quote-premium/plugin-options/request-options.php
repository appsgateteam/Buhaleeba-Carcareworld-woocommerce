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

$section1 = array( 'request_settings'     => array( 'name' => __( 'Request a Quote List Settings', 'yith-woocommerce-request-a-quote' ),
                                                    'type' => 'title',
                                                    'id'   => 'request_settings' ),
                   'page_id'              => array( 'name'     => __( 'Choose what page will include the list.', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'     => sprintf( '%s<br><span class="ywraq-attention">%s</span>%s',
	                                                    __( 'You can choose from this list the page on which users will see their quote requests.', 'yith-woocommerce-request-a-quote' ),
	                                                    __( 'Please note', 'yith-woocommerce-request-a-quote' ),
	                                                    __(': by choosing a page different from the default one (request a quote) and allow users to view their requests list, you will need to insert the following shortcode [yith_ywraq_request_quote] ', 'yith-woocommerce-request-a-quote') ),
                                                    'id'       => 'ywraq_page_id',
                                                    'type'     => 'single_select_page',
                                                    'class'    => 'wc-enhanced-select',
                                                    'css'      => 'min-width:300px',
                                                    'desc_tip' => false, ),
                   //@since 1.1.6
                   'show_sku'             => array( 'name'      => __( 'Show SKU on list table', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'If checked, the sku will be added near the title of product in the request list', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_show_sku',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'checkbox',
                                                    'default'   => 'no' ),
                   //@since 1.1.6
                   'show_preview'         => array( 'name'      => __( 'Show preview thumbnail on email list table', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'If checked, the thumbnail will be added in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_show_preview',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'checkbox',
                                                    'default'   => 'no' ),

                   //@since 1.3.0
                   'show_old_price'       => array( 'name'      => __( 'Show old price on list table', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'If checked, the old price will be showed in the table of request and in the proposal email', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_show_old_price',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'checkbox',
                                                    'default'   => 'yes' ),

                   //@since 1.4.9
                   'show_return_to_shop'  => array( 'name'      => __( 'Show "Return to Shop" button', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'If checked, the "Return to Shop" button will be showed in the request list', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_show_return_to_shop',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'checkbox',
                                                    'default'   => 'yes' ),

                   //@since 1.4.9
                   'return_to_shop_label' => array( 'name'      => __( '"Return to Shop" button label', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'Edit the text of the button that will allow users to go back to the shop page.', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_return_to_shop_label',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'text',
                                                    'deps'      => array( 'id'    => 'ywraq_show_return_to_shop',
                                                                          'value' => 'yes' ),
                                                    'default'   => __( 'Return to Shop', 'yith-woocommerce-request-a-quote' ) ),
                   'return_to_shop_url'   => array( 'name'      => __( '"Return to Shop" URL', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => __( 'Define the URL to assign to the button.', 'yith-woocommerce-request-a-quote' ),
                                                    'id'        => 'ywraq_return_to_shop_url',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'text',
                                                    'deps'      => array( 'id'    => 'ywraq_show_return_to_shop',
                                                                          'value' => 'yes'),
                                                    'default'   => function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : get_permalink( woocommerce_get_page_id( 'shop' ) ), ),

                   'show_update_list'  => array( 'name'      => __( 'Show "Update List" button', 'yith-woocommerce-request-a-quote' ),
                                                 'desc'      => __( 'If checked, the "Update List" button will be showed in the request list', 'yith-woocommerce-request-a-quote' ),
                                                 'id'        => 'ywraq_show_update_list',
                                                 'type'      => 'yith-field',
                                                 'yith-type' => 'checkbox',
                                                 'default'   => 'yes' ),
                   'update_list_label' => array( 'name'      => __( '"Update List" button label', 'yith-woocommerce-request-a-quote' ),
                                                 'desc'      => __( 'Edit the text of the button that will allow users to update the list.', 'yith-woocommerce-request-a-quote' ),
                                                 'id'        => 'ywraq_update_list_label',
                                                 'type'      => 'yith-field',
                                                 'yith-type' => 'text',
                                                 'deps'      => array( 'id'    => 'ywraq_show_update_list',
                                                                       'value' => 'yes' ),
                                                 'default'   => __( 'Update List', 'yith-woocommerce-request-a-quote' ) ),
                   'hide_column_total' => array( 'name'      => __( 'Hide column Total', 'yith-woocommerce-request-a-quote' ),
                                                 'desc'      => __( 'Hide the column Total of single product.', 'yith-woocommerce-request-a-quote' ),
                                                 'id'        => 'ywraq_hide_total_column',
                                                 'type'      => 'yith-field',
                                                 'yith-type' => 'checkbox',
                                                 'default'   => 'no' ),

                   'show_total_in_list'   => array( 'name'      => __( 'Show total in quote list', 'yith-woocommerce-request-a-quote' ),
                                                    'desc'      => '',
                                                    'id'        => 'ywraq_show_total_in_list',
                                                    'type'      => 'yith-field',
                                                    'yith-type' => 'checkbox',
                                                    'default'   => 'no' ),
                   'request_settings_end' => array( 'type' => 'sectionend',
                                                    'id'   => 'ywraq_request_settings_end' ),

);


return array( 'request' => apply_filters( 'ywraq_request_settings_options', $section1 ) );