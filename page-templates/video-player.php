<?php
/**
 * Template Name: Video Player
 */
get_header();
?>

<?php
$dsp_api = new Dsp_External_Api_Request();
$video_slug = get_query_var('video_slug');
$channel_slug = get_query_var('channel_slug');
$player_color = (get_option('dsp_video_color_field')) ? get_option('dsp_video_color_field') : '#000000';
$mute_on_load = (get_option('dsp_video_muteload_field')) ? 'true' : 'false';
$autoplay = (get_option('dsp_video_autoplay_field')) ? 'true' : 'false';

if ($channel_slug) {
    $video = $dsp_api->get_video_by_slug_or_id($channel_slug, $video_slug);
}
if (!is_wp_error($channels)) {
    $video_id = $video['_id'];
    $desc = ($video['description']) ? $video['description'] : '';
    $title = ($video['title']) ? $video['title'] : '';
    $genres = $video['genres'];
    $duration = $video['duration'];
    $year = $video['year'];

    if (!empty($duration)) {
        if ($duration < 60) {
            $duration = $duration . ' sec';
        } else {
            $duration = round(($duration) / 60) . ' min';
        }
    }
    ?>
    <div class="row">
        <div class="container">
            <div class="video-player">
                <div class="player-content">
                    <div class="player-content-inner">
                        <div class="player" data-video_id="<?php echo $video_id; ?>"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="container">
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
    <?php
    $settings = [];
    $settings[] = 'skin=' . ltrim($player_color,"#");
    $settings[] = 'autostart=' . $autoplay;
    $settings[] = 'muteonstart=' . $mute_on_load;
    $player_setting = '?targetelm=.player&companykey=5ab1600597f815014b357891&' . implode('&', $settings)
    ?>
    <script src="<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>"></script>
    <?php
} else {
    ?>
    <div class="row">
        <div class="container">
            <p>Something went wrong!!</p>
        </div>
    </div>
    <?php
}
?>
<?php get_footer(); ?>