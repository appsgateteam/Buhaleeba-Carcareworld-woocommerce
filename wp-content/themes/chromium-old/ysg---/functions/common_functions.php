<?php
add_filter('woocommerce_get_price_html', 'custom_price_message');
function custom_price_message($price)
{
    if (is_product()) {
        $new_price = $price . ' <span class="custom-price-prefix">' . __('(VAT Included)') . '</span>';
        return $new_price;
    }
    return $price;
}

// Product Brand List
function doProductBrandsPage()
{
    $product_brands = getProductBrands();
    ob_start();
    include_once get_template_directory() . '/ysg/includes/product_brands.php';

    $text = ob_get_contents();

    ob_end_clean();

    return $text;
}
// product brands shortcode
add_shortcode('ysg_product_brands', 'doProductBrandsPage');


function getProductBrands()
{
    $terms = get_terms(array(
        'taxonomy' => 'pa_product-brand',
        'hide_empty' => true,
    ));
    if (empty($terms)) {
        return array();
    }

    $all_terms = array();
    foreach ($terms as $term) {
        $at = (array) $term;
        $image = get_field('image', $term);
        $at['image'] = $image;

        $all_terms[] = (object) $at;
    }
    return $all_terms;
}

add_filter('posts_search', 'woocommerce_search_product_tag_extended', 999, 2);
function woocommerce_search_product_tag_extended($search, $query)
{
    global $wpdb, $wp;

    $qvars = $wp->query_vars;

    if (is_admin() || empty($search) ||  !(isset($qvars['ptype'])
        && isset($qvars['post_type']) && !empty($qvars['ptype'])
        && $qvars['post_type'] === 'product')) {
        return $search;
    }

    // Here set your custom taxonomy
    $taxonomy = 'ysg_product_type'; // WooCommerce product tag

    // Get the product Ids
    $ids = get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'tax_query'       => array(array(
            'taxonomy' => $taxonomy,
            'field'    => 'name',
            'terms'    => esc_attr($qvars['ptype']),
        )),
    ));

    if (count($ids) > 0) {
        $search = str_replace('AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode(',', $ids) . ")) OR (", $search);
    }
    return $search;
}
add_filter('posts_search', 'woocommerce_search_product_mega_extended', 999, 2);
function woocommerce_search_product_mega_extended($search, $query)
{
    global $wpdb, $wp;

    $qvars = $wp->query_vars;

    if (is_admin() || empty($search) ||  !(isset($qvars['s'])
        && isset($qvars['post_type']) && !empty($qvars['s'])
        && $qvars['post_type'] === 'product')) {
        return $search;
    }

    // SETTINGS:
    $taxonomies = array('product_tag', 'product_cat'); // Here set your custom taxonomies in the array
    $meta_keys  = array('_sku'); // Here set your product meta key(s) in the array

    // Initializing tax query
    $tax_query  = count($taxonomies) > 1 ? array('relation' => 'OR') : array();

    // Loop through taxonomies to set the tax query
    foreach ($taxonomies as $taxonomy) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field'    => 'name',
            'terms'    => esc_attr($qvars['s']),
        );
    }

    // Get the product Ids from taxonomy(ies)
    $tax_query_ids = (array) get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'tax_query'       => $tax_query,
    ));

    // Initializing meta query
    $meta_query = count($meta_keys) > 1 ? array('relation' => 'OR') : array();

    // Loop through taxonomies to set the tax query
    foreach ($taxonomies as $taxonomy) {
        $meta_query[] = array(
            'key'     => '_sku',
            'value'   => esc_attr($qvars['s']),
        );
    }

    // Get the product Ids from custom field(s)
    $meta_query_ids = (array) get_posts(array(
        'posts_per_page'  => -1,
        'post_type'       => 'product',
        'post_status'     => 'publish',
        'fields'          => 'ids',
        'meta_query'      => $meta_query,
    ));

    $product_ids = array_unique(array_merge($tax_query_ids, $meta_query_ids)); // Merge Ids in one array  with unique Ids

    if (sizeof($product_ids) > 0) {
        $search = str_replace('AND (((', "AND ((({$wpdb->posts}.ID IN (" . implode(',', $product_ids) . ")) OR (", $search);
    }
    return $search;
}
// Adds image to WooCommerce order emails
function w3p_add_image_to_wc_emails($args)
{
    $args['show_image'] = true;
    $args['image_size'] = array(100, 50);
    $args['show_sku'] = true;
    return $args;
}
add_filter('woocommerce_email_order_items_args', 'w3p_add_image_to_wc_emails');


