<?php
$video_id = $video_slug;

/**
 * Get video information based on the id
 */
if (!empty($video_id))
    $video = $dsp_api->get_video_by_id($video_id);

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
    $player_setting = '?targetelm=.player&' . implode('&', $settings)
    ?>

    <div class="video-content-div">
        <div class="custom-container container">
            <div class="video-player">
                <div class="player-content">
                    <div class="player-content-inner">
                        <div class="player"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-gutters">
        <div class="custom-container container video-content">
            <h3 class="post-title mb-5 pt-5"><?php echo $title; ?></h3>
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
    <script src="<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>"></script>
    <?php
endif;
?>
<div class="row no-gutters mb-5">
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
        }
        ?>
    </div></div>
<?php
$theme_function->slick_init_options($class_array, 'video');
?>
