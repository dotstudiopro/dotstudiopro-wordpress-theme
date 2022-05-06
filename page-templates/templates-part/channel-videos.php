<?php include(locate_template('page-templates/templates-processing/channel-videos-processing.php')); ?>
<div class="slick-wrapper <?php echo $class . ' ' . $slide_text_class ?>">
    <?php
    $i = 0;
    foreach ($final_videos_data['final_videos'] as $video):
        ?>
        <div class="slide <?php echo $video['class']; ?>">
            <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'channel_tooltip_content_' . $cnt . $i; ?>">
                <div class="hover <?php echo $lock_video_class; ?> ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                    <?php if (isset($channel_unlocked) && $channel_unlocked == 0 && $video['bypass_channel_lock'] != true && $video['bypass_channel_lock'] != 'true'): ?>
                        <div class="locked-channel"><i class="fa fa-lock"></i></div>
                    <?php endif; ?>

                    <?php if(isset($video['image_attributes_sizes']) && isset($video['image_attributes_srcset'])) :?>
                        <img src="<?php echo $final_videos_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $video['banner']; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>" srcset="<?php echo $video['image_attributes_srcset']; ?>" sizes="<?php echo $video['image_attributes_sizes']; ?>">
                    <?php else : ?>   
                        <img src="<?php echo $final_videos_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $video['banner']; ?>" title="<?php echo $video['channel_title']; ?>" alt="<?php echo $video['channel_title']; ?>">
                    <?php endif; ?>
                    <div class="overlay">
                        <div class="watch_now"><a class="info" href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                    </div>
                </div>
            </div>
            <!-- Condition to check display the content on tooltip or below the images-->
            <?php if ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 1): ?>
                <div class="slide_content">
                    <a class="info" href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">
                        <h4 class="pt-3 pb-1"><?php echo $video['trim_title']; ?></h4>
                        <p><?php echo $video['trim_description']; ?></p>
                    </a>
                </div>
            <?php elseif ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 2): ?>
                <div class="tooltip_templates">
                    <span id="<?php echo 'channel_tooltip_content_' . $cnt . $i; ?>">
                        <h4><?php echo $video['trim_title']; ?></h4>
                        <p><?php echo $video['trim_description']; ?></p>
                    </span>
                </div>
            <?php elseif ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 3): ?>
                <div class="slide_content">
                    <a class="info" href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">
                        <h4 class="pt-3 pb-1"><?php echo $video['trim_title']; ?></h4>
                    </a>
                </div>
                <?php
            endif;
            $i++;
            ?>
        </div>
        <?php
    endforeach;
    ?>
</div>
