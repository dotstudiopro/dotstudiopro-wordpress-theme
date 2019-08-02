<?php
global $dsp_theme_options, $client_token, $post, $wp;
$video_slug = '';
get_header();

if (have_posts()) {

    while (have_posts()) : the_post();

        $theme_function = new Theme_Functions();
        $channel_meta = get_post_meta(get_the_ID());
        // Code to check if user subscribe to watch this channel
        $dsp_api = new Dsp_External_Api_Request();
        $country_code = 'IN'; //$dsp_api->get_country();
        $dspro_channel_geo = unserialize($channel_meta['dspro_channel_geo'][0]);
        if($country_code && !in_array($country_code, $dspro_channel_geo) && !empty($dspro_channel_geo)){
            ?>
            <div class="custom-container container pb-5">
                <div class="row no-gutters other-categories text-center">
                    <h4 class="p-4 w-100">The owner of this content has made it unavailable in your country.</h4>
                    <h4 class="p-4 w-100">Please explore our other selections from <a href="/home-page" title="Explore">here</a></h4>
                </div>
            </div>
            <?php
            break;
        }

        if (isset($channel_meta['chnl_categories'][0])) {
            $categories = array_filter(explode(',', $channel_meta['chnl_categories'][0]));
            $chnl_title = get_the_title();

            // Condition to check platform is web true or not for this channel category
            $plateform_web = false;
            foreach ($categories as $channel_cat) {
                $args = array('name' => $channel_cat, 'post_type' => 'channel-category');
                $cache_key = "single_video_categories_" . $channel_cat;
                $slug_query = $theme_function->query_categories_posts($args, $cache_key);
                if ($slug_query->have_posts()) {
                    $plateform_web = true;
                    break;
                }
            }
        } else {
            $plateform_web = true;
        }

        if ($plateform_web) {
            $check_subscription_status = $dsp_api->check_subscription_status($client_token, $channel_meta['dspro_channel_id'][0]);
            if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked']))
                $parant_channel_unlocked = false;
            else
                $parant_channel_unlocked = true;

            $childchannels = $theme_function->is_child_channels(get_the_ID());
            $channel_banner_image = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
            $banner = ($channel_banner_image) ? $channel_banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
            $first_child_id = get_post(get_the_ID(), OBJECT);
			$p_channel_id = 0;
            if (!empty($first_child_id)) {
                $p_channel_meta = get_post_meta($first_child_id->ID);
                $p_channel_id = $p_channel_meta['chnl_id'][0];
            }
            if ($childchannels) {
                $first_child_id = get_page_by_path($childchannels[0], OBJECT, 'channel');
                $channel_videos = $theme_function->get_channel_videos($first_child_id->ID);
                $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
                $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/' . $first_child_id->post_name . '/video/' . $videoSlug;
            } else {
                $channel_videos = $theme_function->get_channel_videos(get_the_ID());
                $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
                $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/video/' . $videoSlug;
            }
            ?>

            <!-- Channel Banner image or video section start -->
            <div class="chnl inner-banner-bg">
                <!-- Channel Banner image section start -->
                <div class="chanl_background_img">
                    <div class="inner-banner-img"><img src="<?php echo $banner . '/1920/900'; ?>" alt="<?php echo get_the_title(); ?>"></div>
                    <div class="inner-banner-content_bg">
                        <div class="inner-banner-content row no-gutters">
                            <?php if($dsp_theme_options['opt-channel-poster-logo-title'] == 'logo' && !empty($channel_meta['chnl_logo'][0])){?>
                                <img class="title_logo pb-3" src="<?php echo $channel_meta['chnl_logo'][0]. '/400'; ?>" alt="<?php echo get_the_title(); ?>">
                            <?php }else{ ?>
                            <h2><?php echo get_the_title(); ?></h2>
                            <?php }?>
                            <p class="w-100 pb-3"><?php echo dsp_get_channel_publication_meta(get_the_ID()); ?></p>
                            <?php the_content(); ?>
                            <div class="subscribe_now mt-3">
                                <?php if (empty($parant_channel_unlocked)): ?>
                                    <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
                                <?php else: ?>
                                    <a href="<?php echo $first_video_url; ?>" class="btn btn-secondary btn-ds-secondary">Watch Now</a>
                                <?php endif; ?>
                                <?php if (class_exists('WP_Auth0')) { ?>
                                    <div class="my_list_button">
                                        <?php
                                        if ($first_child_id) {
                                            if ($client_token) {
                                                $channel_id = get_post_meta($first_child_id->ID, 'chnl_id', true);
                                                $obj = new Dsp_External_Api_Request();
                                                $list_channel = $obj->get_user_watchlist($client_token);
                                                $in_list = array();
                                                if (!is_wp_error($list_channel) && $list_channel['channels'] && !empty($list_channel['channels'])) {
                                                    foreach ($list_channel['channels'] as $ch) {
                                                        $in_list[] = $ch['_id'];
                                                    }
                                                }
                                                if (in_array($channel_id, $in_list)) { // $channel->isChannelInList($utoken)
                                                    ?>
                                                    <a href="/my-list" class="btn btn-danger"><i class="fa fa-minus-circle"></i> Remove from My List</a>
                                                <?php } else { ?>
                                                    <a href="#" class="btn btn-secondary btn-ds-secondary manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-secondary btn-ds-secondary"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Channel Banner image section end -->

                <!-- Display video insted of background image if user is ideal section start-->
                <?php
                $trailer_id = '';
                $video_id = $theme_function->first_video_id(get_the_ID());
                if (!empty($video_id)) {
                    $video = $dsp_api->get_video_by_id($video_id);
                    if (!is_wp_error($video) && !empty($video)) {
                        if (isset($video['teaser_trailer']) && !empty($video['teaser_trailer'])) {
                            $trailer_id = $video['teaser_trailer']['_id'];
                            $company_id = isset($video['company_id']) ? $video['company_id'] : '';
                            $player_color = (get_option('dsp_video_color_field')) ? get_option('dsp_video_color_field') : '#000000';
                            $mute_on_load = (get_option('dsp_video_muteload_field')) ? 'true' : 'false';
                            $settings = [];
                            $settings[] = 'companykey=' . $company_id;
                            $settings[] = 'skin=' . ltrim($player_color, "#");
                            $settings[] = 'autostart=true';
                            $settings[] = 'muteonstart=' . $mute_on_load;
                            $settings[] = 'disableads=true';
                            $settings[] = 'disablecontrolbar=true';
                            $settings[] = 'loopplayback=true';
                            $settings[] = 'enablesharing=false';
                            $player_setting = '?targetelm=.player&' . implode('&', $settings);
                            ?>
                            <div id="video-overlay" class="channel-teaser">
                                <div class="player" data-video_id="<?php echo $trailer_id; ?>"></div>  
                                <div class="inner-banner-content_bg channel-teaser-info">
                                    <div class="inner-banner-content row no-gutters">
                                        <?php if($dsp_theme_options['opt-channel-poster-logo-title'] == 'logo' && !empty($channel_meta['chnl_logo'][0])){?>
                                            <img class="title_logo pb-3" src="<?php echo $channel_meta['chnl_logo'][0]. '/400'; ?>" alt="<?php echo get_the_title(); ?>">
                                        <?php }else{ ?>
                                        <h2><?php echo get_the_title(); ?></h2>
                                        <?php }?>
                                        <p class="w-100 pb-3"><?php echo dsp_get_channel_publication_meta(get_the_ID()); ?></p>
                                        <?php the_content(); ?>
                                        <div class="subscribe_now mt-3">
                                            <?php if (empty($parant_channel_unlocked)): ?>
                                                <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
                                            <?php else: ?>
                                                <a href="<?php echo $first_video_url; ?>" class="btn btn-secondary btn-ds-secondary">Watch Now</a>
                                            <?php endif; ?>
                                            <?php if (class_exists('WP_Auth0')) { ?>
                                                <div class="my_list_button">
                                                    <?php
                                                    if ($first_child_id) {
                                                        if ($client_token) {
                                                            $channel_id = get_post_meta($first_child_id->ID, 'chnl_id', true);
                                                            $obj = new Dsp_External_Api_Request();
                                                            $list_channel = $obj->get_user_watchlist($client_token);
                                                            $in_list = array();
                                                            if (!is_wp_error($list_channel) && $list_channel['channels'] && !empty($list_channel['channels'])) {
                                                                foreach ($list_channel['channels'] as $ch) {
                                                                    $in_list[] = $ch['_id'];
                                                                }
                                                            }
                                                            if (in_array($channel_id, $in_list)) { // $channel->isChannelInList($utoken)
                                                                ?>
                                                                <a href="/my-list" class="btn btn-danger"><i class="fa fa-minus-circle"></i> Remove from My List</a>
                                                            <?php } else { ?>
                                                                <button class="btn btn-primary btn-revry-primary manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</button>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-primary btn-revry-primary"><i class="fa fa-plus-circle"></i> Add to My List</a>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
                <!-- Display video insted of background image if user is ideal section end-->
            </div>
            <!-- Channel Banner image or video section end -->

            <div class="custom-container container pb-5">
                <div class="row no-gutters other-categories">
                    <?php
                    if (!$childchannels) {
                        $videos = $theme_function->show_videos($post, 'other_carousel');
                        $cnt = 0;
                        $channel_unlocked = $parant_channel_unlocked;
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
                            $channel_unlocked = '';
                            $single_channel = get_page_by_path($channel, OBJECT, 'channel');
                            $single_channel_meta = get_post_meta($single_channel->ID);
                            $check_subscription_status_single = $dsp_api->check_subscription_status($client_token, $single_channel_meta['dspro_channel_id'][0]);
                            if (!is_wp_error($check_subscription_status_single) && empty($check_subscription_status_single['unlocked']))
                                $channel_unlocked = false;
                            else
                                $channel_unlocked = true;
                            $videos = $theme_function->show_videos($single_channel, 'other_carousel', null, $p_channel_slug);
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
        } else {
            include(locate_template('page-templates/templates-part/not-in-web-platform.php'));
        }
    endwhile;
}
if (!empty($trailer_id)) {
    ?>
    <!-- Script to display video of user is ideal for 5 seconds -->        
    <script type="text/javascript">
        idleTimer = null;
        idleState = false;
        idleWait = 5000;
        (function ($) {
            var script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", "<?php echo'https://player.dotstudiopro.com/player/' . $trailer_id . $player_setting; ?>");

            $("#video-overlay").on('mousemove', function (e) {
                if ((e.pageX - this.offsetLeft) < $(this).width() / 2) {
                    $('.channel-teaser-info').fadeOut();
                } else {
                    $('.channel-teaser-info').fadeIn();
                }
            });

            $('#video-overlay').bind('mouseleave', function (e) {
                $('.channel-teaser-info').fadeOut();
            });

            $(document).ready(function () {
                $(window).bind('resize mousemove keydown scroll', function (e) {
                    if ($('.inner-banner-bg').isInViewport()) {
                        clearTimeout(idleTimer);
                        idleState = false;
                        idleTimer = setTimeout(function () {
                            $('.channel-teaser').show();
                            $('.chanl_background_img').hide();
                            document.getElementsByTagName("body")[0].appendChild(script);
                            idleState = true;
                        }, idleWait);
                        if (typeof dotstudiozPlayer !== "undefined" && typeof dotstudiozPlayer.player !== "undefined") {
                            dotstudiozPlayer.player.play();
                        }
                    } else {
                        if (typeof dotstudiozPlayer !== "undefined" && typeof dotstudiozPlayer.player !== "undefined") {
                            dotstudiozPlayer.player.pause();
                        }
                        clearTimeout(idleTimer);
                    }
                });
                $("body").trigger("mousemove");
            });

            $.fn.isInViewport = function () {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                return elementBottom > viewportTop && elementTop < viewportBottom;
            };

        })(jQuery)
    </script>  
<?php } ?>
<?php get_footer(); ?>
