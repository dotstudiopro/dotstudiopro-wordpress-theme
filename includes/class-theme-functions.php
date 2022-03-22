<?php

/**
 * The file extends the Walker_Nav_Menu class to customize the menu option;
 * @since 1.0.0
 */
class Theme_Functions {

    private $external_api_class;
    private $country;

    function __construct() {
        $external_api_class = new Dsp_External_Api_Request;
        // Start the PHP session so we can save the country and don't
        // need to constantly do the call to get a country
        if (!session_id()) {
            session_start();
        }
        // Check our session variable for a country, so we can avoid
        // the API call; we check for an array as an earlier bug set
        // this value as an array, and we don't want/need that
        if(isset($_SESSION['dsp_theme_country']) && !is_array($_SESSION['dsp_theme_country'])) {
            $this->country = $_SESSION['dsp_theme_country'];
            return;
        }
        // Call the API, store the country in the session
        $this->country = $external_api_class->get_country();
        if (empty($this->country) || is_wp_error($this->country)) {
            // Default to no visibility; I think we should add in
            // an option later to set a default country or none
            $this->country = "NONE";
            return;
        }
        $_SESSION['dsp_theme_country'] = $this->country;
    }

    /**
     * home page main carousel function
     * @since 1.0.0
     *
     * @global type $dsp_theme_options
     * @return type
     */
    public function home_page_main_carousel() {

        global $dsp_theme_options;
        $channels_cache_key = "home_page_main_carousel_channels_" . $this->country;
        $show_channels_cache_key = "home_page_main_carousel_show_channels_" . $this->country;
        $show_videos_cache_key = "home_page_main_carousel_show_videos_" . $this->country;
        $response = array();

        $main_carousel_category = $dsp_theme_options['opt-home-carousel'];

        $transient_channels = get_transient( $channels_cache_key );

        $channels = null;

        if ($transient_channels) {
            $channels = $transient_channels;
        } else {
            $channels = $this->get_category_channels($main_carousel_category);
            set_transient( $channels_cache_key, $channels, 3600 );
        }

        if ($channels) {

            $total_channels = count($channels);

            $poster_type = $dsp_theme_options['opt-poster-type'];
            if ($total_channels > 1) {

                $transient_show_channels = get_transient( $show_channels_cache_key );
                if ($transient_show_channels) return $transient_show_channels;

                $show_channels = $this->show_channels($channels, 'main_carousel', $main_carousel_category, $poster_type, null);
                if (!empty($show_channels)) set_transient( $show_channels_cache_key, $show_channels, 3600 );
                return $show_channels;

            } else {

                $transient_show_videos = get_transient( $show_videos_cache_key );
                if ($transient_show_videos) return $transient_show_videos;

                $child_channels = $this->is_child_channels(array_values($channels)[0]->ID);
                if ($child_channels) {
                    $show_videos = $this->show_videos(array_values($channels)[0], 'main_carousel', $main_carousel_category, array_values($channels)[0]->post_name, null);
                }
                else{
                 $show_videos = $this->show_videos(array_values($channels)[0], 'main_carousel', $main_carousel_category, null, null);
                }
                if (!empty($show_videos)) set_transient( $show_videos_cache_key, $show_videos, 3600 );
                return $show_videos;

            }
        }
    }

