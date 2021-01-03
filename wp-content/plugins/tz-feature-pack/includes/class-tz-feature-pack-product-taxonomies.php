<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 25/06/2019
 * Time: 14:38
 */

class Tz_Feature_Pack_Product_Taxonomy {

    public function register_brand_year_model_taxonomy(){

        $labels = array(
            'name'                       => esc_html__('Brands/Models/Years', 'tz-feature-pack'),
            'singular_name'              => esc_html__('Brand/Model/Year', 'tz-feature-pack'),
            'menu_name'                  => esc_html__('Brands/Models/Years', 'tz-feature-pack'),
            'all_items'                  => esc_html__('All Brands/Models/Years', 'tz-feature-pack'),
            'parent_item'                => esc_html__('Parent Brand', 'tz-feature-pack'),
            'parent_item_colon'          => esc_html__('Parent Brand:', 'tz-feature-pack'),
            'new_item_name'              => esc_html__('New Brand/Model/Year', 'tz-feature-pack'),
            'add_new_item'               => esc_html__('Add New Brand/Model/Year', 'tz-feature-pack'),
            'edit_item'                  => esc_html__('Edit Brand/Model/Year', 'tz-feature-pack'),
            'update_item'                => esc_html__('Update Brand/Model/Year', 'tz-feature-pack'),
            'separate_items_with_commas' => esc_html__('Separate Brand/Model/Year with commas', 'tz-feature-pack'),
            'search_items'               => esc_html__('Search Brand/Model/Year', 'tz-feature-pack'),
            'add_or_remove_items'        => esc_html__('Add or remove Brand/Model/Year', 'tz-feature-pack'),
            'choose_from_most_used'      => esc_html__('Choose from the most used Brand/Model/Year', 'tz-feature-pack'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
        );

       if ( true === apply_filters( 'chromium_custom_brand_model_year_taxonomy', false ) )
            register_taxonomy( 'brand_year_model', 'product', $args );
    }

    public function register_tire_size_taxonomy(){

        $labels = array(
            'name'                       => esc_html__('Tire Sizes', 'tz-feature-pack'),
            'singular_name'              => esc_html__('Tire Sizes', 'tz-feature-pack'),
            'menu_name'                  => esc_html__('Tire Sizes', 'tz-feature-pack'),
            'all_items'                  => esc_html__('All Tire Sizes', 'tz-feature-pack'),
            'parent_item'                => esc_html__('Parent Tire Size', 'tz-feature-pack'),
            'parent_item_colon'          => esc_html__('Parent Tire Size:', 'tz-feature-pack'),
            'new_item_name'              => esc_html__('New Tire Size', 'tz-feature-pack'),
            'add_new_item'               => esc_html__('Add New Tire Size', 'tz-feature-pack'),
            'edit_item'                  => esc_html__('Edit Tire Size', 'tz-feature-pack'),
            'update_item'                => esc_html__('Update Tire Size', 'tz-feature-pack'),
            'separate_items_with_commas' => esc_html__('Separate Tire Sizes with commas', 'tz-feature-pack'),
            'search_items'               => esc_html__('Search Tire Sizes', 'tz-feature-pack'),
            'add_or_remove_items'        => esc_html__('Add or remove Tire Sizes', 'tz-feature-pack'),
            'choose_from_most_used'      => esc_html__('Choose from the most used Tire Sizes', 'tz-feature-pack'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'description' => esc_html__('Add Tire Sizes in the following hierarchy: Width -> Aspect Ration -> Diameter. So that Width Paremeter is a parent for Aspect Ratio and Diameter is a child value of Aspect Ratio.')
        );

        if ( true === apply_filters( 'chromium_custom_tire_sizes_taxonomy', false ) )
            register_taxonomy( 'tire_sizes', 'product', $args );
    }

    function disable_popular_ontop( $args ) {
        //If this is your required taxonomy then disable the popular on top.
        if ( ( $args['taxonomy'] == 'brand_year_model') || ( $args['taxonomy'] == 'tire_sizes') ) {
            $args['checked_ontop'] = false;
        }

        return $args;
    }

}