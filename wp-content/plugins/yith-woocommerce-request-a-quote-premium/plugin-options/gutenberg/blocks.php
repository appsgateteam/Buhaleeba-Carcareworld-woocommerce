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

$blocks = array(
	'yith-ywraq-widget-quote' => array(
		'style'          => 'yith-ywraq-gutenberg',
		'title'          => _x( 'Widget Quote List', '[gutenberg]: block name', 'yith-woocommerce-request-a-quote' ),
		'description'    => _x( 'Show products added to your list.', '[gutenberg]: block description', 'yith-woocommerce-request-a-quote' ),
		'shortcode_name' => 'yith_ywraq_widget_quote',
		'do_shortcode'   => true,
		'keywords'       => array(
			_x( 'Quote', '[gutenberg]: keywords', 'yith-woocommerce-request-a-quote' ),
			_x( 'Quote Widget', '[gutenberg]: keywords', 'yith-woocommerce-request-a-quote' ),
		),
		'attributes'     => array(
			'title'        => array(
				'type'    => 'text',
				'label'   => _x( 'Title', '[gutenberg]: title of widget', 'yith-woocommerce-request-a-quote' ),
				'default' => __( 'Quote List', 'yith-woocommerce-request-a-quote' ),
			),
			'show_thumbnail' => array(
				'type'          => 'toggle',
				'label'         => _x( 'Show Thumbnail', '[gutenberg]: show or hide the thumbnail', 'yith-woocommerce-request-a-quote' ),
				'default'       => true,
				'helps'         => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show thumbnail', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide thumbnail', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_price'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Price', '[gutenberg]: show or hide the price', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show price', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide price', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_quantity'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Quantity', '[gutenberg]: show or hide the quantity', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show quantity', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide quantity', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_variations'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Variations', '[gutenberg]: show or hide the variations', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show variations', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide variations', 'yith-woocommerce-request-a-quote' ),
				),
			),

		),
	),
	'yith-ywraq-mini-widget-quote' => array(
		'style'          => 'yith-ywraq-gutenberg',
		'title'          => _x( 'Mini Widget Quote List', '[gutenberg]: block name', 'yith-woocommerce-request-a-quote' ),
		'description'    => _x( 'Show products added to your list.', '[gutenberg]: block description', 'yith-woocommerce-request-a-quote' ),
		'shortcode_name' => 'yith_ywraq_mini_widget_quote',
		'do_shortcode'   => true,
		'keywords'       => array(
			_x( 'Quote', '[gutenberg]: keywords', 'yith-woocommerce-request-a-quote' ),
			_x( 'Mini Quote Widget', '[gutenberg]: keywords', 'yith-woocommerce-request-a-quote' ),
		),
		'attributes'     => array(
			'title'        => array(
				'type'    => 'text',
				'label'   => _x( 'Title', '[gutenberg]: title of widget', 'yith-woocommerce-request-a-quote' ),
				'default' => __( 'Quote List', 'yith-woocommerce-request-a-quote' ),
			),
			'item_name'        => array(
				'type'    => 'text',
				'label'   => _x( 'Item name', '[gutenberg]: title of widget', 'yith-woocommerce-request-a-quote' ),
				'default' => __( 'item', 'yith-woocommerce-request-a-quote' ),
			),
			'item_plural_name'        => array(
				'type'    => 'text',
				'label'   => _x( 'Item plural name', '[gutenberg]: title of widget', 'yith-woocommerce-request-a-quote' ),
				'default' => __( 'items', 'yith-woocommerce-request-a-quote' ),
			),
			'show_thumbnail' => array(
				'type'          => 'toggle',
				'label'         => _x( 'Show Thumbnail', '[gutenberg]: show or hide the thumbnail', 'yith-woocommerce-request-a-quote' ),
				'default'       => true,
				'helps'         => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show thumbnail', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide thumbnail', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_price'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Price', '[gutenberg]: show or hide the price', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show price', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide price', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_quantity'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Quantity', '[gutenberg]: show or hide the quantity', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show quantity', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide quantity', 'yith-woocommerce-request-a-quote' ),
				),
			),
			'show_variations'      => array(
				'type'    => 'toggle',
				'label'   => _x( 'Show Variations', '[gutenberg]: show or hide the variations', 'yith-woocommerce-request-a-quote' ),
				'default' => true,
				'helps'   => array(
					'checked'   => _x( 'Show it', '[gutenberg]: Help to show variations', 'yith-woocommerce-request-a-quote' ),
					'unchecked' => _x( 'Hide it', '[gutenberg]: Help to hide variations', 'yith-woocommerce-request-a-quote' ),
				),
			),

		),
	),
);




return apply_filters('ywraq_gutenberg_blocks', $blocks );