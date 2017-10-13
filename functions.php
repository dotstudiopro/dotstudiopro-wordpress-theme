<?php

set_time_limit(240);

$ds_curl = new DotStudioz_Commands;

function ds_scripts_load_cdn()
{

    global $wpdb, $post;
    // Deregister the included library
    wp_deregister_script('jquery');

    // Register the library again from Google's CDN
    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js', array(), null, false);

    // Register the script like this for a plugin:
    wp_register_script('custom-script', plugins_url('/js/channel.functions.min.js', __FILE__), array('jquery'));
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script('custom-script');

    $channels = get_page_by_path('channels');

    $categories = get_page_by_path('channel-categories');

    $parent = get_post($post->post_parent);

    if ($post->post_parent == $categories->ID || $parent->post_parent == $channels->ID || $parent->post_parent == $categories->ID || $channels->ID == $post->ID || $categories->ID == $post->ID) {

        // Register the script like this for a plugin:
        wp_register_script('grid-script', plugins_url('/js/jquery.gridder.min.js', __FILE__), array('jquery'));
        // For either a plugin or a theme, you can then enqueue the script:
        wp_enqueue_script('grid-script');

    }

}

function ds_plugin_style()
{

    // Check the style option and set up light or dark, depending

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


function ds_styles()
{

    wp_register_style('font-awesome-style', plugins_url('/css/font-awesome.min.css?v=1234', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('font-awesome-style');

    wp_register_style('animate-style', plugins_url('/css/animate.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('animate-style');

    wp_register_style('grid-style', plugins_url('/css/grid.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('grid-style');

    wp_register_style('ds-style', plugins_url('/css/style.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('ds-style');

    // Styles for the FancyFrame portion of this plugin:
    wp_register_style('fancyframe-style', plugins_url('/css/fancyframes.css', __FILE__), array(), '20120208', 'all');
    wp_enqueue_style('fancyframe-style');



}

function ds_video_var($public_query_vars)
{
    $public_query_vars[] = 'video';
    return $public_query_vars;
}


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


function ds_new_token()
{

    // Acquire an API token and save it for later use.

    global $ds_curl;

    $token = $ds_curl->curl_command('token');

    update_option('ds_curl_token', $token);

    update_option('ds_curl_token_time', time());

}

function ds_get_country()
{

    // Get the current user's country based on IP

    global $ds_curl;

    $country = $ds_curl->curl_command('country');

    return $country;

}



function list_recommended($video_id='', $rec_size=8) {
    global $ds_curl;
    $result = $ds_curl->curl_command('recommended',array("rec_size" => $rec_size, "video_id" => $video_id));
    return $result;
}


function list_channels() {
    global $ds_curl;
    $channels = $ds_curl->curl_command('all-channels');
    return $channels;
}


function list_categories() {
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



function channel_revision_check() {
    // Check if we have revisions to the current channel page
    global $wpdb, $post;
    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type = 'revision' AND post_parent = " . $post->ID);
    if (count($results) > 0) {
        return true;
    } else {
        return false;
    }
}



function grab_channel() {
    global $ds_curl;
    $channels = $ds_curl->curl_command('single-channel');
    return $channels;
}



function grab_parent_channel() {
    global $ds_curl;
    $channels = $ds_curl->curl_command('parent-channel');
    return $channels;
}



function grab_category($category) {
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


function ds_iframe_replace() {
    if (is_admin()) {
        return;
    }
    // Start output and check HTML
    ob_start('ds_iframe_html');
}



function generateRandomString($length = 5) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}



function ds_iframe_html($html) {
    // Replace <iframe> code with a div that loads the iframe based on scroll.
    $iframe_split = explode('<iframe', $html);
    foreach($iframe_split as $if){
        $split_one = explode('</iframe>', $if);
        $split_two = $split_one[0];
        $params = explode(' ', $split_two);
        $source = '';
        foreach($params as $param){
            if(strpos($param, 'src') !== false && strpos($split_two, 'nofancyframe') === false){
                $source_split1 = explode('src="', str_replace("'", '"', $param));
                $source_split2 = explode('"', $source_split1[1]);
                $source = $source_split2[0];
                if(strpos($source, "dotstudiopro") !== false || strpos($source, 'dotstudiodev') !== false) {
                    $video_explode1 = explode("/player/", $source);
                    $video_explode2 = explode("?", $video_explode1[1]);
                    $video = $video_explode2[0];
                    $videoObj = grab_video($video);
                    $posterImg = $videoObj->thumb . "/1000/562";
                    $rndID = generateRandomString(5);
                    $strOut = '';
                    $strOut .=  '<div id="' . $rndID . '_container" class="iframe_container" data-vidurl="' . $source . '" data-isplaying="0">';
                    $strOut .= '<a href="#' . $rndID . '" id="' . $rndID . '_link" class="iframe_launch"><i class="iframe_fa fa fa-play-circle-o"></i><img class="iframe_thumb" id="' . $rndID . '_thumb" src="' . $posterImg . '" /></a>';
                    $strOut .= '<div id="' . $rndID . '_spinner" class="iframe_spinner_container" style="display:none;"><div class="iframe_spinner"></div></div>';
                    $iframe = '<iframe' . $split_two . '</iframe>';
                    $html = str_replace($iframe, $strOut, $html);
                }
            }
        }
    }
    return $html;
}





function ds_check() {
    global $ds_curl;
    $token = get_option('ds_curl_token');
    $token_time = !$token ? 0 : get_option('ds_curl_token_time');
    $difference = floor((time() - $token_time) / 84600);
    if (!$token || $difference >= 25) {
        ds_new_token();
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



function ds_channel_is_parent() {
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



function ds_channel_is_child() {
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




function ds_headliner_video_for_template() {
    if (!ds_channel_is_parent() && !ds_channel_is_child()) {
        echo get_query_var("video", false) ? channel_selected_video() : channel_first_video();
    } else if (ds_channel_is_child()) {
        echo get_query_var("video", false) ? child_channel_selected_video() : child_channel_first_video();
    }
}



function channels_check()
{

    // Completely reprocess/recreate all channel pages.  This is done when the admin requests a flush.

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

        $channel_info = !empty($c->description) ? $c->description : !empty($c->video->description) ? $c->video->description : $c->title;

        if(empty($channel_info)) $channel_info = "No description.";

        $page_id = wp_insert_post(array(
            'post_title'   => !empty($c->title) ? $c->title : ucwords(str_replace('-', ' ', $slug)),
            'post_type'    => 'page',
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_excerpt' => $channel_info,
            'post_parent'  => $channel_check_page_id,
        ));

        update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

        if (!empty($c->spotlight_poster)) {
            // Set up a spotlight poster for use in things like the iframe replacement div
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
                    // Set up a spotlight poster for use in things like the iframe replacement div
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

    // Completely reprocess/recreate all category pages.  This is done when the admin requests a flush.

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

        if (!empty($c->image->spotlight_poster)) {
            update_post_meta($page_id, 'ds-spotlight-poster', $c->image->spotlight_poster);
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

        update_option('ds_development_check', sanitize_text_field($_POST['ds_development_check']));

        update_option('ds_development_country', sanitize_text_field($_POST['ds_development_country']));

        update_option('ds_plugin_custom_css', sanitize_text_field($_POST['ds_plugin_custom_css']));

        update_option('ds_comment_type', sanitize_text_field($_POST['ds_comment_type']));

        update_option('ds_player_mute', sanitize_text_field($_POST['ds_player_mute']));

        update_option('ds_token_reset', sanitize_text_field($_POST['ds_token_reset']));

        update_option('ds_auto_assign_menu', sanitize_text_field($_POST['ds_auto_assign_menu']));

        update_option('ds_player_autoplay', sanitize_text_field($_POST['ds_player_autoplay']));

        update_option('ds_player_autoredir', sanitize_text_field($_POST['ds_player_autoredir']));

        update_option('ds_player_minivid', sanitize_text_field($_POST['ds_player_minivid']));

        update_option('ds_player_recplaylist', sanitize_text_field($_POST['ds_player_recplaylist']));

        update_option('ds_fancy_load',sanitize_text_field($_POST['ds_fancy_load']));

        update_option('ds_show_playlist_above_meta',sanitize_text_field($_POST['ds_show_playlist_above_meta']));



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

        $auto_assign = get_option('ds_auto_assign_menu');

        if(empty($auto_assign)) return;

        // Set the main menu up in the correct spot post-flush.
        $locations = get_theme_mod('nav_menu_locations');
        $locations['main_nav'] = $menu_id;
        $locations['header_nav'] = $menu_id;
        $locations['top'] = $menu_id; // WP 2017
        set_theme_mod( 'nav_menu_locations', $locations );

    }



}

function ds_site_flush()
{

    // Completely delete and recreate all category and channel pages.  This is necessary when channels are created or deleted in the DSP dashboard, as well as categories.  There are other use cases, but that is the most common.

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

    ds_create_channel_category_menu();

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options");
    exit;

}

function ds_templates_exist() {
    $templates = array("ds-all-categories.tpl.php",
        "ds-single-category.tpl.php",
        "ds-home.tpl.php",
        "ds-single-channel.tpl.php",
        "ds-single-channel-w-sidebar.tpl.php",
        "video.channel.customization.css",
        "ds-sharing.php");

    foreach($templates as $t) {
        $file_path = get_stylesheet_directory() . '/' . $t;
        if(file_exists($file_path)) {
            return true;
        }
    }
    return false;
}


function ds_template_copy()
{

    // Copy the page templates to the current active theme directory for manipulation by the admin without having to edit our specific template files.

    $error = "";

    $templates = array("ds-all-categories.tpl.php", "ds-single-category.tpl.php","ds-home.tpl.php");
    $single_channel_templates = array("ds-single-channel.tpl.php", "ds-single-channel-w-sidebar.tpl.php");

    foreach ($templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . 'templates/' . $t;
        $theme_dir  = get_stylesheet_directory() . '/' . $t;

        if (!copy($plugin_dir, $theme_dir)) {
            $error = "&error=1";
        }
    }

    foreach ($single_channel_templates as $t) {
        $plugin_dir = plugin_dir_path(__FILE__) . 'templates/' . $t;
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

    $plugin_dir = plugin_dir_path(__FILE__) . 'css/video.channel.customization.css';
    $theme_dir  = get_stylesheet_directory() . '/video.channel.customization.css';

    if (!copy($plugin_dir, $theme_dir)) {
        $error = "&error=1";
    }


    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options$error");

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

    ds_create_channel_category_menu();

}




function ds_add_custom_css()
{
    echo "\n<style>" . get_option('ds_plugin_custom_css') . "</style>\n\n";

}

/** Custom category images **/



function add_post_enctype()
{
    echo ' enctype="multipart/form-data"';
}



// Initialize the necessary functions for extra fields on Sales Pages
function ds_category_images_init()
{
    if(empty($_GET['post'])) return false;
    $post = get_post($_GET['post']);
    $categories = get_page_by_path("channel-categories");

    if ((int) $post->post_parent === (int) $categories->ID) {
        add_meta_box("ds_category_image", "Category Image", "ds_category_image_field", "page", "normal", "high");
    }
}




function ds_category_image_field() {
    // Create the field for uploading custom category images.
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





function ds_cust_filename($dir, $name, $ext) {
    return $_FILES['ds-category-image']['name'] . rand(100, 999) . time() . $ext;
}




function ds_save_category_image_field() {
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


function ds_run_curl_command($curl_url, $curl_request_type, $curl_post_fields, $curl_header) {
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




function display_channel_video_player() {
    $vidPlayerFile = "channel-video.php";

    if(is_file( dirname( __FILE__ ) ."/templates/components/".$vidPlayerFile )) {
        include( dirname( __FILE__ ) ."/templates/components/".$vidPlayerFile );
    }

}



function ds_home_template($single_template) {

    global $post;

  if ($post->post_name == 'home') {

        $single_template = locate_template( 'ds-home.tpl.php' );

        // Set the template...
        if( empty($single_template) !== FALSE ){
            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname( __FILE__ ) . '/templates/ds-home.tpl.php';
        }
   }
     // Return either the template we made, or the template in the theme folders.
     return $single_template;
}




function ds_all_categories_template($single_template) {

    global $post;


  if ($post->post_name == 'channel-categories') {

        $single_template = locate_template( 'ds-all-categories.tpl.php' );

        // Set the template...
        if( empty($single_template) !== FALSE ){
            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
            $single_template = dirname( __FILE__ ) . '/templates/ds-all-categories.tpl.php';
        }
   }
     // Return either the template we made, or the template in the theme folders.

     return $single_template;
}


function ds_get_category_template($single_template) {

    global $post;

    $category_check_grab = get_page_by_path('channel-categories');

    $category_parent = $category_check_grab->ID;

     if ($post->post_parent == $category_parent) {

            $single_template = locate_template( 'ds-single-category.tpl.php' );

            // Set the template...
            if( empty($single_template) !== FALSE ){

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
                $single_template = dirname( __FILE__ ) . '/templates/ds-single-category.tpl.php';

            }

     }

     // Return either the template we made, or the template in the theme folders.

     return $single_template;
}


function ds_get_channel_template($single_template) {

    global $post;

    $channel_check_grab = get_page_by_path('channels');

    $channel_parent = $channel_check_grab->ID;

    $channel_grandparent = wp_get_post_parent_id( $post->post_parent );

     if ($post->post_parent == $channel_parent || $channel_grandparent == $channel_parent) {

            $template_option = get_option('ds_channel_template');

            $single_template = locate_template( $template_option . '.tpl.php' );

            // Set the template...
            if(  empty($single_template) !== FALSE  ){

            // If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...

            if(!$template_option || $template_option == 'default'){

                $template_option = "ds-single-channel";

            }

            $single_template = dirname( __FILE__ ) . '/templates/single_channel_templates/' . $template_option . '.tpl.php';

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



function ds_theater_mode_playlist($videoId) {
    $strOut = "";
    $strOut .= "<div class='row'>";
    $strOut .= "    <!-- THEATER MODE PLAYLIST -->";
    $strOut .= "    <div class='col-md-12 col-sm-12 col-xs-12 ds-vid-playlist ds-playlist-theater-mode'>";
    $strOut .= "        <div class='ds-playlist-theater-outer-container'>";
    $strOut .= "            <div><label>Related Videos</label></div>";
    $strOut .= "                <div class='ds-playlist-theater-inner-container'>";
    $strOut .= "                    <div class='ds-playlist-theater-mode-wrapper'>";
    $strOut .= "                         <div class='related-videos-carousel'>";
    $strOut .=                              ds_owl_recommended_videos_html(array('video_id' => $videoId, 'rec_size' => 8));
    $strOut .= "                          </div>";
    $strOut .= "                     </div>";
    $strOut .= "                </div>";
    $strOut .= "          </div>";
    $strOut .= "    </div>";
    $strOut .= "</div>";
    return $strOut;
}




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


        wp_enqueue_style('video-playlist',plugins_url('dotstudiopro-wordpress/css/video-playlist.css'));

        $video_custom_css = locate_template( 'video.channel.customization.css' );

        if(!empty($video_custom_css)){
            wp_enqueue_style('video-custom',get_template_directory_uri() . '/video.channel.customization.css');
        } else {
            wp_enqueue_style('video-custom', plugin_dir_url( __FILE__ ) . 'css/video.channel.customization.css');
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


function ds_owl_carousel_check_main_plugin() {

    ?>

    <div class="update-nag">
        <p>dotstudioPRO Premium Video plugin is not installed, is inactive, or the version is too low for this add-on.  The dotstudioPRO Premium Owl Carousel plugin has been deactivated.</p>
    </div>

    <?php

}

function ds_owl_admin_animation_select($name,$className='') {
    $aryAnimations = ['bounce','flash','pulse','rubberBand','shake','swing','tada','wobble','jello','bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp','bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp','fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig','fadeOut','fadeOutDown','fadeOutDownBig','fadeOutLeft','fadeOutLeftBig','fadeOutRight','fadeOutRightBig','fadeOutUp','fadeOutUpBig','flipInX','flipInY','flipOutX','flipOutY','lightSpeedIn','lightSpeedOut','rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight','rotateOut','rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight','hinge','rollIn','rollOut','zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp','zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp','slideInDown','slideInLeft','slideInRight','slideInUp','slideOutDown','slideOutLeft','slideOutRight','slideOutUp'];
    $strOut = '<select name='.$name.' id='.$name.' class="'.$className.'" disabled=disabled>';
    $strOut .= '<option value="">-- None --</option>';
    for($i = 0; $i <= count($aryAnimations)-1; $i++) {
        $strOut .= '<option value='.$aryAnimations[$i].'>'.$aryAnimations[$i].'</option>';
    }
    $strOut .= '<select>';
    return $strOut;

}



function ds_owl_carousel(){

    wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.min.js', array('jquery') );
    //wp_enqueue_script( 'owl-carousel-custom', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.custom.min.js' );
    wp_enqueue_style( 'owl-carousel-min', plugin_dir_url( __FILE__ ) . 'css/owl.carousel.min.css' );
    wp_enqueue_style( 'ds-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');

}

function ds_owl_carousel_html($args){

    if($args['channels'] !== '') {
            // generate the code for showing items within a channel
        return ds_owl_channel_html($args);
    }
    if($args['category'] !== '') {
            // generate the code for showing items within a category
        return ds_owl_category_html($args);
    }
}


function ds_owl_recommended_videos_html($args) {
    // renders the recommended videos playlist for the channel video player

    $video_id = $args['video_id'];
    $rec_size = $args['rec_size'];

    $recommended = list_recommended($video_id, $rec_size);

    // error checking
    if($recommended[0] === false) {
        $strOut = $recommended[1];
    } else {
        $opts = ds_owl_create_opts(array(
                'autoplay_hover_pause' => '1',
                'autoplay' => '0',
                'autoplay_timeout' => '3000',
                'autoplay_speed' => '1000',
                'notitle' => '1',
                'items' => '8'
            ));

        $rndId = ds_owl_carousel_rnd_id(5);
        $strOut =  "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
        $strOut .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

        foreach($recommended as $video){
            $info = $video->_source;
            $video_id = $video->_id;

            $title = $info->title;
            $image = $info->thumb;
            $id = $video->_id;
            $company_id = $info->company_id;
            $description =  'No description currently available';
            $slug = '';




            if(trim($title.'') !== '') {
                $description = strlen($description) > 150 ? substr($description, 0, 150)."..." : $description;
                $title = strlen($title) > 50 ? substr($title, 0, 50)."..." : $title;
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



function ds_owl_category_html($args) {

    $category_slug = $args['category'];
    $category = get_page_by_path( '/channel-categories/' . $category_slug, OBJECT );
    $title = $args['title'] !== '' ? $args['title'] : $category->post_title;
    $opts = ds_owl_create_opts($args);
    $rndId = ds_owl_carousel_rnd_id(5);
    $titleclass = $args['titleclass'];

    $carousel =  "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2><a class='owl-carousel-ellipsis' href='/channel-categories/$category_slug/' title='More...'>...</a></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    $catItems = grab_category($category_slug);

    if($catItems && is_array($catItems)){

        foreach($catItems as $ch) {
            // iterate thru the channels, get the applicable thumbnails, create the HTML output

            $id =  $ch->_id;
            $thumb_id = isset( $ch->videos_thumb ) ?  $ch->videos_thumb : '';
            $slug =  $ch->slug;
            $spotlight_poster = isset( $ch->spotlight_poster ) ?  $ch->spotlight_poster : '';

            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <a href='".home_url("channels/$slug")."' class='vert-center'>";
            $carousel .= "              <img class='owl-thumb' src='$spotlight_poster/1280/720' />";
            $carousel .= "          </a>";
            $carousel .= "      </div>";
            $carousel .= "</div>";
        }
    }

    $carousel .= "</div>";
    return $carousel;
}


function ds_owl_channel_html($args) {

    if(strpos($args['channels'], ',') !== false){
        $channels = explode( ',', $args['channels'] );
    } else {
        $channels = array($args['channels']);
    }
    $rndId = ds_owl_carousel_rnd_id(5);
    $objects = ds_owl_carousel_build_objects( $channels );
    $opts = ds_owl_create_opts($args);
    $title = $args['title'] !== '' ? $args['title'] : 'Featured Channels';
    $titleclass = $args['titleclass'];

    $carousel =  "<div id='owl-carosel-width-$rndId' class='owl-carousel-width'></div>";
    if($args['notitle'] != true) {
        $carousel .= "<div class='owl-carousel-title' style='position:relative;'><h2 class='$titleclass'>$title</h2></div>";
    }
    $carousel .= "<div class='owl-carousel owl-theme' id='owl-carousel-$rndId' data-options='$opts'>";

    foreach($objects as $o){
        if(trim($o->title.'') !== '') {
            $description = strlen($o->description) > 150 ? substr($o->description, 0, 150)."..." : $o->description;
            $title = strlen($o->title) > 20 ? substr($o->title, 0, 20)."..." : $o->title;
            $imageexp = explode("/",$o->poster);
            $image = $imageexp[3];
            $carousel .= "<div class='center-container item'>";
            $carousel .= "      <div>";
            $carousel .= "          <i class='ds-owl-fa fa fa-play-circle-o fa-3' aria-hidden='true'></i>";
            $carousel .= "          <a href='".home_url("channels/$o->slug")."' class='vert-center' data-title='$o->title' data-desc='$description'>";
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


function ds_owl_create_opts($args) {
    unset($args['channels']);
    unset($args['category']);
    unset($args['title']);

    $opts = implode(', ', array_map(
       function ($v, $k) { return sprintf("%s=%s", trim($k), trim($v)); },
       $args,
       array_keys($args)
    ));

    return $opts;
}



function ds_owl_carousel_rnd_id($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}




function ds_owl_carousel_build_objects($ids = array()){
    $objs = array();
    foreach($ids as $id){
        $obj = ds_owl_grab_channel_by_id($id);
        $objs[] = $obj[0];
    }
    return $objs;
}


function ds_owl_grab_channel_by_id($id) {
    global $ds_curl;
    $channel = $ds_curl->curl_command( 'single-channel-by-id', array( 'channel_slug' => str_replace( " ", "", $id ) ) );
    return $channel;

}



function ds_owl_carousel_local_channels_list(){

    global $wpdb;

    $channel_parent = get_page_by_path("channels");

    $channels = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE post_parent = ".$channel_parent->ID." ORDER BY post_name ASC");

    $channels_list = "";

    foreach($channels as $ch){

        $channels_list .= "<input type='checkbox' name='channel' value='$ch->post_name'> $ch->post_title<br/>";

    }

    return $channels_list;

}


function ds_owl_carousel_display_shortcode( $atts ) {


    $args = shortcode_atts( array(

        'channels' => '',
        'category' => '',
        'title' => '',
        'autoplay' => true,
        'dots' => false,
        'autoplay_timeout' => 3000,
        'autoplay_speed' => 1000,
        'autoplay_hover_pause' => false,
        'items' => 3,
        'slide_by' => 1,
        'animate_out' => '',
        'animate_in' => '',
        'dots' => false,
        'rtl' => false,
        'nav' => false,
        'notitle' => false,
        'titleclass' => ''

    ), $atts, 'ds_owl_carousel' );

    if(!count($args['channels'])  || !count($args['category']))
        return;

    return ds_owl_carousel_html($args);

}
add_shortcode( 'ds_owl_carousel', 'ds_owl_carousel_display_shortcode' );












if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['flush']) && $_GET['flush'] == 1) {
    add_action("init", "ds_site_flush");
}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['templatecopy']) && $_GET['templatecopy'] == 1) {
    add_action("init", "ds_template_copy");
}

add_filter('query_vars', 'ds_video_var');
add_filter( 'page_template', 'ds_get_channel_template', 11 );
add_filter( 'page_template', 'ds_get_category_template', 11 );
add_filter( 'page_template', 'ds_all_categories_template', 11 );
add_filter( 'page_template', 'ds_home_template', 11 );


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
