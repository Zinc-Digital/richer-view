<?php
/**
 * Single project (e.g. /our-work/first-bus-uniform-catwalk/) – the page is built from blocks.
 */
get_header();
?>

<main class="page-wrapper">
<?php if(have_posts()): 
		while(have_posts()): the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile;
	endif; ?>


<?php get_footer();?>