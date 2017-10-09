<?php get_header();

?>
<div id="main">
   <div class="home-content" style="background-color:#000;">
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      
      <?php simple_boostrap_display_post(false); ?>
          
      <?php endwhile; ?> 
      <?php endif; ?> 
  </div>

	
</div><!--main-->

<?php get_footer();?>
