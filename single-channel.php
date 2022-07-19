<?php
include(locate_template('page-templates/templates-processing/single-channel-processing.php'));
get_header();
if (have_posts()) {
    while (have_posts()) : the_post();
        if($channel_geoblock){
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
        if ($plateform_web) { ?>
            <!-- Channel Banner image or video section start -->
            <div class="chnl inner-banner-bg">
                <!-- Channel Banner image section start -->
                <div class="chanl_background_img">
                    <div class="inner-banner-img">
                        <img src="<?php echo $banner; ?>" alt="<?php echo get_the_title(); ?>">
                    </div>
                    <div class="inner-banner-content_bg">
                       <?php include(locate_template('page-templates/templates-part/single-channel-banner-description.php')); ?>
                    </div>
                </div>
                <!-- Channel Banner image section end -->
                <!-- Display video insted of background image if user is ideal section start-->
                <?php
                if (!empty($trailer_id)) { ?>
                    <div id="video-overlay" class="channel-teaser">
                        <div class="player" data-video_id="<?php echo $trailer_id; ?>"></div>
                        <div class="inner-banner-content_bg channel-teaser-info">
                           <?php include(locate_template('page-templates/templates-part/single-channel-banner-description.php')); ?>
                        </div>
                    </div>
                <?php } ?>
                <!-- Display video insted of background image if user is ideal section end-->
            </div>
            <!-- Channel Banner image or video section end -->

            <div class="custom-container container pb-5">
                <div class="row no-gutters other-categories">
                    <?php
                    // Display Channel video rails section
                    if(!empty($channel_data)){
                        $cnt = 0;
                        foreach($channel_data as $data){
                            $channel_unlocked = $data['channel_unlocked'];
                            if(!empty($data['videos'])){ 
                                $videos = $data['videos'];
                            ?>
                            <!-- Single Channel Video section start -->
                            <div class="col-sm-12 no-gutters pt-7">
                                <h3 class="post-title mb-5"><?php echo $data['title']; ?></h3>
                                <?php
                                $class = 'home-carousel' . $cnt;
                                $class_array[] = $class;
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
                    if ($dsp_theme_options['opt-related-section'] == 1) { ?>
                        <div class="col-sm-12 no-gutters pt-7">
                            <?php
                            include(locate_template('page-templates/templates-part/related-content.php'));
                            ?>
                        </div>
                    <?php
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

if (!empty($trailer_id)) { ?>
    <!-- Script to display video of user is ideal for 5 seconds -->
    <script type="text/javascript">
        idleTimer = null;
        idleState = false;
        idleWait = 5000;
        (function ($) {
            $.fn.isInViewport = function () {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                return elementBottom > viewportTop && elementTop < viewportBottom;
            };
            const mountObj = {
                video_id: "<?php echo $trailer_id; ?>",
                company_id: "<?php echo $company_id; ?>",
                target: ".player",
                autostart: true,
                muted: <?php echo $mute_on_load ? "true" : "false"; ?>,
                fluid: false,
                // We need to loop but don't have a value for it yet...ugh
                controls: false,
                <?php // This flag controls ads; we have this set to false since we are just displaying a trailer ?>
                show_interruptions: false,
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

            let playerMounted = false;
            $(document).ready(function () {
                $(window).bind('resize mousemove keydown scroll', function (e) {
                    if ($('.inner-banner-bg').isInViewport()) {
                        clearTimeout(idleTimer);
                        idleState = false;
                        idleTimer = setTimeout(function () {
                            $('.channel-teaser').show();
                            $('.chanl_background_img').hide();
                            DotPlayer.mount(mountObj);
                            idleState = true;
                        }, idleWait);
                        if (playerMounted) {
                            DotPlayer.play();
                        }
                    } else {
                        if (playerMounted) {
                            DotPlayer.pause();
                        }
                        clearTimeout(idleTimer);
                    }
                });
                $("body").trigger("mousemove");
            });
            var dspPlayerCheck = setInterval(function () {
                if (typeof DotPlayer.on !== "undefined") {
                    clearInterval(dspPlayerCheck);
                    playerMounted = true;
                }
            }, 250);

        })(jQuery)
    </script>
<?php } if(!empty($live_stream_start_time)){ ?>
<script type="text/javascript">
    var timezone_name = Intl.DateTimeFormat().resolvedOptions().timeZone;
    var convert_live_stream_start_time = '<?php echo $convert_live_stream_start_time?>';
    var updated_live_stream_start_time = convertTZ(convert_live_stream_start_time, timezone_name);
    var formate_live_stream_start_time = updated_live_stream_start_time.toLocaleString('en-US', {
        day: 'numeric', 
        year: 'numeric', 
        month: 'long', 
        hour: '2-digit', 
        minute: '2-digit',
    });
    jQuery('.available_on_date').text('Available On ' + formate_live_stream_start_time);
    function convertTZ(date, tzString) {
        return new Date((typeof date === "string" ? new Date(date) : date).toLocaleString("en-US", {timeZone: tzString}));   
    }
</script>
<?php } ?>
<?php get_footer(); ?>
