<?php

function mbt_goodreads_init() {
	add_action('mbt_integrate_settings_render', 'mbt_goodreads_settings_render', 9);
	add_action('mbt_settings_save', 'mbt_goodreads_settings_save');
	add_action('wp_ajax_mbt_goodreads_developer_key_refresh', 'mbt_goodreads_developer_key_refresh_ajax');
	add_filter('mbt_reviews_types', 'mbt_add_goodreads_reviews_type');
}
add_action('mbt_init', 'mbt_goodreads_init');

function mbt_add_goodreads_reviews_type($reviews) {
	$dev_key = mbt_get_setting('goodreads_developer_key');
	$disabled = empty($dev_key) ? '<a href="'.admin_url('admin.php?page=mbt_settings&mbt_current_tab=5').'">'.__('You must input your GoodReads Developer Key', 'mybooktable').'</a>' : '';
	$reviews['goodreads'] = array(
		'name' => 'GoodReads Reviews',
		'callback' => 'mbt_get_goodreads_reviews',
		'book-check' => 'mbt_check_book_goodreads_reviews',
		'disabled' => $disabled,
	);
	return $reviews;
}

function mbt_goodreads_settings_save() {
	if(!isset($_REQUEST['mbt_goodreads_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_goodreads_nonce']), 'mbt_goodreads_nonce')) { return; }
	if(isset($_REQUEST['mbt_goodreads_developer_key'])) {
		mbt_update_setting('goodreads_developer_key',sanitize_text_field(wp_unslash($_REQUEST['mbt_goodreads_developer_key'])));
	}
}

if(!function_exists('mbt_goodreads_developer_key_refresh_ajax')) {
	function mbt_goodreads_developer_key_refresh_ajax() {
		if(!isset($_REQUEST['mbt_nonce']) || !wp_verify_nonce(sanitize_key($_REQUEST['mbt_nonce']), 'mbt-ajax-nonce')) { die(); }
		if(!current_user_can('manage_options')) { die(); }
		if(isset($_REQUEST['data'])){
			mbt_update_setting('goodreads_developer_key', sanitize_text_field(wp_unslash($_REQUEST['data'])));
		}
		echo(wp_kses_post(mbt_goodreads_developer_key_feedback()));
		die();
	}
}

if(!function_exists('mbt_goodreads_developer_key_feedback')) {
	function mbt_goodreads_developer_key_feedback() {
		$output = '';
		$goodreads_developer_key = mbt_get_setting("goodreads_developer_key");
		if(!empty($goodreads_developer_key)) {
			$raw_response = wp_remote_get('https://www.goodreads.com/book/isbn?format=json&isbn=9780618640157&key='.$goodreads_developer_key);
			if(!is_wp_error($raw_response) and 200 == wp_remote_retrieve_response_code($raw_response)) { $response = json_decode(wp_remote_retrieve_body($raw_response)); }
			if(!empty($response->reviews_widget)) {
				$output .= '<span class="mbt_admin_message_success">'.__('Valid Developer Key', 'mybooktable').'</span>';
			} else {
				$output .= '<span class="mbt_admin_message_failure">'.__('Invalid Developer Key', 'mybooktable').'</span>';
			}
		}
		return $output;
	}
}

function mbt_goodreads_settings_render() {
	$mbt_goodreads_nonce = wp_nonce_field('mbt_goodreads_nonce', 'mbt_goodreads_nonce');
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="mbt_goodreads_developer_key"><?php esc_attr_e('GoodReads', 'mybooktable'); ?></label></th>
				<td>
					<?php echo wp_kses_post($mbt_goodreads_nonce);?>
					<div class="mbt_feedback_above mbt_feedback"></div>
					<label for="mbt_goodreads_developer_key" class="mbt-integrate-label">Developer Key:</label>
					<input type="text" id="mbt_goodreads_developer_key" name="mbt_goodreads_developer_key" value="<?php echo(esc_attr(mbt_get_setting('goodreads_developer_key'))); ?>" class="regular-text">
					<div class="mbt_feedback_refresh mbt_feedback_refresh_initial" data-refresh-action="mbt_goodreads_developer_key_refresh" data-refresh-nonce="<?php echo(esc_attr(wp_create_nonce('mbt-ajax-nonce'))); ?>" data-element="mbt_goodreads_developer_key"></div>

					<p class="description"><?php 
						/* translators: %s: link to tutorial */
						echo sprintf(esc_html__('Insert your GoodReads Developer Key to %s on your book pages.', 'mybooktable'), 
						'<a href="'.esc_url(admin_url('admin.php?page=mbt_settings&mbt_current_tab=3')).'" target="_blank">enable GoodReads reviews</a>'); ?>
						<p><a href="https://www.authormedia.com/how-to-add-goodreads-book-reviews-to-mybooktable/" target="_blank"> <?php esc_attr_e('Learn how to get a GoodReads Developer Key', 'mybooktable'); ?></a></p>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}

function mbt_get_goodreads_reviews($post_id = 0) {
	if(empty($post_id)) { global $post; $post_id = $post->ID; }
	global $wp_version;

	$output = '';
	$key = mbt_get_setting('goodreads_developer_key');
	$isbn = get_post_meta($post_id, 'mbt_unique_id_isbn', true);
	if(!empty($key) and !empty($isbn)) {
		$query = apply_filters('mbt_goodreads_reviews_query', 'https://www.goodreads.com/book/isbn?format=json&isbn='.$isbn.'&key='.$key);
		$raw_response = wp_remote_get($query, array('timeout' => 3, 'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url')));
		if(!is_wp_error($raw_response) and 200 == wp_remote_retrieve_response_code($raw_response)) {
			$response = json_decode(wp_remote_retrieve_body($raw_response));
			$output = empty($response->reviews_widget) ? '' : $response->reviews_widget;
			$output = preg_replace("/<style>.*<\/style>/s", "", $output);
			$output = preg_replace("/http:\/\//s", "//", $output);
		}
	}
	return $output;
}

function mbt_check_book_goodreads_reviews($book_id) {
	$isbn = get_post_meta($book_id, 'mbt_unique_id_isbn', true);
	if(empty($isbn)) {
		return '<span class="mbt_admin_message_failure">'.__('No ISBN entered.', 'mybooktable').'</span>';
	} else {
		$matches = array();
		preg_match("/^([0-9][0-9\-]{8,}[0-9Xx])$/", $isbn, $matches);
		if(!empty($matches[1])) {
			$filtered_isbn = preg_replace("/[^0-9Xx]/", "", $isbn);
			return '<span class="mbt_admin_message_success">'.__('Valid ISBN', 'mybooktable').' <a href="https://www.isbnsearch.org/isbn/'.$filtered_isbn.'" target="_blank">'.__('(verify book)', 'mybooktable').'</a></span>';
		} else {
			return '<span class="mbt_admin_message_failure">'.__('Invalid ISBN', 'mybooktable').'</span>';
		}
	}
}
