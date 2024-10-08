<?php

function mbt_shortcodes_init() {
	add_shortcode('mybooktable', 'mbt_mybooktable_shortcode');
	add_filter('authormedia_get_shortcodes', 'mbt_add_authormedia_shortcodes');
}
add_action('mbt_init', 'mbt_shortcodes_init');



/*---------------------------------------------------------*/
/* Shortcode Function                                      */
/*---------------------------------------------------------*/

function mbt_mybooktable_shortcode($attrs) {
	global $wp_query, $posts, $post, $id;

	if(mbt_has_template_context('shortcode')) { return ''; }

	$output = '';
	if(!empty($attrs['list'])) {
		if($attrs['list'] == 'authors') {
			$tax = 'mbt_author';
		} else if($attrs['list'] == 'series') {
			$tax = 'mbt_series';
		} else if($attrs['list'] == 'genres') {
			$tax = 'mbt_genre';
		} else if($attrs['list'] == 'tags') {
			$tax = 'mbt_tag';
		} else {
			return '';
		}

		$display = empty($attrs['display']) ? '' : $attrs['display'];

		if($display == "simple" || $display == "bar") {
			$output .= '<ul class="mbt-taxonomy-list '.$display.'">';

			$terms = get_terms($tax);
			foreach($terms as $term) {
				$link = get_term_link($term);
				$name = $term->name;

				$output .= '<li class="mbt-taxonomy-item"><a href="'.$link.'">'.$name.'</a></li>';
			}

			$output .= '	<div style="clear:both;"></div>';
			$output .= '</ul>';
		} else {
			$output .= '<div id="mbt-container">';
			$output .= '	<div class="mbt-taxonomy-listing">';

			$terms = get_terms($tax);
			foreach($terms as $term) {
				$link = get_term_link($term);
				$name = $term->name;
				$desc = $term->description;
				$img = mbt_get_taxonomy_image($term->taxonomy, $term->term_id);

				$output .= '<div class="mbt-taxonomy">';
				$output .= '	<div class="mbt-taxonomy-image">';
				$output .= '		<a href="'.$link.'"><img class="mbt-taxonomy-image" src="'.$img.'"></a>';
				$output .= '	</div>';
				$output .= '	<div class="mbt-taxonomy-right">';
				$output .= '		<h1 class="mbt-taxonomy-title"><a href="'.$link.'">'.$name.'</a></h1>';
				if(!empty($desc)) { $output .= '		<div class="mbt-taxonomy-description">'.$desc.'</div>'; }
				$output .= '	</div>';
				$output .= '	<div style="clear:both;"></div>';
				$output .= '</div>';

			}

			$output .= '	</div>';
			$output .= '</div>';
		}
	} else {
		$max_books = empty($attrs['max_books']) ? -1 : intval($attrs['max_books']);

		$mbt_shortcode_old_id = $id;
		$mbt_shortcode_old_post = $post;
		$mbt_shortcode_old_posts = $posts;
		$mbt_shortcode_old_wp_query = $wp_query;
		if(!empty($attrs['book'])) {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'name' => $attrs['book']));
		} else if(!empty($attrs['author'])) {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_author' => $attrs['author'], 'orderby' => 'menu_order', 'posts_per_page' => $max_books));
		} else if(!empty($attrs['series'])) {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_series' => $attrs['series'], 'orderby' => 'meta_value_num', 'meta_key' => 'mbt_series_order', 'order' => 'ASC', 'posts_per_page' => $max_books));
		} else if(!empty($attrs['genre'])) {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_genre' => $attrs['genre'], 'orderby' => 'menu_order', 'posts_per_page' => $max_books));
		} else if(!empty($attrs['tag'])) {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'mbt_tag' => $attrs['tag'], 'orderby' => 'menu_order', 'posts_per_page' => $max_books));
		} else {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'orderby' => 'menu_order', 'posts_per_page' => $max_books));
		}
		$wp_query->max_num_pages = 1;
		$post = empty($wp_query->post) ? null : $wp_query->post;
		$posts = $wp_query->posts;

		mbt_enqueue_frontend_scripts();
		ob_start();

		mbt_start_template_context('shortcode');
		if(!empty($wp_query->posts) and $wp_query->is_singular('mbt_book')) {
			if(!empty($attrs['display']) and $attrs['display'] === 'summary') {
				mbt_start_template_context('excerpt');
				echo('<div id="mbt-container">');
				do_action('mbt_book_excerpt_content');
				echo('</div>');
				mbt_end_template_context();
			} else if(!empty($attrs['display']) and $attrs['display'] === 'buybuttons') {
				mbt_start_template_context('shortcode-summary');
				?>
				<div id="mbt-container">
					<div class="<?php mbt_the_book_class(); ?>">
						<?php mbt_the_buybuttons(false, (!empty($attrs['buybutton_shadowbox']) and $attrs['buybutton_shadowbox'] === 'true') ? true : null); ?>
					</div>
				</div>
				<?php
				mbt_end_template_context();
			} else if(!empty($attrs['display']) and $attrs['display'] === 'cover+buybuttons') {
				mbt_start_template_context('shortcode-cover-buybuttons');
				?>
				<div id="mbt-container">
					<div class="<?php mbt_the_book_class(); ?>">
						<?php mbt_the_book_image(); ?>
						<div class="mbt-book-right">
							<?php mbt_the_buybuttons(false, (!empty($attrs['buybutton_shadowbox']) and $attrs['buybutton_shadowbox'] === 'true') ? true : null); ?>
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
				<?php
				mbt_end_template_context();
			} else {
				$display_mode = empty($attrs['display_mode']) ? mbt_get_book_display_mode($post->ID) : $attrs['display_mode'];
				if(!mbt_book_display_mode_supports($display_mode, 'embedding')) { $display_mode = mbt_get_default_book_display_mode(); }
				mbt_start_template_context('single');
				mbt_start_template_display_mode($display_mode);
				?> <div id="mbt-container"> <?php
				do_action('mbt_single_book_'.$display_mode.'_content');
				?> </div> <?php
				mbt_end_template_display_mode();
				mbt_end_template_context();
			}
		} else {
			if(!empty($attrs['header']) and $attrs['header'] == 'hidden') { remove_action('mbt_book_archive_header', 'mbt_do_book_archive_header'); }
			if(!empty($attrs['gridview'])) { add_filter('mbtpro2_is_gridview_active', $attrs['gridview'] == 'true' ? '__return_true' : '__return_false', 100); }
			mbt_start_template_context('archive');
			?> <div id="mbt-container"> <?php
			do_action('mbt_book_archive_content');
			?> </div> <?php
			mbt_end_template_context();
			if(!empty($attrs['gridview'])) { remove_filter('mbtpro2_is_gridview_active', $attrs['gridview'] == 'true' ? '__return_true' : '__return_false', 100); }
		}
		mbt_end_template_context();

		$output = ob_get_contents();
		ob_end_clean();

		$wp_query = $mbt_shortcode_old_wp_query;
		$posts = $mbt_shortcode_old_posts;
		$post = $mbt_shortcode_old_post;
		$id = $mbt_shortcode_old_id;
	}

	return $output;
}



