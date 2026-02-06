<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*---------------------------------------------------------*/
/* Settings Functions                                      */
/*---------------------------------------------------------*/

function mbt_load_settings() {
	global $mbt_settings;
	$mbt_settings = apply_filters("mbt_settings", get_option("mbt_settings"));
	if(empty($mbt_settings)) { mbt_reset_settings(); }
}

function mbt_reset_settings() {
	global $mbt_settings;
	$mbt_settings = array(
		'version' => MBT_VERSION,
		'api_key' => '',
		'api_key_status' => 0,
		'api_key_message' => '',
		'upgrade_active' => false,
		'installed' => '',
		'installed_examples' => false,
		'booktable_page' => 0,
		'compatibility_mode' => true,
		'style_pack' => mbt_get_default_style_pack(),
		'image_size' => 'medium',
		'reviews_type' => 'none',
		'enable_socialmedia_single_book' => false,
		'enable_socialmedia_book_excerpt' => false,
		'enable_seo' => true,
		'buybutton_shadowbox' => 'none',
		'enable_breadcrumbs' => true,
		'show_series' => true,
		'show_find_bookstore' => true,
		'show_find_bookstore_buybuttons_shadowbox' => true,
		'show_about_author' => true,
		'book_button_size' => 'medium',
		'listing_button_size' => 'medium',
		'widget_button_size' => 'medium',
		'posts_per_page' => 12,
		'enable_default_affiliates' => false,
		'google_api_key' => '',
		'product_name' => __('Books', 'mybooktable'),
		'product_slug' => _x('books', 'URL slug', 'mybooktable'),
		'hide_domc_notice' => false,
		'domc_notice_text' => __('Disclosure of Material Connection: Some of the links in the page above are "affiliate links." This means if you click on the link and purchase the item, I will receive an affiliate commission. I am disclosing this in accordance with the Federal Trade Commission\'s <a href="https://www.access.gpo.gov/nara/cfr/waisidx_03/16cfr255_03.html" target="_blank">16 CFR, Part 255</a>: "Guides Concerning the Use of Endorsements and Testimonials in Advertising."', 'mybooktable'),
	);
	$mbt_settings = apply_filters("mbt_default_settings", $mbt_settings);
	update_option("mbt_settings", apply_filters("mbt_update_settings", $mbt_settings));
}

function mbt_get_setting($name) {
	global $mbt_settings;
	return isset($mbt_settings[$name]) ? $mbt_settings[$name] : NULL;
}

function mbt_update_setting($name, $value) {
	global $mbt_settings;
	$mbt_settings[$name] = $value;
	update_option("mbt_settings", apply_filters("mbt_update_settings", $mbt_settings));
}



/*---------------------------------------------------------*/
/* General                                                 */
/*---------------------------------------------------------*/

function mbt_save_taxonomy_image($taxonomy, $term, $url) {
	$taxonomy_images = get_option($taxonomy."_meta");
	if(empty($taxonomy_images)) { $taxonomy_images = array(); }
	$taxonomy_images[$term] = $url;
	update_option($taxonomy."_meta", $taxonomy_images);
}

function mbt_get_taxonomy_image($taxonomy, $term) {
	$taxonomy_images = get_option($taxonomy."_meta");
	if(empty($taxonomy_images)) { $taxonomy_images = array(); }
	return isset($taxonomy_images[$term]) ? $taxonomy_images[$term] : '';
}

function mbt_save_author_priority($author_id, $priority) {
	$author_priorities = mbt_get_setting("author_priorities");
	if(empty($author_priorities)) { $author_priorities = array(); }
	$author_priorities[$author_id] = $priority;
	mbt_update_setting("author_priorities", $author_priorities);
}

function mbt_get_author_priority($author_id) {
	$author_priorities = mbt_get_setting("author_priorities");
	if(empty($author_priorities)) { $author_priorities = array(); }
	return isset($author_priorities[$author_id]) ? $author_priorities[$author_id] : 50;
}

function mbt_get_posts_per_page() {
	$posts_per_page = mbt_get_setting('posts_per_page');
	return empty($posts_per_page) ? get_option('posts_per_page') : $posts_per_page;
}

function mbt_is_mbt_page() {
	return (is_post_type_archive('mbt_book') or is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') or is_tax('mbt_tag') or is_singular('mbt_book') or mbt_is_booktable_page() or mbt_is_archive_query());
}

function mbt_is_mbt_admin_page() {
	global $pagenow;
	$screen = get_current_screen();
	return is_admin() and (
		($pagenow == 'edit.php' and $screen->post_type == 'mbt_book') or
		($pagenow == 'post.php' and $screen->post_type == 'mbt_book') or
		($pagenow == 'post-new.php' and $screen->post_type == 'mbt_book') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_author') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_genre') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_series') or
		(($pagenow == 'edit-tags.php' or $pagenow == 'term.php') and $screen->taxonomy == 'mbt_tag') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_import') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_sort_books') or
		($pagenow == 'admin.php' and $screen->id == 'toplevel_page_mbt_dashboard') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_settings') or
		($pagenow == 'admin.php' and $screen->id == 'mybooktable_page_mbt_help')
	);
}

function mbt_is_booktable_page() {
	global $mbt_is_booktable_page;
	return !empty($mbt_is_booktable_page);
}

