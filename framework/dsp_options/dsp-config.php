<?php

/**
 * Dotstudio Pro Theme options Config File
 * @since 1.0.0
 */
if (!class_exists('Redux')) {
    return;
}

global $wpdb;

// This is your option name where all the Redux data is stored.
$opt_name = "dsp_theme_options";

$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
    'opt_name' => $opt_name,
    'display_name' => $theme->get('Name'),
    'display_version' => $theme->get('Version'),
    'menu_type' => 'menu',
    'allow_sub_menu' => true,
    'menu_title' => __('Theme Options', 'dotstudio-pro'),
    'page_title' => __('Theme Options', 'dotstudio-pro'),
    'google_api_key' => '',
    'google_update_weekly' => false,
    'async_typography' => false,
    'admin_bar' => true,
    'admin_bar_icon' => 'dashicons-portfolio',
    'admin_bar_priority' => 50,
    'global_variable' => '',
    'dev_mode' => true,
    'update_notice' => true,
    'customizer' => true,
    'page_priority' => null,
    'page_parent' => 'themes.php',
    'page_permissions' => 'manage_options',
    'menu_icon' => '',
    'last_tab' => '',
    'page_icon' => 'icon-themes',
    'page_slug' => 'dsp_options',
    'save_defaults' => true,
    'default_show' => false,
    'default_mark' => '',
    'show_import_export' => true,
    'transient_time' => 60 * MINUTE_IN_SECONDS,
    'output' => true,
    'output_tag' => true,
    'database' => '',
    'use_cdn' => true,
    'dev_mode' => false,
    'ajax_save' => false,
    'show_options_object' => false,
    'hints' => array(
        'icon' => 'el el-question-sign',
        'icon_position' => 'right',
        'icon_color' => 'lightgray',
        'icon_size' => 'normal',
        'tip_style' => array(
            'color' => 'light',
            'shadow' => true,
            'rounded' => false,
            'style' => '',
        ),
        'tip_position' => array(
            'my' => 'top left',
            'at' => 'bottom right',
        ),
        'tip_effect' => array(
            'show' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'mouseover',
            ),
            'hide' => array(
                'effect' => 'slide',
                'duration' => '500',
                'event' => 'click mouseleave',
            ),
        ),
    )
);

Redux::setArgs($opt_name, $args);

// Set the help sidebar
$content = __('<p>This is the sidebar content, HTML is allowed.</p>', 'dotstudio-pro');
Redux::setHelpSidebar($opt_name, $content);

/**
 * General Options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('General', 'dotstudio-pro'),
    'id' => 'general',
    'icon' => 'el el-website',
    'fields' => array(
        array(
            'id' => 'opt-layout',
            'type' => 'button_set',
            'title' => __('Layout Option', 'dotstudio-pro'),
            'desc' => __('Choose the layout for the site', 'dotstudio-pro'),
            'options' => array(
                '0' => 'Boxed',
                '1' => 'Full Width'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-favicon-url',
            'type' => 'media',
            'title' => __('Favicon icon', 'dotstudio-pro'),
            'subtitle' => __('The icon that appears in the browser for your site. Recommended sizes: 16x16 or 32x32px.', 'dotstudio-pro'),
            'default' => ''
        ),
        array(
            'id' => 'opt-back-to-top',
            'type' => 'switch',
            'title' => __('Back To Top Button ', 'dotstudio-pro'),
            'subtitle' => __('Choose whether or not to have a button on each page that will return the user to the top of the page', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-img-hover',
            'type' => 'select',
            'title' => __('Select Hover effect for the carousel items', 'dotstudio-pro'),
            'subtitle' => __('Choose any one hover effect from the different 5 types of effects', 'dotstudio-pro'),
            'options' => array(
                1 => 'Hover Effect 1',
                2 => 'Hover Effect 2',
                3 => 'Hover Effect 3',
                4 => 'Hover Effect 4',
                5 => 'Hover Effect 5',
            ),
            'default' => 1
        ),
        array(
            'id' => 'opt-img-hover',
            'type' => 'select',
            'title' => __('Select Hover effect for the carousel items', 'dotstudio-pro'),
            'subtitle' => __('Choose any one hover effect from the different 5 types of effects', 'dotstudio-pro'),
            'options' => array(
                1 => 'Hover Effect 1',
                2 => 'Hover Effect 2',
                3 => 'Hover Effect 3',
                4 => 'Hover Effect 4',
                5 => 'Hover Effect 5',
            ),
            'default' => 1
        ),
        array(
            'id' => 'opt-google-analytics',
            'type' => 'textarea',
            'title' => __('Google Analytics/Tag Manager Code', 'dotstudio-pro'),
            'validate' => 'html_custom',
            'default' => '',
            'allowed_html' => array(
                'a' => array(
                    'href' => array(),
                    'title' => array()
                ),
                'br' => array(),
                'em' => array(),
                'strong' => array()
            )
        ),
    )
));

/**
 * Header Options
 * @since 1.0.0
 */
