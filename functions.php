<?php


/** Custom category images **/

function add_post_enctype()
{
    echo ' enctype="multipart/form-data"';
}

// Initialize the necessary functions for extra fields on Sales Pages
function ds_category_images_init()
{
    if (empty($_GET['post'])) {
        return false;
    }

    $post       = get_post($_GET['post']);
    $categories = get_page_by_path("channel-categories");

    if ((int) $post->post_parent === (int) $categories->ID) {
        add_meta_box("ds_category_image", "Category Image", "ds_category_image_field", "page", "normal", "high");
    }
}

function ds_category_image_field()
{
    // Create the field for uploading custom category images.
    $post      = get_post($_GET['post']);
    $image     = get_option('ds-category-image-' . $post->post_name);
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
    $slug         = $post->post_name;
    $uploadedfile = $_FILES['ds-category-image'];
    $movefile     = wp_handle_upload($uploadedfile, array('test_form' => false, 'unique_filename_callback' => 'ds_cust_filename'));
    if ($movefile && !isset($movefile['error'])) {
        update_option("ds-category-image-$slug", $movefile['url']);
    }
}

function ds_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header)
{
    // Simplify the cURL execution for various API commands within the curl commands class
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL            => $curl_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => $curl_request_type,
        CURLOPT_POSTFIELDS     => $curl_request_type == 'POST' ? $curl_post_fields : "",
        CURLOPT_HTTPHEADER     => $curl_header,
    ));

    $response = curl_exec($curl);
    $err      = curl_error($curl);

    curl_close($curl);
    return (object) compact('response', 'err');
}

function display_channel_video_player()
{
    $vidPlayerFile = "channel-video.php";

    if (is_file(dirname(__FILE__) . "/templates/components/" . $vidPlayerFile)) {
        include dirname(__FILE__) . "/templates/components/" . $vidPlayerFile;
    }

}

function ds_home_template($single_template)
{

    global $post;

    if ($post->post_name == 'home') {

        $single_template = locate_template('ds-home.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {
            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/templates/ds-home.tpl.php';
        }
    }
    // Return either the template we made, or the template in the theme folders.
    return $single_template;
}

