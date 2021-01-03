<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 24/05/2018
 * Time: 13:35
 */

class TZ_Helper{

    public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }

        $located = self::locate_template( $template_name, $template_path, $default_path );

        if ( ! file_exists( $located ) ) {
            /* translators: %s template */
            _doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'themeszone-feature-pack' ), '<code>' . $located . '</code>' ), THEMESZONE_FEATURE_PACK_VERSION );
            return;
        }

        include $located;

    }

    public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
        if ( ! $template_path ) {
            $template_path = self::template_path();
        }

        if ( ! $default_path ) {
            $default_path = self::plugin_path() . '/public/partials/elementor/';
        }

        // Look within passed path within the theme - this is priority.
        $template = locate_template(
            array(
                trailingslashit( $template_path ) . $template_name,
                $template_name,
            )
        );

        // Get default template/.
        if ( ! $template ) {
            $template = $default_path . $template_name;
        }

        // Return what we found.
        return apply_filters( 'tz_locate_template', $template, $template_name, $template_path );
    }

    public static function plugin_path() {
        return untrailingslashit( THEMESZONE_FEATURE_PACK_ROOT );
    }

    public static function template_path() {
        return apply_filters( 'tz_template_path', 'elementor/' );
    }

}