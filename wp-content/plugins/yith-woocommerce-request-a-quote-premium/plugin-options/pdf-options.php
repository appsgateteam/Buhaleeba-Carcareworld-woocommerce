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


$section1 = array( 'pdf_settings'                 => array( 'name' => __( 'PDF Quote Settings', 'yith-woocommerce-request-a-quote' ),
                                                            'type' => 'title',
                                                            'id'   => 'ywraq_pdf_settings' ),
                   'pdf_in_myaccount'             => array( 'name'      => __( 'Allow creating PDF document download in My Account page', 'yith-woocommerce-request-a-quote' ),
                                                            'desc'      => __( 'If checked, a button "Download PDF" will be added in the quote detail page', 'yith-woocommerce-request-a-quote' ),
                                                            'id'        => 'ywraq_pdf_in_myaccount',
                                                            'type'      => 'yith-field',
                                                            'yith-type' => 'checkbox',
                                                            'default'   => 'no' ),
                   'pdf_pagination'               => array( 'name'      => __( 'Enable pagination.', 'yith-woocommerce-request-a-quote' ),
                                                            'desc'      => __( 'Enable pagination.', 'yith-woocommerce-request-a-quote' ),
                                                            'type'      => 'yith-field',
                                                            'id'        => 'ywraq_pdf_pagination',
                                                            'yith-type' => 'checkbox',
                                                            'default'   => 'yes' ),
                   'pdf_attachment'               => array( 'name'      => __( 'Attach PDF quote to the email', 'yith-woocommerce-request-a-quote' ),
                                                            'desc'      => __( 'If checked, the quote will be sent as PDF attachment', 'yith-woocommerce-request-a-quote' ),
                                                            'id'        => 'ywraq_pdf_attachment',
                                                            'type'      => 'yith-field',
                                                            'yith-type' => 'checkbox',
                                                            'default'   => 'no' ),
                   'pdf_link'                     => array( 'name'      => __( 'Show Link  Accept/Reject', 'yith-woocommerce-request-a-quote' ),
                                                            'desc'      => __( 'By enabling this option, the link to accept or reject the quote will be inserted into the PDF. You can enable the link to accept or reject the quote from the Quote Settings tab.', 'yith-woocommerce-request-a-quote' ),
                                                            'id'        => 'ywraq_pdf_link',
                                                            'type'      => 'yith-field',
                                                            'yith-type' => 'checkbox',
                                                            'default'   => 'no' ),
                   'hide_table_is_pdf_attachment' => array( 'name'      => __( 'Remove the list with products from the email', 'yith-woocommerce-request-a-quote' ),
                                                            'desc'      => __( 'Hide quote in the email if it has been sent as PDF attachment', 'yith-woocommerce-request-a-quote' ),
                                                            'id'        => 'ywraq_hide_table_is_pdf_attachment',
                                                            'type'      => 'yith-field',
                                                            'yith-type' => 'checkbox',
                                                            'default'   => 'no' ),

                   'pdf_settings_end'   => array( 'type' => 'sectionend',
                                                  'id'   => 'ywraq_pdf_settings_end' ),
                   'pdf_layout'         => array( 'name' => __( 'PDF Quote Layout', 'yith-woocommerce-request-a-quote' ),
                                                  'type' => 'title',
                                                  'id'   => 'ywraq_pdf_layout' ),
                   'pdf_logo'           => array( 'name'      => __( 'Logo', 'yith-woocommerce-request-a-quote' ),
                                                  'desc'      => __( 'Upload the logo you want to show in the PDF document. Only .png and .jpeg extensions are allowed.', 'yith-woocommerce-request-a-quote' ),
                                                  'id'        => 'ywraq_pdf_logo',
                                                  'type'      => 'yith-field',
                                                  'yith-type' => 'upload',
                                                  'default'   => YITH_YWRAQ_DIR . 'assets/images/logo.jpg', ),
                   'pdf_info'           => array( 'name'      => __( 'Sender Info in PDF file', 'yith-woocommerce-request-a-quote' ),
                                                  'desc'      => __( 'Add sender information that have to be shown in the PDF document', 'yith-woocommerce-request-a-quote' ),
                                                  'id'        => 'ywraq_pdf_info',
                                                  'type'      => 'yith-field',
                                                  'yith-type' => 'textarea',
                                                  'default'   => get_bloginfo( 'name' ) ),
                   'show_author_quote'      => array(
	                   'name'      => __( 'Show quote author', 'yith-woocommerce-request-a-quote' ),
	                   'desc'      => __( 'If checked, the quote will show information of the user who sent it.', 'yith-woocommerce-request-a-quote' ),
	                   'id'        => 'ywraq_show_author_quote',
	                   'type'      => 'yith-field',
	                   'yith-type' => 'checkbox',
	                   'default'   => 'no'
                   ),
                   'pdf_footer_content' => array( 'name'      => __( 'Add general text on the footer of pdf document.', 'yith-woocommerce-request-a-quote' ),
                                                  'desc'      => __( 'If checked, a button "Download PDF" will be added in the quote detail page.', 'yith-woocommerce-request-a-quote' ),
                                                  'id'        => 'ywraq_pdf_footer_content',
                                                  'type'      => 'yith-field',
                                                  'yith-type' => 'textarea',
                                                  'default'   => '' ),
                   'pdf_layout_end' => array( 'type' => 'sectionend',
                                              'id'   => 'ywraq_pdf_layout_end' ),


);


return array( 'pdf' => apply_filters( 'ywraq_pdf_settings_options', $section1 ) );