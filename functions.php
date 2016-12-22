<?php

set_time_limit(120);

$ds_curl = new DotStudioz_Commands;

function wptuts_scripts_load_cdn()
{

    global $wpdb, $post;
    // Deregister the included library
    wp_deregister_script('jquery');

    // Register the library again from Google's CDN
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array(), null, false);

    // Register the script like this for a plugin:
    wp_register_script('custom-script', plugins_url('/js/custom-script.js', __FILE__), array('jquery'));
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script('custom-script');

    $channels = get_page_by_path('channels');

    $categories = get_page_by_path('channel-categories');

    $parent = get_post($post->post_parent);

    if ($post->post_parent == $categories->ID || $parent->post_parent == $channels->ID || $parent->post_parent == $categories->ID || $channels->ID == $post->ID || $categories->ID == $post->ID) {

        // Register the script like this for a plugin:
        wp_register_script('grid-script', plugins_url('/js/vendor/jquery.gridder.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('grid-script');

    }

}
add_action('wp_enqueue_scripts', 'wptuts_scripts_load_cdn');

function ds_plugin_style()
{

    $admin_option = get_option('ds_plugin_style');

    if (!$admin_option) {

        wp_enqueue_style(
            'ds-plugin-style',
            plugin_dir_url(__FILE__) . 'css/light-style.css'
        );

    } else {

        wp_enqueue_style(
            'ds-plugin-style',
            plugin_dir_url(__FILE__) . "css/$admin_option.css"
        );

    }

}

function ds_light_theme_shadows()
{

    if (get_option('ds_plugin_style') != 'light-style' || get_option('ds_light_theme_shadow') == 0) {

        return;

    }

    echo '
        <style>
            /* Box shadows for light theme. Comment these to remove on grid */
            .og-expander-inner.light-theme-shadow{
                -webkit-box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);
                -moz-box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);
                box-shadow: 0px 0px 10px 0px rgba(0,0,0,1);

            }

            .gridder-list.light-theme-shadow img{
                -webkit-box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
                -moz-box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
                box-shadow: 0px 0px 15px 1px rgba(0,0,0,0.75);
            }
        </style>
    ';

}

add_action("wp_head", "ds_light_theme_shadows", 990);

function ds_styles()
{
    // Register the style like this for a plugin:
    wp_register_style('font-awesome-style', plugins_url('/css/font-awesome.min.css?v=1234', __FILE__), array(), '20120208', 'all');

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style('font-awesome-style');

    // Register the style like this for a plugin:
    wp_register_style('animate-style', plugins_url('/css/animate.css', __FILE__), array(), '20120208', 'all');

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style('animate-style');
    // Register the style like this for a plugin:
    wp_register_style('grid-style', plugins_url('/css/grid.css', __FILE__), array(), '20120208', 'all');

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style('grid-style');

    // Register the style like this for a plugin:
    wp_register_style('ds-style', plugins_url('/style.css', __FILE__), array(), '20120208', 'all');

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style('ds-style');

    // Styles for the FancyFrame portion of this plugin:
    wp_register_style('fancyframe-style', plugins_url('/css/fancyframes.css', __FILE__), array(), '20120208', 'all');

    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style('fancyframe-style');



}
add_action('wp_enqueue_scripts', 'ds_styles');

function add_my_var($public_query_vars)
{
    $public_query_vars[] = 'video';
    $public_query_vars[] = 'channel_category';
    return $public_query_vars;
}
add_filter('query_vars', 'add_my_var');

function ds_rewrite_rules()
{

    $option_check = get_option('rewrite_array');

    if (!$option_check) {

        return;

    }

    for ($a = 0; $a < count($option_check[0]); $a++) {

        //echo "add_rewrite_rule(".$option_check[0][$a].", ".$option_check[1][$a].", 'top')\n\n";

        add_rewrite_rule($option_check[0][$a], $option_check[1][$a], 'top');

    }

}
// Depreciated, but may be useful in the future.
//add_action('init', 'ds_rewrite_rules', 10, 0);

function ds_check_api_key_set()
{

    $api_key = get_option('ds_api_key');

    if ($api_key && strlen($api_key) > 0) {

        return false;

    }

    ?>
    <div class="update-nag">
        <p>You need to enter your API Key in order to use its features. <a href="<?php echo home_url('wp-admin/admin.php?page=dot-studioz-options') ?>">Do so here.</a></p>
    </div>
    <?php
}
add_action('admin_notices', 'ds_check_api_key_set');

