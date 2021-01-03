<?php
/**
 * Add filter taxonomy for Attachments
 */
function tz_add_attachment_filters() {
  $labels = array(
        'name'              => esc_html__('Gallery Filters', 'tz-feature-pack'),
        'singular_name'     => esc_html__('Gallery Filter', 'tz-feature-pack'),
        'search_items'      => esc_html__('Search Filters', 'tz-feature-pack'),
        'all_items'         => esc_html__('All Filters', 'tz-feature-pack'),
        'parent_item'       => esc_html__('Gallery Filter', 'tz-feature-pack'),
        'parent_item_colon' => esc_html__('Gallery Filter:', 'tz-feature-pack'),
        'edit_item'         => esc_html__('Edit Gallery Filter', 'tz-feature-pack'),
        'update_item'       => esc_html__('Update Gallery Filter', 'tz-feature-pack'),
        'add_new_item'      => esc_html__('Add New Gallery Filter', 'tz-feature-pack'),
        'new_item_name'     => esc_html__('New Gallery Filter Name', 'tz-feature-pack'),
        'menu_name'         => esc_html__('Filters', 'tz-feature-pack'),
  );

  $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'query_var' => 'true',
        'rewrite' => 'true',
        'show_admin_column' => 'true',
  );

  register_taxonomy( 'gallery-filter', 'attachment', $args );
}
add_action( 'init', 'tz_add_attachment_filters');


/**
 * Add Custom Labels taxonomy for Attachments
 */
function tz_add_custom_product_labels() {
  $labels = array(
        'name'              => esc_html__('Custom Labels', 'tz-feature-pack'),
        'singular_name'     => esc_html__('Custom Label', 'tz-feature-pack'),
        'search_items'      => esc_html__('Search Labels', 'tz-feature-pack'),
        'all_items'         => esc_html__('All Labels', 'tz-feature-pack'),
        'parent_item'       => esc_html__('Parent Custom Label', 'tz-feature-pack'),
        'parent_item_colon' => esc_html__('Parent Custom Label:', 'tz-feature-pack'),
        'edit_item'         => esc_html__('Edit Custom Label', 'tz-feature-pack'),
        'update_item'       => esc_html__('Update Custom Label', 'tz-feature-pack'),
        'add_new_item'      => esc_html__('Add New Custom Label', 'tz-feature-pack'),
        'new_item_name'     => esc_html__('New Custom Label Name', 'tz-feature-pack'),
        'menu_name'         => esc_html__('Custom Labels', 'tz-feature-pack'),
  );

  $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'query_var' => true,
        'rewrite' => true,
        'show_admin_column' => false,
  );

  register_taxonomy( 'product-custom-label', 'product', $args );
}
if ( class_exists('WooCommerce') ) {
  add_action( 'init', 'tz_add_custom_product_labels');

  function tz_the_term_image_taxonomy( $taxonomy ) {
		return array( 'product-custom-label' );
	}
	add_filter( 'taxonomy-term-image-taxonomy', 'tz_the_term_image_taxonomy' );
}