function mbt_get_booktable_url() {
	if(mbt_get_setting('booktable_page') <= 0 or !get_page(mbt_get_setting('booktable_page'))) {
		$url = get_post_type_archive_link('mbt_book');
	} else {
		$url = get_permalink(mbt_get_setting('booktable_page'));
	}
	return $url;
}

function mbt_get_product_name() {
	$name = mbt_get_setting('product_name');
	return apply_filters('mbt_product_name', empty($name) ? __('Books', 'mybooktable') : $name);
}

function mbt_get_product_slug() {
	$slug = mbt_get_setting('product_slug');
	return apply_filters('mbt_product_slug', empty($slug) ? _x('books', 'URL slug', 'mybooktable') : $slug);
}

function mbt_get_reviews_types() {
	// The 'mbt_reviews_boxes' filter is deprecated, but still supported
	return apply_filters('mbt_reviews_boxes', apply_filters('mbt_reviews_types', array()));
}

function mbt_add_disabled_reviews_types($reviews) {
	$reviews['amazon'] = array(
		'name' => __('Amazon Reviews','mybooktable'),
		'disabled' => mbt_get_upgrade_message(),
	);
	return $reviews;
}
//add_filter('mbt_reviews_types', 'mbt_add_disabled_reviews_types', 9);

function mbt_get_wp_filesystem($nonce_url) {
	require_once(ABSPATH.'wp-admin/includes/file.php');

	ob_start();
	$creds = request_filesystem_credentials($nonce_url, '', false, false, null);
	$output = ob_get_contents();
	ob_end_clean();
	if($creds === false) { return $output; }

	if(!WP_Filesystem($creds)) {
		ob_start();
		request_filesystem_credentials($nonce_url, '', true, false, null);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	return '';
}

function mbt_download_and_insert_attachment($url) {
	$raw_response = wp_remote_get($url, array('timeout' => 3));
	if(is_wp_error($raw_response) or wp_remote_retrieve_response_code($raw_response) != 200) { return 0; }
	$file_data = wp_remote_retrieve_body($raw_response);

	$nonce_url = wp_nonce_url('admin.php', 'mbt_download_and_insert_attachment');
	$output = mbt_get_wp_filesystem($nonce_url);
	if(!empty($output)) { return 0;	}
	global $wp_filesystem, $wpdb;

	$url_parts = wp_parse_url($url);
	$filename = basename($url_parts['path']);
	$filename = preg_replace('/[^A-Za-z0-9_.]/', '', $filename);
	$upload_dir = wp_upload_dir();
	$filepath = $upload_dir['path'].'/'.$filename;
	$fileurl = $upload_dir['url'].'/'.$filename;

	if($wp_filesystem->exists($filepath)) {
		$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $fileurl));
		if(!empty($existing_id)) { return $existing_id; } else { return 0; }
	}

	if(!$wp_filesystem->put_contents($filepath, $file_data, FS_CHMOD_FILE)) { return 0; }

	$filetype = wp_check_filetype(basename($filepath), null);
	$attachment = array(
		'guid'           => $fileurl,
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment($attachment, $filepath);

	require_once(ABSPATH.'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
	wp_update_attachment_metadata($attach_id, $attach_data);

	return $attach_id;
}

function mbt_copy_and_insert_attachment($path) {
	$nonce_url = wp_nonce_url('admin.php', 'mbt_copy_and_insert_attachment');
	$output = mbt_get_wp_filesystem($nonce_url);
	if(!empty($output)) { return 0;	}
	global $wp_filesystem, $wpdb;

	$filename = basename($path);
	$filename = preg_replace('/[^A-Za-z0-9_.]/', '', $filename);
	$upload_dir = wp_upload_dir();
	$filepath = $upload_dir['path'].'/'.$filename;
	$fileurl = $upload_dir['url'].'/'.$filename;

	if($wp_filesystem->exists($filepath)) {
		$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $fileurl));
		if(!empty($existing_id)) { return $existing_id; } else { return 0; }
	}

	if(!$wp_filesystem->copy($path, $filepath, false, FS_CHMOD_FILE)) { return 0; }

	$filetype = wp_check_filetype(basename($filepath), null);
	$attachment = array(
		'guid'           => $fileurl,
		'post_mime_type' => $filetype['type'],
		'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content'   => '',
		'post_status'    => 'inherit'
	);

	$attach_id = wp_insert_attachment($attachment, $filepath);

	require_once(ABSPATH.'wp-admin/includes/image.php');
	$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
	wp_update_attachment_metadata($attach_id, $attach_data);

	return $attach_id;
}

function mbt_add_book_pages_to_front_page_options($pages, $args) {
	if(!is_array($args) or !isset($args['name']) or $args['name'] !== 'page_on_front') { return $pages; }

	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	$books = $books_query->posts;
	foreach($books as $book) {
		$book->post_title = __('Book Page', 'mybooktable').': '.$book->post_title;
	}

	return array_merge($pages, $books);
}
add_filter('get_pages', 'mbt_add_book_pages_to_front_page_options', 10, 2);

function mbt_push_query($name, $query) {
	global $wp_query, $posts, $post, $id, $mbt_query_stack;
	if(empty($mbt_query_stack)) { $mbt_query_stack = array(); }
	$mbt_query_stack[] = array(
		'name' => $name,
		'wp_query' => $wp_query,
		'posts' => $posts,
		'post' => $post,
		'id' => $id,
	);
	$wp_query = $query;
	$posts = $query->posts;
	$post = $query->post;
	$id = $query->post ? $query->post->ID : NULL;
}

