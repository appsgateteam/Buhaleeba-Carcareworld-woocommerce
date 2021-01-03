<?php

namespace WDRPro\App\Helpers;

use Wdr\App\Helpers\Helper;
use Wdr\App\Helpers\Validation;
use Wdr\App\Helpers\Woocommerce;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class CoreMethodCheck
{
    public static function getConvertedFixedPrice($value, $type = ''){
        if(method_exists('\Wdr\App\Helpers\Woocommerce', 'getConvertedFixedPrice')){
            return Woocommerce::getConvertedFixedPrice($value, $type);
        }
        return $value;
    }

    public static function create_nonce($action = -1){
        if(method_exists('\Wdr\App\Helpers\Helper', 'create_nonce')){
            return Helper::create_nonce($action);
        }
        return '';
    }

    public static function validateRequest($method){
        if(method_exists('\Wdr\App\Helpers\Helper', 'validateRequest')){
            return Helper::validateRequest($method);
        }
        return false;
    }

    public static function isValidLicenceKey($licence_key){
        if(method_exists('\Wdr\App\Helpers\Validation', 'validateLicenceKay')){
            return Validation::validateLicenceKay($licence_key);
        }
        return false;
    }

    public static function hasAdminPrivilege(){
        if(method_exists('\Wdr\App\Helpers\Helper', 'hasAdminPrivilege')){
            return Helper::hasAdminPrivilege();
        }
        return false;
    }

    public static function getCleanHtml($html){
        if(method_exists('\Wdr\App\Helpers\Helper', 'getCleanHtml')){
            return Helper::getCleanHtml($html);
        } else {
            try {
                $html = html_entity_decode($html);
                $html =   preg_replace('/(<(script|style|iframe)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $html);
                $allowed_html = array(
                    'br' => array(),
                    'strong' => array(),
                    'span' => array('class' => array()),
                    'div' => array('class' => array()),
                    'p' => array('class' => array()),
                );
                return wp_kses($html, $allowed_html);
            } catch (\Exception $e){
                return '';
            }
        }
    }

    /**
     * check rtl site
     * @return bool
     */
    public static function isRTLEnable(){
        if(method_exists('\Wdr\App\Helpers\Woocommerce', 'isRTLEnable')){
            return Woocommerce::isRTLEnable();
        }
        return false;
    }
}