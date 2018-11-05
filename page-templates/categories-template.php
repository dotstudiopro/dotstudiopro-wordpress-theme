<?php
/**
 * Template Name: Categories Template
 * 
 * This template is used to display all the categories
 * @since 1.0.0
 */
global $dsp_theme_options;
get_header();
?>
<div class="container">
    <div class="row no-gutters">

        <?php
        $category_args = array(
            'post_type' => 'category',
            'posts_per_page' => -1,
        );

        $categories = new WP_Query($category_args);

        if ($categories->have_posts()) {
            foreach ($categories->posts as $category) {
                $category_meta = get_post_meta($category->ID);
                $category_banner = ($dsp_theme_options['opt-categories-poster-type'] == 'wallpaper') ? $category->cat_wallpaper : $category->cat_poster;
                $width = filter_var($dsp_theme_options['opt-categories-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                $height = filter_var($dsp_theme_options['opt-categories-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                $banner = ($category_banner) ? $category_banner : 'https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b';
                $number_of_row = $dsp_theme_options['opt-display-categories-row'];
                ?>
                <div class="col-md-<?php echo $number_of_row; ?> p-2">
                    <a href="<?php echo get_permalink($category->ID); ?>" title="<?php echo $category->post_title; ?>">
                        <div class="holder">
                            <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width . '/' . $height ?>" class="lazy" data-src="<?php echo $banner . '/' . $width . '/' . $height; ?>"> 
                            <?php if ($dsp_theme_options['opt-categories-title'] == true): ?>
                                <h3><?php echo $category->post_title; ?></h3>
                            <?php endif; ?>
                        </div>
                    </a>    
                </div>
                <?php
            }
        }
        ?>
    </div><!-- container -->
</div><!-- no-gutters -->
<?php get_footer(); ?>