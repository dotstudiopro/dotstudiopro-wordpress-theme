<?php

global $client_token, $wp;

$video_id = $video_slug;

// condition to check if given video id is our content or not 
if (preg_match('/^[a-f\d]{24}$/i', $video_id)) {
    $video = $dsp_api->get_video_by_id($video_id);
} else {
    wp_redirect(home_url());
    exit();
}

if (is_wp_error($video)){
    wp_redirect(home_url());
    exit();
}

$bypass_channel_lock = isset($video['bypass_channel_lock']) ? $video['bypass_channel_lock'] : '';

$checkDefaultSubscriptionBehavior = $dsp_api->get_default_subscription_behavior();

// condition to check if default subscription is lock videos and user not subscribed then display the error message

if (!is_wp_error($checkDefaultSubscriptionBehavior) && !empty($checkDefaultSubscriptionBehavior)){
    if($checkDefaultSubscriptionBehavior['behavior'] == 'lock_videos' && $bypass_channel_lock != 'true' && $bypass_channel_lock != true){
        if (class_exists('Dotstudiopro_Subscription')) {
            $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
            $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
            if (is_wp_error($user_subscribe) || !$user_subscribe || (empty($user_subscribe['products']['svod'][0]['product']['id']) && empty($user_subscribe['products']['tvod'][0]['product']['id']))) {
                get_header();
                $display_direct_video = false;
            }
            else{
                $display_direct_video = true;
            }
        }
    }
    else{
        $display_direct_video = true;
    }
}
else{
   wp_redirect(home_url());
   exit();
}


// title,description and banner is used for meta tags
if (!is_wp_error($video) && !empty($video)):
    global $share_banner, $share_desc, $share_title;
    $share_desc = $desc = isset($video['description']) ? $video['description'] : '';
    $share_title = $title = isset($video['title']) ? $video['title'] : '';
    $share_banner = $banner = ($video['thumb']) ? $video['thumb'] : '';
endif;
get_header();

// get videos genres, duration, year, company_id, etc.
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

    // Get "recently watched" data for a video.
    $video_point = '';
    $get_video_data = $dsp_api->get_recent_viewed_data_video($client_token, $video_id);
    if (!is_wp_error($get_video_data) && !empty($get_video_data['data']['point'])) {
        $video_point = $get_video_data['data']['point'];
    }
}

?>