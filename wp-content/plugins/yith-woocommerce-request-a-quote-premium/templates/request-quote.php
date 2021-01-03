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
 * Request A Quote pages template; load template parts
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 1.4.3
 * @author  YITH
 */

global $wpdb, $woocommerce;

function_exists( 'wc_nocache_headers' ) && wc_nocache_headers();

?>

<div class="woocommerce ywraq-wrapper">

	<?php
	if ( ! apply_filters( 'yith_ywraq_before_print_raq_page', true ) ) : ?>
        <div id="yith-ywraq-message"><?php echo apply_filters( 'yith_ywraq_raq_page_deniend_access', __( 'You do not have access to this page', 'yith-woocommerce-request-a-quote' ) ) ?></div>
		<?php
		return;
	endif;

	if ( function_exists( 'wc_print_notices' ) ) {
		if ( defined( 'YWMMQ_PREMIUM' ) && YWMMQ_PREMIUM ) {
			wc_print_notices();
		}
		yith_ywraq_print_notices();
	}

    if( ! isset($_REQUEST['hidem'])  ):
    ?>
    <div id="yith-ywraq-message"><?php do_action( 'ywraq_raq_message' ) ?></div>
	<?php if ( isset( $_GET['raq_nonce'] ) )
		return ?>
	<?php wc_get_template( 'request-quote-' . $template_part . '.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ); ?>

	<?php if ( $args['show_form'] == 'yes' && count( $raq_content ) != 0 ): ?>
		<?php if ( ! defined( 'YITH_YWRAQ_PREMIUM' ) ): ?>
			<?php wc_get_template( 'request-quote-form.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' ); ?>

        <?php else: ?>
            <?php YITH_Request_Quote_Premium()->get_inquiry_form( $args ) ?>
        <?php endif ?>
    <?php endif ?>
    <?php endif ?>
</div>