if (!function_exists('ysg_resize_image')) {

    /**
     * Functin that generates custom thumbnail for given attachment
     *
     * @param null $attach_id id of attachment
     * @param null $attach_url URL of attachment
     * @param int $width desired height of custom thumbnail
     * @param int $height desired width of custom thumbnail
     * @param bool $crop whether to crop image or not
     *
     * @return array returns array containing img_url, width and height
     *
     * @see magazinevibe_edge_get_attachment_id_from_url()
     * @see get_attached_file()
     * @see wp_get_attachment_url()
     * @see wp_get_image_editor()
     */
    function ysg_resize_image($attach_id = null, $attach_url = null, $width = null, $height = null, $crop = true)
    {
        $return_array = array();

        //is attachment id empty?
        if (empty($attach_id) && $attach_url !== '') {
            //get attachment id from url
            $attach_id = ysg_get_attachment_id_from_url($attach_url);
        }

        if (!empty($attach_id) && (isset($width) && isset($height))) {

            //get file path of the attachment
            $img_path = get_attached_file($attach_id);
            if (empty($img_path)) {
                $return_array = array(
                    'img_url' => '',
                    'img_width' => '',
                    'img_height' => ''
                );

                return $return_array;
            }

            if ($width === -1) {
                list($uploaded_width, $uploaded_height) = getimagesize($img_path);
                $width = number_format(($height / $uploaded_height) * $uploaded_width, 0, "", "");
            }

            if ($height === -1) {
                list($uploaded_width, $uploaded_height) = getimagesize($img_path);
                $height = number_format((($uploaded_height) * ($width / $uploaded_width)), 0, "", "");
            }

            //get attachment url
            $img_url = wp_get_attachment_url($attach_id);

            //break down img path to array so we can use it's components in building thumbnail path
            $img_path_array = pathinfo($img_path);

            //build thumbnail path
            $new_img_path = $img_path_array['dirname'] . '/' . $img_path_array['filename'] . '-' . $width . 'x' . $height . '.' . $img_path_array['extension'];

            //build thumbnail url
            $new_img_url = str_replace($img_path_array['filename'], $img_path_array['filename'] . '-' . $width . 'x' . $height, $img_url);

            //check if thumbnail exists by it's path
            if (!file_exists($new_img_path)) {
                //get image manipulation object
                $image_object = wp_get_image_editor($img_path);

                if (!is_wp_error($image_object)) {
                    //resize image and save it new to path
                    $image_object->resize($width, $height, $crop);
                    $image_object->save($new_img_path);

                    //get sizes of newly created thumbnail.
                    ///we don't use $width and $height because those might differ from end result based on $crop parameter
                    $image_sizes = $image_object->get_size();

                    $width = $image_sizes['width'];
                    $height = $image_sizes['height'];
                }
            }

            //generate data to be returned
            $return_array = array(
                'img_url' => $new_img_url,
                'img_width' => $width,
                'img_height' => $height
            );
        }

        //attachment wasn't found, probably because it comes from external source
        elseif ($attach_url !== '' && (isset($width) && isset($height))) {
            //generate data to be returned
            $return_array = array(
                'img_url' => $attach_url,
                'img_width' => $width,
                'img_height' => $height
            );
        }

        return $return_array;
    }
}

if (!function_exists('ysg_generate_thumbnail')) {

    /**
     * Generates thumbnail img tag. It calls magazinevibe_edge_resize_image function which resizes img on the fly
     *
     * @param null $attach_id attachment id
     * @param null $attach_url attachment URL
     * @param  int$width width of thumbnail
     * @param int $height height of thumbnail
     * @param bool $crop whether to crop thumbnail or not
     *
     * @return string generated img tag
     *
     * @see magazinevibe_edge_resize_image()
     * @see magazinevibe_edge_get_attachment_id_from_url()
     */
    function ysg_generate_thumbnail($attach_id = null, $attach_url = null, $width = null, $height = null, $crop = true, $vars = array())
    {
        //is attachment id empty?
        if (empty($attach_id)) {
            //get attachment id from attachment url
            $attach_id = ysg_get_attachment_id_from_url($attach_url);
        }

        if (!empty($attach_id) || !empty($attach_url)) {
            $img_info = ysg_resize_image($attach_id, $attach_url, $width, $height, $crop);
            $img_alt = !empty($attach_id) ? get_post_meta($attach_id, '_wp_attachment_image_alt', true) : '';

            if (is_array($img_info) && count($img_info)) {

                $imgclass = isset($vars['class']) ? 'class="' . $vars['class'] . '"' : "";
                $other = isset($vars['other']) ? $vars['other'] : "";

                if (isset($vars['display']) && $vars['display'] == "div") {
                    return '<div ' . $imgclass . ' style="background-image: url( \' ' . $img_info['img_url'] . '\')" ' . $other . '></div>';
                }
                if (isset($vars['display']) && $vars['display'] == "url") {
                    return $img_info['img_url'];
                } else {
                    return '<img src="' . $img_info['img_url'] . '" alt="' . $img_alt . '" width="' . $img_info['img_width'] . '" height="' . $img_info['img_height'] . '" ' . $imgclass . ' ' . $other . ' />';
                }
            }
        }

        return '';
    }
}

if (!function_exists('ysg_get_attachment_id_from_url')) {

    /**
     * Function that retrieves attachment id for passed attachment url
     * @param $attachment_url
     * @return null|string
     */
    function ysg_get_attachment_id_from_url($attachment_url)
    {
        global $wpdb;
        $attachment_id = '';

        //is attachment url set?
        if ($attachment_url !== '') {
            //prepare query

            $query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid=%s", $attachment_url);

            //get attachment id
            $attachment_id = $wpdb->get_var($query);
        }

        //return id
        return $attachment_id;
    }
}