function ds_new_token()
{

    global $ds_curl;

    $token = $ds_curl->curl_command('token');

    update_option('ds_curl_token', $token);

    update_option('ds_curl_token_time', time());

}

function ds_get_country()
{

    global $ds_curl;

    $country = $ds_curl->curl_command('country');

    return $country;

}

add_action("init", "ds_get_country");

function list_channels()
{

    global $ds_curl;

    $channels = $ds_curl->curl_command('all-channels');

    return $channels;

}

function list_categories()
{

    global $ds_curl;

    $categories = $ds_curl->curl_command('all-categories');

    $categories_filtered = array();

    foreach ($categories as $cat) {

        if (!empty($cat->platforms) && !empty($cat->platforms[0]) && isset($cat->platforms[0]->website) && (string) $cat->platforms[0]->website === 'false' || !isset($cat->platforms[0]->website)) {
            continue;
        }

        $categories_filtered[] = $cat;

    }

    return $categories_filtered;

}

function channel_revision_check()
{

    global $wpdb, $post;

    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type = 'revision' AND post_parent = " . $post->ID);

    if (count($results) > 0) {

        return true;

    } else {

        return false;

    }

}

function grab_channel()
{

    global $ds_curl;

    $channels = $ds_curl->curl_command('single-channel');

    return $channels;

}

function grab_parent_channel()
{

    global $ds_curl;

    $channels = $ds_curl->curl_command('parent-channel');

    return $channels;

}

function channel_first_video()
{

    global $ds_curl;

    $videos = grab_channel();

    $id = $videos[0]->video->_id;

    $title = $videos[0]->video->title;

    $duration = round($videos[0]->video->duration / 60);

    $description = $videos[0]->video->description;

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $country = $videos[0]->video->country;

    $language = $videos[0]->video->language;

    $year = $videos[0]->year;

    $rating = $videos[0]->rating;

    $url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

    return "<div class='ds-video-headliner'>
                <div class='ds-video-fluidMedia'>
                <div class='player'></div><script src='$url'></script>
                </div>
                <div class='ds-col-8'>
                    <h1 class='ds-video-headliner-title'>$title</h1>
                    <span class='ds-video-headliner-duration'>($duration min)</span>
                    <ul class='ds-videometalist'>
                      <li>$country</li>
                      <li>Rating:$rating</li>
                      <li>$language</li>
                      <li>$year</li>
                      <li>$company</li>
                    </ul>
                    <ul class='ds-sharepad'>
                        <li>Share <i class='fa fa-share fa-small'></i></li>
                        <li>
                            <a href='https://www.facebook.com/dialog/share?app_id=375655362631161&display=popup&href=http://" . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI] . " class='js-social-share buffer' target='_blank'><i class='fa fa-facebook fa-shadow'></i></a>
                        </li>
                        <li><i class='fa fa-twitter fa-small'></i></li>
                        <li><i class='fa fa-googleplus fa-small'></i></li>
                        <li><i class='fa fa-tumblr fa-small'></i></li>
                        <li><i class='fa fa-pinterest fa-small'></i></li>

                    </ul>
                    </div>
                    <div class='ds-col-4'>
                        <div id='companion300x250'></div>
                    </div>

            </div>";

}

function child_channel_first_video()
{

    global $ds_curl;

    $videos = grab_channel();

    $id = $videos[0]->childchannels[0]->playlist[0]->_id;

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

    return "<div class='player'></div><script src='$url'></script>";

}

function channel_selected_video()
{

    global $ds_curl;

    $video_id = get_query_var("video", false);

    $videos = grab_channel();

    $title = $videos[0]->video->title;

    $duration = round($videos[0]->video->duration / 60);

    $description = $videos[0]->video->description;

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $country = $videos[0]->video->country;

    $language = $videos[0]->video->language;

    $year = $videos[0]->year;

    $rating = $videos[0]->rating;

    $url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

    return "<div class='ds-video-headliner'>
                <div class='ds-video-fluidMedia'>
                <div class='player'></div><script src='$url'></script>
                </div>
                <div class='ds-col-6'>
                    <h1 class='ds-video-headliner-title'>$title</h1>
                    <span class='ds-video-headliner-duration'>($duration min)</span>
                    <ul class='ds-videometalist'>
                      <li>$country</li>
                      <li>Rating:$rating</li>
                      <li>$language</li>
                      <li>$year</li>
                      <li>$company</li>
                    </ul>
                    </div>
                    <div class='ds-col-4'>
                        <div id='companion300x250'></div>
                    </div>
                <span class='ds-video-headliner-description'>$description</span>

            </div>";

}

