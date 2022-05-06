<?php 

global $dsp_theme_options, $client_token, $post, $wp, $share_banner, $share_desc, $share_title;
$video_slug = '';

$channel_meta = get_post_meta(get_the_ID());

$company_id = "";
$chnl_id = isset($channel_meta['chnl_id'][0]) ? $channel_meta['chnl_id'][0] : '';
$dspro_channel_id = isset($channel_meta['dspro_channel_id'][0]) ? $channel_meta['dspro_channel_id'][0] : '';

$share_title = $title = get_the_title();
$share_desc = $desc =  apply_filters('the_content', get_post_field('post_content', get_the_ID()));
//$share_banner = $poster = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
if($dsp_theme_options['opt-channel-poster-type'] == 'poster'){
   $share_banner = $poster = $channel_meta['chnl_poster'][0];
}
elseif($dsp_theme_options['opt-channel-poster-type'] == 'spotlight_poster'){
    $share_banner = $poster = $channel_meta['chnl_spotlight_poster'][0];
}
else{
    $share_banner = $poster = $channel_meta['chnl_wallpaper'][0];
}

$theme_function = new Theme_Functions();
// Code to check if user subscribe to watch this channel
$dsp_api = new Dsp_External_Api_Request();
if(isset($_SESSION['dsp_theme_country']) && !is_array($_SESSION['dsp_theme_country'])) {
    $country_code = $_SESSION['dsp_theme_country'];
}else{
    $country_code = $dsp_api->get_country();    
}
$channel_geoblock = false;
$dspro_channel_geo = unserialize($channel_meta['dspro_channel_geo'][0]);
if($country_code && !in_array("ALL", $dspro_channel_geo) && !in_array($country_code, $dspro_channel_geo) && !empty($dspro_channel_geo)){
    $channel_geoblock = true;
}

// Code to check channel is avilable on web platform or not
if (isset($channel_meta['chnl_categories'][0])) {
    $categories = array_filter(explode(',', $channel_meta['chnl_categories'][0]));
    $chnl_title = get_the_title();

    // Condition to check platform is web true or not for this channel category
    $plateform_web = false;
    foreach ($categories as $channel_cat) {
        $args = array('name' => $channel_cat, 'post_type' => 'channel-category');
        $cache_key = "single_video_categories_" . $channel_cat;
        $slug_query = $theme_function->query_categories_posts($args, $cache_key);
        if ($slug_query) {
            $plateform_web = true;
            break;
        }
    }
} else {
    $chnl_title = get_the_title();
    $plateform_web = true;
}

// Code to check parent channel is unlock or not
$check_subscription_status = $dsp_api->check_subscription_status($client_token, $channel_meta['dspro_channel_id'][0]);
if (!is_wp_error($check_subscription_status) && empty($check_subscription_status['unlocked']))
    $parant_channel_unlocked = false;
else
    $parant_channel_unlocked = true;


// Code to check product is svod or tvod product
$svod_products = array();
$tvod_products = array();
if (class_exists('Dotstudiopro_Subscription')) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $check_product_by_channel = $dsp_subscription_object->getProductsByChannel($channel_meta['dspro_channel_id'][0]);
    if (!is_wp_error($check_product_by_channel) && !empty($check_product_by_channel['products'])){
       $svod_products = array_values(array_filter($check_product_by_channel['products'], function($cp) {
            return $cp && !empty($cp['product_type']) && $cp['product_type'] === 'svod';
        }));
       $tvod_products = array_values(array_filter($check_product_by_channel['products'], function($tcp) {
            return $tcp && !empty($tcp['product_type']) && $tcp['product_type'] === 'tvod';
        }));
    }
}

$childchannels = $theme_function->is_child_channels(get_the_ID());
//$channel_banner_image = ($dsp_theme_options['opt-channel-poster-type'] == 'poster') ? $channel_meta['chnl_poster'][0] : $channel_meta['chnl_spotlight_poster'][0];
if($dsp_theme_options['opt-channel-poster-type'] == 'poster'){
   $channel_banner_image = $poster = $channel_meta['chnl_poster'][0];
}
elseif($dsp_theme_options['opt-channel-poster-type'] == 'spotlight_poster'){
    $channel_banner_image = $poster = $channel_meta['chnl_spotlight_poster'][0];
}
else{
    $channel_banner_image = $poster = $channel_meta['chnl_wallpaper'][0];
}

$banner = ($channel_banner_image) ? $channel_banner_image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
$banner = $banner.'/1920/900';
if($dsp_theme_options['opt-display-webp-image'] == 0)
    $banner = $banner.'?webp=1';

