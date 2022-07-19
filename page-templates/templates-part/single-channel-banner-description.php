<div class="inner-banner-content row no-gutters">
    <?php if($dsp_theme_options['opt-channel-poster-logo-title'] == 'logo' && isset($channel_logo)){ ?>
            <img class="title_logo pb-3" src="<?php echo $channel_logo; ?>" alt="<?php echo get_the_title(); ?>">
    <?php }else{ ?>
    <h2><?php echo get_the_title(); ?></h2>
    <?php }?>
    <p class="w-100 pb-3"><?php echo dsp_get_channel_publication_meta(get_the_ID()); ?></p>
    <?php the_content(); ?>
    <?php if(!empty($live_stream_start_time) && $current_time < $convert_live_stream_start_time_to_user_time){ ?>
        <div class="available_on_info">
           <br><p style="color:#AF202C;" class="available_on_date"></p>
        </div>
    <?php } ?>
    <div class="subscribe_now mt-3">
        <?php if (!empty($svod_products) && empty($parant_channel_unlocked)): ?>
            <a href="/packages" class="btn btn-secondary btn-ds-secondary">Subscribe Now</a>
        <?php elseif(!empty($parant_channel_unlocked)): ?>
            <a href="<?php echo $first_video_url; ?>" class="btn btn-secondary btn-ds-secondary">Watch Now</a>
        <?php endif; ?>
    </div>
    <div class="more_ways_to_watch_now ml-2 mt-3 mr-2">
        <?php if (!empty($tvod_products) && empty($parant_channel_unlocked)): ?>
            <a href="/more-ways-to-watch/<?php echo $post->post_name; ?>" class="btn btn-secondary btn-ds-secondary">More Ways to Watch</a>
        <?php endif; ?>
    </div>
    <?php if (class_exists('WP_Auth0_Options')) { ?>
        <div class="my_list_button mt-3">
            <?php
            if ($first_child_id) {
                if ($client_token) {
                    if (isset($display_remove_from_my_list_button)) { ?>
                        <a href="#" class="btn btn-danger manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="removeFromMyList" data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>"><i class="fa fa-minus-circle"></i> Remove from My List</a>
                    <?php } else { ?>
                        <a href="#" class="btn btn-secondary btn-ds-secondary manage_my_list" data-channel_id="<?php echo $channel_id; ?>" data-parent_channel_id="<?php echo $p_channel_id; ?>" data-action="addToMyList" data-nonce="<?php echo wp_create_nonce('addToMyList'); ?>"><i class="fa fa-plus-circle"></i> Add to My List</a>
                        <span data-nonce="<?php echo wp_create_nonce('removeFromMyList'); ?>" style="display: none;"></span>
                    <?php } ?>
                <?php } else { ?>
                    <a href="<?php echo wp_login_url(home_url($wp->request)); ?>" class="btn btn-secondary btn-ds-secondary"><i class="fa fa-plus-circle"></i> Add to My List</a>
                    <?php
                }
            }
            ?>
        </div>
    <?php } ?>
</div>