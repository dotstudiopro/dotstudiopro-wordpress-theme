<?php

/**
 * A script/plugin that communicates with our WP Updater service to determine theme updates
 */
require 'theme-update-checker/theme-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-main-theme', __FILE__, 'dspdev-main-theme'
);

/**
 * Create the pages when theme is activated
 */
if (isset($_GET['activated']) && is_admin()) {

    $home_page = get_page_by_title('Home Page');
    $categories_page = get_page_by_title('Categories');
    $video = get_page_by_title('Video');
    $my_list_page = get_page_by_title('My List');

    if ($home_page == NULL || $home_page->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('Home Page', 'home-page', 'home-template.php');
    else
        update_post_meta($home_page->ID, '_wp_page_template', 'page-templates/home-template.php');

    if ($categories_page == NULL || $categories_page->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('Categories', 'categories', 'categories-template.php');
    else
        update_post_meta($categories_page->ID, '_wp_page_template', 'page-templates/categories-template.php');

    if ($video == NULL || $video->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('Video', 'video', 'video-player.php');
    else
        update_post_meta($video->ID, '_wp_page_template', 'page-templates/video-player.php');

    if ($my_list_page == NULL || $my_list_page->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('My List', 'my-list', 'my-lists-template.php');
    else
        update_post_meta($my_list_page->ID, '_wp_page_template', 'page-templates/my-lists-template.php');
}

/**
 * function to add new pages with their template
 * @param type $title
 * @param type $slug
 * @param type $new_page_template
 * @param type $desc
 * @param type $status
 * @param type $author
 * @param type $type
 */
function add_dotstudiopro_bootstrap_custom_pages($title, $slug, $new_page_template, $desc = '', $status = 'publish', $author = 1, $type = 'page') {
    $my_post = array(
        'post_title' => wp_strip_all_tags($title),
        'post_content' => $desc,
        'post_status' => $status,
        'post_name' => $slug,
        'post_author' => $author,
        'post_type' => $type,
    );
    $page_id = wp_insert_post($my_post);
    if (!empty($new_page_template)) {
        update_post_meta($page_id, '_wp_page_template', 'page-templates/' . $new_page_template);
    }
}

require_once (dirname(__FILE__) . '/includes/class-walker-dsp-submenu.php');
require_once (dirname(__FILE__) . '/includes/class-theme-functions.php');

// Load Redux-Freamwork
if (!class_exists('ReduxFramework') && file_exists(dirname(__FILE__) . '/framework/ReduxCore/framework.php')) {
    require_once( dirname(__FILE__) . '/framework/ReduxCore/framework.php' );
}

// Load the theme's options
if (!isset($redux_owd) && file_exists(dirname(__FILE__) . '/framework/dsp_options/dsp-config.php')) {
    require_once( dirname(__FILE__) . '/framework/dsp_options/dsp-config.php' );
}

// initialize the the theme's option
Redux::init('dsp_theme_options');

/*
 * checking if user is logged
 */
add_action('init', 'check_user_status', 1);

function check_user_status() {
    ob_start();
    global $client_token;
    $client_token = 0;
    $client_id = get_current_user_id();
    if ($client_id && !current_user_can('administrator')) {
        $client_token = get_user_meta($client_id, 'dotstudiopro_client_token', true);
        $client_token_expiration = get_user_meta($client_id, 'dotstudiopro_client_token_expiration', true);
        if ($client_token_expiration <= time() && class_exists('Dsp_External_Api_Request')) {
            $client = new Dsp_External_Api_Request();
            $client_token = $client->refresh_client_token($client_token);
            update_user_meta($client_id, 'dotstudiopro_client_token', $client_token['client_token']);
            update_user_meta($client_id, 'dotstudiopro_client_token_expiration', time() + 5400);
        }
    }
}

// function to display menu option in admin panel.
if (function_exists('register_nav_menus')) {
    register_nav_menus(
            array(
                'main_menu' => esc_html__('Main Menu', 'wp-bootstrap'),
                'primary' => esc_html__('Primary', 'wp-bootstrap'),
            )
    );
}
// add title-tag and post-thumbnails
add_theme_support('title-tag');
add_theme_support('post-thumbnails');

// function to enqueue default bootstrap, slick, font-awsom stlyes also handle the fallback if cdn falls
function bootstrapstarter_enqueue_styles() {

    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

    wp_enqueue_style('redux-global', get_template_directory_uri() . '/framework/dsp_options/redux-global.css');

    wp_enqueue_style('effects', get_template_directory_uri() . '/assets/css/effects.css');

    wp_enqueue_style('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.css', array(), '1.0.7');

    wp_enqueue_script('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js', array('jquery'), '1.0.7', true);
}

/**
 * Deferred load for Bootstrap styles
 *
 * @return null
 */
function bootstrapstarter_enqueue_footer_styles_scripts() {
    $bootstrapcdn_css_url = 'https://wordpress-assets.dotstudiopro.com/css/bootstrap.4.1.3.min.css';
    $bootstrapcdn_js_url = 'https://wordpress-assets.dotstudiopro.com/js/bootstrap.4.1.3.min.js';
    $slickcdn_url = 'https://wordpress-assets.dotstudiopro.com/css/slick.css';
    $slickthemecdn_url = 'https://wordpress-assets.dotstudiopro.com/css/slick-theme.css';
    $popper_url = 'https://wordpress-assets.dotstudiopro.com/js/popper.min.js';
    // Get our URLs into an array to properly determine type and such
    $urls = array(
        array("url" => $bootstrapcdn_js_url, "type" => "script"),
        array("url" => $bootstrapcdn_css_url, "type" => "style"),
        array("url" => $slickcdn_url, "type" => "style"),
        array("url" => $slickthemecdn_url, "type" => "style"),
        array("url" => $popper_url, "type" => "script")
    );

    dsp_bootstrap_footer_script_defer($urls);
}

// Set our deferred scripts global to use for later when we
// call our deferred scripts
$deferred_scripts = array();

/**
 * Store tags for deferring the load of scripts and styles as needed
 * @param array $arr The array we are looping through
 *
 * @return boolean true
 */
function dsp_bootstrap_footer_script_defer($arr) {
    global $deferred_scripts;
    foreach($arr as $url) {
        $deferred_scripts[] = $url;
    }
    return true;
}

/**
 * Create script tags for deferring the load of scripts and styles as needed
 * @param array $arr The array we are looping through
 *
 * @return boolean Whether or not adding the scripts to the array succeeded
 */
function dsp_bootstrap_process_defer() {
    global $deferred_scripts;

    $scripts = "<script>
                    var dsp_bootstrap_opts = document.querySelector('style.options-output');";
    if (!is_array($deferred_scripts)) return $scripts;
    foreach($deferred_scripts as $url) {
        // Make sure we have what we need
        if (empty($url['url']) || empty($url['type'])) continue;
        // Set up random variables to avoid collisions
        $randVar = "dspvar_" . rand(1,9999) . time();
        $rel = "";
        $srcprop = "src";
        $elem = "script";
        $type = "application/javascript";
        // Change our vars to style values if this is css
        if ($url['type'] == "style") {
            $rel = "$randVar.rel = 'stylesheet';";
            $srcprop = "href";
            $elem = "link";
            $type = "text/css";
        }
        $scripts .= "
                var $randVar = document.createElement('$elem');
                $rel
                $randVar.$srcprop = '" . $url['url'] . "';
                $randVar.type = '$type';
                dsp_bootstrap_opts.parentNode.insertBefore($randVar, dsp_bootstrap_opts);";
    }
    $scripts .= "</script>";
    echo $scripts;
}

add_action("get_footer", "dsp_bootstrap_process_defer", 99);

// function to enqueue default bootstrap, slick, popper scripts and also handle the fallback if cdn falls
function bootstrapstarter_enqueue_scripts() {
    wp_enqueue_script('jquery');

    wp_enqueue_script('tooltipster', get_template_directory_uri() . '/assets/js/tooltipster.bundle.min.js');

    $slickcdn_url = get_template_directory_uri() . '/assets/js/slick.min.js';
    wp_enqueue_script('slick', $slickcdn_url);

    wp_enqueue_script('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js', array('jquery'), '1.0.7', true);
}

add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_styles');
add_action('get_footer', 'bootstrapstarter_enqueue_footer_styles_scripts', 50);
add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_scripts');

/**
 * Set up certain scripts to be asyncronous on load
 * @since 1.0.0
 * @param string $tag
 * @param string $handle
 * @return string
 */
function add_async_attribute($tag, $handle) {
    $scripts = array('slick-init', 'tooltipster');
    if ( !in_array($handle, $scripts) )
        return $tag;
    return str_replace(' src', ' async="async" src', $tag);
}

//add_filter('script_loader_tag', 'add_async_attribute', 10, 2);

/**
 * Set up certain styles to preload
 * @since 1.0.0
 * @param string $tag
 * @param string $handle
 * @return string
 */
// function add_preload_attribute($tag, $handle) {
//     $styles = array('main', 'font-awesome-pro', 'ds-global', 'tooltipster.bundle.min');
//     if ( !in_array($handle, $styles) )
//         return $tag;
//     $preload = str_replace( " rel='stylesheet'", " rel=\"preload\" as=\"style\"", $tag );
//     return $preload . $tag;
// }
// add_filter('style_loader_tag', 'add_preload_attribute', 10, 2);

/**
 * Get remote url response
 * @since 1.0.0
 * @param type $url
 * @return int
 */
function remote_get_url($url) {

    $responce = wp_remote_get($url, array(
        'timeout' => 50,
    ));

    if ($responce)
        return (int) wp_remote_retrieve_response_code($responce);
    else
        return 500;
}

// function to register and enqueue all other scripts
function register_theme_scripts() {
    $scripts = array('jquery.mCustomScrollbar.concat', 'slick-init', 'image-lazy-load', 'classie', 'uisearch', 'custom', 'search-autocomplete.min', 'modernizr.custom', 'effects');
    foreach ($scripts as $script) :
        wp_register_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.min.js');
        wp_enqueue_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.min.js', false, false, true);
    endforeach;
    wp_localize_script('custom.min', 'jsVariable', array('ajaxUrl' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'register_theme_scripts');

// function to register and enqueue all other styles
function register_theme_styles() {
    $styles = array('ds-global', 'ds-header');
    foreach ($styles as $style) :
        wp_register_style($style, get_template_directory_uri() . "/assets/css/" . $style . ".min.css");
        wp_enqueue_style($style, array(), filemtime(get_template_directory() . '/style.css'), 'screen');
    endforeach;
}

add_action('wp_enqueue_scripts', 'register_theme_styles');

// function to register and enqueue styles we don't need immediately
function defer_theme_styles() {
    $styles = array('main', 'font-awesome-pro', 'tooltipster.bundle');
    $urls = array();
    foreach ($styles as $style) :
        // wp_register_style($style, get_template_directory_uri() . "/assets/css/" . $style . ".css");
        // wp_enqueue_style($style, array(), filemtime(get_template_directory() . '/style.css'), 'screen');
        $urls[] = array("url" => get_template_directory_uri() . "/assets/css/" . $style . ".min.css", "type" => "style");
    endforeach;
    dsp_bootstrap_footer_script_defer($urls);
}

add_action('get_footer', 'defer_theme_styles', 50);


// Action to add class in html tag

add_filter('language_attributes', 'class_to_html_tag', 10, 2);

function class_to_html_tag($output, $doctype) {
    if ('html' !== $doctype) {
        return $output;
    }
    global $dsp_theme_options;
    $class = ($dsp_theme_options['opt-layout'] == 1) ? 'full-width' : 'boxed';
    $output .= ' class="' . $class . '"';
    return $output;
}

// function to add bosy class
function theme_body_class($class = '') {
    global $dsp_theme_options;
    $class = ($dsp_theme_options['opt-layout'] == 1) ? 'full-width ' : 'boxed ';
    $class .= ($dsp_theme_options['opt-sticky'] == 1) ? 'stickey-nav ' : '';
    $class .= ($dsp_theme_options['opt-logo-align'] == 'center') ? 'center-header' : '';
    echo 'class="' . join(' ', get_body_class($class)) . '"';
}

/**
 * tgm-plugin activation files
 * Currently we are not use this files. This files are useful to activate the plugin on theme activation.
 *
 */
//require_once('tgm-plugin-activation/class-tgm-plugin-activation.php');
//require_once('tgm-plugin-activation/required_plugins.php');

/**
 * Add a Category menu on home page
 */
add_filter('wp_nav_menu_objects', 'add_category_menu_links', 10, 2);

function add_category_menu_links($items, $args) {

    $new_links = array();
    global $dsp_theme_options;
    if ($dsp_theme_options['opt-category-menu'] == 1) {
        $label = $dsp_theme_options['opt-menu-title'];    // add your custom menu item content here

        if ($args->theme_location == 'main_menu') {
            // Create a nav_menu_item object
            $item = array(
                'title' => $label,
                'menu_item_parent' => 0,
                'ID' => 'category_menu',
                'db_id' => '',
                'url' => $dsp_theme_options['opt-menu-link'],
                'classes' => array('menu-item', 'menu-item-type-custom', 'menu-item-object-custom', 'dropdown')
            );

            $new_links[] = (object) $item; // Add the new menu item to our array
            // insert item
            $location = $dsp_theme_options['opt-menu-position'] - 1;
            array_splice($items, $location, 0, $new_links);
        }
    }
    return $items;
}

/**
 * Display video template when found video in url
 */
add_filter('request', 'display_query_vars', 1);

function display_query_vars($query_vars) {
    $data = $query_vars;
    if (isset($data['channel'])) {
        $video_array = explode('/', $data['channel']);
        if (isset($video_array[1])) {
            if ($video_array[1] == 'video') {
                locate_template('page-templates/video-player.php', true);
            }
        }
    }
    return $query_vars;
}

/**
 * Function to register a widget space
 */
if (function_exists('register_sidebar')) {

    register_sidebar(array(
        'name' => 'Sidebar',
        'id' => 'sidebar',
        'description' => 'This is a default sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ));

    // Our login area widget
    register_sidebar(array(
        'name' => 'Login Area',
        'id' => 'dsp_web_login_area',
        'before_widget' => '<div>',
        'after_widget' => '</div>'
    ));
}

/**
 * Custom menu attributes
 */
add_filter('nav_menu_link_attributes', 'custom_menu_atts', 10, 3);

function custom_menu_atts($atts, $item, $args) {
    // inspect $item
    if ($item->ID == 'category_menu') {
        $atts['data-toggle'] = 'dropdown';
        $atts['class'] = 'dropdown-toggle';
    }
    return $atts;
}

/*
 * getting post id by post slug
 */

function get_id_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

/*
 * adding query vars for routs
 */

function add_query_vars($vars) {
    $vars[] = "p_channel_slug";
    $vars[] = "channel_slug";
    $vars[] = "video_slug";
    return $vars;
}

add_filter('query_vars', 'add_query_vars');


/*
 * rewrite ruls for routes
 */

function custom_rewrite_basic() {
    add_rewrite_rule('channel/([^/]*)/video/([^/]*)/?$', 'index.php?page_id=' . get_id_by_slug('video') . '&channel_slug=$matches[1]&video_slug=$matches[2]', 'top');
    add_rewrite_rule('channel/([^/]*)/([^/]*)/video/([^/]*)/?$', 'index.php?page_id=' . get_id_by_slug('video') . '&p_channel_slug=$matches[1]&channel_slug=$matches[2]&video_slug=$matches[3]', 'top');
    add_rewrite_rule('video/([^/]*)/?$', 'index.php?page_id=' . get_id_by_slug('video') . '&video_slug=$matches[1]', 'top');
    flush_rewrite_rules();
}

add_action('init', 'custom_rewrite_basic', 10, 0);

/**
 * Function to display auto-complete result
 * @global type $dsp_theme_options
 */
function autocomplete() {
    $items = array();
    if (wp_verify_nonce($_POST['nonce'], 'dsp_autocomplete_search')) {
        global $dsp_theme_options;
        $type = $dsp_theme_options['opt-search-option'];
        $dotstudio_api = new Dsp_External_Api_Request();
        $q = $_POST['search'];
		$width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
        $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
        $suggesion = $dotstudio_api->search_suggestion($q);
        $search = $dotstudio_api->search($type, $dsp_theme_options['opt-search-page-size'], 0, $q);

        if (!empty($suggesion['data']['directors']['results'][0]['options']) && !is_wp_error($suggesion)) {
            foreach ($suggesion['data']['directors']['results'][0]['options'] as $key => $director) {
                $items['director'][$key]['name'] = $director['text'];
                $items['director'][$key]['flag'] = 'director';
            }
        }

        if (!empty($suggesion['data']['title']['results'][0]['options']) && !is_wp_error($suggesion)) {
            foreach ($suggesion['data']['title']['results'][0]['options'] as $key => $title) {
                $items['title'][$key]['name'] = $title['text'];
                $items['title'][$key]['flag'] = 'title';
            }
        }

        if (!empty($suggesion['data']['actors']['results'][0]['options']) && !is_wp_error($suggesion)) {
            foreach ($suggesion['data']['actors']['results'][0]['options'] as $key => $actors) {
                $items['actors'][$key]['name'] = $actors['text'];
                $items['actors'][$key]['flag'] = 'actors';
            }
        }

        if (!empty($suggesion['data']['tags']['results'][0]['options']) && !is_wp_error($suggesion)) {
            foreach ($suggesion['data']['tags']['results'][0]['options'] as $key => $tags) {
                $items['tags'][$key]['name'] = $tags['text'];
                $items['tags'][$key]['flag'] = 'tags';
            }
        }

        if (!empty($search) && !is_wp_error($search)) {
            foreach ($search['data']['hits'] as $key => $data):
                if ($type == 'channel'):
                    $url = get_site_url() . '/channel/' . $data['slug'];
                    $image_type = ($dsp_theme_options['opt-search-channel-poster-type'] == 'poster') ? $data['poster'] : $data['spotlight_poster'];
                    $image = (!empty($image_type)) ? $image_type : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                    $title = 'Channels';
                else:
                    $url = get_site_url() . '/video/' . $data['_id'];
                    $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                    $title = 'Videos';
                endif;
                $items['channel'][$key]['name'] = $data['_source']['title'];
                $items['channel'][$key]['image'] = $image;
                $items['channel'][$key]['url'] = $url;
                $items['channel'][$key]['title'] = $title;
				$items['channel'][$key]['height'] = $height;
				$items['channel'][$key]['width'] = $width;
                $items['channel'][$key]['flag'] = 'channel';
            endforeach;
        }
    }

    wp_send_json_success($items);
}

function search_suggesion() {
    $items = array();
    global $dsp_theme_options;
    $type = $dsp_theme_options['opt-search-option'];
    $dotstudio_api = new Dsp_External_Api_Request();
    $q = $_POST['search'];
	$width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
	$height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
    $search = $dotstudio_api->search($type, $dsp_theme_options['opt-search-page-size'], 0, $q);

    if (!empty($search) && !is_wp_error($search)) {
        foreach ($search['data']['hits'] as $key => $data):
            if ($type == 'channel'):
                $url = get_site_url() . '/channel/' . $data['slug'];
                $image_type = ($dsp_theme_options['opt-search-channel-poster-type'] == 'poster') ? $data['poster'] : $data['spotlight_poster'];
                $image = (!empty($image_type)) ? $image_type : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                $title = 'Channels';
            else:
                $url = get_site_url() . '/video/' . $data['_id'];
                $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                $title = 'Videos';
            endif;
            $items[$key]['name'] = $data['_source']['title'];
            $items[$key]['image'] = $image;
            $items[$key]['url'] = $url;
            $items[$key]['title'] = $title;
			$items[$key]['width'] = $width;
			$items[$key]['height'] = $height;
            $items[$key]['flag'] = 'channel';
        endforeach;
    }
    wp_send_json_success($items);
}

/**
 *
 * @global type $client_token
 * @return type
 */
function addToMyList() {
    global $client_token;
    $responce = array();
    if (wp_verify_nonce($_POST['nonce'], 'addToMyList')) {
        $dotstudio_api = new Dsp_External_Api_Request();
        $channel_id = $_POST['channel_id'];
        $responce = $dotstudio_api->add_to_user_list($client_token, $channel_id);
    }
    wp_send_json_success($responce);
}

/**
 *
 * @global type $client_token
 * @return type
 */
function removeFromMyList() {
    global $client_token;
    $responce = array();
    if (wp_verify_nonce($_POST['nonce'], 'removeFromMyList')) {
        $dotstudio_api = new Dsp_External_Api_Request();
        $channel_id = $_POST['channel_id'];
        $responce = $dotstudio_api->remove_from_user_list($client_token, $channel_id);
    }
    wp_send_json_success($responce);
}

add_action('wp_ajax_autocomplete', 'autocomplete');
add_action('wp_ajax_nopriv_autocomplete', 'autocomplete');
add_action('wp_ajax_addToMyList', 'addToMyList');
add_action('wp_ajax_nopriv_addToMyList', 'addToMyList');
add_action('wp_ajax_removeFromMyList', 'removeFromMyList');
add_action('wp_ajax_nopriv_removeFromMyList', 'removeFromMyList');
add_action('wp_ajax_search_suggesion', 'search_suggesion');
add_action('wp_ajax_nopriv_search_suggesion', 'search_suggesion');

/**
 * Remove the admin bar for subscribers
 */
function dsp_remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

add_action('after_setup_theme', 'dsp_remove_admin_bar');

/**
 * Add a login link to the main navigation
 */
function dsp_add_login_link($items, $args) {
    if ($args->theme_location == 'main_menu' && class_exists('WP_Auth0')) {
        if (is_user_logged_in()) {
            $items .= '<li id="menu-item-my_account" class="menu-item menu-item-type-custom menu-item-object-custom dropdown menu-item-category_menu">'
                    . '<a href="#" data-toggle="dropdown" class="dropdown-toggle">My Account</a>'
                    . '<ul class="dropdown-menu position-absolute" role="menu">';
            if (class_exists('Dotstudiopro_Subscription')):
                $items .= '<li><a href="/packages">Subscriptions</a></li>'
                        . '<li><a href="/payment-profile">My Payment Profile</a></li>';
            endif;
            $items .= '<li><a href="/my-list">My List</a></li>'
                    . '<li><a href="' . wp_logout_url(get_home_url()) . '">Log Out</a></li>'
                    . '</ul>'
                    . '</li>';
        } else {
            $items .= '<li><a href="#" data-login_url="' . wp_login_url() . '" class="dsp-auth0-login-button">Log In</a></li>';
        }
    }
    return $items;
}

add_filter('wp_nav_menu_items', 'dsp_add_login_link', 10, 2);

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function dsp_excerpt_more($more) {
    if (!is_single()) {
        $more = sprintf('<a class="read-more" href="%1$s">%2$s</a>', get_permalink(get_the_ID()), __('Read More', 'textdomain')
        );
    }
    return $more;
}

add_filter('excerpt_more', 'dsp_excerpt_more');

/**
 * Get a list of categories that have posts in them by alphabetical order
 *
 * @return array An array of categories, if any of posts in them
 */
function dsp_get_categories_list() {
    return get_categories(array(
        'orderby' => 'name',
        'order' => 'ASC'
    ));
}

/**
 * Get a link to the category with the appropriate alt tag based on description
 *
 * @param object $category The category we need a link for
 * @return string The link to the category, as a string
 */
function dsp_get_category_link($category) {
    return sprintf(
            '<a class="blog-category-link" href="%1$s" alt="%2$s">%3$s</a>', esc_url(get_category_link($category->term_id)), esc_attr(sprintf(__('View all posts in %s', 'textdomain'), $category->name)), esc_html($category->name)
    );
}

/**
 * Echo out a list of categories with posts in them, with links to the category pages and article counts
 *
 * @return null
 */
function dsp_get_category_list_lis() {
    $category_list = dsp_get_categories_list();
    foreach ($category_list as $category) {
        echo "<li>";
        $link = dsp_get_category_link($category);
        $count = $category->count;
        $articles = "article" . ($count > 1 ? "s" : "");
        echo "<div class='blog-category-link'>" . $link . "</div>";
        echo "<div class='blog-category-count'>$count $articles</div>";
        echo "</li>";
    }
}

/**
 * Add a dotstudioPRO customer ID to the user that we get back from Auth0
 *
 * @param integer $user_id - WordPress user ID
 * @param stdClass $userinfo - user information object from Auth0
 * @param boolean $is_new - true if the user was created in WordPress, false if not
 * @param string $id_token - ID token for the user from Auth0 (not used in code flow)
 * @param string $access_token - bearer access token from Auth0 (not used in implicit flow)
 */
function dsp_add_customer_id_to_user($user_id, $userinfo, $is_new, $id_token, $access_token) {

    if (empty($userinfo->user_metadata->customer) || empty($userinfo->user_metadata->spotlight))
        return;
    $customer_id = $userinfo->user_metadata->customer;
    $spotlight = $userinfo->user_metadata->spotlight;

    update_user_meta($user_id, "dotstudiopro_customer_id", $customer_id);
    update_user_meta($user_id, "dotstudiopro_client_token", $spotlight);
    update_user_meta($user_id, "dotstudiopro_client_token_expiration", time() + 5400);

    $subscriptionClass = new Dotstudiopro_Subscription_Request();
    $subscription = $subscriptionClass->getUserSubscription($spotlight);
    if (!empty($sub[0]->subscription->platform)) {
        update_user_meta($user_id, "dotstudiopro_subscription_platform", $sub[0]->subscription->platform);
    }
}

add_action('auth0_user_login', 'dsp_add_customer_id_to_user', 10, 5);

/**
 * Ajax call to store the point data
 */
add_action('wp_ajax_save_point_data', 'save_point_data');
add_action('wp_ajax_nopriv_save_point_data', 'save_point_data');

function save_point_data() {
    global $client_token;
    if ($client_token && wp_verify_nonce($_POST['nonce'], 'save_point_data')) {
        $video_id = $_POST['video_id'];
        $point = $_POST['play_time'];
        $dspExternalApiClass = new Dsp_External_Api_Request();
        $response = $dspExternalApiClass->create_point_data($client_token, $video_id, $point);
        if (is_wp_error($response)) {
            $send_response = array('message' => 'Server Error : ' . $response->get_error_message());
            wp_send_json_error($send_response, 403);
        } elseif (isset($response['success']) && $response['success'] == 1) {
            $send_response = array('message' => 'point data save succesfully.');
            wp_send_json_success($send_response, 200);
        } else {
            $send_response = array('message' => 'Internal Server Error');
            wp_send_json_error($send_response, 500);
        }
    } else {
        $send_response = array('message' => 'Internal Server Error');
        wp_send_json_error($send_response, 500);
    }
}

// Update CSS within in Admin
function admin_style() {
    wp_enqueue_style('admin-styles', get_template_directory_uri() . '/assets/css/admin.css');
}

add_action('admin_enqueue_scripts', 'admin_style');
?>
