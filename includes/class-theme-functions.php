<?php

/**
 * The file extends the Walker_Nav_Menu class to customize the menu option;
 * @since 1.0.0
 */
class Theme_Functions {

    /**
     * home page main carousel function
     * @since 1.0.0
     *
     * @global type $dsp_theme_options
     * @return type
     */
    public function home_page_main_carousel() {

        global $dsp_theme_options;
        $channels_cache_key = "home_page_main_carousel_channels";
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
                return $this->show_channels($channels, 'main_carousel', $main_carousel_category, $poster_type);
            } else {
                return $this->show_videos(array_values($channels)[0], 'main_carousel', $main_carousel_category, array_values($channels)[0]->post_name);
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
    public function home_page_other_carousel($category_name, $poster_type = NULL) {

        global $dsp_theme_options;
        $channels_cache_key = "home_page_other_carousel_channels_" . $category_name;
        $show_channels_cache_key = "home_page_other_carousel_show_channels_" . $category_name;
        $show_videos_cache_key = "home_page_other_carousel_show_videos_" . $category_name;
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

            $total_channels = count($channels);

            $poster_type = ($poster_type) ? $poster_type : $dsp_theme_options['opt-poster-type'];
            if ($total_channels > 1) {

                $transient_show_channels = get_transient( $show_channels_cache_key );
                if ($transient_show_channels) return $transient_show_channels;

                $show_channels = $this->show_channels($channels, 'other_carousel', $category_name, $poster_type);
                set_transient( $show_channels_cache_key, $show_channels, 3600 );
                return $show_channels;

            } else {

                $transient_show_videos = get_transient( $show_videos_cache_key );
                if ($transient_show_videos) return $transient_show_videos;

                $show_videos = $this->show_videos(array_values($channels)[0], 'other_carousel', $category_name, array_values($channels)[0]->post_name);
                set_transient( $show_videos_cache_key, $show_videos, 3600 );
                return $show_videos;

            }
        }
    }

    /**
     * Get all the Channels based on the Category Name
     * @since 1.0.0
     *
     * @param type $category_name
     * @return type Array
     */
    public function get_category_channels($category_name) {

        $cache_key = "show_channels_" . $category_name;
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
                $channel_weightings = get_post_meta($channel->ID, 'chnl_weightings');
                if (!empty($channel_weightings)) {
                    $weightings = maybe_unserialize($channel_weightings[0]);
                    $channel_add = false;
                    foreach ($weightings as $weighting):
                        if (array_keys($weighting)[0] == $category_id) {
                            $channels_array[array_values($weighting)[0]] = $channel;
                            $channel_add = true;
                        }
                    endforeach;
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
    public function show_channels($channels, $type, $category, $poster_type) {

        $cache_key = "show_channels_" . $type . "_" . $category;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        global $dsp_theme_options;

        foreach ($channels as $key => $channel):
            $channel_meta = get_post_meta($channel->ID);
            $response[$key]['id'] = $channel_meta['chnl_id'][0];
            $response[$key]['title'] = $channel->post_title;
            $response[$key]['description'] = $channel->post_content;
            $image = ( $poster_type == 'spotlight_poster') ? $channel_meta['chnl_spotlight_poster'][0] : $channel_meta['chnl_poster'][0];
            $response[$key]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';

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
    public function show_videos($channel, $type, $category = null, $p_channel = null) {

        $cache_key = "show_videos_" . $channel->ID;
        $cache = get_transient($cache_key);
        if ($cache) return $cache;

        global $dsp_theme_options;
        $child_channels = $this->is_child_channels($channel->ID);
        if ($child_channels) {
            foreach ($child_channels as $key => $channel_name):
                $channel = $this->get_channel_by_name($channel_name);
                if ($channel):
                    $channel_meta = get_post_meta($channel->ID);
                    $response[$key]['id'] = $channel_meta['chnl_id'][0];
                    $response[$key]['title'] = $channel->post_title;
                    $response[$key]['description'] = $channel->post_content;
                    $image = ($dsp_theme_options['opt-poster-type'] == 'spotlight_poster') ? $channel_meta['chnl_spotlight_poster'][0] : $channel_meta['chnl_poster'][0];
                    $response[$key]['image'] = (!empty($image)) ? $image : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';

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
                foreach ($videoData as $key => $video):
                    $response[$key]['id'] = $video['_id'];
                    $response[$key]['title'] = $video['title'];
                    $response[$key]['description'] = $video['description'];
                    $response[$key]['image'] = $video['thumb'];
                    $response[$key]['slug'] = ($video['slug']) ? $video['slug'] : '';
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
                        $image = ($dsp_theme_options['opt-related-channel-poster-type'] == 'spotlight_poster') ? $channel_meta['chnl_spotlight_poster'][0] : $channel_meta['chnl_poster'][0];
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
