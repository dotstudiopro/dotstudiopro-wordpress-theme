<?php

// Save the various admin options so we can use them within the plugin
function ds_save_admin_options()
{

    if (isset($_POST['ds-save-admin-options'])) {

        ds_api_key_change();

        update_option('ds_fb_app_id', sanitize_text_field($_POST['ds_fb_app_id']));

        update_option('ds_twitter_handle', sanitize_text_field($_POST['ds_twitter_handle']));

        update_option('ds_player_slider_color', sanitize_text_field($_POST['ds_player_slider_color']));

        update_option('ds_plugin_style', sanitize_text_field($_POST['ds_plugin_style']));

        update_option('ds_light_theme_shadow', sanitize_text_field($_POST['ds_light_theme_shadow']));

        update_option('ds_channel_template', sanitize_text_field($_POST['ds_channel_template']));

        update_option('ds_development_check', sanitize_text_field($_POST['ds_development_check']));

        update_option('ds_development_country', sanitize_text_field($_POST['ds_development_country']));

        update_option('ds_plugin_custom_css', sanitize_text_field($_POST['ds_plugin_custom_css']));

        update_option('ds_comment_type', sanitize_text_field($_POST['ds_comment_type']));

        update_option('ds_player_mute', sanitize_text_field($_POST['ds_player_mute']));

        update_option('ds_token_reset', sanitize_text_field($_POST['ds_token_reset']));

        update_option('ds_auto_assign_menu', sanitize_text_field($_POST['ds_auto_assign_menu']));

        update_option('ds_player_autoplay', sanitize_text_field($_POST['ds_player_autoplay']));

        update_option('ds_player_autoredir', sanitize_text_field($_POST['ds_player_autoredir']));

        update_option('ds_player_minivid', sanitize_text_field($_POST['ds_player_minivid']));

        update_option('ds_player_recplaylist', sanitize_text_field($_POST['ds_player_recplaylist']));

        update_option('ds_fancy_load', sanitize_text_field($_POST['ds_fancy_load']));

        update_option('ds_show_playlist_above_meta', sanitize_text_field($_POST['ds_show_playlist_above_meta']));

    }

}