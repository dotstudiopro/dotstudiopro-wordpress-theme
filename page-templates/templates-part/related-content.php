<?php
$theme_function = new Theme_Functions();

global $dsp_theme_options;

$recommendation_content = $theme_function->get_recommendation_content($type, $related_id);

if (!empty($recommendation_content)):
    ?>
    <div class="related_content_section other-categories">
        <h3 class="post-title mb-5"><?php echo $dsp_theme_options['opt-related-content-text']; ?></h3>
        <?php
        $width = filter_var($dsp_theme_options['opt-related-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
        $height = filter_var($dsp_theme_options['opt-related-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
        ?>
        <div class="slick-wrapper related_content">
            <?php
            $i = 1;

            foreach ($recommendation_content as $channel):
                ?>
                <div class="slide">
                    <div class="slide_image tooltippp clearfix" data-tooltip-content="#<?php echo 'tooltip_content_' . $cnt . $i; ?>">
                        <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                            <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>;" class="lazy" data-src="<?php echo $channel['image'] . '/' . $width . '/' . $height; ?>" title="<?php echo $channel['title']; ?>" alt="<?php echo $channel['title']; ?>">
                            <div class="overlay">
                                <div class="watch_now"><a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">&nbsp;</a></div>
                            </div>
                        </div>
                    </div>
                    <!-- Condition to check display the content on tooltip or below the images-->
                    <?php
                    $title = ($dsp_theme_options['opt-related-title-trim-word'] != 0) ? wp_trim_words($channel['title'], $dsp_theme_options['opt-related-title-trim-word']) : $channel['title'];
                    $description = ($dsp_theme_options['opt-related-description-trim-word'] != 0) ? wp_trim_words($channel['description'], $dsp_theme_options['opt-related-description-trim-word']) : $channel['description'];
                    ?>
                    <?php if ($dsp_theme_options['opt-related-layout-slider-content'] == 1): ?>
                        <div class="slide_content">
                            <a class="info" href="<?php echo $channel['url']; ?>" title="<?php echo $channel['title']; ?>">
                                <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                                <p><?php echo $description; ?></p>
                            </a>
                        </div>
                    <?php elseif ($dsp_theme_options['opt-related-layout-slider-content'] == 2): ?>
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
        </div><!-- related_content -->
    </div><!-- related_content_section -->
<?php endif; ?>