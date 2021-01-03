<?php
if (!defined('ABSPATH')) {
    exit;
}
$range = null;
?>
<!-- Set discount Start-->
<div class="add_set_range" style="display:none;">
    <?php
    $set_range = "{i}";
    include 'set-range.php';
    ?>
</div>
<div class="wdr_set_discount" style="display:none;">
    <div class="wdr-simple-discount-main wdr-set-discount-main">
        <div class="wdr-simple-discount-inner">
            <div class="set_general_adjustment wdr-select-filed-hight">
                <label class="label_font_weight"><?php _e('Count Quantities by:', WDR_PRO_TEXT_DOMAIN); ?><span style="" class="woocommerce-help-tip" title="<?php _e("Filter set above : 
This will count the quantities of products set in the “Filter” section.
Example: If you selected a few categories there, it will count the quantities of products in those categories added in cart. If you selected a few products in the filters section, then it will count the quantities together.

Example: Let’s say, you wanted to offer a Bulk Quantity discount for Category A and chosen Category A in the filters.

So when a customer adds 1 quantity each of X, Y and Z from Category A, then the count here is 3. 

Individual Product :

This counts the total quantity of each product / line item separately.
Example : If a customer wanted to buy 2 quantities of Product A,  3 quantities of Product B, then count will be maintained at the product level. 
2 - count of Product A
3 - Count of Product B

In case of variable products, the count will be based on each variant because WooCommerce considers a variant as a product itself.  

All variants in each product together :
Useful when applying discounts based on variable products and you want the quantity to be counted based on the parent product.
Example: 
Say, you have Product A - Small, Medium, Large.
If a customer buys  2 of Product A - Small,  4 of Product A - Medium,  6 of Product A - Large, then the count will be: 6+4+2 = 12
", WDR_PRO_TEXT_DOMAIN); ?>"></span></label>
                <select name="set_adjustments[operator]" class="wdr-set-type set_discount_select awdr_mode_of_operator">
                    <option value="product_cumulative" title="<?php _e('This will count the quantities of products set in the “Filter” section.
Example: If you selected a few categories there, it will count the quantities of products in those categories added in cart. If you selected a few products in the filters section, then it will count the quantities together.

Example: Let’s say, you wanted to offer a Bulk Quantity discount for Category A and chosen Category A in the filters.

So when a customer adds 1 quantity each of X, Y and Z from Category A, then the count here is 3.', WDR_PRO_TEXT_DOMAIN) ?>" <?php if ($set_adj_operator == 'product_cumulative') {
                        echo 'selected';
                    } ?>><?php _e('Filters set above', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="product" title="<?php _e('This counts the total quantity of each product / line item separately.
Example : If a customer wanted to buy 2 quantities of Product A,  3 quantities of Product B, then count will be maintained at the product level. 
2 - count of Product A
3 - Count of Product B

In case of variable products, the count will be based on each variant because WooCommerce considers a variant as a product itself.  
', WDR_PRO_TEXT_DOMAIN) ?>" <?php if ($set_adj_operator == 'product') {
                        echo 'selected';
                    } ?>><?php _e('Individual product', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="variation" title="<?php _e('Useful when applying discounts based on variable products and you want the quantity to be counted based on the parent product.
Example: 
Say, you have Product A - Small, Medium, Large.
If a customer buys  2 of Product A - Small,  4 of Product A - Medium,  6 of Product A - Large, then the count will be: 6+4+2 = 12', WDR_PRO_TEXT_DOMAIN) ?>" <?php if ($set_adj_operator == 'variation') {
                        echo 'selected';
                    } ?>><?php _e('All variants in each product together', WDR_PRO_TEXT_DOMAIN) ?></option>
                </select>
            </div>
            <div class="awdr-example"></div>
        </div>

        <div class="set_range_setter_group"><?php
            $set_range = 1;
            if ($set_adj_ranges) {
                foreach ($set_adj_ranges as $range) {
                    if (isset($range->from) && !empty($range->from) || isset($range->to) && !empty($range->to) || isset($range->value) && !empty($range->value)) {
                        include 'set-range.php';
                        $set_range++;
                    }
                }
            } else {
                include 'set-range.php';
            } ?>

        </div>
        <div style="padding-left: 14px;">
            <button type="button" class="button add_discount_elements" data-discount-method="add_set_range"
                    data-append="set_range_setter"><?php _e('Add Range', WDR_PRO_TEXT_DOMAIN) ?></button>
        </div>
        <div class="apply_discount_as_cart_section">
            <?php $is_enable_rtl = \WDRPro\App\Helpers\CoreMethodCheck::isRTLEnable();?>
            <div class="apply_as_cart_checkbox awdr_rtl_compatible  <?php echo (!$is_enable_rtl) ? 'page__toggle' : ''; ?>">
                <label class="<?php echo (!$is_enable_rtl) ? 'toggle' : ''; ?>">
                    <input class="<?php echo (!$is_enable_rtl) ? 'toggle__input' : ''; ?>  apply_fee_coupon_checkbox" type="checkbox"
                           name="set_adjustments[apply_as_cart_rule]" <?php echo (isset($set_adj_as_cart) && !empty($set_adj_as_cart)) ? 'checked' : '' ?> value="1">
                    <span class="<?php echo (!$is_enable_rtl) ? 'toggle__label' : ''; ?>"><span
                                class="<?php echo (!$is_enable_rtl) ? 'toggle__text toggle_tic' : ''; ?>"><?php _e('Show discount in cart as coupon instead of changing the product price ?', WDR_PRO_TEXT_DOMAIN); ?></span></span>
                </label>
            </div>
            <div class="simple_discount_value wdr-input-filed-hight apply_fee_coupon_label" style="<?php echo (isset($set_adj_as_cart) && !empty($set_adj_as_cart)) ? '' : 'display: none;' ?> <?php echo ($is_enable_rtl) ? 'padding-top: 0px !important;' : ''; ?>">
                <input name="set_adjustments[cart_label]"
                       type="text"
                       value="<?php echo (isset($set_adj_as_cart_label)) ? $set_adj_as_cart_label : ''; ?>"
                       placeholder="Discount Label">
            </div>
        </div>
    </div>
</div>
<!-- Set discount End-->