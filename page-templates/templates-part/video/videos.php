<?php
include(locate_template('page-templates/templates-processing/videos-processing.php'));
get_header();

if($channel_geoblock){
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
    if (!empty($channel_unlocked) || $video_data['bypass_channel_lock'] == 'true' || $video_data['bypass_channel_lock'] == true): ?>
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
                                        <div class="inner-banner-img">
                                            <img src="<?php echo $banner; ?>" alt="<?php echo get_the_title(); ?>">
                                            <div class="v-overlay">
                                                <div class="lock_overlay"><i class="fa fa-lock"></i></span>
                                                    <div class="subscribe_now mt-3">
                                                        <p>In order to view this video you need to subscribe first</p>
                                                         <?php if(!empty($svod_products)): ?>
                                                            <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
                                                            <?php endif; ?>
                                                            <div class="more_ways_to_watch_now mt-3 mr-4">
                                                                <?php if (!empty($tvod_products)): ?>
                                                                <a href="/more-ways-to-watch/<?php echo $channel->post_name; ?>" class="btn btn-secondary btn-ds-secondary">More Ways to Watch</a>
                                                            <?php endif; ?>
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
                    <p><?php echo $video_data['year'] . ' - ' . $video_data['duration']; ?></p>
                    <p class="video-cat">
                        <?php
                        if ($video_data['genres']) {
                            foreach ($video_data['genres'] as $genre) {
                                echo '<span>' . $genre . '</span>';
                            }
                        }
                        ?>
                    </p>
                    <p class="descr"><?php echo $desc; ?></p>
                </div>
                <div class="col-md-3 col-sm-3 pull-right">
                    <div class="text-center add_to_list mb-2 pt-5">
                        <img src="<?php echo $channel_img; ?>" alt="<?php echo $channel->title; ?>" class="video-right-img mb-2">
                         <?php if(class_exists('WP_Auth0_Options')){ ?>
                            <div class="my_list_button">
                                <?php
                                if ($client_token) {
                                    if (isset($display_remove_from_my_list_button)) { ?>
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
                if(!empty($current_channel_data)){
                    foreach($current_channel_data as $data){
                        if(!empty($data['videos']))
                            $videos = $data['videos'];
                    ?>
                    <div class="no-gutters">
                        <h3 class="post-title mb-5"><?php echo $data['title']; ?></h3>
                    <?php
                        $class = 'home-carousel' . $cnt++;
                        $class_array[] = $class;
                        include(locate_template('page-templates/templates-part/channel-videos.php'));
                    ?>
                    </div>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
        <!--  Code to display another rail section -->
        <div class="row no-gutters">
            <div class="custom-container container other-categories">
                <?php
                if (!empty($channel_data)) {
                    foreach ($channel_data as $p_channel_data) {
                        $channel_unlocked = $p_channel_data['channel_unlocked'];
                        if(!empty($p_channel_data['videos']))
                            $videos = $p_channel_data['videos'];
                        if ($videos) { ?>
                            <!-- Single Channel Video section start -->
                            <div class="no-gutters pt-7">
                                <h3 class="post-title mb-5"><?php echo $p_channel_data['title']; ?></h3>
                                <?php
                                $class = 'home-carousel' . $cnt++;
                                $class_array[] = $class;
                                include(locate_template('page-templates/templates-part/channel-videos.php'));
                                ?>
                            </div>
                            <!-- Single Channel Video section end -->
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
        <?php
        // Display Recomendation section
        if ($dsp_theme_options['opt-related-section'] == 1) { ?>
            <div class="row no-gutters pb-5">
                <div class="custom-container container  pt-7 other-categories">
                    <?php
                    include(locate_template('page-templates/templates-part/related-content.php'));
                    ?>
                </div>
            </div>
        <?php
        }
        $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
        $theme_function->slick_init_options('slick_carousel', $class_array, 'video');
        ?>
        <?php if ($video_unlocked == true || $video_data['bypass_channel_lock'] == 'true' || $video_data['bypass_channel_lock'] == true): ?>
            <script>
                jQuery(document).ready(function (e) {
                    const mountObj = {
                        video_id: "<?php echo $video_id; ?>",
                        company_id: "<?php echo $video_data['company_id']; ?>",
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

                    DotPlayer.mount(mountObj).then(async (player) => {
                        await player.isPlayerLoaded();
                        const {vjs} = player;
                        storeVideoPoint(vjs);
                        vjs.on("ended", function () {
                            var nextHref = "<?php echo (!empty($next_video[0])) ? $next_video[0]['url'] : ''; ?>";
                            if (nextHref.length > 0)
                                window.location.href = nextHref;
                        });
                        <?php if ($client_token && $video_point) { ?>
                                vjs.currentTime(<?php echo $video_point; ?>);
                        <?php } ?>
                    }).catch(mountErr => {
                        console.log({mountErr})
                    });
                });
            </script>
        <?php endif; ?>
    </div>
<?php
endif;
?>