function mbt_pop_query($name) {
	global $wp_query, $posts, $post, $id, $mbt_query_stack;
	if(empty($mbt_query_stack)) { return; }
	$query = array_pop($mbt_query_stack);
	if($name !== $query['name']) {
		trigger_error('MyBookTable tried to close '.esc_attr($name).' query but was prevented by '.esc_attr($query['name']).' query!', E_USER_WARNING);
		$mbt_query_stack[] = $query;
		return;
	}
	$wp_query = $query['wp_query'];
	$posts = $query['posts'];
	$post = $query['post'];
	$id = $query['id'];
}



/*---------------------------------------------------------*/
/* Display Modes                                           */
/*---------------------------------------------------------*/

function mbt_get_book_display_modes() {
	$modes = apply_filters('mbt_display_modes', array());
	foreach($modes as $slug => $mode) {
		if(empty($modes[$slug]['name'])) { $modes[$slug]['name'] = __('Unnamed', 'mybooktable'); }
		if(empty($modes[$slug]['supports'])) { $modes[$slug]['supports'] = array(); }
	}
	return $modes;
}

function mbt_add_default_book_display_modes($modes) {
	$modes['storefront'] = array('name' => __('Storefront', 'mybooktable'), 'supports' => array('embedding', 'sale_price'));
	$modes['singlecolumn'] = array('name' => __('Beautiful Page', 'mybooktable'), 'supports' => array('embedding', 'teaser', 'sortable_sections'));
	return $modes;
}
add_filter('mbt_display_modes', 'mbt_add_default_book_display_modes');

function mbt_get_default_book_display_mode() {
	return apply_filters('mbt_default_book_display_mode', 'singlecolumn');
}

function mbt_get_book_display_mode($post_id) {
	$display_mode = get_post_meta($post_id, 'mbt_display_mode', true);
	$display_modes = mbt_get_book_display_modes();
	if(empty($display_modes[$display_mode])) { $display_mode = mbt_get_default_book_display_mode(); }
	return $display_mode;
}

function mbt_book_display_mode_supports($display_mode, $supports) {
	$display_modes = mbt_get_book_display_modes();
	if(empty($display_modes[$display_mode])) { $display_mode = mbt_get_default_book_display_mode(); }
	return array_search($supports, $display_modes[$display_mode]['supports']) !== FALSE;
}

add_action('admin_footer-edit.php', 'mbt_book_bulk_change_display_mode_admin_footer');
function mbt_book_bulk_change_display_mode_admin_footer() {
	$display_modes = mbt_get_book_display_modes();
	global $post_type;
	if($post_type == 'mbt_book') {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				<?php foreach($display_modes as $slug => $mode) { ?>
				jQuery('<option>').val('mbt-switch-mode-<?php echo(esc_attr($slug)); ?>').text(mbt_admin_pages_i18n.swich_to_mode.replace('%s', '<?php htmlspecialchars($mode['name'], ENT_QUOTES); ?>')).appendTo('#bulk-action-selector-top');
				jQuery('<option>').val('mbt-switch-mode-<?php echo(esc_attr($slug)); ?>').text(mbt_admin_pages_i18n.swich_to_mode.replace('%s', '<?php htmlspecialchars($mode['name'], ENT_QUOTES); ?>')).appendTo('#bulk-action-selector-bottom');
				<?php } ?>
			});
		</script>
	<?php
	}
}

add_action('load-edit.php', 'mbt_book_bulk_change_display_mode_action');
function mbt_book_bulk_change_display_mode_action() {
	$wp_list_table = _get_list_table('WP_Posts_List_Table');
	$action = $wp_list_table->current_action();

	if(substr($action, 0, 16) == 'mbt-switch-mode-') {
		check_admin_referer('bulk-posts');

		$selected_mode = substr($action, 16);

		$display_modes = mbt_get_book_display_modes();
		if(empty($display_modes[$selected_mode])) { return; }

		$post_ids = array_map('intval', isset($_REQUEST['post']) ? $_REQUEST['post'] : array());
		if(empty($post_ids)) { return; }

		$books_updated = 0;
		foreach($post_ids as $post_id) {
			update_post_meta($post_id, 'mbt_display_mode', $selected_mode);

			$books_updated++;
		}

		wp_safe_redirect(add_query_arg(array('paged' => $wp_list_table->get_pagenum(), 'mbt-books-updated' => $books_updated), admin_url('edit.php?post_type=mbt_book')));
		exit();
	}
}

add_action('admin_notices', 'mbt_book_bulk_change_display_mode_admin_notices');
function mbt_book_bulk_change_display_mode_admin_notices() {
	global $post_type, $pagenow;
	$books_updated = filter_input( INPUT_GET,'mbt-books-updated');
	if($pagenow == 'edit.php' && $post_type == 'mbt_book' && isset($books_updated) && intval($books_updated)) {
		/* translators: %s: the number of books */
		$message = sprintf(_n('%s Book updated.', '%s books updated.', intval($books_updated)), number_format_i18n(intval($books_updated)));
		echo('<div class="updated"><p>'.esc_attr($message).'</p></div>');
	}
}

