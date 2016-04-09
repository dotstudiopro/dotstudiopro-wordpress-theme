<?php

/*
** Plugin Name: dotstudioPRO Premium Video
** Version: 1.0
** Author: dotstudioPRO
** Author URI: #
*/

require_once("class.curl_commands.php");

require_once("functions.php");

require_once("ds-templates.php");


$uri = $_SERVER['REQUEST_URI'];

if(strpos($uri, 'wp-admin') === FALSE){

	add_action('init', 'ds_check');

} 

add_action( 'wp_enqueue_scripts', 'ds_plugin_style' );


/** Add Menu Entry **/
function dot_studioz_menu() {
	
	add_menu_page( 'dotstudioPRO Options', 'dotstudioPRO Options', 'manage_options', 'dot-studioz-options', 'dot_studioz_menu_page', 'dashicons-video-alt' );
	
}

add_action( 'admin_menu', 'dot_studioz_menu' );

// Set up the page for the plugin, pulling the content based on various $_GET global variable contents
function dot_studioz_menu_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	echo "<div class='wrap'>";
	
			
	include("menu.tpl.php");	
	
	
	echo "</div>";
	
}
/** End Menu Entry **/

/** Save Admin Menu Options **/

add_action("init", "ds_save_admin_options");

/** End Save Admin Menu Options **/