function child_channel_selected_video()
{

    global $ds_curl;

    $id = get_query_var("video", false);

    $videos = grab_channel();

    $company = $videos[0]->company;

    $company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';

    $url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

    return "<div class='player'></div><script src='$url'></script>";

}

function child_video()
{

    if (!ds_channel_is_child()) {

        return false;

    }

    $uri = $_SERVER['REQUEST_URI'];

    $exp = explode(get_query_var("channel_category", false) . "/" . get_query_var("video", false), $uri);

    $id = str_replace("/", "", $exp[1]);

    if (strlen($id) > 0) {

        return true;

    }

}

function grab_playlist()
{

    global $ds_curl, $post;

    $videos = grab_channel();

    $company = $videos[0]->company;

    $playlist = $videos[0]->playlist;

    $category = get_query_var("channel_category", false);

    echo "<ul class='ds-video-thumbnails'>";

    foreach ($playlist as $pl) {

        $id = $pl->_id;

        $thumb_id = $pl->thumb;

        $title = $pl->title;

        $duration = round($pl->duration / 60);

        $year = $videos[0]->year;

        $country = $pl->country;

        $rating = $pl->rating;

        $company = $pl->company;

        $description = $pl->description;

        $category = get_query_var("channel_category", false);

        $epnum = key($pl);

        echo "
            <li>
                <img src='http://image.myspotlight.tv/$thumb_id/380/215' />
                <div class='ds-overlay animated fadeIn'>
                    <a href='" . home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name . "/?video=$id") . "'>
                 <i class='fa fa-play-circle-o fa-3x'></i>
                </a>
                <label class='delay' style='display: inline-block;'><small>$duration min</small></label>
                </div>
                <h3>$title</h3>
                <span class='ds-video-year animated fadeIn'><small>Year: $year</small></span>
                <span class='ds-video-country animated fadeIn'><small>Country: $country</small></span>
                <span class='ds-video-description animated fadeIn'>$description</span>
            </li>

            ";

    }

    echo "</ul>";

}

function grab_parent_playlist()
{
    return;

    if (!ds_channel_is_parent()) {

        return false;

    }

    global $ds_curl, $wpdb, $post;

    $videos = grab_channel();

    $company = $videos[0]->company;

    $category = get_query_var("channel_category", false);

    $children = $videos[0]->childchannels;

    foreach ($children as $ch) {

        $id = $ch->_id;

        $thumb_id = $ch->video->thumb;

        $slug = $ch->slug;

        $title = $ch->title;

        $spotlight_poster = $ch->spotlight_poster;

        $poster = $ch->poster;

        $year = $ch->year;

        $language = $ch->language;

        $rating = $ch->rating;

        $company = $ch->company;

        $description = $ch->video->description;

        echo "
                <a href='" . home_url("channels/" . $post->post_name . "/$slug/") . "'></a>
             <li class='gridder-list' data-griddercontent='#content1'>
                <img class='channel-spotlight-poster' src='$spotlight_poster/400/225'>
            </li>
            <div id='content1' class='gridder-content'>
                <span class='channel-title'>$title</span>
            <span class='channel-spotlight-poster-holder'><img src='http://image.myspotlight.tv/$thumb_id/1280/720' /></span>
            <span class='channel-poster-holder'><img class='channel-poster' src='$poster'></span>
            <span class='channel-year'>Year: $year</span>
            <span class='channel-language'>Language: $language</span>
            <span class='channel-company>Company: $company</span>
            <span class='channel-description'>Description: $description</span>

            </div>

            ";

    }

}

