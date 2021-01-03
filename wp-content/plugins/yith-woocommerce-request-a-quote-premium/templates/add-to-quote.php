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
 * Add to Quote button template
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.1.0
 * @author  YITH
 */

 $data_variations = ( isset( $variations ) && !empty( $variations ) ) ? ' data-variation="'.$variations.'" ' : '';

 ?>

<div class="yith-ywraq-add-to-quote add-to-quote-<?php echo $product_id ?>" <?php echo $data_variations ?>>
    <?php if ( ! is_product() && apply_filters('yith_ywraq_quantity_loop', false) ) woocommerce_quantity_input(); ?>
    <div class="yith-ywraq-add-button <?php echo ( $exists ) ? 'hide': 'show' ?>" style="display:<?php echo ( $exists ) ? 'none': 'block' ?>" data-product_id="<?php echo esc_attr( $product_id)?>" >
        <?php wc_get_template( 'add-to-quote-' . $template_part . '.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH.'/' );  ?>
    </div>
    <div class="yith_ywraq_add_item_response-<?php echo $product_id ?> yith_ywraq_add_item_response_message <?php echo ( !$exists ) ? 'hide': 'show' ?> hide-when-removed" data-product_id="<?php echo esc_attr( $product_id)?>" style="display:<?php echo ( !$exists ) ? 'none': 'block' ?>"><?php echo ywraq_get_label( 'already_in_quote' )?></div>
    <div class="yith_ywraq_add_item_browse-list-<?php echo $product_id ?> yith_ywraq_add_item_browse_message  <?php echo ( !$exists ) ? 'hide': 'show' ?> hide-when-removed" style="display:<?php echo ( !$exists ) ? 'none': 'block' ?>" data-product_id="<?php echo esc_attr( $product_id)?>" ><a href="<?php echo  $rqa_url ?>"><?php echo $label_browse ?></a></div>
    <div class="yith_ywraq_add_item_product-response-<?php echo $product_id ?> yith_ywraq_add_item_product_message hide hide-when-removed" style="display:none" data-product_id="<?php echo esc_attr( $product_id)?>" ></div>
</div>

<div class="clear"></div>
