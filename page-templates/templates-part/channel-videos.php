<div class="slick-wrapper <?php echo $class ?>">
    <?php
    $i = 0;
    foreach ($videos as $video):
        $class = '';
        if (!preg_match('/^[a-f\d]{24}$/i', $video_slug)) {
            if ($video['slug'] == $video_slug)
                $class = 'active';
        } else {
            if ($video['id'] == $video_slug)
                $class = 'active';
        }
        ?>
        <div class="slide">
            <a href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">
                <div class="slide_image tooltippp <?php echo $class; ?>" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>" class="lazy" data-src="<?php echo $video['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                </div>
                <?php
                $title = ($dsp_theme_options['opt-channel-video-title-trim-word'] != 0) ? substr($video['title'], 0, $dsp_theme_options['opt-channel-video-title-trim-word']). '...' : $video['title'];
                $description = ($dsp_theme_options['opt-channel-video-description-trim-word'] != 0) ? substr($video['description'], 0, $dsp_theme_options['opt-channel-video-description-trim-word']). '...' : $video['description'];
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