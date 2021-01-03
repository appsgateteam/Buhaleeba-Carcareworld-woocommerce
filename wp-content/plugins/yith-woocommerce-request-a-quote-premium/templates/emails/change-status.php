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
 * HTML Template Email Request a Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.1.6
 * @version 2.0.8
 * @author  YITH
 */
$order_id = yit_get_prop( $order, 'id', true );

$quote_number = apply_filters( 'ywraq_quote_number',  $order_id );

do_action( 'woocommerce_email_header', $email_heading, $email );

?>


<?php if( $status == 'accepted'): ?>
    <p><?php printf( __('The Proposal #%s has been accepted', 'yith-woocommerce-request-a-quote'), $quote_number ) ?></p>
<?php else: ?>
    <p><?php printf( __('The Proposal #%s has been rejected.', 'yith-woocommerce-request-a-quote'), $quote_number ) ?></p>
    <?php  echo '"'.stripcslashes( $reason ).'"' ?>
<?php endif ?>
    <p></p>
    <p><?php printf( __( 'You can see details here: <a href="%s">#%s</a>', 'yith-woocommerce-request-a-quote' ),  admin_url( 'post.php?post='.$order_id.'&action=edit'), $quote_number ) ?></p>

<?php
do_action( 'woocommerce_email_footer', $email );
?>