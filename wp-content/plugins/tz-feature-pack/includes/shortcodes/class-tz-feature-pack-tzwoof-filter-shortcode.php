<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 27/06/2019
 * Time: 10:10
 */

class TZ_WOOF_Filter_Shortcode{

    private function _taxonomy_depth($taxonomy_name = ''){
        if ( '' == $taxonomy_name ) return;

        $all_terms = get_terms( array(
            'taxonomy' => $taxonomy_name,
            'hide_empty' => false,
        ) );

        if ( ! count($all_terms) ) return 0;

        $deepest = 0;

        $args = array(
            'separator' => '#',
            'link' => false,
            'inclusive' => false
        );

        foreach ( $all_terms as $term ){
            $depth = count(explode('#', strip_tags(get_term_parents_list($term->term_id, $taxonomy_name, $args))));
            if ( $depth > $deepest ) $deepest = $depth;
        }

        return $deepest;

    }

    /**
     * @param $user_defined_attributes
     * @param $content
     * @param $shortcode_name
     */
    public function shortcode( $user_defined_attributes, $content, $shortcode_name ){

        $attributes = shortcode_atts(
            array(
                'taxonomy' => '',
                'hierarchical' => '1',
                'ajax_enabled' => '1',
                'terms_labels' => '',
            ),
            $user_defined_attributes,
            $shortcode_name
        );

        if ( !isset($attributes['taxonomy']) || ( '' == $attributes['taxonomy']) )
            return;

        $depth = $this->_taxonomy_depth($attributes['taxonomy']);

        var_dump($depth);

    }

}