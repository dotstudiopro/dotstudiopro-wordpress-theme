<div class="col-sm-12 no-gutters pt-5">
    <h3 class="post-title mb-4">Watch Again</h3>
    <div class="slick-wrapper watch-again <?php echo $class .' '. $slide_text_class ?>">
        <?php
        $c = 0;
        if($dsp_theme_options['opt-continue-watch-image-size'] == '0') {
            $c_width = filter_var($dsp_theme_options['opt-continue-watch-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
            $c_height = filter_var($dsp_theme_options['opt-continue-watch-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
        }
        else {
            $c_width = filter_var($dsp_theme_options['opt-continue-watch-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
            $c_ratio_width = filter_var($dsp_theme_options['opt-continue-watch-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
            $c_ratio_height = filter_var($dsp_theme_options['opt-continue-watch-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
            
            $c_ratio = $c_ratio_height / $c_ratio_width;
        }
        foreach ($watch_list['data']['watch-again'] as $video):
            ?>
            <div class="slide">
                <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'channel_tooltip_content_' . $c; ?>">
                    <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                        <?php if( $dsp_theme_options['opt-continue-watch-image-size'] == '1' ) :
                            $image_attributes = dsp_build_responsive_images( 'https://images.dotstudiopro.com/'.$video['thumb'], $c_width, $c_ratio ); 
                            if($dsp_theme_options['opt-display-webp-image'] == 0):?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $c_width; ?>" class="lazy w-100" data-src="<?php echo 'https://images.dotstudiopro.com/' . $video['thumb']; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                            <?php else:?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $c_width; ?>?webp=1" class="lazy w-100" data-src="<?php echo 'https://images.dotstudiopro.com/' . $video['thumb']; ?>?webp=1" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                            <?php endif; ?>
                        <?php else : 
                            if($dsp_theme_options['opt-display-webp-image'] == 0):?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $c_width . '/' . $c_height; ?>" class="lazy w-100" data-src="<?php echo 'https://images.dotstudiopro.com/' . $video['thumb'] . '/' . $c_width . '/' . $c_height; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                            <?php else:?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $c_width . '/' . $c_height; ?>?webp=1" class="lazy w-100" data-src="<?php echo 'https://images.dotstudiopro.com/' . $video['thumb'] . '/' . $c_width . '/' . $c_height; ?>?webp=1" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                            <?php endif; ?>  
                        <?php endif; ?>
                        <div class="overlay">
                            <div class="watch_now"><a class="info" href="<?php echo get_site_url() . '/video/' . $video['_id']; ?>" title="<?php echo $video['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                        </div>
                    </div>
                </div>
                <!-- Condition to check display the content on tooltip or below the images-->
                <?php
                $title = ($dsp_theme_options['opt-continue-watch-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-continue-watch-title-trim-word']) : $video['title'];
                $description = '';
                if(isset($video['description']) && !empty($video['description']))
                    $description = ($dsp_theme_options['opt-continue-watch-description-trim-word'] != 0) ? wp_trim_words($video['description'], $dsp_theme_options['opt-continue-watch-description-trim-word']) : $video['description'];
                ?>
                <?php if ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 1): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo get_site_url() . '/video/' . $video['_id']; ?>" title="<?php echo $video['title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                            <p><?php echo $description; ?></p>
                        </a>
                    </div>
                <?php elseif ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 2): ?>
                    <div class="tooltip_templates">
                        <span id="<?php echo 'channel_tooltip_content_' . $c; ?>">
                            <h4><?php echo $title; ?></h4>
                            <p><?php echo $description; ?></p>
                        </span>
                    </div>
                <?php elseif ($dsp_theme_options['opt-continue-watch-layout-slider-content'] == 3): ?>
                    <div class="slide_content">
                        <a class="info" href="<?php echo get_site_url() . '/video/' . $video['_id']; ?>" title="<?php echo $video['title']; ?>">
                            <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
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