function mbt_get_sorted_content_sections($display_mode) {
	$sections = apply_filters('mbt_get_'.$display_mode.'_content_sections', array());
	$prioritized_sections = array();
	foreach($sections as $id => $section) { $prioritized_sections[] = array_merge($section, array('id' => $id)); }
	$prioritize = function($a, $b) { return ($a['priority'] == $b['priority']) ? 0 : (($a['priority'] < $b['priority']) ? -1 : 1); };
	usort($prioritized_sections, $prioritize);
	$sections_order = mbt_get_setting('book_section_order_'.$display_mode);
	if(empty($sections_order)) { $sections_order = array(); }
	$sorted_sections = array();
	$order_index = 0;
	foreach($prioritized_sections as $section) {
		$index = array_search($section['id'], $sections_order);
		if($index === false) {
			$sorted_sections[] = $section;
		} else {
			while(empty($sections[$sections_order[$order_index]])) { $order_index += 1; }
			$order_section = $sections[$sections_order[$order_index]];
			$sorted_sections[] = array_merge($order_section, array('id' => $sections_order[$order_index], 'priority' => $section['priority']));
			$order_index += 1;
		}
	}
	return $sorted_sections;
}

function mbt_render_book_section($post_id, $section_id, $content, $title='') {
	$output = '';
	$title = apply_filters('mbt_book_section_title', $title, $post_id, $section_id);
	$content = apply_filters('mbt_book_section_content', $content, $post_id, $section_id);
	if(!empty($content)) {
		$output .= '<a class="mbt-book-anchor" id="mbt-book-'.$section_id.'-anchor" name="mbt-book-'.$section_id.'-anchor"></a>';
		$output .= '<div class="mbt-book-section mbt-book-'.$section_id.'-section">';
		if($title) { $output .= '<div class="mbt-book-section-title">'.$title.'</div>'; }
		$output .= '<div class="mbt-book-section-content">'.$content.'</div>';
		$output .= '</div>';
	}
	return apply_filters('mbt_render_book_section', $output, $post_id, $section_id, $content, $title);
}



/*---------------------------------------------------------*/
/* Importers                                               */
/*---------------------------------------------------------*/

function mbt_get_importers() {
	return apply_filters('mbt_importers', array());
}

function mbt_add_disabled_importers($importers) {
	/*$importers['amazon'] = array(
		'name' => __('Amazon Bulk Book Importer', 'mybooktable'),
		'desc' => __('Import your books in bulk from Amazon with a list of ISBNs.', 'mybooktable'),
		'disabled' => mbt_get_upgrade_message(),
	);*/
	$importers['uiee'] = array(
		'name' => __('UIEE File', 'mybooktable'),
		'desc' => __('Import your books from a UIEE (Universal Information Exchange Environment) File.', 'mybooktable'),
		'disabled' => mbt_get_upgrade_message(),
	);
	return $importers;
}
add_filter('mbt_importers', 'mbt_add_disabled_importers', 9);

