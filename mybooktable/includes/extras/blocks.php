<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function mbt_blocks_init() {
	if(function_exists('register_block_type')) {
		add_action('init', 'mbt_register_blocks');
		add_action('enqueue_block_editor_assets', 'mbt_register_block_assets');
		add_filter('block_categories', 'mbt_add_block_category');
	}
}
add_action('mbt_init', 'mbt_blocks_init');

function mbt_add_block_category($categories) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'mybooktable',
				'title' => __('MyBookTable', 'mybooktable'),
				'icon'  => 'mybooktable',
			),
		)
	);
}

function mbt_register_blocks() {
	register_block_type('mybooktable/all-books', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/book-series', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/book-genre', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/book-tag', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/book-author', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/single-book', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
	register_block_type('mybooktable/term-list', array('editor_script' => 'mbt-blocks', 'editor_style' => 'mbt-blocks'));
}

function mbt_register_block_assets() {
	wp_register_script(
		'mbt-blocks',
		plugins_url('js/blocks.js', dirname(dirname(__FILE__))),
		array('wp-api-fetch', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-data', 'wp-element', 'wp-editor', 'wp-i18n', 'wp-url', 'lodash'),
		MBT_VERSION,
		true
	);
	wp_register_style('mbt-blocks', plugins_url('css/blocks.css', dirname(dirname(__FILE__))), array('wp-edit-blocks'), MBT_VERSION, 'all');
}
