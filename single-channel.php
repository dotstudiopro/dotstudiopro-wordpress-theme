<?php
global $dsp_theme_options;
get_header();

if (have_posts()) {

    while (have_posts()) : the_post();

        $theme_function = new Theme_Functions();
        $channel_meta = get_post_meta(get_the_ID());
        $childchannels = $theme_function->is_child_channels(get_the_ID());
        $channel_banner_image = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlisgt_poster'][0];
        $banner = ($channel_banner_image) ? $channel_banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
        ?>

        <!-- Channel Banner image section start -->
        <div class="chnl-bg">
            <img src="<?php echo $banner . '/1920/450'; ?>" alt="<?php echo get_the_title(); ?>">
            <div class="chnl-content row no-gutters">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6"></div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
                    <h1><?php echo get_the_title(); ?></h1>
                    <p><?php the_content(); ?></p>
                </div>
            </div>
        </div>
        <!-- Channel Banner image section end -->


        <div class="container">
            <div class="col-sm-12 other-categories">
                <?php
                if (!$childchannels) {
                    $videos = $theme_function->show_videos($post, 'other_carousel');
                    $cnt = 0;
                    if ($videos) {
                        ?>
                        <!-- Single Channel Video section start -->
                        <div class="col-sm-12 no-gutters">
                            <h2 class="post-title"><?php echo get_the_title(); ?></h2>
                            <?php
                            $class = 'home-carousel' . $cnt;
                            $class_array[] = $class;
                            $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                            $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                            include(locate_template('page-templates/templates-part/channel-videos.php'));
                            ?>

                        </div>
                        <!-- Single Channel Video section end -->
                        <?php
                    }
                } else {
                    $cnt = 0;
                    foreach ($childchannels as $channel) {
                        $single_channel = get_page_by_path($channel, OBJECT, 'channel');
                        $videos = $theme_function->show_videos($single_channel, 'other_carousel');
                        if ($videos) {
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="col-sm-12 no-gutters">
                                <h2 class="post-title"><?php echo $single_channel->post_title; ?></h2>
                                <?php
                                $class = 'home-carousel' . $cnt;
                                $class_array[] = $class;
                                $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                include(locate_template('page-templates/templates-part/channel-videos.php'));
                                ?>
                            </div>
                            <!-- Single Channel Video section end -->
                            <?php
                            $cnt++;
                        }
                    }
                }

                // Display Recomendation section
                if ($dsp_theme_options['opt-related-section'] == 1) {
                    if ($dsp_theme_options['opt-related-option'] == 'channel') {
                        $type = 'channel';
                        $related_id = get_post_meta(get_the_ID(), 'dspro_channel_id', true);
                    } else {
                        $type = 'video';
                        $related_id = $theme_function->first_video_id(get_the_ID());
                    }
                    include(locate_template('page-templates/templates-part/related-content.php'));
                    array_push($class_array, 'related_content');
                }

                $theme_function->slick_init_options($class_array, 'video');
                ?>
            </div> <!-- other-categories -->  
        </div><!-- container -->
        <?php
    endwhile;
}
?>

<?php get_footer(); ?>
