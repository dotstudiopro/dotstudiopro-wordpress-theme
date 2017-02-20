<?php

function ds_is_channel_parent_check()
{

    if (ds_channel_is_parent()) {

        $videos = grab_channel();

        $children = $videos[0]->childchannels;

        $child_slug = $children[0]->slug;

        $current = get_post(get_the_ID());

        $category = get_query_var("channel_category", false);

        if (!$category) {

            $category = 'featured';

        }

        $url = home_url("channels/" . $current->post_name . "/" . $child_slug . "/");

        wp_redirect($url);
        die();

    }
}

function igrab_channel()
{

    global $post;

    $video = false;

    $is_child = ds_channel_is_child();

    $videos = grab_channel();

    if (!is_array($videos)) {

        return array();

    }

    $channel_title = $videos[0]->title;

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $title = $is_child ? $videos[0]->childchannels[0]->title : $videos[0]->title;

    $description = $videos[0]->description;

    $actors = $videos[0]->actors;

    $writers = $videos[0]->writers;

    $directors = $videos[0]->directors;

    $image_id = $is_child ? "http://image.myspotlight.tv/" . $playlist[0]->thumb : "http://image.myspotlight.tv/" . (!empty($videos[0]->playlist[0]->thumb) ? $videos[0]->playlist[0]->thumb : $videos[0]->video->thumb);

    $playlist = $is_child ? $videos[0]->childchannels[0]->playlist : $videos[0]->playlist;

    $channel_parent = get_post($post->post_parent);

    $poster = $videos[0]->poster;

    $to_return['playlist'] = $playlist;

    $to_return['details'] = array('description' => $description, 'actors' => $actors, 'writers' => $writers, 'directors' => $directors, 'poster' => $poster);

    $to_return['link_url'] = $is_child ? home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name) : home_url("channels/" . $post->post_name . "/");

    $to_return['count'] = count($playlist);

    $video = get_query_var("video", false);

    if ($video) {

        $id = get_query_var("video", false);

        $url = home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name . "/video=$id");

        foreach ($playlist as $pl) {

            if ($pl->_id == $id) {

                $title = $pl->title;

                $duration = round($pl->duration / 60);

                $description = $pl->description;

                $country = $pl->country;

                $language = $pl->language;

                $image_id = "http://image.myspotlight.tv/" . $pl->thumb;

                break;

            }

        }

    }

    $to_return['for_meta'] = (object) array('description' => $description, 'url' => $to_return['link_url'], 'channel_title' => $channel_title, 'title' => $title, 'image_id' => $image_id);

    return $to_return;



}

function channel_headline_video()
{

    global $ds_curl;

    $video = get_query_var("video", false);

    $is_child = ds_channel_is_child();

        $videos = grab_channel();

        if (!is_array($videos)) {

            $videos = new stdClass;

            return $videos;

        }

        $playlist = $is_child ? $videos[0]->childchannels[0]->playlist : $videos[0]->playlist;

        $tags = $playlist[0]->tags;

        $id = $playlist[0]->_id;

        $title = $is_child ? $playlist[0]->title : !empty($videos[0]->playlist[0]->title) ? $videos[0]->playlist[0]->title : !empty($videos[0]->video->title) ? $videos[0]->video->title : '';

        $duration = $is_child ? round($playlist[0]->duration / 60) : !empty($videos[0]->playlist[0]->duration) ? round($videos[0]->playlist[0]->duration / 60) : !empty($videos[0]->video->duration) ? round($videos[0]->video->duration / 60) : '';

        $description = "";
        if (!empty($videos[0]->video->description)) {

            $description = $videos[0]->video->description;

        } else if (!empty($videos[0]->playlist[0]->description)) {

            $description = $videos[0]->playlist[0]->description;

        } else if (!empty($videos[0]->video->country)) {

            $description = $videos[0]->video->country;

        } else if (!empty($videos[0]->description)){
            $description = $videos[0]->description;
        }

        $company = !empty($videos[0]->company) ? $videos[0]->company : '';

        $company_id = !empty($videos[0]->childchannels[0]->company_id) ? $videos[0]->childchannels[0]->company_id : !empty($videos[0]->playlist[0]->company_id) ? $videos[0]->playlist[0]->company_id : $videos[0]->spotlight_company_id;

        $country = !empty($playlist[0]->country) ? $playlist[0]->country : '';

        $language = !empty($playlist[0]->language) ? $playlist[0]->language : !empty($videos[0]->video->language) ? $videos[0]->video->language : '';

        $year = !empty($videos[0]->year) ? $videos[0]->year : '';

        $rating = !empty($videos[0]->rating) ? $videos[0]->rating : '';

        if ($video) {
            $id = get_query_var("video", false);

            foreach ($playlist as $pl) {

                if ($pl->_id == $id) {

                    $title = $pl->title;

                    $duration = round($pl->duration / 60);

                    $description = $pl->description;

                    $country = $pl->country;

                    $language = $pl->language;

                    $tags = $pl->tags;

                    break;

                }

            }

        }
        wp_register_script('channel-video-functions', plugins_url('js/channel.video.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-video-functions');

        wp_register_script('channel-display-functions', plugins_url('js/channel.display.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-display-functions');


        wp_enqueue_style('video-playlist',plugins_url('dotstudiopro-wordpress/css/video-playlist.css'));

        $video_custom_css = locate_template( 'video.channel.customization.css' );

        if(!empty($video_custom_css)){
            wp_enqueue_style('video-custom',plugins_url('/dotstudiopro-wordpress/css/video.channel.customization.css'));    
        } else {
            wp_enqueue_style('video-custom',get_template_directory_uri() . '/video.channel.customization.css');    
        }
        

        $player_url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

        $to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $description, 'company' => $company, 'country' => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url, 'tags'=>$tags);

        return $to_return;

}

function get_child_siblings()
{

    if (!ds_channel_is_child()) {

        return false;

    }

    global $post;

    $parent = grab_parent_channel();

    if (!$parent) {

        return '';

    }

    $parent_slug = $parent->slug;

    $siblings = '';

    foreach ($parent->childchannels as $ch) {

        $selected = '';

        if ($ch->slug == $post->post_name) {

            $selected = "active";

        }

        $siblings .= "

        <a href='" . home_url("channels/" . $parent->slug . "/" . $ch->slug . "/") . "' class='$selected'>
            <img src='http://image.myspotlight.tv/" . $ch->playlist[0]->thumb . "/400/225' />
            <h3>" . $ch->title . "</h3>
        </a>";

    }

    return $siblings;

}