function grab_child_playlist()
{

    if (!ds_channel_is_child()) {

        return false;

    }

    global $ds_curl, $wpdb, $post;

    $videos = grab_channel();

    $company = $videos[0]->company;

    $category = get_query_var("channel_category", false);

    $playlist = $videos[0]->childchannels[0]->playlist;

    $channel_parent = get_post($post->post_parent);

    foreach ($playlist as $pl) {

        $id = $pl->_id;

        $thumb_id = $pl->thumb;

        $title = $pl->title;

        $duration = round($pl->duration / 60);

        $year = $pl->year;

        $country = $pl->country;

        $rating = $pl->rating;

        $company = $pl->company;

        $description = $pl->description;

        $epnum = key($pl);

        echo "
            <li class='gridder-list' data-griddercontent='#content1'>
                <img src='http://image.myspotlight.tv/$thumb_id/380/215' />
                <a href='" . home_url("channels/" . $channel_parent->post_name . "/" . $post->post_name . "/") . "'></a>
            </li>
            <div id='content1' class='gridder-content'> Content goes here... </div>
            <span class='ds-video-title'>$title</span>

            <img src='http://image.myspotlight.tv/$thumb_id/380/215' /></a>

            <span class='ds-video-duration'>Duration: $duration</span>
            <span class='ds-video-year'>Year: $year</span>
            <span class='ds-video-country'>Country: $country</span>
            <span class='ds-video-company>Company: $company</span>
            <span class='ds-video-description'>Description: $description</span>

            ";

    }

}

function grab_category($category)
{

    global $ds_curl;

    $category = $ds_curl->curl_command('single-category', array("category" => $category));

    return $category;

}

function grab_video($video)
{

    global $ds_curl;

    $videoObj = $ds_curl->curl_command('play', array("video" => $video));

    return $videoObj;

}

/*** IFRAME REPLACE ***/

add_action('wp', 'dsp_iframe_replace');
function dsp_iframe_replace()
{
    if (is_admin() || !get_option("ds_fancy_load")) {
        return;
    }

    // Start output and check HTML
    ob_start('dsp_iframe_html');
}



function generateRandomString($length = 5) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}



function dsp_iframe_html($html)
{

    $iframe_split = explode('<iframe', $html);
    foreach($iframe_split as $if){
        $split_two = explode('</iframe>', $if)[0];
        if(strpos($split_two, 'data-fancyloader') !== false){
    		$val = explode('"', explode('data-fancyloader="', str_replace("'", '"', $split_two))[1])[0];
    		if($val === 'false') continue;
		}
        $params = explode(' ', $split_two);
        $source = '';
        foreach($params as $param){
            if(strpos($param, 'src') !== false){
                $source = explode('"', explode('src="', str_replace("'", '"', $param))[1])[0];
                if(strpos($source, "dotstudiopro") !== false || strpos($source, 'dotstudiodev') !== false){
                    $video = explode("?", explode("/player/", $source)[1])[0];
                    $videoObj = grab_video($video);



                    $rndID = generateRandomString(5);

                    $strOut =  '<div id="' . $rndID . '_container" class="iframe_container" data-vidurl="' . $source . '" data-isplaying="0">';
                    $strOut .= '<a href="#' . $rndID . '" id="' . $rndID . '_link" class="iframe_launch"><i class="iframe_fa fa fa-play-circle-o"></i><img class="iframe_thumb" id="' . $rndID . '_thumb" src="' . $videoObj->socialImage . '" /></a>';
                    $strOut .= '<div id="' . $rndID . '_spinner" class="iframe_spinner_container" style="display:none;"><div class="iframe_spinner"></div></div>';
                    $iframe = '<iframe' . $split_two . '</iframe>';
                    $html = str_replace($iframe, $strOut, $html);


                }
            }
        }
    }
    return $html;
}

/*** REPLACE IFRAME ***/

function ds_check()
{

    global $ds_curl;

    $token = get_option('ds_curl_token');

    $token_time = !$token ? 0 : get_option('ds_curl_token_time');

    $difference = floor((time() - $token_time) / 84600);

    if (!$token || $difference >= 25) {

        ds_new_token();

    }

    $country = ds_get_country();

    if (!$country && !isset($ds_curl->country)) {

        // Talk to Joe about doing something

    }

}

function ds_no_country()
{

    $country = ds_get_country();

    if ($country) {

        return;

    }

    ?>
    <div class="update-nag">
        <p>Please check your dotstudioPRO API key.  We are having issues authenticating with our server.</p>
    </div>
    <?php

}

add_action("admin_notices", "ds_no_country");

function ds_channel_is_parent()
{

    global $wpdb, $post;

    $channel_check_grab = get_page_by_path('channels');

    $channel_check = $channel_check_grab->ID;

    if ($post->post_parent != $channel_check) {

        return false;

    }

    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type != 'revision' AND post_parent = " . $post->ID);

    if (count($results) > 0) {

        return true;

    } else {

        return false;

    }

}

