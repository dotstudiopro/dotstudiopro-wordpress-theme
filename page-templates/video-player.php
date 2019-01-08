<?php

/**
 * Template Name: Video Player
 */
get_header();

global $dsp_theme_options, $client_token;

$dsp_api = new Dsp_External_Api_Request();
$theme_function = new Theme_Functions();

$video_slug = get_query_var('video_slug');
$channel_slug = get_query_var('channel_slug');
$p_channel_slug = get_query_var('p_channel_slug');
$player_color = (get_option('dsp_video_color_field')) ? get_option('dsp_video_color_field') : '#000000';
$mute_on_load = (get_option('dsp_video_muteload_field')) ? 'true' : 'false';
$autoplay = (get_option('dsp_video_autoplay_field')) ? 'true' : 'false';
$video_id = '';
$video = '';

if (!empty($channel_slug) && !empty($video_slug)) {
    $channel_object = get_page_by_path($channel_slug, OBJECT, 'channel');
    if ($p_channel_slug) {
        $p_channel_object = get_page_by_path($p_channel_slug, OBJECT, 'channel');
        $channel_meta = get_post_meta($p_channel_object->ID);
    } else {
        $channel_meta = get_post_meta($channel_object->ID);
    }
    $chnl_title = $channel_object->post_title;
    if (isset($channel_meta['chnl_categories'][0])) {
        $categories = array_filter(explode(',', $channel_meta['chnl_categories'][0]));
        foreach ($categories as $channel_cat) {
            $args = array('name' => $channel_cat, 'post_type' => 'channel-category');
            $slug_query = new WP_Query($args);
            if ($slug_query->have_posts()) {
                $plateform_web = true;
                break;
            }
        }
    } else {
        $plateform_web = true;
    }
    if ($plateform_web)
        include(locate_template('page-templates/templates-part/video/videos.php'));
    else
        include(locate_template('page-templates/templates-part/not-in-web-plateform.php'));
}
else if (!empty($video_slug))
    include(locate_template('page-templates/templates-part/video/direct-videos.php'));
else
    get_template_part(404);


get_footer();
?>