/*---------------------------------------------------------*/
/* Shortcode Inserter Information                          */
/*---------------------------------------------------------*/

function mbt_add_authormedia_shortcodes($shortcodes) {
	function mbt_get_taxonomy_names($tax) {
		$names = array();
		$terms = get_terms($tax);
		foreach($terms as $term) {
			$names[$term->slug] = $term->name;
		}
		return $names;
	}

	$books = array();
	$books_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
	foreach($books_query->posts as $book) {
		$books[$book->post_name] = $book->post_title;
	}

	//Add default shortcodes
	$shortcodes['mybooktable'] = array('name' => 'MyBookTable', 'shortcodes' => array(
		'mybooktable' => array(
			'title'			=> __('All Books', 'mybooktable'),
			'description'	=> __('List all your books in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'gridview'	=> array(
					'title'			=> 'Force Grid View Display?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> 'Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)'
				),
				'header'	=> array(
					'title'			=> __('Header', 'mybooktable'),
					'description'	=> 'Shows the book table title and description above the book listing',
					'type'			=> 'dropdown',
					'choices'		=> array('show' => 'Shown', 'hidden' => 'Hidden')
				),
				'max_books'	=> array(
					'title'			=> __('Max Books to Display', 'mybooktable'),
					'description'	=> 'Sets the maximum number of books that will be shown',
					'type'			=> 'number'
				)
			)
		),
		'mybooktable-series' => array(
			'title'			=> __('All Books in Series', 'mybooktable'),
			'description'	=> __('List all the books in a given series in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'series'	=> array(
					'title'			=> __('Series', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> mbt_get_taxonomy_names('mbt_series')
				),
				'gridview'	=> array(
					'title'			=> 'Force Grid View Display?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> 'Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)'
				),
				'header'	=> array(
					'title'			=> __('Header', 'mybooktable'),
					'description'	=> 'Shows the taxonomy title and description above the book listing',
					'type'			=> 'dropdown',
					'choices'		=> array('show' => 'Shown', 'hidden' => 'Hidden')
				),
				'max_books'	=> array(
					'title'			=> __('Max Books to Display', 'mybooktable'),
					'description'	=> 'Sets the maximum number of books that will be shown',
					'type'			=> 'number'
				)
			)
		),
		'mybooktable-genre' => array(
			'title'			=> __('All Books in Genre', 'mybooktable'),
			'description'	=> __('List all the books in a given genre in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'genre'	=> array(
					'title'			=> __('Genre', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> mbt_get_taxonomy_names('mbt_genre')
				),
				'gridview'	=> array(
					'title'			=> 'Force Grid View Display?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> 'Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)'
				),
				'header'	=> array(
					'title'			=> __('Header', 'mybooktable'),
					'description'	=> 'Shows the taxonomy title and description above the book listing',
					'type'			=> 'dropdown',
					'choices'		=> array('show' => 'Shown', 'hidden' => 'Hidden')
				),
				'max_books'	=> array(
					'title'			=> __('Max Books to Display', 'mybooktable'),
					'description'	=> 'Sets the maximum number of books that will be shown',
					'type'			=> 'number'
				)
			)
		),
		'mybooktable-tag' => array(
			'title'			=> __('All Books with Tag', 'mybooktable'),
			'description'	=> __('List all the books with a given tag in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'tag'	=> array(
					'title'			=> __('Tag', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> mbt_get_taxonomy_names('mbt_tag')
				),
				'gridview'	=> array(
					'title'			=> 'Force Grid View Display?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> 'Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)'
				),
				'header'	=> array(
					'title'			=> __('Header', 'mybooktable'),
					'description'	=> 'Shows the taxonomy title and description above the book listing',
					'type'			=> 'dropdown',
					'choices'		=> array('show' => 'Shown', 'hidden' => 'Hidden')
				),
				'max_books'	=> array(
					'title'			=> __('Max Books to Display', 'mybooktable'),
					'description'	=> 'Sets the maximum number of books that will be shown',
					'type'			=> 'number'
				)
			)
		),
		'mybooktable-author' => array(
			'title'			=> __('All Books by Author', 'mybooktable'),
			'description'	=> __('List all the books written by a given author in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'author'	=> array(
					'title'			=> __('Author', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> mbt_get_taxonomy_names('mbt_author')
				),
				'gridview'	=> array(
					'title'			=> 'Force Grid View Display?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> 'Shows book covers in a responsive grid. (requires Professional or Developer Upgrade)'
				),
				'header'	=> array(
					'title'			=> __('Header', 'mybooktable'),
					'description'	=> 'Shows the taxonomy title and description above the book listing',
					'type'			=> 'dropdown',
					'choices'		=> array('show' => 'Shown', 'hidden' => 'Hidden')
				),
				'max_books'	=> array(
					'title'			=> __('Max Books to Display', 'mybooktable'),
					'description'	=> 'Sets the maximum number of books that will be shown',
					'type'			=> 'number'
				)
			)
		),
		'mybooktable-book' => array(
			'title'			=> __('Single Book', 'mybooktable'),
			'description'	=> __('Show a given book in an embedded book listing.', 'mybooktable'),
			'settings'		=> array(
				'book'	=> array(
					'title'			=> __('Book', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> $books
				),
				'display'	=> array(
					'title'			=> __('Display Style', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> array("default" => __("Default", 'mybooktable'), "summary" => __("Summary", 'mybooktable'), "buybuttons" => __("Buy Buttons", 'mybooktable'), "cover+buybuttons" => __("Cover and Buy Buttons", 'mybooktable'))
				),
				'buybutton_shadowbox' => array(
					'title'			=> 'Force Shadow Box for Buy Buttons?',
					'default'		=> false,
					'type'			=> 'checkbox',
					'description'	=> ''
				)
			)
		),
		'mybooktable-list' => array(
			'title'			=> __('All Terms in Taxonomy', 'mybooktable'),
			'description'	=> __('This allows you to display all of the different items in a MyBookTable taxonomy.', 'mybooktable'),
			'settings'		=> array(
				'list'	=> array(
					'title'			=> __('Taxonomy', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> array("series" => __("Series", 'mybooktable'), "genres" => __("Genres", 'mybooktable'), "tags" => __("Tags", 'mybooktable'), "authors" => __("Authors", 'mybooktable'))
				),
				'display'	=> array(
					'title'			=> __('Display Style', 'mybooktable'),
					'description'	=> '',
					'type'			=> 'dropdown',
					'choices'		=> array("listing" => __("Listing", 'mybooktable'), "bar" => __("Menu Bar", 'mybooktable'), "simple" => __("Simple", 'mybooktable'))
				)
			)
		)
	));

	return $shortcodes;
}



