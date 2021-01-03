/*!
 * jQuery Validation Plugin v1.12.0
 *
 * http://jqueryvalidation.org/
 *
 * Copyright (c) 2014 Jörn Zaefferer
 * Released under the MIT license
 */

(function ($) {
    $('.woocommerce-form-register__submit').hide();
    //alert(ajax_postajax.ajaxurl);
    jQuery('body').on('click', '.js_verify_otp', function () {
        $('.js_error_message').html("").hide();
        var phone = $('.js_the_phone_number').val();
        $.post('http://127.0.0.1/carcareworld/wp-admin/admin-ajax.php', {
            action: 'ysg_validate_phone',
            phone: phone
        }, function (response) {
            if (response == 1) {
                //validate otp
                $('.js_show_validate_otp').addClass('active')
            }
        });
    });

    jQuery('body').on('click', '.js_validate_otp', function () {
        $('.js_error_message').html("").hide();

        var phone = $('.js_the_otp_value').val();
        $.post('http://127.0.0.1/carcareworld/wp-admin/admin-ajax.php', {
            action: 'ysg_validate_otp',
            phone: phone
        }, function (response) {
            if (response == "1") {
                $('.js_error_message').html("").hide();
                $('.js_show_validate_otp').hide()
                $('.js_hide_phone_number').show()
                $('.js_verify_otp').html("Verified");
                $('.woocommerce-form-register__submit').show();
            } else {
                //not valid
                m = "This OTP is invalid, Please provide a valid otp";
                if (response == "-2") {
                    m = "This OTP has expired, Please request another otp."
                }
                $('.js_error_message').html(m).show();
                $('.woocommerce-form-register__submit').hide();
            }
        });
    });
}(jQuery));

