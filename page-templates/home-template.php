<?php
/**
 * Template Name: Homepage Template
 *
 * This template is used to display Home Page of the site.
 * @since 1.0.0
 */
include(locate_template('page-templates/templates-processing/home-template-processing.php'));
get_header();
?>

<!-- Home page Main carousal section start-->
<div class="row no-gutters home-main-slider claerfix">
    <div class="col-sm-12 ">
        <?php if (isset($final_homepage_data['main_carousel']) && !empty($final_homepage_data['main_carousel'])) { ?>
            <div class="columns slick-wrapper small-12 slider" >
                <?php foreach ($final_homepage_data['main_carousel'] as $slide) { ?>
                    <div class="slide">
                        <div class="slide_image">
                            <img class="img img-fluid w-100" src="<?php echo $slide['image'] ?>" title="<?php echo $slide['title']; ?>" alt="<?php echo $slide['title']; ?>">
                        </div>
                        <div class="slide_content">
                            <div class="container custom-container">
                                <div class="watch_now">
                                    <a href="<?php echo $slide['url']; ?>" class="right-arrow-btn"></a>
                                </div>
                                <div class="inner pt-3"><h2 class="title"><?php echo $slide['title'];; ?></h2>
                                    <p class="desc"><?php echo $slide['description']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div><!-- no-gutters -->
<!-- Home page Main carousal section end-->

<!-- Home page other carousal section start-->
<div class="custom-container container pb-5">
    <div class="row no-gutters other-categories">
        <?php
        
        $cnt = 0;
        $class_array = [];
        $continue_class_array = [];
        $slide_text_class = '';
        if ($dsp_theme_options['opt-layout-slider-content'] == 1) {
            $slide_text_class .= 'slide-text-dec';
        } elseif ($dsp_theme_options['opt-layout-slider-content'] == 3) {
            $slide_text_class .= 'slide-text';
        }
        
        // Continue watching and watch again carousal section start
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
        // Continue watching and watch again carousal section end
        
        if (isset($final_homepage_data['secondary_carousel_data']) && !empty($final_homepage_data['secondary_carousel_data'])) {
            foreach($final_homepage_data['secondary_carousel_data'] as $data){
        ?>
                <div class="col-sm-12 no-gutters pt-7">
                    <h3 class="post-title mb-5">
                         <a href="<?php echo $data['category_url']; ?>">
                            <?php echo $data['category_name']; ?>
                        </a>
                    </h3>
                    <?php
                    $class = 'home-carousel' . $cnt;
                    $class_array[] = $class;
                    $total_channels = count($data['channels']);
                    ?>
                    <div class="slick-wrapper <?php echo $class . ' ' . $slide_text_class ?>">
                        <?php $i = 1 ?>
                        <?php foreach ($data['channels'] as $channel) { ?>
                            <div class="slide">
                                <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                    <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                                        <?php if (isset($channel['show_lock_icon']) && !empty($channel['show_lock_icon'])) : ?>
                                            <div class="locked-channel"><i class="fa fa-lock"></i></div>
                                        <?php endif; ?>
                                        <?php if(isset($channel['image_attributes_sizes']) && isset($channel['image_attributes_srcset'])) :?>
                                            <img src="<?php echo $final_homepage_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['channel_title']; ?>" alt="<?php echo $channel['channel_title']; ?>" srcset="<?php echo $channel['image_attributes_srcset']; ?>" sizes="<?php echo $channel['image_attributes_sizes']; ?>">
                                        <?php else : ?>   
                                            <img src="<?php echo $final_homepage_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['channel_title']; ?>" alt="<?php echo $channel['channel_title']; ?>">
                                        <?php endif; ?>
                                        <div class="overlay">
                                            <div class="watch_now"><a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Condition to check display the content on tooltip or below the images-->
                                <?php if ($dsp_theme_options['opt-layout-slider-content'] == 1): ?>
                                    <div class="slide_content">
                                        <a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">
                                            <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
                                            <p><?php echo $channel['trim_channel_description']; ?></p>
                                        </a>
                                    </div>
                                <?php elseif ($dsp_theme_options['opt-layout-slider-content'] == 2): ?>
                                    <div class="tooltip_templates">
                                        <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                            <h4><?php echo $channel['trim_channel_title']; ?></h4>
                                            <p><?php echo $channel['trim_channel_description']; ?></p>
                                        </span>
                                    </div>
                                <?php elseif ($dsp_theme_options['opt-layout-slider-content'] == 3):
                                    ?>
                                    <div class="slide_content">
                                        <a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">
                                            <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
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
            $theme_function->slick_init_options('slick_carousel', $class_array, 'home');
            $theme_function->slick_init_options('slick_continue_watch', $continue_class_array, 'continue-watch');
        }
        ?>
    </div><!-- no-gutters -->
</div><!-- container -->
<!-- Home page other carousal section end-->
<?php get_footer(); ?>
