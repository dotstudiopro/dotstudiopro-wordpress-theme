<?php
global $dsp_theme_options, $client_token, $post;
$video_slug = '';
get_header();

if (have_posts()) {

    while (have_posts()) : the_post();

        $theme_function = new Theme_Functions();
        $channel_meta = get_post_meta(get_the_ID());

        // Code to check if user subscribe to watch this channel
        $dsp_api = new Dsp_External_Api_Request();
        $check_subscription_status = $dsp_api->check_subscription_status($client_token, $channel_meta['dspro_channel_id'][0]);
        if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked']))
            $channel_unlocked = false;
        else
            $channel_unlocked = true;

        $childchannels = $theme_function->is_child_channels(get_the_ID());
        $channel_banner_image = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
        $banner = ($channel_banner_image) ? $channel_banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
        ?>

        <!-- Channel Banner image section start -->
        <div class="chnl inner-banner-bg">
            <div class="inner-banner-img"><img src="<?php echo $banner . '/1920/650'; ?>" alt="<?php echo get_the_title(); ?>"></div>
            <div class="inner-banner-content_bg">
                <div class="inner-banner-content row no-gutters">
                    <h2><?php echo get_the_title(); ?></h2>
                    <p><?php the_content(); ?></p>
                    <?php if (empty($channel_unlocked)): ?>
                        <div class="subscribe_now mt-3">
                            <a href="/packages" class="btn btn-primary">Subscribe Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Channel Banner image section end -->


        <div class="custom-container container pb-5">
            <div class="row no-gutters other-categories">
                <?php
                if (!$childchannels) {
                    $videos = $theme_function->show_videos($post, 'other_carousel');
                    $cnt = 0;
                    if ($videos) {
                        ?>
                        <!-- Single Channel Video section start -->
                        <div class="col-sm-12 no-gutters pt-7">
                            <h3 class="post-title mb-5"><?php echo get_the_title(); ?></h3>
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
                    $p_channel_slug = $post->post_name;
                    $cnt = 0;
                    foreach ($childchannels as $channel) {
                        //$channel_unlocked = '';
                        $single_channel = get_page_by_path($channel, OBJECT, 'channel');
                        $videos = $theme_function->show_videos($single_channel, 'other_carousel', $p_channel_slug);

                        if ($videos) {
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="col-sm-12 no-gutters pt-7">
                                <h3 class="post-title mb-5"><?php echo $single_channel->post_title; ?></h3>
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
                    ?>
                    <div class="col-sm-12 no-gutters pt-7">
                        <?php
                        include(locate_template('page-templates/templates-part/related-content.php'));
                        ?>
                    </div>
                    <?php
                    //array_push($class_array, 'related_content');
                }
                $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
                $theme_function->slick_init_options('slick_carousel', $class_array, 'video');
                ?>
            </div> <!-- other-categories -->
        </div><!-- container -->
        <?php
    endwhile;
}
?>

<?php get_footer(); ?>
