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
            'id' => 'opt-google-analytics',
            'type' => 'textarea',
            'title' => __('Google Analytics/Tag Manager Code', 'dotstudio-pro'),
            'desc' => __('Add google tag manager code with having SCRIPT tag', 'dotstudio-pro'),
            'default' => '',
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
$page_options = array();
$page_lists = get_pages();
if ($page_lists) {
    foreach ($page_lists as $page_list) {
        $page_options[$page_list->post_name] = stripslashes($page_list->post_title);
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
            'subtitle' => __('Controls the top/right/bottom/left padding for the header.', 'dotstudio-pro'),
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
            'subtitle' => __('Controls the top/right/bottom/left margins for the logo.', 'dotstudio-pro'),
        ),
		array(
			'id'       => 'opt-logo-text',
			'type'     => 'text',
			'title'    => __('External image URL', 'dotstudio-pro'),
			'desc'     => __('Add an external image URL to use it instead of LOGO.', 'dotstudio-pro'),
			'validate' => 'url',
			'msg'      => 'Please upload valid image URL',
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
            'subtitle' => __('Set the height for the logo.', 'dotstudio-pro'),
            'output' => array('.site-logo img'),
            'units' => array('em', 'px', '%'),
            'width' => false,
            'default' => array(
                'height' => 75,
            )
        ),
         array(
            'id' => 'opt-logo-link',
            'type' => 'select',
            'title' => __('Select the page you want to link to logo', 'dotstudio-pro'),
            'subtitle' => __('Choose any one page on which the user will be redirected after clicking the site logo', 'dotstudio-pro'),
            'options' => $page_options,
            'default' => '',
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
            'title' => __('Sticky Navbar', 'dotstudio-pro'),
            'subtitle' => __('Set the navbar to stay at the top of the window/viewpart when a user scrolls down', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-logo-height-after-resize',
            'type' => 'dimensions',
            'title' => __('Dimensions (Height) Option for the Home page logo after resize', 'dotstudio-pro'),
            'subtitle' => __('The height of the logo after the navbar resizes', 'dotstudio-pro'),
            'description' => __('When a user scrolls down a page, you generally want to minimize the presence of your logo and the header in general so as to not interfere with a user seeing what they came for on your site. This allows you to determine what that size is.', 'dotstudio-pro'),
            'output' => array('header.fixed-top.small-header .site-logo img'),
            'required' => array('opt-sticky', '=', '1'),
            'units' => array('em', 'px', '%'),
            'width' => false,
            'default' => array(
                'height' => 75,
            )
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
            'subtitle' => __('The title of the category menu to display in the header', 'dotstudio-pro'),
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
            'desc' => __('Choose the postion to display the menu on the home page.', 'dotstudio-pro'),
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
            'id' => 'opt-color-selection-section',
            'type' => 'switch',
            'title' => __('Set Main Theme Color', 'dotstudio-pro'),
            'description' => __('This option overrides main theme colors set in your CSS file, if turned on.', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-main-theme-color',
            'type' => 'color',
            'title' => __('Site Main Theme Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a text color for the theme (default: #87b145).', 'dotstudio-pro'),
            'default' => '#87b145',
            'validate' => 'color',
            'required' => array('opt-color-selection-section', '=', 1),
        ),
        array(
            'id' => 'opt-main-theme-hover-color',
            'type' => 'color',
            'title' => __('Site Main Theme Hover Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a link hover color the theme (default: #87b145).', 'dotstudio-pro'),
            'default' => '#87b145',
            'validate' => 'color',
            'required' => array('opt-color-selection-section', '=', 1),
        ),
        array(
            'id' => 'opt-bg-color-header',
            'type' => 'color',
            'title' => __('Site Header Background Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the header (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('header'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-header',
            'type' => 'color',
            'title' => __('Site Header Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a color for the header text (default: #ffffff).', 'dotstudio-pro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'output' => array('header', '.main-navigation .navbar-nav > li a', '.site-logo', '.sb-search .sb-icon-search', '.sb-search.sb-search-open .sb-icon-search'),
        ),
        array(
            'id' => 'opt-background',
            'type' => 'background',
            'output' => array('body'),
            'title' => __('Body Background', 'dotstudio-pro'),
            'subtitle' => __('CSS for the body background, such as the color or an image.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-color-body',
            'type' => 'color',
            'title' => __('Site Body Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a color for the body text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('body', 'p'),
        ),
        array(
            'id' => 'opt-bg-color-footer',
            'type' => 'color',
            'title' => __('Footer Background Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the footer (default: #dddcdb).', 'dotstudio-pro'),
            'default' => '#dddcdb',
            'validate' => 'color',
            'output' => array('footer'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-footer',
            'type' => 'color',
            'title' => __('Site Footer Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a color for the footer text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('footer', '.footer-nav ul li a', 'footer .copyright p', 'footer h3'),
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
            'color' => false,
            'default' => array(
                'font-family' => 'Arial,Helvetica,sans-serif',
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
            'output' => array('h3', 'h3 a'),
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
    'posts_per_page' => -1,
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
    'title' => __('Homepage: Main Carousel', 'dotstudio-pro'),
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
            'subtitle' => __('Select the channel banner type you would like to display in the main carousel', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-main-home-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the main carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width and height for the main thumbnails carousel.', 'dotstudio-pro'),
            'default' => array(
                'width' => 1920,
                'height' => 650,
            )
        ),
        array(
            'id' => 'opt-main-home-image-mobile-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the main carousel thumbnails. (For smaller devices)', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width and height for the main thumbnails carousel. (For smaller devices)', 'dotstudio-pro'),
            'default' => array(
                'width' => 600,
                'height' => 450,
            )
        ),
        array(
            'id' => 'opt-play-btn-type',
            'type' => 'radio',
            'title' => __('Select Navigate Button Type For Main Carousel', 'dotstudio-pro'),
            'subtitle' => __('Select the navigate button type which you would like to display on the main carousel. i.e.: <br/> 1). Show a Play Video button (navigates to the first video of the channel) <b>OR</b><br/> 2). Show a Watch Now button (navigates to the channel\'s list page)', 'dotstudio-pro'),
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
            'desc' => __('Set number of words to be shown for related content titles in the carousel', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-homepage-main-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on homepage main carousel', 'dotstudio-pro'),
            'desc' => __('Set number of words to be shown for related content descriptions in the carousel', 'dotstudio-pro'),
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
    'title' => __('Homepage: Secondary Carousels', 'dotstudio-pro'),
    'id' => 'carousels',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-carousel-poster-type',
            'type' => 'radio',
            'title' => __('Select Carousel Image Type', 'dotstudio-pro'),
            'subtitle' => __('Select the poster banner type you would like to display on the carousel', 'dotstudio-pro'),
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
            'subtitle' => __('Choose width and height for the carousel thumbnails', 'dotstudio-pro'),
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
            'desc' => __('Tooltip will display everything in a tooltip, where "Text" and "Text & Description" will display whichever you select below the image', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text & Description',
                '2' => 'Tooltip',
                '3' => 'Text',
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words shown in category titles in the carousel', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words shown in category descriptions in the carousel', 'dotstudio-pro'),
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
            'subtitle' => __('This defines the number of images to be scrolled when one of the arrow navigation buttons in the carousel is clicked', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-home-slidestoload',
            'title' => __('Slides to load', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of slides you want to load in each carousel. Note that higher numbers may impede load times.'),
            'type' => 'spinner',
            'default' => '10',
            'min' => '1',
            'step' => '1',
            'max' => '20',
        ),
        array(
            'id' => 'opt-slick-home-pagination',
            'title' => __('Enable Direct Navigation in Carousels', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation dots under the carousel ', 'dotstudio-pro'),
            'description' => __('By default, this is set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-home-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation arrows on the carousel', 'dotstudio-pro'),
            'description' => __('By default, this is set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('Enable autoplay for secondary carousels', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-home-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('Set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('Please enter the value in milliseconds (ex: 1 second is 1000, 2 seconds is 2000); this will be the time period between autoplay scrolling in secondary carousels', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-home-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-home-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('Set the speed for the slide (how quickly the next slide comes in)', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in milliseconds (ex: half a second is 500, 1 second is 1000).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-home-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('Enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-home-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed on a tablet screen per carousel row', 'dotstudio-pro'),
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
            'subtitle' => __('Defines the number of images to be displayed on a mobile screen per carousel row', 'dotstudio-pro'),
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
 * Home page Slick carousel Settings
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Homepage: Countinue Watch Carousels', 'dotstudio-pro'),
    'id' => 'countinue-carousels',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'opt-continue-watch-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose width and height for the carousel thumbnails', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-continue-watch-layout-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option.', 'dotstudio-pro'),
            'desc' => __('Tooltip will display everything in a tooltip, where "Text" and "Text & Description" will display whichever you select below the image', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text & Description',
                '2' => 'Tooltip',
                '3' => 'Text',
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-continue-watch-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words shown in category titles in the carousel', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-continue-watch-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words shown in category descriptions in the carousel', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'id' => 'opt-slick-continue-watch-slidetoshow',
            'type' => 'spinner',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of images to be displayed in a carousel row', 'dotstudio-pro'),
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-continue-watch-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('This defines the number of images to be scrolled when one of the arrow navigation buttons in the carousel is clicked', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-continue-watch-pagination',
            'title' => __('Enable Direct Navigation in Carousels', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation dots under the carousel ', 'dotstudio-pro'),
            'description' => __('By default, this is set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-continue-watch-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation arrows on the carousel', 'dotstudio-pro'),
            'description' => __('By default, this is set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-continue-watch-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-continue-watch-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('Enable autoplay for secondary carousels', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-continue-watch-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('Set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('Please enter the value in milliseconds (ex: 1 second is 1000, 2 seconds is 2000); this will be the time period between autoplay scrolling in secondary carousels', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-home-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-continue-watch-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('Set the speed for the slide (how quickly the next slide comes in)', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in milliseconds (ex: half a second is 500, 1 second is 1000).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-continue-watch-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('Enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-continue-watch-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed on a tablet screen per carousel row', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array('opt-slick-home-responsive', '=', true),
            'default' => '2',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
        array(
            'id' => 'opt-slick-continue-watch-mobile-slidetoshow',
            'title' => __('Slides to show in mobile device', 'dotstudio-pro'),
            'subtitle' => __('Defines the number of images to be displayed on a mobile screen per carousel row', 'dotstudio-pro'),
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
$options = array();
$args = array(
    'posts_per_page' => -1,
    'post_type' => 'channel-category',
);
$posts = new WP_Query($args);
$default_option = '';
if ($posts->have_posts()) {
    $default_option = $posts->posts[0]->post_name;
    foreach ($posts->posts as $post) {
        $options[$post->post_name] = stripslashes($post->post_title);
    }
}
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
            'subtitle' => __('Select the category banner type you would like to display on the categories page', 'dotstudio-pro'),
            'options' => array(
                'poster' => 'Poster',
                'wallpaper' => 'wallpaper',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-categories-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) for the Category Image', 'dotstudio-pro'),
            'subtitle' => __('Choose the width and height for the category thumbnails', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-categories-title',
            'title' => __('Display Category Title', 'dotstudio-pro'),
            'subtitle' => __('Display the title of the category with the thumbnail', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'title' => __('Categories per Row', 'dotstudio-pro'),
            'desc' => __('The number of categories to display per row on the categories page', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-display-categories-row',
            'options' => array_combine(array(12, 6, 4, 3, 2), array(1, 2, 3, 4, 6)),
            'default' => 4
        ),
        array(
            'id' => 'opt-cateogry-listing-option',
            'type' => 'button_set',
            'title' => __('Category Thumbnail Link Action', 'dotstudio-pro'),
            'subtitle' => __('Set where users are directed when clicking on a category thumbnail', 'dotstudio-pro'),
            'options' => array(
                'category-listing-page' => 'Category Listing Page',
                'ivp-page' => 'IVP Page'
            ),
            'default' => 'category-listing-page'
        ),
        array(
            'id' => 'opt-category-all',
            'title' => __('Categories to display', 'dotstudio-pro'),
            'subtitle' => __('This option is to display all or selected categories on category page', 'dotstudio-pro'),
            'type' => 'switch',
            'on' => 'All Categories',
            'off' => 'Selected Categories',
            'default' => true,
        ),
        array(
            'id' => 'opt-category-list',
            'type' => 'checkbox',
            'required' => array('opt-category-all', '=', false),
            'title' => __('Categories', 'redux-framework-demo'),
            'subtitle' => __('Select categories out of the following to display on the categories list page. <br/><br/> If you will not select any of categories from this list, it will display all categories', 'dotstudio-pro'),
            'options' => $options
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
            'id' => 'opt-category-poster-visible',
            'type' => 'switch',
            'title' => __('Show/Hide the Category Banner', 'dotstudio-pro'),
            'subtitle' => __('Display or hide the Category Banner.', 'dotstudio-pro'),
            'default' => 1,
            'on' => 'Show',
            'off' => 'Hide',
        ),
        array(
            'id' => 'opt-category-poster-type',
            'type' => 'radio',
            'title' => __('Select Category Banner Type', 'dotstudio-pro'),
            'subtitle' => __('The category banner type you would like to display on category page.', 'dotstudio-pro'),
            'options' => array(
                'poster' => 'Poster',
                'wallpaper' => 'wallpaper',
            ),
            'required' => array('opt-category-poster-visible', '=', 1),
            'default' => 'wallpaper'
        ),
        array(
            'id' => 'opt-category-poster-information',
            'title' => __('Display Category Poster Information', 'dotstudio-pro'),
            'subtitle' => __('Display the category information on the category poster(i.e. Ditle and description).', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-category-channel-poster-type',
            'type' => 'radio',
            'title' => __('Select Channel Poster Type', 'dotstudio-pro'),
            'subtitle' => __('The channel poster type you would like to display on category detail page', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'poster'
        ),
        array(
            'id' => 'opt-channel-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) for Channel Thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose the width and height for the channel thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-layout-channel-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout', 'dotstudio-pro'),
            'subtitle' => __('Content Layout', 'dotstudio-pro'),
            'desc' => __('Tooltip will display everything in a tooltip, where "Text" and "Text & Description" will display whichever you select below the image', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text & Description',
                '2' => 'Tooltip',
                '3' => 'Text'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-channel-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim Carousel Titles', 'dotstudio-pro'),
            'desc' => __('Set the number of words displayed for the channel title.', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-channel-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim Carousel Description', 'dotstudio-pro'),
            'desc' => __('Here you can set number of words to be visible on channel description.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'title' => __('Categories per Row', 'dotstudio-pro'),
            'desc' => __('The number of categories to display per row', 'dotstudio-pro'),
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
            'title' => __('Select Channel Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the type of channel banner you would like to display', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-channel-video-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) for Video Thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose the width and height for the video thumbnails.', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'id' => 'opt-channel-poster-logo-title',
            'type' => 'radio',
            'title' => __('Display Logo OR Title', 'dotstudio-pro'),
            'subtitle' => __('Choose the option to display logo or title on channel page overlay.', 'dotstudio-pro'),
            'options' => array(
                'logo' => 'Channel Logo',
                'title' => 'Channel Title',
            ),
            'default' => 'logo'
        ),
        array(
            'id' => 'opt-channel-video-layout-slider-content',
            'type' => 'button_set',
            'title' => __('Content Layout for videos', 'dotstudio-pro'),
            'subtitle' => __('Choose the content layout option.', 'dotstudio-pro'),
            'desc' => __('Tooltip will display everything in a tooltip, where "Text" and "Text & Description" will display whichever you select below the image', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text & Description',
                '2' => 'Tooltip',
                '3' => 'Text'
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-channel-video-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on video carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words displayed for the title of the video carousel', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-channel-video-description-trim-word',
            'type' => 'spinner',
            'title' => __('Trim description on video carousel', 'dotstudio-pro'),
            'desc' => __('Set the number of words shown for the video description on the carousel', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
        ),
        array(
            'id' => 'opt-slick-video-slidetoshow',
            'type' => 'spinner',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed in a carousel row', 'dotstudio-pro'),
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-video-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be scrolled when an arrow navigation is clicked', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
        ),
        array(
            'id' => 'opt-slick-video-pagination',
            'title' => __('Enable Direct Navigation in Carousels', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation dots under the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-video-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation arrows', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('Enable autoplay for the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-video-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('Set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('Please enter the value in milliseconds (ex: 1 second is 1000, 2 seconds is 2000); this will be the time period between autoplay scrolling in secondary carousels', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-video-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-video-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('Set the speed for the slide (how quickly the next slide comes in)', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in milliseconds (ex: half a second is 500, 1 second is 1000).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-video-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('Enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-video-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed on a tablet screen per carousel row', 'dotstudio-pro'),
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
            'subtitle' => __('Defines the number of images to be displayed on a mobile screen per carousel row', 'dotstudio-pro'),
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
            'title' => __('Display Recommendation Content', 'dotstudio-pro'),
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
            'title' => __('Recommendation Type', 'dotstudio-pro'),
            'subtitle' => __('The type of recommendation content to display', 'dotstudio-pro'),
            'desc' => __('Select "Channel" option to show related channels carousel under content, or select "Video" to display related videos.', 'dotstudio-pro'),
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
            'title' => __('Select Channel Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the channel banner type to display', 'dotstudio-pro'),
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
            'title' => __('Dimensions (Width/Height) for Recommendation Content carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose width and height for the thumbnails', 'dotstudio-pro'),
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
            'desc' => __('Tooltip will display everything in a tooltip, where "Text" and "Text & Description" will display whichever you select below the image', 'dotstudio-pro'),
            'options' => array(
                '0' => 'None',
                '1' => 'Text & Description',
                '2' => 'Tooltip',
                '3' => 'Text'
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
            'desc' => __('Set number of words to be shown for related content descriptions in the carousel.', 'dotstudio-pro'),
            'default' => '10',
            'min' => '0',
            'step' => '1',
            'max' => '50',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-slidetoshow',
            'type' => 'spinner',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed in a carousel row', 'dotstudio-pro'),
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be scrolled', 'dotstudio-pro'),
            'type' => 'spinner',
            'default' => '4',
            'min' => '1',
            'step' => '1',
            'max' => '7',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-pagination',
            'title' => __('Enable Direct Navigation in Carousels', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation dots under the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the navigation arrows', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('Enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('Enable autoplay for the carousel', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('Set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('Please enter the value in milliseconds (ex: 1 second is 1000, 2 seconds is 2000); this will be the time period between autoplay scrolling', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array(array('opt-related-section', '=', 1), array('opt-slick-related-autoplay', '=', true)),
        ),
        array(
            'id' => 'opt-slick-related-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('Set the speed for the slide (how quickly the next slide comes in)', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in milliseconds (ex: half a second is 500, 1 second is 1000).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable responsive display mode', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
            'required' => array('opt-related-section', '=', 1),
        ),
        array(
            'id' => 'opt-slick-related-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('The number of images to be displayed on a tablet screen per carousel row', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array(array('opt-related-section', '=', 1), array('opt-slick-related-responsive', '=', true)),
            'default' => '2',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
        array(
            'id' => 'opt-slick-related-mobile-slidetoshow',
            'title' => __('Slides to show in mobile device', 'dotstudio-pro'),
            'subtitle' => __('Defines the number of images to be displayed on a mobile screen per carousel row', 'dotstudio-pro'),
            'type' => 'spinner',
            'required' => array(array('opt-related-section', '=', 1), array('opt-slick-related-responsive', '=', true)),
            'default' => '1',
            'min' => '1',
            'step' => '1',
            'max' => '5',
        ),
    ),
));

/**
 * Search results options
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Search Results Page ', 'dotstudio-pro'),
    'id' => 'search-results',
    'icon' => 'el el-search',
    'fields' => array(
        array(
            'id' => 'opt-search-option',
            'type' => 'button_set',
            'title' => __('Search Content Type', 'dotstudio-pro'),
            'subtitle' => __('Set up your search to search for either Channels or Videos', 'dotstudio-pro'),
            'options' => array(
                'channel' => 'Channel',
                'video' => 'Video'
            ),
            'default' => 'channel'
        ),
        array(
            'id' => 'opt-search-channel-poster-type',
            'type' => 'radio',
            'title' => __('Select Channel Banner Type', 'dotstudio-pro'),
            'subtitle' => __('The channel banner type you would like to display', 'dotstudio-pro'),
            'options' => array(
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'required' => array('opt-search-option', '=', 'channel'),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-search-autocomplete-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) for Search Autocomplete Result Thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose width and height for the search autocomplete result thumbnails', 'dotstudio-pro'),
            'default' => array(
                'width' => 265,
                'height' => 149,
            )
        ),
        array(
            'id' => 'opt-search-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) for Search Result Thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Choose width and height for the search result thumbnails', 'dotstudio-pro'),
            'default' => array(
                'width' => 320,
                'height' => 180,
            )
        ),
        array(
            'title' => __('Number of results to display per row', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-search-columns-row',
            'options' => array_combine(array(12, 6, 4, 3, 2), array(1, 2, 3, 4, 6)),
            'default' => 4
        ),
        array(
            'id' => 'opt-search-page-size',
            'type' => 'spinner',
            'title' => __('Number of results to display per page', 'dotstudio-pro'),
            'default' => '6',
            'min' => '0',
            'step' => '1',
            'max' => '30',
        ),
        array(
            'id' => 'opt-search-title-trim-word',
            'type' => 'spinner',
            'title' => __('Trim title on search results page', 'dotstudio-pro'),
            'desc' => __('Set the number of words to be shown for search results titles', 'dotstudio-pro'),
            'default' => '5',
            'min' => '0',
            'step' => '1',
            'max' => '30',
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
            'desc' => __('Select a menu to display in the footer section', 'dotstudio-pro'),
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
            'title' => __('Show Social Icons', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-social-links-target',
            'type' => 'switch',
            'title' => __('Social icon links: Open in new Tab?', 'dotstudio-pro'),
            'default' => 1,
            'required' => array('opt-social-icons', '=', '1'),
            'on' => 'Enable',
            'off' => 'Disable',
        ),
        array(
            'id' => 'section-start',
            'type' => 'section',
            'title' => __('Social icons', 'dotstudio-pro'),
            'subtitle' => __('Set the social media URLs', 'dotstudio-pro'),
            'required' => array('opt-social-icons', '=', '1'),
            'indent' => true
        ),
        array(
            'id' => 'facebook-link',
            'type' => 'text',
            'title' => __('Facebook Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Facebook Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'twitter-link',
            'type' => 'text',
            'title' => __('Twitter URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Twitter Account', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'dribbble-link',
            'type' => 'text',
            'title' => __('Dribbble Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Dribbble Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'flickr-link',
            'type' => 'text',
            'title' => __('Flickr Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Flickr Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'github-link',
            'type' => 'text',
            'title' => __('Github Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Github Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'pinterest-link',
            'type' => 'text',
            'title' => __('Pinterest Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Pinterest Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'youtube-link',
            'type' => 'text',
            'title' => __('Youtube Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Youtube Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'google-plus-link',
            'type' => 'text',
            'title' => __('Google+ Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Google+ Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'linkedin-link',
            'type' => 'text',
            'title' => __('LinkedIn Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your LinkedIn Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'instagram-link',
            'type' => 'text',
            'title' => __('Instagram Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Instagram Page', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'vimeo-link',
            'type' => 'text',
            'title' => __('Vimeo Page URL', 'dotstudio-pro'),
            'subtitle' => __('The URL of your Vimeo Page', 'dotstudio-pro'),
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
