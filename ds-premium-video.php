<?php

/*
Plugin Name: dotStudioPRO Premium Video
Plugin URI: http://wordpress.dotstudiopro.com/
Description: dotstudioPRO is a video monetization CMS used to manage, deploy, and monetize streaming video on devices like Apple TV, Roku, Mobile, Facebook and browsers. This plugin extends dotstudioPRO functionality into Wordpress turning it into a Netflix or Hulu style website. Documentation: http://docs.wordpress.dotstudiopro.com/dspdev-premium-video/
Version: 2.04
Author: Scott Lonis, Matt Armstrong, DotStudioz
Text Domain: dotstudiopro-wordpress
Author URI: http://www.dotstudiopro.com
License: GPLv2 or later
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016-2017 dotStudioPRO
*/

require_once ABSPATH . 'wp-admin/includes/plugin.php';

if (!class_exists('DotStudioz_Commands')) {

    require_once "class.curl_commands.php";

}

require_once "functions.php";
// Plugin Update Checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-premium-video',
    __FILE__,
    'dspdev-premium-video'
);

$uri = $_SERVER['REQUEST_URI'];

if (strpos($uri, 'wp-admin') === false) {

    add_action('init', 'dsppremium_check');

}

add_action('wp_enqueue_scripts', 'dsppremium_plugin_style');
add_action( 'wp_enqueue_scripts', 'dsppremium_owl_carousel' );

/** Add Menu Entry **/
function dot_studioz_menu()
{

    add_menu_page('dotstudioPRO Options', 'dotstudioPRO Options', 'manage_options', 'dot-studioz-options', 'dot_studioz_menu_page', plugins_url( 'images/dsp.png', __FILE__ ));

}

add_action('admin_menu', 'dot_studioz_menu');

// Set up the page for the plugin, pulling the content based on various $_GET global variable contents
function dot_studioz_menu_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    echo "<div class='wrap'>";

    include "menu.tpl.php";

    echo "</div>";

}
/** End Menu Entry **/

/** Save Admin Menu Options **/

add_action("init", "dsppremium_save_admin_options");

/** End Save Admin Menu Options **/
