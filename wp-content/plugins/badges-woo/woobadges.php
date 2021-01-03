<?php
/**
 * Plugin Name: Badges Woo
 * Description: Show badges for each product on your store
 * Version: 1.0.8
 * Author: TaxarPro
 * Author URI: https://taxarpro.com
 * Text Domain: badges-woo
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 4.1.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    define('WOOBADGES_URL', plugin_dir_url( __FILE__ ));
    define('WOOBADGES_PATH', plugin_dir_path( __FILE__ ));
    define('WOOBADGES_VERSION', '1.0.8');
    if(!class_exists('WOOBADGES')) {
        class WOOBADGES {
    
            static $positions = array('none', 'center', 'top', 'left', 'bottom', 'right', 'left-top', 'right-top', 'left-bottom', 'right-bottom');
            
            public $featuredImage = false;

            function __construct(){
                add_action( 'init', array($this, 'load_text_domain') );
                add_filter('woocommerce_product_get_image', array($this, 'get_image'), 99, 6);
                add_filter('woocommerce_single_product_image_thumbnail_html', array($this, 'show_on_single_product'), 99, 2);
                add_filter('woocommerce_single_product_image_gallery_classes', array($this, 'show_single_product_badges'), 99, 2);
                
                add_action( 'wp_enqueue_scripts', array($this, 'load_styles') );
                add_action( 'admin_enqueue_scripts', array($this, 'load_script_admin') );
                add_action('save_post', array($this, 'save_badges_product'));
                add_action( 'add_meta_boxes', array($this, 'metabox_product_badge') );
   
            }

            function load_text_domain() {
                load_plugin_textdomain( 'badges-woo', false, dirname( plugin_basename(__FILE__) ) . '/languages' );
            } 
    
            static function get_positions() {
                $positions = apply_filters('woobadges_positions', self::$positions);
    
                return $positions;
            }
    
            function get_image($image, $product, $size, $attr, $placeholder, $image2){
                if(is_admin()) { return $image;}
                if(is_cart()) { return $image; }

                $woobadges_values = get_post_meta($product->get_id(), 'woobadge_product', true);
    
                if(!$woobadges_values or $woobadges_values['position'] == 'none') { return $image; }
                if(!isset($woobadges_values['opacity']) == '' or $woobadges_values['opacity'] == '') {
                    $opacity = '';
                }else{
                    $opacity = ';opacity:'. $woobadges_values['opacity'];
                }
    
                if(!isset($woobadges_values['fontSize']) or $woobadges_values['fontSize'] == '') {
                    $fontSize = ';font-size:12px';
                }else{
                    $fontSize = ';font-size:'. $woobadges_values['fontSize'];
                }
                $fontWeight = isset($woobadges_values['fontWeight']) ? $woobadges_values['fontWeight'] : 'normal';
                return '<div class="badge-position" style="position:relative">
                            <div class="badge-overlay">
                                <span class="'.$woobadges_values['position'].' badge" style="background-color:'.$woobadges_values['background'].';color:'.$woobadges_values['color'].$opacity.$fontSize.';font-weight:'.$fontWeight.'">'.$woobadges_values['text'].'</span>
                            </div>
                        '.$image.'
                        </div>'; 
            }

            function show_on_single_product($image, $attchID){
                if(is_admin()) { return $image;}
                if(is_cart()) { return $image; }
                if(!is_product()) { return $image; }

                global $product;

                $woobadges_values = get_post_meta($product->get_id(), 'woobadge_product', true);
                
                // var_dump($woobadges_values);
                if(!$woobadges_values or $woobadges_values['position'] == 'none') { return $image; }
                
                //Show single
                if(!strpos(json_encode($woobadges_values), "showSingle")) {
                    return $image;
                
                }
                if(!isset($woobadges_values['opacity']) == '' or $woobadges_values['opacity'] == '') {
                    $opacity = '';
                }else{
                    $opacity = ';opacity:'. $woobadges_values['opacity'];
                }
    
                if(!isset($woobadges_values['fontSize']) or $woobadges_values['fontSize'] == '') {
                    $fontSize = ';font-size:12px';
                }else{
                    $fontSize = ';font-size:'. $woobadges_values['fontSize'];
                }

                if(!isset($woobadges_values['zoomSingleProduct']) or $woobadges_values['zoomSingleProduct'] == '') {
                    $zoomSingleProduct = ';zoom:1';
                }else{
                    $zoomSingleProduct = ';zoom:'. $woobadges_values['zoomSingleProduct'];
                }
                

                $fontWeight = isset($woobadges_values['fontWeight']) ? $woobadges_values['fontWeight'] : 'normal';

                if(!$this->featuredImage or $this->featuredImage != $product->get_id()) {
                    $this->featuredImage = $product->get_id();
                }else{
                    return $image;
                }


                return '<div class="with-badges master-badge-container">
                            <div class="badge-overlay">
                                <span class="'.$woobadges_values['position'].' badge" style="background-color:'.$woobadges_values['background'].';color:'.$woobadges_values['color'].$opacity.$fontSize.$zoomSingleProduct.';font-weight:'.$fontWeight.'">'.$woobadges_values['text'].'</span>
                            </div>
                        </div>'.$image; 
            }

            function show_single_product_badges($classes){
                global $product;

                $woobadges_values = get_post_meta($product->get_id(), 'woobadge_product', true);

                if(!$woobadges_values or $woobadges_values['position'] == 'none') { return $classes; }

                if(!strpos(json_encode($woobadges_values), "showSingle")) {
                    return $classes;
                }


                $classes[] = 'wrapper-with-badges';
                return $classes;
            }
    
            function metabox_product_badge() {
                add_meta_box( 'woobadgeproduct', __( 'Badge Configuration', 'badges-woo' ), array($this, 'show_config_product'), 'product', 'side' );
            }
    
            function save_badges_product($post_id){
    
                if ( ! current_user_can( 'manage_options' ) ) {
                    return;
                }
                if(isset($_POST['woobadges_position'])) {
                    $position = sanitize_text_field($_POST['woobadges_position']);
                        $badgeInfo = array(
                            'position' => $position,
                            'opacity' => sanitize_text_field($_POST['woobadges_opacity']),
                            'text' => sanitize_text_field($_POST['woobadges_text']),
                            'background' => sanitize_text_field($_POST['woobadges_background']),
                            'color' => sanitize_text_field($_POST['woobadges_color']),
                            'fontSize' => sanitize_text_field($_POST['woobadges_fontSize']),
                            'zoomSingleProduct' => sanitize_text_field($_POST['woobadges_zoomSingleProduct']),
                            'fontWeight' => sanitize_text_field( $_POST['woobadges_fontWeight'] )
                        );
                        

                        if(isset($_POST['woobadges_showSingle'])) {
                            $badgeInfo['showSingle'] = '1';
                        }
                        update_post_meta($post_id, 'woobadge_product', $badgeInfo);
                }
    
    
            }
            function show_config_product(){
                require_once(WOOBADGES_PATH . 'product_metabox.php');
            }
    
            function load_styles() {
                wp_enqueue_style( 'badge-styles', WOOBADGES_URL . 'styles.css?v=' . WOOBADGES_VERSION );
                wp_enqueue_script( 'woobadges-script', plugins_url('frontend.scripts.js', __FILE__ ) . '?v=' . WOOBADGES_VERSION, array( 'jquery','flexslider' ), false, true );

            }
            function load_script_admin(){
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'woobadges-admin-script', plugins_url('scripts.js', __FILE__ ) . '?v=' . WOOBADGES_VERSION, array( 'wp-color-picker' ), false, true );
            }
    
        }
        
        $WOOBADGES = new WOOBADGES();
    }   
}