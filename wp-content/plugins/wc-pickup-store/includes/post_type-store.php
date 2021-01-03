<?php
/**
** Register post type store
**/
function wps_store_post_type() {
	$labels = array(
		'name'                  => _x( 'Stores', 'Post Type General Name', 'wc-pickup-store' ),
		'singular_name'         => _x( 'Store', 'Post Type Singular Name', 'wc-pickup-store' ),
		'menu_name'             => __( 'Stores', 'wc-pickup-store' ),
		'name_admin_bar'        => __( 'Store', 'wc-pickup-store' ),
		'archives'              => __( 'Store Archives', 'wc-pickup-store' ),
		'attributes'            => __( 'Store Attributes', 'wc-pickup-store' ),
		'parent_item_colon'     => __( 'Parent Store:', 'wc-pickup-store' ),
		'all_items'             => __( 'All Stores', 'wc-pickup-store' ),
		'add_new_item'          => __( 'Add New Store', 'wc-pickup-store' ),
		'add_new'               => __( 'Add New Store', 'wc-pickup-store' ),
		'new_item'              => __( 'New Store', 'wc-pickup-store' ),
		'edit_item'             => __( 'Edit Store', 'wc-pickup-store' ),
		'update_item'           => __( 'Update Store', 'wc-pickup-store' ),
		'view_item'             => __( 'View Store', 'wc-pickup-store' ),
		'view_items'            => __( 'View Stores', 'wc-pickup-store' ),
		'search_items'          => __( 'Search Store', 'wc-pickup-store' ),
		'not_found'             => __( 'Not found', 'wc-pickup-store' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wc-pickup-store' ),
		'featured_image'        => __( 'Featured Image', 'wc-pickup-store' ),
		'set_featured_image'    => __( 'Set featured image', 'wc-pickup-store' ),
		'remove_featured_image' => __( 'Remove featured image', 'wc-pickup-store' ),
		'use_featured_image'    => __( 'Use as featured image', 'wc-pickup-store' ),
		'insert_into_item'      => __( 'Insert into item', 'wc-pickup-store' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'wc-pickup-store' ),
		'items_list'            => __( 'Items list', 'wc-pickup-store' ),
		'items_list_navigation' => __( 'Items list navigation', 'wc-pickup-store' ),
		'filter_items_list'     => __( 'Filter items list', 'wc-pickup-store' ),
	);
	$args = array(
		'label'                 => __( 'Store', 'wc-pickup-store' ),
		'description'           => __( 'Stores', 'wc-pickup-store' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', ),
		'taxonomies'            => array(),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-store',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
		'rewrite' => array(
			'slug' => 'store',
		)
	);
	register_post_type( 'store', $args );
}
add_action('init', 'wps_store_post_type');

/**
** Show field in column
**/
function wps_store_id_columns($columns) {
	$new = array();
	unset($columns['date']);

	foreach($columns as $key => $value) {
		if($key == 'title') {
			$new['store_id'] = __('ID', 'wc-pickup-store');
		}
		$new[$key] = $value;
	}
	$new['custom_fields'] = __('Custom Fields', 'wc-pickup-store');

	return $new;
}
add_filter('manage_edit-store_columns', 'wps_store_id_columns');

function wps_store_id_column_content($name, $post_id) {
	$exclude_store = get_post_meta($post_id, '_exclude_store', true);
	$store_country = get_post_meta($post_id, 'store_country', true);

	switch ($name) {
		case 'store_id':
			echo '<a href="' . get_edit_post_link($post_id) . '">' . $post_id . '</a>';
		break;
		case 'custom_fields':
			?>
			<p><strong><?= __('Exclude in Checkout?', 'wc-pickup-store'); ?></strong> <?= ($exclude_store == 1) ? __('Yes', 'wc-pickup-store') : __('No', 'wc-pickup-store'); ?></p>
			<?php if (!empty($store_country)) : ?>
				<p><strong><?= __('Country:', 'wc-pickup-store'); ?> <em><?= $store_country ?></em></strong> <?= WC()->countries->countries[$store_country]; ?></p>
			<?php endif; ?>
			<?php
		break;
	}
}
add_filter('manage_store_posts_custom_column', 'wps_store_id_column_content', 10, 2);

/**
** Activar stores para dropdown checkout
**/
function wps_store_post_meta_box() {
	add_meta_box('checkout-visibility', __( 'Checkout Visibility', 'wc-pickup-store' ), 'wps_store_metabox_content', 'store', 'side', 'high');
	add_meta_box('store-fields', __( 'Store Fields', 'wc-pickup-store' ), 'wps_store_metabox_details_content', 'store', 'normal', 'high');
}
add_action('add_meta_boxes', 'wps_store_post_meta_box');

function wps_store_metabox_content($post) {
	// Display code/markup goes here. Don't forget to include nonces!
	$pid = $post->ID;	
	$exclude_store = get_post_meta( $pid, '_exclude_store', true );

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'wps_store_save_content', 'wps_store_metabox_nonce' );
	?>

	<div class="container_data_metabox">
		<div class="sub_data_poker">
			<p><strong><?php _e('Exclude store in checkout.', 'wc-pickup-store'); ?></strong></p>
			<input type="checkbox" name="exclude_store" class="form-control" <?php checked($exclude_store, 1) ?> />
			
			<input type="hidden" name="save_data_form_custom" value="1"/>
		</div>
	</div>

	<?php
}

function wps_store_metabox_details_content($post) {
	// Display code/markup goes here. Don't forget to include nonces!
	$pid = $post->ID;	
	$city = get_post_meta( $pid, 'city', true );
	$phone = get_post_meta( $pid, 'phone', true );
	$map = get_post_meta( $pid, 'map', true );
	$waze = get_post_meta( $pid, 'waze', true );
	$description = get_post_meta( $pid, 'description', true );
	$address = get_post_meta( $pid, 'address', true );
	$store_shipping_cost = get_post_meta( $pid, 'store_shipping_cost', true );

	$store_order_email = get_post_meta( $pid, 'store_order_email', true );
	$enable_order_email = get_post_meta( $pid, 'enable_order_email', true );

	if (wps_check_countries_count())
		$store_country = get_post_meta( $pid, 'store_country', true );
	$store_country = (!empty($store_country)) ? $store_country : wps_get_wc_default_country();

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'wps_store_save_content', 'wps_store_metabox_nonce' );
	?>
	<table class="form-table">		
		<?php if ($allowed_countries = wps_check_countries_count(false)) : ?>
			<tr>
				<th><?php _e('Country', 'wc-pickup-store') ?></th>
				<td>
					<select name="store_country" class="wc-enhanced-select" id="store-country">
						<option value="-1"><?= __('Choose a Country', 'wc-pickup-store') ?></option>
						<?php foreach ($allowed_countries as $key => $country) : ?>
							<option value="<?= $country['code'] ?>" <?php selected($store_country, $country['code']) ?>><?= $country['name'] ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		<?php else: ?>
			<input type="hidden" name="store_country" value="<?= $store_country ?>">
		<?php endif; ?>

		<?php if (WPS()->costs_per_store == 'yes') : ?>
			<tr>
				<th><?php _e('Store shipping cost', 'wc-pickup-store') ?></th>
				<td>
					<input type="text" name="store_shipping_cost" class="regular-text" value="<?= $store_shipping_cost ?>">
					<p class="description"><?= __('Add shipping cost for this store.', 'wc-pickup-store') ?></p>
			</tr>
		<?php endif; ?>
	
		<tr>
			<th><?php _e('City', 'wc-pickup-store') ?></th>
			<td>
				<input type="text" name="city" class="regular-text" value="<?= $city ?>">
			</td>
		</tr>
		<tr>
			<th><?php _e('Phone', 'wc-pickup-store') ?></th>
			<td>
				<input type="text" name="phone" class="regular-text" value="<?= $phone ?>">
			</td>
		</tr>	
		<tr>
			<th><?php _e('Order Email Notification', 'wc-pickup-store') ?></th>
			<td>
				<input type="text" name="store_order_email" class="regular-text" value="<?= $store_order_email ?>"><br>
				<label for="enable-order-email">
					<input type="checkbox" id="enable-order-email" name="enable_order_email" class="form-control" <?php checked($enable_order_email, 1) ?> /> <?php _e('Enable order email notification', 'wc-pickup-store') ?>
				</label>
				<p class="description"><?= __('Add email to be notified when this store is selected on an order. Comma separated for multiple email addresses.', 'wc-pickup-store') ?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('Waze', 'wc-pickup-store') ?></th>
			<td>
				<textarea name="waze" class="large-text" rows="3"><?= $waze ?></textarea>
				<p class="description"><?= __('Waze link', 'wc-pickup-store') ?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('Map URL', 'wc-pickup-store') ?></th>
			<td>
				<textarea name="map" class="large-text" rows="5"><?= $map ?></textarea>
				<p class="description"><?= __('Add map URL to be embedded. No iframe tag required.', 'wc-pickup-store') ?></p>
			</td>
		</tr>
		<tr>
			<th><?php _e('Short description', 'wc-pickup-store') ?></th>
			<td>
				<?php
					$settings = array('textarea_name' => 'description', 'editor_height' => 75);
					wp_editor($description, 'description', $settings );
				?>
			</td>
		</tr>
		<tr>
			<th><?php _e('Address', 'wc-pickup-store') ?></th>
			<td>
				<?php
					$settings = array('textarea_name' => 'address', 'editor_height' => 75);
					wp_editor($address, 'address', $settings );
				?>
			</td>
		</tr>
	</table>

	<?php
}