function mbt_import_book($book) {
	$defaults = array(
		'post_status' => 'publish',
		'source_id' => null,
		'title' => '',
		'content' => '',
		'excerpt' => '',
		'authors' => array(),
		'series' => array(),
		'genres' => array(),
		'tags' => array(),
		'buybuttons' => '',
		'image_id' => '',
		'unique_id_isbn' => '',
		'unique_id_asin' => '',
		'series_order' => '',
		'display_mode' => '',
		'show_instant_preview' => '',
		'sample_url' => '',
		'sample_audio' => '',
		'sample_video' => '',
		'price' => '',
		'sale_price' => '',
		'ebook_price' => '',
		'audiobook_price' => '',
		'publisher_name'  => '',
		'publisher_url'  => '',
		'publication_year' => '',
		'star_rating' => '',
		'book_format' => '',
		'book_length' => '',
		'narrator' => '',
		'illustrator' => '',
		'bg_color' => '',
		'bg_color_alt' => '',
		'button_color' => '',
		'imported_book_id' => '',
	);
	$book = array_merge($defaults, $book);
	if(!empty($book['unique_id'])) { $book['unique_id_isbn'] = $book['unique_id']; }

	if(!empty($book['imported_book_id']) and ($imported_book = get_post($book['imported_book_id']))) {
		$post_id = wp_update_post(array(
			'ID' => $imported_book->ID,
			'post_title' => $book['title'],
		));
		$old_buybuttons = get_post_meta($post_id, 'mbt_buybuttons', true);
		if(empty($old_buybuttons)) { update_post_meta($post_id, 'mbt_buybuttons', $book['buybuttons']); }
		if(!empty($book['image_id'])) { update_post_meta($post_id, 'mbt_book_image_id', $book['image_id']); }
		if(!empty($book['unique_id_isbn'])) { update_post_meta($post_id, 'unique_id_isbn', $book['unique_id_isbn']); }
		if(!empty($book['unique_id_asin'])) { update_post_meta($post_id, 'unique_id_asin', $book['unique_id_asin']); }
		if(!empty($book['series_order'])) { update_post_meta($post_id, 'mbt_series_order', $book['series_order']); }
		if(!empty($book['display_mode'])) { update_post_meta($post_id, 'mbt_display_mode', $book['display_mode']); }
		if(!empty($book['authors'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['authors'], 'mbt_author'), 'mbt_author'); }
		if(!empty($book['series'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['series'], 'mbt_series'), 'mbt_series'); }
		if(!empty($book['genres'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['genres'], 'mbt_genre'), 'mbt_genre'); }
		if(!empty($book['tags'])) { wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['tags'], 'mbt_tag'), 'mbt_tag'); }
		if(!empty($book['show_instant_preview'])) { update_post_meta($post_id, 'mbt_show_instant_preview', $book['show_instant_preview']); }
		if(!empty($book['sample_url'])) { update_post_meta($post_id, 'mbt_sample_url', $book['sample_url']); }
		if(!empty($book['sample_audio'])) { update_post_meta($post_id, 'mbt_sample_audio', $book['sample_audio']); }
		if(!empty($book['sample_video'])) { update_post_meta($post_id, 'mbt_sample_video', $book['sample_video']); }
		if(!empty($book['price'])) { update_post_meta($post_id, 'mbt_price', $book['price']); }
		if(!empty($book['sale_price'])) { update_post_meta($post_id, 'mbt_sale_price', $book['sale_price']); }
		if(!empty($book['ebook_price'])) { update_post_meta($post_id, 'mbt_ebook_price', $book['ebook_price']); }
		if(!empty($book['audiobook_price'])) { update_post_meta($post_id, 'mbt_audiobook_price', $book['audiobook_price']); }
		if(!empty($book['publisher_name'])) { update_post_meta($post_id, 'mbt_publisher_name', $book['publisher_name']); }
		if(!empty($book['publisher_url'])) { update_post_meta($post_id, 'mbt_publisher_url', $book['publisher_url']); }
		if(!empty($book['publication_year'])) { update_post_meta($post_id, 'mbt_publication_year', $book['publication_year']); }
		if(!empty($book['star_rating'])) { update_post_meta($post_id, 'mbt_star_rating', $book['star_rating']); }
		if(!empty($book['book_format'])) { update_post_meta($post_id, 'mbt_book_format', $book['book_format']); }
		if(!empty($book['book_length'])) { update_post_meta($post_id, 'mbt_book_length', $book['book_length']); }
		if(!empty($book['narrator'])) { update_post_meta($post_id, 'mbt_narrator', $book['narrator']); }
		if(!empty($book['illustrator'])) { update_post_meta($post_id, 'mbt_illustrator', $book['illustrator']); }
		if(!empty($book['bg_color'])) { update_post_meta($post_id, 'mbt_bg_color', $book['bg_color']); }
		if(!empty($book['bg_color_alt'])) { update_post_meta($post_id, 'mbt_bg_color_alt', $book['bg_color_alt']); }
		if(!empty($book['button_color'])) { update_post_meta($post_id, 'mbt_button_color', $book['button_color']); }
	} else {
		$post_id = wp_insert_post(array(
			'post_title' => $book['title'],
			'post_content' => $book['content'],
			'post_excerpt' => $book['excerpt'],
			'post_status' => $book['post_status'],
			'post_type' => 'mbt_book'
		));
		update_post_meta($post_id, 'mbt_buybuttons', $book['buybuttons']);
		update_post_meta($post_id, 'mbt_unique_id_isbn', $book['unique_id_isbn']);
		update_post_meta($post_id, 'mbt_unique_id_asin', $book['unique_id_asin']);
		update_post_meta($post_id, 'mbt_series_order', $book['series_order']);
		update_post_meta($post_id, 'mbt_display_mode', $book['display_mode']);
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['authors'], 'mbt_author'), 'mbt_author');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['series'], 'mbt_series'), 'mbt_series');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['genres'], 'mbt_genre'), 'mbt_genre');
		wp_set_object_terms($post_id, mbt_import_taxonomy_terms($book['tags'], 'mbt_tag'), 'mbt_tag');
		update_post_meta($post_id, 'mbt_show_instant_preview', $book['show_instant_preview']);
		update_post_meta($post_id, 'mbt_sample_url', $book['sample_url']);
		update_post_meta($post_id, 'mbt_sample_audio', $book['sample_audio']);
		update_post_meta($post_id, 'mbt_sample_video', $book['sample_video']);
		update_post_meta($post_id, 'mbt_price', $book['price']);
		update_post_meta($post_id, 'mbt_sale_price', $book['sale_price']);
		update_post_meta($post_id, 'mbt_ebook_price', $book['ebook_price']);
		update_post_meta($post_id, 'mbt_audiobook_price', $book['audiobook_price']);
		update_post_meta($post_id, 'mbt_publisher_name', $book['publisher_name']);
		update_post_meta($post_id, 'mbt_publisher_url', $book['publisher_url']);
		update_post_meta($post_id, 'mbt_publication_year', $book['publication_year']);
		update_post_meta($post_id, 'mbt_star_rating', $book['star_rating']);
		update_post_meta($post_id, 'mbt_book_format', $book['book_format']);
		update_post_meta($post_id, 'mbt_book_length', $book['book_length']);
		update_post_meta($post_id, 'mbt_narrator', $book['narrator']);
		update_post_meta($post_id, 'mbt_illustrator', $book['illustrator']);
		update_post_meta($post_id, 'mbt_bg_color', $book['bg_color']);
		update_post_meta($post_id, 'mbt_bg_color_alt', $book['bg_color_alt']);
		update_post_meta($post_id, 'mbt_button_color', $book['button_color']);

		if(!empty($book['source_id'])) { update_post_meta($book['source_id'], 'mbt_imported_book_id', $post_id); }
		
		// download and import the book image
		if(isset($book['image_src']) && !empty($book['image_src'])) {
			$imgurl = stripslashes($book['image_src']);
			$attach_id = sh_mbt_download_and_insert_attachment_2($imgurl,$book['title']);
			if(isset($attach_id) && !empty($attach_id)){
				update_post_meta($post_id, 'mbt_book_image_id', $attach_id);
			}
		}
	}
	return $post_id;
}

