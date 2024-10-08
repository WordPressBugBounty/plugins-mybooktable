<?php

/*---------------------------------------------------------*/
/* Cusom Taxonomies                                        */
/*---------------------------------------------------------*/

function mbt_taxonomies_init() {
	add_action('init', 'mbt_register_taxonomies');
	add_filter('parent_file', 'mbt_override_taxonomy_parent_files');
	add_action('admin_init', 'mbt_taxonomy_images_init');
	add_action('admin_init', 'mbt_author_priorities_init');
	add_action('admin_enqueue_scripts', 'mbt_enqueue_taxonomy_js');
}
add_action('mbt_init', 'mbt_taxonomies_init');

function mbt_enqueue_taxonomy_js() {
	if(!mbt_is_mbt_admin_page()) { return; }

	wp_enqueue_script("mbt-taxonomies", plugins_url('js/taxonomies.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);
}

function mbt_register_taxonomies()
{
	register_taxonomy('mbt_author', 'mbt_book', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __('Authors', 'mybooktable'),
			'singular_name' => __('Author', 'mybooktable'),
			'all_items' => __('All Authors', 'mybooktable'),
			'add_new' => __('Add New', 'mybooktable'),
			'add_new_item' => __('Add New Author', 'mybooktable'),
			'new_item_name' => __('New Author', 'mybooktable'),
			'edit_item' => __('Edit Author', 'mybooktable'),
			'view_item' => __('View Authors', 'mybooktable'),
			'update_item' => __('Update Author', 'mybooktable'),
			'search_items' => __('Search Authors', 'mybooktable'),
			'parent_item' => __('Parent Author', 'mybooktable'),
			'parent_item_colon' => __('Parent Authors:', 'mybooktable'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array('slug' => apply_filters('mbt_author_rewrite_name', _x('authors', 'URL slug', 'mybooktable')))
	));

	register_taxonomy('mbt_genre', 'mbt_book', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __('Genres', 'mybooktable'),
			'singular_name' => __('Genre', 'mybooktable'),
			'all_items' => __('All Genres', 'mybooktable'),
			'add_new' => __('Add New', 'mybooktable'),
			'add_new_item' => __('Add New Genre', 'mybooktable'),
			'new_item_name' => __('New Genre', 'mybooktable'),
			'edit_item' => __('Edit Genre', 'mybooktable'),
			'view_item' => __('View Genres', 'mybooktable'),
			'update_item' => __('Update Genre', 'mybooktable'),
			'search_items' => __('Search Genres', 'mybooktable'),
			'parent_item' => __('Parent Genre', 'mybooktable'),
			'parent_item_colon' => __('Parent Genres:', 'mybooktable'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array('slug' => apply_filters('mbt_genre_rewrite_name', _x('genre', 'URL slug', 'mybooktable')))
	));

	register_taxonomy('mbt_series', 'mbt_book', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __('Series', 'mybooktable'),
			'singular_name' => __('Series', 'mybooktable'),
			'all_items' => __('All Series', 'mybooktable'),
			'add_new' => __('Add New', 'mybooktable'),
			'add_new_item' => __('Add New Series', 'mybooktable'),
			'new_item_name' => __('New Series', 'mybooktable'),
			'edit_item' => __('Edit Series', 'mybooktable'),
			'view_item' => __('View Series', 'mybooktable'),
			'update_item' => __('Update Series', 'mybooktable'),
			'search_items' => __('Search Series', 'mybooktable'),
			'parent_item' => __('Parent Series', 'mybooktable'),
			'parent_item_colon' => __('Parent Series:', 'mybooktable'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array('slug' => apply_filters('mbt_series_rewrite_name', _x('series', 'URL slug', 'mybooktable')))
	));

	register_taxonomy('mbt_tag', 'mbt_book', array(
		'hierarchical' => true,
		'labels' => array(
			'name' => __('Tags', 'mybooktable'),
			'singular_name' => __('Tag', 'mybooktable'),
			'all_items' => __('All Tags', 'mybooktable'),
			'add_new' => __('Add New', 'mybooktable'),
			'add_new_item' => __('Add New Tag', 'mybooktable'),
			'new_item_name' => __('New Tag', 'mybooktable'),
			'edit_item' => __('Edit Tag', 'mybooktable'),
			'view_item' => __('View Tags', 'mybooktable'),
			'update_item' => __('Update Tag', 'mybooktable'),
			'search_items' => __('Search Tags', 'mybooktable'),
			'parent_item' => __('Parent Tag', 'mybooktable'),
			'parent_item_colon' => __('Parent Tags:', 'mybooktable'),
		),
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array('slug' => apply_filters('mbt_tag_rewrite_name', mbt_get_product_slug()._x('tag', 'URL slug', 'mybooktable')))
	));

	if(!(bool)term_exists('recommended', 'mbt_tag')) { wp_insert_term('Recommended Books', 'mbt_tag', array('slug' => 'recommended')); }
}

function mbt_override_taxonomy_parent_files() {
	global $pagenow, $parent_file, $submenu_file;
	$taxonomy = filter_input(INPUT_GET,'taxonomy');
	if(($pagenow == "edit-tags.php" or $pagenow == "term.php") and ($taxonomy == "mbt_series" or $taxonomy == "mbt_genre" or $taxonomy == "mbt_author" or $taxonomy == "mbt_tag")) {
		$parent_file = "mbt_dashboard";
	}

	return $parent_file;
}



/*---------------------------------------------------------*/
/* Custom Images for Taxonomies                            */
/*---------------------------------------------------------*/

function mbt_taxonomy_images_init() {
	add_filter('mbt_author_edit_form_fields', 'mbt_add_taxonomy_image_edit_form');
	add_filter('mbt_author_add_form_fields', 'mbt_add_taxonomy_image_add_form');
	add_action('edited_mbt_author', 'mbt_save_taxonomy_image_edit_form');
	add_action('created_mbt_author', 'mbt_save_taxonomy_image_add_form');

	add_filter('mbt_genre_edit_form_fields', 'mbt_add_taxonomy_image_edit_form');
	add_filter('mbt_genre_add_form_fields', 'mbt_add_taxonomy_image_add_form');
	add_action('edited_mbt_genre', 'mbt_save_taxonomy_image_edit_form');
	add_action('created_mbt_genre', 'mbt_save_taxonomy_image_add_form');

	add_filter('mbt_series_edit_form_fields', 'mbt_add_taxonomy_image_edit_form');
	add_filter('mbt_series_add_form_fields', 'mbt_add_taxonomy_image_add_form');
	add_action('edited_mbt_series', 'mbt_save_taxonomy_image_edit_form');
	add_action('created_mbt_series', 'mbt_save_taxonomy_image_add_form');

	add_filter('mbt_tag_edit_form_fields', 'mbt_add_taxonomy_image_edit_form');
	add_filter('mbt_tag_add_form_fields', 'mbt_add_taxonomy_image_add_form');
	add_action('edited_mbt_tag', 'mbt_save_taxonomy_image_edit_form');
	add_action('created_mbt_tag', 'mbt_save_taxonomy_image_add_form');
}

function mbt_add_taxonomy_image_edit_form() {
	$taxonomy = filter_input(INPUT_GET,'taxonomy');
	$tag_ID = filter_input(INPUT_GET,'tag_ID');
	$mbt_nonce = wp_nonce_field('mbt-tax-img-edit-nonce','data-tax-img-edit-nonce');
?>
	<tr class="form-field">
		<th><label for="mbt_tax_image_url"><?php esc_attr_e('Image', 'mybooktable') ?></label></th>
		<td>
			<?php echo esc_attr($mbt_nonce); 
			$mbt_tax_image = mbt_get_taxonomy_image($taxonomy, $tag_ID);
			?>
			<input type="text" id="mbt_tax_image_url" name="mbt_tax_image_url" value="<?php echo(wp_kses_post($mbt_tax_image));?>" />
			<input id="mbt_upload_tax_image_button" class="button mbt_upload_button" data-upload-target="mbt_tax_image_url" data-upload-title="<?php esc_attr_e('Taxonomy Image', 'mybooktable'); ?>" type="button" value="<?php esc_attr_e('Upload', 'mybooktable'); ?>" />
		</td>
	</tr>
<?php
}

function mbt_add_taxonomy_image_add_form() {
	$mbt_nonce = wp_nonce_field('mbt-tax-img-add-nonce','data-tax-img-add-nonce');
?>
	<div class="form-field">
		<label for="mbt_tax_image_url"><?php esc_attr_e('Image', 'mybooktable') ?></label>
		<?php echo esc_attr($mbt_nonce); ?>
		<input type="text" id="mbt_tax_image_url" name="mbt_tax_image_url" value="" />
		<input id="mbt_upload_tax_image_button" class="button mbt_upload_button" data-upload-target="mbt_tax_image_url" data-upload-title="<?php esc_attr_e('Taxonomy Image', 'mybooktable'); ?>" type="button" value="<?php esc_attr_e('Upload', 'mybooktable'); ?>" />
	</div>
<?php
}

function mbt_save_taxonomy_image_edit_form() {
	if( isset($_REQUEST['data-tax-img-edit-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-tax-img-edit-nonce']),'mbt-tax-img-edit-nonce') ) {
		if(isset($_REQUEST['taxonomy']) and isset($_REQUEST['tag_ID']) and isset($_REQUEST['mbt_tax_image_url'])) { 
			mbt_save_taxonomy_image(sanitize_text_field(wp_unslash($_REQUEST['taxonomy'])), sanitize_text_field(wp_unslash($_REQUEST['tag_ID'])), sanitize_text_field(wp_unslash($_REQUEST['mbt_tax_image_url'])));
		}
	}
}

function mbt_save_taxonomy_image_add_form($term_id) {
	if( isset($_REQUEST['data-tax-img-add-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-tax-img-add-nonce']),'mbt-tax-img-add-nonce') ) {
		if(isset($_REQUEST['taxonomy']) and isset($_REQUEST['mbt_tax_image_url'])) {
			mbt_save_taxonomy_image(sanitize_text_field(wp_unslash($_REQUEST['taxonomy'])), $term_id, sanitize_text_field(wp_unslash($_REQUEST['mbt_tax_image_url'])));
		}
	}
}



