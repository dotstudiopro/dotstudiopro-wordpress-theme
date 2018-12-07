<?php
/**
 * condition to check video slug in the url or id
 */
global $client_token;

$class_array = array();

$channel = get_page_by_path($channel_slug, OBJECT, 'channel');
$child_channels = $theme_function->is_child_channels($channel->ID);

if (!preg_match('/^[a-f\d]{24}$/i', $video_slug)) {
    if ($child_channels) {
        $video_id = $theme_function->first_video_id($channel->ID);
    } else {
        $video_data = $theme_function->get_channel_videos($channel->ID);
        foreach ($video_data as $data):
            if ($data['slug'] == $video_slug) {
                $video_id = $data['_id'];
            }
        endforeach;
    }
} else {
    $video_id = $video_slug;
}

/**
 * Get video information based on the id
 */
if (!empty($video_id))
    $video = $dsp_api->get_video_by_id($video_id);

if (!is_wp_error($video) && !empty($video)):

    $desc = isset($video['description']) ? $video['description'] : '';
    $title = isset($video['title']) ? $video['title'] : '';
    $genres = isset($video['genres']) ? $video['genres'] : '';
    $duration = isset($video['duration']) ? $video['duration'] : '';
    $year = isset($video['year']) ? '(' . $video['year'] . ')' : '';
    $company_id = isset($video['company_id']) ? $video['company_id'] : '';

    if (!empty($duration)) {
        if ($duration < 60) {
            $duration = $duration . ' sec';
        } else {
            $duration = round(($duration) / 60) . ' min';
        }
    }

    $settings = [];
    $settings[] = 'companykey=' . $company_id;
    $settings[] = 'skin=' . ltrim($player_color, "#");
    $settings[] = 'autostart=' . $autoplay;
    $settings[] = 'muteonstart=' . $mute_on_load;

    // Code to check if user subscribe to watch this channel
    $check_subscription_status = $dsp_api->check_subscription_status($client_token, get_post_meta($channel->ID, 'dspro_channel_id', true));
    if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked'])):
        $channel_unlocked = false;
    else:
        if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['ads_enabled']))
            $settings[] = 'disableads=true';
        $channel_unlocked = true;
    endif;

    $player_setting = '?targetelm=.player&' . implode('&', $settings);

    /*
     * Get "recently watched" data for a video.
     */
    $video_point = '';
    if (!empty($client_token)) {
        $get_video_data = $dsp_api->get_recent_viewed_data_video($client_token, $video_id);
        if (!is_wp_error($get_video_data) && !empty($get_video_data['data']['point'])) {
            $video_point = $get_video_data['data']['point'];
        }
    }
    ?>

    <?php if (!empty($channel_unlocked)): ?>
        <div id="video-overlay">
            <div class="video-content-div">
                <div class="custom-container container">
                    <div class="video-player">
                        <div class="player-content">
                            <div class="player-content-inner">
                                <div class="visible-desktop" id="hero-vid">
                                    <div class="player" data-video_id="<?php echo $video_id; ?>" data-nonce="<?php echo wp_create_nonce('save_point_data'); ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div id="video-overlay">
            <div class="video-content-div">
                <div class="custom-container container">
                    <div class="video-player">
                        <div class="player-content">
                            <div class="player-content-inner">
                                <div class="visible-desktop" id="hero-vid">
                                    <div class="image">
                                        <?php
                                        $banner = get_post_meta($channel->ID, 'chnl_poster', true);
                                        ?>
                                        <div class="inner-banner-img"><img src="<?php echo $banner . '/1300/650'; ?>" alt="<?php echo get_the_title(); ?>">
                                            <div class="v-overlay">
                                                <div class="lock_overlay"><i class="fa fa-lock"></i></span>
                                                    <div class="subscribe_now mt-3">
                                                        <p>In order to view this video you need to subscribe first</p>
                                                        <a href="/packages" class="btn btn-primary">Subscribe Now</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="video-page-content">
        <div class="row no-gutters">
            <div class="custom-container container video-content">
                <div class="col-md-9 col-sm-9 pull-left">
                    <h4 class="post-title mb-2 pt-5"><?php echo $title; ?></h4>
                    <p><?php echo $year . ' - ' . $duration; ?></p>
                    <p>
                        <?php
                        if ($genres) {
                            foreach ($genres as $genre) {
                                echo '<span class="p-2">' . $genre . '</span>';
                            }
                        }
                        ?>
                    </p>
                    <p><?php echo $desc; ?></p>
                </div>
                <div class="col-md-3 col-sm-3 pull-right">
                    <?php
                    $channel_meta = get_post_meta($channel->ID);
                    $channel_img = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/240/360';
                    $channel_id = $channel_meta['chnl_id'][0];
                    if ($channel_meta['chnl_spotlight_poster'][0]) {
                        $channel_img = $channel_meta['chnl_spotlight_poster'][0] . '/240/360';
                    }
                    ?>
                    <div class="col-sm-12 text-center add_to_list mb-2 pt-5">
                        <img src="<?php echo $channel_img; ?>" alt="<?php echo $channel->title; ?>" class="search-custom-width mb-2">
                        <div class="my_list_button">
                            <?php
                            if ($client_token) {
                                $obj = new Dsp_External_Api_Request();
                                $list_channel = $obj->get_user_watchlist($client_token);
                                $in_list = array();
                                if ($list_channel['channels'] && !empty($list_channel['channels'])) {
                                    foreach ($list_channel['channels'] as $ch) {
                                        $in_list[] = $ch['_id'];
                                    }
                                }
                                if (in_array($channel_id, $in_list)) { // $channel->isChannelInList($utoken)
                                    ?>
                                    <a href="/my-list" class="btn btn-danger text-uppercase"><i class="fa fa-minus-circle"></i>Remove from My List</a>
                                <?php } else { ?>
                                    <button class="btn btn-primary text-uppercase manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</button>
                                <?php } ?>
                            <?php } else { ?>
                                <button class="btn btn-primary login-link text-uppercase"><i class="fa fa-plus-circle"></i>Add to My List</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
        /**
         * code to add next and previous video link
         */
        if (!$child_channels) {
            $npvideos = $theme_function->show_videos($channel, 'other_carousel');
            $next_video = array();
            $prev_video = array();
            foreach ($npvideos as $key => $npvideo) {

                if (!preg_match('/^[a-f\d]{24}$/i', $video_slug)) {
                    if ($npvideo['slug'] == $video_slug) {
                        $next_video[] = isset($npvideos[$key + 1]) ? $npvideos[$key + 1] : '';
                        $prev_video[] = isset($npvideos[$key - 1]) ? $npvideos[$key - 1] : '';
                    }
                } else {
                    if ($npvideo['id'] == $video_slug) {
                        $next_video[] = isset($npvideos[$key + 1]) ? $npvideos[$key + 1] : '';
                        $prev_video[] = isset($npvideos[$key - 1]) ? $npvideos[$key - 1] : '';
                    }
                }
            }
        } else {
            foreach ($child_channels as $key => $npchild_channel) {
                $single_channel = get_page_by_path($npchild_channel, OBJECT, 'channel');
                $npvideos = $theme_function->show_videos($single_channel, 'other_carousel');
                foreach ($npvideos as $key => $npvideo) {
                    if (!empty($npvideo['_id']) && !empty($video_id) && $npvideo['_id'] == $video_id) {
                        $next_video[] = isset($npvideos[$key + 1]) ? $npvideos[$key + 1] : '';
                        $prev_video[] = isset($npvideos[$key - 1]) ? $npvideos[$key - 1] : '';
                    }
                }
            }
        }
        ?>
        <div class="row no-gutters pt-4 pb-5 video-next-prev">
            <div class="custom-container container">
                <?php if (!empty($prev_video[0])) { ?>

                    <div class="prev_video_link pull-left pr-1">
                        <a href="<?php echo $prev_video[0]['url']; ?>" title="<?php echo $prev_video[0]['title']; ?>">
                            <div class="simple-navigation-item-content">
                                <span class="pull-left"><i class="fa fa-angle-left"></i></span>
                                <h5 class="pl-2"><?php echo $prev_video[0]['title']; ?></h5>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                <?php if (!empty($next_video[0])) { ?>
                    <div class="next_video_link pull-right pl-1">
                        <a href="<?php echo $next_video[0]['url']; ?>" title="<?php echo $next_video[0]['title']; ?>">
                            <div class="simple-navigation-item-content">
                                <span class="pull-right"><i class="fa fa-angle-right"></i></span>
                                <h5 class="pr-2"><?php echo $next_video[0]['title']; ?></h5>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="row no-gutters">
            <div class="custom-container container  pt-7 other-categories">
                <?php
                /**
                 * function to display rails of the video in the current channel
                 */
                if ($child_channels) {
                    $cnt = 0;
                    foreach ($child_channels as $child_channel) {
                        $single_channel = get_page_by_path($child_channel, OBJECT, 'channel');
                        $videos = $theme_function->show_videos($single_channel, 'other_carousel');
                        if ($videos) {
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="no-gutters">
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
                } else {
                    $videos = $theme_function->show_videos($channel, 'other_carousel');
                    $cnt = 0;
                    if ($videos) {
                        ?>
                        <!-- Single Channel Video section start -->
                        <div class="no-gutters">
                            <h3 class="post-title mb-5"><?php echo $channel->post_title; ?></h3>
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
                }
                ?>
            </div>
        </div>
        <?php
    endif;
    ?>   
    <div class="row no-gutters pb-5">
        <div class="custom-container container  pt-7 other-categories">
            <?php
// Display Recomendation section
            if ($dsp_theme_options['opt-related-section'] == 1) {
                if ($dsp_theme_options['opt-related-option'] == 'channel') {
                    $type = 'channel';
                    $related_id = get_post_meta($channel->ID, 'dspro_channel_id', true);
                } else {
                    $type = 'video';
                    $related_id = $theme_function->first_video_id($channel->ID);
                }
                $cnt = 0;
                include(locate_template('page-templates/templates-part/related-content.php'));
            }
            $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
            $theme_function->slick_init_options('slick_carousel', $class_array, 'video');
            ?>
        </div>
    </div>
    <script>
        jQuery(document).ready(function (e) {

            var script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", "<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>");
            document.getElementsByTagName("body")[0].appendChild(script);

            var dspPlayerCheck = setInterval(function () {
                if (typeof dotstudiozPlayer !== "undefined" && typeof dotstudiozPlayer.player !== "undefined") {
                    clearInterval(dspPlayerCheck);
                    dotstudiozPlayer.player.on("ended", function () {
                        var nextHref = "<?php echo (!empty($next_video[0])) ? $next_video[0]['url'] : ''; ?>";
                        if (nextHref.length > 0)
                            window.location.href = nextHref;
                    });
                }
            }, 250);

<?php if ($client_token && $video_point) { ?>
                jQuery(document).ready(function (e) {
                    var dspPlayerCheckTimepoint = setInterval(function () {
                        if (typeof dotstudiozPlayer !== "undefined" && typeof dotstudiozPlayer.player !== "undefined") {
                            clearInterval(dspPlayerCheckTimepoint);
                            dotstudiozPlayer.player.currentTime(<?php echo $video_point; ?>);
                        }
                    }, 250);
                });
<?php } ?>
        });
    </script>
</div>