function ds_channel_is_child()
{

    global $post;

    if ($post->post_parent == 0) {

        return false;

    }

    $channel_check_grab = get_page_by_path('channels');

    $channel_parent = $channel_check_grab->ID;

    $channel_grandparent = wp_get_post_parent_id($post->post_parent);

    if ($channel_parent == $channel_grandparent) {

        return true;

    } else {

        return false;

    }

}

function ds_headliner_video_for_template()
{

    if (!ds_channel_is_parent() && !ds_channel_is_child()) {

        echo get_query_var("video", false) ? channel_selected_video() : channel_first_video();

    } else if (ds_channel_is_child()) {

        echo get_query_var("video", false) ? child_channel_selected_video() : child_channel_first_video();

    }

}

function ds_get_playlist_for_template()
{

    if (ds_channel_is_parent()) {

        grab_parent_playlist();

    } else if (ds_channel_is_child()) {

        grab_child_playlist();

    } else {

        grab_playlist();

    }

}

add_action("init", function () {

    /*
$channels = list_channels();

print_r($channels);

die();
 */
});

function channels_check()
{

    global $wpdb;

    $channels = list_channels();

    $channel_check = get_page_by_path('channels');

    if (!$channel_check) {

        $channel_check_page_id = wp_insert_post(array(
            'post_title'   => "All Channels",
            'post_type'    => 'page',
            'post_name'    => "channels",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channels',
        ));

    } else {

        $channel_check_page_id = $channel_check->ID;

    }

    foreach ($channels as $c) {

        if (!isset($c->categories[0]->slug)) {

            continue;

        }

        $slug = rtrim(ltrim($c->slug, '-'), '-');

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' AND post_parent = $channel_check_page_id");

        if (count($check) > 0) {

            continue;

        }

        $name = isset($c->name) ? $c->name : '';

        $page_id = wp_insert_post(array(
            'post_title'   => $c->title,
            'post_type'    => 'page',
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel ' . $name,
            'post_parent'  => $channel_check_page_id,
        ));

        update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

        if (!empty($c->spotlight_poster)) {
            update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
        }

        if (count($c->childchannels) > 0) {

            $parent_id = $page_id;

            foreach ($c->childchannels as $ch) {

                $chname = isset($ch->name) ? $ch->name : '';

                $slug2 = rtrim(ltrim($ch->slug, '-'), '-');

                $page_id = wp_insert_post(array(
                    'post_title'   => $ch->title,
                    'post_type'    => 'page',
                    'post_name'    => $slug2,
                    'post_status'  => 'publish',
                    'post_excerpt' => 'Channel ' . $chname,
                    'post_parent'  => $parent_id,
                ));

                update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

                if (!empty($c->spotlight_poster)) {
                    update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
                }

            }

        }

    }

}

function channel_loop()
{

    $channels = list_channels();

    if (!$channels || count($channels) < 1) {

        echo "No channels to display.";

        return;
    }

    foreach ($channels as $c) {

        echo "Title: " . $c->title . "<br/>";

        echo "Image: <img src='" . $c->videos_thumb . "/380/215' /><br/>";

    }

}
/*
function category_channel_loop(){

global $post, $wpdb;

$channel_check_grab = get_page_by_path('channel-categories');

$category_parent = $channel_check_grab->ID;

$post_slug=$post->post_name;

if($post->post_parent != $category_parent){

return;

}

$channels = grab_category($post_slug);

if(!$channels || count($channels) < 1){

echo "No channels to display.";

return;
}

foreach($channels as $ch){

$id =  $ch->_id;

$thumb_id = $ch->video->thumb;

$slug =  $ch->slug;

$title = $ch->title;

$spotlight_poster = $ch->spotlight_poster;

$poster = $ch->poster;

$year = $ch->year;

$language = $ch->language;

$rating = $ch->rating;

$company = $ch->company;

$description = $ch->video->description;

echo "

<li class='gridder-list' data-griddercontent='#content1'>
<img class='channel-spotlight-poster' src='$spotlight_poster/400/225'>
<a href='".home_url("channels/$slug/")."'><img src='http://image.myspotlight.tv/$thumb_id/1280/720'></a>
<div id='content1' class='gridder-content'>
<span class='channel-title'>$title</span>
<span class='channel-poster-holder'><img class='channel-poster' src='$poster'></span>
<span class='channel-year'>Year: $year</span>
<span class='channel-language'>Language: $language</span>
<span class='channel-company>Company: $company</span>
<span class='channel-description'>Description: $description</span>

</div>
</li>

";

}

}
 */