/*---------------------------------------------------------*/
/* Author Media Shortcode Inserter                         */
/*---------------------------------------------------------*/

if(!function_exists('load_authormedia_shortcode_inserter')) {
	add_action('init', 'load_authormedia_shortcode_inserter');
	function load_authormedia_shortcode_inserter() {
		remove_action('admin_init', 'authormedia_setup_shortcode_inserter');
		add_action('admin_init', apply_filters('authormedia_shortcode_inserter_setup_func', '__return_null'));
	}
}

add_filter('authormedia_shortcode_inserter_setup_func', 'mbt_authormedia_shortcode_inserter_setup_func', 1);
function mbt_authormedia_shortcode_inserter_setup_func() {
	return 'mbt_setup_authormedia_shortcode_inserter';
}

function mbt_setup_authormedia_shortcode_inserter() {
	if((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing') == 'true') {
		if( isset($_SERVER['PHP_SELF']) && in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))) {
			add_filter('media_buttons', 'mbt_authormedia_shortcode_inserter_button', 30);
			add_action('admin_footer', 'mbt_authormedia_shortcode_inserter_form');
		}
	}
}

function mbt_authormedia_shortcode_inserter_button($buttons) {
	echo '<a href="#TB_inline?width=480&inlineId=authormedia-insert-shortcode" class="thickbox button authormedia-insert-shortcode-button"><span class="authormedia-insert-shortcode-icon"></span>Insert Shortcode</a>';
}

