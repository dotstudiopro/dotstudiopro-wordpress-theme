<?php
include(locate_template('page-templates/templates-processing/related-content-processing.php'));
if (!empty($final_releted_content_data['releted_content_data'])):
    ?>
    <h3 class="post-title mb-4"><?php echo $dsp_theme_options['opt-related-content-text']; ?></h3>
    <div class="slick-wrapper related_content <?php echo $final_releted_content_data['slide_text_class']; ?>">
        <?php
        $i = 1;
        foreach ($final_releted_content_data['releted_content_data'] as $channel):
            ?>
            <div class="slide">
                <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'releted_tooltip_content_' . $cnt . $i; ?>">
                    <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                        <?php if(isset($channel['show_lock_icon']) && $channel['show_lock_icon'] == 1): ?>
                           <div class="locked-channel"><i class="fa fa-lock"></i></div>
                        <?php endif; ?>
                        <?php if(isset($channel['image_attributes_sizes']) && isset($channel['image_attributes_srcset'])) :?>
                            <img src="<?php echo $final_releted_content_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['channel_title']; ?>" alt="<?php echo $channel['channel_title']; ?>" srcset="<?php echo $channel['image_attributes_srcset']; ?>" sizes="<?php echo $channel['image_attributes_sizes']; ?>">
                        <?php else : ?>   
                            <img src="<?php echo $final_releted_content_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $channel['image']; ?>" title="<?php echo $channel['channel_title']; ?>" alt="<?php echo $channel['channel_title']; ?>">
                        <?php endif; ?>
                        <div class="overlay">
                            <div class="watch_now"><a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                        </div>
                    </div>
                </div>
                <!-- Condition to check display the content on tooltip or below the images-->
                <?php if ($dsp_theme_options['opt-related-layout-slider-content'] == 1): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
                            <p><?php echo $channel['trim_channel_description']; ?></p>
                        </a>
                    </div>
                <?php elseif ($dsp_theme_options['opt-related-layout-slider-content'] == 2): ?>
                    <div class="tooltip_templates">
                        <span id="<?php echo 'releted_tooltip_content_' . $cnt . $i; ?>">
                            <h4><?php echo $channel['trim_channel_title']; ?></h4>
                            <p><?php echo $channel['trim_channel_description']; ?></p>
                        </span>
                    </div>
                <?php elseif ($dsp_theme_options['opt-related-layout-slider-content'] == 3): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo $channel['channel_url']; ?>" title="<?php echo $channel['channel_title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $channel['trim_channel_title']; ?></h4>
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
    </div><!-- related_content -->
<?php endif; ?>
