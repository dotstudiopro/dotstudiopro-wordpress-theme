<?php

/**
 * All of the actions and filters used within the plugin.
 *
 */

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['flush']) && $_GET['flush'] == 1) {
    add_action('init', 'dsppremium_site_flush');
}

if (isset($_GET['page']) && $_GET['page'] == 'dot-studioz-options' && isset($_GET['templatecopy']) && $_GET['templatecopy'] == 1) {
    add_action('init', 'dsppremium_template_copy');
}

add_filter('query_vars', 'dsppremium_video_var');
add_filter('page_template', 'dsppremium_get_channel_template', 11);
add_filter('page_template', 'dsppremium_get_category_template', 11);
add_filter('page_template', 'dsppremium_all_categories_template', 11);
add_action('admin_notices', 'dsppremium_no_country');
add_action('admin_notices', 'dsppremium_is_front_page_channel');
add_action('wp_enqueue_scripts', 'dsppremium_scripts_load_cdn');
add_action('wp_enqueue_scripts', 'dsppremium_styles');
add_action('admin_notices', 'dsppremium_check_api_key_set');
add_action('init', 'dsppremium_get_country');
add_action('wp', 'dsppremium_iframe_replace');
add_action('init', 'dsppremium_create_channel_category_menu');
add_action('wp_head', 'dsppremium_add_custom_css', 999);
add_action('post_edit_form_tag', 'add_post_enctype');
add_action('admin_init', 'dsppremium_category_images_init');
add_action('save_post', 'dsppremium_save_category_image_field');
add_action('admin_init', 'dsppremium_set_front_page_to_categories');
add_action('wp', 'dsppremium_redirect_all_channels');
add_action( 'admin_enqueue_scripts', 'dsppremium_admin_styles_scripts' );