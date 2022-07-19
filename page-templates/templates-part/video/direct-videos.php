<?php
include(locate_template('page-templates/templates-processing/direct-videos-processing.php'));
get_header();

if(empty($display_direct_video)){ ?>
    <div class="custom-container container pt-5 pb-5  pt-5 pb-5 center-page-content">
        <div class="row no-gutters">
            <h3 class="col-12 text-center">In order to view this video you need to subscribe first</h3>
            <div class="col-12 text-center pt-3"><a href="/packages" title="Subscribe Now" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a></div>
        </div>
    </div>
<?php } else{ ?>

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
                $genresArray = array();
                foreach ($genres as $genre) {
                        $genresArray[] = '<span>' . $genre . '</span>';
                }
                echo implode( ', ', $genresArray );
            }
            ?>
            </p>
            <p class="descr"><?php echo $desc; ?></p>
        </div>
    </div>
</div>

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
<?php
}

?>

