<?php
/**
 * Template Name: Categories Template
 * 
 * This template is used to display all the categories
 * @since 1.0.0
 */
global $dsp_theme_options;
get_header();
?>
<div class="custom-container container">
    <div class="row no-gutters categories-page pt-5 pb-5">

        <?php

        $selected_collections = $dsp_theme_options['opt-category-list'];
        
        $arr = [];
        foreach($selected_collections as $key => $value){
            if($value == 1){
                $arr[] = $key;
            }
        }
        if(isset($dsp_theme_options['opt-category-all']) && $dsp_theme_options['opt-category-all'] == 1){
            $arr = [];
        }

        $theme_function = new Theme_Functions();
        $category_args =  $theme_function->category_args($arr);
        $categories = $theme_function->query_categories_posts($category_args, "categories_template");

        if ($categories) {
            foreach ($categories as $category) {
                
                $channels_args = $theme_function->channels_args($category->post_name);
                $cache_key = "categories_channel_" . $category->post_name;
                $channels = $theme_function->query_categories_posts($channels_args, $cache_key);

                if ($channels) {
                    $category_meta = get_post_meta($category->ID);
                    $category_banner = ($dsp_theme_options['opt-categories-poster-type'] == 'wallpaper') ? $category->cat_wallpaper : $category->cat_poster;
                    if( $dsp_theme_options['opt-categories-image-size'] == '1') {
                        $width = filter_var($dsp_theme_options['opt-categories-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                        $ratio_width = filter_var($dsp_theme_options['opt-categories-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                        $ratio_height = filter_var($dsp_theme_options['opt-categories-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                        $ratio = $ratio_height / $ratio_width;
                    }
                    else {
                        $width = filter_var($dsp_theme_options['opt-categories-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                        $height = filter_var($dsp_theme_options['opt-categories-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                    }
                    $banner = ($category_banner) ? $category_banner : 'https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b';
                    $number_of_row = $dsp_theme_options['opt-display-categories-row'];
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
                    ?>
                    <div class="col-md-<?php echo $number_of_row; ?> p-4">
                        <a href="<?php echo $link; ?>" title="<?php echo $category->post_title; ?>">
                            <div class="holder">
                                <?php if( $dsp_theme_options['opt-categories-image-size'] == '1') :
                                    $image_attributes = dsp_build_responsive_images( $banner, $width, $ratio ); 
                                    if($dsp_theme_options['opt-display-webp-image'] == 0):?>
                                        <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width; ?>" class="lazy w-100" data-src="<?php echo $banner; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                    <?php else:?>
                                        <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width; ?>?webp=1" class="lazy w-100" data-src="<?php echo $banner; ?>?webp=1" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                    <?php endif; ?> 
                                <?php else :  
                                    if($dsp_theme_options['opt-display-webp-image'] == 0):?>   
                                        <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width . '/' . $height; ?>" class="lazy w-100" data-src="<?php echo $banner; ?>">
                                    <?php else:?>
                                        <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/<?php echo $width . '/' . $height; ?>?webp=1" class="lazy w-100" data-src="<?php echo $banner; ?>?webp=1">
                                    <?php endif; ?> 
                                <?php endif; ?> 
                                <?php if ($dsp_theme_options['opt-categories-title'] == true): ?>
                                    <?php if(!empty($category_meta['cat_display_name'][0])) : ?>
                                        <h3><?php echo $category_meta['cat_display_name'][0]; ?></h3>
                                    <?php else : ?>    
                                        <h3><?php echo $category->post_title; ?></h3>
                                    <?php endif; ?>    
                                <?php endif; ?>
                            </div>
                        </a>    
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div><!-- container -->
</div><!-- no-gutters -->
<?php get_footer(); ?>