function mbt_import_taxonomy_terms($term_names, $taxonomy) {
	$returns = array();
	foreach($term_names as $name) {
		if(term_exists($name, $taxonomy)) {
			$new_term = (array)get_term_by('name', $name, $taxonomy);
		} else {
			$new_term = (array)wp_insert_term($name, $taxonomy);
		}
		$returns[] = $new_term['term_id'];
	}
	return $returns;
}



/*---------------------------------------------------------*/
/* Pages                                                   */
/*---------------------------------------------------------*/

function mbt_add_custom_page($name, $function, $permissions="edit_posts") {
	$add_page = function() use ($name, $permissions, $function) {
		add_submenu_page("mbt_dashboard", "", "", $permissions, $name, $function);
	};

	$remove_page = function() use ($name) {
		remove_submenu_page("mbt_dashboard", $name);
	};

	add_action('admin_menu', $add_page, 9);
	add_action('admin_head', $remove_page);
}

function mbt_get_custom_page_url($name) {
	return admin_url('admin.php?page='.$name);
}



/*---------------------------------------------------------*/
/* Styles                                                  */
/*---------------------------------------------------------*/

function mbt_image_url($image) {
	$url = mbt_current_style_url($image);
	return apply_filters('mbt_image_url', empty($url) ? plugins_url('styles/'.mbt_get_default_style_pack().'/'.$image, dirname(__FILE__)) : $url, $image);
}

function mbt_current_style_url($file) {
	$style = mbt_get_setting('style_pack');
	if(empty($style)) { $style = mbt_get_default_style_pack(); }

	$url = mbt_style_url($file, $style);
	if(empty($url) and $style !== mbt_get_default_style_pack()) { $url = mbt_style_url($file, mbt_get_default_style_pack()); }

	return $url;
}

function mbt_style_url($file, $style) {
	foreach(mbt_get_style_folders() as $folder) {
		if(file_exists($folder['dir'].'/'.$style)) {
			if(file_exists($folder['dir'].'/'.$style.'/'.$file)) {
				return $folder['url'].'/'.rawurlencode($style).'/'.$file;
			}
		}
	}

	$meta = mbt_get_style_pack_meta($style);
	if($meta['template']) { return mbt_style_url($file, $meta['template']); }

	return '';
}

function mbt_get_style_packs() {
	$folders = mbt_get_style_folders();
	$styles = array();

	foreach($folders as $folder) {
		if(file_exists($folder['dir']) and $handle = opendir($folder['dir'])) {
			while(false !== ($entry = readdir($handle))) {
				if ($entry != '.' and $entry != '..' and !in_array($entry, $styles)) {
					$styles[] = $entry;
				}
			}
			closedir($handle);
		}
	}

	sort($styles);
	return $styles;
}

function mbt_get_style_pack_meta($style) {
	$default_headers = array(
		'name' => 'Style Pack Name',
		'stylepack_uri' => 'Style Pack URI',
		'template' => 'Template',
		'version' => 'Version',
		'desc' => 'Description',
		'author' => 'Author',
		'author_uri' => 'Author URI',
	);

	$data = array(
		'name' => $style,
		'stylepack_uri' => '',
		'template' => '',
		'version' => '',
		'desc' => '',
		'author' => '',
		'author_uri' => '',
	);

	$readme = '';
	foreach(mbt_get_style_folders() as $folder) {
		if(file_exists($folder['dir'].'/'.$style)) {
			if(file_exists($folder['dir'].'/'.$style.'/readme.txt')) {
				$readme = $folder['dir'].'/'.$style.'/readme.txt';
				break;
			}
		}
	}
	if($readme) { $data = get_file_data($readme, $default_headers, 'mbt_style_pack'); }

	return $data;
}

function mbt_get_default_style_pack() {
	return apply_filters('mbt_default_style_pack', 'silver');
}

function mbt_get_style_folders() {
	return apply_filters('mbt_style_folders', array());
}

function mbt_add_default_style_folder($folders) {
	$folders[] = array('dir' => plugin_dir_path(dirname(__FILE__)).'styles', 'url' => plugins_url('styles', dirname(__FILE__)));
	return $folders;
}
add_filter('mbt_style_folders', 'mbt_add_default_style_folder', 100);

function mbt_add_uploaded_style_folder($folders) {
	$upload_dir = wp_upload_dir();
	$folders[] = array('dir' => $upload_dir['basedir'].DIRECTORY_SEPARATOR.'mbt_styles', 'url' => $upload_dir['baseurl'].'/'.'mbt_styles');
	return $folders;
}
add_filter('mbt_style_folders', 'mbt_add_uploaded_style_folder', 100);



/*---------------------------------------------------------*/
/* Analytics                                               */
/*---------------------------------------------------------*/



/*---------------------------------------------------------*/
/* API / Upgrades                                          */
/*---------------------------------------------------------*/

