<?php

function mbt_breadcrumbs_init() {
	add_action('mbt_before_single_book', 'mbt_do_page_breadcrumbs', 20);
	add_action('mbt_before_book_archive', 'mbt_do_page_breadcrumbs', 20);
	add_filter('woo_breadcrumbs_trail', 'mbt_integrate_woo_breadcrumbs');
	add_filter('genesis_page_crumb', 'mbt_integrate_genesis_breadcrumb_archive', 20, 2);
}
add_action('mbt_init', 'mbt_breadcrumbs_init');

function mbt_integrate_woo_breadcrumbs($trail) {
	global $mbt_archive_query;
	if(is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') or is_tax('mbt_tag') || (mbt_is_archive_query() && ($mbt_archive_query->is_tax('mbt_author') or $mbt_archive_query->is_tax('mbt_genre') or $mbt_archive_query->is_tax('mbt_series') or $mbt_archive_query->is_tax('mbt_tag')))) {
		$page_link = '<a href="'.mbt_get_booktable_url().'">'.mbt_get_product_name().'</a>';
		array_splice($trail, 1, count($trail) - 1, array($page_link, $trail['trail_end']));
	}
	return $trail;
}

function mbt_integrate_genesis_breadcrumb_archive($crumb, $args) {
	global $mbt_archive_query;
	if(is_tax('mbt_author') or is_tax('mbt_genre') or is_tax('mbt_series') or is_tax('mbt_tag') || (mbt_is_archive_query() && ($mbt_archive_query->is_tax('mbt_author') or $mbt_archive_query->is_tax('mbt_genre') or $mbt_archive_query->is_tax('mbt_series') or $mbt_archive_query->is_tax('mbt_tag')))) {
		$crumb = '<a href="'.mbt_get_booktable_url().'">'.mbt_get_product_name().'</a>'.$args['sep'].$crumb;
	}
	return $crumb;
}

function mbt_do_page_breadcrumbs() {
	$show_breadcrumbs = apply_filters('mbt_show_page_breadcrumbs', mbt_get_setting('enable_breadcrumbs') and !mbt_has_template_context('shortcode'));
	if($show_breadcrumbs) { mbt_the_breadcrumbs(); }
}

function mbt_get_breadcrumbs($delimiter = '') {
	$delimiter = empty($delimiter) ? ' &gt; ' : $delimiter;
	$delimiter = '<span class="mbt-breadcrumbs-delimiter">'.$delimiter.'</span>';
	$output = '<a href="'.get_site_url().'">'.__('Home', 'mybooktable').'</a>';

	if(is_singular('mbt_book')) {
		global $post;
		$output .= $delimiter.'<a href="'.mbt_get_booktable_url().'">'.mbt_get_product_name().'</a>'.$delimiter.'<a href="'.get_permalink().'">'.$post->post_title.'</a>';
	} else if(is_tax('mbt_author') or is_tax('mbt_series') or is_tax('mbt_genre') or is_tax('mbt_tag')) {
		$output .= $delimiter.'<a href="'.mbt_get_booktable_url().'">'.mbt_get_product_name().'</a>'.$delimiter.'<a href="'.get_term_link(get_queried_object()).'">'.get_queried_object()->name.'</a>';
	} else if(is_post_type_archive('mbt_book') or mbt_is_booktable_page()) {
		$output .= $delimiter.'<a href="'.mbt_get_booktable_url().'">'.mbt_get_product_name().'</a>';
	}

	return apply_filters('mbt_get_breadcrumbs', empty($output) ? '' : '<div class="mbt-breadcrumbs">'.$output.'</div>');
}
function mbt_the_breadcrumbs($delimiter = '') {
	echo(wp_kses_post(mbt_get_breadcrumbs($delimiter))); 
}