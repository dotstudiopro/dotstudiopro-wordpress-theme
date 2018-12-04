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
<div class="custom-container container">
    <div class="row no-gutters categories-page pt-5 pb-5">

        <?php
        $category_args = array(
            'post_type' => 'channel-category',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'meta_key' => 'weight',
            'orderby' => 'meta_value_num',
        );

        $categories = new WP_Query($category_args);
        $theme_function = new Theme_Functions();

        if ($categories->have_posts()) {
            foreach ($categories->posts as $category) {
                $category_meta = get_post_meta($category->ID);
                $category_banner = ($dsp_theme_options['opt-categories-poster-type'] == 'wallpaper') ? $category->cat_wallpaper : $category->cat_poster;
                $width = filter_var($dsp_theme_options['opt-categories-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                $height = filter_var($dsp_theme_options['opt-categories-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                $banner = ($category_banner) ? $category_banner : 'https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b';
                $number_of_row = $dsp_theme_options['opt-display-categories-row'];
                $category_listing_option = $dsp_theme_options['opt-cateogry-listing-option'];
                if ($category_listing_option == 'category-listing-page'):
                    $link = get_permalink($category->ID);
                else:
                    $category_channel = $theme_function->get_category_channels($category->post_name);
                    if (!empty($category_channel)) {
                        $video = $theme_function->show_videos(array_values($category_channel)[0], 'categories-template');
                    $link = $video[0]['url'];
                    } else {
                        $link = get_permalink($category->ID);
                    }
                endif;
                ?>
                <div class="col-md-<?php echo $number_of_row; ?> p-4">
                    <a href="<?php echo $link; ?>" title="<?php echo $category->post_title; ?>">
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