<?php

/**
 * Plugin Name: SMS Integration for WP Registration
 * Plugin URI: 
 * Description: integration plugin for Woocommerce registration with OTP
 * Author: YSG
 * Version: 1.0
 * Author URI: 
 */

add_action('woocommerce_register_form', 'woocommerce_register_form_add_phone_otp', 10, 1);

function woocommerce_register_form_add_phone_otp()
{
    include plugin_basename('/templates/includes/reg_phone_number.php');
}

wp_enqueue_script('ysg-wp-login-validate-script', plugins_url('/scripts/jquery.ysg.validate.js', __FILE__), array('jquery'), '1.0', true);
wp_enqueue_style('ysg-wp-login-validate-css', plugins_url('/css/ysg.otp.css', __FILE__));

add_action("wp_ajax_ysg_validate_phone", "send_otp_to_phone");
add_action("wp_ajax_nopriv_ysg_validate_phone", "send_otp_to_phone");

add_action("wp_ajax_ysg_validate_otp", "validate_the_otp");
add_action("wp_ajax_nopriv_ysg_validate_otp", "validate_the_otp");



// Your Account SID and Auth Token from twilio.com/console
$sid = 'ACd07c1c3f9dc1f57b893f7fc73d550865';
$token = '5f27249ba2de8459ea411175b6566c7b';
$tphone  = "+12056516189";

function send_otp_to_phone()
{
    //get phone number
    $phone_number = sanitize_text_field($_POST['phone']);
    $otp = rand(1000, 9999);

    // set a cookie for 1 year
    setcookie('ysg_to_validate_otp', $otp, time() + 31556926);

    $otp_mes = "Your OTP = " + $otp;
    $res = sendSMSUsingTwilio($phone_number, $otp);
    $a = array('sent' => "1");
    $json = json_encode($a);
    echo 1;
    wp_die();
}

/**
 * 
 */
function sendSMSUsingTwilio($phone_number, $otp)
{
    global $tphone;
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/ACd07c1c3f9dc1f57b893f7fc73d550865/Messages.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "To=$phone_number&From=$tphone&Body=$otp",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Basic QUNkMDdjMWMzZjlkYzFmNTdiODkzZjdmYzczZDU1MDg2NTo1ZjI3MjQ5YmEyZGU4NDU5ZWE0MTExNzViNjU2NmM3Yg==",
            "Content-Type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}


function validate_the_otp()
{
    //get phone number
    $otp_val = sanitize_text_field($_POST['phone']);

    if (!isset($_COOKIE['ysg_to_validate_otp'])) {
        json_encode(array(
            "error" => 1,
            "message" => "This OTP has expired, Please request another otp."
        ));
        echo "-2";
        exit;
    }

    if (isset($_COOKIE['ysg_to_validate_otp'])) {
        $otp = $_COOKIE['ysg_to_validate_otp'];
        if ($otp == $otp_val) {
            json_encode(array(
                "success" => 1,
                "message" => "Valid OTP."
            ));
            echo "1";
            exit;
        }
    }


    json_encode(array(
        "error" => 1,
        "message" => "This OTP is invalid, Please provide a valid otp."
    ));
    echo "-3";
    exit;
}
