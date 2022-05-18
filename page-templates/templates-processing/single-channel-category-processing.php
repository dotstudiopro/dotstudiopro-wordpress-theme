<?php

global $dsp_theme_options, $client_token;

$is_user_subscribed = false;
if (class_exists('Dotstudiopro_Subscription') && $client_token) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
        $is_user_subscribed = true;
    }
}

$theme_function = new Theme_Functions();
$channels = $theme_function->home_page_other_carousel($post->post_name, $dsp_theme_options['opt-category-channel-poster-type'], 'category');

// get width, height and ration based on the option selectes on theme
if( $dsp_theme_options['opt-category-image-size'] == '0' ) {
    $width = filter_var($dsp_theme_options['opt-channel-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-channel-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $width = filter_var($dsp_theme_options['opt-category-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-category-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-category-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
}

$final_channel_data = array();
// var used to display the number of rows for the category channels
$final_channel_data['number_of_row'] = $dsp_theme_options['opt-display-row'];


$channels_data = array();
// loop through channels data and add the required values into an array which we need to display on the page like title, link, banner, etc. 
if ($channels) {
    foreach ($channels as $key => $channel) {
        $channels_data[$key]['url'] = $channel['url'];
        $channels_data[$key]['title'] = $channel['title'];
        if (isset($channel['dspro_is_product']) && $channel['dspro_is_product'] == 1 && $is_user_subscribed == false){
            $channels_data[$key]['show_lock_icon'] = true;    
        }
        $banner = $channel['image'];
        if( $dsp_theme_options['opt-category-image-size'] == '1'){
            $image_attributes = dsp_build_responsive_images( $channel['image'], $width, $ratio );
            $channels_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
            $channels_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
        }
        $channels_data[$key]['banner'] = $banner.'/'.$width;
        if(isset($height))
            $channels_data[$key]['banner'] = $channels_data[$key]['banner'].'/'.$height;
        if($dsp_theme_options['opt-display-webp-image'] == 1){
            $channels_data[$key]['banner'] = $channels_data[$key]['banner'].'?webp=1';
        }
        $channels_data[$key]['trim_channel_title'] = ($dsp_theme_options['opt-channel-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-channel-title-trim-word']) : $channel['title'];
        $channels_data[$key]['trim_channel_description'] = ($dsp_theme_options['opt-channel-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-channel-description-trim-word']) : $channel['description'];
    }
}
// assign all the data into a final array with the default image
$final_channel_data['channels'] = $channels_data;
$final_channel_data['default_image'] = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/'.$width;
if(isset($height))
    $final_channel_data['default_image'] = $final_channel_data['default_image'].'/'.$height;

?>