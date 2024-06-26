<?php
global $dsp_theme_options, $client_token;
if (!$client_token) {
    wp_redirect('/');
    exit();
}
$dotstudio_api = new Dsp_External_Api_Request();
// Api call to get users mylist data
$all_channels = $dotstudio_api->get_user_watchlist($client_token);

// loop through mylist data if there is not any error or the data is not empty and add the required values into an array which we need to display on the page like title, link, banner, etc.
$final_my_list_page_data = array();
if (!is_wp_error($all_channels)) {
    if (!empty($all_channels['channels'])) {
        $channels_array = array();
        foreach ($all_channels['channels'] as $channel_key => $channel) {
            $channels_array[$channel_key]['channel_id'] = $channel['_id'];
            $channels_array[$channel_key]['title'] = $channel['title'];
            $channels_array[$channel_key]['link'] = '/channel/' . $channel['slug'];
            $channels_array[$channel_key]['banner'] = (isset($channel['spotlight_poster'])) ? $channel['spotlight_poster'] . '/240/360' : 'https://defaultdspmedia.cachefly.net/images/5bd9eb28d57fdf6513eb280b/240/360';
            if(isset($channel['parent_channel']) && !empty($channel['parent_channel'])){
                $channels_array[$channel_key]['channel_id'] = $channel['parent_channel']['_id'];
                $channels_array[$channel_key]['title'] = $channel['parent_channel']['title'];
                $channels_array[$channel_key]['link'] = '/channel/' . $channel['parent_channel']['slug'];
                $channels_array[$channel_key]['banner'] = (isset($channel['parent_channel']['spotlight_poster'])) ? $channel['parent_channel']['spotlight_poster'] . '/240/360' : 'https://defaultdspmedia.cachefly.net/images/5bd9eb28d57fdf6513eb280b/240/360';
            }
            if($dsp_theme_options['opt-display-webp-image'] == 1)
                $channels_array[$channel_key]['banner'] = $channels_array[$channel_key]['banner'].'?webp=1';
        }
        $final_my_list_page_data['channels'] = $channels_array;
        $final_my_list_page_data['default_image'] = 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1/240/360';
    }
}
?>