function mbt_authormedia_shortcode_inserter_form() {
	$shortcode_sections = apply_filters('authormedia_get_shortcodes', array());
	?>
	<script type="text/javascript">
		function authormedia_insert_shortcode() {
			var active_item = jQuery('.authormedia-shortcode-section .shortcode-menu-item.active');
			var shortcode_full = active_item.data('shortcode');
			if(shortcode_full == '') {
				alert('Please select a shortcode', 'authormedia');
				return;
			}

			shortcode_tag = shortcode_full.split('-')[0];

			var attrs = {};
			jQuery('#authormedia_shortcode_form_' + shortcode_full + ' .authormedia_shortcode_field').each(function(){
				if('checkbox' == jQuery(this).attr('type')) {
					attrs[jQuery(this).attr('name')] = jQuery(this).is(':checked');
				} else if('radio' == jQuery(this).attr('type')) {
					if(jQuery(this).is(':checked')) { attrs[jQuery(this).attr('name')] = jQuery(this).val(); }
				} else if(jQuery(this).val()) {
					attrs[jQuery(this).attr('name')] = jQuery(this).val();
				}
			});

			if(window.authormedia_shortcode_form_events) {
				window.authormedia_shortcode_form_events.trigger('pre-insert', shortcode_full, attrs);
				window.authormedia_shortcode_form_events.trigger('pre-insert:'+shortcode_full, attrs);
			}

			if(attrs["content"] > "") {
				var setcontent = attrs["content"];
				delete(attrs["content"]);
				shortcode = new wp.shortcode({
					tag: shortcode_tag,
					attrs: attrs,
					type: 'closed',
					content: setcontent
				});
			} else {
				shortcode = new wp.shortcode({
					tag: shortcode_tag,
					attrs: attrs,
					type: 'single'
				});
			}

			if(window.authormedia_shortcode_form_events) {
				window.authormedia_shortcode_form_events.trigger('insert', shortcode_full, shortcode);
				window.authormedia_shortcode_form_events.trigger('insert:'+shortcode_full, shortcode);
			}

			if(window.send_to_editor) {
				window.send_to_editor(shortcode.string());
			}
		}

		jQuery(document).ready(function() {
			jQuery('.shortcode-modal-close').on('click', function(e){
				e.preventDefault();
				tb_remove();
			});

			jQuery('.authormedia-shortcode-section .shortcode-menu-item').on('click', function() {
				jQuery('.authormedia-shortcode-section .shortcode-menu-item').removeClass('active');
				jQuery(this).addClass('active');
				jQuery('.authormedia_shortcode_form_atts').css('display', 'none');
				jQuery('#authormedia_shortcode_form_' + jQuery(this).data('shortcode')).css('display', 'block');
			});

			jQuery('.authormedia-shortcode-section-nav .nav-tab-wrapper a').on('click', function() {
				jQuery('.authormedia-shortcode-section-nav .nav-tab-wrapper a').removeClass('nav-tab-active');
				jQuery(this).addClass('nav-tab-active');
				jQuery('.authormedia-shortcode-section').css('display', 'none');
				jQuery('#authormedia_shortcode_section_' + jQuery(this).attr('data-shortcode-section') ).css('display', 'block');
			});
			jQuery('.authormedia-shortcode-section-nav .nav-tab-wrapper a')[0].click();

			window.authormedia_shortcode_form_events = _.extend({}, Backbone.Events);
		});
	</script>

	<div id="authormedia-insert-shortcode" style="display:none;">
		<div class="authormedia-insert-shortcode-container">
			<a class="media-modal-close shortcode-modal-close" href="#" title="<?php esc_attr_e('Close', 'authormedia'); ?>">
				<span class="media-modal-icon"></span>
			</a>
			<div class="authormedia-shortcode-section-nav">
				<h2 class="nav-tab-wrapper">
					<?php
						foreach($shortcode_sections as $section_id => $section) {
							 echo('<a href="#" class="nav-tab" data-shortcode-section="'.esc_attr($section_id).'">'.esc_attr($section['name']).'</a>');
						}
					?>
				</h2>
			</div>

			<?php foreach($shortcode_sections as $section_id => $section) { ?>
				<?php $shortcodes = $section['shortcodes']; ?>
				<div class="media-modal-content authormedia-shortcode-section" id="authormedia_shortcode_section_<?php echo(esc_attr($section_id)); ?>">
					<div class="media-frame wp-core-ui">
						<div class="media-frame-menu">
							<div class="media-menu">
								<?php
									foreach ( $shortcodes as $shortcode => $atts ) {
										echo '<a href="#" class="media-menu-item shortcode-menu-item" data-shortcode="' . esc_attr( $shortcode ) . '">' . esc_html( $atts['title'] ) . "</a>";
									}
								?>
							</div>
						</div>
						<div class="media-frame-title">
							<h1><?php esc_attr_e('Insert a Shortcode', 'authormedia'); ?></h1>
						</div>
						<div class="media-frame-router"></div>
						<div class="media-frame-content">
							<div id="authormedia_shortcode_form_intro" class="authormedia_shortcode_form_atts">
								<?php esc_attr_e('To get started, select a shortcode from the list on the left.', 'authormedia'); ?>
							</div>
							<?php foreach ( $shortcodes as $shortcode => $atts ): ?>
							<div id="authormedia_shortcode_form_<?php echo esc_attr($shortcode); ?>" class="authormedia_shortcode_form_atts" style="display:none">
								<?php if ( !empty($atts['description']) ) { ?>
									<div class="authormedia_shortcode_description">
										<?php echo esc_html($atts['description']); ?>
									</div>
								<?php } ?>
								<?php if ( empty($atts['settings']) ) { ?>
									<div style="margin:1em">This shortcode has no options, you can insert it directly.</div>
								<?php } else { ?>
									<?php foreach ( $atts['settings'] as $setting => $params ) {
										echo '<div style="margin:1em">';
										switch ( $params['type'] ) {
											case 'dropdown':
												global $_wp_additional_image_sizes;
												if ( ! empty($params['title']) ) echo wp_kses_post("<label for='authormedia_{$shortcode}_field_$setting'>$params[title]</label><br>");
												if ( ! empty($params['choices']) ) {
													echo wp_kses_post("<select class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting' style='max-width:440px;'>");
													foreach ( $params['choices'] as $slug => $name ) {
														echo wp_kses_post("<option value='$slug'>$name</option>");
													}
													echo "</select>";
												}
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'thumbsize':
												global $_wp_additional_image_sizes;
												if ( ! empty($params['title']) ) echo wp_kses_post("<label for='authormedia_{$shortcode}_field_$setting'>$params[title]</label><br>");
												echo wp_kses_post("<select class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting'>");
												echo "<option value=''>(default)</option>";
												foreach ( $_wp_additional_image_sizes as $name => $atts ) {
													echo wp_kses_post("<option value='$name'>$name ($atts[width] x $atts[height])</option>");
												}
												echo wp_kses_post("</select>");
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'checkboxes':
												#!! we need to output a list of checkboxes and on saving, comma-delimit them
												break;
											case 'checkbox':
												echo wp_kses_post("<input type='checkbox' class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting' ".(empty($params['default']) ? '' : 'checked="checked"').">");
												if ( ! empty($params['title']) ) echo wp_kses_post(" <label for='authormedia_{$shortcode}_field_$setting'>$params[title]</label>");
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'radio':
												if ( ! empty($params['title']) ) echo esc_attr($params['title']);
												if ( ! empty($params['choices']) ) {
													echo '<ul style="margin-left:2em">';
													foreach( $params['choices'] as $key => $value ) {
														echo wp_kses_post("<li><input type='radio' class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_{$setting}_{$key}' name='$setting' value='$key'>");
														echo wp_kses_post(" <label for='authormedia_{$shortcode}_field_{$setting}_{$key}'>$value</label></li>");
													}
													echo '</ul>';
												}
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'content':
											case 'textarea':
												if ( ! empty($params['title']) ) {
													echo wp_kses_post("<label for='authormedia_{$shortcode}_field_$setting'>$params[title]");
													if ( ! empty($params['default']) ) echo wp_kses_post(" <em>(default: $params[default])</em>");
													echo "</label><br>";
												}
												echo wp_kses_post("<textarea class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting' rows='5' cols='40'></textarea>");
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'text':
												if ( ! empty($params['title']) ) {
													echo wp_kses_post("<label for='authormedia_{$shortcode}_field_$setting'>$params[title]");
													if ( ! empty($params['default']) ) echo wp_kses_post(" <em>(default: $params[default])</em>");
													echo "</label><br>";
												}
												echo wp_kses_post("<input type='text' class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting'>");
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case 'number':
												if ( ! empty($params['title']) ) {
													echo wp_kses_post("<label for='authormedia_{$shortcode}_field_$setting'>$params[title]");
													if ( ! empty($params['default']) ) echo wp_kses_post(" <em>(default: $params[default])</em>");
													echo "</label><br>";
												}
												echo wp_kses_post("<input type='text' class='authormedia_shortcode_field' id='authormedia_{$shortcode}_field_$setting' name='$setting'>");
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
												break;
											case '':
											default:
												if ( ! empty($params['title']) ) {
													echo wp_kses_post("<label for='authormedia_shortcode_field_$setting'>$params[title]");
													if ( ! empty($params['default']) ) echo wp_kses_post(" <em>(default: $params[default])</em>");
													echo "</label><br>";
												}
												echo wp_kses_post('input type="' . $params['type'] . '" name="' . $setting . '"');
												if ( ! empty($params['description']) ) echo wp_kses_post('<div class="description">' . $params['description'] . '</div>');
										}
										echo '</div>';
									} ?>
								<?php } ?>
								<?php if(!empty($atts['pre-insert'])) { ?>
									<script type="text/javascript">
										jQuery(document).ready(function() { window.authormedia_shortcode_form_events.on('pre-insert:<?php echo(wp_kses_post($shortcode)); ?>', function(attrs) { <?php echo(wp_kses_post($atts['pre-insert']));?> }); });
									</script>
								<?php } ?>
							</div>
							<?php endforeach; ?>
						</div>
						<div class="media-frame-toolbar">
							<div class="media-toolbar">
							<div class="media-toolbar-secondary">
								<a class="button media-button button-large button-cancel" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php esc_attr_e("Cancel", 'authormedia'); ?></a>
							</div>
							<div class="media-toolbar-primary">
								<input type="button" class="button media-button button-primary button-large button-insert" value="<?php esc_attr_e('Insert Shortcode', 'authormedia'); ?>" onclick="authormedia_insert_shortcode();"/>
							</div>
						</div></div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php
}