    /**
     * home page other carousel function
     * @since 1.0.0
     *
     * @global type $dsp_theme_options
     * @return type
     */
    public function home_page_other_carousel($category_name, $poster_type = NULL, $template = '') {

        global $dsp_theme_options;
        // Figure out how many slides we need to load

        $cnt = null;
        if($template == 'home-template'){
            $cnt = $dsp_theme_options['opt-slick-home-slidestoload'];
            if (empty($cnt)) $cnt = $dsp_theme_options['opt-slick-home-slidetoscroll'] * 2;
        }
        $channels_cache_key = "home_page_other_carousel_channels_" . $category_name . "_" . $this->country . "_total_" . $cnt;
        $show_channels_cache_key = "home_page_other_carousel_show_channels_" . $category_name . "_" . $this->country . "_total_" . $cnt;
        $show_videos_cache_key = "home_page_other_carousel_show_videos_" . $category_name . "_" . $this->country . "_total_" . $cnt;
        $response = array();
        // Try to avoid having to get all of our channels via a giant call if we can
        $transient_channels = get_transient( $channels_cache_key );

        $channels = null;

        if ($transient_channels) {
            $channels = $transient_channels;
        } else {
            // If we don't have channels yet, save them for the next time we need to pull
            $channels = $this->get_category_channels($category_name);
            set_transient( $channels_cache_key, $channels, 3600 );
        }

        if ($channels) {

            $channel_is_parent = false;
            $child_channels = get_post_meta(array_values($channels)[0]->ID, 'chnl_child_channels', true);
            if ($child_channels)
                $channel_is_parent = true;


            $total_channels = count($channels);

            $poster_type = ($poster_type) ? $poster_type : $dsp_theme_options['opt-poster-type'];
            if ($total_channels > 1 || $channel_is_parent == true) {

                $transient_show_channels = get_transient( $show_channels_cache_key );
                if ($transient_show_channels) return $transient_show_channels;

                $show_channels = $this->show_channels($channels, 'other_carousel', $category_name, $poster_type, $cnt);
                if (!empty($show_channels)) set_transient( $show_channels_cache_key, $show_channels, 3600 );
                return $show_channels;

            } else {

                $transient_show_videos = get_transient( $show_videos_cache_key );
                if ($transient_show_videos) return $transient_show_videos;

                $child_channels = $this->is_child_channels(array_values($channels)[0]->ID);
                if ($child_channels) {
                    $show_videos = $this->show_videos(array_values($channels)[0], 'other_carousel', $category_name, array_values($channels)[0]->post_name, $cnt);
                }
                else{
                 $show_videos = $this->show_videos(array_values($channels)[0], 'other_carousel', $category_name, null, $cnt);
                }
                if (!empty($show_videos)) set_transient( $show_videos_cache_key, $show_videos, 3600 );
                return $show_videos;

            }
        }
    }

    /**
     * home page other carousel function with api data
     * @since 1.5.7
     *
     * @global type $dsp_theme_options
     * @return type
     */