function wps_store_save_content($post_id) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['wps_store_metabox_nonce'] ) ) { return; }

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wps_store_metabox_nonce'], 'wps_store_save_content' ) ) { return; }

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }

	// Make sure that it is set.
	// if ( ! isset( $_POST['exclude_store'] ) ) { return; }

	$checked = isset( $_POST['exclude_store'] ) ? 1 : 0;
	$checked_order_email = isset( $_POST['enable_order_email'] ) ? 1 : 0;
	update_post_meta( $post_id, '_exclude_store', $checked );
	update_post_meta( $post_id, 'city', sanitize_text_field($_POST['city']) );
	update_post_meta( $post_id, 'phone', sanitize_text_field($_POST['phone']) );
	update_post_meta( $post_id, 'waze', esc_url($_POST['waze']) );
	update_post_meta( $post_id, 'map', wp_kses_data($_POST['map']) );
	update_post_meta( $post_id, 'description', wp_kses_data($_POST['description']));
	update_post_meta( $post_id, 'address', wp_kses_data($_POST['address']));

	update_post_meta( $post_id, 'store_order_email', sanitize_text_field($_POST['store_order_email']) );
	update_post_meta( $post_id, 'enable_order_email', $checked_order_email );
	update_post_meta( $post_id, 'store_country', sanitize_text_field($_POST['store_country']) );

	if(isset($_POST['store_shipping_cost'])) {
		update_post_meta( $post_id, 'store_shipping_cost', sanitize_text_field($_POST['store_shipping_cost']));
	}
}
add_action('save_post', 'wps_store_save_content');

