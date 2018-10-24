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
        $response = array();

        $main_carousel_category = $dsp_theme_options['opt-home-carousel'];

        $channels = $this->get_category_channels($main_carousel_category);

        if ($channels) {

            $total_channels = count($channels);

            if ($total_channels > 1) {
                return $this->show_channels($channels);
            } else {
                return $this->show_videos($channels);
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
    public function home_page_other_carousel($category_name) {

        global $dsp_theme_options;
        $response = array();

        $channels = $this->get_category_channels($category_name);

        if ($channels) {

            $total_channels = count($channels);

            if ($total_channels > 1) {
                return $this->show_channels($channels);
            } else {
                return $this->show_videos($channels);
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

        $channels_args = array(
            'post_type' => 'channel',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'chnl_catagories',
                    'value' => $category_name,
                    'compare' => 'LIKE',
                )
            )
        );

        $channels = new WP_Query($channels_args);

        if ($channels->have_posts())
            return $channels->posts;
        else
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
    public function show_channels($channels) {

        global $dsp_theme_options;

        foreach ($channels as $key => $channel):
            $channel_meta = get_post_meta($channel->ID);
            $response[$key]['id'] = $channel_meta['chnl_id'][0];
            $response[$key]['title'] = $channel->post_title;
            $response[$key]['description'] = $channel->post_content;
            $image = ($dsp_theme_options['opt-poster-type'] == 'spotlight_poster') ? $channel_meta['chnl_spotlisgt_poster'][0] : $channel_meta['chnl_poster'][0];
            $response[$key]['image'] = (!empty($image)) ? $image : 'https://picsum.photos/';

            if ($dsp_theme_options['opt-play-btn-type'] == 'watch_now')
                $response[$key]['url'] = get_the_permalink($channel->ID);

            else {
                $child_channels = $this->is_child_channels($channel->ID);
                if ($child_channels) {
                    $firstChildChannelId = get_page_by_path($child_channels[0], OBJECT, 'channel');
                    $channelVideos = $this->get_channel_videos($firstChildChannelId->ID);
                    if ($channelVideos)
                        $response[$key]['url'] = get_site_url() . '/channel' . $channel->name . '/video/' . $channelVideos[0]['_id'];
                }
                else {
                    $channelVideos = $this->get_channel_videos($channel->ID);
                    if ($channelVideos)
                        $response[$key]['url'] = get_site_url() . '/channel' . $channel->name . '/video/' . $channelVideos[0]['_id'];
                }
            }
        endforeach;

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
    public function show_videos($channel) {

        global $dsp_theme_options;

        $child_channels = $this->is_child_channels($channel[0]->ID);
        if ($child_channels) {
            foreach ($child_channels as $key => $channel_name):
                $channel = $this->get_channel_by_name($channel_name);
                $channel_meta = get_post_meta($channel->ID);
                $response[$key]['id'] = $channel_meta['chnl_id'][0];
                $response[$key]['title'] = $channel->post_title;
                $response[$key]['description'] = $channel->post_content;
                $image = ($dsp_theme_options['opt-poster-type'] == 'spotlight_poster') ? $channel_meta['chnl_spotlisgt_poster'][0] : $channel_meta['chnl_poster'][0];
                $response[$key]['image'] = (!empty($image)) ? $image : 'https://picsum.photos/';

                if ($dsp_theme_options['opt-play-btn-type'] == 'watch_now')
                    $response[$key]['url'] = get_the_permalink($channel->ID);

                else {
                    $channelVideos = $this->get_channel_videos($channel->ID);
                    if ($channelVideos)
                        $response[$key]['url'] = get_site_url() . '/channel' . $channel->name . '/video/' . $channelVideos[0]['_id'];
                }
            endforeach;
        }
        else {
            $videoData = $this->get_channel_videos($channel->ID);
            if ($videoData) {
                foreach ($videoData as $key => $video):
                    $response[$key]['id'] = $video['_id'];
                    $response[$key]['title'] = $video['title'];
                    $response[$key]['description'] = $video['description'];
                    $response[$key]['image'] = get_option('dsp_cdn_img_url_field') . $video['thumb'];
                    $videoSlug = ($video['slug']) ? $video['slug'] : $video['_id'];
                    $response[$key]['url'] = get_site_url() . '/channel/' . $channel->post_name . '/video/' . $videoSlug;
                endforeach;
            }
        }
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

        $channel_args = array(
            'post_type' => 'channel',
            'pagename' => $channel_name,
        );

        $channel = new WP_Query($channel_args);

        if ($channel->have_posts())
            return $channel->posts[0];
        else
            return array();
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

        $videoData = maybe_unserialize(get_post_meta($channel_id, 'chnl_videos', TRUE));
        if ($child_channels)
            return $videoData;
        else
            return array();
    }

    /**
     * function to localize the option for the intializtion of slick slider
     * @since 1.0.0
     * 
     * @param type $class_array
     */
    public function slick_init_options($class_array = null) {
        global $dsp_theme_options;
        wp_localize_script('slick-init', 'slick_carousel', array(
            'selector' => $class_array,
            'slidetoshow' => $dsp_theme_options['opt-slick-slidetoshow'],
            'slidetoscroll' => $dsp_theme_options['opt-slick-slidetoscroll'],
            'infinite' => $dsp_theme_options['opt-slick-infinite'],
            'autoplay' => $dsp_theme_options['opt-slick-autoplay'],
            'autoplayspeed' => $dsp_theme_options['opt-slick-autoplayspeed'],
            'slidespeed' => $dsp_theme_options['opt-slick-slidespeed'],
            'pagination' => $dsp_theme_options['opt-slick-pagination'],
            'navigation' => $dsp_theme_options['opt-slick-navigation'],
            'responsive' => $dsp_theme_options['opt-slick-responsive'],
            'tablet_slidetoshow' => $dsp_theme_options['opt-slick-tablet-slidetoshow'],
            'mobile_slidetoshow' => $dsp_theme_options['opt-slick-mobile-slidetoshow'],
                )
        );
    }

}
