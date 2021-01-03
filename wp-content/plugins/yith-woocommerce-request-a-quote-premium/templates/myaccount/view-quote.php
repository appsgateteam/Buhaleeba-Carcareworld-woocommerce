<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Quote Detail
 *
 * Shows recent orders on the account page
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.0.11
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
YITH_YWRAQ_Order_Request()->is_expired( $order_id );

$order = wc_get_order( $order_id );
add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

if ( ! $order ) {
	_e( 'This Quote doesn\'t exist.', 'yith-woocommerce-request-a-quote' );

	return;
}

$user_email       = yit_get_prop( $order, 'ywraq_customer_email', true );
$customer_message = yit_get_prop( $order, 'ywraq_customer_message', true );
$af4              = yit_get_prop( $order, 'ywraq_other_email_fields', true );
$admin_message    = yit_get_prop( $order, '_ywcm_request_response', true );
$exdata           = yit_get_prop( $order, '_ywcm_request_expire', true );
$order_date       = strtotime( yit_get_prop( $order, 'date_created', true ) );

if ( $order->get_user_id() != $current_user->ID ) {
	_e( 'You do not have permission to read the quote.', 'yith-woocommerce-request-a-quote' );

	return;
}

if ( $order->get_status() == 'trash' ) {
	_e( 'This Quote was deleted by administrator.', 'yith-woocommerce-request-a-quote' );

	return;
}

$show_price        = ! ( get_option( 'ywraq_hide_price' ) == 'yes' && $order->get_status() == 'ywraq-new' );
$show_total_column = ! ( get_option( 'ywraq_hide_total_column', 'yes' ) == 'yes' && $order->get_status() == 'ywraq-new' );
$colspan           = $show_total_column ? 1 : 2;

if ( $order->get_status() == 'ywraq-new' ) {

	if ( catalog_mode_plugin_enabled() ) {

		foreach ( $order->get_items() as $item_id => $item ) {

			//wc 2.7
			if ( is_object( $item ) ) {
				$_product = $item->get_product();
			} else {
				$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			}


			$hide_price = apply_filters( 'yith_ywraq_hide_price_template', WC()->cart->get_product_subtotal( $_product, $item['qty'] ), $_product->get_id(), $item );
			if ( $hide_price == '' ) {
				$show_price = false;
			}
		}
	}
}

//do_action( 'woocommerce_account_navigation' );

?>

<p>
	<strong><?php _e( 'Request date', 'yith-woocommerce-request-a-quote' ) ?></strong>: <?php echo date_i18n( wc_date_format(), $order_date ) ?>
</p>
<?php

$accept_button_text = ( YITH_Request_Quote()->enabled_checkout() && $order->get_status() != 'ywraq-pending' ) ? __( 'Checkout', 'yith-woocommerce-request-a-quote' ) : ywraq_get_label( 'accept' );

$pdf_file = false;

if ( file_exists( YITH_Request_Quote_Premium()->get_pdf_file_path( $order_id ) ) ) {
	$pdf_file = YITH_Request_Quote_Premium()->get_pdf_file_url( $order_id );
}
$print_button_pdf = get_option( 'ywraq_pdf_in_myaccount' ) == 'yes' && $pdf_file;

if ( in_array( $order->get_status(), array( 'ywraq-pending' ) ) ): ?>
	<p class="ywraq-buttons">
		<?php if ( $print_button_pdf ) { ?><a class="ywraq-big-button ywraq-pdf-file"
		                                      href="<?php echo esc_url( $pdf_file ) ?>"
		                                      target="_blank"><?php _e( 'Download PDF', 'yith-woocommerce-request-a-quote' ) ?></a><?php } ?>
		<?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' ) : ?><a class="ywraq-big-button ywraq-accept"
		                                                                   href="<?php echo esc_url( ywraq_get_accepted_quote_page( $order ) ) ?>"><?php echo $accept_button_text ?></a><?php endif ?>
		<?php if ( get_option( 'ywraq_show_reject_link' ) != 'no' ) : ?> <a class="ywraq-big-button ywraq-reject"
		                                                                    href="<?php echo esc_url( ywraq_get_rejected_quote_page( $order ) ) ?>"><?php ywraq_get_label( 'reject', true ) ?></a><?php endif ?>
	</p>

<?php elseif ( $order->get_status() == 'ywraq-accepted' ): ?>
	<p class="ywraq-buttons">
		<?php if ( $print_button_pdf ) { ?><a class="ywraq-big-button ywraq-pdf-file"
		                                      href="<?php echo esc_url( $pdf_file ) ?>"
		                                      target="_blank"><?php _e( 'Download PDF', 'yith-woocommerce-request-a-quote' ) ?></a><?php } ?>
		<?php if ( get_option( 'ywraq_show_accept_link' ) != 'no' && YITH_Request_Quote()->enabled_checkout() ) : ?><a
			class="ywraq-big-button ywraq-accept" href="<?php echo esc_url( add_query_arg( array(
				'request_quote' => $order_id,
				'status'        => 'accepted',
				'raq_nonce'     => ywraq_get_token( 'accept-request-quote', $order_id, $user_email ),
				'lang'          => get_post_meta( $order_id, 'wpml_language', true )
			), YITH_Request_Quote()->get_raq_page_url() ) ) ?>"><?php echo $accept_button_text ?></a><?php endif ?>
	</p>