function mbt_verify_api_key() {
	global $wp_version;

	$to_send = array(
		'action' => 'basic_check',
		'version' => MBT_VERSION,
		'api-key' => mbt_get_setting('api_key'),
		'site' => get_bloginfo('url')
	);

	$options = array(
		'timeout' => 3,
		'body' => $to_send,
		'user-agent' => 'WordPress/'.$wp_version
	);

	// added by stormhill 6/14/24 
	$checkmode = mbt_get_setting('key_mode');
	
	$raw_response = wp_remote_post('https://api.stormhillmedia.com/plugins/apikey/check', $options);

	if(isset($checkmode) && $checkmode == 'mybooktable-dev3') {
		mbt_update_setting('api_key_status', 10);
		mbt_update_setting('upgrade_active', 'mybooktable-dev3');
		mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 3.0', 'mybooktable'));
		return;
	} else if(isset($checkmode) &&  $checkmode == 'mybooktable-pro3') {
		mbt_update_setting('api_key_status', 10);
		mbt_update_setting('upgrade_active', 'mybooktable-pro3');
		mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 3.0', 'mybooktable'));
		return;
	}
	// end edits 6/14/24
	if(is_wp_error($raw_response) || 200 != wp_remote_retrieve_response_code($raw_response)) {
		mbt_update_setting('api_key_status', -1);
		mbt_update_setting('upgrade_active', false);
		mbt_update_setting('api_key_message', __('Unable to connect to server!', 'mybooktable'));
		return;
	}

	$response = maybe_unserialize(wp_remote_retrieve_body($raw_response));

	if(!is_array($response) or empty($response['status'])) {
		mbt_update_setting('api_key_status', -2);
		mbt_update_setting('api_key_message', __('Invalid response received from server', 'mybooktable'));
		return;
	}

	$status = $response['status'];

	if($status >= 10) {
		$permissions = array();
		if(!empty($response['permissions']) and is_array($response['permissions'])) {
			$permissions = $response['permissions'];
		}

		if(in_array('mybooktable-dev3', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev3');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 3.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro3', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro3');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 3.0', 'mybooktable'));
		} else if(in_array('mybooktable-dev2', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev2');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 2.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro2', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro2');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 2.0', 'mybooktable'));
		} else if(in_array('mybooktable-dev', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-dev');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Developer 1.0', 'mybooktable'));
		} else if(in_array('mybooktable-pro', $permissions)) {
			mbt_update_setting('api_key_status', $status);
			mbt_update_setting('upgrade_active', 'mybooktable-pro');
			mbt_update_setting('api_key_message', __('Valid for MyBookTable Professional 1.0', 'mybooktable'));
		} else {
			mbt_update_setting('api_key_status', -20);
			mbt_update_setting('api_key_message', __('Permissions error!', 'mybooktable'));
			mbp_update_setting('upgrade_active', false);
		}
	} else if($status == -10) {
		mbt_update_setting('api_key_status', $status);
		mbt_update_setting('api_key_message', __('Key not found', 'mybooktable'));
		mbt_update_setting('upgrade_active', false);
	} else if($status == -11) {
		mbt_update_setting('api_key_status', $status);
		mbt_update_setting('api_key_message', __('Key has been deactivated', 'mybooktable'));
		mbt_update_setting('upgrade_active', false);
	} else {
		mbt_update_setting('api_key_status', -2);
		mbt_update_setting('api_key_message', __('Invalid response received from server', 'mybooktable'));
		mbt_update_setting('upgrade_active', false);
	} 
}

function mbt_init_api_key_check() {
	if(!wp_next_scheduled('mbt_periodic_api_key_check')) { wp_schedule_event(time(), 'weekly', 'mbt_periodic_api_key_check'); }
	add_action('mbt_periodic_api_key_check', 'mbt_verify_api_key');
}
add_action('mbt_init', 'mbt_init_api_key_check');

function mbt_get_upgrade() {
	$upgrade_active = mbt_get_setting('upgrade_active');
	return empty($upgrade_active) ? false : $upgrade_active;
}

function mbt_get_upgrade_version() {
	$upgrade = mbt_get_upgrade();
	if($upgrade == 'mybooktable-dev3' and defined('MBTDEV3_VERSION')) { return MBTDEV3_VERSION; }
	if($upgrade == 'mybooktable-pro3' and defined('MBTPRO3_VERSION')) { return MBTPRO3_VERSION; }
	if($upgrade == 'mybooktable-dev2' and defined('MBTDEV2_VERSION')) { return MBTDEV2_VERSION; }
	if($upgrade == 'mybooktable-pro2' and defined('MBTPRO2_VERSION')) { return MBTPRO2_VERSION; }
	if($upgrade == 'mybooktable-dev' and defined('MBTDEV_VERSION')) { return MBTDEV_VERSION; }
	if($upgrade == 'mybooktable-pro' and defined('MBTPRO_VERSION')) { return MBTPRO_VERSION; }
	return false;
}

function mbt_get_upgrade_plugin_exists($active=true) {
	if(!$active) { return defined('MBTDEV3_VERSION') or defined('MBTPRO3_VERSION') or defined('MBTDEV2_VERSION') or defined('MBTPRO2_VERSION') or defined('MBTDEV_VERSION') or defined('MBTPRO_VERSION'); }
	$upgrade = mbt_get_upgrade();
	if($upgrade == 'mybooktable-dev3') { return defined('MBTDEV3_VERSION'); }
	if($upgrade == 'mybooktable-pro3') { return defined('MBTPRO3_VERSION'); }
	if($upgrade == 'mybooktable-dev2') { return defined('MBTDEV2_VERSION'); }
	if($upgrade == 'mybooktable-pro2') { return defined('MBTPRO2_VERSION'); }
	if($upgrade == 'mybooktable-dev')  { return defined('MBTDEV_VERSION'); }
	if($upgrade == 'mybooktable-pro')  { return defined('MBTPRO_VERSION'); }
	return false;
}