/**
** Single store template
**/
function wps_single_store_template($template) {
	if (is_singular('store') && $template !== locate_template(array("single-store.php"))) {
		$template = plugin_dir_path(__DIR__) . 'templates/single-store.php';
	}

	return $template;
}
add_filter('single_template', 'wps_single_store_template');

/**
** Archive Template
**/
function wps_store_archive_template($archive_template) {
	if (is_post_type_archive('store') && $archive_template !== locate_template(array("archive-store.php"))) {
		$archive_template = plugin_dir_path(__DIR__) . 'templates/archive-store.php';
	}

	return $archive_template;
}
add_filter('archive_template', 'wps_store_archive_template');

/**
** Check if multicountries are allowed and return data
** Update to disable country filtering
**/
function wps_check_countries_count($only_validate = true) {
	$country_filtering = (isset(WPS()->country_filtering) && WPS()->country_filtering == 'yes') ? true : false;
	
	if (apply_filters('wps_disable_country_filtering', $country_filtering)) {
		return;
	}

	if (count(get_option('woocommerce_specific_allowed_countries')) > 1) {
		if ($only_validate) {
			return true;
		}

		$allowed_countries = array();
		foreach (get_option('woocommerce_specific_allowed_countries') as $key => $country_code) {
			$allowed_countries[] = array(
				'code' => $country_code,
				'name' => WC()->countries->countries[$country_code]
			);
		}
		return $allowed_countries;
	}
	
	return false;
}

/**
** Get default country
**/
function wps_get_wc_default_country() {
	$default_country = get_option('woocommerce_default_country');
	$default_country = explode(':', $default_country);

	return apply_filters('wps_wc_default_country', $default_country[0]);
}

/**
** Get query filtering by Country
**/
function wps_stores_filtering_by_country($country_code) {
	return wps_store_get_store_admin(true, array(
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'store_country',
				'value' => $country_code,
			),
			array(
				'key' => '_exclude_store',
				'compare' => 'EXISTS',
			),
			array(
				'key' => '_exclude_store',
				'value' => '0',
			)
		)
	));
}

/**
** Update stores without Country
**/
function wps_update_stores_without_country() {
	if (isset($_GET['update_country']) && $_GET['update_country'] == 1 && !get_option('wps_countries_updated')) {
		$stores_without_country = wps_store_get_store_admin(true, array(
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'store_country',
					'compare' => 'NOT EXISTS',
				)
			)
		));

		foreach ($stores_without_country as $store_id => $store_data) {
			update_post_meta($store_id, 'store_country', wps_get_wc_default_country());
		}
		update_option('wps_countries_updated', 1);
	}
}
add_action('init', 'wps_update_stores_without_country');