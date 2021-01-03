<?php
if (!defined('ABSPATH')) {
    exit;
}
use Wdr\App\Helpers\Helper;

$buyx_gety_adjustment = null;
?>
<!-- Buy X Get Y Start -->
<div class="add_buyx_gety_range" style="display:none;">
    <?php
    $buyx_gety_index = "{i}";
    include 'buy-x-get-y-range.php';
    $filter_set_above_tip = $individual_product_tip = $variants_together_tip = $default_description_tip = '';
   if(class_exists('\Wdr\App\Helpers\Helper')) {
       if(method_exists('\Wdr\App\Helpers\Helper', 'bogoToolTipDescriptionForFilterTogether')){
           $filter_set_above_tip = Helper::bogoToolTipDescriptionForFilterTogether();
       }
       if(method_exists('\Wdr\App\Helpers\Helper', 'bogoToolTipDescriptionForIndividualProduct')){
           $individual_product_tip = Helper::bogoToolTipDescriptionForIndividualProduct();
       }
       if(method_exists('\Wdr\App\Helpers\Helper', 'bogoToolTipDescriptionForvariants')){
           $variants_together_tip = Helper::bogoToolTipDescriptionForvariants();
       }
   }
    if($get_buyx_gety_operator == 'variation'){
        $default_description_tip = $variants_together_tip;
    }elseif ($get_buyx_gety_operator == 'product'){
        $default_description_tip = $individual_product_tip;
    }else{
        $default_description_tip = $filter_set_above_tip;
    }
    ?>
</div>