function categories_loop()
{

    set_time_limit(120);

    $cat = list_categories();

    foreach ($cat as $c) {

        $post = get_page_by_path('channel-categories/' . $c->slug);

        $show_check = get_post_meta($post->ID, 'ds_show_category', true);

        if ($show_check != 1) {

            continue;

        }

        $image = get_option('ds-category-image-' . $c->slug);

        if ($image) {

            $thumb_id = $image;

        } else if (isset($c->image->poster)) {

            $thumb_id = $c->image->poster . "/960/540";

        } else if (isset($c->image->videos_thumb)) {

            $thumb_id = $c->image->videos_thumb . "/960/540";

        } else {

            $thumb_id = 'https://placehold.it/960x540';

        }

        echo "<li><a href='" . home_url("channel-categories/" . $c->slug) . "'><img class='img img-responsive' src='$thumb_id' /><label class='delay' style='display: inline-block;'><h1>" . $c->name . "</h1></label></a></li>";

    }

}

function categories_check()
{

    global $wpdb;

    $categories = list_categories();

    $category_post_check = get_page_by_path('channel-categories');

    if (!$category_post_check) {

        $category_page_id = wp_insert_post(array(
            'post_title'   => "Channel Categories",
            'post_type'    => 'page',
            'post_name'    => "channel-categories",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Categories',
        ));

    } else {

        $category_page_id = $category_post_check->ID;

    }

    foreach ($categories as $c) {

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $c->slug . "' AND post_type != 'nav_menu_item'");

        if (count($check) > 0) {

            continue;

        }

        $page_id = wp_insert_post(array(
            'post_title'   => $c->name,
            'post_type'    => 'page',
            'post_name'    => $c->slug,
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Category ' . $c->name,
            'post_parent'  => $category_page_id,
        ));

        update_post_meta($page_id, 'ds_show_category', 1);

        if ($c->homepage != 1) {

            update_post_meta($page_id, 'ds_show_category', 0);

        }

    }

}

/*** Various Meta Tags ***/

function ds_meta_tags()
{

    global $channel;

    if (!$channel) {

        return;

    }

    $meta = $channel['for_meta'];

    $description = str_replace('"', "'", $meta->description);

    if ($meta->channel_title == $meta->title) {

        $name_site = $meta->title . " - " . get_bloginfo('name');

        $name = $meta->title;

    } else {

        $name_site = $meta->title . " - " . $meta->channel_title . " - " . get_bloginfo('name');

        $name = $meta->title . " - " . $meta->channel_title;

    }

    ?><meta name="description" content="<?php echo $description; ?>">
    <meta property="fb:app_id" content="<?php echo get_option('ds_fb_app_id'); ?>" >
    <!-- OG meta --><meta property="og:site_name" content="<?php echo $name_site; ?>">
    <meta property="og:description" content="<?php echo $description; ?>">
    <meta property="og:type" content="website" >
    <meta property="og:url" content="<?php echo $meta->url; ?>">
    <meta property="og:title" content="<?php echo $name; ?>" >
    <meta property="og:image" content="<?php echo $meta->image_id; ?>">
    <meta property="og:image:width" content="640" >
    <meta property="og:image:height" content="360" >
    <!-- Twitter Summay Card -->
    <meta name="twitter:card" content="summary_large_image" >
    <meta name="twitter:title" content="<?php echo $name; ?>">
    <meta name="twitter:site" content="<?php echo get_option('ds_twitter_handle'); ?>">
    <meta name="twitter:creator" content="<?php echo get_option('ds_twitter_handle'); ?>">
    <meta name="twitter:description" content="<?php echo $description; ?>">
    <meta name="twitter:image" content="<?php echo $meta->image_id; ?>/640/360"><?php

}

/******* ******************/

/*** Code Wrappers for Misc. Things ***/

function ds_template_fb_code()
{

    ?>
    <div id="ds-comments">
       <div class="fb-comments" data-colorscheme="dark" data-href="<?php echo home_url(); ?>" data-width="100%" data-numposts="5"></div>
        </div>

      <div id="fb-root"></div>
      <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=243289792365862";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

    <?php

}

/*** Admin Options Save ***/

