<?php include(locate_template('page-templates/templates-processing/continue-watch-processing.php')); ?>
<div class="col-sm-12 no-gutters pt-5">
    <h3 class="post-title mb-4">Continue Watching</h3>
    <div class="slick-wrapper continue-watching <?php echo $class .' '. $slide_text_class ?>">
        <?php
        $c = 0;
        foreach ($final_continue_watching_data['continue_watching_data'] as $data):
            ?>
            <div class="slide">
                <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'channel_tooltip_content_' . $c; ?>">
                    <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                        <?php if(isset($data['image_attributes_sizes']) && isset($data['image_attributes_srcset'])) :?>
                            <img src="<?php echo $final_continue_watching_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $data['banner']; ?>" title="<?php echo $data['title']; ?>" alt="<?php echo $data['title']; ?>" srcset="<?php echo $data['image_attributes_srcset']; ?>" sizes="<?php echo $data['image_attributes_sizes']; ?>">
                        <?php else : ?>   
                            <img src="<?php echo $final_continue_watching_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $data['banner']; ?>" title="<?php echo $data['title']; ?>" alt="<?php echo $data['title']; ?>">
                        <?php endif; ?>
                        <div class="overlay">
                            <div class="watch_now"><a class="info" href="<?php echo $data['url']; ?>" title="<?php echo $data['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                        </div>
                    </div>
                </div>
                <!-- Condition to check display the content on tooltip or below the images-->
                <?php if ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 1): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo $data['url']; ?>" title="<?php echo $data['title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $data['trim_title']; ?></h4>
                            <p><?php echo $data['trim_description']; ?></p>
                        </a>
                    </div>
                <?php elseif ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 2): ?>
                    <div class="tooltip_templates">
                        <span id="<?php echo 'channel_tooltip_content_' . $c; ?>">
                            <h4><?php echo $data['trim_title']; ?></h4>
                            <p><?php echo $data['trim_description']; ?></p>
                        </span>
                    </div>
                <?php elseif ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 3): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo $data['url']; ?>" title="<?php echo $data['title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $data['trim_title']; ?></h4>
                        </a>
                    </div>
                    <?php
                endif;
                $c++;
                ?>
            </div>
            <?php
        endforeach;
        ?>
    </div>
</div>
