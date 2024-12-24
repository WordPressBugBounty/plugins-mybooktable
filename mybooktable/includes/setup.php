<?php

/*---------------------------------------------------------*/
/* Check for Updates                                       */
/*---------------------------------------------------------*/

function mbt_update_check() {
	$version = mbt_get_setting('version');

	if(version_compare($version, '1.1.0') < 0) { mbt_update_1_1_0(); }
	if(version_compare($version, '1.1.3') < 0) { mbt_update_1_1_3(); }
	if(version_compare($version, '1.1.4') < 0) { mbt_update_1_1_4(); }
	if(version_compare($version, '1.2.7') < 0) { mbt_update_1_2_7(); }
	if(version_compare($version, '1.3.1') < 0) { mbt_update_1_3_1(); }
	if(version_compare($version, '1.3.8') < 0) { mbt_update_1_3_8(); }
	if(version_compare($version, '2.0.1') < 0) { mbt_update_2_0_1(); }
	if(version_compare($version, '2.0.4') < 0) { mbt_update_2_0_4(); }
	if(version_compare($version, '2.1.0') < 0) { mbt_update_2_1_0(); }
	if(version_compare($version, '2.2.0') < 0) { mbt_update_2_2_0(); }
	if(version_compare($version, '2.3.0') < 0) { mbt_update_2_3_0(); }
	if(version_compare($version, '3.0.0') < 0) { mbt_update_3_0_0(); }

	if($version !== MBT_VERSION) {
		mbt_update_setting('version', MBT_VERSION);
	}
}

function mbt_update_1_1_0() {
	if(mbt_get_setting('compatibility_mode') !== false) {
		mbt_update_setting('compatibility_mode', true);
	}
}

function mbt_update_1_1_3() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			$image_id = get_post_meta($book_id, '_thumbnail_id', true);
			$mbt_book_image_id = get_post_meta($book_id, 'mbt_book_image_id', true);
			if(empty($mbt_book_image_id) && !empty($image_id)) { update_post_meta($book_id, 'mbt_book_image_id', $image_id); }
		}
	}
}

function mbt_update_1_1_4() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			delete_post_meta($book_id, '_thumbnail_id');

			$buybuttons = get_post_meta($book_id, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				for($i = 0; $i < count($buybuttons); $i++)
				{
					if($buybuttons[$i]['type']) {
						$buybuttons[$i]['store'] = $buybuttons[$i]['type'];
						unset($buybuttons[$i]['type']);
					}
				}
			}
			update_post_meta($book_id, 'mbt_buybuttons', $buybuttons);
		}
	}
}

function mbt_update_1_2_7() {
	if(mbt_get_setting('enable_default_affiliates') !== false) {
		mbt_update_setting('enable_default_affiliates', true);
	}
}

function mbt_update_1_3_1() {
	//mbt_update_setting('help_page_email_subscribe_popup', 'show');
	mbt_update_setting('product_name', __("Books"));
	mbt_update_setting('product_slug', _x('books', 'URL slug', 'mybooktable'));
}

function mbt_update_1_3_8() {
	mbt_update_setting('domc_notice_text', esc_attr_e('Disclosure of Material Connection: Some of the links in the page above are "affiliate links." This means if you click on the link and purchase the item, I will receive an affiliate commission. I am disclosing this in accordance with the Federal Trade Commission\'s <a href="https://www.access.gpo.gov/nara/cfr/waisidx_03/16cfr255_03.html" target="_blank">16 CFR, Part 255</a>: "Guides Concerning the Use of Endorsements and Testimonials in Advertising."', 'mybooktable'));
}

function mbt_update_2_0_1() {
	mbt_verify_api_key();
}

function mbt_update_2_0_4() {
	mbt_update_setting('show_find_bookstore_buybuttons_shadowbox', true);
}

function mbt_update_2_1_0() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			$buybuttons = get_post_meta($book_id, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				for($i = 0; $i < count($buybuttons); $i++) {
					$buybuttons[$i]['display'] = ($buybuttons[$i]['display'] == 'text_only' or $buybuttons[$i]['display'] == 'text') ? 'text' : 'button';
				}
			}
			update_post_meta($book_id, 'mbt_buybuttons', $buybuttons);
		}
	}

	mbt_update_setting('buybutton_shadowbox', mbt_get_setting('enable_buybutton_shadowbox') ? 'all' : 'none');
}

