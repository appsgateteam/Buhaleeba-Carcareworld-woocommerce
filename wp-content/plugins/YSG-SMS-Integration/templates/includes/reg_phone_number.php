<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label for="reg_phone">
        <?php esc_html_e('Phone', 'woocommerce'); ?>&nbsp;<span class="required">*</span>
    </label>

    <div class="hold_phone_number">
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text js_the_phone_number" name="phone" id="ysg_otp_phone" value="<?php echo (!empty($_POST['phone'])) ? esc_attr(wp_unslash($_POST['phone'])) : ''; ?>" />
        <a href="javascript:void(0)" class="send_verify js_verify_otp">Verify</a>
        <div class="js_hide_phone_number the_hide_phone_number"></div>
    </div>

    <div class="hold_phone_number validate_otp js_show_validate_otp">
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text js_the_otp_value" name="otp_value" id="ysg_otp_phone" value="" />
        <a href="javascript:void(0)" class="send_verify js_validate_otp">Validate</a>
    </div>

    <div class="error_box_ys js_error_message">
        Please enter the valid message
    </div>
</p>