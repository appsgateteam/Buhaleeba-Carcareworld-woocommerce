<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Admin class.
 *
 * @class   YITH_YWRAQ_Admin
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Admin' ) ) {

	/**
	 * Class YITH_YWRAQ_Admin
	 */
	class YITH_YWRAQ_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWRAQ_Admin
		 */
		protected static $instance;

		/**
		 * @var $_panel YIT_Plugin_Panel_WooCommerce
		 */
		protected $_panel;

		/**
		 * @var $_premium string Premium tab template file name
		 */
		protected $_premium = 'premium.php';

		/**
		 * @var string Premium version landing link
		 */
		protected $_premium_landing = 'https://yithemes.com/themes/plugins/yith-woocommerce-request-a-quote/';

		/**
		 * @var string Official plugin landing page
		 */
		protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-request-a-quote/';

		/**
		 * @var string Panel page
		 */
		public $_panel_page = 'yith_woocommerce_request_a_quote';

		/**
		 * @var string List of messages
		 */
		protected $messages = array();

		/**
		 * @var string Doc Url
		 */
		public $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-request-a-quote/';

		/**
		 * @var string Official plugin support page
		 */
		protected $_support = 'https://wordpress.org/support/plugin/yith-woocommerce-request-a-quote';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_YWRAQ_Admin
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			// register plugin to licence/update system
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
			add_action( 'init', array( $this, 'gutenberg_integration' ) );


			$this->_support = function_exists( 'yith_get_premium_support_url' ) ? yith_get_premium_support_url() : 'https://yithemes.com/my-account/support/dashboard/';
			$this->create_menu_items();

			//Add action links
			add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWRAQ_DIR . '/' . basename( YITH_YWRAQ_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );


			add_action( 'init', array( $this, 'add_page' ) );

			//notices
			add_action( 'admin_notices', array( $this, 'check_coupon' ) );
			add_action( 'admin_notices', array( $this, 'check_deprecated_template' ) );
			add_action( 'wp_ajax_ywraq_dismiss_notice_message', array( $this, 'ajax_dismiss_notice' ) );

			//custom styles and javascripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ),20 );

			// add custom tabs
			add_action( 'yith_ywraq_exclusions_table', array( $this, 'exclusions_table' ) );
			add_action( 'plugins_loaded', array( $this, 'load_privacy_dpa' ), 20 );
			add_filter( 'yith_plugin_fw_metabox_class', array( $this, 'add_custom_metabox_class'), 10, 2 );



		}

		/**
		 * Add new plugin-fw style.
		 *
		 * @param $class
		 * @param $post
		 *
		 * @return string
		 */
		public function add_custom_metabox_class( $class, $post ){
    		$allow_post_types = array( 'shop_order', 'product' );

			if( in_array( $post->post_type, $allow_post_types ) ){
				$class.= ' '.yith_set_wrapper_class();
			}
			return $class;
		}


		/**
		 * Gutenberg Integration
		 */
		public function gutenberg_integration() {
			if ( function_exists( 'yith_plugin_fw_gutenberg_add_blocks' ) ) {
				$blocks = include_once( YITH_YWRAQ_DIR . 'plugin-options/gutenberg/blocks.php' );
				yith_plugin_fw_gutenberg_add_blocks( $blocks );
				wp_register_style( 'yith-ywraq-gutenberg', YITH_YWRAQ_ASSETS_URL . '/css/ywraq-gutenberg.css' );
			}
		}

		/**
		 *  Load the Privacy DPA
		 */
		public function load_privacy_dpa(  ) {
			if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
				require_once( YITH_YWRAQ_INC . 'class.yith-request-quote-privacy-dpa.php' );
			}
		}


		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since  1.0.0
		 */
		public function enqueue_styles_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';


			wp_register_script( 'yith_ywraq_admin', YITH_YWRAQ_ASSETS_URL . '/js/yith-ywraq-admin' . $suffix . '.js', array(
				'jquery',
				'jquery-ui-dialog',
                'yith-plugin-fw-fields'
			), YITH_YWRAQ_VERSION, true );

			if ( defined( 'YITH_YWRAQ_PREMIUM' ) ) {
				wp_register_style( 'yith_ywraq_backend', YITH_YWRAQ_ASSETS_URL . '/css/ywraq-backend.css' );
			}

			//load the script in selected pages
			global $pagenow;
			$post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : ( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : 0 );
			$post = get_post( $post );

			if ( ( $pagenow == 'admin.php' && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'yith_woocommerce_request_a_quote' ) || ( $post && $post->post_type == 'shop_order' ) || ( $pagenow == 'post-new.php' && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == 'shop_order' ) ) {

				if ( ! wp_script_is( 'selectWoo' ) ) {
					wp_enqueue_script( 'selectWoo' );
					wp_enqueue_style( 'select2' );
				}

				wp_enqueue_script( 'yith_ywraq_admin' );
				wp_enqueue_style( 'yith_ywraq_backend' );
				wp_enqueue_style( 'yith-ywraq-gutenberg' );
				wp_localize_script( 'yith_ywraq_admin', 'ywraq_admin', array(
					'popup_add_title'           => __( 'Add new field', 'yith-woocommerce-request-a-quote' ),
					'popup_edit_title'          => __( 'Edit field', 'yith-woocommerce-request-a-quote' ),
					'default_form_submit_label' => __( 'Set', 'yith-woocommerce-request-a-quote' ),
					'enabled'                   => '<span class="status-enabled tips" data-tip="' . __( 'Yes', 'yith-woocommerce-request-a-quote' ) . '"></span>',
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				) );
			}

		}

		/**
		 * Create Menu Items
		 *
		 * Print admin menu items
		 *
		 * @since  1.0
		 * @author Emanuela Castorina
		 */
		private function create_menu_items() {

			// Add a panel under YITH Plugins tab
			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
			add_action( 'yith_woocommerce_request_a_quote', array( $this, 'premium_tab' ) );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      /Yit_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_panel() {

			if ( ! empty( $this->_panel ) ) {
				return;
			}

			$admin_tabs = array( 'button' => __( 'Button Settings', 'yith-woocommerce-request-a-quote' ), );

			if ( defined( 'YITH_YWRAQ_FREE_INIT' ) ) {
				$admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-request-a-quote' );
			} else {
				$admin_tabs['form'] = __( 'Form Settings', 'yith-woocommerce-request-a-quote' ); //@since 1.4.5

				$admin_tabs['request'] = __( 'Request List', 'yith-woocommerce-request-a-quote' ); //@since 1.4.5
				$admin_tabs['quote']   = __( 'Quote Settings', 'yith-woocommerce-request-a-quote' ); //@since 1.4.5
				$admin_tabs['other']   = __( 'Other Settings', 'yith-woocommerce-request-a-quote' );
			}

			if ( ( isset( $_POST['ywraq_enable_pdf'] ) && $_POST['ywraq_enable_pdf'] == 'yes' ) || get_option( 'ywraq_enable_pdf', 'yes' ) == 'yes' ) {
				$admin_tabs['pdf'] = __( 'PDF Quote', 'yith-woocommerce-request-a-quote' );
			}

			if ( isset( $_POST['ywraq_exclusion_list_setting'] ) && ! isset( $_POST['ywraq_enable_pdf'] ) ) {
				unset( $admin_tabs['pdf'] );
			}

			if ( ( isset( $_POST['ywraq_show_btn_exclusion'] ) && $_POST['ywraq_show_btn_exclusion'] == 'yes' ) || get_option( 'ywraq_show_btn_exclusion', 'yes' ) == 'yes' ) {
				$admin_tabs['exclusions'] = __( 'Exclusion List', 'yith-woocommerce-request-a-quote' );
			}

			if ( isset( $_POST['ywraq_exclusion_list_setting'] ) && ! isset( $_POST['ywraq_show_btn_exclusion'] ) ) {
				unset( $admin_tabs['exclusions-prod'], $admin_tabs['exclusions-cat'] );
			}

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Request a Quote',
				'menu_title'       => 'Request a Quote',
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->_panel_page,
				'admin-tabs'       => apply_filters( 'ywraq_admin_tabs', $admin_tabs ),
				'options-path'     => YITH_YWRAQ_DIR . '/plugin-options',
                'class'            => yith_set_wrapper_class()
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once( YITH_YWRAQ_DIR . '/plugin-fw/lib/yit-plugin-panel-wc.php' );
			}

			$this->_panel = new YIT_Plugin_Panel_WooCommerce( $args );

			add_action( 'woocommerce_admin_field_ywraq_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );

			$this->check_db_update();

		}

		/**
		 * Check if there's a new version of plugin to update something.
		 *
		 * @since  2.0.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function check_db_update() {
			$current_option_version = get_option( 'yit_ywraq_option_version', '0' );
			$forced                 = isset( $_GET['update_ywraq_options'] ) && $_GET['update_ywraq_options'] == 'forced';

			if ( version_compare( $current_option_version, YITH_YWRAQ_VERSION, '>=' ) && ! $forced ) {
				return;
			}

			//Save all products with the meta _ywraq_hide_quote_button inside the exclusion list.
			$is_populated = get_option( 'yith_ywraw_exclusion_list_populated' );
			if ( ! $is_populated ) {
				$this->populate_exclusion_list();
			}

			update_option( 'yit_ywraq_option_version', YITH_YWRAQ_VERSION );

		}

		/**
		 * Save all products with meta _ywraq_hide_quote_button inside the exclusion list
		 *
		 * @since  2.0.0
		 * @author Emanuela Castorina
		 */
		private function populate_exclusion_list() {
			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => '_ywraq_hide_quote_button',
						'value'   => 1,
						'compare' => 'LIKE',
					),
				),
			);

			$products       = get_posts( $args );
			$exclusion_prod = explode( ',', get_option( 'yith-ywraq-exclusions-prod-list', '' ) );
			if ( $products ) {
				$exclusion_prod = array_unique( array_merge( $exclusion_prod, $products ) );
			}

			update_option( 'yith-ywraq-exclusions-prod-list', implode( ',', $exclusion_prod ) );
			update_option( 'yith_ywraw_exclusion_list_populated', true );
		}

		/**
		 * Add a page "Request a Quote".
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_page() {
			global $wpdb;

			$option_value = get_option( 'ywraq_page_id' );

			if ( $option_value > 0 && get_post( $option_value ) ) {
				return;
			}

			$page_found = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_name` = 'request-quote' LIMIT 1;" );
			if ( $page_found ) :
				if ( ! $option_value ) {
					update_option( 'ywraq_page_id', $page_found );
				}

				return;
			endif;

			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => esc_sql( _x( 'request-quote', 'page_slug', 'yit' ) ),
				'post_title'     => __( 'Request a Quote', 'yit' ),
				'post_content'   => '[yith_ywraq_request_quote]',
				'post_parent'    => 0,
				'comment_status' => 'closed'
			);
			$page_id   = wp_insert_post( $page_data );

			update_option( 'ywraq_page_id', $page_id );
		}

		/**
		 * Premium Tab Template
		 *
		 * Load the premium tab template on admin page
		 *
		 * @return   void
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function premium_tab() {
			$premium_tab_template = YITH_YWRAQ_TEMPLATE_PATH . '/admin/' . $this->_premium;
			if ( file_exists( $premium_tab_template ) ) {
				include_once( $premium_tab_template );
			}
		}

		/**
		 * Action Links
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $links | links plugin array
		 *
		 * @return   mixed Array
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return mixed
		 * @use      plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->_panel_page, true );
			return $links;
		}

		/**
		 * plugin_row_meta
		 *
		 * add the action links to plugin admin page
		 *
		 * @param $new_row_meta_args
		 * @param $plugin_meta
		 * @param $plugin_file
		 * @param $plugin_data
		 * @param $status
		 *
		 * @param string $init_file
		 *
		 * @return   Array
		 * @since    1.6.5
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @use      plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWRAQ_INIT' ) {
			if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
				$new_row_meta_args['slug']      = YITH_YWRAQ_SLUG;
				$new_row_meta_args['is_premium'] = true;
			}

			if ( defined( 'YITH_YWRAQ_FREE_INIT' ) && YITH_YWRAQ_FREE_INIT == $plugin_file ) {
				$new_row_meta_args['support'] = array(
					'url' => 'https://wordpress.org/support/plugin/yith-woocommerce-request-a-quote'
				);
			}

			return $new_row_meta_args;
		}

		/**
		 * Get the premium landing uri
		 *
		 * @since   1.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  string The premium landing link
		 */
		public function get_premium_landing_uri() {
			return defined( 'YITH_REFER_ID' ) ? $this->get_premium_landing_uri() . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing;
		}

		/**
		 * Display Admin Notice if coupons are enabled
		 *
		 * @access public
		 * @return void
		 *
		 * @since  1.3.0
		 */
		public function check_coupon() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( get_option( 'woocommerce_enable_coupons' ) != 'yes' && 'yes' != get_option( 'ywraq_dismiss_disabled_coupons_warning_message', 'no' ) ) { ?>
                <div id="message" class="notice notice-warning is-dismissible ywraq_disabled_coupons">
                    <p>
                        <strong><?php _e( 'YITH WooCommerce Request a Quote', 'yith-woocommerce-request-a-quote' ); ?></strong>
                    </p>

                    <p>
						<?php _e( 'WooCommerce coupon system has been disabled. In order to make YITH WooCommerce Request a Quote work correctly, you have to enable coupons.', 'yith-woocommerce-request-a-quote' ); ?>
                    </p>

                    <p>
                        <a href="<?php echo admin_url( "admin.php?page=wc-settings&tab=general" ) ?>"><?php echo __( 'Enable the use of coupons', 'yith-woocommerce-request-a-quote' ) ?></a>
                    </p>
                </div>
                <script>
                    (function ($) {
                        $('.ywraq_disabled_coupons').on('click', '.notice-dismiss', function () {
                            jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", {
                                action: "ywraq_dismiss_notice_message",
                                dismiss_action: "ywraq_dismiss_disabled_coupons_warning_message",
                                nonce: "<?php echo esc_js( wp_create_nonce( 'ywraq_dismiss_notice' ) ); ?>"
                            });
                        });
                    })(jQuery);
                </script>
				<?php
			}
		}

		/**
		 * Show a notice on the dashboard if the old form template is override in the theme.
		 *
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function check_deprecated_template() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$located = wc_locate_template( 'request-quote-form.php', '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
			$message = __( 'The template \'request-quote-form.php\' that you\'ve override in your theme was deprecated since version 2.0 and will be ignored.', 'yith-woocommerce-request-a-quote' );

			if ( $located != YITH_YWRAQ_TEMPLATE_PATH . '/' . 'request-quote-form.php' && 'yes' !== get_option( 'ywraq_dismiss_old_template_warning_message', 'no' ) ) {
				?>
                <div class="notice notice-warning is-dismissible ywraq-dismiss-old-template-warning-message">
                    <p>
                        <strong><?php _e( 'YITH WooCommerce Request a Quote', 'yith-woocommerce-request-a-quote' ); ?></strong>
                    </p>
                    <p>
						<?php echo $message; ?>
                    </p>
                </div>
                <script>
                    (function ($) {
                        $('.ywraq-dismiss-old-template-warning-message').on('click', '.notice-dismiss', function () {
                            jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", {

                                action: "ywraq_dismiss_notice_message",
                                dismiss_action: "ywraq_dismiss_old_template_warning_message",
                                nonce: "<?php echo esc_js( wp_create_nonce( 'ywraq_dismiss_notice' ) ); ?>"
                            });
                        });
                    })(jQuery);
                </script>
				<?php
			}
		}

		/**
		 * AJAX handler for dismiss notice action.
		 *
		 * @since  2.0.0
		 * @access public
		 */
		public function ajax_dismiss_notice() {
			if ( empty( $_POST['dismiss_action'] ) ) {
				return;
			}

			check_ajax_referer( 'ywraq_dismiss_notice', 'nonce' );
			switch ( $_POST['dismiss_action'] ) {
				case 'ywraq_dismiss_old_template_warning_message':
					update_option( 'ywraq_dismiss_old_template_warning_message', 'yes' );
					break;
				case 'ywraq_dismiss_disabled_coupons_warning_message':
					update_option( 'ywraq_dismiss_disabled_coupons_warning_message', 'yes' );
					break;
			}
			wp_die();
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_YWRAQ_DIR . 'plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_YWRAQ_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_YWRAQ_INIT, YITH_YWRAQ_SECRET_KEY, YITH_YWRAQ_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since    1.0.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once YITH_YWRAQ_DIR . 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YITH_YWRAQ_SLUG, YITH_YWRAQ_INIT );
		}

		/**
		 * Add categories exclusion table.
		 *
		 * @access public
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function exclusions_table() {

			if ( isset( $_GET['page'] ) && $_GET['page'] == $this->_panel_page && isset( $_GET['tab'] ) && $_GET['tab'] == 'exclusions' && file_exists( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-exclusions-table.php' ) ) {

				// define variables
				$sections      = array( 'product', 'category','tag');
				$current       = isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : 'product';
				$base_page_url = admin_url( "admin.php?page={$this->_panel_page}&tab=exclusions" );

				if ( 'product' == $current ) {
					$table = new YITH_YWRAQ_Exclusions_Prod_Table();
					$table->prepare_items();
				} elseif ( 'category' == $current ) {
					$table = new YITH_YWRAQ_Exclusions_Cat_Table();
					$table->prepare_items();
				} elseif ( 'tag' == $current ) {
					$table = new YITH_YWRAQ_Exclusions_Tag_Table();
					$table->prepare_items();
				}

				include_once( YITH_YWRAQ_TEMPLATE_PATH . '/admin/ywraq-exclusions-table.php' );
			}
		}



	}

}

/**
 * Unique access to instance of YITH_YWRAQ_Admin class
 *
 * @return \YITH_YWRAQ_Admin
 */
function YITH_YWRAQ_Admin() {
	return YITH_YWRAQ_Admin::get_instance();
}