if(!empty($channel_meta['chnl_logo'][0])){
    $channel_logo = $channel_meta['chnl_logo'][0].'/400';  
    if($dsp_theme_options['opt-display-webp-image'] == 0)
        $channel_logo = $channel_logo.'?webp=1';  
}

$first_child_id = get_post(get_the_ID(), OBJECT);
$p_channel_id = 0;
if (!empty($first_child_id)) {
    $p_channel_meta = get_post_meta($first_child_id->ID);
    $p_channel_id = $p_channel_meta['chnl_id'][0];
    if(!isset($p_channel_meta['chnl_child_channels'][0]) && empty($p_channel_meta['chnl_child_channels'][0])){
        $p_channel_id = 0;
    }
}
if ($childchannels) {
    $first_child_id = get_page_by_path($childchannels[0], OBJECT, 'channel');
    $channel_videos = $theme_function->get_channel_videos($first_child_id->ID);
    $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
    $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/' . $first_child_id->post_name . '/video/' . $videoSlug;
} else {
    $channel_videos = $theme_function->get_channel_videos(get_the_ID());
    $videoSlug = ($channel_videos[0]['slug']) ? $channel_videos[0]['slug'] : $channel_videos[0]['_id'];
    $first_video_url = get_site_url() . '/channel/' . $post->post_name . '/video/' . $videoSlug;
}

// Code to set live strem date
$live_stream_start_time = isset($channel_meta['chnl_live_stream_start_time'][0]) ? $channel_meta['chnl_live_stream_start_time'][0] : '';
if(!empty($live_stream_start_time)){
    $timm = strtotime($live_stream_start_time);
    $convert_live_stream_start_time_to_user_time = get_date_from_gmt( date( 'Y-m-d H:i:s', $timm ), 'Y/m/d H:i:s' );
    $convert_live_stream_start_time = $live_stream_start_time;
    $current_time = current_time('F j, Y H:i a');   
}

if ($first_child_id) {
    if ($client_token) {
        $channel_id = get_post_meta($first_child_id->ID, 'chnl_id', true);
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
}

$trailer_id = '';
$video_id = $theme_function->first_video_id(get_the_ID());
if (!empty($video_id)) {
    $video = $dsp_api->get_video_by_id($video_id);
    if (!is_wp_error($video) && !empty($video)) {
        if (isset($video['teaser_trailer']) && !empty($video['teaser_trailer'])) {
            $trailer_id = $video['teaser_trailer']['_id'];
            $company_id = isset($video['company_id']) ? $video['company_id'] : '';
            $mute_on_load = (get_option('dsp_video_muteload_field')) ? true : false;
        }
    }
}

if( $dsp_theme_options['opt-channel-video-image-size'] == '0' ) {
    $width = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-channel-video-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $width = filter_var($dsp_theme_options['opt-channel-video-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-channel-video-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
}

if ($dsp_theme_options['opt-related-option'] == 'channel') {
    $type = 'channel';
    $related_id = get_post_meta(get_the_ID(), 'dspro_channel_id', true);
} else {
    $type = 'video';
    $related_id = $theme_function->first_video_id(get_the_ID());
}

$channel_data = array();
if (!$childchannels) {
    $channel_data[0]['videos'] = $theme_function->show_videos($post, 'other_carousel');
    $channel_data[0]['channel_unlocked'] = $parant_channel_unlocked;
    $channel_data[0]['title'] = get_the_title();
}else{
    $p_channel_slug = $post->post_name;
    foreach ($childchannels as $key => $channel) {
        $channel_data[$key]['channel_unlocked'] = '';
        $single_channel = get_page_by_path($channel, OBJECT, 'channel');
        $single_channel_meta = get_post_meta($single_channel->ID);
        if (!is_wp_error($check_subscription_status) && !empty($check_subscription_status['childchannels'])){
            $checkIfChannelExists = array_search($single_channel_meta['dspro_channel_id'][0], array_column($check_subscription_status['childchannels'],'dspro_id'));
            if($checkIfChannelExists || $checkIfChannelExists == 0){
                if (empty($check_subscription_status['childchannels'][$checkIfChannelExists]['unlocked']))
                    $channel_data[$key]['channel_unlocked'] = false;
                else
                    $channel_data[$key]['channel_unlocked'] = true;
            }
            else{
                $channel_data[$key]['channel_unlocked'] = true;
            }
        }
        else{
            $channel_data[$key]['channel_unlocked'] = true;
        }
        $channel_data[$key]['videos'] = $theme_function->show_videos($single_channel, 'other_carousel', null, $p_channel_slug);
        $channel_data[$key]['title'] =$single_channel->post_title;
    }
}

?>