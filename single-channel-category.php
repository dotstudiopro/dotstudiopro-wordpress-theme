<?php
include(locate_template('page-templates/templates-processing/single-channel-category-processing.php'));
get_header();
if (have_posts()) {
    while (have_posts()) : the_post();
        $theme_function = new Theme_Functions();
        $category_meta = get_post_meta(get_the_ID());
        $banner_image = ($dsp_theme_options['opt-category-poster-type'] == 'poster') ? $category_meta['cat_poster'][0] : $category_meta['cat_wallpaper'][0];
        $banner = ($banner_image) ? $banner_image.'/1920/350' : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/1920/350';
        if($dsp_theme_options['opt-display-webp-image'] == 1)
            $banner = $banner.'?webp=1';
        $category_title = (!empty($category_meta['cat_display_name'][0]) ? $category_meta['cat_display_name'][0] : get_the_title() );
        ?>
        <!-- Category Background and Information section start -->
        <div class="category inner-banner-bg">
            <?php if($dsp_theme_options['opt-category-poster-visible'] == true): ?>
                <div class="inner-banner-img">
                    <img src="<?php echo $banner; ?>" alt="<?php echo $category_title; ?>">
                </div>
            <?php endif; ?>
            <?php if ($dsp_theme_options['opt-category-poster-information'] == true) : ?>
                <div class="inner-banner-content_bg">
                    <div class="inner-banner-content row no-gutters">
                        <div class="custom-container container pt-5">
                            <h2><?php echo $category_title; ?></h2>
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Category Background and Information section start -->
        <!-- Channel Information section start -->
        <div class="custom-container container pt-5 pb-5">
            <div class="col-sm-12 other-categories">
                <div class="row">
                    <?php
                    if(isset($final_channel_data['channels']) && !empty($final_channel_data['channels'])){
                        $i = 0;
                        foreach($final_channel_data['channels'] as $channel){ ?>
                            <div class="col-md-<?php echo $final_channel_data['number_of_row']; ?> p-2 channel-banner">
                                <a href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                    <div class="tooltippp" data-tooltip-content="#<?php echo 'tooltip_content_' . $i; ?>">
                                        <div class="clearfix">
                                            <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                                                <?php if (isset($channel['dspro_is_product']) && $channel['dspro_is_product'] == 1 && $is_user_subscribed == false): ?>
                                                    <div class="locked-channel"><i class="fa fa-lock"></i></div>
                                                <?php endif; ?>
                                                <?php if(isset($channel['image_attributes_sizes']) && isset($channel['image_attributes_srcset'])) :?>
                                                    <img src="<?php echo $final_channel_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['banner']; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>" srcset="<?php echo $channel['image_attributes_srcset']; ?>" sizes="<?php echo $channel['image_attributes_sizes']; ?>">
                                                <?php else : ?>   
                                                    <img src="<?php echo $final_channel_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['banner']; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                                <?php endif; ?>
                                                <div class="overlay">
                                                    <div class="watch_now"><a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($dsp_theme_options['opt-layout-channel-slider-content'] == 1): ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
                                                <p><?php echo $channel['trim_channel_description']; ?></p>
                                            </a>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-channel-slider-content'] == 2): ?>
                                        <div class="tooltip_templates">
                                            <span id="<?php echo 'tooltip_content_' . $i; ?>">
                                                <h4><?php echo $channel['trim_channel_title']; ?></h4>
                                                <p><?php echo $channel['trim_channel_description']; ?></p>
                                            </span>
                                        </div>
                                    <?php elseif ($dsp_theme_options['opt-layout-channel-slider-content'] == 3):
                                        ?>
                                        <div class="slide_content">
                                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                                <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php
                        $i++;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- Channel Information section start -->
        <?php
    endwhile;
}
?>
<?php get_footer(); ?>
