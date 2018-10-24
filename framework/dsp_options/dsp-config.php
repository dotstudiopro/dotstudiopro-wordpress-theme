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
            'id' => 'opt-logo-height',
            'type' => 'dimensions',
            'title' => __('Dimensions (Height) Option for the Home page logo', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose height for the logo.', 'dotstudio-pro'),
            'desc' => __('You can enable or disable any piece of this field. Height, or Units.', 'dotstudio-pro'),
            'output' => array('.site-logo img'),
            'width' => false,
            'default' => array(
                'height' => 50,
            )
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
            'options' => array_combine(range(1, 10), range(1, 10)),
            'default' => 1
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
                'spotlight_poster' => 'Spotlight Poster',
                'poster' => 'Poster',
            ),
            'default' => 'spotlight_poster'
        ),
        array(
            'id' => 'opt-play-btn-type',
            'type' => 'radio',
            'title' => __('Select Navigate Button Type', 'dotstudio-pro'),
            'subtitle' => __('Select the navigate button type which you would like to display on the main carousel. i.e: <br/> 1). Play Video Button(it will bnavigate the user to play the first video of the channel) <b>OR</b><br/> 2). Watch Now Button(It will navigate the user to the channel\'s list page)', 'dotstudio-pro'),
            'options' => array(
                'play_video' => 'Play Video Button',
                'watch_now' => 'Watch Now Button',
            ),
            'default' => 'watch_now'
        ),
    ),
));

/**
 * Slick carousel Settings
 * @since 1.0.0
 */
Redux::setSection($opt_name, array(
    'title' => __('Slick Carousel Settings', 'dotstudio-pro'),
    'id' => 'carousels',
    'icon' => 'el el-th',
    'fields' => array(
        array(
            'id' => 'opt-image-dimensions',
            'type' => 'dimensions',
            'title' => __('Dimensions (Width/Height) Option for the carousel thumbnails', 'dotstudio-pro'),
            'subtitle' => __('Allow your users to choose width, height for the thumbnails.', 'dotstudio-pro'),
            'desc' => __('You can enable or disable any piece of this field. Width, Height, or Units.', 'dotstudio-pro'),
            'default' => array(
                'width' => 200,
                'height' => 100,
            )
        ),
        array(
            'id' => 'opt-title-trim-word',
            'title' => __('Trim title on carousel', 'dotstudio-pro'),
            'subtitle' => __('Here you can set number of words to be visible on carousel for category title.', 'dotstudio-pro'),
            'description' => __("By default set to  '5'. Set value 0 if you don't need to trim words.", 'dotstudio-pro'),
            'type' => 'text',
            'default' => '5',
            'validate' => 'numeric'
        ),
        array(
            'id' => 'opt-description-trim-word',
            'title' => __('Trim description on carousel', 'dotstudio-pro'),
            'subtitle' => __('Here you can set number of words to be visible on carousel for category description.', 'dotstudio-pro'),
            'description' => __("By default set to  '10'. Set value 0 if you don't need to trim words.", 'dotstudio-pro'),
            'type' => 'text',
            'default' => '10',
            'validate' => 'numeric'
        ),
        array(
            'id' => 'opt-slick-slidetoshow',
            'title' => __('Slides to show', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be display in a carousel row', 'dotstudio-pro'),
            'description' => __('By default set to "3" ie all images. eg. if you want to display only 5 images then select option "5"', 'dotstudio-pro'),
            'type' => 'select',
            'options' => array_combine(range(1, 5), range(1, 5)),
            'default' => 4
        ),
        array(
            'id' => 'opt-slick-slidetoscroll',
            'title' => __('Slides to scroll', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be scrolled', 'dotstudio-pro'),
            'description' => __('By default set to "1".', 'dotstudio-pro'),
            'type' => 'select',
            'options' => array_combine(range(1, 5), range(1, 5)),
            'default' => 4
        ),
        array(
            'id' => 'opt-slick-pagination',
            'title' => __('Enable Pagination', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the dots navigation under the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-navigation',
            'title' => __('Enable Navigation', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the navigation arrows', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-infinite',
            'title' => __('Enable infinite loop', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable/disable the infinite loop for the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "on".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-autoplay',
            'title' => __('Autoplay', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable autoplay to the carousel', 'dotstudio-pro'),
            'description' => __('By default set to "off".', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => false,
        ),
        array(
            'id' => 'opt-slick-autoplayspeed',
            'title' => __('Autoplay Interval', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the interval for the autoplay sliding', 'dotstudio-pro'),
            'description' => __('By default set to  "2000". Please enter the value in miliseconds (ie. in multiples of 100).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '2000',
            'required' => array('opt-slick-autoplay', '=', true),
        ),
        array(
            'id' => 'opt-slick-slidespeed',
            'title' => __('Slide Speed', 'dotstudio-pro'),
            'subtitle' => __('This option is to set the speed for the slide', 'dotstudio-pro'),
            'description' => __('By default set to  "500". Please enter the value in miliseconds (ie. in multiples of 100).', 'dotstudio-pro'),
            'type' => 'text',
            'default' => '500',
        ),
        array(
            'id' => 'opt-slick-responsive',
            'title' => __('Responsive Display', 'dotstudio-pro'),
            'subtitle' => __('This option is to enable responsive display mode', 'dotstudio-pro'),
            'description' => __('By default set "on"', 'dotstudio-pro'),
            'type' => 'switch',
            'default' => true,
        ),
        array(
            'id' => 'opt-slick-tablet-slidetoshow',
            'title' => __('Slides to show in tablet device (portrait mode)', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be displayed in tablet screen', 'dotstudio-pro'),
            'description' => __('By default set to "2".', 'dotstudio-pro'),
            'type' => 'select',
            'options' => array_combine(range(1, 5), range(1, 5)),
            'default' => 2,
            'required' => array('opt-slick-responsive', '=', true),
        ),
        array(
            'id' => 'opt-slick-mobile-slidetoshow',
            'title' => __('Slides to show in mobile device', 'dotstudio-pro'),
            'subtitle' => __('This define the number of images to be displayed in mobile screen', 'dotstudio-pro'),
            'description' => __('By default set to "1".', 'dotstudio-pro'),
            'type' => 'select',
            'options' => array_combine(range(1, 5), range(1, 5)),
            'default' => 1,
            'required' => array('opt-slick-responsive', '=', true),
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
                'font-size' => '12px',
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '32px',
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '24px',
                'line-height' => '30px'
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '19px',
                'line-height' => '22px'
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '16px',
                'line-height' => '22px'
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '14px',
                'line-height' => '20px'
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
                'font-family' => 'Arial,Helvetica,sans-serif',
                'google' => true,
                'font-size' => '13px',
                'line-height' => '20px'
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
            'id' => 'opt-social-icons',
            'type' => 'switch',
            'title' => __('Show social icons', 'dotstudio-pro'),
            'default' => 0,
            'on' => 'On',
            'off' => 'Off',
        ),
        array(
            'id' => 'section-start',
            'type' => 'section',
            'title' => __('Social icons', 'dotstudio-pro'),
            'subtitle' => __('Controls the social media pages URLs.', 'dotstudio-pro'),
            'required' => array('opt-social-icons', '=', '1'),
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