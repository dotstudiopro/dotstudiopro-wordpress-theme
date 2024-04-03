<?php
/**
 * Template Name: Categories Template
 * 
 * This template is used to display all the categories
 * @since 1.0.0
 */
include(locate_template('page-templates/templates-processing/category-template-processing.php'));
get_header();
?>
<div class="custom-container container">
    <div class="row no-gutters categories-page pt-5 pb-5">
        <?php
        if (isset($final_category_data['category']) && !empty($final_category_data['category'])) {
         foreach ($final_category_data['category'] as $category) {
         ?>
         <div class="col-md-<?php echo $final_category_data['number_of_row']; ?> p-4">
            <a href="<?php echo $category['link']; ?>" title="<?php echo $category['title']; ?>">
                <div class="holder">
                    <?php if(isset($category['image_attributes_sizes']) && isset($category['image_attributes_srcset'])) :?>
                        <img src="<?php echo $final_category_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $category['banner']; ?>" srcset="<?php echo $category['image_attributes_srcset']; ?>" sizes="<?php echo $category['image_attributes_sizes']; ?>">
                    <?php else : ?>   
                        <img src="<?php echo $final_category_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $category['banner']; ?>">
                    <?php endif; ?> 
                    <?php if (isset($category['display_name'])): ?>
                        <h3><?php echo $category['display_name']; ?></h3>
                    <?php endif; ?>
                </div>
            </a>    
        </div>
         <?php
         }   
        }
        ?>
    </div><!-- container -->
</div><!-- no-gutters -->
<?php get_footer(); ?>
