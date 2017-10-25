<?php

// Determine if the channel we are on is a parent channel
function ds_channel_is_parent()
{
    global $wpdb, $post;
    $channel_check_grab = get_page_by_path('channels');
    $channel_check      = $channel_check_grab->ID;
    if ($post->post_parent != $channel_check) {
        return false;
    }
    $results = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_type != 'revision' AND post_parent = " . $post->ID);
    if (count($results) > 0) {
        return true;
    } else {
        return false;
    }
}

// Determine if the channel we are on is a child channel
function ds_channel_is_child()
{
    global $post;
    if ($post->post_parent == 0) {
        return false;
    }
    $channel_check_grab  = get_page_by_path('channels');
    $channel_parent      = $channel_check_grab->ID;
    $channel_grandparent = wp_get_post_parent_id($post->post_parent);
    if ($channel_parent == $channel_grandparent) {
        return true;
    } else {
        return false;
    }
}

// Completely reprocess/recreate all channel pages; this is done when the admin requests a flush
function channels_check()
{
	// This process can take a moment, so we make sure we have time
	set_time_limit(240);

    global $wpdb;

    $channels = list_channels();

    $channel_check = get_page_by_path('channels');

    // Check if we have a main channel page; if not, create one
    if (!$channel_check) {

        $channel_check_page_id = wp_insert_post(array(
            'post_title'   => "All Channels",
            'post_type'    => 'page',
            'post_name'    => "channels",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channels',
        ));

    } else {

        $channel_check_page_id = $channel_check->ID;

    }

    foreach ($channels as $c) {

        if (!isset($c->categories[0]->slug)) {
            continue;
        }

        $slug = rtrim(ltrim($c->slug, '-'), '-');

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $slug . "' AND post_parent = $channel_check_page_id");

        if (count($check) > 0) {
            continue;
        }

        $channel_info = !empty($c->description) ? $c->description : !empty($c->video->description) ? $c->video->description : $c->title;

        if (empty($channel_info)) {
            $channel_info = "No description.";
        }

        $page_id = wp_insert_post(array(
            'post_title'   => !empty($c->title) ? $c->title : ucwords(str_replace('-', ' ', $slug)),
            'post_type'    => 'page',
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_excerpt' => $channel_info,
            'post_parent'  => $channel_check_page_id,
        ));

        update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

        if (!empty($c->spotlight_poster)) {
            // Set up a spotlight poster for use in things like the iframe replacement div
            update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
        }

        if (count($c->childchannels) > 0) {

            $parent_id = $page_id;

            foreach ($c->childchannels as $ch) {

                $chname = isset($ch->name) ? $ch->name : '';

                $slug2 = rtrim(ltrim($ch->slug, '-'), '-');

                $page_id = wp_insert_post(array(
                    'post_title'   => $ch->title,
                    'post_type'    => 'page',
                    'post_name'    => $slug2,
                    'post_status'  => 'publish',
                    'post_excerpt' => 'Channel ' . $chname,
                    'post_parent'  => $parent_id,
                ));

                update_post_meta($page_id, 'ds-category', $c->categories[0]->slug);

                if (!empty($c->spotlight_poster)) {
                    // Set up a spotlight poster for use in things like the iframe replacement div
                    update_post_meta($page_id, 'ds-spotlight-poster', $c->spotlight_poster);
                }

            }

        }

    }

}

// Loop through the channels within a category to display them
function channel_loop()
{

    $channels = list_channels();

    if (!$channels || count($channels) < 1) {

        echo "No channels to display.";

        return;
    }

    foreach ($channels as $c) {

        echo "Title: " . $c->title . "<br/>";

        echo "Image: <img src='" . $c->videos_thumb . "/380/215' /><br/>";

    }

}

// Loop through categories on the channel categories page
function categories_loop()
{

    set_time_limit(120);

    $cat = list_categories();

    foreach ($cat as $c) {

        $post = get_page_by_path('channel-categories/' . $c->slug);

        $show_check = get_post_meta($post->ID, 'ds_show_category', true);

        if ($show_check != 1) {
            continue;
        }

        $image = get_option('ds-category-image-' . $c->slug);

        if ($image) {

            $thumb_id = $image;

        } else if (isset($c->image->poster)) {

            $thumb_id = $c->image->poster . "/960/540";

        } else if (isset($c->image->videos_thumb)) {

            $thumb_id = $c->image->videos_thumb . "/960/540";

        } else {

            $thumb_id = 'https://placehold.it/960x540';

        }

        echo "<li><a href='" . home_url("channel-categories/" . $c->slug) . "'><img class='img img-responsive' src='$thumb_id' /><label class='delay' style='display: inline-block;'><h1>" . $c->name . "</h1></label></a></li>";

    }

}

