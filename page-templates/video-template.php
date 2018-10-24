<?php
/**
 * Template Name: Video Template
 */
global $dsp_theme_options;
get_header();
?>
<div class="container">
    <div class="row no-gutters">

        <?php
        $category_args = array(
            'post_type' => 'category',
            'posts_per_page' => -1,
        );
        $categories = new WP_Query($category_args);
        if ($categories->have_posts()) {
            foreach ($categories->posts as $category) {
                $category_meta = get_post_meta($category->ID);
                $banner = ($category->cat_poster) ? $category->cat_poster : 'https://picsum.photos';
                ?>
                <div class="col-md-4 p-2">
                    <div class="holder">
                        <img src="https://worldwithouthorizons.com/wp-content/uploads/placeholder.jpg" class="lazy" data-src="<?php echo $banner; ?>"> 
                        <h3><?php echo $category->post_title; ?></h3>
                    </div>
                </div>
                <?php
            }
        } else {
            
        }
        ?>
    </div>
</div>
<?php get_footer(); ?>