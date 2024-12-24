<?php

function mbt_admin_pages_init() {
	if(is_admin()) {
		add_action('admin_menu', 'mbt_add_admin_pages', 9);
		add_action('admin_enqueue_scripts', 'mbt_enqueue_admin_resources');
	}
}
add_action('mbt_init', 'mbt_admin_pages_init');

function mbt_enqueue_admin_resources() {
	wp_enqueue_style('mbt-admin-global-css', plugins_url('css/admin-global-style.css', dirname(__FILE__)), array(), MBT_VERSION);
	wp_enqueue_script('mbt-admin-global-js', plugins_url('js/admin-global.js', dirname(__FILE__)), array('jquery'), MBT_VERSION);

	if(!mbt_is_mbt_admin_page()) { return; }

	wp_enqueue_style('mbt-admin-css', plugins_url('css/admin-style.css', dirname(__FILE__)), array(), MBT_VERSION);
	wp_enqueue_style('mbt-jquery-ui', plugins_url('css/jquery-ui.css', dirname(__FILE__)), array(), MBT_VERSION);

	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-position');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-accordion');

	wp_enqueue_script('mbt-admin-pages', plugins_url('js/admin.js', dirname(__FILE__)), array('jquery', 'underscore', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-slider', 'jquery-ui-accordion'), MBT_VERSION);
	wp_localize_script('mbt-admin-pages', 'mbt_admin_pages_i18n', array(
		'select' => __('Select', 'mybooktable'),
		'post_title' => __('Post Title', 'mybooktable'),
		'post_date' => __('Post Date', 'mybooktable'),
		'author' => __('Author', 'mybooktable'),
		'genre' => __('Genre', 'mybooktable'),
		'tag' => __('Tag', 'mybooktable'),
		'series' => __('Series', 'mybooktable'),
		'series_order' => __('Series Order', 'mybooktable'),
		'ascending' => __('Ascending', 'mybooktable'),
		'descending' => __('Descending', 'mybooktable'),
		'import_interrupted' => __('Import Interrupted', 'mybooktable'),
		'import_complete' => __('Import Complete', 'mybooktable'),
		'successfully_imported' => __('Successfully imported', 'mybooktable'),
		'swich_to_mode' => __('Switch to mode'),
		'set_author_priority' => __('Set the priority (order) of the authors', 'mybooktable'),
		'enter_main_author_bio' => __('Edit the main author bio', 'mybooktable'),
	));
	if(function_exists('wp_enqueue_media')) { wp_enqueue_media(); }
}

function mbt_add_admin_pages() {
	add_menu_page(__('MyBookTable', 'mybooktable'), __('MyBookTable', 'mybooktable'), 'edit_posts', 'mbt_dashboard', 'mbt_render_dashboard', 'dashicons-book-alt', '10.7');
	add_submenu_page('mbt_dashboard', __('Books', 'mybooktable'), __('Books', 'mybooktable'), 'edit_posts', 'edit.php?post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Add Book', 'mybooktable'), __('Add Book', 'mybooktable'), 'edit_posts', 'post-new.php?post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Authors', 'mybooktable'), __('Authors', 'mybooktable'), 'manage_categories', 'edit-tags.php?taxonomy=mbt_author&amp;post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Genres', 'mybooktable'), __('Genres', 'mybooktable'), 'manage_categories', 'edit-tags.php?taxonomy=mbt_genre&amp;post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Series', 'mybooktable'), __('Series', 'mybooktable'), 'manage_categories', 'edit-tags.php?taxonomy=mbt_series&amp;post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Tags', 'mybooktable'), __('Tags', 'mybooktable'), 'manage_categories', 'edit-tags.php?taxonomy=mbt_tag&amp;post_type=mbt_book');
	add_submenu_page('mbt_dashboard', __('Import Books', 'mybooktable'), __('Import Books', 'mybooktable'), 'edit_posts', 'mbt_import', 'mbt_render_import_page');
	add_submenu_page('mbt_dashboard', __('MyBookTable Settings', 'mybooktable'), __('Settings', 'mybooktable'), 'manage_options', 'mbt_settings', 'mbt_render_settings_page');
	add_submenu_page('mbt_dashboard', __('MyBookTable Help', 'mybooktable'), __('Help', 'mybooktable'), 'edit_posts', 'mbt_help', 'mbt_render_help_page');

	if(mbt_get_upgrade() === false) {
		$page_hook = add_submenu_page('mbt_dashboard', __('Upgrade', 'mybooktable'), __('Upgrade MyBookTable', 'mybooktable'), 'edit_posts', 'mbt_upgrade_link', ' ');
		add_action('load-'.$page_hook, 'mbt_upgrade_link_redirect');
	}

	remove_menu_page("edit.php?post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "edit.php?post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "post-new.php?post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "edit-tags.php?taxonomy=mbt_author&amp;post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "edit-tags.php?taxonomy=mbt_genre&amp;post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "edit-tags.php?taxonomy=mbt_series&amp;post_type=mbt_book");
	remove_submenu_page("edit.php?post_type=mbt_book", "edit-tags.php?taxonomy=mbt_tag&amp;post_type=mbt_book");
}

function mbt_upgrade_link_redirect() {
	wp_redirect('https://www.stormhillmedia.com/all-products/mybooktable/upgrades/', 301);
	exit();
}

/*---------------------------------------------------------*/
/* Settings Page                                           */
/*---------------------------------------------------------*/

function mbt_settings_page_init() {
	add_action('wp_ajax_mbt_api_key_refresh', 'mbt_api_key_refresh_ajax');
	add_action('wp_ajax_mbt_style_pack_preview', 'mbt_style_pack_preview_ajax');
	add_action('wp_ajax_mbt_button_size_preview', 'mbt_button_size_preview_ajax');
	add_action('wp_ajax_mbt_check_reviews', 'mbt_check_reviews_ajax');
	add_action('wp_ajax_mbt_google_api_key_refresh', 'mbt_google_api_key_refresh_ajax');

	//needs to happen before setup.php admin_init in order to properly update admin notices
	add_action('admin_init', 'mbt_save_settings_page');
}
add_action('mbt_init', 'mbt_settings_page_init');

function mbt_save_settings_page() {
	if( (isset($_REQUEST['page']) && $_REQUEST['page'] === 'mbt_settings') && ((isset($_REQUEST['data-settings-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-settings-nonce']),'mbt-settings-nonce'))) && isset($_REQUEST['save_settings'])) {
		
		do_action('mbt_settings_save');

		if(isset($_REQUEST['mbt_api_key']) && $_REQUEST['mbt_api_key'] !== mbt_get_setting('api_key') && $_REQUEST['mbt_api_key'] !== mbt_hide_api_key(mbt_get_setting('api_key'))) {
			mbt_update_setting('api_key', sanitize_text_field(wp_unslash($_REQUEST['mbt_api_key'])));
			mbt_verify_api_key();
		}
		if(isset($_REQUEST['mbt_product_name'])){
			mbt_update_setting('product_name', sanitize_text_field(wp_unslash($_REQUEST['mbt_product_name'])));
			mbt_update_setting('product_slug', sanitize_title(wp_unslash($_REQUEST['mbt_product_name'])));
		}
		if(isset($_REQUEST['mbt_booktable_page'])){
			mbt_update_setting('booktable_page', intval($_REQUEST['mbt_booktable_page']));
		}
		mbt_update_setting('compatibility_mode', isset($_REQUEST['mbt_compatibility_mode']));
		mbt_update_setting('enable_socialmedia_single_book',isset($_REQUEST['mbt_enable_socialmedia_single_book']));
	
		mbt_update_setting('enable_socialmedia_book_excerpt', isset($_REQUEST['mbt_enable_socialmedia_book_excerpt']));

		mbt_update_setting('enable_seo', isset($_REQUEST['mbt_enable_seo']));
		
		if(isset($_REQUEST['mbt_style_pack'])){
			mbt_update_setting('style_pack', sanitize_text_field(wp_unslash($_REQUEST['mbt_style_pack'])));
		}
		if(isset($_REQUEST['mbt_image_size'])){
			mbt_update_setting('image_size', sanitize_text_field(wp_unslash($_REQUEST['mbt_image_size'])));
		}
		mbt_update_setting('reviews_type', isset($_REQUEST['mbt_reviews_type']) ? sanitize_text_field(wp_unslash($_REQUEST['mbt_reviews_type'])) : 'none');

		if(isset($_REQUEST['mbt_buybutton_shadowbox'])){
			mbt_update_setting('buybutton_shadowbox', sanitize_text_field(wp_unslash($_REQUEST['mbt_buybutton_shadowbox'])));
		}

		mbt_update_setting('enable_breadcrumbs', isset($_REQUEST['mbt_enable_breadcrumbs']));
		mbt_update_setting('show_series', isset($_REQUEST['mbt_show_series']));
		mbt_update_setting('show_find_bookstore', isset($_REQUEST['mbt_show_find_bookstore']));
		mbt_update_setting('show_find_bookstore_buybuttons_shadowbox', isset($_REQUEST['mbt_show_find_bookstore_buybuttons_shadowbox']));
		mbt_update_setting('show_about_author', isset($_REQUEST['mbt_show_about_author']));
		mbt_update_setting('hide_domc_notice', isset($_REQUEST['mbt_hide_domc_notice']));
		if(isset($_REQUEST['mbt_domc_notice_text'])){
			mbt_update_setting('domc_notice_text', wp_kses_post(wp_unslash($_REQUEST['mbt_domc_notice_text'])));
		}
		if(isset($_REQUEST['mbt_posts_per_page'])){
			mbt_update_setting('posts_per_page', intval(sanitize_text_field(wp_unslash($_REQUEST['mbt_posts_per_page']))));
		}
		if(isset($_REQUEST['mbt_book_button_size'])){
			mbt_update_setting('book_button_size', sanitize_text_field(wp_unslash($_REQUEST['mbt_book_button_size'])));
		}
		if(isset($_REQUEST['mbt_listing_button_size'])){
			mbt_update_setting('listing_button_size', sanitize_text_field(wp_unslash($_REQUEST['mbt_listing_button_size'])));
		}
		if(isset($_REQUEST['mbt_widget_button_size'])){
			mbt_update_setting('widget_button_size', sanitize_text_field(wp_unslash($_REQUEST['mbt_widget_button_size'])));
		}
		if(isset($_REQUEST['mbt_google_api_key'])){
			mbt_update_setting('google_api_key', sanitize_text_field(wp_unslash($_REQUEST['mbt_google_api_key'])));
		}
		$settings_updated = true;
	} else if(isset($_REQUEST['page']) and $_REQUEST['page'] === 'mbt_settings' and isset($_REQUEST['save_default_affiliate_settings'])) {
		if(isset($_REQUEST['mbt_enable_default_affiliates']) and !empty($_REQUEST['mbt_enable_default_affiliates'])) {
			mbt_update_setting('enable_default_affiliates', $_REQUEST['mbt_enable_default_affiliates'] === 'true');
		}
	}
}

function mbt_hide_api_key($key) {
	if (strlen($key) > 0){
		return substr($key, 0, 4) . str_repeat("*", max(0, strlen($key)-4));
	}
}

function mbt_api_key_refresh_ajax() {
	if(!current_user_can('manage_options')) { die(); }
	if(isset($_REQUEST['mbt_nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']),'mbt-ajax-nonce')){
		if(isset($_REQUEST['data']) && $_REQUEST['data'] !== mbt_hide_api_key(mbt_get_setting('api_key'))) {
			mbt_update_setting('api_key', sanitize_text_field(wp_unslash($_REQUEST['data'])));
		}
		mbt_verify_api_key();
		echo(wp_kses_post(mbt_api_key_feedback()));
		die();
	} else {
		die( esc_attr_e('Nonce Failed', 'mybooktable'));
	}
}

