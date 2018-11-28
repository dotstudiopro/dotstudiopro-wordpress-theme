<?php
/**
 * Template Name: Full Width w/Container
 */
get_header();
?>
<div class="custom-container container">
	<div class="col-sm-12 blog-main">

	    <?php
	    if (have_posts()) {
	        while (have_posts()) : the_post();
	            ?>
	            <div class="blog-post">
	                <?php the_content(); ?>
	            </div><!-- /.blog-post -->
	            <?php
	        endwhile;
	    }
	    ?>

	</div><!-- /.blog-main -->
</div>
<?php get_footer(); ?>
