<?php
/**
 * condition to check video slug in the url or id
 */
global $client_token, $wp;

$class_array = array();
$cnt = 0;
$video_unlocked = $channel_unlocked = false;

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
    global $share_banner, $share_desc, $share_title;
    $share_desc = $desc = isset($video['description']) ? $video['description'] : '';
    $share_title = $title = isset($video['title']) ? $video['title'] : '';
    $share_banner = $banner = isset($video['thumb']) ? $video['thumb'] : get_post_meta($channel->ID, 'chnl_poster', true);
endif;
get_header();
$channel_meta = get_post_meta($channel->ID);
$dsp_api = new Dsp_External_Api_Request();
$country_code = $dsp_api->get_country();
$dspro_channel_geo = unserialize($channel_meta['dspro_channel_geo'][0]);
if($country_code && !in_array("ALL", $dspro_channel_geo) && !in_array($country_code, $dspro_channel_geo) && !empty($dspro_channel_geo)){
    ?>
    <div class="custom-container container pb-5">
        <div class="row no-gutters other-categories text-center">
            <h4 class="p-4 w-100">The owner of this content has made it unavailable in your country.</h4>
            <h4 class="p-4 w-100">Please explore our other selections from <a href="/home-page" title="Explore">here</a></h4>
        </div>
    </div>
    <?php
    return;
}
if (!is_wp_error($video) && !empty($video)):
    $genres = isset($video['genres']) ? $video['genres'] : '';
    $duration = isset($video['duration']) ? $video['duration'] : '';
    $year = isset($video['year']) ? '(' . $video['year'] . ')' : '';
    $company_id = isset($video['company_id']) ? $video['company_id'] : '';
    $chnl_id = isset($main_channel_meta['chnl_id'][0]) ? $main_channel_meta['chnl_id'][0] : '';
    $dspro_channel_id = isset($main_channel_meta['dspro_channel_id'][0]) ? $main_channel_meta['dspro_channel_id'][0] : '';

    if (!empty($duration)) {
        if ($duration < 60) {
            $duration = $duration . ' sec';
        } else {
            $duration = round(($duration) / 60) . ' min';
        }
    }

    $settings = [];

    $show_ads = true;

    // Code to check if user subscribe to watch this channel
    $check_subscription_status = $dsp_api->check_subscription_status($client_token, get_post_meta($channel->ID, 'dspro_channel_id', true));
    if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked'])):
        $video_unlocked = $channel_unlocked = false;
    else:
        if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['ads_enabled']))
            $show_ads = false;
        $video_unlocked = $channel_unlocked = true;
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
        <div id="video-player-block">
          <div class="player-container">
            <div class="aspect-ratio-controller">
              <div id="DotPlayer" class="player" data-video_id="<?php echo $video_id; ?>" data-nonce="<?php echo wp_create_nonce('save_point_data'); ?>"></div>
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
                                        <div class="inner-banner-img"><img src="<?php echo $banner . '/1300/731'; ?>" alt="<?php echo get_the_title(); ?>">
                                            <div class="v-overlay">
                                                <div class="lock_overlay"><i class="fa fa-lock"></i></span>
                                                    <div class="subscribe_now mt-3">
                                                        <p>In order to view this video you need to subscribe first</p>
                                                        <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
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
                    <h3 class="post-title mb-4 pt-4"><?php echo $title; ?></h3>
                    <?php if (function_exists('sharethis_inline_buttons')) { ?>
                        <p> <?php echo sharethis_inline_buttons(); ?> </p>
                    <?php } ?>
                    <p><?php echo $year . ' - ' . $duration; ?></p>
                    <p class="video-cat">
                        <?php
                        if ($genres) {
                            foreach ($genres as $genre) {
                                echo '<span>' . $genre . '</span>';
                            }
                        }
                        ?>
                    </p>
                    <p class="descr"><?php echo $desc; ?></p>
                </div>
                <div class="col-md-3 col-sm-3 pull-right">
                    <?php
                    $channel_meta = get_post_meta($channel->ID);
                    $channel_img = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/240/360';
                    $channel_id = $channel_meta['chnl_id'][0];
                    if ($channel_meta['chnl_spotlight_poster'][0]) {
                        $channel_img = $channel_meta['chnl_spotlight_poster'][0] . '/240/360';
                    }
					$p_channel_id = 0;
                    if (!empty($p_channel_slug)) {
                        $p_channel = get_page_by_path($p_channel_slug, OBJECT, 'channel');
                        $p_channel_meta = get_post_meta($p_channel->ID);
                        $p_channel_id = $p_channel_meta['chnl_id'][0];
                    }
                    ?>
                    <div class="text-center add_to_list mb-2 pt-5">
                        <img src="<?php echo $channel_img; ?>" alt="<?php echo $channel->title; ?>" class="video-right-img mb-2">
			<?php if(class_exists('WP_Auth0_Options')){ ?>
							<div class="my_list_button">
								<?php
								if ($client_token) {
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
										<button class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove from My List</button>
									<?php } else { ?>
										<button class="btn btn-primary btn-ds-primary manage_my_list" data-channel_id="<?php echo $channel_id; ?>"  data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</button>
                                        <span data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>" style="display: none;"></span>
									<?php } ?>
								<?php } else { ?>
                                                                        <a href="<?php echo wp_login_url( home_url( $wp->request ) ); ?>" class="btn btn-primary btn-ds-primary">+ Add to My List</a>
								<?php } ?>
							</div>
						<?php } ?>
                    </div>
                </div>

            </div>
        </div>
        <?php
        /**
         * code to add next and previous video link
         */
        if (!$child_channels) {
            $npvideos = $theme_function->show_videos($channel, 'other_carousel', null, $p_channel_slug);
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
                $npvideos = $theme_function->show_videos($single_channel, 'other_carousel', null, $single_channel->post_name);
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

                    foreach ($child_channels as $child_channel) {
                        $single_channel = get_page_by_path($child_channel, OBJECT, 'channel');
                        $videos = $theme_function->show_videos($single_channel, 'other_carousel', null, $single_channel->post_name);
                        if ($videos) {
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="no-gutters">
                                <h3 class="post-title mb-5"><?php echo $single_channel->post_title; ?></h3>
                                <?php
                                $class = 'home-carousel' . $cnt++;
                                $class_array[] = $class;
                                if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
                                    $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                } else {
                                    $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio = $ratio_height / $ratio_width;
                                }
                                include(locate_template('page-templates/templates-part/channel-videos.php'));
                                ?>
                            </div>
                            <!-- Single Channel Video section end -->
                            <?php
                        }
                    }
                } else {
                    $videos = $theme_function->show_videos($channel, 'other_carousel', null, $p_channel_slug);
                    if ($videos) {
                        ?>
                        <!-- Single Channel Video section start -->
                        <div class="no-gutters">
                            <h3 class="post-title mb-5"><?php echo $channel->post_title; ?></h3>
                            <?php
                            $class = 'home-carousel' . $cnt++;
                            $class_array[] = $class;
                            if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
                                $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                            } else {
                                $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                $ratio = $ratio_height / $ratio_width;
                            }
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

        <!--  Code to display another rail section -->

        <div class="row no-gutters">
            <div class="custom-container container  pt-7 other-categories">
                <?php
                if (!empty($p_channel_slug)) {
                    $parent_channel = get_page_by_path($p_channel_slug, OBJECT, 'channel');
                    $parant_child_channels = $theme_function->is_child_channels($parent_channel->ID);
                    /**
                     * function to display rails of the video in the paranet channel
                     */
                    if ($parant_child_channels) {
                        if (($key = array_search($channel_slug, $parant_child_channels)) !== false) {
                            unset($parant_child_channels[$key]);
                        }
                        foreach ($parant_child_channels as $parant_child_channel) {
                            $single_channel = get_page_by_path($parant_child_channel, OBJECT, 'channel');
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
                                <div class="no-gutters">
                                    <h3 class="post-title mb-5"><?php echo $single_channel->post_title; ?></h3>
                                    <?php
                                    $class = 'home-carousel' . $cnt++;
                                    $class_array[] = $class;
                                    if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
                                        $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                        $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                    } else {
                                        $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                        $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                        $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                        $ratio = $ratio_height / $ratio_width;
                                    }
                                    include(locate_template('page-templates/templates-part/channel-videos.php'));
                                    ?>
                                </div>
                                <!-- Single Channel Video section end -->
                                <?php
                            }
                        }
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
                include(locate_template('page-templates/templates-part/related-content.php'));
            }
            $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
            $theme_function->slick_init_options('slick_carousel', $class_array, 'video');
            ?>
        </div>
    </div>
    <?php if ($video_unlocked == true): ?>
        <script>
            jQuery(document).ready(function (e) {
                const mountObj = {
                    video_id: "<?php echo $video_id; ?>",
                    company_id: "<?php echo $company_id; ?>",
                    target: "#DotPlayer",
                    autostart: <?php echo $autoplay ? "true" : "false"; ?>,
                    muted: <?php echo $mute_on_load ? "true" : "false"; ?>,
                    fluid: false,
                    theme: {}
                }

                /* PLAYER THEMEING */
                <?php  if (!empty($dsp_theme_options["opt-player-icon-color"])) { ?>
                    mountObj.theme.fontColor = "<?php echo $dsp_theme_options["opt-player-icon-color"]; ?>";
                <?php } ?>

                <?php  if (!empty($dsp_theme_options["opt-player-font-color-hover"])) { ?>
                    mountObj.theme.fontColorHover = "<?php echo $dsp_theme_options["opt-player-font-color-hover"]; ?>";
                <?php } ?>

                <?php  if (!empty($dsp_theme_options["opt-player-progress-slider-main"])) { ?>
                    mountObj.theme.progressSliderMain = "<?php echo $dsp_theme_options["opt-player-progress-slider-main"]; ?>";
                <?php } ?>

                <?php  if (!empty($dsp_theme_options["opt-player-progress-slider-bg"])) { ?>
                    mountObj.theme.progressSliderBackground = "<?php echo $dsp_theme_options["opt-player-progress-slider-bg"]; ?>";
                <?php } ?>

                <?php  if (!empty($dsp_theme_options["opt-player-control-bar-color"])) { ?>
                    mountObj.theme.controlBar = "<?php echo $dsp_theme_options["opt-player-control-bar-color"]; ?>";
                <?php } ?>
                /* /END PLAYER THEMEING */


                <?php if (!empty($chnl_id)) { ?>
                    mountObj.channel_id = "<?php echo $chnl_id; ?>";
                    mountObj.channel_title = <?php echo json_encode($chnl_title); ?>;
                <?php } ?>
                <?php if(!empty($dspro_channel_id)) { ?>
                    mountObj.dspro_channel_id = "<?php echo $dspro_channel_id; ?>";
                <?php } ?>

                <?php if (!$show_ads) { ?>
                    mountObj.show_interruptions = false;
                <?php } ?>



                DotPlayer.mount(mountObj);

                let playerMounted = false;

                var dspPlayerCheck = setInterval(function () {
                    if (typeof DotPlayer.on !== "undefined") {
                        clearInterval(dspPlayerCheck);
                        DotPlayer.on("ended", function () {
                            var nextHref = "<?php echo (!empty($next_video[0])) ? $next_video[0]['url'] : ''; ?>";
                            if (nextHref.length > 0)
                                window.location.href = nextHref;
                        });
                        playerMounted = true;
                    }
                }, 250);

                <?php if ($client_token && $video_point) { ?>
                        var dspPlayerCheckTimepoint = setInterval(function () {
                            if (playerMounted) {
                                clearInterval(dspPlayerCheckTimepoint);
                                DotPlayer.currentTime(<?php echo $video_point; ?>);
                            }
                        }, 250);
                <?php } ?>
            });
        </script>
    <?php endif; ?>
</div>