function mbt_get_upgrade_message($require_upgrade=true, $upgrade_text=null, $thankyou_text=null) {
	if(mbt_get_upgrade()) {
		if(mbt_get_upgrade_plugin_exists()) {
			if($require_upgrade) {
				return '<a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/" target="_blank">'.($upgrade_text !== null ? $upgrade_text : __('Upgrade your MyBookTable to enable these advanced features!', 'mybooktable')).'</a>';
			} else {
				return ($thankyou_text !== null ? $thankyou_text : (__('Thank you for purchasing a MyBookTable Upgrade!', 'mybooktable').' <a href="https://www.stormhillmedia.com/book-table/premium-support/" target="_blank">'.__('Get premium support.', 'mybooktable').'</a>'));
			}
		} else {
			$upgradepage_url = 
wp_nonce_url(admin_url('admin.php?page=mbt_dashboard&subpage=mbt_get_upgrade_page'),'dashboard_nonce','nonce_dashboard');
			return '<a href="'.$upgradepage_url.'">'.__('Download your MyBookTable Upgrade plugin to enable your advanced features!', 'mybooktable').'</a>';
		}
	} else {
		if(mbt_get_upgrade_plugin_exists(false)) {
			$api_key = mbt_get_setting('api_key');
			if(empty($api_key)) {
				return '<a href="'.admin_url('admin.php?page=mbt_settings').'">'.__('Insert your License Key to enable your advanced features!', 'mybooktable').'</a>';
			} else {
				return '<a href="'.admin_url('admin.php?page=mbt_settings').'">'.__('Update your License Key to enable your advanced features!', 'mybooktable').'</a>';
			}
		} else {
			return '<a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/" target="_blank">'.($upgrade_text !== null ? $upgrade_text : __('Upgrade your MyBookTable to enable these advanced features!', 'mybooktable')).'</a>';
		}
	}
}

// import image
if(!function_exists('sh_mbt_download_and_insert_attachment_2')){ 
	function sh_mbt_download_and_insert_attachment_2($url,$name) {
		$raw_response = wp_remote_get($url, array('timeout' => 3));
		if(is_wp_error($raw_response) or wp_remote_retrieve_response_code($raw_response) != 200) { return 0; }
		$file_data = wp_remote_retrieve_body($raw_response);
		$file_header = wp_remote_retrieve_headers($raw_response);

		$nonce_url = wp_nonce_url('admin.php', 'mbt_download_and_insert_attachment');
		$output = mbt_get_wp_filesystem($nonce_url);
		if(!empty($output)) { return 0;	}
		global $wp_filesystem, $wpdb;

		$url_parts = wp_parse_url($url);
		$filename = str_replace(' ','-',strtolower($name)).'-book-image.jpg';
		$filename = preg_replace('/[^A-Za-z0-9_.-]/', '', $filename);

		$upload_dir = wp_upload_dir();
		$filepath = $upload_dir['path'].'/'.$filename;
		$fileurl = $upload_dir['url'].'/'.$filename;

		if($wp_filesystem->exists($filepath)) {

			$existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $fileurl));
			if(!empty($existing_id)) { return $existing_id; } else { return 0; }
		}

		if(!$wp_filesystem->put_contents($filepath, $file_data, FS_CHMOD_FILE)) { return 0; }

		$filetype = wp_check_filetype(basename($filepath), null);
		$attachment = array(
			'guid'           => $fileurl,
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment($attachment, $filepath);

		require_once(ABSPATH.'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
		wp_update_attachment_metadata($attach_id, $attach_data);

		return $attach_id;
	}
}

function allow_admin_script_tags( $allowedposttags,$context ){
	if(!is_admin() || $context != 'post'){
		return $allowedposttags;
	}
	$allowedposttags['script'] = array(
		'src' => true,
		'type' => true,
		'charset' => true,
		'class' =>true,
		'async' =>true
	);
	$allowedposttags['input'] = array(
		'id' => true,
		'type' => true,
		'value' => true,
		'name' => true,
		'class' =>true,
		'data-*' =>true,
		'checked' =>true
	);
	$allowedposttags['select'] = array(
		'name' => true,
		'id' => true,
		'class' => true,
		'value' =>true,
		'type' =>true
	);
	$allowedposttags['option'] = array(
		'selected' => true,
	);
	$allowedposttags['textarea'] = array(
		'name' => true,
		'id' => true,
		'class' => true,
		'cols' =>true,
	);
	return $allowedposttags;
}
add_filter('wp_kses_allowed_html','allow_admin_script_tags', 50, 2);

function allow_script_tags( $allowedposttags,$context ){
	if( is_admin() || $context != 'post'){
		return $allowedposttags;
	}
	$allowedposttags['iframe'] = array(
		'src' => true,
		'height' => true,
		'width' => true,
		'class' => true,
		'frameborder' => true,
		'allowfullscreen' => true,
	);
	$allowedposttags['audio'] = array(
		'controls' => true,
	);
	$allowedposttags['source'] = array(
		'src' => true,
		'type' => true,
	);
	$allowedposttags['script'] = array(
		'src' => true,
		'type' => true,
		'charset' => true,
		'class' =>true,
		'async' =>true
	);
	
	return $allowedposttags;
}
add_filter('wp_kses_allowed_html','allow_script_tags', 50, 2);

