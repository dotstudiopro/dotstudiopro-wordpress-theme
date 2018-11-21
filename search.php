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
<div class="custom-container container mb-5 pt-5">
    <div class="row no-gutters">
        <h3 class="page-title"><?php printf(__('Search Results for: %s', 'twentyfifteen'), get_search_query()); ?></h3>
    </div>
        <?php if (have_posts()) : ?>
        <div class="row">
            <?php while (have_posts()) : the_post(); ?>
                <div class="col-md-4 p-2">
                    <a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
                        <div class="holder">
                            <img src="<?php echo get_search_image($post->ID); ?>" class="lazy">
                            <div class='title-holder'>
                                <h3><?php echo get_the_title(); ?></h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
            </div>
            <div class="pagination-links row">
            <?php
            the_posts_pagination(array(
                'prev_text' => '<span class="screen-reader-text">' . __('Previous page', 'dotstudio-pro') . '</span>',
                'next_text' => '<span class="screen-reader-text">' . __('Next page', 'dotstudio-pro') . '</span>' ,
                'before_page_number' => '<span class="meta-nav screen-reader-text"> </span>',
                'screen_reader_text' => ''
            ));
            ?>
        </div>
        <?php else : ?>
            <h4><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></h4>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
