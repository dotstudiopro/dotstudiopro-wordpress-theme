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
    'async_typography' => true,
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
            'desc' => __('Choose the layout option.', 'dotstudio-pro'),
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
            'subtitle' => __('Favicon for your website at 16px x 16px or 32px x 32px.', 'dotstudio-pro'),
            'default' => ''
        ),
        array(
            'id' => 'opt-back-to-top',
            'type' => 'switch',
            'title' => __('Back To Top Button ', 'dotstudio-pro'),
            'subtitle' => __('Turn On/OFF Back To Top', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'opt-google-analytics',
            'type' => 'textarea',
            'title' => __('Google analytics code', 'dotstudio-pro'),
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
    'post_type' => 'category',
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
                'right' => 'Right'
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
            'id' => 'opt-search',
            'type' => 'switch',
            'title' => __('Enable/disable search bar', 'dotstudio-pro'),
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
            'title' => 'Menu title',
            'required' => array('opt-category-menu', '=', '1'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Menu title Display on Header', 'dotstudio-pro'),
            'default' => 'categories'
        ),
        array(
            'id' => 'opt-menu-link',
            'type' => 'text',
            'title' => __('Menu URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Menu URL', 'dotstudio-pro'),
            'required' => array('opt-category-menu', '=', '1'),
            'default' => '#'
        ),
        array(
            'title' => __('Menu Position', 'dotstudio-pro'),
            'desc' => __('Choose the postion to display menu on home-page.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-menu-position',
            'required' => array('opt-category-menu', '=', '1'),
            'options' => range(1, 10),
            'default' => 0
        ),
        array(
            'id' => 'opt-menu-sorter',
            'type' => 'sorter',
            'title' => 'Header category menu order',
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
            'default' => '#428bca',
            'validate' => 'color',
            'output' => array('header'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-header',
            'type' => 'color',
            'title' => __('Site Header Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the header text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('header'),
        ),
        array(
            'id' => 'opt-background',
            'type' => 'background',
            'output' => array('body'),
            'title' => __('Body Background', 'redux-framework-demo'),
            'subtitle' => __('Body background with image, color, etc.', 'redux-framework-demo'),
        ),
        array(
            'id' => 'opt-color-body',
            'type' => 'color',
            'title' => __('Site Body Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the body text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('body'),
        ),
        array(
            'id' => 'opt-bg-color-footer',
            'type' => 'color',
            'title' => __('Footer Background Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the footer (default: #dd9933).', 'dotstudio-pro'),
            'default' => '#dd9933',
            'validate' => 'color',
            'output' => array('footer'),
            'mode' => 'background',
        ),
        array(
            'id' => 'opt-color-footer',
            'type' => 'color',
            'title' => __('Site Footer Text Color', 'dotstudio-pro'),
            'subtitle' => __('Pick a background color for the footer text (default: #000000).', 'dotstudio-pro'),
            'default' => '#000000',
            'validate' => 'color',
            'output' => array('footer'),
        ),
    )
));

$options = array();
$args = array(
    'post_per_page' => -1,
    'post_type' => 'category',
    'meta_query' => array(
        array(
            'key' => 'is_on_cat_homepage',
            'value' => 1
        )
    )
);
$posts = new WP_Query($args);
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
    'title' => __('Homepage Settings', 'dotstudio-pro'),
    'id' => 'homepage',
    'icon' => 'el el-home',
    'fields' => array(
        array(
            'title' => __('Select Category', 'dotstudio-pro'),
            'desc' => __('Choose the category to be used for homepage carousel.', 'dotstudio-pro'),
            'type' => 'select',
            'id' => 'opt-home-carousel',
            'options' => $options,
            'default' => $default_option
        ),
        array(
            'id' => 'opt-poster-type',
            'type' => 'radio',
            'title' => __('Select Channal Banner Type', 'dotstudio-pro'),
            'subtitle' => __('Select the channel banner type which you would like to display on all the carousel', 'dotstudio-pro'),
            'options' => array(
                '1' => 'Spotlight Poster',
                '2' => 'Poster',
            ),
            'default' => '1'
        ),
        array(
            'id' => 'opt-play-btn-type',
            'type' => 'radio',
            'title' => __('Select Navigate Button Type', 'dotstudio-pro'),
            'subtitle' => __('Select the navigate button type which you would like to display on the main carousel. i.e: <br/> 1). Play Video Button(it will bnavigate the user to play the first video of the channel) <b>OR</b><br/> 2). Watch Now Button(It will navigate the user to the channel\'s list page)', 'dotstudio-pro'),
            'options' => array(
                '1' => 'Play Video Button',
                '2' => 'Watch Now Button',
            ),
            'default' => '2'
        ),
    ),
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
            'all_styles' => false,
            'title' => __('Body Font', 'dotstudio-pro'),
            'subtitle' => __('Specify the body font properties.', 'dotstudio-pro'),
            'google' => true,
            'default' => array(
                'color' => '#dd9933',
                'font-size' => '30px',
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
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h1'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
        array(
            'id' => 'opt-typography-h2',
            'type' => 'typography',
            'title' => __('Typography h2', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h2'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
        array(
            'id' => 'opt-typography-h3',
            'type' => 'typography',
            'title' => __('Typography h3', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h3'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
        array(
            'id' => 'opt-typography-h4',
            'type' => 'typography',
            'title' => __('Typography h4', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h4'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
        array(
            'id' => 'opt-typography-h5',
            'type' => 'typography',
            'title' => __('Typography h5', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h5'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
        array(
            'id' => 'opt-typography-h6',
            'type' => 'typography',
            'title' => __('Typography h6', 'dotstudio-pro'),
            'font-backup' => true,
            'all_styles' => true,
            // Enable all Google Font style/weight variations to be added to the page
            'output' => array('h6'),
            // An array of CSS selectors to apply this font style to dynamically
            'compiler' => array('site-description-compiler'),
            // An array of CSS selectors to apply this font style to dynamically
            'units' => 'px',
            // Defaults to px
            'subtitle' => __('Typography option with each property can be called individually.', 'dotstudio-pro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '700',
                'font-family' => 'Abel',
                'google' => true,
                'font-size' => '33px',
                'line-height' => '40px'
            ),
        ),
    )
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
            'desc' => __('Select a menu to display in footer section.', 'dotstudio-pro'),
        ),
        array(
            'id' => 'opt-copyright-text',
            'type' => 'text',
            'title' => __('Copyright Text', 'dotstudio-pro'),
            'default' => '',
        ),
        array(
            'id' => 'section-start',
            'type' => 'section',
            'title' => __('Social icons', 'dotstudio-pro'),
            'subtitle' => __('Controls the social media pages URLs.', 'dotstudio-pro'),
            'indent' => true
        ),
        array(
            'id' => 'facebook-link',
            'type' => 'text',
            'title' => __('Facebook Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Facebook Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'twitter-link',
            'type' => 'text',
            'title' => __('Twitter Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Twitter Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'dribbble-link',
            'type' => 'text',
            'title' => __('Dribbble Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Dribbble Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'flickr-link',
            'type' => 'text',
            'title' => __('Flickr Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Flickr Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'github-link',
            'type' => 'text',
            'title' => __('Github Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Github Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'pinterest-link',
            'type' => 'text',
            'title' => __('Pinterest Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Pinterest Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'youtube-link',
            'type' => 'text',
            'title' => __('Youtube Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Youtube Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'google-plus-link',
            'type' => 'text',
            'title' => __('Google+ Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Google+ Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'linkedin-link',
            'type' => 'text',
            'title' => __('LinkedIn Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('LinkedIn Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'instagram-link',
            'type' => 'text',
            'title' => __('Instagram Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
            'desc' => __('Instagram Page URL', 'dotstudio-pro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'vimeo-link',
            'type' => 'text',
            'title' => __('Vimeo Page URL', 'dotstudio-pro'),
            'subtitle' => __('This must be an URL.', 'dotstudio-pro'),
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

        $filename = dirname(__FILE__) . '/style.css';

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