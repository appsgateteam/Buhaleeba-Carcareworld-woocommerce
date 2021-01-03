<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themes.zone/
 * @since      1.0.0
 *
 * @package    Tz_Feature_Pack
 * @subpackage Tz_Feature_Pack/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Tz_Feature_Pack
 * @subpackage Tz_Feature_Pack/admin
 * @author     Themes Zone <themes.zonehelp@gmail.com>
 */
class Tz_Feature_Pack_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tz-feature-pack-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tz-feature-pack-admin.js', array( 'jquery' ), $this->version, false );
	}

    public function add_wc_settings_tab( $settings_tabs ){
        $settings_tabs['settings_tab_chromium'] = esc_html__( 'Chromium Custom Taxonomies', 'tz-feature-pack' );
        return $settings_tabs;
    }

    public function settings_tab() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    private function get_settings(){
        $settings = array(
            'section_title' => array(
                'name'     => esc_html__( 'Turn on custom taxonomies', 'tz-feature-pack' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'tz_f_p_custom_taxonomies'
            ),

            array(
                'title'    => esc_html__( 'Enable Brand/Year/Model Taxonomy', 'tz-feature-pack' ),
                'desc'     => esc_html__( 'Enable Brand/Year/Model Taxonomy for your products.', 'tz-feature-pack' ),
                'id'       => 'tz_brand_year_model_taxonomy',
                'default'  => 'no',
                'type'     => 'checkbox',
                'desc_tip' => esc_html__( 'Checking this field will turn on brand/year/model taxonomy', 'tz-feature-pack' ),
            ),

            array(
                'title'    => esc_html__( 'Enable Tire Size Taxonomy', 'tz-feature-pack' ),
                'desc'     => esc_html__( 'Enable Tire Size Taxonomy for your products.', 'tz-feature-pack' ),
                'id'       => 'tz_tire_sizes_taxonomy',
                'default'  => 'no',
                'type'     => 'checkbox',
                'desc_tip' => esc_html__( 'Checking this field will turn on tire size taxonomy', 'tz-feature-pack' ),
            ),

            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'tz_f_p_custom_taxonomies_end'
            )
        );
        return apply_filters( 'tz_f_p_custom_taxonomies_settings', $settings );
    }

    function update_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    /*
	 * Register Header/Footer Post Type
	 *
	 * @since    1.0.0
	 */
    public function register_elementor_hf_posttype(){
        $labels = array(
            'name'               => __( 'Header/Footer Template', 'tz-feature-pack' ),
            'singular_name'      => __( 'Elementor Header/Footer', 'tz-feature-pack' ),
            'menu_name'          => __( 'Header/Footer Template', 'tz-feature-pack' ),
            'name_admin_bar'     => __( 'Elementor Header/Footer ', 'tz-feature-pack' ),
            'add_new'            => __( 'Add New', 'tz-feature-pack' ),
            'add_new_item'       => __( 'Add New Header/Footer', 'tz-feature-pack' ),
            'new_item'           => __( 'New Header/Footer Template', 'tz-feature-pack' ),
            'edit_item'          => __( 'Edit Header/Footer Template', 'tz-feature-pack' ),
            'view_item'          => __( 'View Header/Footer Template', 'tz-feature-pack' ),
            'all_items'          => __( 'All Elementor Header/Footer', 'tz-feature-pack' ),
            'search_items'       => __( 'Search Header/Footer Templates', 'tz-feature-pack' ),
            'parent_item_colon'  => __( 'Parent Header/Footer Templates:', 'tz-feature-pack' ),
            'not_found'          => __( 'No Header/Footer Templates found.', 'tz-feature-pack' ),
            'not_found_in_trash' => __( 'No Header/Footer Templates found in Trash.', 'tz-feature-pack' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-editor-kitchensink',
            'supports'            => array( 'title', 'thumbnail', 'elementor' ),
        );

        register_post_type( 'elementor-tz-hf', $args );
    }

    public function register_elementor_sections_posttype(){
        $labels = array(
            'name'               => __( 'Elementor Sections', 'tz-feature-pack' ),
            'singular_name'      => __( 'Elementor Section', 'tz-feature-pack' ),
            'menu_name'          => __( 'Elementor Section Template', 'tz-feature-pack' ),
            'name_admin_bar'     => __( 'Elementor Sections', 'tz-feature-pack' ),
            'add_new'            => __( 'Add New', 'tz-feature-pack' ),
            'add_new_item'       => __( 'Add New Elementor Section', 'tz-feature-pack' ),
            'new_item'           => __( 'New Elementor Section Template', 'tz-feature-pack' ),
            'edit_item'          => __( 'Edit Elementor Section Template', 'tz-feature-pack' ),
            'view_item'          => __( 'View Elementor Section Template', 'tz-feature-pack' ),
            'all_items'          => __( 'All Elementor Sections', 'tz-feature-pack' ),
            'search_items'       => __( 'Search Elementor Section Templates', 'tz-feature-pack' ),
            'parent_item_colon'  => __( 'Parent Elementor Section Templates:', 'tz-feature-pack' ),
            'not_found'          => __( 'No Elementor Sections found.', 'tz-feature-pack' ),
            'not_found_in_trash' => __( 'No Elementor Sections found in Trash.', 'tz-feature-pack' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'rewrite'             => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'exclude_from_search' => true,
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-editor-kitchensink',
            'supports'            => array( 'title', 'thumbnail', 'elementor' ),
        );

        register_post_type( 'elementor-tz-section', $args );
    }

    /*
     * Register Admin Menu under "Themes" section
     *
     * @since 1.0.0
     */

    public function register_admin_menu_hf() {
        add_submenu_page(
            'themes.php',
            __( 'Header Footer Builder ', 'tz-feature-pack' ),
            __( 'Header Footer Builder ThemesZone Theme', 'tz-feature-pack' ),
            'edit_pages',
            'edit.php?post_type=elementor-tz-hf'
        );
    }

    public function register_admin_menu_sections() {
        add_submenu_page(
            'themes.php',
            __( 'Elementor Sections', 'tz-feature-pack' ),
            __( 'Elementor Sections ThemesZone Theme', 'tz-feature-pack' ),
            'edit_pages',
            'edit.php?post_type=elementor-tz-section'
        );
    }



    /*
     * Register Options Meta box
     *
     * @since 1.0.0
     */

    function register_hf_metabox() {
        add_meta_box(
            'elementor-tz-hf-meta-box', __( 'Elementor Header/Footer options', 'tz-feature-pack' ), array(
            $this,
            'metabox_hf_render',
        ), 'elementor-tz-hf', 'normal', 'high'
        );
    }

    function register_sections_metabox() {
        add_meta_box(
            'elementor-tz-section-meta-box', __( 'Elementor Sections Options', 'tz-feature-pack' ), array(
            $this,
            'metabox_sections_render',
        ), 'elementor-tz-section', 'normal', 'high'
        );
    }

    /**
     * Render Meta field.
     *
     * @param  POST $post Currennt post object which is being displayed.
     */
    function metabox_hf_render( $post ) {
        $values            = get_post_custom( $post->ID );
        $template_type     = isset( $values['tz_hf_template_type'] ) ? esc_attr( $values['tz_hf_template_type'][0] ) : '';
        $display_on_canvas = isset( $values['display-on-canvas'] ) ? true : false;
        $display_on_home = isset( $values['display-on-home'] ) ? true : false;

        // We'll use this nonce field later on when saving.
        wp_nonce_field( 'tz_hf_meta_nounce', 'tz_hf_meta_nounce' );
        ?>
        <p>
            <label for="tz_hf_template_type"><?php _e( 'Select the type of template this is', 'tz-feature-pack' ); ?></label>
            <select name="tz_hf_template_type" id="tz_hf_template_type">
                <option value="" <?php selected( $template_type, '' ); ?>><?php _e( 'Select Option', 'tz-feature-pack' ); ?></option>
                <option value="type_header" <?php selected( $template_type, 'type_header' ); ?>><?php _e( 'Header', 'tz-feature-pack' ); ?></option>
                <option value="type_footer" <?php selected( $template_type, 'type_footer' ); ?>><?php _e( 'Footer', 'tz-feature-pack' ); ?></option>
            </select>
        </p>
        <p>
            <label for="display-on-canvas">
                <input type="checkbox" id="display-on-canvas" name="display-on-canvas" value="1" <?php checked( $display_on_canvas, true ); ?> />
                <?php _e( 'Display Layout on Elementor Canvas Template?', 'tz-feature-pack' ); ?>
            </label>
        </p>
        <p class="description"><?php _e( 'If enabled your header/footer templates will be displayed using Elementor Canvas.', 'tz-feature-pack' ); ?></p>
        <p>
            <label for="display-on-home">
                <input type="checkbox" id="display-on-home" name="display-on-home" value="0" <?php checked( $display_on_home, true ); ?> />
                <?php _e( 'Use only on home page', 'tz-feature-pack' ); ?>
            </label>
        </p>
        <p class="description"><?php _e( 'If checked this template will only be shown on home page.', 'tz-feature-pack' ); ?></p>
        <?php
    }

    function metabox_sections_render( $post ) {
        $values            = get_post_custom( $post->ID );
        $display_on_canvas = isset( $values['display-on-canvas'] ) ? true : false;

        // We'll use this nonce field later on when saving.
        wp_nonce_field( 'sections_meta_nounce', 'sections_meta_nounce' );
        ?>
        <p>
            <label for="display-on-canvas">
                <input type="checkbox" id="display-on-canvas" name="display-on-canvas" value="1" <?php checked( $display_on_canvas, true ); ?> />
                <?php _e( 'Display Layout on Elementor Canvas Template?', 'tz-feature-pack' ); ?>
            </label>
        </p>
        <p class="description"><?php _e( 'If enabled your section templates will be displayed using Elementor Canvas.', 'tz-feature-pack' ); ?></p>
        <?php
    }


    /**
     * Save meta field.
     *
     * @param  POST $post_id Currennt post object which is being displayed.
     *
     * @return Void
     */
    public function save_meta_for_tz_hf( $post_id ) {

        // Bail if we're doing an auto save.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // if our nonce isn't there, or we can't verify it, bail.
        if ( ! isset( $_POST['tz_hf_meta_nounce'] ) || ! wp_verify_nonce( $_POST['tz_hf_meta_nounce'], 'tz_hf_meta_nounce' ) ) {
            return;
        }

        // if our current user can't edit this post, bail.
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        if ( isset( $_POST['tz_hf_template_type'] ) ) {
            update_post_meta( $post_id, 'tz_hf_template_type', esc_attr( $_POST['tz_hf_template_type'] ) );
        }

        if ( isset( $_POST['display-on-canvas'] ) ) {
            update_post_meta( $post_id, 'display-on-canvas', esc_attr( $_POST['display-on-canvas'] ) );
        } else {
            delete_post_meta( $post_id, 'display-on-canvas' );
        }

        if ( isset( $_POST['display-on-home'] ) ) {
            update_post_meta( $post_id, 'display-on-home', esc_attr( $_POST['display-on-home'] ) );
        } else {
            delete_post_meta( $post_id, 'display-on-home' );
        }

    }

    public function save_meta_for_tz_sections( $post_id ) {

        // Bail if we're doing an auto save.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // if our nonce isn't there, or we can't verify it, bail.
        if ( ! isset( $_POST['themeszone_sections_meta_nounce'] ) || ! wp_verify_nonce( $_POST['sections_meta_nounce'], 'sections_meta_nounce' ) ) {
            return;
        }

        // if our current user can't edit this post, bail.
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }

        if ( isset( $_POST['display-on-canvas'] ) ) {
            update_post_meta( $post_id, 'display-on-canvas', esc_attr( $_POST['display-on-canvas'] ) );
        } else {
            delete_post_meta( $post_id, 'display-on-canvas' );
        }

    }


    /**
     * Display notice when editing the header or footer when there is one more of similar layout is active on the site.
     *
     * @since 1.0.0
     */
    public function location_notice() {

        global $pagenow;
        global $post;

        if ( 'post.php' != $pagenow || ! is_object( $post ) || 'elementor-themeszone-hf' != $post->post_type ) {
            return;
        }

        $template_type = get_post_meta( $post->ID, 'tz_hf_template_type', true );

        if ( '' !== $template_type ) {
            $templates = ThemesZone_Feature_Pack_Elementor_HF::get_template_id( $template_type );

            // Check if more than one template is selected for current template type.
            if ( is_array( $templates ) && isset( $templates[1] ) && $post->ID != $templates[0] ) {

                $post_title        = '<strong>' . get_the_title( $templates[0] ) . '</strong>';
                $template_location = '<strong>' . $this->template_location( $template_type ) . '</strong>';
                /* Translators: Post title, Template Location */
                $message = sprintf( __( 'Template %1$s is already assigned to the location %2$s', 'tz-feature-pack' ), $post_title, $template_location );

                echo '<div class="error"><p>';
                echo $message;
                echo '</p></div>';
            }
        }

    }

    /**
     * Convert the Template name to be added in the notice.
     *
     * @since  1.0.0
     *
     * @param  String $template_type Template type name.
     *
     * @return String $template_type Template type name.
     */
    public function template_location( $template_type ) {
        $template_type = ucfirst( str_replace( 'type_', '', $template_type ) );

        return $template_type;
    }


    /**
     * Don't display the elementor header footer templates on the frontend for non edit_posts capable users.
     *
     * @since  1.0.0
     */
    public function block_template_frontend() {
        if ( is_singular( 'elementor-themeszone-hf' ) && ! current_user_can( 'edit_posts' ) ) {
            wp_redirect( site_url(), 301 );
            die;
        }
    }


    /**
     * Single template function which will choose our template
     *
     * @since  1.0.1
     *
     * @param  String $single_template Single template.
     */
    function load_canvas_template( $single_template ) {

        global $post;

        if ( ( 'elementor-tz-hf' == $post->post_type ) || ( 'elementor-tz-section' == $post->post_type ) ) {

            $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

            if ( file_exists( $elementor_2_0_canvas ) ) {
                return $elementor_2_0_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }

        return $single_template;
    }

}
