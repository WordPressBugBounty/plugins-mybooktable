<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

mbt_start_template_context('excerpt');
do_action('mbt_book_excerpt_content');
mbt_end_template_context();