// Completely reprocess/recreate all category pages.  This is done when the admin requests a flush.
function categories_check()
{

    global $wpdb;

    $categories = list_categories();

    $category_post_check = get_page_by_path('channel-categories');

    if (!$category_post_check) {

        $category_page_id = wp_insert_post(array(
            'post_title'   => "Channel Categories",
            'post_type'    => 'page',
            'post_name'    => "channel-categories",
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Categories',
        ));

    } else {

        $category_page_id = $category_post_check->ID;

    }

    foreach ($categories as $c) {

        $check = $wpdb->get_results("SELECT id FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $c->slug . "' AND post_type != 'nav_menu_item'");

        if (count($check) > 0) {

            continue;

        }

        $page_id = wp_insert_post(array(
            'post_title'   => $c->name,
            'post_type'    => 'page',
            'post_name'    => $c->slug,
            'post_status'  => 'publish',
            'post_excerpt' => 'Channel Category ' . $c->name,
            'post_parent'  => $category_page_id,
        ));

        update_post_meta($page_id, 'ds_show_category', 1);

        if ($c->homepage != 1) {

            update_post_meta($page_id, 'ds_show_category', 0);

        }

        if (!empty($c->image->spotlight_poster)) {
            update_post_meta($page_id, 'ds-spotlight-poster', $c->image->spotlight_poster);
        }

    }

}

// Create the channel category menu; we add this so the admin can set up a menu to go to the categories directly
function ds_create_channel_category_menu()
{

    // Check if the menu exists
    $menu_name   = 'Browse Channel Categories';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    // If it doesn't exist, let's create it.
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        // Set up default menu items

        $cat = list_categories();

        foreach ($cat as $c) {

            $channels = grab_category($c->slug);

            $args = array(
                'menu-item-title'   => __($c->name),
                'menu-item-classes' => $c->slug,
                'menu-item-url'     => home_url('/channel-categories/' . $c->slug . '/'),
                'menu-item-status'  => 'publish');

            wp_update_nav_menu_item($menu_id, 0, $args);

        }

        $auto_assign = get_option('ds_auto_assign_menu');

        if (empty($auto_assign)) {
            return;
        }

        // Set the main menu up in the correct spot post-flush.
        $locations               = get_theme_mod('nav_menu_locations');
        $locations['main_nav']   = $menu_id;
        $locations['header_nav'] = $menu_id;
        $locations['top']        = $menu_id; // WP 2017
        set_theme_mod('nav_menu_locations', $locations);

    }

}

// Completely delete and recreate all category and channel pages.  This is necessary when channels are created or deleted in the DSP dashboard, as well as categories.  There are other use cases, but that is the most common.
function ds_site_flush()
{

    global $wpdb;

    $all_cat_page = get_page_by_path('channel-categories');

    if (isset($all_cat_page->ID)) {

        $cats = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_cat_page->ID);

        foreach ($cats as $cat) {

            wp_delete_post($cat->ID, true); // Delete category page, force true deletion

            $wpdb->query("DELETE FROM " . $wpdb->prefix . "posts WHERE post_name = '" . $cat->post_name . "' AND post_type = 'nav_menu_item'");

        }

    }

    $all_chan_page = get_page_by_path('channels');

    if (isset($all_chan_page->ID)) {

        $chans = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $all_chan_page->ID);

        foreach ($chans as $chan) {

            $child_check = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "posts WHERE post_parent = " . $chan->ID);

            foreach ($child_check as $child) {

                wp_delete_post($child->ID, true); // Delete child channel page, force true deletion

            }

            wp_delete_post($chan->ID, true); // Delete channel page, force true deletion

        }

    }

    wp_delete_nav_menu("Browse Channel Categories");

    // Rebuild Categories
    categories_check();

    // Rebuild Channels
    channels_check();

    ds_create_channel_category_menu();

    wp_redirect(site_url() . "/wp-admin/admin.php?page=dot-studioz-options");
    exit;

}