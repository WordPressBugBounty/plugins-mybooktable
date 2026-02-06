<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="mbt-book-meta">
	<?php mbt_the_book_authors_list(); ?>
	<?php mbt_the_book_series_list(); ?>
	<?php mbt_the_book_genres_list(); ?>
	<?php mbt_the_book_tags_list(); ?>
</div>