<?php
global $dsp_theme_options;
$slide_text_class = '';
if ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 1) {
    $slide_text_class .= 'slide-text';
}
?>
<div class="slick-wrapper <?php echo $class . ' ' . $slide_text_class ?>">
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
        <div class="slide <?php echo $class; ?>">
            <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>;" class="lazy" data-src="<?php echo $video['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                    <div class="overlay">
                        <div class="watch_now"><a class="info" href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                    </div>
                </div>
            </div>
            <!-- Condition to check display the content on tooltip or below the images-->
            <?php
            $title = ($dsp_theme_options['opt-channel-video-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-channel-video-title-trim-word']) : $video['title'];
            $description = ($dsp_theme_options['opt-channel-video-description-trim-word'] != 0) ? wp_trim_words($video['description'], $dsp_theme_options['opt-channel-video-description-trim-word']) : $video['description'];
            ?>
            <?php if ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 1): ?>
                <div class="slide_content">
                    <a class="info" href="<?php echo $video['url']; ?>" title="<?php echo $video['title']; ?>">
                        <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                        <p><?php echo $description; ?></p>
                    </a>
                </div>
            <?php elseif ($dsp_theme_options['opt-channel-video-layout-slider-content'] == 2): ?>
                <div class="tooltip_templates">
                    <span id="<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                        <h4><?php echo $title; ?></h4>
                        <p><?php echo $description; ?></p>
                    </span>
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
