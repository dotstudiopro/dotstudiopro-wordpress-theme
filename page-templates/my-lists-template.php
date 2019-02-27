<?php
/**
 * Template Name: My Lists Template
 * 
 * This template is used to display all my list channels
 * @since 1.0.0
 */
global $dsp_theme_options, $client_token;
if (!$client_token) {
    wp_redirect('/');
}
get_header();
?>
<div class="custom-container container">
    <h2 class="page-title pt-5">My List</h2>
    <div class="row no-gutters pt-5 pb-5">
        <?php
        $dotstudio_api = new Dsp_External_Api_Request();
        $all_channels = $dotstudio_api->get_user_watchlist($client_token);

        if (!is_wp_error($all_channels)) {
            if (!empty($all_channels['channels'])) {

                foreach ($all_channels['channels'] as $channel) {
                    $channel_id = $channel['_id'];
                    $link = '/channel/' . $channel['slug'];
                    $banner = $channel['spotlight_poster'] . '/240/360'
                    ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 text-center-img p-2">
                        <a href="<?php echo $link; ?>" title="<?php echo $channel['title']; ?>">
                            <div class="holder">
                                <img src="https://images.dotstudiopro.com/5bd9eb28d57fdf6513eb280b/240/360" class="lazy w-100" data-src="<?php echo $banner; ?>"> 
                                <h3><?php echo $channel['title']; ?></h3>
                            </div>
                        </a>    
                        <div class="pt-3 text-center pb-3">
                            <button class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove</button>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <h4>You don't have any titles added to your list. <a href="/">Explore</a> our selection to add some!</h4>
                <?php
            }
        }
        ?>
    </div><!-- container -->
</div><!-- no-gutters -->
<?php get_footer(); ?>