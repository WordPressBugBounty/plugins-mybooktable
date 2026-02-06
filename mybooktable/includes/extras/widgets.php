<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function mbt_register_widgets() {
	register_widget("MBT_Featured_Book");
	register_widget("MBT_Taxonomies");
	add_action('admin_enqueue_scripts', 'mbt_enqueue_widget_admin_js');
}
add_action('widgets_init', 'mbt_register_widgets');

function mbt_enqueue_widget_admin_js() {
	global $pagenow; if($pagenow != 'widgets.php' and $pagenow != 'customize.php') { return; }

	wp_enqueue_script("mbt-widgets", plugins_url('js/widgets.js', dirname(dirname(__FILE__))), 'jquery', MBT_VERSION, true);
}

/*---------------------------------------------------------*/
/* Featured Books Widget                                   */
/*---------------------------------------------------------*/

class MBT_Featured_Book extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'mbt_featured_book', 'description' => __("Displays featured or random books.", 'mybooktable'));
		parent::__construct('mbt_featured_book', __('MyBookTable Featured Books', 'mybooktable'), $widget_ops);
		$this->defaultargs = array('title' => __('Featured Books', 'mybooktable'), 'selectmode' => 'by_date', 'featured_books' => array(), 'image_size' => 'medium', 'num_books' => 1, 'show_blurb' => true, 'use_shadowbox' => true);
	}

	function widget($args, $instance) {
		extract(wp_parse_args($instance, $this->defaultargs));

		$num_books = intval($num_books);
		if($num_books > 10 or $num_books < 1) { $num_books = 1; }
		if(!empty($featured_book)) { $featured_books = array((int)$featured_book); }

		echo(wp_kses_post($args['before_widget']));
		if($title) { echo(wp_kses_post($args['before_title'].$title.$args['after_title'])); }

		if($selectmode == 'manual_select' and !empty($featured_books)) {
			$books = array();
			foreach($featured_books as $featured_book) {
				$new_book = get_post($featured_book);
				if($new_book) { $books[] = $new_book; }
			}
		} else if($selectmode == 'random') {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'posts_per_page' => -1));
			$books = array();
			$keys = array_rand($wp_query->posts, $num_books);
			if(!is_array($keys)) { $keys = array($keys); }
			foreach($keys as $key) {
				$books[] = $wp_query->posts[$key];
			}
		} else {
			$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'orderby' => 'date', 'posts_per_page' => $num_books));
			$books = $wp_query->posts;
		}

		if(!empty($books)) {
			mbt_enqueue_frontend_scripts();
			?> <div class="mbt-featured-book-widget"> <?php
			foreach($books as $book) {
				$permalink = get_permalink($book->ID);
				mbt_start_template_context('featured-book-widget');
				?>
					<div class="mbt-featured-book-widget-book">
						<h2 class="mbt-book-title widget-title"><a href="<?php echo(esc_url($permalink)); ?>"><?php echo(esc_attr(get_the_title($book->ID))); ?></a></h2>
						<div class="mbt-book-images"><a href="<?php echo(esc_url($permalink)); ?>"><?php echo(wp_kses_post(mbt_get_book_image($book->ID, array('class' => $image_size, 'size' => '25vw')))); ?></a></div>
						<?php if($show_blurb) { ?><div class="mbt-book-blurb"><?php echo(wp_kses_post(mbt_get_book_blurb($book->ID, true))); ?></div><?php } ?>
						<?php echo(wp_kses_post(mbt_get_buybuttons($book->ID, true, !empty($use_shadowbox)))); ?>
					</div>
				<?php
				mbt_end_template_context();
			}
			?> </div> <?php
		}

		echo('<div style="clear:both;"></div>');
		echo(wp_kses_post($args['after_widget']));
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags($new_instance['title']);
		$instance['selectmode'] = wp_strip_all_tags($new_instance['selectmode']);
		$instance['image_size'] = $new_instance['image_size'];
		$instance['num_books'] = intval($new_instance['num_books']);
		$instance['show_blurb'] = (bool)$new_instance['show_blurb'];
		$instance['use_shadowbox'] = (bool)$new_instance['use_shadowbox'];
		$instance['featured_books'] = (array)json_decode($new_instance['featured_books']);
		unset($instance['featured_book']);
		return $instance;
	}

	function form($instance) {
		extract(wp_parse_args($instance, $this->defaultargs));
		if(!empty($featured_book)) { $featured_books = array((int)$featured_book); }
		?>

		<div class="mbt-featured-book-widget-editor" onmouseover="mbt_initialize_featured_book_widget_editor(this);">
		<p>
				<label><?php esc_attr_e('Title', 'mybooktable'); ?>: <input type="text" name="<?php echo(esc_attr($this->get_field_name('title'))); ?>" value="<?php echo(esc_attr($title)); ?>"></label>
			</p>
			<p>
				<label><?php esc_attr_e('Book image size:', 'mybooktable'); ?></label><br>
				<?php $sizes = array('small' =>__('Small', 'mybooktable'), 'medium' => __('Medium', 'mybooktable'), 'large' => __('Large', 'mybooktable')); ?>
				<?php foreach($sizes as $size => $size_name) { ?>
					<input type="radio" name="<?php echo(esc_attr($this->get_field_name('image_size'))); ?>" id="<?php echo(esc_attr($this->get_field_id('image_size').' '.$size)); ?>" value="<?php echo(esc_attr($size)); ?>" <?php echo($image_size == $size ? ' checked' : ''); ?> >
					<label for="<?php echo(esc_attr($this->get_field_id('image_size').' '.$size)); ?>"><?php echo(esc_attr($size_name)); ?></label><br>
				<?php } ?>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_blurb')); ?>" name="<?php echo(esc_attr($this->get_field_name('show_blurb'))); ?>"<?php checked($show_blurb); ?> />
				<label for="<?php echo(esc_attr($this->get_field_id('show_blurb'))); ?>"><?php esc_attr_e('Show book blurb', 'mybooktable'); ?></label>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('use_shadowbox')); ?>" name="<?php echo(esc_attr($this->get_field_name('use_shadowbox'))); ?>"<?php checked($use_shadowbox); ?> />
				<label for="<?php echo(esc_attr($this->get_field_id('use_shadowbox'))); ?>"><?php esc_attr_e('Use shadow box for Buy Buttons', 'mybooktable'); ?></label>
			</p>
			<p>
				<label for="<?php echo(esc_attr($this->get_field_id('selectmode'))); ?>"><?php esc_attr_e('Choose how to select the featured books:', 'mybooktable'); ?></label>
				<select class="mbt_featured_book_selectmode" name="<?php echo(esc_attr($this->get_field_name('selectmode'))); ?>" id="<?php echo(esc_attr($this->get_field_id('selectmode'))); ?>">
					<option value="by_date"<?php selected($selectmode, 'by_date'); ?>><?php esc_attr_e('Most Recent Books', 'mybooktable'); ?></option>
					<option value="manual_select"<?php selected($selectmode, 'manual_select'); ?>><?php esc_attr_e('Choose Manually', 'mybooktable'); ?></option>
					<option value="random"<?php selected($selectmode, 'random'); ?>><?php esc_attr_e('Random Books', 'mybooktable'); ?></option>
				</select>
			</p>
			<div class="mbt-featured-book-manual-selector" <?php echo($selectmode === 'manual_select' ? '' : 'style="display:none"'); ?>>
				<label for="mbt-featured-book-selector"><?php esc_attr_e('Select Books:', 'mybooktable'); ?></label></br>
				<select class="mbt-featured-book-selector">
					<option value=""><?php esc_attr_e('-- Choose One --', 'mybooktable'); ?></option>
					<?php
						$wp_query = new WP_Query(array('post_type' => 'mbt_book', 'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => -1));
						if(!empty($wp_query->posts)) {
							foreach($wp_query->posts as $book) {
								$shorttitle = substr($book->post_title, 0, 25).(strlen($book->post_title) > 25 ? '...' : '');
								echo '<option value="'.esc_attr($book->ID).'">'.esc_attr($shorttitle).'</option>';
							}
						}
					?>
				</select>
				<input type="button" class="mbt-featured-book-adder button" value="<?php esc_attr_e('Add', 'mybooktable'); ?>" /><br>

				<?php
					echo('<ul class="mbt-featured-book-list">');
					foreach($featured_books as $featured_book) {
						$book = get_post($featured_book);
						if($book) {
							$shorttitle = substr($book->post_title, 0, 25).(strlen($book->post_title) > 25 ? '...' : '');
							echo('<li data-id="'.esc_attr($book->ID).'" class="mbt-book">'.esc_attr($shorttitle).'<a class="mbt-book-remover">X</a></li>');
						}
					}
					echo('</ul>');
				?>
				<input class="mbt-featured-books" id="<?php echo(esc_attr($this->get_field_id('featured_books'))); ?>" name="<?php echo(esc_attr($this->get_field_name('featured_books'))); ?>" type="hidden" value="<?php echo(wp_json_encode($featured_books)); ?>">
			</div>
			<div class="mbt-featured-book-options" <?php echo($selectmode !== 'manual_select' ? '' : 'style="display:none"'); 
				 $nbooks = intval($num_books ? $num_books : 1);
				 ?>>
				<p>
					<label><?php esc_attr_e('Number of Books:', 'mybooktable'); ?>
						<input type="number" name="<?php echo(esc_attr($this->get_field_name('num_books'))); ?>" value="<?php echo(esc_attr($nbooks)); ?>"  min="1" max="10">
					</label>
				</p>
			</div>
		</div>
		<?php
	}
}