function mbt_api_key_feedback() {
	$output = '';
	if(mbt_get_setting('api_key') and mbt_get_setting('api_key_status') != 0) {
		if(mbt_get_setting('api_key_status') > 0) {
			$output .= '<span class="mbt_admin_message_success">'.__('Valid License Key', 'mybooktable').': '.mbt_get_setting('api_key_message').'</span>';
			$upgrade_message = mbt_get_upgrade_message(false, '', '');
			if(!empty($upgrade_message)) { $output .= '<br>'.$upgrade_message; }
		} else {
			$output .= '<span class="mbt_admin_message_failure">'.__('Invalid License Key', 'mybooktable').': '.mbt_get_setting('api_key_message').'</span>';
		}
	}
	return $output;
}

function mbt_google_api_key_refresh_ajax() {
	if(!isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']), 'mbt-ajax-nonce')) { die(); }
	if(!empty($_REQUEST['data'])) {
		echo('<span class="mbt_admin_message_success">'.esc_attr_e('Valid API Key', 'mybooktable').'</span>');
	}
	die();
}

function mbt_style_pack_preview_ajax() {
	if(!isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']), 'mbt-ajax-nonce')) { die(); }
	if(isset($_REQUEST['data'])){
		echo(wp_kses_post('<img src="'.mbt_style_url('bnn_button.png', sanitize_text_field(wp_unslash($_REQUEST['data']))).'" />'));
	}
	die();
}

function mbt_button_size_preview_ajax() {
	if(!isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']), 'mbt-ajax-nonce')) { die(); }
	if(isset($_REQUEST['data'])){
		mbt_button_size_feedback(sanitize_text_field(wp_unslash($_REQUEST['data'])));
	}
	die();
}

function mbt_button_size_feedback($size) {
	$id = 'mbt_book_'.time().'_'.wp_rand();
	echo(wp_kses_post('<img id="'.$id.'" src="'.mbt_style_url('bnn_button.png', mbt_get_default_style_pack()).'">'));
	echo('<style type="text/css">');
	if($size === 'small') { echo('#'.esc_attr($id).' { width: 144px; height: 25px; }'); }
	else if($size === 'medium') { echo('#'.esc_attr($id).' { width: 172px; height: 30px; }'); }
	else { echo('#'.esc_attr($id).' { width: 201px; height: 35px; }'); }
	echo('</style>');
}

function mbt_check_reviews_ajax() {
	if(!isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']), 'mbt-ajax-nonce')) { die(); }
	$output = '';
	$reviews_types = mbt_get_reviews_types();
	if(isset($_REQUEST['reviews_type'])){
		$reviews_type = sanitize_text_field(wp_unslash($_REQUEST['reviews_type']));
	} else {
		$reviews_type = '';
	}
	if(!empty($reviews_types[$reviews_type]['book-check'])) {
		$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
		$books_results = array();
		foreach ($books_query->posts as $book) {
			$result = call_user_func_array($reviews_types[$reviews_type]['book-check'], array($book->ID));
			$books_results[] = array('id' => $book->ID, 'title' => (strlen($book->post_title) > 30 ? substr($book->post_title, 0, 30) : $book->post_title), 'result' => $result);
		}

		$sort_books_results = function($a, $b) {
			$a_val = (strpos($a['result'], 'mbt_admin_message_failure') !== false) ? 0 : ((strpos($a['result'], 'mbt_admin_message_warning') !== false) ? 1 : 2);
			$b_val = (strpos($b['result'], 'mbt_admin_message_failure') !== false) ? 0 : ((strpos($b['result'], 'mbt_admin_message_warning') !== false) ? 1 : 2);
			return $a_val == $b_val ? 0 : ($a_val > $b_val ? 1 : -1);
		};
		usort($books_results, $sort_books_results);

		$output .= '<ul id="mbt-check-reviews-results-list">';
		foreach($books_results as $result) {
			$output .= '<li><a href="'.get_permalink($result['id']).'" target="_blank">'.$result['title'].'</a> - '.$result['result'].'</li>';
		}
		$output .= '</ul>';
	} else {
		$output .= '<div class="mbt-check-reviews-noresults">This reviews system does not support reviews checking.</div>';
	}

	echo(wp_kses_post($output)); exit();
}


function mbt_render_settings_page() {
	$mbt_settings_nonce = wp_create_nonce('mbt-settings-nonce','data-settings-nonce');
	$mbt_nonce = wp_create_nonce('mbt-ajax-nonce');
	//if(!empty($_GET['mbt_setup_default_affiliates'])) { return mbt_render_setup_default_affiliates_page(); }
?>
	<div class="wrap mbt_settings">
		<h2><?php esc_attr_e('MyBookTable Settings', 'mybooktable'); ?></h2>
		<?php if(!empty($settings_updated)) { ?>
			<div id="setting-error-settings_updated" class="updated settings-error"><p><strong><?php esc_attr_e('Settings saved', 'mybooktable'); ?>.</strong></p></div>
		<?php } ?>

		<form id="mbt_settings_form" method="post" action="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings'))); ?>">
			
			<input type="hidden" id="data-settings-nonce" name="data-settings-nonce" value="<?php echo(esc_attr($mbt_settings_nonce)); ?>">
			<!-- <input type="hidden" name="mbt_current_tab" id="mbt_current_tab" value="<?php //echo(wp_kses_post(isset($_REQUEST['mbt_current_tab'])?$_REQUEST['mbt_current_tab']:1)); ?>"> -->

			<div id="mbt-settings-tabs">
				<ul>
					<li><a href="#mbt-tab-1"><?php esc_attr_e('Setup', 'mybooktable'); ?></a></li>
					<li><a href="#mbt-tab-2"><?php esc_attr_e('Style', 'mybooktable'); ?></a></li>
					<li><a href="#mbt-tab-3"><?php esc_attr_e('Promote', 'mybooktable'); ?></a></li>
					<li><a href="#mbt-tab-4"><?php esc_attr_e('Earn', 'mybooktable'); ?></a></li>
					<li><a href="#mbt-tab-5"><?php esc_attr_e('Integrate', 'mybooktable'); ?></a></li>
					<li><a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_help'))); ?>" id="mbt-help-link" ><?php esc_attr_e('Troubleshoot', 'mybooktable'); ?></a></li>
					<?php do_action('additional_mbt_tabs');?>
				</ul>
				<div class="mbt-tab" id="mbt-tab-1">
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_attr_e('MyBookTable License Key', 'mybooktable'); ?></th>
								<td>
									<div class="mbt_feedback_above mbt_feedback"><?php echo(wp_kses_post(mbt_api_key_feedback())); ?></div>
									<div style="clear:both"></div>
									<input type="text" name="mbt_api_key" id="mbt_api_key" value="<?php echo(esc_attr(mbt_hide_api_key(mbt_get_setting('api_key')))); ?>" size="60" class="regular-text" />
									<div class="mbt_feedback_refresh" data-refresh-action="mbt_api_key_refresh" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_api_key"></div>
									<p class="description"><?php
										/* translators: %s: link to gumroad where plugin was purchased */
										echo sprintf(esc_html__('If you have purchased an License Key for MyBookTable, enter it here to activate your enhanced features. You can find it in your %1$s. If you would like to purchase an License key visit %2$s.','mybooktable'),'<a href="https://gumroad.com/library/" target="_blank">Gumroad Library here</a>','<a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/">MyBookTable Upgrades</a>');?>
									</p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('Book Table Page', 'mybooktable'); ?></th>
								<td>
									<select name="mbt_booktable_page" id="mbt_booktable_page">
										<option value="0" <?php selected(mbt_get_setting('booktable_page'), 0); ?> ><?php esc_attr_e('-- Choose One --', 'mybooktable');?></option>
										<?php foreach(get_pages() as $page) { ?>
											<option value="<?php echo(esc_attr($page->ID)); ?>" <?php selected(mbt_get_setting('booktable_page'), $page->ID); ?> ><?php echo(esc_attr($page->post_title)); ?></option>
										<?php } ?>
									</select>
									<?php if(mbt_get_setting('booktable_page') == 0 or !get_page(mbt_get_setting('booktable_page'))) { 
                                    // nonce the url
                                    $add_bt_page_url = wp_nonce_url(admin_url('admin.php?page=mbt_settings&mbt_add_booktable_page=1'),'add_page_nonce','nonce_add_page');
                                    ?>
										<a href="<?php echo(esc_url($add_bt_page_url)); ?>" class="button button-primary"><?php esc_attr_e('Click here to create a Book Table page', 'mybooktable'); ?></a>
									<?php } else { 
                                    // nonce the url
                                    $remove_bt_page_url = wp_nonce_url(admin_url('admin.php?page=mbt_settings&mbt_remove_booktable_page=1'),'remove_page_nonce','nonce_remove_page'); ?>
										<a href="<?php echo(esc_url($remove_bt_page_url)); ?>" class="button button-primary"><?php esc_attr_e('Remove Book Table page', 'mybooktable'); ?></a>
									<?php } ?>
									<p class="description"><?php esc_attr_e('The Book Table page is the main landing page for your books.', 'mybooktable'); ?></p>
								</td>
							</tr>
							<?php if(!mbt_get_setting('installed_examples')) { ?>
								<tr>
									<th><?php esc_attr_e('Example Books', 'mybooktable'); ?></th>
									<td>
										<a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings&mbt_install_examples=1'))); ?>" class="button button-primary"><?php esc_attr_e('Click here to create example books', 'mybooktable'); ?></a>
										<p class="description"><?php esc_attr_e('These examples will help you learn how to set up Genres, Series, Authors, and Books of your own.', 'mybooktable'); ?></p>
									</td>
								</tr>
							<?php } ?>
							<tr>
								<th><?php esc_attr_e('Compatability Mode', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_compatibility_mode" id="mbt_compatibility_mode" <?php checked(mbt_get_setting('compatibility_mode'), true); ?> >
									<p class="description"><?php esc_attr_e('Checked = More Compatible Out of the Box. Unchecked = More Developer Control.', 'mybooktable'); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('MyBookTable Product Name', 'mybooktable'); ?></th>
								<td>
									<input type="text" name="mbt_product_name" id="mbt_product_name" value="<?php echo(esc_attr(mbt_get_setting('product_name'))); ?>" size="60" class="regular-text" />
									<p class="description"><?php esc_attr_e('You can use this to change the "books" slug used in the book page urls if you are selling something other than books, such as "DVDs", "Movies", or simply "Products".', 'mybooktable'); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<?php do_action("mbt_setup_settings_render"); ?>
					<input type="submit" name="save_settings" class="button button-primary" value="<?php echo sprintf(esc_html__('Save Changes', 'mybooktable')); ?>">
				</div>
				<div class="mbt-tab" id="mbt-tab-2">
					<div class="mbt-section">
						<div class="mbt-section-header">Buy Button Styles</div>
						<div class="mbt-section-content">
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php esc_attr_e('Style Pack', 'mybooktable'); ?></th>
										<td colspan="3">
											<?php $pack_upload_output = mbt_do_style_pack_upload(); ?>
											<?php $current_style = mbt_get_setting('style_pack'); ?>
											<div id="mbt_style_pack_preview" class="mbt_feedback"><img src="<?php echo(esc_url(mbt_style_url('bnn_button.png', $current_style))); ?>"></div>
											<select name="mbt_style_pack" id="mbt_style_pack" class="mbt_feedback_refresh" data-refresh-action="mbt_style_pack_preview" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_style_pack">
												<?php foreach(mbt_get_style_packs() as $style) { ?>
													<?php $meta = mbt_get_style_pack_meta($style); ?>
													<option value="<?php echo(esc_attr($style)); ?>" <?php echo($current_style == $style ? ' selected="selected"' : ''); ?> ><?php echo(esc_attr($meta['name'])); ?></option>
												<?php } ?>
											</select>
											<input type="hidden" id="mbt_style_pack_id" name="mbt_style_pack_id" onchange="jQuery('#mbt_current_tab').val(2); jQuery('#mbt_settings_form').submit();">
											<p class="description"><?php esc_attr_e('Choose the style pack you would like for your buy buttons.', 'mybooktable'); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_attr_e('Buy Button Sizes', 'mybooktable'); ?></th>
										<?php $button_sizes = array('small' =>__('Small', 'mybooktable'), 'medium' => __('Medium', 'mybooktable'), 'large' => __('Large', 'mybooktable')); ?>
										<td>
											<h4><label for="mbt_book_button_size"><?php esc_attr_e('Book Pages', 'mybooktable'); ?></label></h4>
											<?php $book_button_size = mbt_get_setting('book_button_size'); ?>
											<?php if(empty($book_button_size)) { $book_button_size = 'medium'; } ?>
											<div id="mbt_book_button_size_preview" class="mbt_feedback"><?php mbt_button_size_feedback($book_button_size); ?></div>
											<?php foreach($button_sizes as $size => $size_name) { ?>
												<input type="radio" name="mbt_book_button_size" id="mbt_book_button_size_<?php echo(esc_attr($size)); ?>" value="<?php echo(esc_attr($size)); ?>" <?php checked($book_button_size, $size); ?>
												class="mbt_feedback_refresh" data-refresh-action="mbt_button_size_preview" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_book_button_size_<?php echo(esc_attr($size)); ?>"><?php echo(esc_attr($size_name)); ?><br>
											<?php } ?>
											<p class="description"><?php esc_attr_e('Select the size of the buy buttons on book pages.', 'mybooktable'); ?></p>
										</td>
										<td>
											<h4><label for="mbt_book_button_size"><?php esc_attr_e('Book Listings', 'mybooktable'); ?></label></h4>
											<?php $listing_button_size = mbt_get_setting('listing_button_size'); ?>
											<?php if(empty($listing_button_size)) { $listing_button_size = 'medium'; } ?>
											<div id="mbt_listing_button_size_preview" class="mbt_feedback"><?php mbt_button_size_feedback($listing_button_size); ?></div>
											<?php foreach($button_sizes as $size => $size_name) { ?>
												<input type="radio" name="mbt_listing_button_size" id="mbt_listing_button_size_<?php echo(esc_attr($size)); ?>" value="<?php echo(esc_attr($size)); ?>" <?php checked($listing_button_size, $size); ?>
												class="mbt_feedback_refresh" data-refresh-action="mbt_button_size_preview" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_listing_button_size_<?php echo(esc_attr($size)); ?>"><?php echo(esc_attr($size_name)); ?><br>
											<?php } ?>
											<p class="description"><?php esc_attr_e('Select the size of the buy buttons on book listings.', 'mybooktable'); ?></p>
										</td>
										<td>
											<h4><label for="mbt_book_button_size"><?php esc_attr_e('Widgets', 'mybooktable'); ?></label></h4>
											<?php $widget_button_size = mbt_get_setting('widget_button_size'); ?>
											<?php if(empty($widget_button_size)) { $widget_button_size = 'medium'; } ?>
											<div id="mbt_widget_button_size_preview" class="mbt_feedback"><?php mbt_button_size_feedback($widget_button_size); ?></div>
											<?php foreach($button_sizes as $size => $size_name) { ?>
												<input type="radio" name="mbt_widget_button_size" id="mbt_widget_button_size_<?php echo(esc_attr($size)); ?>" value="<?php echo(esc_attr($size)); ?>" <?php checked($widget_button_size, $size); ?>
												class="mbt_feedback_refresh" data-refresh-action="mbt_button_size_preview" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_widget_button_size_<?php echo(esc_attr($size)); ?>"><?php echo(esc_attr($size_name)); ?><br>
											<?php } ?>
											<p class="description"><?php esc_attr_e('Select the size of the buy buttons on widgets.', 'mybooktable'); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_attr_e('Buy Button Shadow Box', 'mybooktable'); ?></th>
										<td colspan="3">
											<label class="mbt_buybutton_shadowbox"><input type="radio" name="mbt_buybutton_shadowbox" value="none" <?php checked(mbt_get_setting('buybutton_shadowbox'), 'none'); ?> ><?php esc_attr_e('Nowhere', 'mybooktable'); ?></label>
											<label class="mbt_buybutton_shadowbox"><input type="radio" name="mbt_buybutton_shadowbox" value="listings" <?php checked(mbt_get_setting('buybutton_shadowbox'), 'listings'); ?> ><?php esc_attr_e('Book Listings Only', 'mybooktable'); ?></label>
											<label class="mbt_buybutton_shadowbox"><input type="radio" name="mbt_buybutton_shadowbox" value="all" <?php checked(mbt_get_setting('buybutton_shadowbox'), 'all'); ?> ><?php esc_attr_e('Everywhere', 'mybooktable'); ?></label>
											<p class="description"><?php esc_attr_e('Replace store buy buttons with a single "Buy Now" button that loads a shadow box with all the buttons within it.', 'mybooktable'); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_attr_e('Upload New Style Pack', 'mybooktable'); ?></th>
										<td colspan="3">
											<?php 
											if(isset($pack_upload_output) && !empty($pack_upload_output)){
												echo(wp_kses_post($pack_upload_output));
											}
											?>
											<input id="mbt_upload_style_pack_button" class="button mbt_upload_button" data-upload-target="mbt_style_pack_id" data-upload-property="id" data-upload-title="<?php esc_attr_e('Style Pack', 'mybooktable'); ?>" type="button" value="<?php esc_attr_e('Upload', 'mybooktable'); ?>">
											<p class="description"><?php 
												/* translators: %s: link to tutorial */
												echo sprintf(esc_html__('If you would like to make your own style pack you can learn how from our %s.', 'mybooktable'), 
												'<a href="https://gitlab.com/authormedia/mybooktable/wikis/Style-Pack-System" target="_blank">developer documentation</a>'); ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
							<?php do_action("mbt_buybutton_style_settings_render"); ?>
						</div>
					</div>
					<div class="mbt-section">
						<div class="mbt-section-header">General Styles</div>
						<div class="mbt-section-content">
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php esc_attr_e('Book Image Size', 'mybooktable'); ?></th>
										<td>
											<?php $image_sizes = array('small' =>__('Small', 'mybooktable'), 'medium' => __('Medium', 'mybooktable'), 'large' => __('Large', 'mybooktable')); ?>
											<?php $image_size = mbt_get_setting('image_size'); ?>
											<?php if(empty($image_size)) { $image_size = 'medium'; } ?>
											<?php foreach($image_sizes as $size => $size_name) { ?>
												<input type="radio" name="mbt_image_size" value="<?php echo(esc_attr($size)); ?>" <?php checked($image_size, $size); ?> ><?php echo(esc_attr($size_name)); ?><br>
											<?php } ?>
											<p class="description"><?php esc_attr_e('Book Images in MyBookTable respond to mobile devices regardless of which size you select.', 'mybooktable'); ?></p>
										</td>
									</tr>
									<tr>
										<th><?php esc_attr_e('Breadcrumbs', 'mybooktable'); ?></th>
										<td>
											<input type="checkbox" name="mbt_enable_breadcrumbs" id="mbt_enable_breadcrumbs" <?php checked(mbt_get_setting('enable_breadcrumbs'), true); ?> >
											<label for="mbt_enable_breadcrumbs"><?php esc_attr_e('Enable', 'mybooktable'); ?></label>
											<p class="description"><?php esc_attr_e('Breadcrumbs make your website easier to navigate for both humans and search engines. Uncheck this box if MyBookTable\'s breadcrumb system is conflicting with your theme.', 'mybooktable'); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
							<?php do_action("mbt_general_style_settings_render"); ?>
						</div>
					</div>
					<div class="mbt-section">
						<div class="mbt-section-header">Book Listing Styles</div>
						<div class="mbt-section-content">
							<table class="form-table">
								<tbody>
									<tr>
										<th><?php esc_attr_e('Number of Books per Page', 'mybooktable'); ?></th>
										<td>
											<input name="mbt_posts_per_page" type="text" id="mbt_posts_per_page" value="<?php echo(mbt_get_setting('posts_per_page') ? esc_attr(mbt_get_setting('posts_per_page')) : esc_attr(get_option('posts_per_page'))); ?>" class="regular-text">
											<p class="description"><?php esc_attr_e('Choose the number of books to show per page on the book listings.', 'mybooktable'); ?></p>
										</td>
									</tr>
								</tbody>
							</table>
							<?php do_action("mbt_listings_style_settings_render"); ?>
						</div>
					</div>
					<input type="submit" name="save_settings" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'mybooktable'); ?>">
				</div>
				<div class="mbt-tab" id="mbt-tab-3">
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_attr_e('Search Engine Optimization', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_enable_seo" id="mbt_enable_seo" <?php echo(mbt_get_setting('enable_seo') ? ' checked="checked"' : ''); ?> >
									<label for="mbt_enable_seo"><?php esc_attr_e('Use MyBookTable\'s built-in SEO features', 'mybooktable'); ?></label>
									<p class="description"><?php esc_attr_e('Let MyBookTable\'s built in search engine optimization do the work for you.', 'mybooktable'); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('Book Reviews', 'mybooktable'); ?></th>
								<td>
									<?php
										$reviews_types = mbt_get_reviews_types();
										$current_reviews = mbt_get_setting('reviews_type');
										if(empty($current_reviews) or empty($reviews_types[$current_reviews])) { $current_reviews = 'none'; }
										echo('<input type="radio" name="mbt_reviews_type" id="mbt_reviews_type_none" value="none" '.checked($current_reviews, 'none', false).'><label for="mbt_reviews_type_none">None</label><br>');
										foreach($reviews_types as $slug => $reviews_data) {
											if(!empty($reviews_data['disabled'])) {
												echo('<input type="radio" name="mbt_reviews_type" id="mbt_reviews_type_'.esc_attr($slug).'" value="'.esc_attr($slug).'" '.checked($current_reviews, $slug, false).' disabled="disabled">');
												echo(wp_kses_post('<label for="mbt_reviews_type_'.$slug.'" class="mbt_reviews_type_disabled">'.$reviews_data['name'].' ('.$reviews_data['disabled'].')</label><br>'));
											} else {
												echo(wp_kses_post('<input type="radio" name="mbt_reviews_type" id="mbt_reviews_type_'.$slug.'" value="'.$slug.'" '.checked($current_reviews, $slug, false).'>'));
												echo(wp_kses_post('<label for="mbt_reviews_type_'.$slug.'">'.$reviews_data['name'].'</label><br>'));
											}
										}
									?>
									<p class="description"><?php esc_attr_e('Select the reviews provider that will be displayed under each book with a valid ISBN.', 'mybooktable'); ?></p>
									<div class="mbt-check-reviews">
										<div class="mbt-check-reviews-checking">Checking&hellip;<div class="mbt-check-reviews-spinner"></div></div>
										<div class="mbt-check-reviews-results"></div>
										<div class="mbt-check-reviews-begin">
											<div class="mbt-check-reviews-button button" data-nonce="<?php echo(esc_attr($mbt_nonce)); ?>"><?php esc_attr_e('Check Reviews', 'mybooktable'); ?></div>
											<span class="description"> - <?php esc_attr_e('Use this tool to check if your books will be able to display reviews.', 'mybooktable'); ?></span>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('Social Media', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_enable_socialmedia_single_book" id="mbt_enable_socialmedia_single_book" <?php checked(mbt_get_setting('enable_socialmedia_single_book'), true); ?> >
									<label for="mbt_enable_socialmedia_single_book"><?php esc_attr_e('Show on Book Pages', 'mybooktable'); ?></label><br>
									<input type="checkbox" name="mbt_enable_socialmedia_book_excerpt" id="mbt_enable_socialmedia_book_excerpt" <?php checked(mbt_get_setting('enable_socialmedia_book_excerpt'), true); ?> >
									<label for="mbt_enable_socialmedia_book_excerpt"><?php esc_attr_e('Show on Book Listings', 'mybooktable'); ?></label>
									<p class="description"><?php esc_attr_e('Check to enable MyBookTable\'s social media buttons.', 'mybooktable'); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('"Find a Local Bookstore" Form', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_show_find_bookstore" id="mbt_show_find_bookstore" <?php checked(mbt_get_setting('show_find_bookstore'), true); ?> >
									<label for="mbt_show_find_bookstore"><?php esc_attr_e('Show on Book Pages', 'mybooktable'); ?></label><br>
									<input type="checkbox" name="mbt_show_find_bookstore_buybuttons_shadowbox" id="mbt_show_find_bookstore_buybuttons_shadowbox" <?php checked(mbt_get_setting('show_find_bookstore_buybuttons_shadowbox'), true); ?> >
									<label for="mbt_show_find_bookstore_buybuttons_shadowbox"><?php esc_attr_e('Show in Buy Buttons Shadow Box', 'mybooktable'); ?></label>
									<p class="description">
										<?php esc_attr_e('If checked, show a form that helps your readers find places to buy your book will display under each book.', 'mybooktable'); ?>
										<?php sprintf('<a href="%s">(', admin_url('admin.php?page=mbt_settings&mbt_current_tab=5')).__('You must enter your Google Maps API Key for this feature to work', 'mybooktable').')</a>'; ?>
									</p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('Cross Promote Books in a Series', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_show_series" id="mbt_show_series" <?php checked(mbt_get_setting('show_series'), true); ?> >
									<label for="mbt_show_series"><?php esc_attr_e('Show books', 'mybooktable'); ?></label>
									<p class="description"><?php esc_attr_e('If checked, the other books in the same series will display under the book on that book\'s page.', 'mybooktable'); ?></p>
								</td>
							</tr>
							<tr>
								<th><?php esc_attr_e('"About the Author" Section', 'mybooktable'); ?></th>
								<td>
									<input type="checkbox" name="mbt_show_about_author" id="mbt_show_about_author" <?php checked(mbt_get_setting('show_about_author'), true); ?> >
									<label for="mbt_show_about_author"><?php esc_attr_e('Show on Book Pages', 'mybooktable'); ?></label><br>
									<p class="description"><?php esc_attr_e('If checked, show a section on the book page that displays information about the book author.', 'mybooktable'); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<?php do_action("mbt_promote_settings_render"); ?>
					<input type="submit" name="save_settings" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'mybooktable'); ?>">
				</div>
				<div class="mbt-tab" id="mbt-tab-4">
					<?php do_action("mbt_affiliate_settings_render", $mbt_nonce); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th><?php esc_attr_e('Disclosure of Material Connection Disclaimer', 'mybooktable'); ?></th>
								<td>
									<textarea rows="5" cols="60" name="mbt_domc_notice_text" id="mbt_domc_notice_text"><?php echo(wp_kses_post(mbt_get_setting('domc_notice_text'))); ?></textarea>
									<p class="description">
										<input type="checkbox" name="mbt_hide_domc_notice" id="mbt_hide_domc_notice" <?php checked(mbt_get_setting('hide_domc_notice'), true); ?> >
										<?php esc_attr_e('Hide Disclosure of Material Connection Disclaimer?', 'mybooktable'); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
					<?php
						if(mbt_get_upgrade() === false) {
							echo('<div class="mbt-default-affiliates">');
							if(mbt_get_setting('enable_default_affiliates')) {
								//echo(esc_attr_e('Amazon and Barnes &amp; Noble Buy Buttons enabled!', 'mybooktable').sprintf('<a href="%s" target="_blank">', esc_url(admin_url('admin.php?page=mbt_settings&mbt_setup_default_affiliates=1')).esc_attr_e('Disable').'</a>'));
							} else {
								//echo(esc_attr_e('Amazon and Barnes &amp; Noble Buy Buttons disabled!', 'mybooktable'));
								//echo '<a href="'.esc_url(admin_url('admin.php?page=mbt_settings&mbt_setup_default_affiliates=1')).'" target="_blank">'.__('Enable', 'mybooktable').'</a>';
							}
							//echo('<a href="'.esc_url(admin_url('admin.php?page=mbt_settings&mbt_setup_default_affiliates=1')).'" class="mbt-default-affiliates-small" target="_blank">'.__('What does this mean?', 'mybooktable').'</a>');
							//echo('</div>');
						}
					?>
					<input type="submit" name="save_settings" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'mybooktable'); ?>">
				</div>
				<div class="mbt-tab" id="mbt-tab-5">
					<?php do_action("mbt_integrate_settings_render"); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="mbt_google_api_key"><?php esc_attr_e('Google Maps API', 'mybooktable'); ?></label></th>
								<td>
									<div class="mbt_feedback_above mbt_feedback"></div>
									<label for="mbt_google_api_key" class="mbt-integrate-label">API Key:</label>
									<input type="text" id="mbt_google_api_key" name="mbt_google_api_key" value="<?php echo(esc_attr(mbt_get_setting('google_api_key'))); ?>" class="regular-text">
									<div class="mbt_feedback_refresh mbt_feedback_refresh_initial" data-refresh-action="mbt_google_api_key_refresh" data-refresh-nonce="<?php echo(esc_attr($mbt_nonce)); ?>" data-element="mbt_google_api_key"></div>
									<p class="description"><?php 
										/* translators: %s: link to tutorial */
										echo sprintf(esc_html__('Insert your Google Maps API Key to %s on your book pages.', 'mybooktable'), 
										'<a href="'.esc_url(admin_url('admin.php?page=mbt_settings&mbt_current_tab=3')).'" target="_blank">enable the Find Local Bookstore Form</a>'); ?>
										<p><a href="https://developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank"> <?php esc_attr_e('Learn how to get a Google Maps API Key', 'mybooktable'); ?></a></p>
									</p>									
									
								</td>
							</tr>
						</tbody>
					</table>
					<input type="submit" name="save_settings" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'mybooktable'); ?>">
				</div>
			</div>

		</form>

	</div>
<?php
}

function mbt_mailchimp_api_key_settings_render() {
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th style="color: #666"><?php esc_attr_e('MailChimp', 'mybooktable'); ?></th>
				<td>
					<input type="text" disabled="true" value="" class="regular-text">
					<p class="description"><?php echo(wp_kses_post(mbt_get_upgrade_message())); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action('mbt_integrate_settings_render', 'mbt_mailchimp_api_key_settings_render');

function mbt_amazon_web_services_general_settings_render() {
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th style="color: #666"><?php esc_attr_e('Amazon Web Services', 'mybooktable'); ?></th>
				<td>
					<input type="text" disabled="true" value="" class="regular-text">
					<p class="description"><?php echo(wp_kses_post(mbt_get_upgrade_message())); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
//add_action('mbt_integrate_settings_render', 'mbt_amazon_web_services_general_settings_render');

function mbt_genius_link_integration_general_settings_render() {
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th style="color: #666"><?php esc_attr_e('Genius Link', 'mybooktable'); ?></th>
				<td>
					<input type="text" disabled="true" value="" class="regular-text">
					<p class="description"><?php echo(wp_kses_post(mbt_get_upgrade_message())); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action('mbt_integrate_settings_render', 'mbt_genius_link_integration_general_settings_render');

function mbt_render_setup_default_affiliates_page() {
?>
	<div class="wrap mbt_settings">
		<h2><?php esc_attr_e('MyBookTable Settings', 'mybooktable'); ?></h2>

		<p style="font-size:16px;">
			<?php esc_attr_e('MyBookTable comes with over a dozen buy buttons from stores around the web. Several of these buttons, including the ones for Audible and Barnes &amp; Noble, use affiliate links. The revenue from these links is used to help support and improve the MyBookTable plugin. If you would like to use your own affiliate links, we have premium upgrades that come not only with affiliate integration but with premium support as well. You may also disable these buttons if you prefer.', 'mybooktable'); ?>
		</p>

		<form id="mbt_settings_form" method="post" action="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings'))); ?>">
			<input type="hidden" name="mbt_enable_default_affiliates" id="mbt_enable_default_affiliates" value="">
			<input type="submit" name="save_default_affiliate_settings" class="button button-primary" onclick="jQuery('#mbt_enable_default_affiliates').val('true');" value="<?php esc_attr_e('Enable Affiliate Buttons', 'mybooktable'); ?>">
			<input type="submit" name="save_default_affiliate_settings" class="button button-primary" onclick="jQuery('#mbt_enable_default_affiliates').val('false');" value="<?php esc_attr_e('Disable Affiliate Buttons', 'mybooktable'); ?>">
			<a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/" class="button button-primary" target="_blank"><?php esc_attr_e('Buy a Premium Upgrade with Affiliate support', 'mybooktable'); ?></a>
		</form>
		<br>
		<a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings'))); ?>&amp;mbt_current_tab=4"><?php esc_attr_e('Go to Affiliate Settings', 'mybooktable'); ?></a>
	</div>
<?php
}

function mbt_do_style_pack_upload() {
	if( (isset($_REQUEST['page']) && $_REQUEST['page'] === 'mbt_settings') && ((isset($_REQUEST['data-settings-nonce']) && wp_verify_nonce(sanitize_key($_REQUEST['data-settings-nonce']),'mbt-settings-nonce'))) ) {
		if(empty($_REQUEST['mbt_style_pack_id'])) { return ""; }
		$file_post = get_post(intval($_REQUEST['mbt_style_pack_id']));
		if(empty($file_post)) { return ""; }
		$style_name = $file_post->post_title;
		$file_path = get_post_meta(intval($_REQUEST['mbt_style_pack_id']), '_wp_attached_file', true);

		$nonce_url = wp_nonce_url('admin.php?page=mbt_settings', 'mbt-style-pack-upload');
		$output = mbt_get_wp_filesystem($nonce_url);
		if(!empty($output)) { return '<br>'.$output; }

		global $wp_filesystem;

		$upload_dir = wp_upload_dir();
		if(substr($upload_dir['basedir'], 0, strlen(ABSPATH)) !== ABSPATH) {
			return '<br><span class="mbt_admin_message_failure">'.__('Path error while adding style pack!', 'mybooktable').'</span>';
		}
		$content_prefix = substr($upload_dir['basedir'], strlen(ABSPATH));
		$from = $upload_dir['basedir'].DIRECTORY_SEPARATOR.$file_path;
		$to = $wp_filesystem->abspath().$content_prefix.DIRECTORY_SEPARATOR.'mbt_styles'.DIRECTORY_SEPARATOR.$style_name;
		$result = unzip_file($from, $to);

		if($result === true) {
			return '<br><span class="mbt_admin_message_success">'.__('Successfully added button pack!', 'mybooktable').'</span>';
		} else {
			return '<br><span class="mbt_admin_message_failure">'.__('Error unzipping style pack!', 'mybooktable').'</span>';
		}
	}
}



/*---------------------------------------------------------*/
/* Help Page                                               */
/*---------------------------------------------------------*/

function mbt_render_help_page() {
?>
	<div class="wrap mbt_help">
		<h2 class="mbt_help_title"><?php esc_attr_e('MyBookTable Help', 'mybooktable'); ?></h2>

		<div class="mbt_help_top_links">
			<a class="mbt_help_link mbt_apikey" href="https://gumroad.com/library/" target="_blank" >
				<div class="mbt_icon"></div><?php esc_attr_e('Need to find or manage your License Key? Access through Gumroad', 'mybooktable'); ?>
			</a>
			<a class="mbt_help_link mbt_forum" href="https://wordpress.org/support/plugin/mybooktable" target="_blank">
				<div class="mbt_icon"></div><?php esc_attr_e('Have questions or comments? Check out the Support Forum', 'mybooktable'); ?>
			</a>
			<a class="mbt_help_link mbt_develop" href="https://gitlab.com/authormedia/mybooktable/wikis" target="_blank">
				<div class="mbt_icon"></div><?php esc_attr_e('Looking for developer documentation? Find it on Gitlab', 'mybooktable'); ?>
			</a>
			<div style="clear:both"></div>
		</div>

		<?php if(mbt_get_upgrade() === false) { ?>
			<div class="mbt_get_premium_support"><a href="https://www.stormhillmedia.com/all-products/mybooktable/upgrades/" class="button button-primary"><?php esc_attr_e('Need Premium Support? Purchase an upgrade here', 'mybooktable'); ?></a></div>
		<?php } else { ?>
			<div class="mbt_help_box">
				<div class="mbt_help_box_title"><?php esc_attr_e('Premium Support Options', 'mybooktable'); ?></div>
				<div class="mbt_help_box_content">
					<ul class="mbt_premium_support">
						<li><a href="https://www.stormhillmedia.com/book-table/premium-support/" target="_blank"><div class="mbt_icon mbt_ticket"></div><?php esc_attr_e('Submit a Ticket', 'mybooktable'); ?></a></li>
						<li><a href=" https://www.stormhillmedia.com/book-table/mybooktable-feature-request/" target="_blank"><div class="mbt_icon mbt_feature"></div><?php esc_attr_e('Suggest a Feature', 'mybooktable'); ?></a></li>
						<li><a href="https://www.stormhillmedia.com/book-table/premium-support/" target="_blank"><div class="mbt_icon mbt_bug"></div><?php esc_attr_e('Submit a Bug', 'mybooktable'); ?></a><br></li>
						<div style="clear:both"></div>
					</ul>
				</div>
			</div>
		<?php } ?>

		<?php
			$mybooktable_articles = array(
				'goodreads' => array(
					'link' => 'https://www.authormedia.com/how-to-add-goodreads-book-reviews-to-mybooktable/',
					'img' => plugins_url('images/help/goodreads-reviews.jpg', dirname(__FILE__)),
					'title' => 'How to Add GoodReads Book Reviews to MyBookTable'
				),
			);
		?>

		<div class="mbt_help_box">
			<div class="mbt_help_box_title"><?php esc_attr_e('MyBookTable Tutorials', 'mybooktable'); ?></div>
			<div class="mbt_help_box_content">
				<ul class="mbt_articles">
					<?php foreach ($mybooktable_articles as $id => $article) { ?>
						<li>
							<a href="<?php echo(esc_url($article['link'])); ?>" target="_blank">
								<img src="<?php echo(wp_kses_post($article['img'])); ?>">
								<span><?php echo(esc_attr($article['title'])); ?></span>
							</a>
						</li>
					<?php } ?>
					<div style="clear:both"></div>
				</ul>
			</div>
		</div>

		<?php
			$video_tutorial = array(
/*				'overview' => array(
					'video' => 'https://player.vimeo.com/video/66113243',
					'title' => 'MyBookTable Overview',
					'desc' => 'This video is a general introduction to MyBookTable.'
				),*/
				'buy_buttons' => array(
					'video' => 'https://player.vimeo.com/video/68790296',
					'title' => 'How to Add Buy Buttons',
					'desc' => 'This video shows you how to add buy buttons to your books.'
				),
				'books_in_series' => array(
					'video' => 'https://player.vimeo.com/video/66110874',
					'title' => 'How to Put Books in a Series',
					'desc' => 'This video shows you how to add books into a series.'
				),
				'amazon_affiliates' => array(
					'video' => 'https://player.vimeo.com/video/69188658',
					'title' => 'How to Setup an Amazon Affiliate Account With MyBookTable',
					'desc' => 'This video walks you through setting up an Amazon Affiliate account and how to take your affiliate code and insert it into your MyBookTable plugin.'
				),
				'book_blurbs' => array(
					'video' => 'https://www.youtube.com/embed/LABESfhThhY',
					'title' => 'Effective Book Blurb Strategies',
					'desc' => 'This video shows you how to write book blurbs for your books.'
				),
			);
		?>

		<div class="mbt_help_box mbt_video_tutorials">
			<div class="mbt_help_box_title"><?php esc_attr_e('Tutorial Videos', 'mybooktable'); ?></div>
			<div class="mbt_video_selector">
				<?php foreach ($video_tutorial as $id => $tutorial) { ?>
					<a target="_blank" href="<?php echo(wp_kses_post($tutorial['video'])); ?>" data-video-id="mbt_video_<?php echo(esc_attr($id)); ?>"><?php echo(esc_attr($tutorial['title'])); ?></a>
				<?php } ?>
			</div>
			<div class="mbt_video_display">
				<?php foreach ($video_tutorial as $id => $tutorial) { ?>
					<div class="mbt_video" id="mbt_video_<?php echo(esc_attr($id)); ?>">
						<iframe src="<?php echo(wp_kses_post($tutorial['video'])); ?>" frameborder="0" allowfullscreen></iframe>
					</div>
				<?php } ?>
			</div>
			<div style="clear:both"></div>
		</div>

		<?php
			$wordpress_articles = array(
				'shortcodes' => array(
					'link' => 'https://www.authormedia.com/how-to-harness-the-magic-of-mybooktable-with-shortcodes/',
					'img' => plugins_url('images/help/shortcodes.jpg', dirname(__FILE__)),
					'title' => 'How to Harness the Magic of MyBookTable with Shortcodes'
				),
				'draw_readers' => array(
					'link' => 'https://www.authormedia.com/10-elements-proven-to-draw-readers-to-your-novels-website/',
					'img' => plugins_url('images/help/draw-readers.jpg', dirname(__FILE__)),
					'title' => '10 Ways Proven to Draw Readers to Your Novel\'s Website'
				),
				'upload_file' => array(
					'link' => 'https://www.authormedia.com/how-to-upload-a-file-to-your-wordpress-site/',
					'img' => plugins_url('images/help/upload-file.jpg', dirname(__FILE__)),
					'title' => 'How to Upload a File to Your WordPress Site'
				),
				'create_pdf' => array(
					'link' => 'https://www.authormedia.com/how-to-create-a-pdf/',
					'img' => plugins_url('images/help/create-pdf.jpg', dirname(__FILE__)),
					'title' => 'How to Create a PDF'
				),
				'add_hyperlink' => array(
					'link' => 'https://www.authormedia.com/how-to-add-a-hyperlink-to-wordpress/',
					'img' => plugins_url('images/help/add-link.jpg', dirname(__FILE__)),
					'title' => 'How to Add a Hyperlink to WordPress'
				),
				'hotkeys_cheat_sheet' => array(
					'link' => 'https://www.authormedia.com/the-wordpress-hotkey-cheat-sheet-every-author-needs/',
					'img' => plugins_url('images/help/hotkeys.jpg', dirname(__FILE__)),
					'title' => 'The WordPress Hotkey Cheat Sheet Every Author Needs'
				),
				'write_posts' => array(
					'link' => 'https://www.authormedia.com/how-to-create-or-edit-posts-in-wordpress/',
					'img' => plugins_url('images/help/write-posts.jpg', dirname(__FILE__)),
					'title' => 'How to Create or Edit Posts in WordPress'
				),
				'protect_from_hackers' => array(
					'link' => 'https://www.authormedia.com/how-to-keep-your-wordpress-site-secure-from-hackers/',
					'img' => plugins_url('images/help/hackers.jpg', dirname(__FILE__)),
					'title' => 'How to Keep Your WordPress Site Secure From Hackers'
				),
				'zen_mode' => array(
					'link' => 'https://www.authormedia.com/how-to-find-zen-mode-in-wordpress/',
					'img' => plugins_url('images/help/zen.jpg', dirname(__FILE__)),
					'title' => 'How To Find Zen Mode in WordPress'
				),
				'book_marketing_ideas' => array(
					'link' => 'https://www.authormedia.com/89-book-marketing-ideas-that-will-change-your-life/',
					'img' => plugins_url('images/help/ideas.jpg', dirname(__FILE__)),
					'title' => '89+ Book Marketing Ideas That Will Change Your Life'
				),
				'standard_nonfiction' => array(
					'link' => 'https://www.authormedia.com/standard-pages-for-a-non-fiction-website/',
					'img' => plugins_url('images/help/standard-nonfiction.jpg', dirname(__FILE__)),
					'title' => 'Standard Pages for A Non-Fiction Website'
				),
				'standard_fiction' => array(
					'link' => 'https://www.authormedia.com/standard-pages-for-a-fiction-website/',
					'img' => plugins_url('images/help/standard-fiction.jpg', dirname(__FILE__)),
					'title' => 'Standard Pages for A Fiction Website'
				),
				'what_readers_want' => array(
					'link' => 'https://www.authormedia.com/what-readers-want-from-your-author-website/',
					'img' => plugins_url('images/help/what-readers-want.jpg', dirname(__FILE__)),
					'title' => '6 Things Readers Want from Your Author Website'
				),
			);
		?>

		<div class="mbt_help_box">
			<div class="mbt_help_box_title"><?php esc_attr_e('General WordPress Guides &amp; Tutorials', 'mybooktable'); ?></div>
			<div class="mbt_help_box_content">
				<ul class="mbt_articles">
					<?php foreach ($wordpress_articles as $id => $article) { ?>
						<li>
							<a href="<?php echo(esc_url($article['link'])); ?>" target="_blank">
								<img src="<?php echo(wp_kses_post($article['img'])); ?>">
								<span><?php echo(esc_attr($article['title'])); ?></span>
							</a>
						</li>
					<?php } ?>
					<div style="clear:both"></div>
				</ul>
			</div>
		</div>

		<?php do_action("mbt_render_help_page", 'mybooktable'); ?>

		<br>
	</div>

<?php
}

add_filter('wp101_get_custom_help_topics', 'mbt_add_wp101_help');
function mbt_add_wp101_help($videos) {
	//$videos["mbt-overview"] = array("title" => "MyBookTable Overview", "content" => '<iframe width="640" height="360" src="https://player.vimeo.com/video/66113243" frameborder="0" allowfullscreen></iframe>');
	$videos["mbt-buybuttons"] = array("title" => "MyBookTable Buy Buttons", "content" => '<iframe width="640" height="360" src="https://player.vimeo.com/video/68790296" frameborder="0" allowfullscreen></iframe>');
	$videos["mbt-booksinseries"] = array("title" => "How to Put Books in a Series", "content" => '<iframe width="640" height="360" src="https://player.vimeo.com/video/66110874" frameborder="0" allowfullscreen></iframe>');
	$videos["mbt-amazonaffiliates"] = array("title" => "MyBookTable Amazon Affiliate Accounts", "content" => '<iframe width="640" height="360" src="https://player.vimeo.com/video/69188658" frameborder="0" allowfullscreen></iframe>');
	$videos["mbt-bookblurbs"] = array("title" => "MyBookTable Book Blurbs", "content" => '<iframe width="640" height="360" src="https://www.youtube.com/embed/LABESfhThhY" frameborder="0" allowfullscreen></iframe>');
	return $videos;
}



/*---------------------------------------------------------*/
/* Dashboard Page                                          */
/*---------------------------------------------------------*/

function mbt_render_dashboard() {
	
	if(!empty($_REQUEST['subpage']) and $_REQUEST['subpage'] === 'mbt_founders_page' && (isset($_REQUEST['nonce_dashboard']) && wp_verify_nonce(sanitize_key($_REQUEST['nonce_dashboard']),'dashboard_nonce'))) {
		return mbt_render_founders_page(); 
	}
	if(!empty($_REQUEST['subpage']) and $_REQUEST['subpage'] === 'mbt_get_upgrade_page' && (isset($_REQUEST['nonce_dashboard']) && wp_verify_nonce(sanitize_key($_REQUEST['nonce_dashboard']),'dashboard_nonce')))
	{
		return mbt_render_get_upgrade_page();
	}
	
	$founderpage_url = 
		wp_nonce_url(admin_url('admin.php?page=mbt_dashboard&subpage=mbt_founders_page'),'dashboard_nonce','nonce_dashboard');
			 
	$upgradepage_url = 
	wp_nonce_url(admin_url('admin.php?page=mbt_dashboard&subpage=mbt_get_upgrade_page'),'dashboard_nonce','nonce_dashboard');
	
	$import_url = wp_nonce_url(admin_url('admin.php?page=mbt_import'),'import_nonce','nonce_import');
?>

	<div class="wrap mbt-dashboard">
		<h2><?php esc_attr_e('MyBookTable', 'mybooktable'); ?></h2>
		<table class="mbt-dashboard-table"><tbody>
			<tr>
				<td class="dashboard-contents-left">
					<div class="welcome-video-container">
						<a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_help'))); ?>"><?php esc_attr_e('Tutorial Videos', 'mybooktable'); ?></a>
					</div>

					<div class="buttons-container">
						<a href="<?php echo(esc_url(admin_url('post-new.php?post_type=mbt_book'))); ?>" class="add-new-book"><?php esc_attr_e('Add New Book', 'mybooktable'); ?></a>
					</div>

					<div class="welcome-panel">
						<div class="welcome-panel-content">
							<h3><?php esc_attr_e('Welcome to MyBookTable!', 'mybooktable'); ?></h3>
							<div class="welcome-panel-column-container">
								<div class="welcome-panel-column">
									<h4><?php esc_attr_e('First Steps', 'mybooktable'); ?></h4>
									<ul>
										<?php if(!mbt_get_setting('installed_examples')) { ?>
											<li><a href="<?php echo(esc_url(admin_url('edit.php?post_type=mbt_book&mbt_install_examples=1'))); ?>" class="welcome-icon welcome-view-site"><?php esc_attr_e('Look at some example Books', 'mybooktable'); ?></a></li>
										<?php } ?>
										<li><a href="<?php echo(esc_url(admin_url('post-new.php?post_type=mbt_book'))); ?>" class="welcome-icon welcome-add-page"><?php esc_attr_e('Create your first book', 'mybooktable'); ?></a></li>
										<li><a href="<?php echo(esc_url($import_url)); ?>" class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Import Books', 'mybooktable'); ?></a></li>
										<li><a href="<?php echo(esc_url(mbt_get_booktable_url())); ?>" class="welcome-icon welcome-view-site"><?php esc_attr_e('View your Book Table', 'mybooktable'); ?></a></li>
									</ul>
								</div>
								<div class="welcome-panel-column">
									<h4><?php esc_attr_e('Actions', 'mybooktable'); ?></h4>
									<ul>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('edit.php?post_type=mbt_book'))); ?>"><?php esc_attr_e('Books', 'mybooktable'); ?></a></div></li>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('edit-tags.php?taxonomy=mbt_author'))); ?>"><?php esc_attr_e('Authors', 'mybooktable'); ?></a></div></li>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('edit-tags.php?taxonomy=mbt_genre'))); ?>"><?php esc_attr_e('Genres', 'mybooktable'); ?></a></div></li>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('edit-tags.php?taxonomy=mbt_series'))); ?>"><?php esc_attr_e('Series', 'mybooktable'); ?></a></div></li>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('edit-tags.php?taxonomy=mbt_tag'))); ?>"><?php esc_attr_e('Tags', 'mybooktable'); ?></a></div></li>
										<li><div class="welcome-icon welcome-widgets-menus"><?php esc_attr_e('Manage', 'mybooktable'); ?> <a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings'))); ?>"><?php esc_attr_e('Settings', 'mybooktable'); ?></a></div></li>
									</ul>
								</div>
								<div class="welcome-panel-column welcome-panel-last">
									<h4><?php esc_attr_e('Resources', 'mybooktable'); ?></h4>
									<ul>
										<li><a href="<?php echo(esc_url(admin_url('admin.php?page=mbt_help'))); ?>" class="welcome-icon welcome-learn-more"><?php esc_attr_e('Get help using MyBookTable', 'mybooktable'); ?></a></li>
										<li><a href="https://gitlab.com/authormedia/mybooktable/wikis" class="welcome-icon welcome-learn-more" target="_blank"><?php esc_attr_e('Developer Documentation', 'mybooktable'); ?></a></li>
										<li><a href="https://authormedia.us1.list-manage.com/subscribe?u=b7358f48fe541fe61acdf747b&amp;id=6b5a675fcf" class="welcome-icon welcome-write-blog" target="_blank"><?php esc_attr_e('Sign Up for Book Marketing Tips from Author Media', 'mybooktable'); ?></a></li>
										<li><a href="<?php echo(esc_url($founderpage_url)); ?>" class="welcome-icon welcome-write-blog"><?php esc_attr_e('Plugin Founders', 'mybooktable'); ?></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="metabox-holder">
						<div id="mbt_dashboard_rss" class="postbox">
							<div class="handlediv" title=""><br></div>
							<h3 class="hndle"><?php esc_attr_e('Book Marketing Tips from Author Media', 'mybooktable'); ?></h3>
							<div class="inside">
								<?php wp_widget_rss_output(array(
									'link' => 'https://www.authormedia.com/',
									'url' => 'https://www.authormedia.com/feed/',
									'title' => __('Recent News from Author Media', 'mybooktable'),
									'items' => 3,
									'show_summary' => 1,
									'show_author' => 0,
									'show_date' => 0,
								)); ?>
							</div>
						</div>
						<div id="mbt_dashboard_upsell" class="postbox">
							<div class="handlediv"><br></div>
							<h3 class="hndle"><?php esc_attr_e('Current Version', 'mybooktable'); ?></h3>
							<div class="inside">
								<h1 class="mybooktable-version"><?php echo(esc_attr_e('You are currently using').' <span class="current-version">'.esc_attr_e('MyBookTable').wp_kses_post(MBT_VERSION).'</span>'); ?></h1>
								<?php
									$with_the = _x('with the', 'You are currently using MyBookTable (with the) Developer Upgrade.', 'mybooktable');
									$dev_upgrade = __('Developer Upgrade', 'mybooktable');
									$pro_upgrade = __('Professional Upgrade', 'mybooktable');
								?>
								<?php if(mbt_get_upgrade() == 'mybooktable-dev3' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.esc_attr($dev_upgrade).' '.wp_kses_post(MBTDEV3_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() == 'mybooktable-pro3' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.wp_kses_post($pro_upgrade).' '.wp_kses_post(MBTPRO3_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() == 'mybooktable-dev2' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.wp_kses_post($dev_upgrade).' '.wp_kses_post(MBTDEV2_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() == 'mybooktable-pro2' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.wp_kses_post($pro_upgrade).' '.wp_kses_post(MBTPRO2_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() == 'mybooktable-dev' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.wp_kses_post($dev_upgrade).' '.wp_kses_post(MBTDEV_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() == 'mybooktable-pro' and mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="upgrade-version"><?php echo(esc_attr($with_the).' <span class="current-version">'.wp_kses_post($pro_upgrade).' '.wp_kses_post(MBTPRO_VERSION).'</span>'); ?></h1>
									<h2 class="thank-you"><?php esc_attr_e('Thank you for your support!', 'mybooktable'); ?></h2>
								<?php } else if(mbt_get_upgrade() and !mbt_get_upgrade_plugin_exists()) { ?>
									<h1 class="activate-upgrade"><?php echo(wp_kses_post(mbt_get_upgrade_message())); ?></h1>
								<?php } else { ?>
									<h2 class="upgrade-title"><?php esc_attr_e('Upgrade your MyBookTable and get:', 'mybooktable'); ?></h2>
									<ul class="upgrade-list">
										<li><?php esc_attr_e('Premium Support', 'mybooktable'); ?></li>
										<li><?php esc_attr_e('Amazon Affiliate Integration', 'mybooktable'); ?></li>
										<li><?php esc_attr_e('Barnes &amp; Noble Affiliate Integration', 'mybooktable'); ?></li>
										<li><?php esc_attr_e('Universal Buy Button', 'mybooktable'); ?></li>
										<li><a href="https://mybooktable.com" target="_blank"><?php esc_attr_e('And much much more', 'mybooktable'); ?></a></li>
									</ul>
								<?php } ?>
							</div>
						</div>
					</div>
				</td>
				<td class="welcome-panel welcome-panel-promotions-right">
					<div>
						<div class="welcome-panel-content">
							<h3><?php esc_attr_e('Promotions', 'mybooktable'); ?></h3>
							<div class="welcome-panel-column-container">
								<div class="welcome-panel-column">
									<a href="https://www.authormedia.com/all-products/7-tax-saving-tips-irs-doesnt-want-authors-know-2/" target="_blank"><img src="<?php echo(esc_url(plugins_url('images/promotions/tax_strategies_authors.jpg', dirname(__FILE__)))); ?>"></a>
									<div style="font-size: 20px; font-weight: 700; text-align: center;">$99.00</div>
								</div>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</tbody></table>
		<div style="clear:both"></div>
	</div>

<?php
}



/*---------------------------------------------------------*/
/* Upgrade Page                                            */
/*---------------------------------------------------------*/

function mbt_render_get_upgrade_page() {
?>
	<div class="wrap mbt_settings">
		<h2><?php esc_attr_e('Get Upgrade', 'mybooktable'); ?></h2>
		<?php
			function mbt_get_upgrade_check_is_plugin_inactivate($slug) {
				$plugin = $slug.DIRECTORY_SEPARATOR.$slug.'.php';
				if(!is_wp_error(activate_plugin($plugin))) {
					echo('<p>'.esc_attr_e('Plugin successfully activated.', 'mybooktable').'</p>');
					return true;
				}

				return false;
			}

			function mbt_get_upgrade_get_plugin_url($slug) {
				global $wp_version;

				$api_key = mbt_get_setting('api_key');
				if(!empty($api_key)) {
					$to_send = array(
						'action'  => 'basic_check',
						'version' => 'none',
						'api-key' => $api_key,
						'site'    => get_bloginfo('url')
					);

					$options = array(
						'timeout' => 3,
						'body' => $to_send,
						'user-agent' => 'WordPress/'.$wp_version
					);

					$raw_response = wp_remote_post('https://api.authormedia.com/plugins/'.$slug.'/update-check', $options);
					if(!is_wp_error($raw_response) and wp_remote_retrieve_response_code($raw_response) == 200) {
						$response = maybe_unserialize(wp_remote_retrieve_body($raw_response));
						if(is_array($response) and !empty($response['package'])) {
							return $response['package'];
						}
					}
				}

				return '';
			}

			function mbt_get_upgrade_do_plugin_install($name, $slug, $url) {
				if(empty(esc_url($url))) { echo('<p>'.esc_attr_e('An error occurred while trying to retrieve the plugin from the server. Please check your License Key.', 'mybooktable').'</p>'); return; }
				if(!current_user_can('install_plugins')) { echo('<p>'.esc_attr_e('Sorry, but you do not have the correct permissions to install plugins. Contact the administrator of this site for help on getting the plugin installed.', 'mybooktable').'</p>'); return; }

				$nonce_url = wp_nonce_url('admin.php?page=mbt_dashboard', 'mbt-install-upgrade');
				$output = mbt_get_wp_filesystem($nonce_url);
				
				if(!empty($output)) { echo(wp_kses_post($output)); return; }

				$plugin = array();
				$plugin['name']   = $name;
				$plugin['slug']   = $slug;
				$plugin['source'] = $url;

				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				
				$args = array(
					'type'   => 'web',
					/* translators: %s: plugin name */
					'title'  => sprintf(esc_attr_e('Installing Plugin: %s', 'mybooktable'), $plugin['name']),
					'nonce'  => 'install-plugin_' . $plugin['slug'],
					'plugin' => $plugin,
				);

				add_filter('install_plugin_complete_actions', '__return_false', 100);
				$upgrader = new Plugin_Upgrader(new Plugin_Installer_Skin($args));
				$upgrader->install($plugin['source']);
				wp_cache_flush();
				remove_filter('install_plugin_complete_actions', '__return_false', 100);

				$plugin_info = $upgrader->plugin_info();
				$activate    = activate_plugin($plugin_info);
				if(is_wp_error($activate)) { echo('<div id="message" class="error"><p>'.wp_kses_post($activate->get_error_message()).'</p></div>'); }
			}

			$slug = mbt_get_upgrade();
			if(empty($slug) or mbt_get_upgrade_plugin_exists()) {
				echo('<p>'.esc_attr_e('You have no Upgrades available to download at this time.', 'mybooktable').'</p>');
			} else {
				if(!mbt_get_upgrade_check_is_plugin_inactivate($slug)) {
					$url = mbt_get_upgrade_get_plugin_url($slug);
					if($slug == 'mybooktable-dev3') { $name = 'MyBookTable Developer Upgrade 3.0'; }
					if($slug == 'mybooktable-pro3') { $name = 'MyBookTable Professional Upgrade 3.0'; }
					if($slug == 'mybooktable-dev2') { $name = 'MyBookTable Developer Upgrade 2.0'; }
					if($slug == 'mybooktable-pro2') { $name = 'MyBookTable Professional Upgrade 2.0'; }
					if($slug == 'mybooktable-dev')  { $name = 'MyBookTable Developer Upgrade'; }
					if($slug == 'mybooktable-pro')  { $name = 'MyBookTable Professional Upgrade'; }
					mbt_get_upgrade_do_plugin_install($name, $slug, $url);
				}
			}
		?>
		<a class="button button-primary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_dashboard'))); ?>"><?php esc_attr_e('Back to Dashboard', 'mybooktable'); ?></a>
	</div>
<?php
}



/*---------------------------------------------------------*/
/* Founders Page                                           */
/*---------------------------------------------------------*/

function mbt_render_founders_page() {
?>
	<div class="wrap">
		<h2><?php esc_attr_e('MyBookTable Founders', 'mybooktable'); ?></h2>
		<p><?php esc_attr_e('This plugin was made possible by some adventurous kickstarters. We are so grateful for the members of the writing community who backed our Kickstarter project and helped us launch this plugin! Below are the ones who sponsored at the $75 level or higher. Thank you for your support!', 'mybooktable'); ?></p>
		<h3 dir="ltr">$75+ Backer Level</h3>
		<ul>
			<li><a href="http://www.stevelaube.com">Steve Laube</a>, founder of <a href="http://www.stevelaube.com">Steve Laube Agency</a></li>
			<li><a href="http://www.dianneprice-author.com/">Dianne Price</a>, author of <a href="http://www.amazon.com/dp/B00CGDPZ68">Dying Light</a></li>

			<li><a href="http://www.creatingthestory.com">Inger Fountain</a>, author of <a href="http://www.creatingthestory.com">Creating the Story</a></li>
			<li><a href="http://www.caroletowriss.com">Carole Towriss</a>, author of <a href="http://www.amazon.com/Shadow-Sinai-Journey-Canaan-ebook/dp/B00BM92PUQ/ref=sr_1_1?ie=UTF8&amp;qid=1366837954&amp;sr=8-1&amp;keywords=towriss">The Shadow of Sinai</a></li>
			<li><a href="http://www.hiswords2growby.com">Lisa Phillips</a>, author of <a href="http://www.hiswords2growby.com">Words 2 Grow By</a></li>
			<li><a href="http://tracyhigley.com">Tracy Higley</a>, author of <a href="http://amzn.to/RxemDx">So Shines the Night</a></li>
			<li><a href="http://www.normandiefischer.com">Normandie Fischer</a>, author of <a href="http://www.amazon.com/Becalmed-Normandie-Fischer/dp/1938499611/">Becalmed</a></li>
			<li><a href="http://www.judykbaer.com/">Judy Baer</a>, author of <a href="http://www.amazon.com/Judy-Baer/e/B000APHRLK/">Tales from Grace Chapel Inn</a></li>
			<li><a href="http://www.robinleehatcher.com">Robin Lee Hatcher</a>, author of <a href="http://www.amazon.com/exec/obidos/ASIN/031025809X/novelistrobinlee">Betrayed</a></li>
			<li><a href="http://www.contrarymarket.com">Krystine Kercher</a>, author of <a href="http://www.amazon.com/Shadow-Land-Legends-Astarkand/dp/148262477X/">A Shadow on the Land</a></li>
			<li><a href="http://www.mischievousmalamute.com">Harley Christensen</a>, author of <a href="http://www.amazon.com/Gemini-Rising-Mischievous-Malamute-ebook/dp/B00A9FTM3C">Gemini Rising</a></li>
			<li><a href="http://www.juliemcovert.com">Julie Covert</a>, author of <a href="http://www.amazon.com/Art-Winter-Julie-M-Covert/dp/0985369000/ref=sr_1_1?ie=UTF8&amp;qid=1366665243&amp;sr=8-1&amp;keywords=Art+of+Winter">Art of Winter</a></li>
			<li><a href="http://www.dogmathebook.com">Barbara Brunner</a>, author of <a href="http://www.amazon.com/Dog-Ma-Slobber-barbara-boswell-brunner/dp/1478106581/ref=sr_1_1?ie=UTF8&amp;qid=1366664672&amp;sr=8-1&amp;keywords=Dog-Ma">Dog-Ma: The Zen of Slobber</a></li>
			<li><a href="http://writingas.kerineal.com">Keri Neal</a>, author of <a href="http://www.amazon.com/Keri-Neal/e/B007SD2KNC">Torn</a></li>
			<li><a href="http://angelahuntbooks.com">Angela Hunt</a>, author of <a href="http://www.amazon.com/Angela-E.-Hunt/e/B000AQ1EJU/ref=sr_tc_2_0?qid=1366664404&amp;sr=8-2-ent">The Offering</a></li>
			<li><a href="http://www.calebbreakey.com">Caleb Jennings Breakey</a>, author of <a href="http://www.amazon.com/Called-Stay-Uncompromising-Mission-Church/dp/0736955429">Called to Stay</a></li>
			<li><a href="http://www.thedigitaldelusion.com">Doyle Buehler</a>, author of <a href="http://digitaldelusion.info/">The Digital Delusion</a></li>
			<li><a href="http://johnwhowell.com">John Howell</a></li>
			<li><a href="http://www.amazingthingsministry.com">Mona Corwin</a>, author of <a href="http://www.amazon.com/Table-Doing-Savoring-Scripture-together/dp/1415868417/" target="_blank">Table for Two</a></li>
			<li><a href="http://saltrunpublishing.com" target="_blank">Kellie Sharpe</a>, author of <a href="http://www.amazon.com/Surviving-Foaling-Season-ebook/dp/B00BEZBFSG" target="_blank">Surviving Foaling Season</a></li>
		</ul>
		<h3 dir="ltr">$100 + Backer Level</h3>
		<ul>
			<li><a href="http://www.thetrustdiamond.com">Tink DeWitt</a>, author of <a href="http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&amp;field-keywords=The+Trust+Diamond">The Trust Diamond</a></li>
			<li><a href="http://authorsbroadcast.com">Reno Lovison</a>, author of <a href="http://www.amazon.com/Turn-Your-Business-Card-Into/dp/1434847683/">Turn Your Business Card into Business</a></li>
			<li><a href="http://www.alex-f-fayle.com">Alex F. Fayle</a>, author of <a href="http://www.amazon.com/An-Extraordinarily-Ordinary-Life-ebook/dp/B0051EZL54/">An Extraordinarily Ordinary Life</a></li>
			<li><a href="http://www.talkstorymedia.net">Barbara Holbrook</a>, founder of <a href="http://www.talkstorymedia.net">TalkStory Media</a></li>
			<li><a href="http://mirwriter.wordpress.com">Mir Schultz</a>, author of <a href="http://mirwriter.wordpress.com">Mir Writes</a></li>
			<li><a href="http://www.beachhousesinvabeach.com">Bruce Gwaltney</a>, author of <a href="http://www.beachhousesinvabeach.com">Beach Houses in Virginia Beach</a></li>
			<li><a href="http://www.rabbimoffic.com">Evan Moffic</a>, author of <a href="http://www.amazon.com/-/e/B00BNHHWPK">Wisdom for People of all Faiths</a></li>
			<li><a href="http://www.adminismith.com">Janica Smith</a>, author of <a href="http://www.adminismith.com">Virtual Business Solutions</a></li>
			<li><a href="http://www.booksbyjoy.com">Joy DeKok</a>, author of <a href="http://www.amazon.com/s/ref=ntt_athr_dp_sr_1?_encoding=UTF8&amp;field-author=Joy%20DeKok&amp;search-alias=books&amp;sort=relevancerank">Rain Dance</a></li>
			<li><a href="http://www.lindahoenigsberg.com">Linda Hoenigsberg</a></li>
			<li>Lisa Hendrix</li>
			<li><a href="http://vickivlucas.com/" target="_blank">Vicki Lucas</a>, author of <a href="http://www.amazon.com/Vicki-V.-Lucas/e/B006X7117U/ref=ntt_athr_dp_pel_1" target="_blank">Toxic</a></li>
			<li><a href="http://www.Hunting-America.com" target="_blank">Richard James</a></li>
		</ul>
		<h3>$150 + Backer Level</h3>
		<ul>
			<li><a href="http://www.dollarplanning.com">Brenda Taylor</a>, author of <a href="http://www.dollarplanning.com">Dollar Planning</a></li>
			<li><a href="http://lauradomino.com">Laura Domino</a>, author of <a href="http://lauradomino.com">Laura Domino</a></li>
			<li><a href="http://www.warmenhoven.co">Adrianus Warmenhoven</a>, author of <a href="http://www.warmenhoven.co">Warmenhoven</a></li>
			<li><a href="http://www.accidentalauthor.ca">Mike Hartner</a>, author of <a href="http://www.amazon.com/I-Walter-ebook/dp/B00C7FJ7B4/ref=sr_1_1?ie=UTF8&amp;qid=1366292416&amp;sr=8-1&amp;keywords=%22I%2C+Walter%22">I, Walter</a></li>
			<li><a href="http://www.kathleenoverby.com">Kathleen Overby</a></li>
			<li><a href="http://vivianmabuni.com/">Vivian Mabuni</a>, author of <a href="http://vivianmabuni.com/">Warrior in Pink</a></li>
			<li><a href="http://wadewebster.com">Wade Webster</a></li>
			<li><a href="http://gloriaclover.com">Gloria Clover</a>, author of <a href="http://www.amazon.com/Children-King-Book-Two-ebook/dp/B008W1AUUO/ref=sr_1_1?ie=UTF8&amp;qid=1366664833&amp;sr=8-1&amp;keywords=The+Fire+Starter%2C+Clover">The Fire Starter</a></li>
			<li><a href="http://www.lisabergren.com">Lisa Bergren</a>, author of <a href="http://www.amazon.com/Glamorous-Illusions-Novel-Grand-Series/dp/1434764303/ref=tmm_pap_title_0">Glamorous Illusions</a></li>
			<li><a href="http://techguyjay.com/books" target="_blank">Jay Donovan</a></li>
			<li><a href="http://www.DebiJHolliday.com" target="_blank">Debi J. Holliday</a></li>
			<li><a href="http://www.nickbuchan.com" target="_blank">Nick and Lu</a></li>
			<li><a href="http://www.sbbflonghorns.com" target="_blank">Chrisann Merriman</a></li>
			<li><a href="http://www.cloudlinkco.com" target="_blank">Brandon Frye</a></li>
			<li>Diane Finlayson</li>
			<li>David Buggs</li>
		</ul>
		<h3 dir="ltr">$250+ Backer Level</h3>
		<ul>
			<li><a href="http://christopherschmitt.com/">Christopher Schmitt</a>, author of <a href="http://www.amazon.com/Designing-Web-Mobile-Graphics-Fundamental/dp/0321858549/">Designing Web and Mobile Graphics</a></li>
			<li><a href="http://hotappleciderbooks.com">Les and N.J. Lindquist</a>, authors of <a href="http://www.amazon.com/Second-Cup-Hot-Apple-Cider/dp/0978496310/ref=sr_1_3?s=books&amp;ie=UTF8&amp;qid=1366743096&amp;sr=1-3">A Second Cup of Apple Cider</a></li>
			<li><a href="http://www.inboundmastery.com">Tony Tovar</a>, <a href="http://www.amazon.com/dp/B008R1F446">How to Make Money from Writing Online</a></li>
			<li><a href="http://www.remcdermott.com">R.E. McDermott</a>, author of <a href="http://www.amazon.com/Deadly-Straits-Dugan-Novel-ebook/dp/B0057AMO2A">Deadly Straits</a></li>
			<li><a href="http://www.marydemuth.com">Mary DeMuth</a>, author of <a href="http://amzn.to/sDBhqT">The 11 Secrets of Getting Published</a></li>
			<li><a href="http://livinignited.org/Livin_Ignited/Home.html">Nancy Grisham</a>, author of <a href="http://www.amazon.com/Thriving-Trusting-God-Life-Fullest/dp/080101543X/ref=sr_1_1?ie=UTF8&amp;qid=1366995488&amp;sr=8-1&amp;keywords=nancy+grisham">Thriving: Trusting God for Life to the Fullest</a></li>
			<li><a href="http://www.markmittleburg.com">Mark Mittleberg</a>, author of <a href="http://www.amazon.com/Confident-Faith-Building-Foundation-Beliefs/dp/1414329962/ref=sr_1_2?s=books&amp;ie=UTF8&amp;qid=1367010724&amp;sr=1-2&amp;keywords=confident+faith">Confident Faith</a></li>
			<li><a href="http://www.ageviewpress.com">Jeanette Vaughan</a>, author of <a href="http://www.amazon.com/Flying-Solo-Unconventional-Navigates-Turbulence/dp/061561888X/ref=sr_1_1?ie=UTF8&amp;qid=1366856431&amp;sr=8-1&amp;keywords=jeanette+vaughan+flying+solo">Flying Solo</a></li>
			<li><a href="http://markmccluretoday.com" target="_blank">Mark McClure</a></li>
			<li><a href="http://www.recalculatingthebook.com/" target="_blank">Dennis Pappenfus</a></li>
			<li><a href="http://www.advancedfictionwriting.com">Randy Ingermanson</a>, author of <a href="http://www.amazon.com/Writing-Fiction-Dummies-Randy-Ingermanson/dp/0470530707/">Writing Fiction for Dummies</a></li>
			<li><a href="http://www.qualityusproducts.com">Ellen Pope</a></li>
		</ul>
	</div>
<?php
}



/*---------------------------------------------------------*/
/* Import Page                                             */
/*---------------------------------------------------------*/

function mbt_import_page_init() {
	add_action('wp_ajax_mbt_import_page_import_book', 'mbt_import_page_import_book');
}
add_action('mbt_init', 'mbt_import_page_init');

function mbt_import_page_import_book() {
	$importer_nonce = wp_create_nonce('importer_nonce','nonce_importer');
	$response = array('error' => 'Unknown error!');

	$importers = mbt_get_importers();
	$import_data = get_transient('mbt_import_data');

	if(is_array($import_data) and isset($_POST['book']) and !empty($importers[$import_data['import_type']]) && wp_verify_nonce(sanitize_key($importer_nonce),'importer_nonce')) {
		$importer = $importers[$import_data['import_type']];
		$pbook = sanitize_text_field(wp_unslash($_POST['book']));
		$book = apply_filters('mbt_pre_import_book', $import_data['book_list'][$pbook], $import_data['import_type']);
		if(is_array($book)) {
			$imported_book_id = mbt_import_book($book);
			$response = array(
				'book_link' => get_permalink($imported_book_id),
				'title' => $book['title'],
			);
		} else {
			$response = array('error' => $book);
		}
	}

	echo(wp_json_encode($response));
	die();
}

function mbt_render_import_page() {
	$import_nonce = wp_create_nonce('import_nonce','nonce_import');
	$importers = mbt_get_importers();
	?> 
	<div class="main-importer-wrap">
	<?php
	if(!empty($_GET['mbt_import_type']) && wp_verify_nonce(sanitize_key($import_nonce),'import_nonce')) {
		$import_type = sanitize_text_field(wp_unslash($_GET['mbt_import_type']));
		if(!empty($importers[$import_type])) {
			$importer = $importers[$import_type];

			if(isset($_POST['import-submit'])) {
				if(is_callable($importer['get_book_list'])) {
					$book_list = call_user_func($importer['get_book_list']);
				} else if(is_array($importer['get_book_list'])) {
					$book_list = call_user_func($importer['get_book_list']['parse_import_form']);
				}
				if(!is_array($book_list)) {
					?>
						<div class="wrap mbt-book-importer">
							<h2><?php echo(isset($importer['page_title']) ? esc_attr($importer['page_title']) : esc_attr($importer['name'])); ?></h2>
							<span class="mbt-book-importer-error"><?php esc_attr_e('Import error:'); ?></span>
							<?php echo(wp_kses_post($book_list)); ?>
						</div>
					<?php
					return;
				}

				set_transient('mbt_import_data', array('import_type' => $import_type, 'book_list' => $book_list), DAY_IN_SECONDS);
				?>
					<div class="wrap mbt-book-importer">
						<h2><?php echo(isset($importer['page_title']) ? esc_attr($importer['page_title']) : esc_attr($importer['name'])); ?></h2>

						<h3 id="mbt-book-import-progress-status"><?php esc_attr_e('Please wait, your books are importing...', 'mybooktable'); ?></h3>
						<div id="mbt-book-import-progress">
							<div id="mbt-book-import-progress-bar"><div id="mbt-book-import-progress-bar-inner"></div></div>
							<button type="button" class="import-stop button"><?php esc_attr_e('Stop Import', 'mybooktable'); ?></a>
						</div>
						<ul id="mbt-book-import-results"></ul>
						<a href="<?php echo(esc_url(admin_url('edit.php?post_type=mbt_book'))); ?>" class="import-continue button button-primary" style="display:none"><?php esc_attr_e('Continue', 'mybooktable'); ?></a>
						<script type="text/javascript">
							jQuery(document).ready(function() {
								var num_books = <?php echo(count($book_list)); ?>;

								var import_stopped = false;
								jQuery('.import-stop').click(function() {
									import_stopped = true;
									jQuery(this).attr('disabled', 'disabled');
								});

								function mbt_book_imported(response) {
									try { response = JSON.parse(response); }
									catch(e) { response = {error: 'PHP error: "'+response+'"'}; }

									var el = null;
									if('error' in response) {
										el = jQuery('<li class="mbt-result-failure">'+response.error+'</li>');
									} else {
										el = jQuery('<li class="mbt-result-success">'+mbt_admin_pages_i18n.successfully_imported+': <a href="'+response.book_link+'" target="_blank">'+response.title+'</a></li>');
									}
									jQuery('#mbt-book-import-results').append(el);
									jQuery('#mbt-book-import-results').scrollTop(jQuery('#mbt-book-import-results').prop('scrollHeight'));
								}

								function mbt_book_request_failed(query) {
									mbt_book_imported('HTTP error: '+query.status+' '+query.statusText);
								}

								function mbt_import_book(i) {
									i = typeof i !== 'undefined' ? i : 0;

									if(i >= num_books || import_stopped) {
										if(import_stopped) {
											jQuery('#mbt-book-import-progress-status').text(mbt_admin_pages_i18n.import_interrupted);
										} else {
											jQuery('#mbt-book-import-progress-status').text(mbt_admin_pages_i18n.import_complete);
										}
										jQuery('.import-stop').remove();
										jQuery('.import-continue').show();
										return;
									}

									jQuery.post(ajaxurl, {
										action: 'mbt_import_page_import_book',
										book: i,
									}).done(mbt_book_imported).fail(mbt_book_request_failed).always(function() {
										var percent = ((i+1)/num_books)*90.0 + 10.0;
										jQuery('#mbt-book-import-progress-bar-inner').css('width', percent+'%');
										mbt_import_book(i+1);
									});
								}

								mbt_import_book();
							});
						</script>
					</div>
				<?php			
			} else {
				?>
					<div class="wrap mbt-book-importer">
					<h2><?php echo(isset($importer['page_title']) ? esc_attr($importer['page_title']) : esc_attr($importer['name'])); ?></h2>
					<form action="<?php echo(esc_url(admin_url('admin.php?page=mbt_import&mbt_import_type='.$import_type))); ?>" method="POST" enctype="multipart/form-data">
					<?php
						if(is_callable($importer['get_book_list'])) {
							$book_list = call_user_func($importer['get_book_list']);
							echo('<h3>'.esc_attr_e('The following books will be imported:', 'mybooktable').'</h3>');
							echo('<ul class="mbt-import-books-preview">');
							foreach($book_list as $book) {
								echo('<li>'.esc_attr($book['title']).'</li>');
							}
							echo('</ul>');
							echo('<h3>'.esc_attr_e('Are you sure you want to import these books?', 'mybooktable').'</h3>');
							echo('<div style="clear:both"></div><input type="submit" name="import-submit" class="import-submit button button-primary" value="'.sprintf(esc_html__('Import', 'mybooktable')).'">');
						} else if(is_array($importer['get_book_list'])) {
							if(!isset($importer['get_book_list']['render_import_form']) or !isset($importer['get_book_list']['parse_import_form'])) {
								esc_attr_e('Importer error! Unable to render import form.');
							} else {
								call_user_func($importer['get_book_list']['render_import_form']);
								echo('<div style="clear:both"></div><input type="submit" name="import-submit" class="import-submit button button-primary"  value="'.sprintf(esc_html__('Import','mybooktable')).'">');
							}
						} else {
							esc_attr_e('Importer error! Please ensure your MyBookTable Upgrade plugins are updated.');
						}
					?>
					</form>
					</div>
					</div>
				<?php				
			}
			return;
		}
	}
?>
	<div class="wrap mbt-import-page">
		<h2>Import Books</h2>
		<p>If you have books in another system, MyBookTable can import those into this site. To get started, choose a system to import from below:</p>
		<table class="widefat importers">
			<tbody>
				<?php
					$alternate = true;
					foreach($importers as $slug => $importer) {
						echo('<tr'.($alternate ? ' class="alternate"' : '').'>');
						if(empty($importer['disabled'])) {
							echo('<td class="import-system row-title"><a href="'.esc_url(admin_url('admin.php?page=mbt_import&mbt_import_type='.$slug)).'">'.esc_attr($importer['name']).'</a></td>');
						} else {
							echo('<td class="import-system row-title disabled">'.esc_attr($importer['name']).'</td>');
						}
						echo('<td class="desc">'.wp_kses_post($importer['desc']).((!empty($importer['disabled']) and is_string($importer['disabled'])) ? ' ('.esc_attr($importer['disabled']).')' : '').'</td>');
						echo('</tr>');
						$alternate = !$alternate;
					}
				?>
			</tbody>
		</table>
	</div>
<?php
}
