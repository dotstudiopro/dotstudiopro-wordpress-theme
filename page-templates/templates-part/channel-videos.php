<div class="slick-wrapper <?php echo $class ?>">
    <?php
    $i = 0;
    foreach ($videos as $video):
        ?>
        <div class="slide">
            <a href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">
                <div class="slide_image tooltippp" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>" class="lazy" data-src="<?php echo $video['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                </div>
                <?php
                $title = ($dsp_theme_options['opt-channel-video-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-channel-video-title-trim-word'], '...') : $video['title'];
                $description = ($dsp_theme_options['opt-channel-video-description-trim-word'] != 0) ? wp_trim_words($video['description'], $dsp_theme_options['opt-channel-video-description-trim-word'], '...') : $video['description'];
                ?>
                <?php if ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 1): ?>
                    <div class="slide_content">
                        <h6><?php echo $title; ?></h6>
                        <p><?php echo $description; ?></p>
                    </div>
                <?php else: ?>
                    <div class="tooltip_templates">
                        <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                            <h4><?php echo $title; ?></h4>
                            <p><?php echo $description; ?></p>
                        </span>
                    </div>
                <?php endif; ?>
            </a>
        </div>
        <?php
        $i++;
    endforeach;
    ?>
</div>