/*---------------------------------------------------------*/
/* Author Priority Sorting                                 */
/*---------------------------------------------------------*/

function mbt_author_priorities_init() {
	add_filter('mbt_author_edit_form_fields', 'mbt_add_author_priority_edit_form');
	add_filter('mbt_author_add_form_fields', 'mbt_add_author_priority_add_form');
	add_action('edited_mbt_author', 'mbt_save_author_priority_edit_form');
	add_action('created_mbt_author', 'mbt_save_author_priority_add_form');
}

function mbt_add_author_priority_edit_form() {
	$mbt_nonce = wp_nonce_field('mbt-authpri-ed-nonce','data-authpri-ed-nonce');
	$tag_ID = filter_input(INPUT_GET,'tag_ID');
?>
	<tr class="form-field">
		<th><label for="mbt_author_priority"><?php esc_attr_e('Priority', 'mybooktable') ?></label></th>
		<td>
			<?php echo esc_attr($mbt_nonce); ?>
			<input type="text" id="mbt_author_priority" name="mbt_author_priority" value="<?php echo(esc_attr(mbt_get_author_priority(intval($tag_ID)))); ?>" />
			<div id="mbt_author_priority_slider"></div>
			<div id="mbt_author_priority_display"></div>
			<div style="clear:both"></div>
			<p class="description">Authors with higher priority will be shown first in the list of authors when a book has multiple authors.</p>
		</td>
	</tr>
<?php
}

