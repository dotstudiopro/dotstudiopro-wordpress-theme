<?php
/**
 * Template Name: Video Player
 */
get_header();
?>

<?php
$dsp_api = new Dsp_External_Api_Request();
$theme_function = new Theme_Functions();

$video_slug = get_query_var('video_slug');
$channel_slug = get_query_var('channel_slug');
$player_color = (get_option('dsp_video_color_field')) ? get_option('dsp_video_color_field') : '#000000';
$mute_on_load = (get_option('dsp_video_muteload_field')) ? 'true' : 'false';
$autoplay = (get_option('dsp_video_autoplay_field')) ? 'true' : 'false';
$channel_data = get_page_by_path($channel_slug, OBJECT, 'channel');
$channel_id = $channel_data->ID;
$video = null;
if ($channel_slug) {
    $request = $dsp_api->get_video_by_slug_or_id($channel_slug, $video_slug);
    if (!is_wp_error($request)) {
        if ($request['object_type'] == 'video') {
            $video = $request;
        } else if ($request['object_type'] == 'channel') {
            if (!empty($request['channels'])) {
                foreach ($request['channels'] as $channel) {
                    if (!empty($channel['childchannels'])) {
                        if ($channel['slug'] == $channel_sulg) {
                            foreach ($channel['childchannels'] as $child) {
                                if (!empty($child['playlist'])) {
                                    foreach ($child['playlist'] as $video) {
                                        if ((isset($video['slug']) && $video['slug'] == $video_slug) || (isset($video['_id']) && $video['_id'] == $video_slug)) {
                                            $data = $video;
                                        }
                                    }
                                }
                            }
                        } else {
                            foreach ($channel['childchannels'] as $child) {
                                if ($child['slug'] != $channel_slug)
                                    return;
                                if (!empty($child['playlist'])) {
                                    foreach ($child['playlist'] as $video) {
                                        if ((isset($video['slug']) && $video['slug'] == $video_slug) || (isset($video['_id']) && $video['_id'] == $video_slug)) {
                                            $data = $video;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (!empty($channel['playlist'])) {
                            foreach ($channel['playlist'] as $video) {
                                if ((isset($video['slug']) && $video['slug'] == $video_slug) || (isset($video['_id']) && $video['_id'] == $video_slug)) {
                                    $data = $video;
                                }
                            }
                        } else if (!empty($channel['video'])) {
                            $data = $channel['video'];
                        }
                    }
                }
            }
            $video = $data;
        }
    }
}
if ($video) {
    $video_id = $video['_id'];
    $desc = ($video['description']) ? $video['description'] : '';
    $title = ($video['title']) ? $video['title'] : '';
    $genres = $video['genres'];
    $duration = $video['duration'];
    $year = $video['year'];
    $company_id = $video['company_id'];

    if (!empty($duration)) {
        if ($duration < 60) {
            $duration = $duration . ' sec';
        } else {
            $duration = round(($duration) / 60) . ' min';
        }
    }
    ?>
    <div class="video-content-div">
        <div class="custom-container container">
            <div class="video-player">
                <div class="player" data-video_id="<?php echo $video_id; ?>"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="custom-container container">
            <h2><?php echo $title; ?></h2>
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
    </div>
    <div class="custom-container container">
        <?php
        $class_array = [];
        // Display Recomendation section
        if ($dsp_theme_options['opt-related-section'] == 1) {
            $cnt = 0;
            if ($dsp_theme_options['opt-related-option'] == 'channel') {
                $type = 'channel';
                $related_id = get_post_meta($channel_id, 'dspro_channel_id', true);
            } else {
                $type = 'video';
                $related_id = $video_id;
            }
            include(locate_template('page-templates/templates-part/related-content.php'));
            $class_array[] = 'related_content';
        }

        $theme_function->slick_init_options($class_array, 'video');
        ?>
    </div>
    <?php
    $settings = [];
    $settings[] = 'companykey=' . $company_id;
    $settings[] = 'skin=' . ltrim($player_color, "#");
    $settings[] = 'autostart=' . $autoplay;
    $settings[] = 'muteonstart=' . $mute_on_load;
    $player_setting = '?targetelm=.player&' . implode('&', $settings)
    ?>
    <script src="<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>"></script>
    <?php
}
?>
<?php get_footer(); ?>