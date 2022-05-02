<?php
global $dsp_theme_options;
$selected_collections = $dsp_theme_options['opt-category-list'];
$arr = [];
foreach($selected_collections as $selected_collection_key => $value){
    if($value == 1){
        $arr[] = $selected_collection_key;
    }
}
if(isset($dsp_theme_options['opt-category-all']) && $dsp_theme_options['opt-category-all'] == 1){
    $arr = [];
}
$theme_function = new Theme_Functions();
$category_args =  $theme_function->category_args($arr);
$categories = $theme_function->query_categories_posts($category_args, "categories_template");

$final_category_data = array();
$final_category_data['number_of_row'] = $dsp_theme_options['opt-display-categories-row'];

$category_data = array();

if($dsp_theme_options['opt-categories-image-size'] == '1') {
    $width = filter_var($dsp_theme_options['opt-categories-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_width = filter_var($dsp_theme_options['opt-categories-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
    $ratio_height = filter_var($dsp_theme_options['opt-categories-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
    $ratio = $ratio_height / $ratio_width;
    $final_category_data['width'] = $width;
}
else {
    $width = filter_var($dsp_theme_options['opt-categories-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
    $height = filter_var($dsp_theme_options['opt-categories-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
    $final_category_data['width'] = $width;
    $final_category_data['height'] = $height;
}

if ($categories) {
    foreach ($categories as $key => $category) {
        $channels_args = $theme_function->channels_args($category->post_name);
        $cache_key = "categories_channel_" . $category->post_name;
        $channels = $theme_function->query_categories_posts($channels_args, $cache_key);
        if($channels){
            $category_meta = get_post_meta($category->ID);
            $category_banner = ($dsp_theme_options['opt-categories-poster-type'] == 'wallpaper') ? $category->cat_wallpaper : $category->cat_poster;
            $banner = ($category_banner) ? $category_banner : 'https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b';
            $category_listing_option = $dsp_theme_options['opt-cateogry-listing-option'];
            if ($category_listing_option == 'category-listing-page'):
                $link = get_permalink($category->ID);
            else:
                $category_channel = $theme_function->get_category_channels($category->post_name);
                if (!empty($category_channel)) {
                    $child_channels = $theme_function->is_child_channels(array_values($category_channel)[0]->ID);
                     if($child_channels){
                        $video = $theme_function->show_videos(array_values($category_channel)[0], 'categories-template', $category->post_name, array_values($category_channel)[0]->post_name, null);
                     }
                     else{
                        $video = $theme_function->show_videos(array_values($category_channel)[0], 'categories-template', $category->post_name, null, null );
                    }
                    $link = $video[0]['url'];
                    
                } else {
                    $link = get_permalink($category->ID);
                }
            endif;
            $category_data[$key]['link'] = $link;
            $category_data[$key]['title'] = $category->post_title;
            if ($dsp_theme_options['opt-categories-title'] == true){
                if(!empty($category_meta['cat_display_name'][0])){
                    $category_data[$key]['display_name'] = $category_meta['cat_display_name'][0];
                }else{
                    $category_data[$key]['display_name'] = $category->post_title;
                }
            }
            if( $dsp_theme_options['opt-categories-image-size'] == '1'){
                $image_attributes = dsp_build_responsive_images( $banner, $width, $ratio );
                $category_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
                $category_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
            }
            $category_data[$key]['banner'] = $banner.'/'.$final_category_data['width'];
            if(isset($final_category_data['height']))
                $category_data[$key]['banner'] = $category_data[$key]['banner'].'/'.$final_category_data['height'];
            if($dsp_theme_options['opt-display-webp-image'] == 1)
                $category_data[$key]['banner'] = $category_data[$key]['banner'].'?webp=1';
        }
    }
}
$final_category_data['default_image'] = 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/'.$final_category_data['width'];
if(isset($final_category_data['height']))
    $final_category_data['default_image'] = $final_category_data['default_image'].'/'.$final_category_data['height'];
$final_category_data['category'] = $category_data;

?>