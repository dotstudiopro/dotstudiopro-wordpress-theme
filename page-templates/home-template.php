<?php
/**
 * Template Name: Homepage Template
 */
global $dsp_theme_options;
get_header();

$theme_function = new Theme_Functions();
$main_carousel = $theme_function->home_page_main_carousel();
?>
<div class="row">
    <div class="col-sm-12 blog-main">
        <?php if ($main_carousel) { ?>
            <div class="columns small-12 slider" >
                <?php foreach ($main_carousel as $slide) { ?>
                    <div class="slide">
                        <div class="slide_image">
                            <img src="<?php echo $slide['image'] . '/1920/600'; ?>" title="<?php echo $slide['title']; ?>" alt="<?php echo $slide['title']; ?>">
                        </div>
                        <div class="slide_content">
                            <div class="container">
                                <div class="watch_now">
                                    <a href="<?php echo $slide['url']; ?>" class="btn btn-primary"><i class="el el-arrow-right"></i>Play Now</a>
                                </div>
                                <h2 class="title"><?php echo $slide['title']; ?></h2>
                                <p class="desc"><?php echo $slide['description']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div><!-- /.blog-main -->
</div>

<?php
$home = get_page_by_path($dsp_theme_options['opt-home-carousel'], OBJECT, 'category');

$category_args = array(
    'post_type' => 'category',
    'posts_per_page' => -1,
    'post_not_in' => $home->ID,
);
$categories = new WP_Query($category_args);

if ($categories->have_posts()) {
    $cnt = 1;
    $class_array = [];
    foreach ($categories->posts as $category) {
        $category_slug = $category->post_name;
        $category_name = $category->post_title;
        $channels = $theme_function->home_page_other_carousel($category_slug);
        if ($channels) {
            ?>
            <div class="row">
                <div class="container">
                    <div class="col-sm-12">
                        <h2 class="post-title"><?php echo $category_name; ?></h2>
                        <?php
                        $class = 'home-carousel' . $cnt;
                        $class_array[] = $class;
                        $width = filter_var($dsp_theme_options['opt-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                        $height = filter_var($dsp_theme_options['opt-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT)
                        ?>
                        <div class="<?php echo $class ?>">
                            <?php foreach ($channels as $channel) { ?>
                                <div class="slide">
                                    <div class="slide_image">
                                        <img src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                                    </div>
                                    <div class="slide_content">
                                        <h6><?php echo $channel['title']; ?></h6>
                                        <p><?php echo wp_trim_words($channel['description'], 5, '...'); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $cnt++;
        }
    }
}
$theme_function->slick_init_options($class_array);
?>
<?php get_footer(); ?>