<?php else:
	?>
	<p>
		<strong><?php echo __( 'Order Status:', 'yith-woocommerce-request-a-quote' ) ?></strong> <?php echo wc_get_order_status_name( $order->get_status() ) ?>
	</p>
	<?php if ( $order->has_status( 'ywraq-rejected' ) && $order->get_customer_note() ): ?>
	<p>
		<strong><?php echo __( 'Customer reason:', 'yith-woocommerce-request-a-quote' ) ?></strong> <?php echo $order->get_customer_note() ?>
	</p>
<?php endif; ?>
	<?php if ( $print_button_pdf ) { ?><p class="ywraq-buttons"><a class="ywraq-big-button ywraq-pdf-file"
	                                                               href="<?php echo esc_url( $pdf_file ) ?>"
	                                                               target="_blank"><?php _e( 'Download PDF', 'yith-woocommerce-request-a-quote' ) ?></a>
	</p><?php } ?>
<?php endif ?>
<h2><?php _e( 'Quote Details', 'yith-woocommerce-request-a-quote' ); ?></h2>

<?php if ( $exdata != '' ): ?>
	<p>
		<strong><?php _e( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></strong>: <?php echo date_i18n( wc_date_format(), strtotime( $exdata ) ) ?>
	</p>
<?php endif ?>

<table class="shop_table order_details">
	<thead>
	<tr>
		<th class="product-name"
		    colspan="<?php echo $colspan ?>"><?php _e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php if ( $show_total_column && $show_price ): ?>
			<th class="product-total"><?php _e( 'Total', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php endif ?>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( sizeof( $order->get_items() ) > 0 ) {

		foreach ( $order->get_items() as $item_id => $item ) {


			if ( is_object( $item ) ) {
				$_product = $item->get_product();
			} else {
				$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			}

			//retro compatibility
			$item_meta = false;
			if ( version_compare( WC()->version, '2.7.0', '<' ) ) {
				$item_meta = new WC_Order_Item_Meta( $item, $_product );
			}

			$title = $_product ? $_product->get_title() : $item->get_name();

			if ( $_product && $_product->get_sku() != '' && get_option( 'ywraq_show_sku' ) == 'yes' ) {
				$sku   = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
				$title .= ' ' . apply_filters( 'ywraq_sku_label_html', $sku, $_product );
			}

			if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) :
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td class="product-name">
						<?php

						if ( ! $_product || ( $_product && ! $_product->is_visible() ) || ! apply_filters( 'ywraq_list_show_product_permalinks', true, 'view_quote' ) ) {
							echo apply_filters( 'woocommerce_order_item_name', $title, $item, false );
						} else {
							echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $title ), $item, true );
						}

						echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

						// Allow other plugins to add additional product information here
						do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

						if ( $item_meta ) {
							$item_meta->display();
						} else {
							wc_display_item_meta( $item );
						}

						// Allow other plugins to add additional product information here
						do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );
						?>
					</td>
					<?php if ( $show_price ): ?>
						<td class="product-total">
							<?php

							echo $order->get_formatted_line_subtotal( $item );

							?>
						</td>
					<?php endif ?>
				</tr>
			<?php

			endif;

			if ( $order->has_status( array(
					'completed',
					'processing'
				) ) && ( $purchase_note = get_post_meta( $_product->get_id(), '_purchase_note', true ) ) ) { ?>
				<tr class="product-purchase-note">
					<td colspan="3"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); ?></td>
				</tr>
				<?php
			}
		}
	}

	do_action( 'woocommerce_order_items_table', $order );
	?>
	</tbody>
	<tfoot>
	<?php
	$has_refund = false;

	if ( $total_refunded = $order->get_total_refunded() ) {
		$has_refund = true;
	}

	if ( $show_total_column && $totals = $order->get_order_item_totals() ) {
		foreach ( $totals as $key => $total ) {
			$value = $total['value'];

			?>
			<?php if ( $show_price ): ?>
				<tr>
					<th scope="row"><?php echo $total['label']; ?></th>
					<td><?php echo $value; ?></td>
				</tr>
			<?php endif ?>
			<?php
		}
	}
	?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<header>
	<h2><?php _e( 'Customer\'s details', 'yith-woocommerce-request-a-quote' ); ?></h2>
