<?php 

/**
 * Get video information based on the id
 */
 

if (!is_wp_error($video) && !empty($video)):
    global $share_banner, $share_desc, $share_title;
    $share_desc = $desc = isset($video['description']) ? $video['description'] : '';
    $share_title = $title = isset($video['title']) ? $video['title'] : '';
    $share_banner = $banner = ($video['thumb']) ? $video['thumb'] : '';
endif;
get_header(); 

if (!is_wp_error($video) && !empty($video)):
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

    /*
     * Get "recently watched" data for a video.
     */
    $video_point = '';
    $get_video_data = $dsp_api->get_recent_viewed_data_video($client_token, $video_id);
    if (!is_wp_error($get_video_data) && !empty($get_video_data['data']['point'])) {
        $video_point = $get_video_data['data']['point'];
    }
?>

<div id="video-player-block">
  <div class="player-container">
    <div class="aspect-ratio-controller">
      <div id="DotPlayer" class="player" data-video_id="<?php echo $video_id; ?>" data-nonce="<?php echo wp_create_nonce('save_point_data'); ?>"></div>
    </div>
  </div>
</div>

<div class="video-page-content">
    <div class="row no-gutters">
        <div class="custom-container container video-content">
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
    </div>
    <?php
endif;
?>

<div class="row no-gutters pb-5">
    <div class="custom-container container  pt-5 other-categories">
        <?php
        // Display Recomendation section
        if ($dsp_theme_options['opt-related-section'] == 1) {
            $type = 'video';
            $related_id = $video_id;
            $cnt = 0;
            include(locate_template('page-templates/templates-part/related-content.php'));
            $theme_function->slick_init_options('slick_related_carousel', 'related_content', 'related');
        }
        ?>
    </div>
</div>

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

        // Note: There are no channel-related params to pass,
        // as this is viewing a video outside of a channel

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