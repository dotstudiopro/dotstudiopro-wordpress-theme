<?php

/**
 * Admin-related functions; currently only the option save
 *
 */

 /**
  * Save the various admin options so we can use them within the plugin
  *
  * @return void
  */
function dsppremium_save_admin_options()
{

    if (isset($_POST['ds-save-admin-options'])) {

        dsppremium_api_key_change();

        update_option('ds_fb_app_id', dsppremium_verify_post_var('ds_fb_app_id'));

        update_option('ds_twitter_handle', dsppremium_verify_post_var('ds_twitter_handle'));

        update_option('dspremium_player_slider_color', dsppremium_verify_post_var('dspremium_player_slider_color'));

        update_option('ds_plugin_style', dsppremium_verify_post_var('ds_plugin_style'));

        update_option('ds_channel_template', dsppremium_verify_post_var('ds_channel_template'));

        update_option('ds_development_check', dsppremium_verify_post_var('ds_development_check'));

        update_option('ds_development_country', dsppremium_verify_post_var('ds_development_country'));

        update_option('ds_plugin_custom_css', dsppremium_verify_post_var('ds_plugin_custom_css'));

        update_option('ds_comment_type', dsppremium_verify_post_var('ds_comment_type'));

        update_option('ds_player_mute', dsppremium_verify_post_var('ds_player_mute'));

        update_option('ds_token_reset', dsppremium_verify_post_var('ds_token_reset'));

        update_option('ds_auto_assign_menu', dsppremium_verify_post_var('ds_auto_assign_menu'));

        update_option('ds_player_autoplay', dsppremium_verify_post_var('ds_player_autoplay'));

        update_option('ds_player_autoredir', dsppremium_verify_post_var('ds_player_autoredir'));

        update_option('ds_player_minivid', dsppremium_verify_post_var('ds_player_minivid'));

        update_option('ds_player_recplaylist', dsppremium_verify_post_var('ds_player_recplaylist'));

        update_option('ds_fancy_load', dsppremium_verify_post_var('ds_fancy_load'));

        update_option('ds_show_playlist_above_meta', dsppremium_verify_post_var('ds_show_playlist_above_meta'));

    }

}
