<?php

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
     * @param int$width width of thumbnail
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
