<?php

if (!is_wp_error($video) && !empty($video)):
    global $share_banner, $share_desc, $share_title;
    $share_desc = $desc = isset($video['description']) ? $video['description'] : '';
    $share_title = $title = isset($video['title']) ? $video['title'] : '';
    $share_banner = $banner = ($video['thumb']) ? $video['thumb'] : '';
endif;
get_header();

if (!is_wp_error($video) && !empty($video)){
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
}

?>