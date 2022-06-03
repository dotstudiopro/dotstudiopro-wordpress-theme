<?php
/**
 * Template Name: Account Deletion
 */
get_header();
include(locate_template('page-templates/templates-processing/account-deletion-template-processing.php'));
?>
<div class="col-sm-12 blog-main pt-5">
    <div class="page title-banner-bg">
            <div class="title-banner-content_bg">
            <div class="title-banner-content row no-gutters">
                <div class="container pt-5">
                    <div class="col-sm-12 blog-main">
                        <div class="blog-post">
                            <h2 class="post-title">Account Deletion</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="blog-post pt-5 pb-5 text-center">
    <?php
    if ($client_token) {
        if($account_deleted){ ?>
            <h4 class="account-deleted-message">Your account is marked for deletion & will be deleted on <b style="color: #af202c;"><?php echo $date; ?></b>
            </h4>
        <?php } else{
            the_content();
            ?>
            <a href="#" class="btn btn-lg btn-secondary btn-ds-secondary mt-3" id="account_deletion_button" data-nonce='<?php echo wp_create_nonce('account_deletion'); ?>' data-action='account_deletion'>I Understand. Delete Account.</a>
        <?php } 
    }else{ ?>
        <h4 class="login-message">You must be logged in to delete your account.
        </h4>
        <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-lg btn-secondary btn-ds-secondary">Log in or sign up here</a>
    <?php } ?>
    </div>
</div><!-- /.blog-main -->
<?php get_footer(); 
?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/account-deletion.min.js"></script>