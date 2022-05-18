<?php

$theme_function = new Theme_Functions();

global $dsp_theme_options, $client_token;

// Code to check user is subscribed or not
$is_user_subscribed = false;
if (class_exists('Dotstudiopro_Subscription') && $client_token) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
        $is_user_subscribed = true;
    }
}

// Api call to get recommendation data
$recommendation_content = $theme_function->get_recommendation_content($type, $related_id);

// get width, height and ration based on the option selectes on theme
if( $dsp_theme_options['opt-related-image-size'] == '0' ) {
    $width = filter_var($dsp_theme_options['opt-related-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-related-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $width = filter_var($dsp_theme_options['opt-related-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-related-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-related-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
}

$final_related_content_data = array();

// add a class when we selected layout as text &description or only text from the theme option
$slide_text_class = '';
if ($dsp_theme_options['opt-related-layout-slider-content'] == 1) {
    $slide_text_class .= 'slide-text-dec';
} elseif ($dsp_theme_options['opt-related-layout-slider-content'] == 3) {
    $slide_text_class .= 'slide-text';
}

$final_related_content_data['slide_text_class'] = $slide_text_class;

// loop through recommendation data and add the required values into an array which we need to display on the page like title, link, banner, etc.
$related_content_data = array();
if (!empty($recommendation_content)){
    foreach ($recommendation_content as $key => $channel){
        if (isset($channel['is_product']) && $channel['is_product'] == 1 && $is_user_subscribed == false){
            $related_content_data[$key]['show_lock_icon'] = true;
        }
        if( $dsp_theme_options['opt-related-image-size'] == '1'){
            $image_attributes = dsp_build_responsive_images( $channel['image'], $width, $ratio );
            $related_content_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
            $related_content_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
        }
        $related_content_data[$key]['image'] = $channel['image'].'/'.$width;
        if(isset($height))
            $related_content_data[$key]['image'] = $related_content_data[$key]['image'].'/'.$height;
        if($dsp_theme_options['opt-display-webp-image'] == 1)
            $related_content_data[$key]['image'] = $related_content_data[$key]['image'].'?webp=1';
        $related_content_data[$key]['channel_title'] = $channel['title'];
        $related_content_data[$key]['channel_url'] = $channel['url'];
        $related_content_data[$key]['trim_channel_title'] = ($dsp_theme_options['opt-related-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-related-title-trim-word']) : $channel['title'];
        $related_content_data[$key]['trim_channel_description'] = ($dsp_theme_options['opt-related-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-related-description-trim-word']) : $channel['description'];
    }
}
// assign all the data into a final array with the default image
$final_related_content_data['related_content_data'] = $related_content_data;
$final_related_content_data['default_image'] = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/'.$width;
if(isset($height))
    $final_related_content_data['default_image'] = $final_related_content_data['default_image'].'/'.$height;


?>