<?php

/**
 * Template Name: Video Player
 */
global $dsp_theme_options, $client_token;

$dsp_api = new Dsp_External_Api_Request();
$theme_function = new Theme_Functions();

$video_slug = get_query_var('video_slug');
$channel_slug = get_query_var('channel_slug');
$p_channel_slug = get_query_var('p_channel_slug');
$player_color = (get_option('dsp_video_color_field')) ? get_option('dsp_video_color_field') : '#000000';
$mute_on_load = (get_option('dsp_video_muteload_field')) ? true : false;
$autoplay = (get_option('dsp_video_autoplay_field')) ? true : false;
$video_id = '';
$video = '';

if (!empty($channel_slug) && !empty($video_slug)) {
    $channel_object = get_page_by_path($channel_slug, OBJECT, 'channel');
    if ($p_channel_slug) {
        $p_channel_object = get_page_by_path($p_channel_slug, OBJECT, 'channel');
        $main_channel_meta = get_post_meta($p_channel_object->ID);
    } else {
        $main_channel_meta = get_post_meta($channel_object->ID);
    }
    $chnl_title = $channel_object->post_title;
    $dspro_channel_id = isset($main_channel_meta['dspro_channel_id'][0]) ? $main_channel_meta['dspro_channel_id'][0] : '';
    // Code to check channel is avilable on web platform or not
    $matched_categories = $theme_function->get_categories_by_channel($dspro_channel_id);
    if ($matched_categories->have_posts()) {
        $plateform_web = true;
    }else{
        $plateform_web = false;
    }

    if ($plateform_web)
        include(locate_template('page-templates/templates-part/video/videos.php'));
    else{
        get_header();
        include(locate_template('page-templates/templates-part/not-in-web-platform.php'));
    }
}
else if (!empty($video_slug))
    include(locate_template('page-templates/templates-part/video/direct-videos.php'));
else
    get_template_part(404);


get_footer();
?>
