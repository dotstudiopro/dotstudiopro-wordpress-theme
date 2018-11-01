<?php
global $dsp_theme_options;
get_header();

if (have_posts()) {

    while (have_posts()) : the_post();
        $theme_function = new Theme_Functions();
        $category_meta = get_post_meta(get_the_ID());
        $banner_image = ($dsp_theme_options['opt-category-poster-type'] == 'poster') ? $category_meta['cat_poster'][0] : $category_meta['cat_wallpaper'][0];
        $banner = ($banner_image) ? $banner_image : 'https://picsum.photos/';
        ?>

        <!-- Category Background and Information section start -->
        <div class="chnl-bg">
            <img src="<?php echo $banner . '/1920/450'; ?>" alt="<?php echo get_the_title(); ?>">
            <?php if ($dsp_theme_options['opt-category-poster-information'] == true) : ?>
                <div class="chnl-content row no-gutters">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6"></div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                        <h1><?php echo get_the_title(); ?></h1>
                        <p><?php the_content(); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Category Background and Information section start -->

        <!-- Channel Information section start -->
        <div class="container">
            <div class="col-sm-12 other-categories">
                <div class="row">
                    <?php
                    $channels = $theme_function->home_page_other_carousel($post->post_name, $dsp_theme_options['opt-category-channel-poster-type']);
                    if ($channels) {
                        $width = filter_var($dsp_theme_options['opt-channel-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                        $height = filter_var($dsp_theme_options['opt-channel-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                        $i = 0;
                        foreach ($channels as $channel) {
                            ?>
                            <div class="col-md-3 p-2 channel-banner">
                                <a href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                    <div class="tooltippp" data-tooltip-content="#<?php echo 'tooltip_content_' .$i; ?>">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/placeholder.jpg" class="lazy" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                    </div>
                                    <?php
                                    $title = ($dsp_theme_options['opt-channel-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-title-trim-word'], '...') : $channel['title'];
                                    $description = ($dsp_theme_options['opt-channel-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-description-trim-word'], '...') : $channel['description'];
                                    ?>
                                    <?php if ($dsp_theme_options['opt-layout-channel-slider-content'] == 1): ?>
                                        <div class="slide_content">
                                            <h6><?php echo $title; ?></h6>
                                            <p><?php echo $description; ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="tooltip_templates">
                                            <span id="<?php echo 'tooltip_content_' . $i; ?>">
                                                <h4><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php
                            $i++;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- Channel Information section start -->
        <?php
    endwhile;
}
?>

<?php get_footer(); ?>