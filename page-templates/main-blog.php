<?php
/**
 * Template Name: Main Blog
 */
get_header();
?>
<div class="custom-container container blog-container">
    <div class="row">
        <div class="col-md-9 col-sm-12">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            // Enforce 15 posts per page; will eventually make this a Theme Option choice
            $args = array('posts_per_page' => 15, 'paged' => $paged);
            // Update the query
            query_posts($args);
            ?>
            <?php if (have_posts()) : ?>

                <?php
                while (have_posts()) : the_post();
                    $post_date = strtotime(get_the_date());
                    ?>
                    <div class='blog-post'>
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class='blog-image'><img class='img img-responsive' src='<?php the_post_thumbnail_url('large'); ?>' /></div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class='blog-title'><h3><?php the_title(); ?></h3></div>
                                <div class='blog-author'><span class='blog-by'>By</span> <?php the_author_meta('display_name'); ?></div>
                                <div class='blog-date'>
                                    <div class='blog-two-digit-date'><?php echo date("d", $post_date); ?></div>
                                    <div class='blog-month'><?php echo date("M", $post_date); ?></div>
                                </div>
                                <div class='blog-excerpt'>
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="nav-previous alignleft"><?php previous_posts_link('Older posts'); ?></div>
                <div class="nav-next alignright"><?php next_posts_link('Newer posts'); ?></div>

            <?php else : ?>
                <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
            <?php endif; ?>
        </div>

        <div class="col-md-3 col-sm-12">
            <div class="blog-categories-container">
                <h3 class='blog-categories-header'>Categories</h3>
                <ul>
                    <?php dsp_get_category_list_lis() ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
