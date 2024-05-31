<?php
// get width, height and ration based on the option selectes on theme
if($dsp_theme_options['opt-continue-watch-image-size'] == '0') {
    $c_width = filter_var($dsp_theme_options['opt-continue-watch-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $c_height = filter_var($dsp_theme_options['opt-continue-watch-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
}
else {
    $c_width = filter_var($dsp_theme_options['opt-continue-watch-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $c_ratio_width = filter_var($dsp_theme_options['opt-continue-watch-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $c_ratio_height = filter_var($dsp_theme_options['opt-continue-watch-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $c_ratio = $c_ratio_height / $c_ratio_width;
}

$final_watch_again_data = array();
// loop through countinue watch data and add the required values into an array which we need to display on the page like title, link, banner, etc. 
$continue_watch_data = array();
foreach ($watch_list['data']['watch-again'] as $key => $video){
    $banner = (isset($video['thumb'])) ? 'https://defaultdspmedia.cachefly.net/images/' . $video['thumb'] : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
    if($dsp_theme_options['opt-continue-watch-image-size'] == '1'){
        $image_attributes = dsp_build_responsive_images( $banner, $c_width, $c_ratio );
        $continue_watch_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
        $continue_watch_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
    }
    $continue_watch_data[$key]['banner'] = $banner.'/'.$c_width;
    if(isset($c_height))
        $continue_watch_data[$key]['banner'] = $continue_watch_data[$key]['banner'].'/'.$c_height;
    if($dsp_theme_options['opt-display-webp-image'] == 1)
        $continue_watch_data[$key]['banner'] = $continue_watch_data[$key]['banner'].'?webp=1';

    $continue_watch_data[$key]['url'] = get_site_url() . '/video/' . $video['_id'];
    $continue_watch_data[$key]['title'] = $video['title'];

    $continue_watch_data[$key]['trim_title'] = ($dsp_theme_options['opt-continue-watch-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-continue-watch-title-trim-word']) : $video['title'];
    $continue_watch_data[$key]['trim_description'] = '';
    if(isset($video['description']) && !empty($video['description']))
        $continue_watch_data[$key]['trim_description'] = ($dsp_theme_options['opt-continue-watch-description-trim-word'] != 0) ? wp_trim_words($video['description'], $dsp_theme_options['opt-continue-watch-description-trim-word']) : $video['description'];
}
// assign all the data into a final array with the default image
$final_watch_again_data['watch_again_data'] = $continue_watch_data;
$final_watch_again_data['default_image'] = 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1/'.$c_width;
if(isset($c_height))
    $final_watch_again_data['default_image'] = $final_watch_again_data['default_image'].'/'.$c_height;

?>