<?php

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

// function to enqueue default stlyes

function bootstrapstarter_enqueue_styles() {
    wp_register_style('bootstrap', get_template_directory_uri() . '/framework/dsp_options/style.css');
    wp_register_style('custom-css', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css');
    $dependencies = array('bootstrap');
    wp_enqueue_style('bootstrapstarter-style', get_stylesheet_uri(), $dependencies);
}

// function to enqueue default scripts

function bootstrapstarter_enqueue_scripts() {
    $dependencies = array('jquery');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', $dependencies, '', true);
}

add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_styles');
add_action('wp_enqueue_scripts', 'bootstrapstarter_enqueue_scripts');

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

// function to enable menu option in Theme
if (function_exists('register_nav_menus')) {
    register_nav_menus(
            array(
                'main_menu' => esc_html__('Main Menu', 'wp-bootstrap'),
                'primary' => esc_html__('Primary', 'wp-bootstrap'),
            )
    );
}
?>