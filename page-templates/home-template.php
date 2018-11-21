<?php
/**
 * Template Name: Homepage Template
 * 
 * This template is used to display Home Page of the site.
 * @since 1.0.0
 */
global $dsp_theme_options;
get_header();

$theme_function = new Theme_Functions();
$main_carousel = $theme_function->home_page_main_carousel();
?>

<!-- Home page Main carousal section start-->
<div class="row no-gutters home-main-slider claerfix">
    <div class="col-sm-12 ">
        <?php if ($main_carousel) { ?>
            <div class="columns slick-wrapper small-12 slider" >
                <?php foreach ($main_carousel as $slide) { ?>
                    <?php
                    $title = ($dsp_theme_options['opt-homepage-main-title-trim-word'] != 0) ? wp_trim_words($slide['title'], $dsp_theme_options['opt-homepage-main-title-trim-word']) : $slide['title'];
                    $description = ($dsp_theme_options['opt-homepage-main-description-trim-word'] != 0) ? wp_trim_words($slide['description'], $dsp_theme_options['opt-homepage-main-description-trim-word']) : $slide['description'];
                    ?>
                    <div class="slide">
                        <div class="slide_image">
                            <img class="img img-fluid w-100" src="<?php echo $slide['image'] . '/1920/650'; ?>" title="<?php echo $slide['title']; ?>" alt="<?php echo $slide['title']; ?>">
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
        $home = get_page_by_path($dsp_theme_options['opt-home-carousel'], OBJECT, 'category');

        $category_args = array(
            'post_type' => 'category',
            'posts_per_page' => -1,
            'post__not_in' => array($home->ID),
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
        $categories = new WP_Query($category_args);

        if ($categories->have_posts()) {
            $cnt = 1;
            $class_array = [];
            foreach ($categories->posts as $category) {
                $category_slug = $category->post_name;
                $category_name = $category->post_title;
                $channels = $theme_function->home_page_other_carousel($category_slug, $dsp_theme_options['opt-carousel-poster-type']);
                if ($channels) {
                    ?>
                    <div class="col-sm-12 no-gutters pt-7">
                        <h3 class="post-title mb-5"><?php echo $category_name; ?></h3>
                        <?php
                        $class = 'home-carousel' . $cnt;
                        $class_array[] = $class;
                        $width = filter_var($dsp_theme_options['opt-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                        $height = filter_var($dsp_theme_options['opt-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                        ?>
                        <div class="slick-wrapper <?php echo $class ?>">
                            <?php $i = 1 ?>
                            <?php foreach ($channels as $channel) { ?>

                                <div class="slide">
                                    <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                        <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                                            <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>;" class="lazy" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                            <div class="overlay">
                                                <div class="watch_now"><a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">&nbsp;</a></div>
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
                                    <?php else: ?>
                                        <div class="tooltip_templates">
                                            <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                                                <h4><?php echo $title; ?></h4>
                                                <p><?php echo $description; ?></p>
                                            </span>
                                        </div>
                                    <?php
                                    endif;
                                    $i++;
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    $cnt++;
                }
            }
            $theme_function->slick_init_options($class_array, 'home');
        }
        ?>

    </div><!-- no-gutters -->
</div><!-- container -->
<!-- Home page other carousal section end-->
<?php get_footer(); ?>