    public function home_page_other_carousel_with_api($channels, $poster_type){

        global $dsp_theme_options;

        $cnt = null;
        $cnt = $dsp_theme_options['opt-slick-home-slidestoload'];
        if (empty($cnt)) $cnt = $dsp_theme_options['opt-slick-home-slidetoscroll'] * 2;

        $response = array();
        if(count($channels) == 1 && $channels[0]['channel_type'] != 'parent'){
            if($channels[0]['channel_type'] == 'video'){
                $response[0]['id'] = $channels[0]['video']['_id'];
                $response[0]['title'] = $channels[0]['video']['title'];
                $response[0]['description'] = isset($channels[0]['video']['description']) ? $channels[0]['video']['description'] : '';
                $response[0]['image'] = $channels[0]['video']['thumb'];
                $response[0]['slug'] = ($channels[0]['video']['slug']) ? $channels[0]['video']['slug'] : '';
                $response[0]['bypass_channel_lock'] = isset($channels[0]['video']['bypass_channel_lock']) ? $channels[0]['video']['bypass_channel_lock'] : false;
                $response[0]['channel_unlock'] = isset($channels[0]['subscription_access']) ? $channels[0]['subscription_access']['unlocked'] : true;
                $response[0]['url'] = '/channel/'.$channels[0]['slug'].'/video/'.$channels[0]['video']['slug'];
            }
            else if($channels[0]['channel_type'] == 'playlist'){
              foreach ($channels[0]['playlist'] as $key => $singlePlaylist) {
                if ($cnt && count($response) >= $cnt) break;
                $response[$key]['id'] = $singlePlaylist['_id'];
                $response[$key]['title'] = $singlePlaylist['title'];
                $response[$key]['description'] = $singlePlaylist['description'];
                $response[$key]['image'] = $singlePlaylist['thumb'];
                $response[$key]['slug'] = ($singlePlaylist['slug']) ? $singlePlaylist['slug'] : '';
                $response[$key]['bypass_channel_lock'] = isset($singlePlaylist['bypass_channel_lock']) ? $singlePlaylist['bypass_channel_lock'] : false;
                $response[$key]['channel_unlock'] = isset($channels[0]['subscription_access']) ? $channels[0]['subscription_access']['unlocked'] : true;
                $response[$key]['url'] = '/channel/'.$channels[0]['slug'].'/video/'.$singlePlaylist['slug'];
              }
            }
        }
        else if (count($channels) == 1 && $channels[0]['channel_type'] == 'parent'){
            $response[0]['id'] = $channels[0]['_id'];
            $response[0]['title'] = $channels[0]['title'];
            $response[0]['description'] = $channels[0]['description'];
            if($poster_type == 'spotlight_poster'){
                $image = $channels[0]['spotlight_poster'];
            }
            elseif($poster_type == 'wallpaper'){
                $image = $channels[0]['wallpaper'];
            }
            else{
                $image = $channels[0]['poster'];
            }
            $response[0]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
            $response[0]['slug'] = ($channels[0]['slug']) ? $channels[0]['slug'] : '';
            $response[0]['bypass_channel_lock'] =  '';
            $response[0]['channel_unlock'] = isset($channels[0]['subscription_access']) ? $channels[0]['subscription_access']['unlocked'] : true;
            $response[0]['url'] = '/channel/'.$channels[0]['slug'];
        }
        else{
            if($cnt)
                $channels = array_slice($channels, 0, $cnt);
            foreach ($channels as $key => $channel) {
                $response[$key]['id'] = $channel['_id'];
                $response[$key]['title'] = $channel['title'];
                $response[$key]['description'] = !empty($channel['description']) ? $channel['description'] : "";
                if($poster_type == 'spotlight_poster' && !empty($channel['spotlight_poster'])){
                    $image = $channel['spotlight_poster'];
                }
                elseif($poster_type == 'wallpaper' && !empty($channel['wallpaper'])){
                    $image = $channel['wallpaper'];
                }
                else if (!empty($channel['poster'])){
                    $image = $channel['poster'];
                }
                $response[$key]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                $response[$key]['slug'] = ($channel['slug']) ? $channel['slug'] : '';
                $response[$key]['bypass_channel_lock'] =  '';
                $response[$key]['channel_unlock'] = isset($channel['subscription_access']) ? $channel['subscription_access']['unlocked'] : true;
                $response[$key]['url'] = '/channel/'.$channel['slug'];

            }
        }
        return $response;
    }