function ds_save_admin_options()
{

    if (isset($_POST['ds-save-admin-options'])) {

        ds_api_key_change();

        update_option('ds_fb_app_id', sanitize_text_field($_POST['ds_fb_app_id']));

        update_option('ds_twitter_handle', sanitize_text_field($_POST['ds_twitter_handle']));

        update_option('ds_player_slider_color', sanitize_text_field($_POST['ds_player_slider_color']));

        update_option('ds_plugin_style', sanitize_text_field($_POST['ds_plugin_style']));

        update_option('ds_light_theme_shadow', sanitize_text_field($_POST['ds_light_theme_shadow']));

        update_option('ds_channel_template', sanitize_text_field($_POST['ds_channel_template']));

        update_option('ds_fancy_load', sanitize_text_field($_POST['ds_fancy_load']));

        update_option('ds_development_check', sanitize_text_field($_POST['ds_development_check']));

        update_option('ds_development_country', sanitize_text_field($_POST['ds_development_country']));

        update_option('ds_plugin_custom_css', sanitize_text_field($_POST['ds_plugin_custom_css']));

        update_option('ds_comment_type', sanitize_text_field($_POST['ds_comment_type']));

        update_option('ds_token_reset', sanitize_text_field($_POST['ds_token_reset']));

    }

}

/*** Change Comment Type ***/

function ds_set_comments()
{

    global $wpdb;

    $type = get_option('ds_comment_type');

    if ($type == 'wordpress') {

        $channel_page = get_page_by_path('channels', OBJECT);

        $children = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = $channel_page->ID");

    }

}

/*** Channel Category Menu ***/

function ds_create_channel_category_menu()
{

    // Check if the menu exists
    $menu_name   = 'Browse Channel Categories';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    // If it doesn't exist, let's create it.
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        // Set up default menu items

        $cat = list_categories();

        foreach ($cat as $c) {

            $channels = grab_category($c->slug);

            $args = array(
                'menu-item-title'   => __($c->name),
                'menu-item-classes' => $c->slug,
                'menu-item-url'     => home_url('/channel-categories/' . $c->slug . '/'),
                'menu-item-status'  => 'publish');

            wp_update_nav_menu_item($menu_id, 0, $args);

        }

    }

}
add_action("init", "ds_create_channel_category_menu");

function ds_site_flush()
{

    global $wpdb;

    $all_cat_page = get_page_by_path('channel-categories');

    if (isset($all_cat_page->ID)) {

        $cats = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_cat_page->ID);

        foreach ($cats as $cat) {

            wp_delete_post($cat->ID, true); // Delete category page, force true deletion

            $wpdb->query("DELETE FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $cat->post_name . "' AND post_type = 'nav_menu_item'");

        }

    }

    $all_chan_page = get_page_by_path('channels');

    if (isset($all_chan_page->ID)) {

        $chans = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_chan_page->ID);

        foreach ($chans as $chan) {

            $child_check = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $chan->ID);

            foreach ($child_check as $child) {

                wp_delete_post($child->ID, true); // Delete child channel page, force true deletion

            }

            wp_delete_post($chan->ID, true); // Delete channel page, force true deletion

        }

    }

    wp_delete_nav_menu("Browse Channel Categories");

    // Rebuild Categories
    categories_check();

    // Rebuild Channels
    channels_check();

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options");
    exit;

}

function ds_template_copy()
{

    $error = "";

    $templates = array("ds-all-categories.tpl.php", "ds-single-category.tpl.php");

    $single_channel_templates = array("ds-single-channel.tpl.php", "ds-single-channel-w-sidebar.tpl.php");

    foreach ($templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . 'templates/' . $t;
        $theme_dir  = get_stylesheet_directory() . '/' . $t;

        if (!copy($plugin_dir, $theme_dir)) {
            $error = "&error=1";
        }
    }

    foreach ($single_channel_templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . 'templates/single_channel_templates/' . $t;
        $theme_dir  = get_stylesheet_directory() . '/' . $t;

        if (!copy($plugin_dir, $theme_dir)) {
            $error = "&error=1";
        }
    }

    $plugin_dir = plugin_dir_path(__FILE__) . 'templates/components/sharing.php';
    $theme_dir  = get_stylesheet_directory() . '/ds-sharing.php';

    if (!copy($plugin_dir, $theme_dir)) {
        $error = "&error=1";
    }

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options$error");

}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['flush']) && $_GET['flush'] == 1) {

    add_action("init", "ds_site_flush");

}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['templatecopy']) && $_GET['templatecopy'] == 1) {

    add_action("init", "ds_template_copy");

}

