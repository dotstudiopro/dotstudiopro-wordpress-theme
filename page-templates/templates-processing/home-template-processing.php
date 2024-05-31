<?php
global $dsp_theme_options, $client_token;
$theme_function = new Theme_Functions();
$dsp_api = new Dsp_External_Api_Request();
$main_carousel = $theme_function->home_page_main_carousel();
$main_carousel_width = filter_var($dsp_theme_options['opt-main-home-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
$main_carousel_height = filter_var($dsp_theme_options['opt-main-home-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
// Api call to get home page data
$homepageAPI = $dsp_api->homepage($client_token);
$homepageData = [];
if(!is_wp_error($homepageAPI)){
    $homepageData = $homepageAPI['homepage'];
}

$final_homepage_data = array();

// Get Home page Main carousal Data
if ($main_carousel) {
    $main_carousel_data = array();
    foreach ($main_carousel as $main_carousel_key => $slide) {
        $main_carousel_data[$main_carousel_key]['title'] = ($dsp_theme_options['opt-homepage-main-title-trim-word'] != 0) ? wp_trim_words($slide['title'], $dsp_theme_options['opt-homepage-main-title-trim-word']) : $slide['title'];
        $main_carousel_data[$main_carousel_key]['description'] = ($dsp_theme_options['opt-homepage-main-description-trim-word'] != 0) ? wp_trim_words($slide['description'], $dsp_theme_options['opt-homepage-main-description-trim-word']) : $slide['description'];
        $main_carousel_data[$main_carousel_key]['image'] = $slide['image'] . '/' . $main_carousel_width . '/' . $main_carousel_height;
        if($dsp_theme_options['opt-display-webp-image'] == 1)
            $main_carousel_data[$main_carousel_key]['image'] = $main_carousel_data[$main_carousel_key]['image'] .'?webp=1';
        $main_carousel_data[$main_carousel_key]['url'] = $slide['url'];
    }
    $final_homepage_data['main_carousel'] = $main_carousel_data;
}

// get width, height and ration based on the option selectes on theme

$home = get_page_by_path($dsp_theme_options['opt-home-carousel'], OBJECT, 'channel-category');
if($dsp_theme_options['opt-home-image-size'] == "0") {
    $width = filter_var($dsp_theme_options['opt-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
}
else {
    $width = filter_var($dsp_theme_options['opt-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
}

// Get Home page Secondary carousal Data

$secondary_carousel_data = array();

foreach ($homepageData as $homepagedata_key => $data) {
    $category_slug = $data['category']['slug'];
    if($category_slug == $dsp_theme_options['opt-home-carousel']){
        continue;
    }
    $channels = $theme_function->home_page_other_carousel_with_api($data['channels'], $dsp_theme_options['opt-carousel-poster-type']);
    if ($channels) {
        $secondary_carousel_data[$homepagedata_key]['category_slug'] = $category_slug;
        $secondary_carousel_data[$homepagedata_key]['category_url'] = '/channel-category/' . $data['category']['slug'];
        $secondary_carousel_data[$homepagedata_key]['category_name'] = $data['category']['name'];
        $category_channels = array();
        foreach ($channels as $channel_key => $channel) {
            if (isset($channel['channel_unlock']) && $channel['channel_unlock'] == false && $channel['bypass_channel_lock'] != true && $channel['bypass_channel_lock'] != 'true'){
                $category_channels[$channel_key]['show_lock_icon'] = true;
            }
            if( $dsp_theme_options['opt-home-image-size'] == '1'){
                $image_attributes = dsp_build_responsive_images( $channel['image'], $width, $ratio );
                $category_channels[$channel_key]['image_attributes_srcset'] = $image_attributes['srcset'];
                $category_channels[$channel_key]['image_attributes_sizes'] = $image_attributes['sizes'];
            }
            $category_channels[$channel_key]['image'] = $channel['image'].'/'.$width;
            if(isset($height))
                $category_channels[$channel_key]['image'] = $category_channels[$channel_key]['image'].'/'.$height;
            if($dsp_theme_options['opt-display-webp-image'] == 1)
                $category_channels[$channel_key]['image'] = $category_channels[$channel_key]['image'].'?webp=1';
            $category_channels[$channel_key]['channel_title'] = $channel['title'];
            $category_channels[$channel_key]['channel_url'] = $channel['url'];
            $category_channels[$channel_key]['trim_channel_title'] = ($dsp_theme_options['opt-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-title-trim-word']) : $channel['title'];
            $category_channels[$channel_key]['trim_channel_description'] = ($dsp_theme_options['opt-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-description-trim-word']) : $channel['description'];
        }
        $secondary_carousel_data[$homepagedata_key]['channels'] = $category_channels;
    }
}
// assign all the data into a final array with the default image
$final_homepage_data['secondary_carousel_data'] = $secondary_carousel_data;
$final_homepage_data['default_image'] = 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1/'.$width;
if(isset($height))
    $final_homepage_data['default_image'] = $final_homepage_data['default_image'].'/'.$height;

?>