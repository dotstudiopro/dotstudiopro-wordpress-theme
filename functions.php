<?php

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

// function to enqueue default stlyes
function bootstrapstarter_enqueue_styles() {
    wp_register_style('custom-css', get_template_directory_uri() . '/framework/dsp_options/style.css');
    wp_register_style('bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css');
    $dependencies = array('bootstrap');
    wp_enqueue_style('bootstrapstarter-style', get_stylesheet_uri(), $dependencies);
}

// function to enqueue default scripts
function bootstrapstarter_enqueue_scripts() {
    $dependencies = array('jquery');
    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', $dependencies, false, true);
}

add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_styles');
add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_scripts');

// function to register and enqueue all other scripts
function register_theme_scripts() {
    $scripts = array('slick.min','slick-init', 'custom');
    foreach ($scripts as $script) :
        wp_register_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.js');
        wp_enqueue_script($script, get_template_directory_uri() . '/assets/js/' . $script . '.js', false, false, true);
    endforeach;
}

add_action('wp_enqueue_scripts', 'register_theme_scripts');

// function to register and enqueue all other styles
function register_theme_styles() {
    $styles = array('slick','slick-theme');
    foreach ($styles as $style) :
        wp_register_style($style, get_template_directory_uri() . "/assets/css/" . $style . ".css");
        wp_enqueue_style($style, array(), filemtime(get_template_directory() . '/style.css'), 'screen');
    endforeach;
}

add_action('wp_enqueue_scripts', 'register_theme_styles');

// function to add title-tag
function bootstrapstarter_wp_setup() {
    add_theme_support('title-tag');
}

// function to add bosy class
function theme_body_class($class = '') {
    global $dsp_theme_options;
    $class = ($dsp_theme_options['opt-layout'] == 1) ? 'full-width' : 'boxed';
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
                'classes' => array('menu-item', 'menu-item-type-custom', 'menu-item-object-custom')
            );

            $new_links[] = (object) $item; // Add the new menu item to our array
            // insert item
            $location = $dsp_theme_options['opt-menu-position'] + 1;
            array_splice($items, $location, 0, $new_links);
        }
    }
    return $items;
}

?>