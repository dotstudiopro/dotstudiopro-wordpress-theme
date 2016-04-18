<?php

function ds_all_categories_template($single_template) {
			
	global $post;
	 

     if ($post->post_name == 'channel-categories') {
		 
	
		// Set the template... 
			if(!locate_template( 'ds-all-categories.tpl.php' )){
			
			// If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
			$single_template = dirname( __FILE__ ) . '/templates/ds-all-categories.tpl.php';
			
		} else {

			$single_template = get_stylesheet_directory() . '/ds-all-categories.tpl.php';
			
		} 
		
     }
	 
	 // Return either the template we made, or the template in the theme folders.
	 
     return $single_template;
} 

add_filter( 'page_template', 'ds_all_categories_template', 11 );

function ds_get_category_template($single_template) {
			
	global $post;
	 
	$category_check_grab = get_page_by_path('channel-categories');
	
	$category_parent = $category_check_grab->ID;

     if ($post->post_parent == $category_parent) {
		 
	
		// Set the template... 
			if(!locate_template( 'ds-single-category.tpl.php' )){
			
			// If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
			$single_template = dirname( __FILE__ ) . '/templates/ds-single-category.tpl.php';
			
		} else {

			$single_template = get_stylesheet_directory() . '/ds-single-category.tpl.php';
			
		} 
		
     }
	 
	 // Return either the template we made, or the template in the theme folders.
	 
     return $single_template;
}

add_filter( 'page_template', 'ds_get_category_template', 11 );

function ds_get_channel_template($single_template) {
			
	global $post;
	 
	$channel_check_grab = get_page_by_path('channels');
	
	$channel_parent = $channel_check_grab->ID;
	
	$channel_grandparent = wp_get_post_parent_id( $post->post_parent );
	
     if ($post->post_parent == $channel_parent || $channel_grandparent == $channel_parent) {
	
			$template_option = get_option('ds_channel_template');
	
			// Set the template... 
			if(!locate_template( $template_option . '.tpl.php ')){
			
			// If we can't locate a file named ds-single-category.php that should be the template file, we use our own template...
						
			if(!$template_option || $template_option == 'default'){
				
				$template_option = "ds-single-channel";
				
			}
				
			$single_template = dirname( __FILE__ ) . '/templates/single_channel_templates/' . $template_option . '.tpl.php';
					
		} else {

			$single_template = get_stylesheet_directory() . '/' . $template_option . '.tpl.php ';
			
		} 
		
     }
	 
	 // Return either the template we made, or the template in the theme folders.
	 
     return $single_template;
}

add_filter( 'page_template', 'ds_get_channel_template', 11 );
