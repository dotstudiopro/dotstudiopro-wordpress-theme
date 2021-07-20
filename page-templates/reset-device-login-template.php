<?php
/**
 * Template Name: Reset Device Login
 */
get_header();
?>
<div class="col-sm-12 blog-main pt-5">

    <?php
    if (have_posts()) {
        while (have_posts()) : the_post();
            ?>
            <div class="blog-post pt-5 text-center">
                <h4>You have reached the maximum number of logged-in devices allowed on your account.</h4>
                <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST" class="pt-5">
                    <input type="hidden" name="action" value="destroy_every_user_login_session">
                    <input type="submit" class="btn btn-secondary btn-ds-secondary" value="Log out from other sessions">
                </form>  
            </div><!-- /.blog-post -->
            <?php
        endwhile;
    }
    ?>

</div><!-- /.blog-main -->
<?php get_footer(); ?>