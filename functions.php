<?php

/**
 * A script/plugin that communicates with our WP Updater service to determine theme updates
 */
require 'theme-update-checker/theme-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
                'https://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-main-theme', __FILE__, 'dspdev-main-theme'
);

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
    $bootstrapcdn_url = 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css';
    $bootstrapcdn = remote_get_url($bootstrapcdn_url);
    if ($bootstrapcdn !== 200) {
        $bootstrapcdn_url = 'https://wordpress-assets.dotstudiopro.com/css/bootstrap.4.1.3.min.css';
    }
    wp_enqueue_style('bootstrap', $bootstrapcdn_url);

    $slickcdn_url = 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css';
    $slickcdn = remote_get_url($slickcdn_url);
    if ($slickcdn !== 200) {
        $slickcdn_url = get_template_directory_uri() . '/assets/css/slick.css';
    }
    wp_enqueue_style('slick', $slickcdn_url);

    $slickthemecdn_url = 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css';
    $slickthemecdn = remote_get_url($slickthemecdn_url);
    if ($slickthemecdn !== 200) {
        $slickthemecdn_url = get_template_directory_uri() . '/assets/css/slick-theme.css';
    }
    wp_enqueue_style('slick-theme', $slickthemecdn_url);

    wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');

    wp_enqueue_style('redux-global', get_template_directory_uri() . '/framework/dsp_options/redux-global.css');

    wp_enqueue_style('effects', get_template_directory_uri() . '/assets/css/effects.css');

    wp_enqueue_style('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.css', array(), '1.0.7');
}

// function to enqueue default bootstrap, slick, popper scripts and also handle the fallback if cdn falls
function bootstrapstarter_enqueue_scripts() {
    wp_enqueue_script('jquery');

    //wp_enqueue_script('slim', 'https://code.jquery.com/jquery-3.3.1.slim.min.js');

    wp_enqueue_script('tooltipster', get_template_directory_uri() . '/assets/js/tooltipster.bundle.min.js');

    $bootstrapcdn_url = 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js';
    $bootstrapcdn = remote_get_url($bootstrapcdn_url);
    if ($bootstrapcdn !== 200) {
        $bootstrapcdn_url = 'https://wordpress-assets.dotstudiopro.com/js/bootstrap.4.1.3.min.js';
    }
    wp_enqueue_script('bootstrap', $bootstrapcdn_url);


    $poper_url = 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js';
    $popercdn = remote_get_url($poper_url);
    if ($popercdn !== 200) {
        $poper_url = 'https://wordpress-assets.dotstudiopro.com/js/popper.min.js';
    }
    wp_enqueue_script('popper', $poper_url);

    $slickcdn_url = 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js';
    $slickcdn = remote_get_url($slickcdn_url);
    if ($slickcdn !== 200) {
        $slickcdn_url = get_template_directory_uri() . '/assets/js/slick.min.js';
    }
    wp_enqueue_script('slick', $slickcdn_url);

    wp_enqueue_script('jquery-auto-complete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js', array('jquery'), '1.0.7', true);
}

add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_styles');
add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_scripts');

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
    $scripts = array('jquery.mCustomScrollbar.concat.min', 'slick-init', 'image-lazy-load.min', 'classie', 'uisearch', 'custom.min', 'modernizr.custom', 'effects.min');
    foreach ($scripts as $script) :
        wp_register_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.js');
        wp_enqueue_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.js', false, false, true);
    endforeach;
    wp_localize_script('custom.min', 'jsVariable', array('ajaxUrl' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'register_theme_scripts');

// function to register and enqueue all other styles
function register_theme_styles() {
    $styles = array('main', 'ds-global', 'tooltipster.bundle.min');
    foreach ($styles as $style) :
        wp_register_style($style, get_template_directory_uri() . "/assets/css/" . $style . ".css");
        wp_enqueue_style($style, array(), filemtime(get_template_directory() . '/style.css'), 'screen');
    endforeach;
}

add_action('wp_enqueue_scripts', 'register_theme_styles');


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
    $class = ($dsp_theme_options['opt-layout'] == 1) ? 'full-width' : 'boxed';
    $fix_class = ($dsp_theme_options['opt-sticky'] == 1) ? 'stickey-nav' : '';
    echo 'class="' . join(' ', get_body_class($class)) . join(' ', get_body_class($fix_class)) . '"';
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
        $result = $dotstudio_api->search($type, $dsp_theme_options['opt-search-page-size'], 0, $q);
        if (!empty($result) && !is_wp_error($result)) {
            foreach ($result['data']['hits'] as $key => $data):
                if ($type == 'channel'):
                    $image = (isset($data['poster'])) ? $data['poster'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                else:
                    $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                endif;
                $items[$key]['title'] = $data['_source']['title'];
                $items[$key]['image'] = $image;
            endforeach;
        }
    }
    wp_send_json_success($items);
}

add_action('wp_ajax_autocomplete', 'autocomplete');
add_action('wp_ajax_nopriv_autocomplete', 'autocomplete');

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
    if ($args->theme_location == 'main_menu') {
        if (is_user_logged_in()) {
            $items .= '<li><a href="' . wp_logout_url(get_home_url()) . '">Log Out</a></li>';
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

?>
