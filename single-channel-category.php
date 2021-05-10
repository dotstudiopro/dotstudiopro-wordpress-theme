<?php
global $dsp_theme_options, $client_token;

$is_user_subscribed = false;
if (class_exists('Dotstudiopro_Subscription') && $client_token) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
        $is_user_subscribed = true;
    }
}

get_header();

if (have_posts()) {

    while (have_posts()) : the_post();
        $theme_function = new Theme_Functions();
        $category_meta = get_post_meta(get_the_ID());
        $banner_image = ($dsp_theme_options['opt-category-poster-type'] == 'poster') ? $category_meta['cat_poster'][0] : $category_meta['cat_wallpaper'][0];
        $banner_visibility = ($dsp_theme_options['opt-category-poster-visible'] == 1) ? true : false;
        $banner = ($banner_image) ? $banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
        ?>

        <!-- Category Background and Information section start -->
        
        <div class="category inner-banner-bg">
            <?php if($banner_visibility ): ?>
                <div class="inner-banner-img"><img src="<?php echo $banner . '/1920/350'; ?>" alt="<?php echo (!empty($category_meta['cat_display_name'][0]) ? $category_meta['cat_display_name'][0] : get_the_title() ); ?>"></div>
            <?php endif; ?>
            <?php if ($dsp_theme_options['opt-category-poster-information'] == true) : ?>
                <div class="inner-banner-content_bg">
                    <div class="inner-banner-content row no-gutters">
                        <div class="custom-container container pt-5">
                            <?php if($category_meta['cat_display_name'][0]) : ?>
                                <h2><?php echo $category_meta['cat_display_name'][0]; ?></h2>
                            <?php else : ?>    
                                <h2><?php echo get_the_title(); ?></h2>
                            <?php endif; ?>
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Category Background and Information section start -->

        <!-- Channel Information section start -->
        <div class="custom-container container pt-5 pb-5">
            <div class="col-sm-12 other-categories">
                <div class="row">
                    <?php
                    $channels = $theme_function->home_page_other_carousel($post->post_name, $dsp_theme_options['opt-category-channel-poster-type'], 'category');
                    if ($channels) {
                        if( $dsp_theme_options['opt-category-image-size'] == '0' ) {
                            $width = filter_var($dsp_theme_options['opt-channel-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                            $height = filter_var($dsp_theme_options['opt-channel-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                        } else {
                            $width = filter_var($dsp_theme_options['opt-category-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                            $ratio_width = filter_var($dsp_theme_options['opt-category-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                            $ratio_height = filter_var($dsp_theme_options['opt-category-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                            $ratio = $ratio_height / $ratio_width;
                        }
                        $number_of_row = $dsp_theme_options['opt-display-row'];
                        $i = 0;
                        foreach ($channels as $channel) {
                            ?>
                            <div class="col-md-<?php echo $number_of_row; ?> p-2 channel-banner">
                                <a href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                    <div class="tooltippp" data-tooltip-content="#<?php echo 'tooltip_content_' . $i; ?>">
                                        <div class="clearfix">
                                            <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                                                <?php if (isset($channel['dspro_is_product']) && $channel['dspro_is_product'] == 1 && $is_user_subscribed == false): ?>
                                                    <div class="locked-channel"><i class="fa fa-lock"></i></div>
                                                <?php endif; ?>

                                                <?php if( $dsp_theme_options['opt-category-image-size'] == '1' ) :
                                                    $image_attributes = dsp_build_responsive_images( $channel['image'], $width, $ratio ); ?>

                                                    <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                                <?php else : ?>    
                                                    <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width . '/' . $height; ?>" class="lazy w-100" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                                <?php endif; ?>
                                                <div class="overlay">

                                                    <div class="watch_now"><a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $title = ($dsp_theme_options['opt-channel-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-channel-title-trim-word']) : $channel['title'];
                                    $description = ($dsp_theme_options['opt-channel-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-channel-description-trim-word']) : $channel['description'];
                                    ?>
                                    <?php if ($dsp_theme_options['opt-layout-channel-slider-content'] == 1): ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </a>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-channel-slider-content'] == 2): ?>
                                        <div class="tooltip_templates">
                                            <span id="<?php echo 'tooltip_content_' . $i; ?>">
                                                <h4><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </span>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-channel-slider-content'] == 3):
                                        ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                                            </a>
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
