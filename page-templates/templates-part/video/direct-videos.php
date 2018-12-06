<?php
$video_id = $video_slug;

/**
 * Get video information based on the id
 */
if (preg_match('/^[a-f\d]{24}$/i', $video_id)) {
    $video = $dsp_api->get_video_by_id($video_id);
} else {
    wp_redirect(home_url());
}

if (!is_wp_error($video) && !empty($video)):
    $desc = ($video['description']) ? $video['description'] : '';
    $title = ($video['title']) ? $video['title'] : '';
    $genres = isset($video['genres']) ? $video['genres'] : '';
    $duration = isset($video['duration']) ? $video['duration'] : '';
    $year = isset($video['year']) ? $video['year'] : '';
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
    $player_setting = '?targetelm=.player&' . implode('&', $settings);

    /*
     * Get "recently watched" data for a video.
     */
    $video_point = '';
    $get_video_data = $dsp_api->get_recent_viewed_data_video($client_token, $video_id);
    if (!is_wp_error($get_video_data) && !empty($get_video_data['data']['point'])) {
        $video_point = $get_video_data['data']['point'];
    }
    ?>
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
        <div class="video-page-content">
            <div class="row no-gutters">
                <div class="custom-container container video-content">
                    <h4 class="post-title mb-2 pt-5"><?php echo $title; ?></h4>
                    <p><?php echo '(' . $year . ') - ' . $duration; ?></p>
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
            </div>
        <!--    <script src="<?php //echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting;                                          ?>"></script>-->
            <?php
        endif;
        ?>
        <div class="row no-gutters pb-5">
            <div class="custom-container container  pt-7 other-categories">
                <?php
// Display Recomendation section
                if ($dsp_theme_options['opt-related-section'] == 1) {
                    $type = 'video';
                    $related_id = $video_id;
                    $cnt = 0;
                    include(locate_template('page-templates/templates-part/related-content.php'));
                    $class_array = array();
                    array_push($class_array, 'related_content');
                    $theme_function->slick_init_options($class_array, 'video');
                }
                ?>
            </div></div>
        <?php
        ?>
        <script>
            jQuery(document).ready(function (e) {

                var script = document.createElement("script");
                script.setAttribute("type", "text/javascript");
                script.setAttribute("src", "<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>");
                document.getElementsByTagName("body")[0].appendChild(script);

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