/*---------------------------------------------------------*/
/* Taxonomy Widget                                         */
/*---------------------------------------------------------*/

class MBT_Taxonomies extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'mbt_taxonomies', 'description' => __("A list of Authors, Genres, Series, or Tags.", 'mybooktable'));
		parent::__construct('mbt_taxonomies', __('MyBookTable Taxonomy Widget', 'mybooktable'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args);

		$tax = empty($instance['tax']) ? 'mbt_author' : $instance['tax'];
		if($tax === 'mbt_genre') {
			$title = __('Genres', 'mybooktable');
		} else if($tax === 'mbt_series') {
			$title = __('Series', 'mybooktable');
		} else if($tax === 'mbt_tag') {
			$title = __('Tags', 'mybooktable');
		} else {
			$tax = 'mbt_author';
			$title = __('Authors', 'mybooktable');
		}
		if(!empty($instance['title'])) { $title = $instance['title']; }
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);
		$c = !empty($instance['count']) ? '1' : '0';

		echo(wp_kses_post($before_widget));
		if($title) { 
			$alltitle = $before_title.$title.$after_title;
			echo(wp_kses_post($alltitle)); 
		}

		$args = array('orderby' => 'name', 'title_li' => '', 'show_count' => $c, 'taxonomy' => $tax);

		echo('<ul>');
		wp_list_categories(apply_filters('mbt_taxonomy_widget_args', $args));
		echo('</ul>');

		echo(wp_kses_post($after_widget));
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['tax'] = $new_instance['tax'];
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args((array)$instance, array('title' => ''));
		$count = isset($instance['count']) ? (bool)$instance['count'] : false;
		$tax = isset($instance['tax']) ? $instance['tax'] : '';

		?>
		<div class="mbt-taxonomies-widget-editor">
			<p>
				<label><?php esc_attr_e('Title', 'mybooktable'); ?>: <input type="text" name="<?php echo(wp_kses_post($this->get_field_name('title'))); ?>" value="<?php echo(wp_kses_post($instance['title'])); ?>"></label>
			</p>
			<p>
				<label for="<?php echo esc_attr($this->get_field_id('tax')); ?>"><?php esc_attr_e('Displayed taxonomy', 'mybooktable'); ?>:</label>
				<select name="<?php echo esc_attr($this->get_field_name('tax')); ?>" id="<?php echo esc_attr($this->get_field_id('tax')); ?>" class="widefat">
					<option value=""><?php esc_attr_e('-- Choose One --', 'mybooktable'); ?></option>
					<option value="mbt_author"<?php selected($tax, 'mbt_author'); ?>><?php esc_attr_e('Authors', 'mybooktable'); ?></option>
					<option value="mbt_genre"<?php selected($tax, 'mbt_genre'); ?>><?php esc_attr_e('Genres', 'mybooktable'); ?></option>
					<option value="mbt_series"<?php selected($tax, 'mbt_series'); ?>><?php esc_attr_e('Series', 'mybooktable'); ?></option>
					<option value="mbt_tag"<?php selected($tax, 'mbt_tag'); ?>><?php esc_attr_e('Tags', 'mybooktable'); ?></option>
				</select>
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked($count); ?> />
				<label for="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked($count); ?> />
				<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_attr_e('Show post counts', 'mybooktable'); ?></label>
			</p>
		</div>
		<?php
	}
}
