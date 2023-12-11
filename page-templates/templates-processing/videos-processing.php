<?php

global $client_token, $wp;

$class_array = array();
$cnt = 0;
$video_unlocked = $channel_unlocked = false;

$channel = get_page_by_path($channel_slug, OBJECT, 'channel');
$child_channels = $theme_function->is_child_channels($channel->ID);

// condition to check video slug in the url or id
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

// Api call to get video information based on the id
 
if (!empty($video_id))
    $video = $dsp_api->get_video_by_id($video_id);

// title,description and banner is used for meta tags
if (!is_wp_error($video) && !empty($video)):
    global $share_banner, $share_desc, $share_title;
    $share_desc = $desc = isset($video['description']) ? $video['description'] : '';
    $share_title = $title = isset($video['title']) ? $video['title'] : '';
    $share_banner = $banner = isset($video['thumb']) ? $video['thumb'] : get_post_meta($channel->ID, 'chnl_poster', true);
endif;

$channel_meta = get_post_meta($channel->ID);
$dsp_api = new Dsp_External_Api_Request();

// Code to check if channel is avialable on users country or not
if(isset($_SESSION['dsp_theme_country']) && !is_array($_SESSION['dsp_theme_country'])) {
    $country_code = $_SESSION['dsp_theme_country'];
}else{
    $country_code = $dsp_api->get_country();    
}
$dspro_channel_geo = unserialize($channel_meta['dspro_channel_geo'][0]);
$channel_geoblock = false;
if($country_code && !in_array("ALL", $dspro_channel_geo) && !in_array($country_code, $dspro_channel_geo) && !empty($dspro_channel_geo)){
    $channel_geoblock = true;
}

// Get the required video data and stored into an array.
$video_data = array();
$video_data['bypass_channel_lock'] = '';
if (!is_wp_error($video) && !empty($video)){
    $video_data['genres'] = isset($video['genres']) ? $video['genres'] : '';
    $video_data['duration'] = isset($video['duration']) ? $video['duration'] : '';
    $video_data['year'] = isset($video['year']) ? '(' . $video['year'] . ')' : '';
    $video_data['company_id'] = isset($video['company_id']) ? $video['company_id'] : '';
    $video_data['chnl_id'] = isset($main_channel_meta['chnl_id'][0]) ? $main_channel_meta['chnl_id'][0] : '';
    $video_data['dspro_channel_id'] = isset($main_channel_meta['dspro_channel_id'][0]) ? $main_channel_meta['dspro_channel_id'][0] : '';
    $video_data['bypass_channel_lock'] = isset($video['bypass_channel_lock']) ? $video['bypass_channel_lock'] : '';

    if (!empty($video_data['duration'])) {
        if ($video_data['duration'] < 60) {
            $video_data['duration'] = $video_data['duration'] . ' sec';
        } else {
            $video_data['duration'] = round(($video_data['duration']) / 60) . ' min';
        }
    }
    $banner = $banner.'/1300/731';
    if($dsp_theme_options['opt-display-webp-image'] == 1){
        $banner = $banner.'?webp=1';
    }
}

$settings = [];

$show_ads = true;

// Code to check if user subscribe to watch this video
$check_subscription_status = $dsp_api->check_subscription_status($client_token, get_post_meta($channel->ID, 'dspro_channel_id', true));
if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked'])):
    $video_unlocked = $channel_unlocked = false;
else:
    if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['ads_enabled']))
        $show_ads = false;
    $video_unlocked = $channel_unlocked = true;
endif;

// Code to check product is svod or tvod product
$svod_products = array();
$tvod_products = array();
if (class_exists('Dotstudiopro_Subscription')) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $check_product_by_channel = $dsp_subscription_object->getProductsByChannel(get_post_meta($channel->ID, 'dspro_channel_id', true));
    if (!is_wp_error($check_product_by_channel) && !empty($check_product_by_channel['products'])){
       $svod_products = array_values(array_filter($check_product_by_channel['products'], function($cp) {
            return $cp && !empty($cp['product_type']) && $cp['product_type'] === 'svod';
        }));
       $tvod_products = array_values(array_filter($check_product_by_channel['products'], function($tcp) {
            return $tcp && !empty($tcp['product_type']) && $tcp['product_type'] === 'tvod';
        }));
    }
}

$player_setting = '?targetelm=.player&' . implode('&', $settings);

// Get "recently watched" data for a video.
$video_point = '';
if (!empty($client_token)) {
    $get_video_data = $dsp_api->get_recent_viewed_data_video($client_token, $video_id);
    if (!is_wp_error($get_video_data) && !empty($get_video_data['data']['point'])) {
        $video_point = $get_video_data['data']['point'];
    }
}