function ds_all_categories_template($single_template)
{

    global $post;

    if ($post->post_name == 'channel-categories') {

        $single_template = locate_template('ds-all-categories.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {
            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/templates/ds-all-categories.tpl.php';
        }
    }
    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

function ds_get_category_template($single_template)
{

    global $post;

    $category_check_grab = get_page_by_path('channel-categories');

    $category_parent = $category_check_grab->ID;

    if ($post->post_parent == $category_parent) {

        $single_template = locate_template('ds-single-category.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname(__FILE__) . '/templates/ds-single-category.tpl.php';

        }

    }

    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

function ds_get_channel_template($single_template)
{

    global $post;

    $channel_check_grab = get_page_by_path('channels');

    $channel_parent = $channel_check_grab->ID;

    $channel_grandparent = wp_get_post_parent_id($post->post_parent);

    if ($post->post_parent == $channel_parent || $channel_grandparent == $channel_parent) {

        $template_option = get_option('ds_channel_template');

        $single_template = locate_template($template_option . '.tpl.php');

        // Set the template...
        if (empty($single_template) !== false) {

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...

            if (!$template_option || $template_option == 'default') {

                $template_option = "ds-single-channel";

            }

            $single_template = dirname(__FILE__) . '/templates/' . $template_option . '.tpl.php';

        }

    }

    // Return either the template we made, or the template in the theme folders.

    return $single_template;
}

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

function ds_theater_mode_playlist($videoId)
{
    $strOut = "";
    $strOut .= "<div class='row'>";
    $strOut .= "    <!-- THEATER MODE PLAYLIST -->";
    $strOut .= "    <div class='col-md-12 col-sm-12 col-xs-12 ds-vid-playlist ds-playlist-theater-mode'>";
    $strOut .= "        <div class='ds-playlist-theater-outer-container'>";
    $strOut .= "            <div><label>Related Videos</label></div>";
    $strOut .= "                <div class='ds-playlist-theater-inner-container'>";
    $strOut .= "                    <div class='ds-playlist-theater-mode-wrapper'>";
    $strOut .= "                         <div class='related-videos-carousel'>";
    $strOut .= ds_owl_recommended_videos_html(array('video_id' => $videoId, 'rec_size' => 8));
    $strOut .= "                          </div>";
    $strOut .= "                     </div>";
    $strOut .= "                </div>";
    $strOut .= "          </div>";
    $strOut .= "    </div>";
    $strOut .= "</div>";
    return $strOut;
}

function ds_owl_carousel_check_main_plugin()
{

    ?>

    <div class="update-nag">
        <p>dotstudioPRO Premium Video plugin is not installed, is inactive, or the version is too low for this add-on.  The dotstudioPRO Premium Owl Carousel plugin has been deactivated.</p>
    </div>

    <?php

}

function ds_owl_admin_animation_select($name, $className = '')
{
    $aryAnimations = ['bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'swing', 'tada', 'wobble', 'jello', 'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp', 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY', 'lightSpeedIn', 'lightSpeedOut', 'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight', 'hinge', 'rollIn', 'rollOut', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp', 'zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp', 'slideInDown', 'slideInLeft', 'slideInRight', 'slideInUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight', 'slideOutUp'];
    $strOut        = '<select name=' . $name . ' id=' . $name . ' class="' . $className . '" disabled=disabled>';
    $strOut .= '<option value="">-- None --</option>';
    for ($i = 0; $i <= count($aryAnimations) - 1; $i++) {
        $strOut .= '<option value=' . $aryAnimations[$i] . '>' . $aryAnimations[$i] . '</option>';
    }
    $strOut .= '<select>';
    return $strOut;

}

function ds_owl_carousel()
{

    wp_enqueue_script('owl-carousel', plugin_dir_url(__FILE__) . 'js/owl.carousel.min.js', array('jquery'));
    //wp_enqueue_script( 'owl-carousel-custom', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.custom.min.js' );
    wp_enqueue_style('owl-carousel-min', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css');
    wp_enqueue_style('ds-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');

}

function ds_owl_carousel_html($args)
{

    if ($args['channels'] !== '') {
        // generate the code for showing items within a channel
        return ds_owl_channel_html($args);
    }
    if ($args['category'] !== '') {
        // generate the code for showing items within a category
        return ds_owl_category_html($args);
    }
}

function ds_owl_recommended_videos_html($args)
{
    // renders the recommended videos playlist for the channel video player

    $video_id = $args['video_id'];
    $rec_size = $args['rec_size'];

    $recommended = list_recommended($video_id, $rec_size);

    // error checking
    if ($recommended[0] === false) {
        $strOut = $recommended[1];
    } else {
        $opts = ds_owl_create_opts(array(
            'autoplay_hover_pause' => '1',
            'autoplay'             => '0',
            'autoplay_timeout'     => '3000',
            'autoplay_speed'       => '1000',
            'notitle'              => '1',
            'items'                => '8',
        ));

        $rndId  = ds_owl_carousel_rnd_id(5);
        $strOut = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
        $strOut .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

        foreach ($recommended as $video) {
            $info     = $video->_source;
            $video_id = $video->_id;

            $title       = $info->title;
            $image       = $info->thumb;
            $id          = $video->_id;
            $company_id  = $info->company_id;
            $description = 'No description currently available';
            $slug        = '';

            if (trim($title . '') !== '') {
                $description = strlen($description) > 150 ? substr($description, 0, 150) . "..." : $description;
                $title       = strlen($title) > 50 ? substr($title, 0, 50) . "..." : $title;
                $strOut .= "<div class='center-container item'>";
                $strOut .= "        <div>";
                $strOut .= "            <i class='ds-owl-fa fa fa-play-circle-o fa-3' aria-hidden='true'></i>";
                $strOut .= "            <a href='#$video_id' class='vert-center rec-list-item' data-title='$title' data-desc='$description'>";
                $strOut .= "                <img class='owl-thumb' src='https://image.dotstudiopro.com/$image/177/100' />";
                $strOut .= "            </a>";
                $strOut .= "        </div>";
                $strOut .= "        <div><strong><small class='owl-carousel-subtitle'>$title</small></strong></div>";
                $strOut .= "</div>";
            }

        }
        $strOut .= "</div>";

    }

    return $strOut;

}

function ds_owl_category_html($args)
{

    $category_slug = $args['category'];
    $category      = get_page_by_path('/channel-categories/' . $category_slug, OBJECT);
    $title         = $args['title'] !== '' ? $args['title'] : $category->post_title;
    $opts          = ds_owl_create_opts($args);
    $rndId         = ds_owl_carousel_rnd_id(5);
    $titleclass    = $args['titleclass'];

    $carousel = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if ($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2><a class='owl-carousel-ellipsis' href='/channel-categories/$category_slug/' title='More...'>...</a></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    $catItems = grab_category($category_slug);

    if ($catItems && is_array($catItems)) {

        foreach ($catItems as $ch) {
            // iterate thru the channels, get the applicable thumbnails, create the HTML output

            $id               = $ch->_id;
            $thumb_id         = isset($ch->videos_thumb) ? $ch->videos_thumb : '';
            $slug             = $ch->slug;
            $spotlight_poster = isset($ch->spotlight_poster) ? $ch->spotlight_poster : '';

            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <a href='" . home_url("channels/$slug") . "' class='vert-center'>";
            $carousel .= "              <img class='owl-thumb' src='$spotlight_poster/1280/720' />";
            $carousel .= "          </a>";
            $carousel .= "      </div>";
            $carousel .= "</div>";
        }
    }

    $carousel .= "</div>";
    return $carousel;
}

function ds_owl_channel_html($args)
{

    if (strpos($args['channels'], ',') !== false) {
        $channels = explode(',', $args['channels']);
    } else {
        $channels = array($args['channels']);
    }
    $rndId      = ds_owl_carousel_rnd_id(5);
    $objects    = ds_owl_carousel_build_objects($channels);
    $opts       = ds_owl_create_opts($args);
    $title      = $args['title'] !== '' ? $args['title'] : 'Featured Channels';
    $titleclass = $args['titleclass'];

    $carousel = "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if ($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    foreach ($objects as $o) {
        if (trim($o->title . '') !== '') {
            $description = strlen($o->description) > 150 ? substr($o->description, 0, 150) . "..." : $o->description;
            $title       = strlen($o->title) > 20 ? substr($o->title, 0, 20) . "..." : $o->title;
            $imageexp    = explode("/", $o->poster);
            $image       = $imageexp[3];
            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <i class='ds-owl-fa fa fa-play-circle-o fa-3' aria-hidden='true'></i>";
            $carousel .= "          <a href='" . home_url("channels/$o->slug") . "' class='vert-center' data-title='$o->title' data-desc='$description'>";
            $carousel .= "              <img class='owl-thumb' src='https://image.dotstudiopro.com/$image/1280/720' />";
            $carousel .= "          </a>";
            $carousel .= "      </div>";
            $carousel .= "      <div><strong><small class='owl-carousel-subtitle'>$o->title</small></strong></div>";
            $carousel .= "</div>";
        }

    }

    $carousel .= "</div>";

    return $carousel;
}

function ds_owl_create_opts($args)
{
    unset($args['channels']);
    unset($args['category']);
    unset($args['title']);

    $opts = implode(', ', array_map(
        function ($v, $k) {return sprintf("%s=%s", trim($k), trim($v));},
        $args,
        array_keys($args)
    ));

    return $opts;
}

function ds_owl_carousel_rnd_id($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function ds_owl_carousel_build_objects($ids = array())
{
    $objs = array();
    foreach ($ids as $id) {
        $obj    = ds_owl_grab_channel_by_id($id);
        $objs[] = $obj[0];
    }
    return $objs;
}

function ds_owl_grab_channel_by_id($id)
{
    global $ds_curl;
    $channel = $ds_curl->curl_command('single-channel-by-id', array('channel_slug' => str_replace(" ", "", $id)));
    return $channel;

}

function ds_owl_carousel_local_channels_list()
{

    global $wpdb;

    $channel_parent = get_page_by_path("channels");

    $channels = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $channel_parent->ID . " ORDER BY post_name ASC");

    $channels_list = "";

    foreach ($channels as $ch) {

        $channels_list .= "<input type='checkbox' name='channel' value='$ch->post_name'> $ch->post_title<br/>";

    }

    return $channels_list;

}

function ds_owl_carousel_display_shortcode($atts)
{

    $args = shortcode_atts(array(

        'channels'             => '',
        'category'             => '',
        'title'                => '',
        'autoplay'             => true,
        'dots'                 => false,
        'autoplay_timeout'     => 3000,
        'autoplay_speed'       => 1000,
        'autoplay_hover_pause' => false,
        'items'                => 3,
        'slide_by'             => 1,
        'animate_out'          => '',
        'animate_in'           => '',
        'dots'                 => false,
        'rtl'                  => false,
        'nav'                  => false,
        'notitle'              => false,
        'titleclass'           => '',

    ), $atts, 'ds_owl_carousel');

    if (!count($args['channels']) || !count($args['category'])) {
        return;
    }

    return ds_owl_carousel_html($args);

}
add_shortcode('ds_owl_carousel', 'ds_owl_carousel_display_shortcode');

function channel_headline_video()
{

    global $ds_curl;

    $video = get_query_var("video", false);

    if (ds_channel_is_child()) {

        $videos = grab_channel();

        if (!is_array($videos)) {

            $videos = new stdClass;

            return $videos;

        }

        $playlist = $videos[0]->childchannels[0]->playlist[0];

        $id = $playlist->_id;

        $title = $playlist->title;

        $duration = round($playlist->duration / 60);

        $description = isset($videos[0]->description) ? $videos[0]->description : '';

        $company = isset($videos[0]->company) ? $videos[0]->company : '';

        $company_id = isset($videos[0]->childchannels[0]->company_id) ? $videos[0]->childchannels[0]->company_id : $videos[0]->spotlight_company_id;

        $country = isset($playlist->country) ? $playlist->country : '';

        $language = isset($playlist->language) ? $playlist->language : '';

        $year = isset($videos[0]->year) ? $videos[0]->year : '';

        $rating = isset($videos[0]->rating) ? $videos[0]->rating : '';

        if ($video) {
            $id = get_query_var("video", false);

            foreach ($videos[0]->childchannels[0]->playlist as $pl) {

                if ($pl->_id == $id) {

                    $title = $pl->title;

                    $duration = round($pl->duration / 60);

                    $description = $pl->description;

                    $country = $pl->country;

                    $language = $pl->language;

                    break;

                }

            }

        }

        $player_url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

        $to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $description, 'company' => $company, 'country' => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);

        return $to_return;

    } else {

        $videos = grab_channel();

        if (!is_array($videos)) {

            $videos = new stdClass;

            return $videos;

        }

        $id = $videos[0]->playlist[0]->_id;

        $title = isset($videos[0]->playlist[0]->title) ? $videos[0]->playlist[0]->title : isset($videos[0]->video->title) ? $videos[0]->video->title : '';

        $duration = isset($videos[0]->playlist[0]->duration) ? round($videos[0]->playlist[0]->duration / 60) : isset($videos[0]->video->duration) ? round($videos[0]->video->duration / 60) : '';

        $chdescription = "";
        if (isset($videos[0]->video->description)) {

            $chdescription = $videos[0]->video->description;

        } else if (isset($videos[0]->playlist[0]->description)) {

            $chdescription = $videos[0]->playlist[0]->description;

        } else if (isset($videos[0]->video->country)) {

            $chdescription = $videos[0]->video->country;

        }

        $company = isset($videos[0]->company) ? $videos[0]->company : '';

        $company_id = isset($videos[0]->playlist[0]->company_id) ? $videos[0]->playlist[0]->company_id : $videos[0]->spotlight_company_id;

        $country = isset($videos[0]->playlist[0]->country) ? $videos[0]->playlist[0]->country : isset($videos[0]->video->country) ? $videos[0]->video->country : '';

        $language = isset($videos[0]->playlist[0]->language) ? $videos[0]->playlist[0]->language : isset($videos[0]->video->language) ? $videos[0]->video->language : '';

        $year = isset($videos[0]->year) ? $videos[0]->year : '';

        $rating = isset($videos[0]->rating) ? $videos[0]->rating : '';

        if ($video) {

            $id = get_query_var("video", false);

            foreach ($videos[0]->playlist as $pl) {

                if ($pl->_id == $id) {

                    $title = $pl->title;

                    $duration = round($pl->duration / 60);

                    $chdescription = $pl->description;

                    $country = $pl->country;

                    $language = $pl->language;

                    break;

                }

            }

        }

        if (!$id) {

            $id = $videos[0]->video->_id;

        }

        wp_register_script('channel-video-functions', plugins_url('js/channel.video.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-video-functions');

        wp_register_script('channel-display-functions', plugins_url('js/channel.display.functions.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('channel-display-functions');

        wp_enqueue_style('video-playlist', plugins_url('dotstudiopro-wordpress/css/video-playlist.css'));

        $video_custom_css = locate_template('video.channel.customization.css');

        if (!empty($video_custom_css)) {
            wp_enqueue_style('video-custom', get_template_directory_uri() . '/video.channel.customization.css');
        } else {
            wp_enqueue_style('video-custom', plugin_dir_url(__FILE__) . 'css/video.channel.customization.css');
        }

        $player_url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=" . get_option("ds_player_slider_color", "228b22") . "&autostart=" . (get_option("ds_player_autostart", 0) == 1 ? "true" : "false") . "&sharing=" . (get_option("ds_player_sharing", 0) == 1 ? "true" : "false") . "&muteonstart=" . (get_option("ds_player_mute", 0) == 1 ? "true" : "false") . "&disablecontrolbar=" . (get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");

        $to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $chdescription, 'company' => $company, 'country' => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);

        return $to_return;

    }

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

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['flush']) && $_GET['flush'] == 1) {
    add_action("init", "ds_site_flush");
}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['templatecopy']) && $_GET['templatecopy'] == 1) {
    add_action("init", "ds_template_copy");
}

add_filter('query_vars', 'ds_video_var');
add_filter('page_template', 'ds_get_channel_template', 11);
add_filter('page_template', 'ds_get_category_template', 11);
add_filter('page_template', 'ds_all_categories_template', 11);
add_filter('page_template', 'ds_home_template', 11);

add_action("admin_notices", "ds_no_country");
add_action('wp_enqueue_scripts', 'ds_scripts_load_cdn');
add_action("wp_head", "ds_light_theme_shadows", 990);
add_action('wp_enqueue_scripts', 'ds_styles');
add_action('admin_notices', 'ds_check_api_key_set');
add_action("init", "ds_get_country");
add_action('wp', 'ds_iframe_replace');
add_action("init", "ds_create_channel_category_menu");
add_action('wp_head', 'ds_add_custom_css', 999);
add_action('post_edit_form_tag', 'add_post_enctype');
add_action("admin_init", "ds_category_images_init");
add_action("save_post", "ds_save_category_image_field");
