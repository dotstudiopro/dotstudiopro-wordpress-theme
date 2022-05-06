<?php

global $dsp_theme_options;

$slide_text_class = '';
if ($dsp_theme_options['opt-layout-slider-content'] == 1) {
    $slide_text_class .= 'slide-text-dec';
} elseif ($dsp_theme_options['opt-layout-slider-content'] == 3) {
    $slide_text_class .= 'slide-text';
}
$lock_video_class = '';
if (isset($channel_unlocked) && $channel_unlocked == 0)
    $lock_video_class = 'lock-overlay';

$final_videos_data = array();
$final_videos = array();

foreach ($videos as $key => $video){
    $final_videos[$key]['class'] = '';
    if (!preg_match('/^[a-f\d]{24}$/i', $video_slug)) {
        if ($video_slug && $video['slug'] == $video_slug)
            $final_videos[$key]['class'] = 'active';
    } else {
        if ($video_slug && $video['id'] == $video_slug)
            $final_videos[$key]['class'] = 'active';
    }

    $banner = $video['image'];
    if($dsp_theme_options['opt-channel-video-image-size'] == '1'){
        $image_attributes = dsp_build_responsive_images( $banner, $width, $ratio );
        $final_videos[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
        $final_videos[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
    }
    $final_videos[$key]['banner'] = $banner.'/'.$width;
    if(isset($height))
        $final_videos[$key]['banner'] = $final_videos[$key]['banner'].'/'.$height;
    if($dsp_theme_options['opt-display-webp-image'] == 1)
        $final_videos[$key]['banner'] = $final_videos[$key]['banner'].'?webp=1';

    $final_videos[$key]['url'] = $video['url'];
    $final_videos[$key]['title'] = $video['title'];
    $final_videos[$key]['trim_title'] = ($dsp_theme_options['opt-channel-video-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-channel-video-title-trim-word']) : $video['title'];
    $final_videos[$key]['trim_description'] = ($dsp_theme_options['opt-channel-video-description-trim-word'] != 0) ? wp_trim_words($video['description'], $dsp_theme_options['opt-channel-video-description-trim-word']) : $video['description'];
    $final_videos[$key]['bypass_channel_lock'] = $video['bypass_channel_lock'];
}

$final_videos_data['final_videos'] = $final_videos;
$final_videos_data['default_image'] = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/'.$width;
if(isset($height))
    $final_videos_data['default_image'] = $final_videos_data['default_image'].'/'.$height;

    

?>