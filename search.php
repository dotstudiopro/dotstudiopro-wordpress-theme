<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
get_header();
?>
<div class="container">
    <div class="row">
        <h1 class="page-title"><?php printf(__('Search Results for: %s', 'twentyfifteen'), get_search_query()); ?></h1>
    </div>
    <div class="row">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="col-md-4 p-2">
                    <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
                        <div class="holder">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.jpg" class="lazy"> 
                            <h3><?php echo get_the_title(); ?></h3>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
            <?php
            the_posts_pagination(array(
                'prev_text' => twentyseventeen_get_svg(array('icon' => 'arrow-left')) . '<span class="screen-reader-text">' . __('Previous page', 'twentyseventeen') . '</span>',
                'next_text' => '<span class="screen-reader-text">' . __('Next page', 'twentyseventeen') . '</span>' . twentyseventeen_get_svg(array('icon' => 'arrow-right')),
                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'twentyseventeen') . ' </span>',
            ));
            ?>
        <?php else : ?>
            <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
