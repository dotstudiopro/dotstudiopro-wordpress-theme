<div class="col-sm-12 no-gutters pt-7">
    <h3 class="post-title mb-5">Watch Again</h3>
    <div class="slick-wrapper <?php echo $class . ' ' . $slide_text_class ?>">
        <?php $i = 1 ?>
        <?php foreach ($watch_list['data']['watch-again'] as $video) { ?>
            <div class="slide">
                <div class="slide_image  clearfix">
                    <div class="hover ehover<?php echo $dsp_theme_options['opt-img-hover']; ?>">
                        <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height; ?>" class="lazy w-100" data-src="<?php echo 'https://images.dotstudiopro.com/' . $video['thumb'] . '/' . $width . '/' . $height; ?>" title="<?php echo $video['title']; ?>" alt="<?php echo $video['title']; ?>">
                        <div class="overlay">
                            <div class="watch_now"><a class="info" href="<?php echo get_site_url() . '/video/' . $video['_id']; ?>" title="<?php echo $video['title']; ?>">&nbsp;<span>&nbsp;</span></a></div>
                        </div>
                    </div>
                </div>

                <?php
                $title = ($dsp_theme_options['opt-title-trim-word'] != 0) ? wp_trim_words($video['title'], $dsp_theme_options['opt-title-trim-word']) : $video['title'];
                ?>
                <div class="slide_content">
                    <a class="info" href="<?php echo get_site_url() . '/video/' . $video['_id']; ?>" title="<?php echo $video['title']; ?>">
                        <h4 class="pt-4 pb-1"><?php echo $title; ?></h4>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>