function mbt_update_2_2_0() {
	global $wpdb;
	$books = $wpdb->get_col('SELECT ID FROM '.$wpdb->posts.' WHERE post_type = "mbt_book"');
	if(!empty($books)) {
		foreach($books as $book_id) {
			update_post_meta($book_id, 'mbt_unique_id_isbn', get_post_meta($book_id, 'mbt_unique_id', true));
		}
	}
}

function mbt_update_2_3_0() {
	$style_pack = mbt_get_setting('style_pack');
	if($style_pack == 'Default') { mbt_update_setting('style_pack', 'silver'); }
	if($style_pack == 'Golden') { mbt_update_setting('style_pack', 'golden'); }
	if($style_pack == 'Blue Flat') { mbt_update_setting('style_pack', 'blue_flat'); }
	if($style_pack == 'Gold Flat') { mbt_update_setting('style_pack', 'gold_flat'); }
	if($style_pack == 'Green Flat') { mbt_update_setting('style_pack', 'green_flat'); }
	if($style_pack == 'Grey Flat') { mbt_update_setting('style_pack', 'grey_flat'); }
	if($style_pack == 'Orange Flat') { mbt_update_setting('style_pack', 'orange_flat'); }
}

function mbt_update_3_0_0() {
	mbt_update_setting('show_about_author', true);
	mbt_update_setting('reviews_type', mbt_get_setting('reviews_box'));
	mbt_update_setting('enable_socialmedia_single_book', mbt_get_setting('enable_socialmedia_badges_single_book') or mbt_get_setting('enable_socialmedia_bar_single_book'));
	mbt_update_setting('enable_socialmedia_book_excerpt', mbt_get_setting('enable_socialmedia_badges_book_excerpt'));

	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	foreach ($books_query->posts as $book) {
		update_post_meta($book->ID, 'mbt_display_mode', 'storefront');

		if(get_post_meta($book->ID, 'mbt_unique_id_asin', true) == '') {
			$buybuttons = get_post_meta($book->ID, 'mbt_buybuttons', true);
			if(is_array($buybuttons) and !empty($buybuttons)) {
				foreach($buybuttons as $buybutton) {
					if($buybutton['store'] == 'amazon' or $buybutton['store'] == 'kindle') {
						$asin = mbt_get_amazon_AISN($buybutton['url']);
						if(!empty($asin)) { update_post_meta($book->ID, 'mbt_unique_id_asin', $asin); }
						break;
					}
				}
			}
		}
	}
}



/*---------------------------------------------------------*/
/* Rewrites Check                                          */
/*---------------------------------------------------------*/

function mbt_rewrites_check_init() {
	add_action('wp_loaded', 'mbt_rewrites_check', 999);
}
add_action('mbt_init', 'mbt_rewrites_check_init');

function mbt_rewrites_check() {
	if(!mbt_check_rewrites()) {
		flush_rewrite_rules();
		if(!mbt_check_rewrites()) { add_action('admin_notices', 'mbt_rewrites_check_admin_notice'); }
	}
}

function mbt_check_rewrites() {
	global $pagenow;
	$settings_updated = filter_input(INPUT_GET,'settings-updated');
	
	if($pagenow == 'options-permalink.php' and !empty($settings_updated)) { return true; }

	global $wp_rewrite;
	$rules = $wp_rewrite->wp_rewrite_rules();
	if(empty($rules) or !is_array($rules)) { return true; }

	$book_page_correct = false;
	$books = new WP_Query(array('post_type' => 'mbt_book', 'post_status' => 'publish', 'posts_per_page' => 1));
	if(empty($books->posts)) {
		$book_page_correct = true;
	} else {
		$book = $books->posts[0];
		$book_page_correct = mbt_get_rewrite($rules, get_permalink($book)) === 'index.php?mbt_book=$matches[1]&page=$matches[2]';
	}

	$archive_correct = mbt_get_rewrite($rules, get_post_type_archive_link('mbt_book')) === 'index.php?post_type=mbt_book';
	$genres_correct = mbt_check_tax_rewrites($rules, 'mbt_genre');
	$authors_correct = mbt_check_tax_rewrites($rules, 'mbt_author');
	$series_correct = mbt_check_tax_rewrites($rules, 'mbt_series');
	$tags_correct = mbt_check_tax_rewrites($rules, 'mbt_tag');

	return $archive_correct and $book_page_correct and $genres_correct and $authors_correct and $series_correct and $tags_correct;
}

