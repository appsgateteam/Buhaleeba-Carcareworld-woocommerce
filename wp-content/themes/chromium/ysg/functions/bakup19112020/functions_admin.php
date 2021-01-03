<?php

add_filter('manage_edit-shop_order_columns', function ($columns) {
    $columns['ysg_tracking_status'] = 'Tracking Status';
    return $columns;
});

add_action('manage_shop_order_posts_custom_column', function ($column) {
    global $post;
    if ('ysg_tracking_status' === $column) {
        $order = wc_get_order($post->ID);
        echo get_post_meta($order->id, 'ysg_tracking_status', true);;
    }
});

add_filter('manage_edit-product_columns', 'change_columns_filter', 10, 1);
function change_columns_filter($columns)
{
    unset($columns['total_subscribers']);
    unset($columns['taxonomy-ysg_product_type']);
    unset($columns['featured']);
    unset($columns['product_tag']);
    return $columns;
}


add_action('admin_menu', 'cities_url');
function cities_url()
{
    add_menu_page('linked_url', 'Shipping Cities / Areas', 'read', '/edit-tags.php?taxonomy=ysg_wc_shipping_cities', '', 'dashicons-text', 58);
}