function mbt_add_author_priority_add_form() {
	$mbt_nonce = wp_nonce_field('mbt-authpri-add-nonce','data-authpri-add-nonce');
?>
	<div class="form-field">
		<label for="mbt_author_priority"><?php esc_attr_e('Priority', 'mybooktable') ?></label>
		<?php echo esc_attr($mbt_nonce); ?>
		<input type="text" id="mbt_author_priority" name="mbt_author_priority" value="50" />
		<div id="mbt_author_priority_slider"></div>
		<div id="mbt_author_priority_display"></div>
		<div style="clear:both"></div>
		<p class="description">Authors with higher priority will be shown first in the list of authors when a book has multiple authors.</p>
	</div>
<?php
}

function mbt_save_author_priority_edit_form() {
	if(isset($_REQUEST['data-authpri-ed-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-authpri-ed-nonce']),'mbt-authpri-ed-nonce') ) {
		if(isset($_REQUEST['taxonomy']) and isset($_REQUEST['tag_ID']) and isset($_REQUEST['mbt_author_priority'])) {
			mbt_save_author_priority(sanitize_text_field(wp_unslash($_REQUEST['tag_ID'])), sanitize_text_field(wp_unslash($_REQUEST['mbt_author_priority'])));
		}
	}
}

function mbt_save_author_priority_add_form($term_id) {
	if(isset($_REQUEST['data-authpri-add-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-authpri-add-nonce']),'mbt-authpri-ed-nonce') ) {
		if(isset($_REQUEST['taxonomy']) and isset($_REQUEST['mbt_author_priority'])) {
			mbt_save_author_priority($term_id, sanitize_text_field(wp_unslash($_REQUEST['mbt_author_priority'])));
		}
	}
}