function mbt_check_tax_rewrites($rules, $tax) {
	$terms = get_terms($tax);
	if(empty($terms)) { return true; }
	return mbt_get_rewrite($rules, get_term_link(reset($terms), $tax)) === 'index.php?'.$tax.'=$matches[1]';
}

function mbt_get_rewrite($rules, $url) {
	$parts = wp_parse_url(home_url('/'));
	$default_path = $parts['path'];
	$parts = wp_parse_url($url);
	$url = $parts['path'];
	$url = substr($url, strlen($default_path));

	foreach($rules as $match => $query) {
		if(preg_match("#^$match#", $url)) {
			return $query;
		}
	}
	return '';
}

function mbt_rewrites_check_admin_notice() {
	?>
	<div id="message" class="error">
		<p>
			<strong><?php esc_attr_e('MyBookTable Rewrites Error', 'mybooktable'); ?></strong> &#8211;
			<?php esc_attr_e('You have a plugin or theme that has post types or taxonomies that are conflicting with MyBookTable. MyBookTable pages will not display correctly.', 'mybooktable'); ?>
		</p>
	</div>
	<?php
}



/*---------------------------------------------------------*/
/* Admin notices                                           */
/*---------------------------------------------------------*/

function mbt_admin_notices_init() {
	add_action('admin_init', 'mbt_add_admin_notices', 20);
}
add_action('mbt_init', 'mbt_admin_notices_init');

function mbt_add_admin_notices() {
	$install_mbt = filter_input(INPUT_GET,'install_mbt');
	$skip_install_mbt = filter_input(INPUT_GET,'skip_install_mbt');
	$mbt_setup_default_affiliates = filter_input(INPUT_GET,'mbt_setup_default_affiliates');
	$mbt_finish_install = filter_input(INPUT_GET,'mbt_finish_install');
	$mbt_install_examples = filter_input(INPUT_GET,'mbt_install_examples');
	$mbt_add_booktable_page = filter_input(INPUT_GET,'mbt_add_booktable_page');
	$mbt_remove_booktable_page = filter_input(INPUT_GET,'mbt_remove_booktable_page');
	if(!mbt_get_setting('installed')) {
		if(isset($install_mbt)) {
			mbt_install();
			mbt_update_setting('installed', 'check_api_key');
		} elseif(isset($skip_install_mbt) || mbt_get_setting('booktable_page') != 0) {
			mbt_update_setting('installed', 'check_api_key');
		} else {
			add_action('admin_notices', 'mbt_admin_install_notice');
		}
	}
	if(mbt_get_setting('installed') == 'check_api_key') {
		if(!mbt_get_setting('api_key') and mbt_get_upgrade_plugin_exists(false)) {
			add_action('admin_notices', 'mbt_admin_setup_api_key_notice');
		} else {
			mbt_update_setting('installed', 'setup_default_affiliates');
		}
	}
	if(mbt_get_setting('installed') == 'setup_default_affiliates') {
		if(!mbt_get_setting('enable_default_affiliates') and mbt_get_upgrade() === false and !isset($mbt_setup_default_affiliates)) {
			add_action('admin_notices', 'mbt_admin_setup_default_affiliates_notice');
		} else {
			mbt_update_setting('installed', 'post_install');
		}
	}
	if(mbt_get_setting('installed') == 'post_install') {
		if(isset($mbt_finish_install)) {
			do_action('mbt_installed');
			mbt_update_setting('installed', 'done');
		} else {
			add_action('admin_notices', 'mbt_admin_installed_notice');
		}
	}
	if(mbt_get_setting('installed') == 'done' or is_int(mbt_get_setting('installed'))) {
		if(!mbt_get_setting('api_key') and mbt_get_upgrade_plugin_exists(false)) {
			add_action('admin_notices', 'mbt_admin_setup_api_key_notice');
		} else if(mbt_get_upgrade() and !mbt_get_upgrade_plugin_exists()) {
			add_action('admin_notices', 'mbt_admin_enable_upgrade_notice');
		} 
	}

	if(isset($mbt_install_examples)) {
		mbt_install_examples();
	}

	if(isset($mbt_add_booktable_page)) {
        if((isset($_REQUEST['nonce_add_page']) && wp_verify_nonce(sanitize_key($_REQUEST['nonce_add_page']),'add_page_nonce'))) {
            mbt_add_booktable_page();
        }
	}
    if(isset($mbt_remove_booktable_page)) {
        if((isset($_REQUEST['nonce_remove_page']) && wp_verify_nonce(sanitize_key($_REQUEST['nonce_remove_page']),'remove_page_nonce'))) {
            mbt_update_setting('booktable_page', 0);
        }
    }
}