// Code to display channel image for the selected video
$channel_meta = get_post_meta($channel->ID);
$channel_img = 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1/240/360';
$channel_id = $channel_meta['chnl_id'][0];
if ($channel_meta['chnl_spotlight_poster'][0]) {
    $channel_img = $channel_meta['chnl_spotlight_poster'][0] . '/240/360';
}
if($dsp_theme_options['opt-display-webp-image'] == 1){
    $channel_img = $channel_img.'?webp=1';
}
$p_channel_id = 0;
if (!empty($p_channel_slug)) {
    $p_channel = get_page_by_path($p_channel_slug, OBJECT, 'channel');
    $p_channel_meta = get_post_meta($p_channel->ID);
    $p_channel_id = $p_channel_meta['chnl_id'][0];
}

// Code to display Add to mylist button or Remove form mylist button
if ($client_token) {
    $obj = new Dsp_External_Api_Request();
    $list_channel = $obj->get_user_watchlist($client_token);
    $in_list = array();
    if (!is_wp_error($list_channel) && $list_channel['channels'] && !empty($list_channel['channels'])) {
        foreach ($list_channel['channels'] as $ch) {
            $in_list[] = $ch['_id'];
        }
    }
    if (in_array($channel_id, $in_list)) {
        $display_remove_from_my_list_button = true;
    }
}

// code to add next and previous video link
if (!$child_channels) {
    $npvideos = $theme_function->show_videos($channel, 'other_carousel', null, $p_channel_slug);
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
        $npvideos = $theme_function->show_videos($single_channel, 'other_carousel', null, $single_channel->post_name);
        foreach ($npvideos as $key => $npvideo) {
            if (!empty($npvideo['_id']) && !empty($video_id) && $npvideo['_id'] == $video_id) {
                $next_video[] = isset($npvideos[$key + 1]) ? $npvideos[$key + 1] : '';
                $prev_video[] = isset($npvideos[$key - 1]) ? $npvideos[$key - 1] : '';
            }
        }
    }
}

// get width, height and ration based on the option selectes on theme
if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
    $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
}

// this array is used to other video rails for selected video channel
$current_channel_data = array();
if ($child_channels) {
    foreach ($child_channels as $c_key => $child_channel) {
        $single_channel = get_page_by_path($child_channel, OBJECT, 'channel');
        $current_channel_data[$c_key]['videos'] = $videos = $theme_function->show_videos($single_channel, 'other_carousel', null, $single_channel->post_name);
        if ($videos) {
            $current_channel_data[$c_key]['title'] = $single_channel->post_title;
        }
    }
} else {
    $current_channel_data[0]['videos'] = $videos = $theme_function->show_videos($channel, 'other_carousel', null, $p_channel_slug);
    if ($videos) {
        $current_channel_data[0]['title'] = $channel->post_title;
    }
}

// loop through channels data and add the required values into an array which we need to display on the page like videos, title, channel_unlocked, etc.
// Basically, this array is used to other video rails for child channels
if (!empty($p_channel_slug)) {
    $parent_channel = get_page_by_path($p_channel_slug, OBJECT, 'channel');
    $parent_channel_meta = get_post_meta($parent_channel->ID);
    $parant_child_channels = $theme_function->is_child_channels($parent_channel->ID);
    $check_subscription_status_single = $dsp_api->check_subscription_status($client_token, $parent_channel_meta['dspro_channel_id'][0]);
    if ($parant_child_channels) {
        if (($key = array_search($channel_slug, $parant_child_channels)) !== false) {
            unset($parant_child_channels[$key]);
        }
        $channel_data = array();
        foreach ($parant_child_channels as $p_child_key => $parant_child_channel) {
            $single_channel = get_page_by_path($parant_child_channel, OBJECT, 'channel');
            $single_channel_meta = get_post_meta($single_channel->ID);
            if (!is_wp_error($check_subscription_status_single) && !empty($check_subscription_status_single['childchannels'])){
                $checkIfChannelExists = array_search($single_channel_meta['dspro_channel_id'][0], array_column($check_subscription_status_single['childchannels'],'dspro_id'));
                if($checkIfChannelExists || $checkIfChannelExists == 0){
                    if (empty($check_subscription_status_single['childchannels'][$checkIfChannelExists]['unlocked']))
                        $channel_data[$p_child_key]['channel_unlocked'] = false;
                    else
                        $channel_data[$p_child_key]['channel_unlocked'] = true;
                }
                else{
                    $channel_data[$p_child_key]['channel_unlocked'] = true;
                }
            }
            else{
                $channel_data[$p_child_key]['channel_unlocked'] = true;
            }
            $channel_data[$p_child_key]['videos'] = $videos = $theme_function->show_videos($single_channel, 'other_carousel', null, $p_channel_slug);
            if ($videos) {
                $channel_data[$p_child_key]['title'] = $single_channel->post_title;
            }
        }
    }
}

// code to pass the releted channel or video id based on the option selected for related content
if ($dsp_theme_options['opt-related-section'] == 1) {
    if ($dsp_theme_options['opt-related-option'] == 'channel') {
        $type = 'channel';
        $related_id = get_post_meta($channel->ID, 'dspro_channel_id', true);
    } else {
        $type = 'video';
        $related_id = $theme_function->first_video_id($channel->ID);
    }
    
}



?>