</header>
<table class="shop_table shop_table_responsive customer_details">

	<?php

	$user_name         = yit_get_prop( $order, 'ywraq_customer_name', true );
	$billing_name      = yit_get_prop( $order, '_billing_first_name', true );
	$billing_surname   = yit_get_prop( $order, '_billing_last_name', true );
	$billing_company   = yit_get_prop( $order, '_billing_company', true );
	$billing_address_1 = yit_get_prop( $order, '_billing_address_1', true );
	$billing_address_2 = yit_get_prop( $order, '_billing_address_2', true );
	$billing_city      = yit_get_prop( $order, '_billing_city', true );
	$billing_postcode  = yit_get_prop( $order, '_billing_postcode', true );
	$billing_state     = yit_get_prop( $order, '_billing_state', true );
	$billing_country   = yit_get_prop( $order, '_billing_country', true );
	$billing_email     = yit_get_prop( $order, '_billing_email', true );
	$billing_email     = empty( $billing_email ) ? $user_email : $billing_email;
	$billing_phone     = yit_get_prop( $order, '_billing_phone', true );
	$billing_phone     = empty( $billing_phone ) ? yit_get_prop( $order, 'ywraq_billing_phone', true ) : $billing_phone;
	$billing_vat       = yit_get_prop( $order, 'ywraq_billing_vat', true );
	$billing_vat       = empty( $billing_vat ) ? yit_get_prop( $order, '_billing_vat', true ) : $billing_vat;


	echo '<tr><th>' . __( 'Name:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Name', 'yith-woocommerce-request-a-quote' ) . '">' . ( empty( $billing_name ) && empty( $billing_surname ) ? $user_name : $billing_name . ' ' . $billing_surname ) . '</td></tr>';

	if ( $billing_company ) {
		echo '<tr><th>' . __( 'Company:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Company', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_company . '</td></tr>';
	}

	if ( $billing_address_1 || $billing_address_2 ) {
		echo '<tr><th>' . __( 'Address:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Address', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_address_1 . ( $billing_address_1 ? '<br />' : '' ) . $billing_address_2 . '</td></tr>';
	}

	if ( $billing_city ) {
		echo '<tr><th>' . __( 'City:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'City', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_city . '</td></tr>';
	}

	if ( $billing_postcode ) {
		echo '<tr><th>' . __( 'Postcode:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Postcode', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_postcode . '</td></tr>';
	}

	if ( $billing_state ) {
		$states = WC()->countries->get_states( $billing_country );
		$state  = $states[ $billing_state ];
		echo '<tr><th>' . __( 'State/Province:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'State/Province', 'yith-woocommerce-request-a-quote' ) . '">' . ( $state == '' ? $billing_state : $state ) . '</td></tr>';
	}

	if ( $billing_country ) {
		$countries = WC()->countries->get_countries();
		echo '<tr><th>' . __( 'Country:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Country', 'yith-woocommerce-request-a-quote' ) . '">' . $countries[ $billing_country ] . '</td></tr>';
	}

	if ( $billing_email ) {
		echo '<tr><th>' . __( 'Email:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Email', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_email . '</td></tr>';
	}

	if ( $billing_phone ) {
		echo '<tr><th>' . __( 'Telephone:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'Telephone', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_phone . '</td></tr>';
	}

	if ( $billing_vat ) {
		echo '<tr><th>' . __( 'VAT:', 'yith-woocommerce-request-a-quote' ) . '</th><td data-title="' . __( 'VAT', 'yith-woocommerce-request-a-quote' ) . '">' . $billing_vat . '</td></tr>';
	}

	// Additional customer details hook
	do_action( 'woocommerce_order_details_after_customer_details', $order );
	?>
</table>

<?php

if ( '' != $customer_message || ! empty( $af4 ) || '' != $admin_message ) :
	?>
	<header>
		<h2><?php _e( 'Additional Information', 'yith-woocommerce-request-a-quote' ); ?></h2>
	</header>
	<table class="shop_table shop_table_responsive customer_details">
		<?php

		// Check for customer note
		if ( '' != $customer_message ) { ?>
			<tr>
				<th scope="row"><?php _e( 'Customer\'s Message:', 'yith-woocommerce-request-a-quote' ); ?></th>
				<td data-title="<?php _e( 'Customer\'s Message', 'yith-woocommerce-request-a-quote' ); ?>"><?php echo wptexturize( $customer_message ); ?></td>
			</tr>
		<?php } //


		if ( ! empty( $af4 ) ) {
			foreach ( $af4 as $key => $value ) {
				?>
				<tr>
					<th scope="row"><?php echo ucwords( str_replace( '-', ' ', $key ) ); ?></th>
					<td data-title="<?php echo ucwords( str_replace( '-', ' ', $key ) ); ?>"><?php echo $value; ?></td>
				</tr>
			<?php }
		}


		if ( '' != $admin_message ) { ?>
			<tr>
				<th scope="row"><?php _e( 'Administrator\'s Message:', 'yith-woocommerce-request-a-quote' ); ?></th>
				<td data-title="<?php _e( 'Administrator\'s Message', 'yith-woocommerce-request-a-quote' ); ?>"><?php echo wptexturize( $admin_message ); ?></td>
			</tr>
		<?php } ?>

	</table>
<?php endif ?>
<div class="clear"></div>
