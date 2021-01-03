<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 30/04/2018
 * Time: 12:47
 */

class ThemesZone_Feature_Pack_Elementor_HF{

    public $plugin_name;

    public $plugin_version;

    /**
     * Current theme template
     *
     * @var String
     */
    public $template;

    /**
     * Instance of Elemenntor Frontend class.
     *
     * @var \Elementor\Frontend()
     */
    private static $elementor_instance;
    /**
     * Constructor
     */
    function __construct( $plugin_name, $plugin_version) {
        $this->plugin_name = $plugin_name;
        $this->plugin_version = $plugin_version;

        $this->template = get_template();
        if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
            self::$elementor_instance = \Elementor\Plugin::instance();
        }

        add_shortcode( 'tz-elementor-section', [ $this, 'elementor_section_shortcode' ] );
    }


    /**
     * Enqueue styles and scripts.
     */
    public function enqueue_scripts() {

        if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {

            if ( class_exists( '\Elementor\Plugin' ) ) {
                $elementor = \Elementor\Plugin::instance();
                $elementor->frontend->enqueue_styles();
            }

            if ( class_exists( '\ElementorPro\Plugin' ) ) {
                $elementor_pro = \ElementorPro\Plugin::instance();
                $elementor_pro->enqueue_styles();
            }

            if ( self::header_enabled() ) {
                $css_file = new Elementor\Core\Files\CSS\Post( self::get_header_id() );
                $css_file->enqueue();
            }

            if ( self::footer_enabled() ) {
                $css_file = new Elementor\Core\Files\CSS\Post( self::get_footer_id() );
                $css_file->enqueue();
            }
        }
    }

    /**
     * Prints the admin notics when Elementor is not installed or activated.
     */
    public function elementor_not_available() {

        if ( file_exists( WP_PLUGIN_DIR . '/elementor/elementor.php' ) ) {
            $url = network_admin_url() . 'plugins.php?s=elementor';
        } else {
            $url = network_admin_url() . 'plugin-install.php?s=elementor';
        }

        echo '<div class="notice notice-error">';
        /* Translators: URL to install or activate Elementor plugin. */
        echo '<p>' . sprintf( __( 'The <strong>Header Footer Elementor</strong> plugin requires <strong><a href="%s">Elementor</strong></a> plugin installed & activated.', 'themeszone-feature-pack' ) . '</p>', $url );
        echo '</div>';
    }

    /**
     * Adds classes to the body tag conditionally.
     *
     * @param  Array $classes array with class names for the body tag.
     *
     * @return Array          array with class names for the body tag.
     */
    public function body_class( $classes ) {

        if ( self::header_enabled() ) {
            $classes[] = 'tz-hf-header';
        }

        if ( self::footer_enabled() ) {
            $classes[] = 'tz-hf-footer';
        }

        $classes[] = 'tz-hf-template-' . $this->template;
        $classes[] = 'tz-hf-stylesheet-' . get_stylesheet();

        return $classes;
    }

    /*
     * Returns template id
     *
     * @since 1.0.0
     */

    public static function get_template_id( $type ) {

        $cached = wp_cache_get( $type );

        if ( false !== $cached ) {
            return $cached;
        }

        $args = array(
            'post_type'    => 'elementor-tz-hf',
            'meta_key'     => 'tz_hf_template_type',
            'meta_value'   => $type,
            'meta_type'    => 'post',
            'meta_compare' => '>=',
            'orderby'      => 'meta_value',
            'order'        => 'ASC',
            'meta_query'   => array(
                'relation' => 'OR',
                array(
                    'key'     => 'tz_hf_template_type',
                    'value'   => $type,
                    'compare' => '==',
                    'type'    => 'post',
                ),
            ),
        );

        $args = apply_filters( 'tz_hf_get_template_id_args', $args );

        $template = new WP_Query(
            $args
        );

        if ( $template->have_posts() ) {
            $posts = wp_list_pluck( $template->posts, 'ID' );
            wp_cache_set( $type, $posts );
            return $posts;
        }

        return '';
    }

    public static function get_settings( $setting = '', $default = '' ) {
        if ( 'type_header' == $setting || 'type_footer' == $setting ) {
            $templates = self::get_template_id( $setting );
            $template = is_array( $templates ) ? $templates[0] : '';
            $template = apply_filters( "tz_hf_get_settings_{$setting}", $template );

            return $template;
        }
    }

    /**
     * Checks if Header is enabled.
     *
     * @since  1.0.0
     * @return bool True if header is enabled. False if header is not enabled
     */
    public static function header_enabled() {
        $header_id = self::get_settings( 'type_header', '' );
        $status    = false;

        if ( '' !== $header_id ) {
            $status = true;
        }

        return apply_filters( 'tz_hf_header_enabled', $status );
    }

    /**
     * Checks if Footer is enabled.
     *
     * @since  1.0.0
     * @return bool True if header is enabled. False if header is not enabled.
     */
    public static function footer_enabled() {
        $footer_id = self::get_settings( 'type_footer', '' );
        $status    = false;

        if ( '' !== $footer_id ) {
            $status = true;
        }

        return apply_filters( 'tz_hf_footer_enabled', $status );
    }

    /**
     * Get Header ID
     *
     * @since  1.0.0
     * @return (String|boolean) header id if it is set else returns false.
     */
    public static function get_header_id() {
        $header_id = self::get_settings( 'type_header', '' );
        if ( '' === $header_id ) {
            $header_id = false;
        }

        return apply_filters( 'get_tz_hf_header_id', $header_id );
    }

    /**
     * Get Footer ID
     *
     * @since  1.0.0
     * @return (String|boolean) header id if it is set else returns false.
     */
    public static function get_footer_id() {
        $footer_id = self::get_settings( 'type_footer', '' );

        if ( '' === $footer_id ) {
            $footer_id = false;
        }

        return apply_filters( 'get_tz_hf_footer_id', $footer_id );
    }

    /*
     * Elementor Section Shortcode
     */

    public function elementor_section_shortcode( $atts ){
        $args = shortcode_atts( array(
            'id' => ''
        ), $atts );

        if ( isset( $args['id'] ) &&  $args['id'] != '' )
            return self::$elementor_instance->frontend->get_builder_content_for_display( (int) $args['id'] );

    }

    /**
     * Prints the Header content.
     */
    public static function get_header_content() {
        echo self::$elementor_instance->frontend->get_builder_content_for_display( self::get_header_id() );
    }

    /**
     * Prints the Footer content.
     */
    public static function get_footer_content() {
        echo self::$elementor_instance->frontend->get_builder_content_for_display( self::get_footer_id() );
    }

    public static function header_wrapper_start(){
        ?>
        <header id="masthead" itemscope="itemscope" itemtype="https://schema.org/WPHeader" class="<?php echo esc_attr__( ( defined( 'THEMESZONE_PREFIX' ) ? THEMESZONE_PREFIX.'-elementor-header' : 'themeszone-elementor-header' ) )?>">
        <p class="main-title bhf-hidden" itemprop="headline"><a href="<?php echo bloginfo( 'url' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
        <?php
    }

    public static function header_wrapper_end(){
        ?>
        </header>
        <?php
    }

    /**
     * Display header markup.
     *
     * @since  1.0.0
     */
    public static function render_header() {

        if ( false == apply_filters( 'enable_tz_hf_render_header', true ) ) {
            return;
        }
        self::header_wrapper_start();
        self::get_header_content();
        self::header_wrapper_end();
    }


    public static function footer_wrapper_start(){
        ?>
        <aside itemtype="https://schema.org/WPFooter" itemscope="itemscope" class="<?php echo esc_attr__( ( defined( 'THEME_PREFIX' ) ? THEME_PREFIX.'-elementor-footer' : 'themeszone-elementor-footer' ) )?>">
        <?php
    }

    public static function footer_wrapper_end(){
        ?>
        </aside>
        <?php
    }

    /**
     * Display footer markup.
     *
     * @since  1.0.0
     */
    public static function render_footer() {

        if ( false == apply_filters( 'enable_tz_hf_render_footer', true ) ) {
            return;
        }
        self::footer_wrapper_start();
        self::get_footer_content();
        self::footer_wrapper_end();

    }


    /**
     *
     *
     */

    public function add_wrapper_class_start(){
        global $post;
        $template_type = get_post_meta( $post->ID, 'tz_hf_template_type', true );

        if ( 'type_footer' === $template_type )
            self::footer_wrapper_start();
        elseif( 'type_header' === $template_type )
            self::header_wrapper_start();

    }

    public function add_wrapper_class_end(){
        global $post;
        $template_type = get_post_meta( $post->ID, 'tz_hf_template_type', true );

        if ( 'type_footer' === $template_type )
            self::footer_wrapper_end();
        elseif( 'type_header' === $template_type )
            self::header_wrapper_end();
    }

}