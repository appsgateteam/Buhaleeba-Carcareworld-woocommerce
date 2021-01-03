<?php
/**
 * Request Quote PDF Header
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.0.11
 * @author  YITH
 */

if ( function_exists( 'icl_get_languages' ) ) {
	global $sitepress;
	$lang = yit_get_prop( $order, 'wpml_language', true );
	if( function_exists('wc_switch_to_site_locale' ) ) {
		wc_switch_to_site_locale();
	}
	YITH_Request_Quote_Premium()->change_pdf_language( $lang );

}

$order_id = yit_get_prop( $order, 'id', true );
$logo_url = get_option( 'ywraq_pdf_logo' );

$logo_attachment_id = apply_filters('yith_pdf_logo_id', get_option( 'ywraq_pdf_logo-yith-attachment-id' ));
if ( ! $logo_attachment_id && $logo_url ) {
	$logo_attachment_id = attachment_url_to_postid( $logo_url );
}

$logo               = $logo_attachment_id ? get_attached_file( $logo_attachment_id ) : $logo_url;

$image_type         = wp_check_filetype( $logo );
$mime_type          = array( 'image/jpeg', 'image/png' );
$logo               = apply_filters( 'ywraq_pdf_logo', ( isset( $image_type['type'] ) && in_array( $image_type['type'], $mime_type ) ) ? $logo : '' );

$user_name         = yit_get_prop( $order, 'ywraq_customer_name', true );
$user_email        = yit_get_prop( $order, 'ywraq_customer_email', true );
$formatted_address = $order->get_formatted_billing_address();

$billing_phone     = yit_get_prop( $order, 'ywraq_billing_phone', true );
$billing_name      = yit_get_prop( $order, '_billing_first_name', true );
$billing_surname   = yit_get_prop( $order, '_billing_last_name', true );
$billing_phone     = yit_get_prop( $order, 'ywraq_billing_phone', true );
$billing_phone     = empty( $billing_phone ) ? yit_get_prop( $order, '_billing_phone', true ) : $billing_phone;
$billing_vat       = yit_get_prop( $order, 'ywraq_billing_vat', true );


$exdata             = yit_get_prop( $order, '_ywcm_request_expire', true );
$expiration_data    = '';

if ( function_exists( 'wc_format_datetime' ) ) {
	$order_date = wc_format_datetime( $order->get_date_created() );
	if ( ! empty( $exdata ) ) {
		$exdata          = new WC_DateTime( $exdata, new DateTimeZone( 'UTC' ) );
		$expiration_data = wc_format_datetime( $exdata );
	}
} else {
	$date_format     = isset( $raq_data['lang'] ) ? ywraq_get_date_format( $raq_data['lang'] ) : wc_date_format();
	$order_date      = date_i18n( $date_format, strtotime( yit_get_prop( $order, 'date_created', true ) ) );
	$expiration_data = empty( $exdata ) ? '' : date_i18n( $date_format, strtotime( $exdata ) );
}

?>
<div class="logo">
	<img src="<?php echo $logo ?>" style="max-width: 300px;">
</div>
<div class="admin_info right">
	<table>
		<tr>
			<td valign="top" class="small-title"><?php echo __( 'From', 'yith-woocommerce-request-a-quote' ) ?></td>
            <td valign="top" class="small-info">
                <p>
	                <?php if ( 'yes' == get_option( 'ywraq_show_author_quote' ) ):
		                /**
		                 * @var $user WC_Customer
		                 */
		                $user = new WC_Customer( yit_get_prop( $order, '_ywraq_author', true ) );
		                if ( $user ):
			                $name  = $user->get_billing_first_name() . ' ' . $user->get_billing_last_name();
			                $email = $user->get_billing_email();
			                $phone = $user->get_billing_phone();
			                $from  = $name ? $name . '<br>' : '';
			                $from  .= $email ? $email . '<br>' : '';
			                $from  .= $phone ? $phone . '<br>' : '';
			                ?>
			                <?php echo $from. '<br>' ?>
		                <?php endif ?>
	                <?php endif ?>

	                <?php echo apply_filters( 'ywraq_pdf_info', nl2br( get_option( 'ywraq_pdf_info' ) ), $order ) ?>
                </p>
            </td>
		</tr>
		<tr>
			<td valign="top" class="small-title"><?php echo __( 'Customer', 'yith-woocommerce-request-a-quote' ) ?></td>
            <td valign="top" class="small-info">
                <p>
					<?php if ( empty( $billing_name ) && empty( $billing_surname ) ): ?>
                        <strong><?php echo $user_name ?></strong>
                        <br>
					<?php endif;

					echo $formatted_address . '<br>';
					echo $user_email . '<br>';

					if ( $billing_phone != '' ) {
						echo $billing_phone . '<br>';
					}

					if ( $billing_vat != '' ) {
						echo $billing_vat . '<br>';
					} ?>
                </p>
            </td>
		</tr>

		<?php if ( $expiration_data != '' ): ?>
			<tr>
				<td valign="top" class="small-title"><?php echo __( 'Expiration date', 'yith-woocommerce-request-a-quote' ) ?></td>
				<td valign="top" class="small-info">
					<p><strong><?php echo $expiration_data ?></strong></p>
				</td>
			</tr>
		<?php endif ?>
	</table>
</div>
<div class="clear"></div>
<div class="quote-title">
	<h2><?php printf( __( 'Quote #%s', 'yith-woocommerce-request-a-quote' ), apply_filters( 'ywraq_quote_number', $order_id ) ) ?></h2>
</div>