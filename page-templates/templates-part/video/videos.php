<?php
/**
 * condition to check video slug in the url or id
 */
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
                <div class="player"></div>
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

    <script src="<?php echo'https://player.dotstudiopro.com/player/' . $video_id . $player_setting; ?>"></script>
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

    <?php if (!empty($prev_video[0])) { ?>
        <div class="prev_video_link">
            <a href="<?php echo $prev_video[0]['url']; ?>" title="<?php echo $prev_video[0]['title']; ?>">
                <i class="fa fa-angle-left pull-left"></i>
                <div class="simple-navigation-item-content">
                    <span>Previous</span>
                    <h4><?php echo $prev_video[0]['title']; ?></h4>
                </div>
            </a>
        </div>    
    <?php } ?>



    <?php if (!empty($next_video[0])) { ?>
        <div class="next_video_link">
            <a href="<?php echo $next_video[0]['url']; ?>" title="<?php echo $next_video[0]['title']; ?>">
                <i class="fa fa-angle-right pull-right"></i>
                <div class="simple-navigation-item-content">
                    <span>Next</span>
                    <h4><?php echo $next_video[0]['title']; ?></h4>
                </div>
            </a>
        </div>
    <?php } ?>


    <?php
endif;

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
            <div class="col-sm-12 no-gutters">
                <h2 class="post-title"><?php echo $single_channel->post_title; ?></h2>
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
        <div class="col-sm-12 no-gutters">
            <h2 class="post-title"><?php echo $channel->post_title; ?></h2>
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
    array_push($class_array, 'related_content');
}

$theme_function->slick_init_options($class_array, 'video');
?>
<script>
    jQuery(document).ready(function (e) {
        var dspPlayerCheck = setInterval(function () {
            if (typeof dotstudiozPlayer !== "undefined" && typeof dotstudiozPlayer.player !== "undefined") {
                clearInterval(dspPlayerCheck);
                dotstudiozPlayer.player.on("ended", function () {
                    var nextHref = "<?php echo $next_video[0]['url']; ?>";
                    if (nextHref.length > 0)
                        window.location.href = nextHref;
                });
            }
        }, 250);
    });
</script>