    /**
     * Get all the Channels based on the Category Name
     * @since 1.0.0
     *
     * @param type $category_name
     * @return type Array
     */
    public function get_category_channels($category_name) {

        $cache_key = "show_channels_" . $category_name . "_" . $this->country;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        $channels_args = array(
            'post_type' => 'channel',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'chnl_categories',
                    'value' => ',' . $category_name . ',',
                    'compare' => 'LIKE',
                )
            )
        );

        $channels = new WP_Query($channels_args);

        $category = get_page_by_path($category_name, OBJECT, 'channel-category');
        // Make sure we actually have something here to show, or else this throws an error
        if (empty($category->ID))
            return array();

        $category_meta = get_post_meta($category->ID, 'cat_id');
        $category_id = $category_meta[0];

        if ($channels->have_posts()) {
            $channels_array = array();
            $i = 999;
            foreach ($channels->posts as $channel):
                $post_meta = get_post_meta($channel->ID);
                $geo = maybe_unserialize($post_meta['dspro_channel_geo'][0]);
                if (count($geo) && !in_array("ALL", $geo) && !in_array($this->country, $geo)) {
                    // If the user doesn't have access to this channel due to
                    // location, we don't need to show it to them in the category
                    continue;
                }
                $channel_weightings = !empty($post_meta['chnl_weightings']) ? maybe_unserialize($post_meta['chnl_weightings'][0]) : array();
                if (!empty($channel_weightings)) {
                    $weightings = maybe_unserialize($channel_weightings);
                    $channel_add = false;
                    if(is_array($weightings)){
                        foreach ($weightings as $weighting):
                            foreach ($weighting as $key => $value):
                                if ($key == $category_id) {
                                    $channels_array[$value] = $channel;
                                    $channel_add = true;
                                }
                            endforeach;
                        endforeach;
                    }
                    if ($channel_add == false)
                        $channels_array[$i] = $channel;
                }
                else {
                    $channels_array[$i] = $channel;
                }
                $i++;
            endforeach;
            ksort($channels_array);
            set_transient($cache_key, $channels_array, 3600);
            return $channels_array;
        } else
            return array();
    }

    /**
     * function to show channels when there is multiple channels in category
     * @since 1.0.0
     *
     * @global type $dsp_theme_options
     * @param type $channels
     * return type
     */
    public function show_channels($channels, $type, $category, $poster_type, $total = null) {

        $cache_key = "show_channels_" . $type . "_" . $category . "_" . $this->country . "_" . $total;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        global $dsp_theme_options;
        $response = [];

        if($total)
            $channels = array_slice($channels, 0, $total);

        foreach ($channels as $key => $channel):
            $response[$key] = [];
            $channel_meta = get_post_meta($channel->ID);
            $geo = maybe_unserialize($channel_meta['dspro_channel_geo'][0]);
            if (count($geo) && !in_array("ALL", $geo) && !in_array($this->country, $geo)) {
                // If the user doesn't have access to this channel due to
                // location, we don't need to show it to them in the category
                continue;
            }
            $response[$key]['id'] = $channel_meta['chnl_id'][0];
            $response[$key]['title'] = $channel->post_title;
            $response[$key]['description'] = $channel->post_content;
            if($poster_type == 'spotlight_poster'){
                $image = $channel_meta['chnl_spotlight_poster'][0];
            }
            elseif($poster_type == 'wallpaper'){
                $image = $channel_meta['chnl_wallpaper'][0];
            }
            else{
                $image = $channel_meta['chnl_poster'][0];
            }
            $response[$key]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
            $response[$key]['dspro_is_product'] = $channel_meta['dspro_is_product'][0];

            if ($type == 'other_carousel' || $dsp_theme_options['opt-play-btn-type'] == 'watch_now')
                $response[$key]['url'] = get_the_permalink($channel->ID);

            else {
                $child_channels = $this->is_child_channels($channel->ID);
                if ($child_channels) {
                    $firstChildChannelId = get_page_by_path($child_channels[0], OBJECT, 'channel');
                    $channelVideos = $this->get_channel_videos($firstChildChannelId->ID);
                    if ($channelVideos) {
                        $response[$key]['slug'] = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : '';
                        $videoSlug = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : $channelVideos[0]['_id'];
                        $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name . '/' . $firstChildChannelId->post_name . '/video/' . $videoSlug;
                    }
                } else {
                    $channelVideos = $this->get_channel_videos($channel->ID);
                    if ($channelVideos) {
                        $response[$key]['slug'] = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : '';
                        $videoSlug = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : $channelVideos[0]['_id'];
                        $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name . '/video/' . $videoSlug;
                    }
                }
            }
        endforeach;
        set_transient($cache_key, $response, 3600);
        return $response;
    }

    /**
     * function to show videos when there is single channels in category
     * @since 1.0.0
     *
     * @global type $dsp_theme_options
     * @param type $channel
     * @return string
     */
    public function show_videos($channel, $type, $category = null, $p_channel = null, $total = null) {

        $cache_key = "show_videos_" . $channel->ID . "_" . $this->country;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;
        $response = array();
        global $dsp_theme_options;
        $child_channels = $this->is_child_channels($channel->ID);
        if ($child_channels) {
            foreach ($child_channels as $key => $channel_name):
                // Make sure we don't end up with too many slides
                if ($total && count($response) >= $total) break;
                $channel = $this->get_channel_by_name($channel_name);
                if ($channel):
                    $channel_meta = get_post_meta($channel->ID);
                    $geo = maybe_unserialize($channel_meta['dspro_channel_geo'][0]);
                    if (count($geo) && !in_array("ALL", $geo) && !in_array($this->country, $geo)) {
                        // If the user doesn't have access to this channel due to
                        // location, we don't need to show it to them in the category
                        continue;
                    }
                    $response[$key]['id'] = $channel_meta['chnl_id'][0];
                    $response[$key]['title'] = $channel->post_title;
                    $response[$key]['description'] = $channel->post_content;
                    if($dsp_theme_options['opt-poster-type'] == 'spotlight_poster'){
                        $image = $channel_meta['chnl_spotlight_poster'][0];
                    }
                    elseif($dsp_theme_options['opt-poster-type'] == 'wallpaper'){
                        $image = $channel_meta['chnl_wallpaper'][0];
                    }
                    else{
                        $image = $channel_meta['chnl_poster'][0];
                    }
                    $response[$key]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                    $response[$key]['dspro_is_product'] = $channel_meta['dspro_is_product'][0];

                    if ($type == 'categories-template') {
                        $dsp_theme_options['opt-play-btn-type'] = 'play-video';
                    }

                    if ($type == 'other_carousel' || $dsp_theme_options['opt-play-btn-type'] == 'watch_now') {
                        if ($p_channel)
                            $response[$key]['url'] = get_site_url() . '/channel/' . $p_channel . '/' . $channel->post_name;
                        else
                            $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name;
                    }

                    else {
                        $channelVideos = $this->get_channel_videos($channel->ID);
                        if ($channelVideos) {
                            $response[$key]['slug'] = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : '';
                            $videoSlug = ($channelVideos[0]['slug']) ? $channelVideos[0]['slug'] : $channelVideos[0]['_id'];
                            if ($p_channel)
                                $response[$key]['url'] = get_site_url() . '/channel/' . $p_channel . '/' . $channel->post_name . '/video/' . $videoSlug;
                            else
                                $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name . '/video/' . $videoSlug;
                        }
                    }
                endif;
            endforeach;
        } else {
            $videoData = $this->get_channel_videos($channel->ID);
            if ($videoData) {

                if($total)
                    $videoData = array_slice($videoData, 0, $total);

                foreach ($videoData as $key => $video):
                    $response[$key]['id'] = $video['_id'];
                    $response[$key]['title'] = $video['title'];
                    $response[$key]['description'] = $video['description'];
                    $response[$key]['image'] = $video['thumb'];
                    $response[$key]['slug'] = ($video['slug']) ? $video['slug'] : '';
                    $response[$key]['bypass_channel_lock'] = ($video['bypass_channel_lock']) ? $video['bypass_channel_lock'] : '';
                    $videoSlug = ($video['slug']) ? $video['slug'] : $video['_id'];
                    if ($p_channel)
                        $response[$key]['url'] = get_site_url() . '/channel/' . $p_channel . '/' . $channel->post_name . '/video/' . $videoSlug;
                    else
                        $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name . '/video/' . $videoSlug;
                endforeach;
            }
        }
        set_transient($cache_key, $response, 3600);
        return $response;
    }

    /**
     * function to get channel information bt it's name
     * @since 1.0.0
     *
     * @param type $channel_name
     * @return type
     */
    public function get_channel_by_name($channel_name) {

        $cache_key = "get_channel_by_name_" . $channel_name;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        $channel_args = array(
            'post_type' => 'channel',
            'pagename' => $channel_name,
        );

        $channel = new WP_Query($channel_args);

        if ($channel->have_posts()) {
            set_transient($cache_key, $channel->posts[0], 3600);
            return $channel->posts[0];
        } else {
            return array();
        }
    }

    /**
     * function to check if channel has any child channels
     * @since 1.0.0
     *
     * @param type $channel_id
     * @return type
     */
    public function is_child_channels($channel_id) {

        $child_channels = get_post_meta($channel_id, 'chnl_child_channels', true);
        if ($child_channels)
            return explode(',', $child_channels);
        else
            return array();
    }

    /**
     * function to get video information based on channel id
     * @since 1.0.0
     *
     * @param type $channel_id
     * @return type
     */
    public function get_channel_videos($channel_id) {

        $cache_key = "get_channel_videos_" . $channel_id;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        global $wpdb;
        $dsp = new Dotstudiopro_Api();
        $dsp_video_table = $dsp->get_Dotstudiopro_Video_Table();
        $videos = explode(',', get_post_meta($channel_id, 'chnl_videos', true));
        $videoData = array();
        if ($videos) {
            foreach ($videos as $key => $video):
                $data = $wpdb->get_results("SELECT * FROM $dsp_video_table WHERE video_id = '" . $video . "'");
                $videoData[$key] = maybe_unserialize(base64_decode($data[0]->video_detail));
                $videoData[$key]['_id'] = $video;
            endforeach;
            set_transient($cache_key, $videoData, 3600);
            return $videoData;
        }
        else {
            return array();
        }
    }

    /**
     * Function to get first video of the channel
     * @since 1.0.0
     * @global type $dsp_theme_options
     * @param type $channel_id
     * @return type
     */
    public function first_video_id($channel_id) {

        $cache_key = "first_video_id_" . $channel_id;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;
        // If we don't have a video, we can bring in global variables and such;
        // no point in wasting time doing so if we don't have to
        global $dsp_theme_options;
        $child_channels = $this->is_child_channels($channel_id);
        if ($child_channels) {
            $channel = $this->get_channel_by_name($child_channels[0]);
            $channelVideos = $this->get_channel_videos($channel->ID);
            $response = $channelVideos[0]['_id'];
        } else {
            $videoData = $this->get_channel_videos($channel_id);
            if ($videoData) {
                $response = $videoData[0]['_id'];
            }
        }
        set_transient($cache_key, $response, 3600);
        return $response;
    }

    /**
     * function to get channel by channel id
     * @since 1.0.0
     * @param type $channel_id
     * @return type
     */
    public function get_channelByChannelId($channel_id) {

        $cache_key = "get_channelByChannelId_" . $channel_id;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        $channels_args = array(
            'post_type' => 'channel',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'chnl_id',
                    'value' => $channel_id,
                    'compare' => '=',
                )
            )
        );
        $channel = new WP_Query($channels_args);
        if (isset($channel->posts[0])){
            set_transient($cache_key, $channel->posts[0], 3600);
            return $channel->posts[0];
        } else {
            return array();
        }
    }

    /**
     * function to get the recommendation content
     * @since 1.0.0
     * @param type $type
     * @param type $id
     * @return string
     */
    public function get_recommendation_content($type, $id) {

        global $dsp_theme_options;

        $dsp_external_api = new Dsp_External_Api_Request();
        $recommendations = $dsp_external_api->get_recommendation($type, $id);
        $recommendation_content = array();
        if (is_wp_error($recommendations))
            return array();
        else {
            foreach ($recommendations['data']['hits'] as $key => $recommendation):
                if ($type == 'channel') {
                    $channel = $this->get_channelByChannelId($recommendation['_id']);
                    if (!empty($channel)) {
                        $recommendation_content[$key]['_id'] = $channel->ID;
                        $channel_meta = get_post_meta($channel->ID);
                        $recommendation_content[$key]['title'] = $channel->post_title;
                        $recommendation_content[$key]['description'] = $channel->post_content;
                        if($dsp_theme_options['opt-related-channel-poster-type'] == 'spotlight_poster'){
                            $image = $channel_meta['chnl_spotlight_poster'][0];
                        }
                        elseif($dsp_theme_options['opt-related-channel-poster-type'] == 'wallpaper'){
                            $image = $channel_meta['chnl_wallpaper'][0];
                        }
                        else{
                            $image = $channel_meta['chnl_poster'][0];
                        }
                        $recommendation_content[$key]['image'] = ($image) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                        $recommendation_content[$key]['url'] = get_the_permalink($channel->ID);
                    }
                } else {
                    $video = $dsp_external_api->get_video_by_id($recommendation['_id']);
                    if (!is_wp_error($video) && !empty($video['_id'])) {
                        $recommendation_content[$key]['_id'] = isset($video['_id']) ? $video['_id'] : '';
                        $recommendation_content[$key]['title'] = isset($video['title']) ? $video['title'] : '';
                        $recommendation_content[$key]['description'] = isset($video['description']) ? $video['description'] : '';
                        $recommendation_content[$key]['image'] = isset($video['thumb']) ? $video['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                        $recommendation_content[$key]['url'] = get_site_url() . '/video/' . $video['_id'];
                    }
                }
                $recommendation_content[$key]['is_product'] = (isset($recommendation['_source']['is_product'])) ? $recommendation['_source']['is_product'] : '';
            endforeach;
            return $recommendation_content;
        }
    }

    /**
     * Query a category and cache the results so we can return them later
     * @since 1.0.0
     *
     * @param object $args The arguments for the query
     * @param string $trans_key The cache key for storing/grabbing this query from a transient cache
     * @return object Query result
     */
    public function query_categories_posts($args, $trans_key = null) {
        if ($trans_key) {
            $posts = get_transient($trans_key);
            if ($posts) return $posts;
        }
        $query = new WP_Query($args);
        if (empty($query->posts)) return array();
        // Cache for 30 mins
        set_transient($trans_key, $query->posts, 1800);
        return $query->posts;
    }

    /**
     * Category args for category template page
     * @since 2.0.0
     *
     * @param object $args The arguments for the query
     * @return object Query result
     */
    public function category_args($args){
        $category_args = array(
            'post_type' => 'channel-category',
            'posts_per_page' => -1,
            'post_name__in' => $args,
            'order' => 'ASC',
            'meta_key' => 'weight',
            'orderby' => 'meta_value_num',
        );
        return $category_args;
    }

    /**
     * Category args for Home page
     * @since 2.0.0
     *
     * @param object $args The arguments for the query
     * @return object Query result
     */
    public function category_args_homepage($args){
       $category_args_homepage = array(
            'post_type' => 'channel-category',
            'posts_per_page' => -1,
            'post__not_in' => !empty($args->ID) ? array($args->ID) : array(), // Ensure we have a home here, or else we get errors
            'order' => 'ASC',
            'meta_key' => 'weight',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key' => 'is_on_cat_homepage',
                    'value' => 1
                )
            )
        );
        return $category_args_homepage;
    }

    /**
     * Channel args for category template page
     * @since 2.0.0
     *
     * @param object $args The arguments for the query
     * @return object Query result
     */
    public function channels_args($args){
        $channels_args = array(
            'post_type' => 'channel',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'chnl_categories',
                    'value' => ',' . $args . ',',
                    'compare' => 'LIKE',
                )
            )
        );
        return $channels_args;
    }

    /**
     * function to localize the option for the intializtion of slick slider
     * @since 1.0.0
     *
     * @param type $class_array
     */
    public function slick_init_options($slider_class_name, $class_array = null, $type) {
        global $dsp_theme_options;
        wp_localize_script('slick-init', $slider_class_name, array(
            'selector' => $class_array,
            'slidetoshow' => $dsp_theme_options['opt-slick-' . $type . '-slidetoshow'],
            'slidetoscroll' => $dsp_theme_options['opt-slick-' . $type . '-slidetoscroll'],
            'infinite' => $dsp_theme_options['opt-slick-' . $type . '-infinite'],
            'autoplay' => $dsp_theme_options['opt-slick-' . $type . '-autoplay'],
            'autoplayspeed' => $dsp_theme_options['opt-slick-' . $type . '-autoplayspeed'],
            'slidespeed' => $dsp_theme_options['opt-slick-' . $type . '-slidespeed'],
            'pagination' => $dsp_theme_options['opt-slick-' . $type . '-pagination'],
            'navigation' => $dsp_theme_options['opt-slick-' . $type . '-navigation'],
            'responsive' => $dsp_theme_options['opt-slick-' . $type . '-responsive'],
            'tablet_slidetoshow' => $dsp_theme_options['opt-slick-' . $type . '-tablet-slidetoshow'],
            'mobile_slidetoshow' => $dsp_theme_options['opt-slick-' . $type . '-mobile-slidetoshow'],
                )
        );
    }

}