<div class="wdr_buy_x_get_y_discount" style="display:none;">
    <div class="wdr-discount-block">
        <div class="awdr-get-y-general-settings">
            <div class="buyx-gety-cumulative-option wdr-select-filed-hight">
                <select name="buyx_gety_adjustments[type]" class="awdr-left-align select_bxgy_type">
                    <option value="0"><?php _e('Select Types', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="bxgy_product" <?php if ($get_buyx_gety_types == 'bxgy_product') {
                        echo 'selected';
                    } ?>><?php _e('Buy X Get Y - Products ', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="bxgy_category" <?php if ($get_buyx_gety_types == 'bxgy_category') {
                        echo 'selected';
                    } ?>><?php _e('Buy X Get Y - Categories', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="bxgy_all" <?php if ($get_buyx_gety_types == 'bxgy_all') {
                        echo 'selected';
                    } ?>><?php _e('Buy X Get Y - All', WDR_PRO_TEXT_DOMAIN) ?></option>
                </select>
                <span class="wdr_desc_text awdr-clear-both"><?php _e('Get Y Discount Type ', WDR_PRO_TEXT_DOMAIN); ?></span>
            </div>
            <div class="buyx-gety-cumulative-option wdr-select-filed-hight bxgy_type_selected" style="padding-left:5px; <?php echo (is_null($get_buyx_gety_types) || $get_buyx_gety_types == '')? 'display: none;': ''?>">
                <select name="buyx_gety_adjustments[operator]" class="awdr-left-align awdr_mode_of_operator">
                    <option value="product_cumulative" title="<?php echo $filter_set_above_tip;?>" <?php if ($get_buyx_gety_operator == 'product_cumulative') {
                        echo 'selected';
                        } ?>><?php _e('Filters set above', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="product" title="<?php echo $individual_product_tip;?>" <?php if ($get_buyx_gety_operator == 'product') {
                        echo 'selected';
                    } ?>><?php _e('Individual Product', WDR_PRO_TEXT_DOMAIN) ?></option>
                    <option value="variation" title="<?php echo $variants_together_tip;?>" <?php if ($get_buyx_gety_operator == 'variation') {
                        echo 'selected';
                    } ?>><?php _e('All variants in each product together', WDR_PRO_TEXT_DOMAIN) ?></option>
                </select>

                <span class="wdr_desc_text awdr-clear-both"><?php _e('Buy X Count Based on ', WDR_PRO_TEXT_DOMAIN); ?>  <span style="" class="woocommerce-help-tip awdr-bxgy-dynamic-tip" title="<?php echo $default_description_tip;?>"></span></span>

            </div>
            <div class="awdr-example" <!--style="width: min-content"-->></div>
            <div class="awdr-get-y-middle-section">
                <?php

                $is_enable_rtl = \WDRPro\App\Helpers\CoreMethodCheck::isRTLEnable();;
                $rtl_compatibility_P_start = ($is_enable_rtl) ? "<p>" : '';
                $rtl_compatibility_P_end = ($is_enable_rtl) ? "</p>" : '';
                ?>
                <div class="awdr-buyx-gety-cumulative-radio-mode wdr-select-filed-hight bxgy_type_selected" style="<?php echo (is_null($get_buyx_gety_types) || $get_buyx_gety_types == '')? 'display: none;': ''?>">
                    <?php echo $rtl_compatibility_P_start;?><label class="auto_add" style="<?php echo ($get_buyx_gety_types == 'bxgy_category' || $get_buyx_gety_types == "bxgy_all") ? 'display:none':'';?>"><input type="radio" name="buyx_gety_adjustments[mode]"  value="auto_add" <?php echo (($get_buyx_gety_mode == '' && $get_buyx_gety_types == 'bxgy_product') || $get_buyx_gety_mode == 'auto_add') ? 'checked="checked"' : '';?>><?php _e('Auto add', WDR_PRO_TEXT_DOMAIN); ?></label><?php echo $rtl_compatibility_P_end;?>
                    <?php echo $rtl_compatibility_P_start;?><label class="cheapest"> <input type="radio" name="buyx_gety_adjustments[mode]"  value="cheapest" <?php echo ($get_buyx_gety_mode == 'cheapest') ? 'checked="checked"' : '';?>><?php _e('Cheapest', WDR_PRO_TEXT_DOMAIN); ?></label><?php echo $rtl_compatibility_P_end;?>
                    <?php echo $rtl_compatibility_P_start;?><label class="highest"> <input type="radio" name="buyx_gety_adjustments[mode]"  value="highest" <?php echo ($get_buyx_gety_mode == 'highest') ? 'checked="checked"' : '';?>><?php _e('Highest', WDR_PRO_TEXT_DOMAIN); ?></label><?php echo $rtl_compatibility_P_end;?>
                </div>
                <span class="wdr_desc_text awdr-clear-both bxgy_type_selected"><?php _e('Mode of Apply', WDR_PRO_TEXT_DOMAIN); ?></span>
            </div>
        </div>

        <div class="wdr-simple-discount-main wdr-buyx-gety-discount-main bxgy_type_selected" style="<?php echo (is_null($get_buyx_gety_types) || $get_buyx_gety_types == '')? 'display: none;': ''?>">
            <div class="awdr_buyx_gety_range_group awdr_bogo_main">
                <?php
                $buyx_gety_index = 1;
                if (isset($get_buyx_gety_adjustments) && !empty($get_buyx_gety_adjustments)) {
                    foreach ($get_buyx_gety_adjustments as $buyx_gety_adjustment) {
                        include 'buy-x-get-y-range.php';
                        $buyx_gety_index++;
                    }
                } else {
                    include 'buy-x-get-y-range.php';
                }
                ?>
            </div>
            <div class="add-condition-and-filters awdr-discount-add-row hide_gety_recursive" style="<?php echo (isset($buyx_gety_adjustment->recursive) && !empty($buyx_gety_adjustment->recursive)) ? 'display:none' : '';?>">
                <button type="button" class="button add_discount_elements"
                        data-discount-method="add_buyx_gety_range"
                        data-append="awdr_buyx_gety_range_setter"
                        data-next-starting-value=".buyx_gety_individual_range"><?php _e('Add Range', WDR_PRO_TEXT_DOMAIN) ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Buy X Get Y End -->