function mbt_admin_install_notice() {
	?>
	<div class="mbt-admin-notice">
		<h4><?php echo('<strong>'.esc_attr_e('Welcome to MyBookTable', 'mybooktable').'</strong> &#8211; '.esc_attr_e("You're almost ready to start promoting your books", 'mybooktable').' :)'); ?></h4>
		<a class="notice-button primary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings&install_mbt=1'))); ?>"><?php esc_attr_e('Install MyBookTable Pages', 'mybooktable'); ?></a>
		<a class="notice-button secondary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings&skip_install_mbt=1'))); ?>"><?php esc_attr_e('Skip setup', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_installed_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.esc_attr_e('MyBookTable has been installed', 'mybooktable').'</strong> &#8211; '.esc_attr_e("You're ready to start promoting your books", 'mybooktable').' :)'); ?></h4>
		<a class="notice-button primary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_help&mbt_finish_install=1'))); ?>"><?php esc_attr_e('Show Me How', 'mybooktable'); ?></a>
		<a class="notice-button secondary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings&mbt_finish_install=1'))); ?>"><?php esc_attr_e('Thanks, I Got This', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_setup_api_key_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.esc_attr_e('Setup your License Key', 'mybooktable').'</strong> &#8211; '.esc_attr_e('MyBookTable needs your License key to enable enhanced features', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings'))); ?>" ><?php esc_attr_e('Go To Settings', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_setup_default_affiliates_notice() {
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.esc_attr_e('Setup your Amazon and Barnes &amp; Noble Buttons', 'mybooktable').'</strong> &#8211; '.esc_attr_e('MyBookTable needs your input to enable these features', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(esc_url(admin_url('admin.php?page=mbt_settings&mbt_setup_default_affiliates=1'))); ?>"><?php esc_attr_e('Go To Settings', 'mybooktable'); ?></a>
	</div>
	<?php
}

function mbt_admin_enable_upgrade_notice() {
	$subpage = filter_input(INPUT_GET,'subpage');
	if(isset($subpage) and $subpage == 'mbt_get_upgrade_page') { return; }
	$upgradepage_url = 
wp_nonce_url(esc_url(admin_url('admin.php?page=mbt_dashboard&subpage=mbt_get_upgrade_page'),'dashboard_nonce','nonce_dashboard'));		
	?>
	<div id="message" class="mbt-admin-notice">
		<h4><?php echo('<strong>'.esc_attr_e('Enable your Upgrade', 'mybooktable').'</strong> &#8211; '.esc_attr_e('Download or Activate your MyBookTable Upgrade plugin to enable your advanced features!', 'mybooktable')); ?></h4>
		<a class="notice-button primary" href="<?php echo(esc_attr($upgradepage_url)); ?>"><?php esc_attr_e('Enable', 'mybooktable'); ?></a>
	</div>
	<?php
}

/*---------------------------------------------------------*/
/* Installation Functions                                  */
/*---------------------------------------------------------*/

function mbt_install() {
	mbt_add_booktable_page();
	mbt_install_examples();
}

function mbt_add_booktable_page() {
	if(mbt_get_setting('booktable_page') <= 0 or !get_page(mbt_get_setting('booktable_page'))) {					
		$post_id = wp_insert_post(array(
			'post_title' => esc_attr_e('Book Table', 'mybooktable'),
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'page'
		));
		mbt_update_setting("booktable_page", $post_id);
	}
}

function mbt_install_examples() {
	if(!mbt_get_setting('installed_examples')) {
		include("examples.php");
		mbt_update_setting('installed_examples', true);
	}
}