function ds_api_key_change()
{

    set_time_limit(120);

    global $wpdb, $ds_curl;

    // If the API key changes in any way, we need to delete the existing pages and grab new ones.
    // This is a fairly intensive action once the key changes.

    // If the api key isn't posted, nothing to do here.
    if (!isset($_POST['ds_api_key'])) {

        return;

    }

    $api = get_option('ds_api_key');

    // If the api key is posted, but hasn't changed, nothing to do here.
    if ($api == $_POST['ds_api_key'] && !isset($_POST['ds_token_reset'])) {

        return;

    }

    update_option('ds_api_key', sanitize_text_field($_POST['ds_api_key'])); // Force early API key update, in case we haven't updated it yet, so we get a valid token.

    $token = $ds_curl->curl_command('token'); // Since we determined the API has changed, update token, since the new API key is being stored.

    update_option('ds_curl_token', $token);

    update_option('ds_curl_token_time', time());

    // If we have an API key change, we get to delete all of the pages we've created.
    // Please note that, because this function will be ran within ds_check(), we don't
    // need to do the re-creation.

    $all_cat_page = get_page_by_path('channel-categories');

    if (isset($all_cat_page->ID)) {

        $cats = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_cat_page->ID);

        foreach ($cats as $cat) {

            wp_delete_post($cat->ID, true); // Delete category page, force true deletion

            $wpdb->query("DELETE FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $cat->post_name . "' AND post_type = 'nav_menu_item'");

        }

    }

    $all_chan_page = get_page_by_path('channels');

    if (isset($all_chan_page->ID)) {

        $chans = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_chan_page->ID);

        foreach ($chans as $chan) {

            $child_check = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $chan->ID);

            foreach ($child_check as $child) {

                wp_delete_post($child->ID, true); // Delete child channel page, force true deletion

            }

            wp_delete_post($chan->ID, true); // Delete channel page, force true deletion

        }

    }

    wp_delete_nav_menu("Browse Channel Categories");

    // Rebuild Categories
    categories_check();

    // Rebuild Channels
    channels_check();

}

function ds_add_custom_css()
{

    echo "\n<style>" . get_option('ds_plugin_custom_css') . "</style>\n\n";

}

add_action('wp_head', 'ds_add_custom_css', 999);

/** Custom category images **/

add_action('post_edit_form_tag', 'add_post_enctype');

function add_post_enctype()
{
    echo ' enctype="multipart/form-data"';
}

add_action("admin_init", "ds_category_images_init");

// Initialize the necessary functions for extra fields on Sales Pages
function ds_category_images_init()
{

    $post = get_post($_GET['post']);

    $categories = get_page_by_path("channel-categories");

    if ($post->post_parent == $categories->ID) {

        add_meta_box("ds_category_image", "Category Image", "ds_category_image_field", "page", "normal", "high");

    }

}

function ds_category_image_field()
{

    $post = get_post($_GET['post']);

    $image = get_option('ds-category-image-' . $post->post_name);

    $cat_image = '';

    if ($image) {

        $cat_image = "<img src='$image' />";

    }

    ?>

            <table class='widefat'>

                <tbody>

                    <tr><td><h3>Upload new image</h3></td><td><input type='file' name='ds-category-image' /></td></tr>

                    <tr><td><h3>Current image</h3></td><td><?php echo $cat_image; ?></td></tr>

                </tbody>

            </table>

        <?php

}

// You codes

function ds_cust_filename($dir, $name, $ext)
{

    return $_FILES['ds-category-image']['name'] . rand(100, 999) . time() . $ext;

}

function ds_save_category_image_field()
{

    if (!isset($_FILES['ds-category-image'])) {

        return;

    }

    global $post;

    $slug = $post->post_name;

    $uploadedfile = $_FILES['ds-category-image'];

    $movefile = wp_handle_upload($uploadedfile, array('test_form' => false, 'unique_filename_callback' => 'ds_cust_filename'));

    if ($movefile && !isset($movefile['error'])) {

        update_option("ds-category-image-$slug", $movefile['url']);

    }

}

add_action("save_post", "ds_save_category_image_field");

function ds_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header)
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $curl_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => $curl_request_type,
        CURLOPT_POSTFIELDS     => $curl_post_fields,
        CURLOPT_HTTPHEADER     => $curl_header,
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);

    curl_close($curl);

    return (object) compact('response', 'err');
}