$options = array();
$args = array(
    'post_type' => 'channel-category',
    'post_status' => 'publish',
    'posts_per_page' => '-1',
    'meta_query' => array(
        array(
            'key' => 'is_in_cat_menu',
            'compare' => '=',
            'value' => 1,
        ),
    ),
);
$posts = new WP_Query($args);
if ($posts->have_posts()) {
    foreach ($posts->posts as $post) {
        $options[$post->post_name] = stripslashes($post->post_title);
    }
}

Redux::setSection($opt_name, array(
    'title' => __('Header', 'dotstudio-pro'),
    'id' => 'header',
    'icon' => 'el el-arrow-up',
    'fields' => array(
        array(
            'id' => 'opt-header-padding',
            'type' => 'spacing',
            'mode' => 'padding',
            'compiler' => array('header'),
            'all' => false,
            'default' => '',
            'units' => array('em', 'px', '%'), // You can specify a unit value. Possible: px, em, %
            'units_extended' => 'true', // Allow users to select any type of unit
            'title' => __('Header Padding', 'dotstudio-pro'),
            'subtitle' => __('Controls the top/right/bottom/left margins for the header. Enter values including any valid CSS unit, ex: 31px, 31px, 0px, 0px.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-logo-align',
            'type' => 'button_set',
            'title' => __('Logo Alignment', 'dotstudio-pro'),
            'options' => array(
                'left' => 'Left',
                'center' => 'Center',
            ),
            'default' => 'left'
        ),
        array(
            'id' => 'opt-logo-margin',
            'type' => 'spacing',
            'mode' => 'margin',
            'compiler' => array('.site-logo'),
            'default' => '',
            'all' => false,
            'units' => array('em', 'px', '%'), // You can specify a unit value. Possible: px, em, %
            'units_extended' => 'true', // Allow users to select any type of unit
            'title' => __('Logo Margins', 'dotstudio-pro'),
            'subtitle' => __('Controls the top/right/bottom/left margins for the logo. Enter values including any valid CSS unit, ex: 31px, 31px, 0px, 0px.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-logo-url',
            'type' => 'media',
            'title' => __('Default Logo', 'dotstudio-pro'),
            'subtitle' => __('Select an image file for your logo.', 'dotstudio-pro'),
            'default' => ''
        ),
        array(
            'id' => 'opt-logo-height',
            'type' => 'dimensions',
            'title' => __('Dimensions (Height) Option for the Home page logo', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose height for the logo.', 'dotstudio-pro'),
            'desc' => __('You can enable or disable any piece of this field. Height, or Units.', 'dotstudio-pro'),
            'compiler' => array('.site-logo img'),
            'units' => array('px', 'rem'),
            'width' => false,
            'default' => array(
                'height' => 50,
            )
        ),
        array(
            'id' => 'opt-search',
            'type' => 'switch',
            'title' => __('Enable/Disable the search bar', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'Enabled',
            'off' => 'Disabled',
        ),
        array(
            'id' => 'opt-sticky',
            'type' => 'switch',
            'title' => __('Navbar Sticky', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-category-menu',
            'type' => 'switch',
            'title' => __('Show Category Menu', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-menu-title',
            'type' => 'text',
            'title' => 'Menu Title',
            'required' => array('opt-category-menu', '=', '1'),
            'subtitle' => __('The menu title to display on the header menu', 'dotstudio-pro'),
            'default' => 'categories'
        ),
        array(
            'id' => 'opt-menu-link',
            'type' => 'text',
            'title' => __('Menu URL', 'dotstudio-pro'),
            'subtitle' => __('The URL for the Menu', 'dotstudio-pro'),
            'desc' => __('This must be a URL.', 'dotstudio-pro'),
            'required' => array('opt-category-menu', '=', '1'),
            'default' => '#'
        ),
        array(
            'title' => __('Menu Position', 'dotstudio-pro'),
            'desc' => __('Choose the postion to display menu on the home page.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-menu-position',
            'required' => array('opt-category-menu', '=', '1'),
            'options' => array_combine(range(1, 10), range(1, 10)),
            'default' => 1
        ),
        array(
            'id' => 'opt-menu-sorter',
            'type' => 'sorter',
            'title' => 'Header Category Menu Order',
            'required' => array('opt-category-menu', '=', '1'),
            'options' => array(
                'enabled' => $options,
                'disabled' => array(),
            ),
        ),
    )
));

/**
 * Color Schema
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Color Schema', 'dotstudio-pro'),
    'id' => 'color',
    'icon' => 'el el-brush',
    'fields' => array(
        array(
            'id' => 'opt-bg-color-header',
            'type' => 'color',
            'title' => __('Site Header Background Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the header (default: #428bca).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'compiler' => array('header'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-header',
            'type' => 'color',
            'title' => __('Site Header Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the header text (default: #000000).', 'dotstudio-pro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'compiler' => array('header'),
        ),
        array(
            'id' => 'opt-background',
            'type' => 'background',
            'compiler' => array('body'),
            'title' => __('Body Background', 'dotstudio-pro'),
            'subtitle' => __('Body background with image, color, etc.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-color-body',
            'type' => 'color',
            'title' => __('Site Body Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the body text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'compiler' => array('body'),
        ),
        array(
            'id' => 'opt-bg-color-footer',
            'type' => 'color',
            'title' => __('Footer Background Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the footer (default: #dd9933).', 'dotstudio-pro'),
            'default' => '#dddcdb',
            'validate' => 'color',
            'compiler' => array('footer'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-footer',
            'type' => 'color',
            'title' => __('Site Footer Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the footer text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'compiler' => array('footer'),
        ),
    )
));

/**
 * Typrography
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Typography', 'dotstudio-pro'),
    'id' => 'typography',
    'icon' => 'el el-fontsize',
    'fields' => array(
        array(
            'id' => 'opt-typography-body',
            'type' => 'typography',
            'output' => array('*'),
            'all_styles' => true,
            'font-backup' => true,
            'title' => __('Body Font', 'dotstudio-pro'),
            'font-size' => false,
            'line-height' => false,
            'subtitle' => __('Specify the body font properties.', 'dotstudio-pro'),
            'google' => true,
            'default' => array(
                'color' => '#dd9933',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'font-weight' => 'Normal',
            ),
        ),
        array(
            'id' => 'opt-typography-h1',
            'type' => 'typography',
            'title' => __('Typography h1', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h1'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => ' 4.9375',
                'line-height' => ' 4.9375'
            ),
        ),
        array(
            'id' => 'opt-typography-h2',
            'type' => 'typography',
            'title' => __('Typography h2', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h2'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => ' 4.9375',
                'line-height' => ' 4.9375'
            ),
        ),
        array(
            'id' => 'opt-typography-h3',
            'type' => 'typography',
            'title' => __('Typography h3', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h3'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '3.125',
                'line-height' => '3.125'
            ),
        ),
        array(
            'id' => 'opt-typography-h4',
            'type' => 'typography',
            'title' => __('Typography h4', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h4'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '1.875',
                'line-height' => '1.875'
            ),
        ),
        array(
            'id' => 'opt-typography-h5',
            'type' => 'typography',
            'title' => __('Typography h5', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h5'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '1.5',
                'line-height' => '1.5'
            ),
        ),
        array(
            'id' => 'opt-typography-h6',
            'type' => 'typography',
            'title' => __('Typography h6', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // An array of CSS selectors to apply this font style to dynamically
            'output' => array('h6'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'rem',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '1',
                'line-height' => '1'
            ),
        ),
    )
));

$options = array();
$args = array(
    'post_per_page' => -1,
    'post_type' => 'channel-category',
    'meta_query' => array(
        array(
            'key' => 'is_on_cat_homepage',
            'value' => 1
        )
    )
);
$posts = new WP_Query($args);
$default_option = '';
if ($posts->have_posts()) {
    $default_option = $posts->posts[0]->post_name;
    foreach ($posts->posts as $post) {
        $options[$post->post_name] = stripslashes($post->post_title);
    }
}

/**
 * HomePage Settings
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Home Page', 'dotstudio-pro'),
    'id' => 'home',
    'customizer_width' => '400px',
    'icon' => 'el el-home'
));

Redux::setSection($opt_name, array(
    'title' => __('Homepage Main Carousel', 'dotstudio-pro'),
    'id' => 'homepage',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => __('Select Category For Main Carousel', 'dotstudio-pro'),
            'desc' => __('Choose the category to be used for the homepage carousel.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-home-carousel',
            'options' => $options,
            'default' => $default_option
        ),
        array(
            'id' => 'opt-poster-type',
            'type' => 'radio',
            'title' => __('Select Channel Banner Type For Main Carousel', 'dotstudio-pro'),
            'subtitle' => __('Select the channel banner type which you would like to display on the main carousel', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-play-btn-type',
            'type' => 'radio',
            'title' => __('Select Navigate Button Type For Main Carousel', 'dotstudio-pro'),
            'subtitle' => __('Select the navigate button type which you would like to display on the main carousel. i.e.: <br/> 1). show a Play Video button (navigates to the first video of the channel) <b>OR</b><br/> 2). show a Watch Now button (navigates to the channel\'s list page)', 'dotstudio-pro'),
            'options' => array(
                'play_video' => 'Play Video Button',
                'watch_now' => 'Watch Now Button',
            ),
            'default' => 'watch_now'
        ),
        array(
            'id' => 'opt-homepage-main-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on homepage main carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be shown for related content titles in the carousel.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-homepage-main-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on homepage main carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be shown for related content descriptions in the carousel.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
    ),
));

/**
 * Home page Slick carousel Settings
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Homepage other Carousel', 'dotstudio-pro'),
    'id' => 'carousels',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-carousel-poster-type',
            'type' => 'radio',
            'title' => __('Select Carousel Image Type', 'dotstudio-pro'),
            'subtitle' => __('Select the poster banner type which you would like to display on the carousel.', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-layout-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option.', 'dotstudio-pro'),
            'desc' => __('Select Tooltip option if you need to display description on tooltip either select text to display description below the image. ', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text',
                '2' => 'Tooltip'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words shown in category titles in the carousel', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words shown in category descriptions in the carousel', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'id' => 'opt-slick-home-slidetoshow',
            'type' => 'spinner',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of images to be displayed in a carousel row', 'dotstudio-pro'),
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-home-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be scrolled', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-home-pagination',
            'title' => __('Enable Pagination', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation dots under the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-home-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation arrows', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable autoplay to the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-home-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('Please enter the value in miliseconds (ex: 1 second is 1000, 2 seconds is 2000)', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-home-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the speed for the slide', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in miliseconds (ie. in multiples of 100).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-home-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be displayed in tablet screen', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array('opt-slick-home-responsive', '=', true),
            'default' => '2',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
        array(
            'id' => 'opt-slick-home-mobile-slidetoshow',
            'title' => __('Slides to show in mobile device', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be displayed in mobile screen', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array('opt-slick-home-responsive', '=', true),
            'default' => '1',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
    ),
));

/**
 * Categories page
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Category Page', 'dotstudio-pro'),
    'id' => 'cat',
    'customizer_width' => '400px',
    'icon' => 'el el-list'
));

Redux::setSection($opt_name, array(
    'title' => __('Categories', 'dotstudio-pro'),
    'id' => 'categories',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-categories-poster-type',
            'type' => 'radio',
            'title' => __('Select Category Image Type', 'dotstudio-pro'),
            'subtitle' => __('Select the category banner type which you would like to display on category page.', 'dotstudio-pro'),
            'options' => array(
                'poster' => 'Poster',
                'wallpaper' => 'wallpaper',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-categories-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the Category Image', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-categories-title',
            'title' => __('Display Category Title', 'dotstudio-pro'),
            'subtitle' => __('This option is to display title on category page.', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => __('Number of category to display in a row', 'dotstudio-pro'),
            'desc' => __('Choose the option to display number of rows in Categories page.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-display-categories-row',
            'options' => array_combine(array(12, 6, 4, 3, 2), array(1, 2, 3, 4, 6)),
            'default' => 4
        ),
        array(
            'id' => 'opt-cateogry-listing-option',
            'type' => 'button_set',
            'title' => __('Choose the option to display Category listing page', 'dotstudio-pro'),
            'subtitle' => __('Choose the option to display Category listing page / IVP page.', 'dotstudio-pro'),
            'desc' => __('Select Category listing page option to show Channel/video listing by click on category, or select IVP page to play first video in playlist..', 'dotstudio-pro'),
            'options' => array(
                'category-listing-page' => 'Category listing page',
                'ivp-page' => 'IVP page'
            ),
            'default' => 'category-listing-page'
        ),
    )
));

/**
 * Category Detail page options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Category Detail Page', 'dotstudio-pro'),
    'id' => 'category',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-category-poster-type',
            'type' => 'radio',
            'title' => __('Select Category Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the category banner type which you would like to display on category page.', 'dotstudio-pro'),
            'options' => array(
                'poster' => 'Poster',
                'wallpaper' => 'wallpaper',
            ),
            'default' => 'wallpaper'
        ),
        array(
            'id' => 'opt-category-poster-information',
            'title' => __('Display Category Poster Information', 'dotstudio-pro'),
            'subtitle' => __('This option is to display the category information on poster and also display the "Play Now" button that you will navigate first video of that category.', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-category-channel-poster-type',
            'type' => 'radio',
            'title' => __('Select Channal Poster Type', 'dotstudio-pro'),
            'subtitle' => __('Select the channel poster type which you would like to display on category detail page', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-channel-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the Channel Image', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-layout-channel-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option for channels.', 'dotstudio-pro'),
            'desc' => __('Select Tooltip option if you need to display description on tooltip either select text to display description below the image. ', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text',
                '2' => 'Tooltip'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-channel-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be visible on channel title.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-channel-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be visible on channel description.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'title' => __('Number of category to display in a row', 'dotstudio-pro'),
            'desc' => __('Choose the option to display number of rows in Categories page.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-display-row',
            'options' => array_combine(array(12, 6, 4, 3, 2), array(1, 2, 3, 4, 6)),
            'default' => 4
        ),
    )
));

/**
 * Channel Page options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Channel Page', 'dotstudio-pro'),
    'id' => 'channel-page-carousels',
    'icon' => 'el el-film',
    'fields' => array(
        array(
            'id' => 'opt-channel-poster-type',
            'type' => 'radio',
            'title' => __('Select Channal Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the channel banner type which you would like to display', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-channel-video-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the video carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-channel-video-layout-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout for videos', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option.', 'dotstudio-pro'),
            'desc' => __('Tooltip will display information in a tooltip, while Text will display information on the thumbnail', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text',
                '2' => 'Tooltip'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-channel-video-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on video carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words shown for the Video Title on the carousel.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-channel-video-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on video carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words shown for the Video Description on the carousel.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'id' => 'opt-slick-video-slidetoshow',
            'type' => 'spinner',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of images to be displayed in a carousel row', 'dotstudio-pro'),
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-video-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be scrolled', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-video-pagination',
            'title' => __('Enable Pagination', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation dots under the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-video-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation arrows', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable autoplay to the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-video-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('By default set to  "2000". Please enter the value in miliseconds (ie. in multiples of 1000).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-video-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the speed for the slide', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in miliseconds (ie. in multiples of 1000 for seconds).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-video-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be displayed in tablet screen', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array('opt-slick-video-responsive', '=', true),
            'default' => '2',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
        array(
            'id' => 'opt-slick-video-mobile-slidetoshow',
            'title' => __('Slides to show in mobile device', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of images to be displayed in mobile screen', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array('opt-slick-video-responsive', '=', true),
            'default' => '1',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
    ),
));

/**
 * Recommendations content options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Recommendations ', 'dotstudio-pro'),
    'id' => 'related-content',
    'icon' => 'el el-photo',
    'fields' => array(
        array(
            'id' => 'opt-related-section',
            'type' => 'switch',
            'title' => __('Display Recommendations Content', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-related-content-text',
            'type' => 'text',
            'title' => __('Recommendations Content Title', 'dotstudio-pro'),
            'default' => 'Related Content',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-related-option',
            'type' => 'button_set',
            'title' => __('Choose the option to display recommendations content', 'dotstudio-pro'),
            'subtitle' => __('Choose the option to display video / channel.', 'dotstudio-pro'),
            'desc' => __('Select Channel option to show related channels carousel under the content, or select Video to display related videos.', 'dotstudio-pro'),
            'options' => array(
                'channel' => 'Channel',
                'video' => 'Video'
            ),
            'required' => array('opt-related-section', '=', 1),
            'default' => 'channel'
        ),
        array(
            'id' => 'opt-related-channel-poster-type',
            'type' => 'radio',
            'title' => __('Select Channal Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the channel banner type which you would like to display', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'required' => array(array('opt-related-section', '=', 1), array('opt-related-option', '=', 'channel')),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-related-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the recommendations content carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'required' => array('opt-related-section', '=', 1),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-related-layout-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option.', 'dotstudio-pro'),
            'desc' => __('Select Tooltip option if you need to display description in a tooltip, or select Text to display description below the image. ', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text',
                '2' => 'Tooltip'
            ),
            'required' => array('opt-related-section', '=', 1),
            'default' => '1'
        ),
        array(
            'id' => 'opt-related-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on recommendations content carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be shown for related content titles in the carousel.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-related-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on recommendations content carousel', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be shown for related content descriptions in the carousel.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
            'required' => array('opt-related-section', '=', 1),
        ),
    ),
));

/**
 * Search results options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Saerch Results  Page ', 'dotstudio-pro'),
    'id' => 'search-results',
    'icon' => 'el el-search',
    'fields' => array(
        array(
            'id' => 'opt-search-option',
            'type' => 'button_set',
            'title' => __('Choose the option to search for content', 'dotstudio-pro'),
            'subtitle' => __('Choose the option to search video / channel.', 'dotstudio-pro'),
            'options' => array(
                'channel' => 'Channel',
                'video' => 'Video'
            ),
            'default' => 'channel'
        ),
        array(
            'id' => 'opt-search-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the search result content thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the search result thumbnails.', 'dotstudio-pro'),
            'required' => array('opt-related-section', '=', 1),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'title' => __('Number of columns to display in a row', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-search-columns-row',
            'options' => array_combine(array(12, 6, 4, 3, 2), array(1, 2, 3, 4, 6)),
            'default' => 4
        ),
        array(
            'id' => 'opt-search-page-size',
            'type' => 'spinner',
            'title' => __('Select the number of posts to display per page', 'dotstudio-pro'),
            'default' => '6',
            'min' => '0',
            'step' => '1',
            'max' => '30',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-search-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on search results page', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be shown for search results titles.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
            'required' => array('opt-related-section', '=', 1),
        ),
    ),
));

/**
 * Footer Options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Footer', 'dotstudio-pro'),
    'id' => 'footer',
    'icon' => 'el el-arrow-down',
    'fields' => array(
        array(
            'id' => 'opt-select-menus',
            'type' => 'select',
            'data' => 'menus',
            'title' => __('Footer menu', 'dotstudio-pro'),
            'desc' => __('Select a menu to display in the footer section.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-copyright-text',
            'type' => 'text',
            'title' => __('Copyright Text', 'dotstudio-pro'),
            'default' => '',
        ),
        array(
            'id' => 'opt-social-icons',
            'type' => 'switch',
            'title' => __('Show social icons', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-social-links-target',
            'type' => 'switch',
            'title' => __('Social icon links: Open in new Tab?', 'dotstudio-pro'),
            'default' => 1,
            'on' => 'Enable',
            'off' => 'Disable',
        ),
        array(
            'id' => 'section-start',
            'type' => 'section',
            'title' => __('Social icons', 'dotstudio-pro'),
            'subtitle' => __('Controls the social media URLs.', 'dotstudio-pro'),
            'required' => array('opt-social-icons', '=', '1'),
            'indent' => true
        ),
        array(
            'id' => 'facebook-link',
            'type' => 'text',
            'title' => __('Facebook Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Facebook Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'twitter-link',
            'type' => 'text',
            'title' => __('Twitter Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Twitter Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'dribbble-link',
            'type' => 'text',
            'title' => __('Dribbble Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Dribbble Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'flickr-link',
            'type' => 'text',
            'title' => __('Flickr Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Flickr Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'github-link',
            'type' => 'text',
            'title' => __('Github Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Github Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'pinterest-link',
            'type' => 'text',
            'title' => __('Pinterest Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Pinterest Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'youtube-link',
            'type' => 'text',
            'title' => __('Youtube Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Youtube Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'google-plus-link',
            'type' => 'text',
            'title' => __('Google+ Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Google+ Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'linkedin-link',
            'type' => 'text',
            'title' => __('LinkedIn Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('LinkedIn Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'instagram-link',
            'type' => 'text',
            'title' => __('Instagram Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Instagram Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'vimeo-link',
            'type' => 'text',
            'title' => __('Vimeo Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be a URL.', 'dotstudio-pro'),
            'desc' => __('Vimeo Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'section-end',
            'type' => 'section',
            'indent' => false,
        ),
    )
));

add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);

/**
 * This is a test function that will let you see when the compiler hook occurs.
 * It only runs if a field    set with compiler=>true is changed.
 *
 * @since 1.0.0
 */
if (!function_exists('compiler_action')) {

    function compiler_action($options, $css, $changed_values) {
        global $wp_filesystem;

        $filename = dirname(__FILE__) . '/redux-global.css';

        if (empty($wp_filesystem)) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        if ($wp_filesystem) {
            $wp_filesystem->put_contents(
                    $filename, $css, FS_CHMOD_FILE // predefined mode settings for WP files
            );
        }
    }

}