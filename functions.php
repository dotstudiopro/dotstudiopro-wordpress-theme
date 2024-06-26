<?php

/**
 * A script/plugin that communicates with our WP Updater service to determine theme updates
 */
require 'theme-update-checker/theme-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
                'https://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-main-theme', __FILE__, 'dspdev-main-theme'
);
// The base url for theme assets; note that Bootstrap and some other things
// pull from a different url
$theme_data = wp_get_theme();
$parent_theme = $theme_data->parent();
if ($parent_theme)
    $theme_version = $parent_theme->Version;
else
    $theme_version = $theme_data->Version;
$url = "https://wordpress-assets.dotstudiopro.com/main-theme/v".$theme_version;
$buster = date("YmdHi", filemtime( __DIR__ . '/assets/css/ds-global.min.css'));
if (defined('DOTSTUDIOPRO_DEV')) {
    $url = get_template_directory_uri() . "/assets";
    $buster = time();
}
define('DSP_THEME_ASSETS_BASE_URL', $url);
define('DSP_THEME_ASSETS_CACHEBUSTER', $buster);

/**
 * Create the pages when theme is activated
 */
if (isset($_GET['activated']) && is_admin()) {

    $home_page = get_page_by_title('Home Page');
    $categories_page = get_page_by_title('Categories');
    $video = get_page_by_title('Video');
    $my_list_page = get_page_by_title('My List');
    $reset_device_login_page = get_page_by_title('Reset Device Login');
    $account_deletion_page = get_page_by_title('Account Deletion');

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

    if ($reset_device_login_page == NULL || $reset_device_login_page->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('Reset Device Login', 'reset-device-login', 'reset-device-login-template.php');
    else
        update_post_meta($reset_device_login_page->ID, '_wp_page_template', 'page-templates/reset-device-login-template.php');

    if ($account_deletion_page == NULL || $account_deletion_page->post_status == 'trash')
        add_dotstudiopro_bootstrap_custom_pages('Account Deletion', 'account-deletion', 'account-deletion-template.php');
    else
        update_post_meta($account_deletion_page->ID, '_wp_page_template', 'page-templates/account-deletion-template.php');

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

// Add the action so it will delete all transients on save button click
add_action('redux/options/dsp_theme_options/saved', 'delete_my_transients_on_save');

function delete_my_transients_on_save() {
    // Get all transients
    global $wpdb;
    $transients = $wpdb->get_results("
                        SELECT option_name
                        FROM $wpdb->options
                        WHERE option_name LIKE '_transient_dotstudiopro_%'
                   ");

    // Delete each transient
    foreach ($transients as $transient) {
        $transient_name = str_replace('_transient_', '', $transient->option_name);
        // Delete the transient
        $deleted = delete_transient($transient_name);
    }
}

/*
 * checking if user is logged
 */
add_action('init', 'check_user_status', 1);

function check_user_status() {
    ob_start();
    global $client_token;
    $client_token = 0;
    $client_id = get_current_user_id();
    if ($client_id) {
        // Check max login limit reached
        if(!session_id())
            session_start();

        $current_page = trim($_SERVER['REQUEST_URI'], '/');
        $is_account_information_post_call = false;
        if(isset($_POST['action']) && $_POST['action'] == 'destroy_every_user_login_session'){
            $is_account_information_post_call = true;
        }
        if(!is_admin() && isset($_SESSION['max_login_limit_reached']) && $current_page != 'reset-device-login' && $is_account_information_post_call == false){
            wp_redirect(home_url('reset-device-login'));
            exit();
        }
        
        $client_token = isset($_SESSION['dotstudiopro_client_token']) ? $_SESSION['dotstudiopro_client_token'] : 0;
        $client_token_expiration = isset($_SESSION['dotstudiopro_client_token_expiration']) ? $_SESSION['dotstudiopro_client_token_expiration'] : '';
        if (!empty($client_token_expiration) && $client_token_expiration <= time() && class_exists('Dsp_External_Api_Request')) {
            $client = new Dsp_External_Api_Request();
            $client_token = $client->refresh_client_token($client_token);
            if (is_wp_error($client_token)) {
                // If we have an error with the client token, we can't leave that value as a wp_error object;
                // if we do, every API call involving a client token throws an error because we can't send a
                // wp_error object as a header value, so we set an empty value instead
                $client_token = "";
                return $client_token;
            }
            $_SESSION['dotstudiopro_client_token'] = $client_token['client_token'];
            $_SESSION['dotstudiopro_client_token_expiration'] = time() + 5400;
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
// add post-thumbnails support
add_theme_support('post-thumbnails');

add_filter( 'template_include', 'bootstrapstarter_current_template_filter', 1000 );

/**
 * Sets the base filename for the current page template in the global variables
 *
 * @return string The base filename of the current page template
 */
function bootstrapstarter_current_template_filter( $t ){
    $GLOBALS['current_theme_template'] = basename($t);
    return $t;
}

/**
 * Gets the current template name from the global variables
 *
 * @return string The base filename of the current page template
 */
function bootstrapstarter_get_current_template( $echo = false ) {
    if( !isset( $GLOBALS['current_theme_template'] ) )
        return false;
    if( $echo )
        echo $GLOBALS['current_theme_template'];
    else
        return $GLOBALS['current_theme_template'];
}

/**
 * Function to allow us to enqueue styles as we need them on a per-template basis
 *
 * @return string The base filename of the current page template
 */
function bootstrapstarter_enqueue_current_styles() {
    $template = bootstrapstarter_get_current_template();
    $styles = array();
    switch($template) {
        case "single-channel.php":
            $styles[] = "ds-cat-channel";
            $styles[] = "ds-category";
            break;
        case "video-player.php":
            $styles[] = "ds-cat-channel";
            $styles[] = "ds-video";
            break;
    }
    if (count($styles) > 0) {
        foreach($styles as $style) {
            wp_enqueue_style($style, DSP_THEME_ASSETS_BASE_URL . "/css/" . $style . ".min.css", [], DSP_THEME_ASSETS_CACHEBUSTER, 'screen');
        }
    }
}
add_action("wp_enqueue_scripts", "bootstrapstarter_enqueue_current_styles");

// function to enqueue default bootstrap, slick, font-awsom stlyes also handle the fallback if cdn falls
function bootstrapstarter_enqueue_styles() {

    wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css', array(), null);

    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/fontawesome.min.css');

    wp_enqueue_style('redux-global', get_template_directory_uri() . '/framework/dsp_options/redux-global.css');

    wp_enqueue_style('effects', DSP_THEME_ASSETS_BASE_URL . '/css/effects.min.css');

    wp_enqueue_style('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.css', array(), null);

    wp_enqueue_script('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js', array('jquery'), null, true);
}

/**
 * Deferred load for Bootstrap styles
 *
 * @return null
 */
function bootstrapstarter_enqueue_footer_styles_scripts() {
    $bootstrapcdn_js_url = 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js';
    $slickcdn_url = DSP_THEME_ASSETS_BASE_URL . '/css/slick.min.css';
    $slickthemecdn_url = DSP_THEME_ASSETS_BASE_URL . '/css/slick-theme.min.css';
    $popper_url = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js';
    // Get our URLs into an array to properly determine type and such
    $urls = array(
        array("url" => $bootstrapcdn_js_url, "type" => "script"),
        // array("url" => $bootstrapcdn_css_url, "type" => "style"),
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
                    var dsp_bootstrap_opts = document.querySelector('style.redux-options-output');";
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

    wp_enqueue_script('tooltipster', DSP_THEME_ASSETS_BASE_URL . '/js/tooltipster.bundle.min.js');

    $slickcdn_url = DSP_THEME_ASSETS_BASE_URL . '/js/slick.min.js';
    wp_enqueue_script('slick', $slickcdn_url);
    wp_enqueue_script('DotPlayer', "https://www.dplayer.pro/dotplayer.js");
    wp_enqueue_script('youbora-plugin', 'https://smartplugin.youbora.com/v6/js/lib/6.5.26/youboralib.min.js', array(), false, true);
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
    $scripts = array('jquery.mCustomScrollbar.concat', 'slick-init', 'image-lazy-load', 'classie', 'uisearch', 'custom', 'search-autocomplete', 'modernizr.custom', 'effects');
    foreach ($scripts as $script) :
        wp_enqueue_script($script, DSP_THEME_ASSETS_BASE_URL . '/js/' . $script . '.min.js', array(), DSP_THEME_ASSETS_CACHEBUSTER, true);
    endforeach;
    $configs = get_option('dsp_analytics_parameters');
    if (!$configs) $configs = json_decode("{\"company_id\": \"\", \"subdomain\": \"\"}"); // Shortcut for an empty object with our params
    wp_localize_script('custom', 'jsVariable', array('ajaxUrl' => admin_url('admin-ajax.php'), 'company_id' => $configs->company_id, 'subdomain' => $configs->subdomain  ));
}

add_action('wp_enqueue_scripts', 'register_theme_scripts', 99);

// function to register and enqueue all other styles
function register_theme_styles() {
    $styles = array('ds-global', 'ds-header');
    foreach ($styles as $style) :
        wp_enqueue_style($style, DSP_THEME_ASSETS_BASE_URL . "/css/" . $style . ".min.css", false, DSP_THEME_ASSETS_CACHEBUSTER, 'all');
    endforeach;
}

add_action('wp_enqueue_scripts', 'register_theme_styles');

// function to register and enqueue styles we don't need immediately
function defer_theme_styles() {
    $styles = array('ds-footer', 'main', 'font-awesome-pro', 'tooltipster.bundle');
    $urls = array();
    foreach ($styles as $style) :
        $urls[] = array("url" => DSP_THEME_ASSETS_BASE_URL . "/css/" . $style . ".min.css?ver=" . DSP_THEME_ASSETS_CACHEBUSTER, "type" => "style");
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
    $class .= (isset($dsp_theme_options['opt-advertise']) && $dsp_theme_options['opt-advertise'] == 1) ? 'advertise ' : '';
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
    if (isset($data['channel']) && !is_array($data['channel'])) {
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
        if( $dsp_theme_options['opt-search-image-size'] == '0' ) {
            $width = filter_var($dsp_theme_options['opt-search-autocomplete-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
            $height = filter_var($dsp_theme_options['opt-search-autocomplete-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $width = filter_var($dsp_theme_options['opt-search-autocomplete-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

            $ratio_width = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
            $ratio_height = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

            $ratio = $ratio_height / $ratio_width;
        }
        //$suggesion = $dotstudio_api->search_suggestion($q);
        $search = $dotstudio_api->search($type, $dsp_theme_options['opt-search-page-size'], 1, $q);

        // if (!empty($suggesion['data']['directors']['results'][0]['options']) && !is_wp_error($suggesion)) {
        //     foreach ($suggesion['data']['directors']['results'][0]['options'] as $key => $director) {
        //         $items['director'][$key]['name'] = $director['text'];
        //         $items['director'][$key]['flag'] = 'director';
        //     }
        // }

        // if (!empty($suggesion['data']['title']['results'][0]['options']) && !is_wp_error($suggesion)) {
        //     foreach ($suggesion['data']['title']['results'][0]['options'] as $key => $title) {
        //         $items['title'][$key]['name'] = $title['text'];
        //         $items['title'][$key]['flag'] = 'title';
        //     }
        // }

        // if (!empty($suggesion['data']['actors']['results'][0]['options']) && !is_wp_error($suggesion)) {
        //     foreach ($suggesion['data']['actors']['results'][0]['options'] as $key => $actors) {
        //         $items['actors'][$key]['name'] = $actors['text'];
        //         $items['actors'][$key]['flag'] = 'actors';
        //     }
        // }

        // if (!empty($suggesion['data']['tags']['results'][0]['options']) && !is_wp_error($suggesion)) {
        //     foreach ($suggesion['data']['tags']['results'][0]['options'] as $key => $tags) {
        //         $items['tags'][$key]['name'] = $tags['text'];
        //         $items['tags'][$key]['flag'] = 'tags';
        //     }
        // }

        if (!empty($search) && !is_wp_error($search)) {
            if ($type == 'channel'):
                foreach ($search['channels'] as $key => $data):
                    $url = get_site_url() . '/channel/' . $data['slug'];
                    if($dsp_theme_options['opt-search-channel-poster-type'] == 'poster'){
                       $image_type = $data['poster'];
                    }
                    elseif($dsp_theme_options['opt-search-channel-poster-type'] == 'spotlight_poster'){
                        $image_type = $data['spotlight_poster'];
                    }
                    else{
                        $image_type = $data['wallpaper'];
                    }
                    $image = (!empty($image_type)) ? $image_type : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                    $is_product = (isset($data['is_product'])) ? $data['is_product'] : 0;
                    $title = 'Channels';

                    $items['channel'][$key]['name'] = $data['title'];
                    if( $dsp_theme_options['opt-search-image-size'] == '1' ) :
                        $image_attributes = dsp_build_responsive_images( $image, $width, $ratio );

                        $items['channel'][$key]['image'] = $image;
                        $items['channel'][$key]['image_attributes'] = $image_attributes;
                    else :
                        $items['channel'][$key]['image'] = $image.'/'.$width.'/'.$height;
                        $items['channel'][$key]['image_attributes'] = '';
                    endif;
                    $items['channel'][$key]['url'] = $url;
                    $items['channel'][$key]['is_product'] = $is_product;
                    $items['channel'][$key]['title'] = $title;
                    $items['channel'][$key]['flag'] = 'channel';
                endforeach;  
            else:
                foreach ($search['videos'] as $key => $data):
                    $url = get_site_url() . '/video/' . $data['_id'];
                    $image = (isset($data['thumb'])) ? $data['thumb'] : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                    $is_product = 0;
                    $title = 'Videos';
                    $items['channel'][$key]['name'] = $data['title'];
                    if( $dsp_theme_options['opt-search-image-size'] == '1' ) :
                        $image_attributes = dsp_build_responsive_images( $image, $width, $ratio );

                        $items['channel'][$key]['image'] = $image;
                        $items['channel'][$key]['image_attributes'] = $image_attributes;
                    else :
                        $items['channel'][$key]['image'] = $image.'/'.$width.'/'.$height;
                        $items['channel'][$key]['image_attributes'] = '';
                    endif;
                    $items['channel'][$key]['url'] = $url;
                    $items['channel'][$key]['is_product'] = $is_product;
                    $items['channel'][$key]['title'] = $title;
                    $items['channel'][$key]['flag'] = 'channel';
                endforeach;  
            endif;
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
    if( $dsp_theme_options['opt-search-image-size'] == '0' ) {
        $width = filter_var($dsp_theme_options['opt-search-autocomplete-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
        $height = filter_var($dsp_theme_options['opt-search-autocomplete-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        $width = filter_var($dsp_theme_options['opt-search-autocomplete-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

        $ratio_width = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
        $ratio_height = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

        $ratio = $ratio_height / $ratio_width;
    }
    $search = $dotstudio_api->search($type, $dsp_theme_options['opt-search-page-size'], 1, $q);

    if (!empty($search) && !is_wp_error($search)) {
        if ($type == 'channel'):
            foreach ($search['channels'] as $key => $data):
                $url = get_site_url() . '/channel/' . $data['slug'];
                if($dsp_theme_options['opt-search-channel-poster-type'] == 'poster'){
                   $image_type = $data['poster'];
                }
                elseif($dsp_theme_options['opt-search-channel-poster-type'] == 'spotlight_poster'){
                    $image_type = $data['spotlight_poster'];
                }
                else{
                    $image_type = $data['wallpaper'];
                }
                //$image_type = ($dsp_theme_options['opt-search-channel-poster-type'] == 'poster') ? $data['poster'] : $data['spotlight_poster'];
                $image = (!empty($image_type)) ? $image_type : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                $is_product = (isset($data['is_product'])) ? $data['is_product'] : 0;
                $title = 'Channels';
                $items[$key]['name'] = $data['title'];
                if( $dsp_theme_options['opt-search-image-size'] == '1' ) :
                    $image_attributes = dsp_build_responsive_images( $image, $width, $ratio );
                    $items[$key]['image'] = $image;
                    $items[$key]['image_attributes'] = $image_attributes;
                else :
                    $items[$key]['image'] = $image.'/'.$width.'/'.$height;
                    $items[$key]['image_attributes'] = '';
                endif;
                $items[$key]['url'] = $url;
                $items[$key]['title'] = $title;
                $items[$key]['is_product'] = $is_product;
                $items[$key]['flag'] = 'channel';
            endforeach;  
        else:
            foreach ($search['videos'] as $key => $data):
                $url = get_site_url() . '/video/' . $data['_id'];
                $image = (isset($data['thumb'])) ? $data['thumb'] : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                $is_product = 0;
                $title = 'Videos';
                $items[$key]['name'] = $data['title'];
                if( $dsp_theme_options['opt-search-image-size'] == '1' ) :
                    $image_attributes = dsp_build_responsive_images( $image, $width, $ratio );
                    $items[$key]['image'] = $image;
                    $items[$key]['image_attributes'] = $image_attributes;
                else :
                    $items[$key]['image'] = $image.'/'.$width.'/'.$height;
                    $items[$key]['image_attributes'] = '';
                endif;
                $items[$key]['url'] = $url;
                $items[$key]['title'] = $title;
                $items[$key]['is_product'] = $is_product;
                $items[$key]['flag'] = 'channel';
            endforeach;
        endif;
    }
    wp_send_json_success($items);
}

 /**
 * Function to delete user account
 * @since 1.0.0
 */
function account_deletion() {
    global $client_token;
    if ($client_token && wp_verify_nonce($_POST['nonce'], 'account_deletion')) {
        $dotstudio_api = new Dsp_External_Api_Request();
        $response = $dotstudio_api->remove_user_from_system($client_token);
        if (is_wp_error($response)) {
            $err = $response->get_error_message();
            $send_response = array('message' => (is_string($err) ? $err : json_encode($err)));
            wp_send_json_error($send_response, 403);
        } elseif (isset($response['success']) && $response['success'] == 1) {
            $send_response = array('message' => 'Your request for account deletion proceeds successfully.');
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

/**
 * Function to send device login code
 * @since 1.0.0
 */
function dspdl_ajax_customer_code_theme() {
    global $client_token;
    if ($client_token && wp_verify_nonce($_POST['nonce'], 'customer_code')) {
        $dotstudio_api = new Dsp_External_Api_Request();
        if(isset($_SESSION['dotstudiopro_customer_id']) && !empty($_SESSION['dotstudiopro_customer_id'])){
            $response = $dotstudio_api->submit_device_code($_POST['code'], $_SESSION['dotstudiopro_customer_id']);
            if (is_wp_error($response)) {
                $err = $response->get_error_message();
                $send_response = array('message' => (is_string($err) ? $err : json_encode($err)));
                wp_send_json_error($send_response, 403);
            } elseif (isset($response['success']) && $response['success'] == 1) {
                $send_response = array('message' => $response['message']);
                wp_send_json_success($send_response, 200);
            } else {
                $send_response = array('message' => $response['error']);
                wp_send_json_error($send_response, 500);
            }
        }else{
            $send_response = array('success' => false, 'message' => 'Customer ID is missing');
            wp_send_json_error($send_response, 500);
        }
    } else {
        $send_response = array('success' => false, 'message' => 'Internal Server Error');
        wp_send_json_error($send_response, 500);
    }
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
        $parent_channel_id = ($_POST['parent_channel_id']) ? $_POST['parent_channel_id'] : null;
        $responce = $dotstudio_api->add_to_user_list($client_token, $channel_id, $parent_channel_id);
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
add_action('wp_ajax_account_deletion', 'account_deletion');
add_action('wp_ajax_nopriv_account_deletion', 'account_deletion');
add_action('wp_ajax_dspdl_ajax_customer_code_theme', 'dspdl_ajax_customer_code_theme');
add_action('wp_ajax_nopriv_dspdl_ajax_customer_code_theme', 'dspdl_ajax_customer_code_theme');


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
if(!function_exists('dsp_add_login_link')){
    function dsp_add_login_link($items, $args) {
        global $wp;
        if ($args->theme_location == 'main_menu' && class_exists('WP_Auth0_Options')) {
            if (is_user_logged_in()) {
                $items .= '<li id="menu-item-my_account" class="menu-item menu-item-type-custom menu-item-object-custom dropdown menu-item-category_menu">'
                        . '<a href="#" data-toggle="dropdown" class="dropdown-toggle">My Account</a>'
                        . '<ul class="dropdown-menu position-absolute" role="menu">';
                if (class_exists('Dotstudiopro_Subscription')):
                    $items .= '<li><a href="/packages">Subscriptions</a></li>'
                            . '<li><a href="/payment-profile">My Payment Profile</a></li>'
                            . '<li><a href="/purchase-history">My Purchases</a></li>';
                endif;
                $items .= '<li><a href="/my-list">My List</a></li>'
                        . '<li><a href="/account-deletion">Account Deletion</a></li>'
                        . '<li><a href="' . wp_logout_url(get_home_url()) . '">Log Out</a></li>'
                        . '</ul>'
                        . '</li>';
            } else {
                $items .= '<li><a href="' . wp_login_url( home_url( $wp->request )) . '">Log In</a></li>';
            }
        }
        return $items;
    }
}
add_filter('wp_nav_menu_items', 'dsp_add_login_link', 10, 2);

/**
 * Adjust the authorize URL before redirecting.
 *
 * @param string $auth_url - Built authorize URL.
 * @param array  $auth_params - Existing URL parameters.
 *
 * @return string
 */
function ds_auth0_authorize_url( $auth_url, $auth_params ) {
    if(dsp_wp_is_mobile())
        $auth_url .= '&device_type=mobile_web';
    else
        $auth_url .= '&device_type=desktop_web';

    return $auth_url;
}
add_filter( 'auth0_authorize_url', 'ds_auth0_authorize_url', 10, 2 );

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

    $user_metadata_property_key = 'https://dotstudiopro.com/user_metadata';

    if(isset($userinfo->{$user_metadata_property_key}) && !empty($userinfo->{$user_metadata_property_key})){
        $user_metadata_info = $userinfo->{$user_metadata_property_key};
    }

    if(isset($userinfo->user_metadata) && !empty($userinfo->user_metadata)){
        $user_metadata_info = $userinfo->user_metadata;
    }

    if((empty($user_metadata_info->customer) && empty($user_metadata_info->spotlight)) && (empty($user_metadata_info->dsp_customer_id) && empty($user_metadata_info->dsp_customer_token))){
        // No customer data
        return;
    }
    
    if(isset($user_metadata_info->dsp_customer_id) && !empty($user_metadata_info->dsp_customer_id)){
        $customer_id = $user_metadata_info->dsp_customer_id;
    }else{
        $customer_id = $user_metadata_info->customer;
    }

    if(isset($user_metadata_info->dsp_customer_token) && !empty($user_metadata_info->dsp_customer_token)){
        $spotlight = $user_metadata_info->dsp_customer_token;
    }else{
        $spotlight = $user_metadata_info->spotlight;
    }

    if(!session_id())
        session_start();

    if(isset($user_metadata_info->clear_sessions_only) && !empty($user_metadata_info->clear_sessions_only)){
        $_SESSION['max_login_limit_reached'] = true;
    }

    $_SESSION['dotstudiopro_customer_id'] = $customer_id;
    $_SESSION['dotstudiopro_client_token'] = $spotlight;
    $_SESSION['dotstudiopro_client_token_expiration'] = time() + 5400;

    // if (class_exists('Dotstudiopro_Subscription_Request')) {
    //     $subscriptionClass = new Dotstudiopro_Subscription_Request();
    //     $subscription = $subscriptionClass->getUserSubscription($spotlight);
    //     if (!empty($sub[0]->subscription->platform)) {
    //         update_user_meta($user_id, "dotstudiopro_subscription_platform", $sub[0]->subscription->platform);
    //     }
    // }
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
    wp_enqueue_style('admin-styles', DSP_THEME_ASSETS_BASE_URL . '/css/admin.min.css');
}

add_action('admin_enqueue_scripts', 'admin_style');

/**
 *  Method to get channel year and language as a string
 *
 * @param type $channel_id
 * @return type string
 *
 */
function dsp_get_channel_publication_meta($channel_id = null){
    $properties = array();
    $channel_year = get_post_meta($channel_id, 'dspro_channel_year', true);
    if($channel_year){
        $properties['year'] = $channel_year;
    }
    $channel_language = get_post_meta($channel_id, 'dspro_channel_language', true);
    if($channel_language){
        $properties['language'] = $channel_language ;
    }
    $channel_properties = implode(' | ', $properties);
    return $channel_properties;
}

/**
 * Check for mobile devise
 *
 * @staticvar type $is_mobile
 * @return boolean
 */

function dsp_wp_is_mobile() {
    static $is_mobile;

    if ( isset($is_mobile) )
        return $is_mobile;

    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_mobile = 0;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false ) {
            $is_mobile = 1;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = 1;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
        $is_mobile = 2;
    } else {
        $is_mobile = 0;
    }

    return $is_mobile;
}

/**
 * Check for devise type
 *
 * @staticvar type $is_mobile
 * @return boolean
 */

function dsp_wp_device_type() {
    static $is_mobile;

    if ( isset($is_mobile) )
        return $is_mobile;

    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_mobile = 'desktop';
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false ) {
            $is_mobile = 'mobile';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false) {
            $is_mobile = 'mobile';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
        $is_mobile = 'tablet';
    } else {
        $is_mobile = 'desktop';
    }

    return $is_mobile;
}

/**
 * Generate responsive images
 *
 * @param url $image
 * @param size $target_width
 * @param ratio $ratio
 * @return type array
 */
function dsp_build_responsive_images($image, $target_width, $ratio) {
    // fill the variant factors with defaults
    $variant_factors = [1, 1.25, 1.5, 2];

    $attributes = $srcset = array();
    // build the srcset attribute string, and generate the corresponding widths
    foreach($variant_factors as $factor) {
        $new_width = ceil($target_width * $factor);
        $new_height = ceil($ratio * $new_width);
        global $dsp_theme_options;
        if($dsp_theme_options['opt-display-webp-image'] == 0){
            $srcset[] = $image . '/'. $new_width . '/' . $new_height . ' ' . $new_width.'w';    
        }else{
            $srcset[] = $image . '/'. $new_width . '/' . $new_height . '?webp=1 ' . $new_width.'w';    
        }
    }
    $attributes['srcset'] = implode(', ', $srcset);

    // build the sizes attribute string
    $size_quries = array('(max-width: '.$target_width.'px) 100vw, 50vw');
    $attributes['sizes'] = implode(', ', $size_quries);

    return $attributes;
}

/**
 * Add shortcode for recaptcha
 */
function recaptchaAdd() {
    global $dsp_theme_options;
    if(!empty($dsp_theme_options['opt-captcha-site-key']) && !empty($dsp_theme_options['opt-captcha-secret-key'])) {
        return '<div class="g-recaptcha" data-sitekey="'.$dsp_theme_options['opt-captcha-site-key'].'" data-size="invisible"></div>';
    }
    return '';
}
add_shortcode('recaptcha', 'recaptchaAdd');

/**
 * shortcode for device login plugin
 */
function dspdl_customer_form_shortcode_theme() {
    global $dsp_theme_options, $client_token;
    $user_id = get_current_user_id();
    if (empty($user_id) || empty($client_token)) {
        ob_start(); ?>
        <div>
            <h3 class='dspdl-customer-form-login-message text-center'>You must be logged in to submit a code.
                <br><a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn btn-secondary btn-ds-secondary">Log in or sign up here</a>
            </h3>
        </div>
        <?php
        $form = ob_get_contents();
        ob_end_clean();
    } else {
        ob_start(); ?>
            <div class='dspdl-customer-form-container'>
                <div class='dspdl-customer-form-code'>
                    <input type='text' name='dspdl-customer-code' />
                    <input type="hidden" class="customer_code" name="customer_code" value="<?php echo wp_create_nonce('customer_code'); ?>">
                </div>
                <div class='dspdl-customer-form-button'>
                    <div class='dspdl-customer-form-message'></div>
                    <button class='btn btn-secondary btn-ds-secondary' type='button'>Submit Code</button>
                </div>
            </div>
        <?php
        $form = ob_get_contents();
        ob_end_clean();
    }
    return $form;
}

add_shortcode('dspdl_show_form', 'dspdl_customer_form_shortcode_theme');
add_shortcode('dsp_device_login_form', 'dspdl_customer_form_shortcode_theme');

/*
 * Generate login again dialog (Session expired)
 */
add_action ('wp_head','header_login_again_dialog');
function header_login_again_dialog() {
    ?>
    <div class="modal" tabindex="-1" data-backdrop="false" data-keyboard="false" style="background: rgba(0, 0, 0, 0.9);" role="dialog" aria-labelledby="login_again_dialog_label" id="login_again_dialog">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Session expired</h5>
          </div>
          <div class="modal-body">
            <p>Your session has expired. Please log in again.</p>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="logout_clear_session">
                <input type="submit" class="btn btn-secondary btn-ds-secondary pull-right" value="OK">
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php
}

/**
 * Session expired then User logout and clear session
 */
add_action( 'admin_post_logout_clear_session', 'logout_clear_session' );
function logout_clear_session() {
    setcookie('dsp_session_expired', null, time() - 3600, "/");
    wp_destroy_current_session();
    wp_clear_auth_cookie();
    wp_set_current_user(0);
    wp_redirect(site_url().'/wp-login.php');
}

/**
 * User logout destroy session and client token
 */
add_action( 'wp_logout', 'destroy_user_login_drm_session', 0);
function destroy_user_login_drm_session() {
    global $client_token;
    $dsp_api = new Dsp_External_Api_Request();
    $dsp_api->destroy_user_login_session($client_token);

    if(!session_id())
        session_start();

    setcookie('dsp_session_expired', null, time() - 3600, "/");
    unset($_SESSION['dotstudiopro_customer_id']);
    unset($_SESSION['dotstudiopro_client_token']);
    unset($_SESSION['dotstudiopro_client_token_expiration']);
    return true;
}

/**
 * Destroy all users login session and current user set client token
 */
add_action( 'admin_post_destroy_every_user_login_session', 'destroy_every_user_login_session' );
function destroy_every_user_login_session() {
    global $client_token;
    $dsp_api = new Dsp_External_Api_Request();
    $responce = $dsp_api->destroy_every_user_login_session($client_token);
    if(isset($responce) && isset($responce['token'])) {
        $client_token = $responce['token'];

        if(!session_id())
            session_start();

        $_SESSION['dotstudiopro_client_token'] = $client_token;
        $_SESSION['dotstudiopro_client_token_expiration'] = time() + 5400;
        unset($_SESSION['max_login_limit_reached']);
    }
    wp_redirect(site_url());

}

function dsp_get_cc_field($credit_card = null, $field) {
    return !empty($credit_card[$field]) ? $credit_card[$field] : null;
}

function dsp_get_theme_option($key) {
    return $dsp_theme_options[$key];
}

?>
