<?php
/**
 * Template Name: My Lists Template
 * 
 * This template is used to display all my list channels
 * @since 1.0.0
 */
include(locate_template('page-templates/templates-processing/my-lists-template-processing.php'));
get_header();
?>
<div class="custom-container container">
    <h2 class="page-title pt-5">My List</h2>
    <div class="row no-gutters pt-3 pb-5">
        <?php
        if (!empty($final_my_list_page_data['channels'])) {
            foreach ($final_my_list_page_data['channels'] as $channel) { ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center-img p-2">
                    <a href="<?php echo $channel['link']; ?>" title="<?php echo $channel['title']; ?>">
                        <div class="holder">
                            <img src="<?php echo $final_my_list_page_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['banner']; ?>"> 
                            <h4 class="pt-2 text-center"><?php echo $channel['title']; ?></h4>
                        </div>
                    </a>    
                    <div class="text-center pb-2">
                        <button class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel['channel_id']; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove</button>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <h4>You don't have any titles added to your list. <a href="/">Explore</a> our selection to add some!</h4>
            <?php
        }
        ?>
    </div><!-- container -->
</div><!-- no-gutters -->
<?php get_footer(); ?>