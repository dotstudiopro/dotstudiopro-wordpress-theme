<?php
/**
 * Template Name: Homepage Template
 *
 * This template is used to display Home Page of the site.
 * @since 1.0.0
 */
global $dsp_theme_options, $client_token, $is_user_subscribed;
get_header();

$theme_function = new Theme_Functions();
$dsp_api = new Dsp_External_Api_Request();
$main_carousel = $theme_function->home_page_main_carousel();
$main_carousel_width = filter_var($dsp_theme_options['opt-main-home-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
$main_carousel_height = filter_var($dsp_theme_options['opt-main-home-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
$homepageAPI = $dsp_api->homepage($client_token);
$homepageData = $homepageAPI['homepage'];
?>

<!-- Home page Main carousal section start-->
<div class="row no-gutters home-main-slider claerfix">
    <div class="col-sm-12 ">
        <?php if ($main_carousel) { ?>
            <div class="columns slick-wrapper small-12 slider" >
                <?php
                foreach ($main_carousel as $slide) {
                    ?>
                    <?php
                    $title = ($dsp_theme_options['opt-homepage-main-title-trim-word'] != 0) ? wp_trim_words($slide['title'], $dsp_theme_options['opt-homepage-main-title-trim-word']) : $slide['title'];
                    $description = ($dsp_theme_options['opt-homepage-main-description-trim-word'] != 0) ? wp_trim_words($slide['description'], $dsp_theme_options['opt-homepage-main-description-trim-word']) : $slide['description'];
                    ?>
                    <div class="slide">
                        <div class="slide_image">
                            <img class="img img-fluid w-100" src="<?php echo $slide['image'] . '/' . $main_carousel_width . '/' . $main_carousel_height; ?>" title="<?php echo $slide['title']; ?>" alt="<?php echo $slide['title']; ?>">
                        </div>
                        <div class="slide_content">
                            <div class="container custom-container">
                                <div class="watch_now">
                                    <a href="<?php echo $slide['url']; ?>" class="right-arrow-btn"></a>
                                </div>
                                <div class="inner pt-3"><h2 class="title"><?php echo $title; ?></h2>
                                    <p class="desc"><?php echo $description; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php } ?>
    </div>
</div><!-- no-gutters -->
<!-- Home page Main carousal section end-->

<!-- Home page other carousal section start-->
<div class="custom-container container pb-5">
    <div class="row no-gutters other-categories">
        <?php
        $home = get_page_by_path($dsp_theme_options['opt-home-carousel'], OBJECT, 'channel-category');
        $cnt = 0;
        $class_array = [];
        $continue_class_array = [];
        $slide_text_class = '';
        if ($dsp_theme_options['opt-layout-slider-content'] == 1) {
            $slide_text_class .= 'slide-text-dec';
        } elseif ($dsp_theme_options['opt-layout-slider-content'] == 3) {
            $slide_text_class .= 'slide-text';
        }
        if($dsp_theme_options['opt-home-image-size'] == "0") {
            $width = filter_var($dsp_theme_options['opt-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
            $height = filter_var($dsp_theme_options['opt-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
        }
        else {
            $width = filter_var($dsp_theme_options['opt-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

            $ratio_width = filter_var($dsp_theme_options['opt-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
            $ratio_height = filter_var($dsp_theme_options['opt-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
            
            $ratio = $ratio_height / $ratio_width;
        }
        if ($client_token) {
            $dotstudiopro_api = new Dsp_External_Api_Request();
            $watch_list = $dotstudiopro_api->get_recent_viewed_data($client_token);
            if (!is_wp_error($watch_list)) {
                if (!empty($watch_list['data']['continue-watching'])) {
                    $class = 'home-cnt-carousel' . $cnt;
                    $continue_class_array[] = $class;
                    include(locate_template('page-templates/templates-part/homepage/continue-watch.php'));
                    $cnt++;
                }
                if (!empty($watch_list['data']['watch-again'])) {
                    $class = 'home-cnt-carousel' . $cnt;
                    $continue_class_array[] = $class;
                    include(locate_template('page-templates/templates-part/homepage/watch-again.php'));
                    $cnt++;
                }
            }
        }
        $category_args = array(
            'post_type' => 'channel-category',
            'posts_per_page' => -1,
            'post__not_in' => !empty($home->ID) ? array($home->ID) : array(), // Ensure we have a home here, or else we get errors
            'order' => 'ASC',
            'meta_key' => 'weight',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key' => 'is_on_cat_homepage',
                    'value' => 1
                )
            )
        );
        $categories = $theme_function->query_categories_posts($category_args, "homepage_other_carousel_categories");

        if (!empty($homepageData)) {
            foreach ($homepageData as $data) {
                $category_slug = $data['category']['slug'];
                if($category_slug == $dsp_theme_options['opt-home-carousel']){
                    continue;
                }
                $channels = $theme_function->home_page_other_carousel_with_api($data['channels'], $dsp_theme_options['opt-carousel-poster-type']);
                if ($channels) {
                    ?>
                    <div class="col-sm-12 no-gutters pt-7">
                        <h3 class="post-title mb-5">
                            <?php 
                            //if(count($channels) != 1)
                                $category_url = '/channel-category/' . $data['category']['slug'];
                            //else
                                //$category_url = $channels[0]['url'];
                             ?>
                            <a href="<?php echo $category_url; ?>">
                                <?php echo $data['category']['name']; ?>
                            </a>
                        </h3>
                        <?php
                        $class = 'home-carousel' . $cnt;
                        $class_array[] = $class;
                        $total_channels = count($channels);
                        ?>
                        <div class="slick-wrapper <?php echo $class . ' ' . $slide_text_class ?>">
                            <?php $i = 1 ?>
                            <?php foreach ($channels as $channel) { ?>
                                <div class="slide">
                                    <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                        <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                                            <?php if (isset($channel['channel_unlock']) && $channel['channel_unlock'] == false && $channel['bypass_channel_lock'] != true && $channel['bypass_channel_lock'] != 'true'): ?>
                                                <div class="locked-channel"><i class="fa fa-lock"></i></div>
                                            <?php endif; ?>

                                            <?php if( $dsp_theme_options['opt-home-image-size'] == '1' ) : 
                                                $image_attributes = dsp_build_responsive_images( $channel['image'], $width, $ratio ); ?>
                                                
                                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                            <?php else : ?>
                                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height; ?>" class="lazy w-100" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                            <?php endif; ?>    
                                            <div class="overlay">
                                                <div class="watch_now"><a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Condition to check display the content on tooltip or below the images-->
                                    <?php
                                    $title = ($dsp_theme_options['opt-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-title-trim-word']) : $channel['title'];
                                    $description = ($dsp_theme_options['opt-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-description-trim-word']) : $channel['description'];
                                    ?>
                                    <?php if ($dsp_theme_options['opt-layout-slider-content'] == 1): ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </a>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-slider-content'] == 2): ?>
                                        <div class="tooltip_templates">
                                            <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                                <h4><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </span>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-slider-content'] == 3):
                                        ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                                            </a>
                                        </div>
                                        <?php
                                    endif;
                                    $i++;
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                            $style = '';
                            switch (dsp_wp_is_mobile()) {
                                case 0:
                                    if($total_channels <= $dsp_theme_options['opt-slick-home-slidetoshow']):
                                        $style = 'style="display:none;"';
                                    endif;
                                    break;
                                case 1:
                                    if($total_channels <= $dsp_theme_options['opt-slick-home-mobile-slidetoshow']):
                                        $style = 'style="display:none;"';
                                    endif;
                                    break;
                                case 2:
                                    if($total_channels <= $dsp_theme_options['opt-slick-home-tablet-slidetoshow']):
                                        $style = 'style="display:none;"';
                                    endif;
                                    break;
                                default:
                                    $style = '';
                                    break;
                            }
                        ?>
                        <div class="dsp-homepage-see-more" <?php echo $style; ?>>
                            <a href="<?php echo '/channel-category/' . $category_slug; ?>">View All</a>
                        </div>
                    </div>
                    <?php
                    $cnt++;
                }
            }
            $theme_function->slick_init_options('slick_carousel', $class_array, 'home');
            $theme_function->slick_init_options('slick_continue_watch', $continue_class_array, 'continue-watch');
        }
        ?>

    </div><!-- no-gutters -->
</div><!-- container -->
<!-- Home page other carousal section end-->
<?php get_footer(); ?>
